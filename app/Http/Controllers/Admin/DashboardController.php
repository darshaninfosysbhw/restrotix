<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService, Request $request)
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

        $isOwner = in_array($user->role, ['admin', 'superadmin'], true);
        $dashboardBranches = collect();
        $dashboardBranchId = (int) session('active_branch_id', $user->branch_id ?? 0) ?: null;
        $dashboardBranchLabel = 'Assigned Branch';

        if ($isOwner) {
            $dashboardBranches = Branch::query()->where('tenant_id', $tenantId)
                ->orderBy('branch_name')->get(['id', 'branch_name']);
            $selection = $request->validate([
                'dashboard_branch' => ['nullable', 'string', 'regex:/^(all|[1-9][0-9]*)$/'],
            ])['dashboard_branch'] ?? 'all';
            $dashboardBranchId = $selection === 'all' ? null : (int) $selection;
            $selectedBranch = $dashboardBranches->firstWhere('id', $dashboardBranchId);
            abort_if($dashboardBranchId !== null && !$selectedBranch, 403);
            $dashboardBranchLabel = $selectedBranch?->branch_name ?? 'All Branches';
        }

        $dashboardData = $dashboardService->buildDashboardPayload(
            $tenantId,
            $currencySymbol,
            $dashboardBranchId
        );

        return view('admin.dashboard', array_merge(
            compact('user', 'dashboardBranches', 'dashboardBranchId', 'dashboardBranchLabel'),
            $dashboardData
        ));
    }
}
