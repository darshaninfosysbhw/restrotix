<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    private const FEATURE_KEYS = [
        'inventory_management',
        'ai_analytics',
        'staff_management',
        'kitchen_display_system',
        'whatsapp_integration',
    ];

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $defaultCurrencyId = $request->attributes->get('default_currency_id');
        $defaultCurrencyCode = $request->attributes->get('default_currency_code');
        $defaultCurrencySymbol = $request->attributes->get('default_currency_symbol');

        $features = $this->normalizeFeatures($this->features ?? []);
        $enabledFeatures = collect($features)
            ->filter(fn($enabled) => (bool) $enabled)
            ->keys()
            ->values()
            ->all();

        $pricesCollection = PlanPriceResource::collection($this->whenLoaded('prices'))->resolve($request);

        $prices = collect($pricesCollection)->mapWithKeys(function (array $price) {
            return [
                (string) ($price['currency_id'] ?? '') => [
                    'monthly' => (string) ($price['monthly'] ?? '0'),
                    'yearly' => (string) ($price['yearly'] ?? '0'),
                ],
            ];
        })->all();

        $defaultPrice = $defaultCurrencyId ? ($prices[(string) $defaultCurrencyId] ?? null) : null;

        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'max_branches' => (int) $this->max_branches,
            'trial_days' => (int) $this->trial_days,
            'status' => $this->is_active ? 'Active' : 'Inactive',
            'is_recommended' => (bool) $this->is_recommended,
            'tenants_count' => (int) ($this->tenants_count ?? 0),
            'features' => $features,
            'enabled_features' => $enabledFeatures,
            'default_currency_code' => $defaultCurrencyCode,
            'default_currency_symbol' => $defaultCurrencySymbol,
            'default_monthly_price' => $defaultPrice['monthly'] ?? null,
            'default_yearly_price' => $defaultPrice['yearly'] ?? null,
            'prices' => $prices,
        ];
    }

    private function normalizeFeatures($rawFeatures): array
    {
        $normalized = array_fill_keys(self::FEATURE_KEYS, false);

        if (!is_array($rawFeatures)) {
            return $normalized;
        }

        $isList = array_values($rawFeatures) === $rawFeatures;

        if ($isList) {
            foreach ($rawFeatures as $featureKey) {
                if (array_key_exists($featureKey, $normalized)) {
                    $normalized[$featureKey] = true;
                }
            }

            return $normalized;
        }

        foreach (self::FEATURE_KEYS as $key) {
            $normalized[$key] = filter_var($rawFeatures[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        return $normalized;
    }
}
