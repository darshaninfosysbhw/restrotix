<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'country_id',
        'exchange_rate',
        'decimal_places',
        'symbol_position',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'exchange_rate' => 'decimal:6',
        'decimal_places' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function planPrices()
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function servicePrices()
    {
        return $this->hasMany(ServicePrice::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function subscriptionHistories()
    {
        return $this->hasMany(SubscriptionHistory::class);
    }
}
