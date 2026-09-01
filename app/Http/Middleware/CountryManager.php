<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Country;
use Illuminate\Support\Facades\View;

class CountryManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeCountry = null;

        if (session()->has('active_country_id')) {
            // सिलेक्टेड देश का पूरा डेटा (Currency के साथ) फेच करो
            $activeCountry = Country::with('currency')->find(session('active_country_id'));
        }

        // इसे पूरे ऐप की सभी Blade फाइल्स के लिए 'Global Variable' बना दो
        View::share('activeCountry', $activeCountry);
        return $next($request);
    }
}
