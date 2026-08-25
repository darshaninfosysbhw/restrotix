<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'order_id',
        'invoice_number',
        'item_count',
        'total_qty',
        'subtotal_before_discount',
        'subtotal',
        'item_discount_amount',
        'subtotal_after_item_discount',
        'overall_discount_percent',
        'discount_amount',
        'overall_discount_amount',
        'taxable_amount',
        'tax_setting',
        'tax_rate',
        'tax_rate_snapshot',
        'tax_amount',
        'payment_mode',
        'payment_method',
        'tender_amount',
        'change_amount',
        'paid_amount',
        'due_amount',
        'customer_name_snapshot',
        'table_number_snapshot',
        'cashier_user_id',
        'notes_snapshot',
        'grand_total',
        'status',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'branch_id' => 'integer',
        'order_id' => 'integer',
        'item_count' => 'integer',
        'total_qty' => 'integer',
        'cashier_user_id' => 'integer',
        'subtotal' => 'decimal:2',
        'subtotal_before_discount' => 'decimal:2',
        'item_discount_amount' => 'decimal:2',
        'subtotal_after_item_discount' => 'decimal:2',
        'overall_discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'overall_discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'tax_rate_snapshot' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tender_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class, 'invoice_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'invoice_id');
    }
}
