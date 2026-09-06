<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = strtolower(trim($user->role));

            // 1. Tenant Check (Agar company banned hai toh logout kar do)
            if ($user->tenant && $user->tenant->is_banned) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withInput(['email' => $request->email])
                    ->with('toast', [
                        [
                            'type' => 'error',
                            'message' => 'Your restaurant access has been cancelled. Please contact our support team.',
                            'duration' => 6000,
                        ],
                    ]);
            }

            // 2. --- REDIRECTION LOGIC (Standard Industry Level) ---

            // SuperAdmin ke liye alag raasta
            if ($role === 'superadmin') {
                // SuperAdmin should always land on the global dashboard,
                // even if the browser still remembers a tenant route.
                $request->session()->forget([
                    'impersonated_by',
                    'active_tenant_id',
                    'url.intended',
                ]);

                return redirect()->route('superadmin.dashboard');
            }

            // Chef ke liye direct KDS Module ka raasta
            if ($role === 'chef') {
                return redirect()->intended(route('chef.kds.index'));
            }

            // Waiter ke liye direct Table Management ka raasta
            if ($role === 'waiter') {
                return redirect()->intended(route('waiter.tables.index'));
            }

             if ($role === 'manager') {
                return redirect()->route('manager.dashboard');
            }

            // Baaki saare managers (Admin, Sales, Accounts, etc.) ke liye Dashboard
            $managementRoles = [
                'admin',
                'sales_manager',
                'purchase_manager',
                'account_manager',
                'auditor',
                'store_keeper',
            ];

            if (in_array($role, $managementRoles)) {
                return redirect()->route('admin.dashboard');
            }

            // Default fall back
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
