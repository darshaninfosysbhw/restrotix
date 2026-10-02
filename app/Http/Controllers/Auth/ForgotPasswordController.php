<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Models\AuthSecurityLog;

class ForgotPasswordController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 5;

    /**
     * Forgot password page.
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset OTP.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'identifier' => [
                    'required',
                    'string',
                    'max:255',
                ],
            ]);

            $identifier = trim($validated['identifier']);

            /*
            |--------------------------------------------------------------------------
            | Detect channel
            |--------------------------------------------------------------------------
            */
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                return $this->sendEmailOtp($request, $identifier);
            }

            if ($this->looksLikePhoneNumber($identifier)) {
                return $this->sendMobileOtp($identifier);
            }

            throw ValidationException::withMessages([
                'identifier' => 'Please enter a valid email address or mobile number.',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
                    ?? 'Please check the information and try again.',
            ], 422);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to send OTP right now. Please try again.',
            ], 500);
        }
    }

    /**
     * Email OTP channel.
     */
    private function sendEmailOtp(Request $request, string $email): JsonResponse
    {
        $email = $this->normalizeEmail($email);

        

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Do not reveal whether account exists
        |--------------------------------------------------------------------------
        */
        if (!$user) {
            return $this->genericOtpResponse();
        }

        $otpCode = (string) random_int(100000, 999999);

        DB::transaction(function () use ($request, $user, $email, $otpCode) {

            /*
             * Invalidate old unused OTPs for this user/channel.
             */
            PasswordResetOtp::query()
                ->where('user_id', $user->id)
                ->where('channel', 'email')
                ->whereNull('verified_at')
                ->delete();

            PasswordResetOtp::create([
                'user_id' => $user->id,
                'identifier' => $email,
                'channel' => 'email',

                // Raw OTP database me store nahi karenge.
                'otp_hash' => Hash::make($otpCode),

                'expires_at' => now()->addMinutes(
                    self::OTP_EXPIRY_MINUTES
                ),

                'verified_at' => null,
                'attempts' => 0,
                'resend_count' => 0,
            ]);

            Mail::to($email)->send(
                new PasswordResetOtpMail(
                    $otpCode,
                    self::OTP_EXPIRY_MINUTES
                )
            );

            $this->logSecurityEvent(
                request: $request,
                eventType: 'password_reset_otp_sent',
                userId: $user->id,
                channel: 'email',
                identifier: $email,
            );
        });

        return $this->genericOtpResponse();
    }


    /**
     * Mobile OTP channel.
     *
     * SMS provider future me yahin plug hoga.
     */
    private function sendMobileOtp(string $phone): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Mobile OTP is not available yet. Please use your registered email address.',
        ], 422);
    }

    /**
     * Generic response prevents account enumeration.
     */
    private function genericOtpResponse(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'If an eligible account exists, an OTP has been sent.',
        ]);
    }

    private function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    private function looksLikePhoneNumber(string $value): bool
    {
        $phone = preg_replace('/[\s\-\(\)]+/', '', trim($value));

        return (bool) preg_match('/^\+?[0-9]{7,15}$/', $phone);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'identifier' => ['required', 'string', 'max:255'],
                'otp' => ['required', 'digits:6'],
            ]);

            $identifier = trim($validated['identifier']);

            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $identifier = $this->normalizeEmail($identifier);
                $channel = 'email';
            } elseif ($this->looksLikePhoneNumber($identifier)) {
                $channel = 'sms';
            } else {
                throw ValidationException::withMessages([
                    'identifier' => 'Please enter a valid email address or mobile number.',
                ]);
            }

            $otpRecord = PasswordResetOtp::query()
                ->where('identifier', $identifier)
                ->where('channel', $channel)
                ->whereNull('verified_at')
                ->latest('id')
                ->first();

            if (!$otpRecord) {
                throw ValidationException::withMessages([
                    'otp' => 'Please request a new OTP first.',
                ]);
            }

            if ($otpRecord->isExpired()) {
             $otpRecord->delete();

                throw ValidationException::withMessages([
                  'otp' => 'OTP has expired. Please request a new one.',
             ]);
            }

            if ($otpRecord->attempts >= 5) {
                throw ValidationException::withMessages([
                    'otp' => 'Too many incorrect attempts. Please request a new OTP.',
                ]);
            }

            if (!Hash::check($validated['otp'], $otpRecord->otp_hash)) {
                $otpRecord->increment('attempts');
                $otpRecord->refresh();

                $this->logSecurityEvent(
                    request: $request,
                    eventType: 'password_reset_otp_failed',
                    userId: $otpRecord->user_id,
                    channel: $channel,
                    identifier: $identifier,
                    attemptNumber: $otpRecord->attempts,
                );

                if ($otpRecord->attempts >= 5) {
                    $otpRecord->delete();

                    throw ValidationException::withMessages([
                        'otp' => 'Too many incorrect attempts. Please request a new OTP.',
                    ]);
                }

                throw ValidationException::withMessages([
                    'otp' => 'Invalid OTP. Please check and try again.',
                ]);
         }

            $otpRecord->update([
                'verified_at' => now(),
            ]);

            $this->logSecurityEvent(
                request: $request,
                eventType: 'password_reset_otp_verified',
                userId: $otpRecord->user_id,
                channel: $channel,
                identifier: $identifier,
            );

            session([
                'password_reset_user_id' => $otpRecord->user_id,
                'password_reset_otp_id' => $otpRecord->id,
                'password_reset_authorized' => true,
            ]);
 
            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
            ]);

            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first()
                        ?? 'Unable to verify OTP.',
                ], 422);

            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to verify OTP right now. Please try again.',
                ], 500);
            }
    }

   

    public function resetPassword(Request $request): JsonResponse
{
    try {
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        if (!session('password_reset_authorized')) {
            throw ValidationException::withMessages([
                'password' => 'Your password reset session has expired. Please verify OTP again.',
            ]);
        }

        $userId = (int) session('password_reset_user_id');
        $otpId = (int) session('password_reset_otp_id');

        $otpRecord = PasswordResetOtp::query()
            ->whereKey($otpId)
            ->where('user_id', $userId)
            ->whereNotNull('verified_at')
            ->first();

        if (!$otpRecord) {
            throw ValidationException::withMessages([
                'password' => 'Password reset authorization is invalid or expired.',
            ]);
        }

        $user = User::find($userId);

        if (!$user) {
            throw ValidationException::withMessages([
                'password' => 'Unable to reset password for this account.',
            ]);
        }

        DB::transaction(function () use ($request, $user, $validated, $otpRecord) {
            $user->update([
                'password' => $validated['password'],
                'remember_token' => Str::random(60),
            ]);

            $this->logSecurityEvent(
                request: $request,
                eventType: 'password_reset_completed',
                userId: $user->id,
            );

            PasswordResetOtp::query()
                ->where('user_id', $user->id)
                ->delete();

            session()->forget([
                'password_reset_user_id',
                'password_reset_otp_id',
                'password_reset_authorized',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. Please login with your new password.',
            'redirect' => route('login'),
        ]);

    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => collect($e->errors())->flatten()->first()
                ?? 'Unable to reset password.',
        ], 422);

    } catch (\Throwable $e) {
        report($e);

        return response()->json([
            'success' => false,
            'message' => 'Unable to reset password right now. Please try again.',
        ], 500);
    }
}

private function logSecurityEvent(
    Request $request,
    string $eventType,
    ?int $userId = null,
    ?string $channel = null,
    ?string $identifier = null,
    ?int $attemptNumber = null,
    array $metadata = []
): void {
    AuthSecurityLog::create([
        'user_id' => $userId,
        'event_type' => $eventType,
        'channel' => $channel,
        'identifier_hash' => $identifier
            ? hash('sha256', mb_strtolower(trim($identifier)))
            : null,
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'attempt_number' => $attemptNumber,
        'metadata' => !empty($metadata) ? $metadata : null,
    ]);
}
}