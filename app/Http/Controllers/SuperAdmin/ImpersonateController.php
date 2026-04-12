<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function impersonate($id)
    {
        $originalAdmin = Auth::user(); // SuperAdmin object
        $userToImpersonate = User::findOrFail($id);

        // Security check
        if ($originalAdmin->id == $userToImpersonate->id) {
            return back()->with('toast', [['type' => 'error', 'message' => 'You cannot impersonate yourself!']]);
        }

        // 1. SuperAdmin ki ID save karo (Wapas aane ke liye)
        session()->put('impersonated_by', $originalAdmin->id);

        // 2. IMPORTANT: Target Tenant ki ID session mein fix karo
        // Isse pura application ab isi Tenant ke context mein chalega
        session()->put('active_tenant_id', $userToImpersonate->tenant_id);

        // 3. Login as new user
        Auth::login($userToImpersonate);

        return redirect()->route('admin.dashboard')->with('toast', [
            ['type' => 'success', 'message' => 'Now managing: ' . $userToImpersonate->tenant->company_name, 'duration' => 5000]
        ]);
    }

    public function leave()
    {
        if (!session()->has('impersonated_by')) {
            return redirect()->back();
        }

        // 1. Tenant ID ko session se delete karo (Kyunki ab hum SuperAdmin hain)
        session()->forget('active_tenant_id');

        // 2. Wapas purane admin ko login karo
        $adminId = session()->pull('impersonated_by');
        Auth::loginUsingId($adminId);

        return redirect()->route('superadmin.dashboard')->with('toast', [
            ['type' => 'success', 'message' => 'Back to SuperAdmin Control', 'duration' => 3000]
        ]);
    }
}
