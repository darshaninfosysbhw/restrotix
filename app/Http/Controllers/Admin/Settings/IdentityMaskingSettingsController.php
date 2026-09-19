<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IdentityMaskingSettingsController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user()?->tenant;
        abort_unless($tenant, 403);

        $branches = $tenant->branches()
            ->orderBy('branch_name')
            ->get(['id', 'branch_name', 'mask_scope', 'display_name']);

        return view('admin.settings.identity-masking', [
            'branches' => $branches,
            'canMaskIdentity' => $tenant->canMaskIdentity(),
        ]);
    }

    public function update(Request $request, Branch $branch)
    {
        $tenant = $request->user()?->tenant;
        abort_unless($tenant && (int) $branch->tenant_id === (int) $tenant->id, 403);
        abort_unless($tenant->canMaskIdentity(), 403, 'Identity Masking is not enabled for your plan.');

        $validated = $request->validate([
            'mask_scope' => ['required', Rule::in(['none', 'public_only', 'everywhere'])],
            'display_name' => ['nullable', 'string', 'max:150'],
        ]);

        $branch->update([
            'mask_scope' => $validated['mask_scope'],
            'display_name' => filled($validated['display_name'] ?? null)
                ? trim((string) $validated['display_name'])
                : null,
        ]);

        return redirect()->route('admin.settings.identity-masking.index')->with('toast', [[
            'type' => 'success',
            'message' => 'Identity masking settings saved.',
            'duration' => 4000,
        ]]);
    }
}
