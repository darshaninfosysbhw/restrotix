<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuperAdmin\DashboardPlanBreakdownResource;
use App\Http\Resources\SuperAdmin\DashboardRecentTenantResource;
use App\Http\Resources\SuperAdmin\DashboardServiceUsageResource;
use App\Models\Branch;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\TenantService;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = now();
        $today = $now->toDateString();
        $sevenDaysLater = $now->copy()->addDays(7)->toDateString();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonthNoOverflow()->endOfMonth()->toDateString();

        // Filtered Base Queries
        $tenantQuery = Tenant::query();
        $branchQuery = Branch::query();

        $activeTenantsCurrent = (clone $tenantQuery)->active($today)->count();
        $activeTenantsPrevious = (clone $tenantQuery)->active($endOfLastMonth)->count();

        $activeTenantGrowthPercent = $activeTenantsPrevious > 0
            ? round((($activeTenantsCurrent - $activeTenantsPrevious) / $activeTenantsPrevious) * 100, 1)
            : ($activeTenantsCurrent > 0 ? 100 : 0);

        $urgentRenewals = (clone $tenantQuery)
            ->trial($today)
            ->whereDate('subscription_ends_at', '<=', $sevenDaysLater)
            ->count();

        // 2. Revenue Filtering (Based on Country)
        // हम सिर्फ उन सर्विसेज का रेवेन्यू जोड़ रहे हैं जो सिलेक्टेड कंट्री के टेनेंट्स की हैं
        $monthlyRevenueMtd = Service::query()
            ->join('tenant_service', 'tenant_service.service_id', '=', 'services.id')
            ->join('tenants', 'tenant_service.tenant_id', '=', 'tenants.id') // Tenant join किया ताकि country_id चेक कर सकें
            ->when(session('active_country_id'), function ($q) {
                return $q->where('tenants.country_id', session('active_country_id'));
            })
            ->where('tenant_service.status', 'active')
            ->whereBetween('tenant_service.created_at', [$startOfMonth, $now])
            ->sum('services.price');

        $arpuMtd = $activeTenantsCurrent > 0 ? round($monthlyRevenueMtd / $activeTenantsCurrent, 2) : 0;

        $selectedRange = $request->query('range', 'month');
        if (!in_array($selectedRange, ['today', 'week', 'month'], true)) {
            $selectedRange = 'month';
        }

        // 3. Recent Tenants Query with Country Filter
        $recentTenantsQuery = (clone $tenantQuery)->recentForDashboard();

        if ($selectedRange === 'today') {
            $recentTenantsQuery->whereDate('created_at', $today);
        } elseif ($selectedRange === 'week') {
            $recentTenantsQuery->whereDate('created_at', '>=', $now->copy()->startOfWeek()->toDateString());
        } else {
            $recentTenantsQuery->whereDate('created_at', '>=', $now->copy()->startOfMonth()->toDateString());
        }

        $stats = [
            'total_tenants' => (clone $tenantQuery)->count(),
            'active_tenants' => $activeTenantsCurrent,
            'active_tenants_growth_percent' => $activeTenantGrowthPercent,
            'trial_tenants' => (clone $tenantQuery)->trial($today)->count(),
            'pending_tenants' => (clone $tenantQuery)->pending()->count(),
            'urgent_renewals' => $urgentRenewals,
            'total_branches' => $branchQuery->count(),
            'total_users' => User::query()->whereIn('tenant_id', (clone $tenantQuery)->pluck('id'))->nonSuperadmin()->count(),
            'total_services' => Service::query()->count(),
            'active_service_subscriptions' => TenantService::query()->whereIn('tenant_id', (clone $tenantQuery)->pluck('id'))->active()->count(),
            'monthly_revenue_mtd' => (float) $monthlyRevenueMtd,
            'arpu_mtd' => (float) $arpuMtd,
        ];

        $request->attributes->set('dashboard_total_tenants', (int) ($stats['total_tenants'] ?? 0));

        $planBreakdown = collect(
            DashboardPlanBreakdownResource::collection((clone $tenantQuery)->planBreakdown()->get())->resolve($request)
        );

        $recentTenants = collect(
            DashboardRecentTenantResource::collection(
                $recentTenantsQuery->take(4)->get()
            )->resolve($request)
        );

        // Service usage also needs to be country specific
        $serviceUsage = collect(
            DashboardServiceUsageResource::collection(
                Service::query()
                    ->leftJoin('tenant_service', 'tenant_service.service_id', '=', 'services.id')
                    ->leftJoin('tenants', 'tenant_service.tenant_id', '=', 'tenants.id')
                    ->when(session('active_country_id'), function ($q) {
                        return $q->where('tenants.country_id', session('active_country_id'));
                    })
                    ->select('services.name')
                    ->selectRaw('COUNT(tenant_service.id) as total')
                    ->groupBy('services.id', 'services.name')
                    ->orderByDesc('total')
                    ->take(6)->get()
            )->resolve($request)
        );

        return view('superadmin.dashboard', [
            'stats' => $stats,
            'planBreakdown' => $planBreakdown,
            'recentTenants' => $recentTenants,
            'serviceUsage' => $serviceUsage,
            'selectedRange' => $selectedRange,
        ]);
    }

    public function switchCountry(Request $request)
    {
        if ($request->country_id == 0) {
            session()->forget('active_country_id');
        } else {
            session(['active_country_id' => $request->country_id]);
        }

        return back()->with('success', 'Dashboard filtered successfully!');
    }
}
