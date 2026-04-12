<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
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
        return $this->belongsToMany(Service::class);
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
}
