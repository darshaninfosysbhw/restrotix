<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // यह कोड सिर्फ 'layout' वाले पेजों पर डेटा भेजेगा (जहाँ तुम्हारा Sidebar है)
        View::composer(['core.layouts.admin', 'core.layouts.branch'], function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $tenantId = session('active_tenant_id') ?? $user->tenant_id;

                $tenant = Tenant::with('plan.services')->find($tenantId);

                if (!$tenant || !$tenant->plan) {
                    view()->share('planServiceSlugs', []);
                    view()->share('activeServiceSlugs', []);
                    view()->share('allowedServiceSlugs', []);
                    return;
                }
                // 1. User ke PLAN mein kaun-kaun si services allowed hain? (Visibility ke liye)
                $planServiceSlugs = $tenant->plan->services->pluck('slug')->toArray();

                // डेटाबेस से सिर्फ Active और Valid सर्विसेज के Slug उठाओ
                $activeServiceSlugs = DB::table('tenant_service')
                    ->join('services', 'tenant_service.service_id', '=', 'services.id')
                    ->where('tenant_service.tenant_id', $tenantId)
                    ->where('tenant_service.status', 'active')
                    ->where(function ($query) {
                        $query->whereNull('tenant_service.expires_at')
                            ->orWhere('tenant_service.expires_at', '>', now());
                    })
                    ->pluck('services.slug')
                    ->toArray();

                $allowedServiceSlugs = array_values(array_unique(array_filter(array_merge(
                    $planServiceSlugs,
                    $activeServiceSlugs
                ))));

                // अब $activeServiceSlugs हर Blade फाइल में अपने आप मिल जायेगा
                $view->with(
                    [
                        'planServiceSlugs'   => $planServiceSlugs,
                        'activeServiceSlugs' => $activeServiceSlugs,
                        'allowedServiceSlugs' => $allowedServiceSlugs,
                    ]
                );
            }
        });
    }
}
