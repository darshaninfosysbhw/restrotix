<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class QrOrderSettingsController extends Controller
{
    private function branch(Request $request): Branch
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['admin', 'manager'], true), 403);
        $branchId = $user->role === 'admin'
            ? $request->session()->get('active_branch_id', $user->branch_id)
            : $user->branch_id;

        return Branch::where('tenant_id', $user->tenant_id)->whereKey($branchId)->firstOrFail();
    }

    public function index(Request $request)
    {
        return view('admin.settings.qr-orders', ['branch' => $this->branch($request)]);
    }

    public function update(Request $request)
    {
        $branch = $this->branch($request);
        $data = $request->validate([
            'branch_id' => ['required', 'integer', 'in:'.$branch->id],
            'auto_accept_qr_orders' => ['required', 'boolean'],
        ]);
        $branch->update(['auto_accept_qr_orders' => $data['auto_accept_qr_orders']]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'QR order settings saved.',
                'auto_accept_qr_orders' => $branch->auto_accept_qr_orders,
            ]);
        }

        return redirect()->route('admin.settings.qr-orders.index')->with('toast', [[
            'type' => 'success', 'message' => 'QR order settings saved.', 'duration' => 4000,
        ]]);
    }
}
