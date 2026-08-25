<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuSettingsController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()?->tenant;
        abort_unless($tenant, 403);

        $branches = $tenant->branches()
            ->latest()
            ->get(['id', 'branch_name', 'city', 'branch_menu_theme', 'updated_at']);

        return view('admin.settings.menu-settings', [
            'branches' => $branches,
        ]);
    }

    public function update(Request $request, Branch $branch)
    {
        $tenant = Auth::user()?->tenant;
        abort_unless($tenant && (int) $branch->tenant_id === (int) $tenant->id, 403);

        $validated = $request->validate([
            'branch_menu_theme' => 'required|in:dark,light',
        ]);

        $branch->update([
            'branch_menu_theme' => $validated['branch_menu_theme'],
        ]);

        return redirect()
            ->route('admin.settings.menu.index')
            ->with('toast', [[
                'type' => 'success',
                'message' => 'Menu theme updated successfully.',
                'duration' => 4000,
            ]]);
    }
}
