<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'payment_mode',
        'payment_method',
        'amount',
        'tender_amount',
        'change_amount',
        'transaction_ref',
        'gateway_response',
        'status',
        'verified_by_user_id',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'invoice_id' => 'integer',
        'amount' => 'decimal:2',
        'tender_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'verified_by_user_id' => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(OrderInvoice::class, 'invoice_id');
    }
}
