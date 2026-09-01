<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Branch;
use App\Mail\CheckoutOtpMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\SubscriptionHistory;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $planSlug = $request->query('plan', 'base');
        $billingCycle = strtolower((string) $request->query('billing_cycle', 'monthly'));
        if (!in_array($billingCycle, ['monthly', 'yearly'], true)) {
            $billingCycle = 'monthly';
        }

        $planDetails = Plan::with(['services', 'prices.currency'])->where('slug', $planSlug)->first();

        if (!$planDetails) {
            return redirect('/pricing')->with('error', 'Plan not found.');
        }

        $currencyId = session('currency_id', 1);
        $priceData = $planDetails->prices->firstWhere('currency_id', $currencyId) ?? $planDetails->prices->first();
        $monthlyPrice = $priceData ? (float) $priceData->monthly_price : 0.0;
        $yearlyPrice = $priceData
            ? (float) $priceData->yearly_price
            : ($monthlyPrice > 0 ? $monthlyPrice * 10 : 0.0);
        $selectedPrice = $billingCycle === 'yearly' ? $yearlyPrice : $monthlyPrice;

        $planDetails->monthly_price = $monthlyPrice;
        $planDetails->yearly_price = $yearlyPrice;
        $planDetails->selected_billing_cycle = $billingCycle;
        $planDetails->selected_price = $selectedPrice;
        $planDetails->currency_symbol = $priceData?->currency?->symbol ?? session('currency_symbol', '₹');


        return view('checkout', compact('planDetails', 'billingCycle', 'selectedPrice'));
    }

    public function sendOtp(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            ]);

            $email = $this->normalizeEmail($validated['email']);
            $otpCode = (string) random_int(100000, 999999);
            $expiresAt = now()->addMinutes(5)->timestamp;

            session([
                'checkout_otp_email' => $email,
                'checkout_otp_code' => $otpCode,
                'checkout_otp_expires_at' => $expiresAt,
                'checkout_otp_verified' => false,
                'checkout_otp_verified_email' => null,
            ]);

            Mail::to($email)->send(new CheckoutOtpMail($otpCode, 5));

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email address.',
            ]);
        } catch (ValidationException $e) {
            $errorMessages = Arr::flatten($e->errors());

            return response()->json([
                'success' => false,
                'message' => $errorMessages[0] ?? 'Unable to send OTP.',
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email', 'max:255'],
                'otp'   => ['required', 'digits:6'],
            ]);

            $email = $this->normalizeEmail($validated['email']);
            $sessionEmail = $this->normalizeEmail((string) session('checkout_otp_email', ''));
            $sessionCode = (string) session('checkout_otp_code', '');
            $expiresAt = (int) session('checkout_otp_expires_at', 0);

            if ($sessionEmail === '' || $sessionCode === '' || $expiresAt === 0) {
                throw ValidationException::withMessages([
                    'otp' => 'Please request a new OTP first.',
                ]);
            }

            if ($email !== $sessionEmail) {
                throw ValidationException::withMessages([
                    'email' => 'Please verify the same email that received the OTP.',
                ]);
            }

            if (now()->timestamp > $expiresAt) {
                session()->forget([
                    'checkout_otp_email',
                    'checkout_otp_code',
                    'checkout_otp_expires_at',
                    'checkout_otp_verified',
                    'checkout_otp_verified_email',
                ]);

                throw ValidationException::withMessages([
                    'otp' => 'OTP has expired. Please request a new one.',
                ]);
            }

            if (! hash_equals($sessionCode, (string) $validated['otp'])) {
                throw ValidationException::withMessages([
                    'otp' => 'Invalid OTP. Please check and try again.',
                ]);
            }

            session([
                'checkout_otp_verified' => true,
                'checkout_otp_verified_email' => $email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully.',
            ]);
        } catch (ValidationException $e) {
            $errorMessages = Arr::flatten($e->errors());

            return response()->json([
                'success' => false,
                'message' => $errorMessages[0] ?? 'Unable to verify OTP.',
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // 1. Validation
            $request->validate([
                'full_name'       => 'required|string|max:255',
                'email'           => 'required|email|unique:users,email',
                'password'        => 'required|min:8',
                'restaurant_name' => 'required|string|max:255',
                'plan_id'         => 'required|exists:plans,id',
                'phone'          => 'required|string|max:15|unique:users,phone_number',
                'city'            => 'required|string|max:100',
                'country'         => 'required|string|max:100',
                'currency'        => 'required|string|max:10',
                'timezone'        => 'required|string',
                'billing_cycle'   => 'nullable|in:monthly,yearly',
                'pincode'         => 'nullable|string|max:10', // Field optional ho sakta hai
                'currency_id' => 'nullable|exists:currencies,id',
            ]);

            $verifiedEmail = $this->normalizeEmail((string) session('checkout_otp_verified_email', ''));
            $requestEmail = $this->normalizeEmail((string) $request->input('email', ''));

            if (
                ! session('checkout_otp_verified')
                || $verifiedEmail === ''
                || $verifiedEmail !== $requestEmail
            ) {
                throw ValidationException::withMessages([
                    'email' => 'Please verify your email with the OTP before continuing.',
                ]);
            }

            return DB::transaction(function () use ($request) {
                // Plan details for trial days
                $plan = Plan::findOrFail($request->plan_id);
                $billingCycle = strtolower((string) ($request->input('billing_cycle', 'monthly')));
                if (!in_array($billingCycle, ['monthly', 'yearly'], true)) {
                    $billingCycle = 'monthly';
                }

                $country = $this->resolveCheckoutCountry($request);
                $currency = $this->resolveCheckoutCurrency($request, $country);
                $tenantSlug = $this->buildUniqueTenantSlug($request->restaurant_name);

                // 2. Create Tenant
                $tenant = Tenant::create([
                    'company_name'  => $request->restaurant_name,
                    'owner_name'    => $request->full_name,
                    'slug'          => $tenantSlug,
                    'country_id'    => $country?->id,
                    'currency_id'   => $currency?->id,
                    'plan_id'       => $plan->id,
                    'billing_cycle' => $billingCycle,
                    'subscription_status' => 'trial',
                    'subscription_ends_at' => now()->addDays($plan->trial_days),
                    'is_banned' => false,
                ]);



                // 3. Create Main Branch
                $branch = Branch::create([
                    'tenant_id'      => $tenant->id,
                    'branch_name'    => $request->restaurant_name . ' - Main',
                    'contact_number' => $request->phone,
                    'branch_email'   => $request->email,
                    'country_id'     => $country?->id,
                    'currency_id'    => $currency?->id,
                    'timezone'       => $request->timezone,
                    'state'          => $request->state ?? '',
                    'city'           => $request->city,
                    'pincode'        => $request->pincode,
                    'full_address'   => $this->buildFullAddress($request, $country),
                ]);


                // 4. Create User (Admin)
                $user = User::create([
                    'name'      => $request->full_name,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branch->id,
                    'phone_number'     => $request->phone,
                    'role'      => 'admin',
                ]);


                // 5. Subscription History में पहली एंट्री (Trial) डालना
                SubscriptionHistory::create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $request->plan_id,
                    'currency_id' => $currency?->id,
                    'billing_cycle' => $billingCycle,
                    'amount' => 0.00,
                    'status' => 'active',
                    'started_at' => now(),
                    'ended_at' => now()->addDays($plan->trial_days ?? 14), // प्लान टेबल से ट्रायल के दिन उठाएं
                ]);



                // 6. Auto Login
                Auth::login($user);

                session()->forget([
                    'checkout_otp_email',
                    'checkout_otp_code',
                    'checkout_otp_expires_at',
                    'checkout_otp_verified',
                    'checkout_otp_verified_email',
                ]);

                return response()->json([
                    'success'  => true,
                    'message'  => 'Welcome ' . $request->full_name . '! Your restaurant is ready.',
                    'redirect' => route('admin.dashboard')
                ]);
            });
        } catch (ValidationException $e) {
            $errorMessages = Arr::flatten($e->errors());
            return response()->json([
                'success' => false,
                'message' => $errorMessages[0]
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    private function resolveCheckoutCountry(Request $request): ?Country
    {
        $countryId = (int) $request->input('country_id', 0);

        if ($countryId > 0) {
            $country = Country::with('currency')->find($countryId);
            if ($country) {
                return $country;
            }
        }

        $countryInput = trim((string) $request->input('country', ''));
        if ($countryInput !== '') {
            $normalizedCountry = mb_strtolower($countryInput);
            $normalizedIso = mb_strtolower($countryInput);

            $country = Country::with('currency')
                ->where(function ($query) use ($normalizedCountry, $normalizedIso) {
                    $query->whereRaw('LOWER(name) = ?', [$normalizedCountry])
                        ->orWhereRaw('LOWER(iso_code) = ?', [$normalizedIso]);
                })
                ->first();

            if ($country) {
                return $country;
            }
        }

        $activeCountryId = (int) session('active_country_id', 0);
        if ($activeCountryId > 0) {
            $country = Country::with('currency')->find($activeCountryId);
            if ($country) {
                return $country;
            }
        }

        return Country::with('currency')->where('is_active', true)->first();
    }

    private function resolveCheckoutCurrency(Request $request, ?Country $country = null): ?Currency
    {
        if ($country) {
            $country->loadMissing('currency');
            if ($country->currency) {
                return $country->currency;
            }
        }

        $currencyId = (int) $request->input('currency_id', 0);
        if ($currencyId > 0) {
            $currency = Currency::query()->find($currencyId);
            if ($currency) {
                return $currency;
            }
        }

        $currencyCode = strtoupper(trim((string) $request->input('currency', '')));
        if ($currencyCode !== '') {
            $currency = Currency::query()->where('code', $currencyCode)->first();
            if ($currency) {
                return $currency;
            }
        }

        $sessionCurrencyId = (int) session('currency_id', 0);
        if ($sessionCurrencyId > 0) {
            $currency = Currency::query()->find($sessionCurrencyId);
            if ($currency) {
                return $currency;
            }
        }

        return Currency::query()
            ->where('is_active', true)
            ->where('is_default', true)
            ->first()
            ?? Currency::query()
                ->where('is_active', true)
                ->first();
    }

    private function buildUniqueTenantSlug(string $restaurantName): string
    {
        $baseSlug = Str::slug($restaurantName);
        if ($baseSlug === '') {
            $baseSlug = 'restaurant';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function buildFullAddress(Request $request, ?Country $country = null): string
    {
        $parts = array_filter([
            trim((string) $request->input('city', '')),
            trim((string) $request->input('state', '')),
            $country?->name ?: trim((string) $request->input('country', '')),
            trim((string) $request->input('pincode', '')),
        ], fn ($value) => $value !== '');

        return implode(', ', $parts);
    }

    private function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }
}
