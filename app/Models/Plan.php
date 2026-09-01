<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Plan extends Model
{
    public const FEATURE_LABELS = [
        'inventory_management' => 'Inventory Management',
        'ai_analytics' => 'AI Analytics',
        'staff_management' => 'Staff Management',
        'kitchen_display_system' => 'Kitchen Display System (KDS)',
        'whatsapp_integration' => 'WhatsApp Integration',
        'self_payment_enabled' => 'Self Payment Enabled',
    ];

    protected $fillable = [
        'name',
        'slug',
        'summary',
        'max_branches',
        'features',
        'trial_days',
        'is_active',
        'is_recommended',
    ];

    protected $casts = [
        'max_branches' => 'integer',
        'trial_days' => 'integer',
        'is_active' => 'boolean',
        'is_recommended' => 'boolean',
        'features' => 'array',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }

    public function prices()
    {
        return $this->hasMany(PlanPrice::class, 'plan_id');
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function getPriceForCurrency($currencyId)
    {
        return $this->prices()->where('currency_id', $currencyId)->first();
    }

    public function getBranchLimitLabelAttribute(): string
    {
        $branchCount = (int) ($this->max_branches ?? 0);

        if ($branchCount === 1) {
            return '1 Branch Only';
        }

        if ($branchCount > 1) {
            return 'Up to ' . $branchCount . ' branches';
        }

        return 'Unlimited branches';
    }

    public function getMarketingSummaryAttribute(): string
    {
        $customSummary = trim((string) ($this->summary ?? ''));
        if ($customSummary !== '') {
            return $customSummary;
        }

        return match ((string) ($this->slug ?? '')) {
            'single-outlet' => 'Perfect for standalone restaurants',
            'enterprise' => 'White-label options',
            'multi-branch' => 'Built for growing restaurant chains',
            default => 'Flexible for your restaurant business',
        };
    }

    public function hasFeature(string $featureKey): bool
    {
        $featureKey = trim($featureKey);

        if ($featureKey === '') {
            return false;
        }

        $serviceSlugs = $this->relationLoaded('services')
            ? $this->services->pluck('slug')->filter()->values()->all()
            : $this->services()->pluck('slug')->filter()->values()->all();

        if (in_array($featureKey, $serviceSlugs, true)) {
            return true;
        }

        $features = $this->features ?? [];

        if (!is_array($features)) {
            return false;
        }

        $isList = array_values($features) === $features;

        if ($isList) {
            return in_array($featureKey, $features, true);
        }

        return (bool) data_get($features, $featureKey, false);
    }

    public function getDisplayFeatures(): array
    {
        $items = [];
        $branchLimitLabel = trim((string) $this->branch_limit_label);
        if ($branchLimitLabel !== '') {
            $items[] = [
                'name' => $branchLimitLabel,
                'available' => true,
                'bold' => true,
            ];
        }

        $selectedServices = $this->relationLoaded('services')
            ? $this->services
            : $this->services()->orderBy('name')->get();

        if ($selectedServices->isNotEmpty()) {
            return array_merge($items, $selectedServices
                ->filter(fn ($service) => !empty($service?->name))
                ->map(function ($service) {
                    return [
                        'name' => (string) $service->name,
                        'available' => true,
                        'bold' => false,
                    ];
                })
                ->values()
                ->all());
        }

        $features = $this->features ?? [];

        if (!is_array($features) || empty($features)) {
            return $items;
        }

        $isList = array_values($features) === $features;

        if ($isList) {
            foreach ($features as $feature) {
                if (is_array($feature)) {
                    $name = trim((string) ($feature['name'] ?? ''));
                    $available = (bool) ($feature['available'] ?? true);
                    $bold = (bool) ($feature['bold'] ?? false);
                } else {
                    $rawKey = trim((string) $feature);

                    if ($rawKey === '') {
                        continue;
                    }

                    $name = self::legacyFeatureLabel($rawKey);
                    $available = true;
                    $bold = false;
                }

                if ($name !== '') {
                    $items[] = [
                        'name' => $name,
                        'available' => $available,
                        'bold' => $bold,
                    ];
                }
            }

            return $items;
        }

        foreach (self::FEATURE_LABELS as $key => $label) {
            if (data_get($features, $key, false)) {
                $items[] = [
                    'name' => $label,
                    'available' => true,
                    'bold' => false,
                ];
            }
        }

        foreach ($features as $key => $value) {
            if (array_key_exists($key, self::FEATURE_LABELS)) {
                continue;
            }

            if (is_array($value)) {
                $name = trim((string) data_get($value, 'name', ''));
                $available = (bool) data_get($value, 'available', true);
                $bold = (bool) data_get($value, 'bold', false);

                if ($name === '' && $available) {
                    $name = self::legacyFeatureLabel((string) $key);
                }

                if ($name !== '') {
                    $items[] = [
                        'name' => $name,
                        'available' => $available,
                        'bold' => $bold,
                    ];
                }

                continue;
            }

            if ((bool) $value) {
                $items[] = [
                    'name' => self::legacyFeatureLabel((string) $key),
                    'available' => true,
                    'bold' => false,
                ];
            }
        }

        return $items;
    }

    protected static function legacyFeatureLabel(string $key): string
    {
        $key = trim($key);

        if ($key === '') {
            return '';
        }

        if (array_key_exists($key, self::FEATURE_LABELS)) {
            return self::FEATURE_LABELS[$key];
        }

        if (str_contains($key, '_') || str_contains($key, '-')) {
            return Str::headline(str_replace(['_', '-'], ' ', $key));
        }

        return $key;
    }
}
