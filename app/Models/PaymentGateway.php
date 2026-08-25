<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'credentials',
        'supported_currencies',
        'mode',
        'is_active',
        'logo'
    ];

    // Casting JSON to Array
    protected $casts = [
        'credentials' => 'array',
        'supported_currencies' => 'array',
        'is_active' => 'boolean',
    ];

    // Scope for active gateways
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function branchConfigurations()
    {
        return $this->hasMany(BranchPaymentGateway::class, 'payment_gateway_id');
    }
}
