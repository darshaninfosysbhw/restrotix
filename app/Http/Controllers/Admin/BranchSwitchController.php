<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchSwitchController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $user = $request->user();
        $tenant = $user?->tenant()->with('plan')->first();

        abort_unless(
            $user
                && in_array(strtolower((string) $user->role), ['admin', 'superadmin'], true)
                && (int) ($tenant?->plan?->max_branches ?? 1) > 1,
            403
        );

        $validated = $request->validate([
            'branch_id' => ['required', 'integer'],
        ]);

        $branch = $tenant->branches()->findOrFail((int) $validated['branch_id']);
        $request->session()->put('active_branch_id', (int) $branch->id);

        return redirect()->back()->with('toast', [
            [
                'type' => 'success',
                'message' => 'Switched to ' . $branch->branch_name,
                'duration' => 2500,
            ],
        ]);
    }
}
