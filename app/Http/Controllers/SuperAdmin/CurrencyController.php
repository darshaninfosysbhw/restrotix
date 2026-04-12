<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuperAdmin\CurrencyResource;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::with('country')->latest()->get();
        $currenciesMapped = collect(CurrencyResource::collection($currencies)->resolve());
        $countries = Country::where('is_active', true)->orderBy('name')->get(['id', 'name', 'flag', 'iso_code']);

        $currencyStats = [
            'total' => $currenciesMapped->count(),
            'active' => $currenciesMapped->where('status', 'Active')->count(),
            'inactive' => $currenciesMapped->where('status', 'Inactive')->count(),
            'default' => $currenciesMapped->where('is_default', true)->count(),
        ];

        return view('superadmin.master-settings.currencies.index', [
            'currencies' => $currenciesMapped,
            'currencyStats' => $currencyStats,
            'countries' => $countries,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code',
            'symbol' => 'required|string|max:10',
            'country_id' => 'required|exists:countries,id',
            'exchange_rate' => 'required|numeric|min:0',
            'decimals' => 'required|integer|in:0,2,3',
            'position' => 'required|in:Prefix,Suffix',
            'status' => 'required|in:Active,Inactive',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [
                [
                    'type' => 'error',
                    'message' => $validator->errors()->first(),
                    'duration' => 5000,
                ],
            ]);
        }

        try {
            DB::transaction(function () use ($request, $validator) {
                $payload = $validator->validated();
                $isDefault = $request->boolean('is_default');

                if ($isDefault) {
                    Currency::query()->update(['is_default' => false]);
                }

                Currency::create([
                    'name' => $payload['name'],
                    'code' => strtoupper($payload['code']),
                    'symbol' => $payload['symbol'],
                    'country_id' => $payload['country_id'],
                    'exchange_rate' => $payload['exchange_rate'],
                    'decimal_places' => $payload['decimals'],
                    'symbol_position' => $payload['position'],
                    'is_active' => $payload['status'] === 'Active',
                    'is_default' => $isDefault || Currency::count() === 0,
                ]);
            });

            return redirect()->back()->with('toast', [
                ['type' => 'success', 'message' => 'Currency added successfully!', 'duration' => 5000]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [
                ['type' => 'error', 'message' => 'Something went wrong: ' . $e->getMessage(), 'duration' => 5000]
            ]);
        }
    }

    public function update(Request $request, Currency $currency)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'size:3',
                Rule::unique('currencies', 'code')->ignore($currency->id),
            ],
            'symbol' => 'required|string|max:10',
            'country_id' => 'required|exists:countries,id',
            'exchange_rate' => 'required|numeric|min:0',
            'decimals' => 'required|integer|in:0,2,3',
            'position' => 'required|in:Prefix,Suffix',
            'status' => 'required|in:Active,Inactive',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [
                [
                    'type' => 'error',
                    'message' => $validator->errors()->first(),
                    'duration' => 5000,
                ],
            ]);
        }

        try {
            DB::transaction(function () use ($request, $validator, $currency) {
                $payload = $validator->validated();
                $isDefault = $request->boolean('is_default');

                if ($isDefault) {
                    Currency::where('id', '!=', $currency->id)->update(['is_default' => false]);
                }

                $currency->update([
                    'name' => $payload['name'],
                    'code' => strtoupper($payload['code']),
                    'symbol' => $payload['symbol'],
                    'country_id' => $payload['country_id'],
                    'exchange_rate' => $payload['exchange_rate'],
                    'decimal_places' => $payload['decimals'],
                    'symbol_position' => $payload['position'],
                    'is_active' => $payload['status'] === 'Active',
                    'is_default' => $isDefault,
                ]);

                if (!Currency::where('is_default', true)->exists()) {
                    Currency::orderBy('id')->first()?->update(['is_default' => true]);
                }
            });

            return redirect()->back()->with('toast', [
                ['type' => 'success', 'message' => 'Currency updated successfully!', 'duration' => 5000]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [
                ['type' => 'error', 'message' => 'Something went wrong: ' . $e->getMessage(), 'duration' => 5000]
            ]);
        }
    }

    public function destroy(Currency $currency)
    {
        try {
            if ($currency->is_default) {
                return redirect()->back()->with('toast', [
                    ['type' => 'error', 'message' => 'Default currency cannot be deleted.', 'duration' => 5000]
                ]);
            }

            $currency->delete();

            return redirect()->back()->with('toast', [
                ['type' => 'success', 'message' => 'Currency deleted successfully!', 'duration' => 5000]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('toast', [
                ['type' => 'error', 'message' => 'Unable to delete currency: ' . $e->getMessage(), 'duration' => 5000]
            ]);
        }
    }
}
