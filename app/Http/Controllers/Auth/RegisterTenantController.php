<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class RegisterTenantController extends Controller
{
    public function index()
    {
        // मान लो डिफॉल्ट करेंसी INR (ID: 1) है
        $currencyId = session('currency_id', 1);

        $plans = Plan::with(['services', 'prices'])
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($plan) use ($currencyId) {
                $priceRecord = $plan->getPriceForCurrency($currencyId);
                return $priceRecord ? $priceRecord->monthly_price : 0;
            });

        return view('index', compact('plans'));
    }

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
