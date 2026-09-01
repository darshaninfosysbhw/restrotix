<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'table_id',
        'order_id',
        'order_invoice_id',
        'branch_payment_gateway_id',
        'gateway_slug',
        'gateway_name',
        'checkout_mode',
        'amount',
        'currency_code',
        'provider_reference',
        'payment_url',
        'provider_request',
        'provider_response',
        'status',
        'failure_reason',
        'initiated_at',
        'paid_at',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'branch_id' => 'integer',
        'table_id' => 'integer',
        'order_id' => 'integer',
        'order_invoice_id' => 'integer',
        'branch_payment_gateway_id' => 'integer',
        'amount' => 'decimal:2',
        'provider_request' => 'array',
        'provider_response' => 'array',
        'initiated_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice()
    {
        return $this->belongsTo(OrderInvoice::class, 'order_invoice_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function branchPaymentGateway()
    {
        return $this->belongsTo(BranchPaymentGateway::class);
    }
}
