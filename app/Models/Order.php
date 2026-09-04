<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $appends = [
        'order_by_label',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
    ];

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'table_id',
        'order_number',
        'table_number',
        'order_type',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'paid_amount',
        'status',
        'kitchen_status',
        'payment_status',
        'notes',
        'source',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function kitchenPickupAlerts()
    {
        return $this->hasMany(KitchenPickupAlert::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoice()
    {
        return $this->hasOne(OrderInvoice::class);
    }

    public function paymentSessions()
    {
        return $this->hasMany(PaymentSession::class);
    }

    public function isGuestOrder(): bool
    {
        $source = strtolower(trim((string) ($this->source ?? '')));
        $orderType = strtolower(trim((string) ($this->order_type ?? '')));

        return $source === 'qr' || $orderType === 'self_order';
    }

    public function getOrderByLabelAttribute(): string
    {
        if ($this->isGuestOrder()) {
            return 'Guest';
        }

        $creatorName = trim((string) data_get($this, 'creator.name', ''));

        return $creatorName !== '' ? $creatorName : 'Guest';
    }
}
