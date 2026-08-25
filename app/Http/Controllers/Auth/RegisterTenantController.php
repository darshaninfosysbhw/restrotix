<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class RegisterTenantController extends Controller
{
    // Home logic moved to App\Http\Controllers\HomeController@index
    // This controller continues to own tenant registration related views.

    public function showPricing()
    {
        $plans = Plan::with('services')
            ->where('is_active', true)
            ->orderBy('monthly_price')
            ->get();

        return view('auth.pricing', compact('plans'));
    }

    public function showPlan(Plan $plan)
    {
        if (!$plan->is_active) {
            abort(404);
        }

        $plan->load('services');

        return view('plans.show', compact('plan'));
    }

    public function showRegister(Request $request)
    {
        $selectedPlan = Plan::where('slug', $request->plan)->first();

        if (!$selectedPlan) {
            return redirect()->route('pricing')->with('error', 'Please select a valid plan.');
        }

        return view('auth.register-tenant', compact('selectedPlan'));
    }
}
