<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $defaultCurrencyId = $request->attributes->get('default_currency_id');
        $defaultCurrencyCode = $request->attributes->get('default_currency_code');
        $defaultCurrencySymbol = $request->attributes->get('default_currency_symbol');

        $features = $this->resolveSelectedFeatures();
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
            'summary' => (string) ($this->summary ?? ''),
            'marketing_summary' => (string) $this->marketing_summary,
            'max_branches' => (int) $this->max_branches,
            'trial_days' => (int) $this->trial_days,
            'status' => $this->is_active ? 'Active' : 'Inactive',
            'is_recommended' => (bool) $this->is_recommended,
            'tenants_count' => (int) ($this->tenants_count ?? 0),
            'features' => $features,
            'enabled_features' => $enabledFeatures,
            'default_currency_id' => $defaultCurrencyId ? (int) $defaultCurrencyId : null,
            'default_currency_code' => $defaultCurrencyCode,
            'default_currency_symbol' => $defaultCurrencySymbol,
            'default_monthly_price' => $defaultPrice['monthly'] ?? null,
            'default_yearly_price' => $defaultPrice['yearly'] ?? null,
            'prices' => $prices,
        ];
    }

    private function resolveSelectedFeatures(): array
    {
        $selectedServices = $this->relationLoaded('services')
            ? $this->services
            : collect();

        if ($selectedServices->isNotEmpty()) {
            return $selectedServices
                ->filter(fn ($service) => !empty($service?->slug))
                ->mapWithKeys(fn ($service) => [(string) $service->slug => true])
                ->all();
        }

        $rawFeatures = $this->features ?? [];

        if (!is_array($rawFeatures) || empty($rawFeatures)) {
            return [];
        }

        $isList = array_values($rawFeatures) === $rawFeatures;

        if ($isList) {
            return collect($rawFeatures)
                ->filter(fn ($featureKey) => is_string($featureKey) && trim($featureKey) !== '')
                ->mapWithKeys(fn ($featureKey) => [trim((string) $featureKey) => true])
                ->all();
        }

        return collect($rawFeatures)
            ->filter(fn ($enabled) => filter_var($enabled, FILTER_VALIDATE_BOOLEAN))
            ->keys()
            ->mapWithKeys(fn ($featureKey) => [(string) $featureKey => true])
            ->all();
    }
}
