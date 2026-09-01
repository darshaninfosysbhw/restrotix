<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $currencyId = session('currency_id', 1);

        $plans = Plan::with(['services', 'prices.currency'])
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($plan) use ($currencyId) {
                $priceRecord = $plan->prices->firstWhere('currency_id', $currencyId) ?? $plan->prices->first();
                return $priceRecord ? (float) $priceRecord->monthly_price : 0;
            });

        return view('index', compact('plans'));
    }
}
