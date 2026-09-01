<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class CheckServiceAddon
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $serviceName): Response
    {
        $user = Auth::user();

        // 1. अगर सुपरएडमिन है, तो उसे सब कुछ देखने दो (Testing के लिए आसान रहेगा)
        if ($user->role == 'superadmin') {
            return $next($request);
        }

        // 2. टेनेंट चेक करो
        $tenant = $user->tenant; // पक्का करना कि User model में tenant() relationship है

        if (!$tenant) {
            abort(403, 'कोई टेनेंट प्रोफाइल नहीं मिली।');
        }

        $tenant->loadMissing('plan.services');

        $planHasService = in_array(
            $serviceName,
            $tenant->plan?->services?->pluck('slug')->filter()->values()->all() ?? [],
            true
        );

        // 3. सर्विस चेक करो (Plan access + Add-on access)
        $addonHasService = $tenant->services()
            ->where('slug', $serviceName)
            ->wherePivot('status', 'active') // पिवोट टेबल का स्टेटस चेक
            ->where(function ($query) {
                $query->whereNull('tenant_service.expires_at') // अगर लाइफटाइम एक्सेस है
                    ->orWhere('tenant_service.expires_at', '>', now()); // या अभी एक्सपायर नहीं हुआ
            })
            ->exists();

        if (!$planHasService && !$addonHasService) {
            return redirect()->route('admin.dashboard')
                ->with('toast', [
                    [
                        'type' => 'error',
                        'message' => 'यह सर्विस (' . ucfirst($serviceName) . ') आपके एक्टिव प्लान में नहीं है। कृपया Add-on खरीदें।',
                        'duration' => 6000,
                    ],
                ]);
        }

        return $next($request);
    }
}
