<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use App\Models\Currency;

class SetUserCurrency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */


    public function handle(Request $request, Closure $next): Response
    {
        // अगर सेशन में पहले से करेंसी सेट है, तो आगे बढ़ो
        if ($request->session()->has('currency_id')) {
            return $next($request);
        }

        $userIp = $request->ip();
        $defaultCurrencyCode = Currency::query()
            ->where('is_default', true)
            ->where('is_active', true)
            ->value('code')
            ?? Currency::query()
                ->where('is_active', true)
                ->value('code')
            ?? 'NPR';

        $currencyCode = $defaultCurrencyCode;

        // Localhost टेस्टिंग के लिए (चूंकि तुम अभी नेपाल में हो)
        if ($userIp === '127.0.0.1' || $userIp === '::1') {
            $currencyCode = 'NPR';
        } else {

            try {
                /** @var \Illuminate\Http\Client\Response $response */
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$userIp}");
                if ($response->successful()) {
                    $country = $response->json()['countryCode'] ?? 'IN';
                    $currencyCode = ($country === 'NP') ? 'NPR' : 'INR';
                }
            } catch (\Exception $e) {
                $currencyCode = $defaultCurrencyCode;
            }
        }

        // डेटाबेस से ID निकाल कर सेशन में डालो
        $currency = Currency::query()
            ->where('code', $currencyCode)
            ->where('is_active', true)
            ->first()
            ?? Currency::query()
                ->where('code', $defaultCurrencyCode)
                ->where('is_active', true)
                ->first()
            ?? Currency::query()
                ->where('is_active', true)
                ->first();

        if (! $currency) {
            return $next($request);
        }

        session([
            'currency_id' => $currency->id,
            'currency_code' => $currency->code,
            'currency_symbol' => $currency->symbol
        ]);
        return $next($request);
    }
}
