<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'superadmin' && !session()->has('impersonated_by')) {
            return redirect()->route('superadmin.dashboard')->with('toast', [
                ['type' => 'info', 'message' => 'Aap Super Admin panel me ho. Admin dashboard ke liye impersonate use karein.', 'duration' => 5000]
            ]);
        }

        if (!$user->tenant_id) {
            return redirect()->route('login')->with('toast', [
                ['type' => 'error', 'message' => 'Restaurant profile not linked. Please login again.', 'duration' => 5000]
            ]);
        }

        $user->loadMissing('tenant.currency');

        $currencySymbol = trim((string) ($user->tenant?->currency?->symbol ?? session('currency_symbol', '₹')));
        $tenantId = (int) $user->tenant_id;

        $dashboardData = $dashboardService->buildDashboardPayload(
            $tenantId,
            $currencySymbol,
            (int) session('active_branch_id', $user->branch_id ?? 0) ?: null
        );

        return view('admin.dashboard', array_merge(compact('user'), $dashboardData));
    }
}
