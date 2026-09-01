<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuperAdmin\ProfileActivityResource;
use App\Http\Resources\SuperAdmin\UserProfileResource;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $profile = UserProfileResource::make($request->user())->resolve($request);
        $recentActivities = ProfileActivityResource::collection(
            ActivityLog::query()
                ->where('actor_id', $request->user()->id)
                ->latest()
                ->take(5)
                ->get()
        )->resolve($request);

        return match ($user->role) {
            'chef' => view('profile.chef', compact('profile')),
            'waiter' => view('profile.waiter', compact('profile')),
            default => view('profile.admin', compact('profile', 'recentActivities')),
        };
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $payload = $this->normalizeProfileInput($request);

        $validator = Validator::make($payload, [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone_number')->ignore($user->id),
            ],
        ]);

        if ($validator->fails()) {
            return $this->toastValidationError($validator->errors()->first(), true);
        }

        $changes = $this->buildProfileChangeSet($user->only(['name', 'email', 'phone_number']), $validator->validated());
        $user->update($validator->validated());
        if ($changes !== []) {
            $this->logActivity(
                request: $request,
                event: 'admin.profile.updated',
                description: 'Admin profile details updated.',
                meta: ['changes' => $changes]
            );
        }

        return redirect()->route('admin.profile')->with('toast', [
            [
                'type' => 'success',
                'message' => 'Profile updated successfully.',
                'duration' => 4000,
            ],
        ]);
    }

    public function updatePassword(Request $request)
    {
        $payload = $this->normalizePasswordInput($request);

        $validator = Validator::make($payload, [
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->toastValidationError($validator->errors()->first());
        }

        $request->user()->update([
            'password' => $payload['password'],
        ]);
        $this->logActivity(
            request: $request,
            event: 'admin.profile.password_changed',
            description: 'Admin password changed.',
            meta: []
        );

        return redirect()->route('admin.profile')->with('toast', [
            [
                'type' => 'success',
                'message' => 'Password updated successfully.',
                'duration' => 4000,
            ],
        ]);
    }

    private function normalizeProfileInput(Request $request): array
    {
        return [
            'name' => trim((string) $request->input('full_name', $request->input('name', ''))),
            'email' => strtolower(trim((string) $request->input('email', ''))),
            'phone_number' => $this->normalizeNullableString(
                $request->input('phone', $request->input('phone_number'))
            ),
        ];
    }

    private function normalizePasswordInput(Request $request): array
    {
        return [
            'current_password' => (string) $request->input('current_password', ''),
            'password' => (string) $request->input('new_password', $request->input('password', '')),
            'password_confirmation' => (string) $request->input(
                'new_password_confirmation',
                $request->input('password_confirmation', '')
            ),
        ];
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    private function toastValidationError(string $message, bool $withInput = false)
    {
        $redirect = redirect()->back();

        if ($withInput) {
            $redirect->withInput();
        }

        return $redirect->with('toast', [
            [
                'type' => 'error',
                'message' => $message,
                'duration' => 5000,
            ],
        ]);
    }

    private function buildProfileChangeSet(array $before, array $after): array
    {
        $changes = [];

        foreach (['name', 'email', 'phone_number'] as $field) {
            $old = $before[$field] ?? null;
            $new = $after[$field] ?? null;

            if ((string) $old !== (string) $new) {
                $changes[$field] = [
                    'old' => $old,
                    'new' => $new,
                ];
            }
        }

        return $changes;
    }

    private function logActivity(Request $request, string $event, string $description, array $meta): void
    {
        ActivityLog::create([
            'actor_id' => $request->user()?->id,
            'event' => $event,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => $meta,
        ]);
    }
}
