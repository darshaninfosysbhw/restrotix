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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = strtolower(trim($user->role));

            // 1. Tenant Check (Agar company banned hai toh logout kar do)
            if ($user->tenant && $user->tenant->is_banned) {
                Auth::logout();
                return back()->withErrors(['email' => 'आपकी कंपनी का एक्सेस बंद कर दिया गया है।']);
            }

            // 2. --- REDIRECTION LOGIC (Standard Industry Level) ---

            // SuperAdmin ke liye alag raasta
            if ($role === 'superadmin') {
                return redirect()->intended(route('superadmin.dashboard'));
            }

            // Chef ke liye direct KDS Module ka raasta
            if ($role === 'chef') {
                return redirect()->intended(route('admin.kds.index'));
            }

            // Waiter ke liye direct Table Management ka raasta
            if ($role === 'waiter') {
                return redirect()->intended(route('admin.waiter.index'));
            }

            // Baaki saare managers (Admin, Sales, Accounts, etc.) ke liye Dashboard
            $managementRoles = [
                'admin',
                'manager',
                'sales_manager',
                'purchase_manager',
                'account_manager',
                'auditor',
                'store_keeper',
            ];

            if (in_array($role, $managementRoles)) {
                return redirect()->intended(route('admin.dashboard'));
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
