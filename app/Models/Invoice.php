<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'amount',
        'currency_id',
        'status',
        'payment_method',
        'transaction_id',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
