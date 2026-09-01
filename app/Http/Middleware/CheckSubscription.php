<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $isAdminPanelRoute = $request->is('admin*');
        $isImpersonating = session()->has('impersonated_by');

        // Superadmin ko tenant admin routes par bhejne ke bajay apne panel par redirect karo.
        if ($user->role === 'superadmin' && !$isImpersonating && $isAdminPanelRoute) {
            return redirect()->route('superadmin.dashboard')->with('toast', [
                ['type' => 'info', 'message' => 'Aap Super Admin panel me ho. Admin panel ke liye impersonate use karein.', 'duration' => 5000]
            ]);
        }

        // Superadmin apne own panel aur impersonation ke bahar baaki routes use kar sakta hai.
        if ($user->role === 'superadmin' && !$isImpersonating) {
            return $next($request);
        }

        // 3. TENANT CONTEXT (Session priority)
        $tenantId = session('active_tenant_id') ?? $user->tenant_id;
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant) {
            return redirect()->route('login')->with('toast', [
                ['type' => 'error', 'message' => 'Restaurant context missing hai. Please dubara login karein.', 'duration' => 5000]
            ]);
        }

        // ✅ Impersonation → FULL BYPASS [Important!]
        if ($isImpersonating) {
            return $next($request);
        }

        if ($tenant->is_banned) {
            if (!session()->has('impersonated_by')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('toast', [
                    [
                        'type' => 'error',
                        'message' => 'Your restaurant access has been cancelled. Please contact our support team.',
                        'duration' => 6000,
                    ],
                ]);
            }
        }

        $isBillingRoute = $request->routeIs('admin.billing*') || $request->routeIs('checkout*');

        if ($tenant->isExpired() && !$isBillingRoute) {
            return redirect()->route('admin.billing')->with('toast', [
                ['type' => 'error', 'message' => 'आपका सब्सक्रिप्शन खत्म हो गया है। कृपया रिन्यू करें।', 'duration' => 7000]
            ]);
        }

        return $next($request);
    }
}
