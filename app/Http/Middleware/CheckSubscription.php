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
        // $user = Auth::user();
        // // Safety check
        // if (!$user) {
        //     return redirect()->route('login');
        // }

        // // 1. Superadmin को छूट दें
        // if ($user->role == 'superadmin') {
        //     return $next($request);
        // }

        // // 2. इम्पर्सनेशन चेक (अगर सुपर-एडमिन किसी टेनेंट को देख रहा है)
        // // अगर आपके पास सेशन में इम्पर्सनेट की आईडी है, तो उसे भी छूट दें
        // if (session()->has('impersonated_by')) {
        //     return $next($request);
        // }

        // $tenant = $user->tenant;

        // // अगर टेनेंट ही नहीं है (जैसे कोई नया यूजर जिसका टेनेंट क्रिएट नहीं हुआ), तो आगे बढ़ने दें
        // if (!$tenant) {
        //     return $next($request);
        // }

        // // 2. क्या ट्रायल खत्म हो गया है?
        // if ($tenant->isExpired()) {

        //     // dd($tenant->trial_ends_at, $tenant->subscription_ends_at);
        //     if ($tenant->subscription_status !== 'active') {

        //         if ($request->routeIs('admin.billing*')) {
        //             return $next($request);
        //         }

        //         return redirect()->route('admin.billing')
        //             ->with('error', 'आपका ट्रायल/सब्सक्रिप्शन खत्म हो गया है। कृपया आगे बढ़ने के लिए पेमेंट करें।');
        //     }
        // }

        // // 3. क्या टेनेंट बैन है?
        // if ($tenant->is_banned) {
        //     Auth::logout();
        //     return redirect()->route('login')->with('error', 'आपका अकाउंट सस्पेंड कर दिया गया है।');
        // }
        // return $next($request);

        // =====================================================================================
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 2. SUPERADMIN BYPASS
        if ($user->role == 'superadmin' && !session()->has('impersonated_by')) {
            return $next($request);
        }

        // 3. TENANT CONTEXT (Session priority)
        $tenantId = session('active_tenant_id') ?? $user->tenant_id;
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant) {
            return $next($request);
        }

        // ✅ Impersonation → FULL BYPASS [Important!]
        if (session()->has('impersonated_by')) {
            return $next($request);
        }

        if ($tenant->is_banned) {
            if (!session()->has('impersonated_by')) {
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('toast', [
                    ['type' => 'error', 'message' => 'आपका अकाउंट सस्पेंड कर दिया गया है।', 'duration' => 5000]
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
