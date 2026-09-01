<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'billing_cycle',
        'amount',
        'currency_id',
        'status',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    // यह हिस्ट्री किस टेनेंट की है?
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // यह हिस्ट्री किस प्लान की है?
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
