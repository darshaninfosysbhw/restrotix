<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetBranchContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->tenant_id) {
            return $next($request);
        }

        $tenant = $user->tenant()->with('plan')->first();
        $branches = $tenant?->branches()->orderBy('branch_name')->get(['id', 'branch_name']) ?? collect();
        $isAdmin = in_array(strtolower((string) $user->role), ['admin', 'superadmin'], true);
        $canSwitchBranches = $isAdmin && (int) ($tenant?->plan?->max_branches ?? 1) > 1;

        if (!$canSwitchBranches) {
            $branchId = (int) ($user->branch_id ?: $branches->first()?->id ?: 0);
            $branchId > 0
                ? $request->session()->put('active_branch_id', $branchId)
                : $request->session()->forget('active_branch_id');
        } else {
            $activeBranchId = (int) $request->session()->get('active_branch_id', 0);
            $hasAccess = $activeBranchId > 0 && $branches->contains('id', $activeBranchId);

            if (!$hasAccess) {
                $request->session()->put('active_branch_id', (int) ($branches->first()?->id ?? 0));
            }
        }

        return $next($request);
    }
}
