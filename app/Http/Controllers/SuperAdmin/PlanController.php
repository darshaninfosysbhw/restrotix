<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuperAdmin\PlanResource;
use App\Models\Currency;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    private const FEATURE_KEYS = [
        'inventory_management',
        'ai_analytics',
        'staff_management',
        'kitchen_display_system',
        'whatsapp_integration',
    ];

    public function index(Request $request)
    {
        $activeCurrencies = Currency::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('code')
            ->get(['id', 'name', 'code', 'symbol', 'decimal_places', 'symbol_position', 'is_default']);

        $defaultCurrency = $activeCurrencies->firstWhere('is_default', true) ?? $activeCurrencies->first();

        $plans = Plan::query()
            ->withCount('tenants')
            ->with(['prices.currency'])
            ->latest()
            ->get();

        $request->attributes->set('default_currency_id', $defaultCurrency?->id);
        $request->attributes->set('default_currency_code', $defaultCurrency?->code);
        $request->attributes->set('default_currency_symbol', $defaultCurrency?->symbol);

        $plansMapped = collect(PlanResource::collection($plans)->resolve($request));

        $planStats = [
            'total' => $plansMapped->count(),
            'active' => $plansMapped->where('status', 'Active')->count(),
            'inactive' => $plansMapped->where('status', 'Inactive')->count(),
            'recommended' => $plansMapped->where('is_recommended', true)->count(),
        ];

        return view('superadmin.master-settings.plan.index', [
            'plans' => $plansMapped,
            'planStats' => $planStats,
            'currencies' => $activeCurrencies,
            'featureKeys' => self::FEATURE_KEYS,
        ]);
    }

    public function store(Request $request)
    {
        $activeCurrencyIds = $this->activeCurrencyIds();
        $validator = $this->validatePlanRequest($request, $activeCurrencyIds);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [[
                'type' => 'error',
                'message' => $validator->errors()->first(),
                'duration' => 5000,
            ]]);
        }

        try {
            DB::transaction(function () use ($request, $validator, $activeCurrencyIds) {
                $payload = $validator->validated();

                $plan = Plan::create([
                    'name' => $payload['name'],
                    'slug' => $this->buildUniqueSlug($payload['name']),
                    'max_branches' => $payload['max_branches'],
                    'trial_days' => $payload['trial_days'] ?? 0,
                    'features' => $this->normalizeFeatures($payload['features'] ?? []),
                    'is_active' => $payload['status'] === 'Active',
                    'is_recommended' => $request->boolean('is_recommended'),
                ]);

                if ($plan->is_recommended) {
                    Plan::where('id', '!=', $plan->id)->update(['is_recommended' => false]);
                }

                foreach ($activeCurrencyIds as $currencyId) {
                    $monthly = data_get($payload, "prices.$currencyId.monthly", 0);
                    $yearly = data_get($payload, "prices.$currencyId.yearly", 0);

                    $plan->prices()->updateOrCreate(
                        ['currency_id' => $currencyId],
                        ['monthly_price' => $monthly, 'yearly_price' => $yearly]
                    );
                }
            });

            return redirect()->back()->with('toast', [[
                'type' => 'success',
                'message' => 'Plan added successfully!',
                'duration' => 5000,
            ]]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [[
                'type' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'duration' => 5000,
            ]]);
        }
    }

    public function update(Request $request, Plan $plan)
    {
        $activeCurrencyIds = $this->activeCurrencyIds();
        $validator = $this->validatePlanRequest($request, $activeCurrencyIds, $plan);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [[
                'type' => 'error',
                'message' => $validator->errors()->first(),
                'duration' => 5000,
            ]]);
        }

        try {
            DB::transaction(function () use ($request, $validator, $plan, $activeCurrencyIds) {
                $payload = $validator->validated();

                $isRecommended = $request->boolean('is_recommended');

                $plan->update([
                    'name' => $payload['name'],
                    'slug' => $this->buildUniqueSlug($payload['name'], $plan->id),
                    'max_branches' => $payload['max_branches'],
                    'trial_days' => $payload['trial_days'] ?? 0,
                    'features' => $this->normalizeFeatures($payload['features'] ?? []),
                    'is_active' => $payload['status'] === 'Active',
                    'is_recommended' => $isRecommended,
                ]);

                if ($isRecommended) {
                    Plan::where('id', '!=', $plan->id)->update(['is_recommended' => false]);
                }

                foreach ($activeCurrencyIds as $currencyId) {
                    $monthly = data_get($payload, "prices.$currencyId.monthly", 0);
                    $yearly = data_get($payload, "prices.$currencyId.yearly", 0);

                    $plan->prices()->updateOrCreate(
                        ['currency_id' => $currencyId],
                        ['monthly_price' => $monthly, 'yearly_price' => $yearly]
                    );
                }
            });

            return redirect()->back()->with('toast', [[
                'type' => 'success',
                'message' => 'Plan updated successfully!',
                'duration' => 5000,
            ]]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [[
                'type' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'duration' => 5000,
            ]]);
        }
    }

    public function destroy(Plan $plan)
    {
        try {
            if ($plan->tenants()->exists()) {
                return redirect()->back()->with('toast', [[
                    'type' => 'error',
                    'message' => 'Subscribers are attached to this plan. Move subscribers first.',
                    'duration' => 5000,
                ]]);
            }

            $plan->delete();

            return redirect()->back()->with('toast', [[
                'type' => 'success',
                'message' => 'Plan deleted successfully!',
                'duration' => 5000,
            ]]);
        } catch (\Exception $e) {
            return redirect()->back()->with('toast', [[
                'type' => 'error',
                'message' => 'Unable to delete plan: ' . $e->getMessage(),
                'duration' => 5000,
            ]]);
        }
    }

    private function validatePlanRequest(Request $request, array $activeCurrencyIds, ?Plan $plan = null)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('plans', 'name')->ignore($plan?->id)],
            'trial_days' => 'required|integer|min:0',
            'max_branches' => 'required|integer|min:1',
            'status' => 'required|in:Active,Inactive',
            'is_recommended' => 'nullable|boolean',
            'features' => 'required|array',
        ];

        foreach (self::FEATURE_KEYS as $featureKey) {
            $rules["features.$featureKey"] = 'nullable|boolean';
        }

        foreach ($activeCurrencyIds as $currencyId) {
            $rules["prices.$currencyId.monthly"] = 'required|numeric|min:0';
            $rules["prices.$currencyId.yearly"] = 'required|numeric|min:0';
        }

        return Validator::make($request->all(), $rules);
    }

    private function normalizeFeatures(array $rawFeatures): array
    {
        $normalized = [];

        foreach (self::FEATURE_KEYS as $key) {
            $normalized[$key] = filter_var($rawFeatures[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        return $normalized;
    }

    private function activeCurrencyIds(): array
    {
        return Currency::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->pluck('id')
            ->all();
    }

    private function buildUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $root = $base !== '' ? $base : 'plan';
        $slug = $root;
        $counter = 2;

        while (
            Plan::query()
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $root . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
