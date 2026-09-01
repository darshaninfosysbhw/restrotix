<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $appends = [
        'order_by_label',
    ];

    protected $fillable = [
        'order_id',
        'source',
        'created_by',
        'kot_number',
        'menu_item_id',
        'menu_item_variant_id',
        'invoice_id',
        'item_name',
        'price',
        'quantity',
        'total',
        'applied_discount',
        'notes',
        'status',
        'started_at',
        'ready_at',
        'served_at',
        'rejected_at',
        'rejection_reason',
        'kitchen_type',
        'preparation_time',
        'is_delayed',
        'priority'
    ];

    protected $casts = [
        'kot_number' => 'integer',
        'invoice_id' => 'integer',
        'applied_discount' => 'decimal:2',
        'started_at' => 'datetime',
        'ready_at' => 'datetime',
        'served_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoice()
    {
        return $this->belongsTo(OrderInvoice::class, 'invoice_id');
    }

    public function menuItem()
    {
        // Agar tera model 'MenuItem' naam se hai:
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    // Har ordered item kisi variant se belong kar sakta hai (e.g. Half Momos)
    public function variant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MenuItemVariant::class, 'menu_item_variant_id');
    }

    // Ek ordered item ke sath customer kai saare addons le sakta hai
    public function orderItemAddons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItemAddon::class, 'order_item_id');
    }

    public function getOrderByLabelAttribute(): string
    {
        $source = strtolower(trim((string) ($this->source ?? '')));
        if ($source === 'qr' || $source === 'self_order') {
            return 'Guest';
        }

        $creatorName = trim((string) data_get($this, 'creator.name', ''));
        if ($creatorName !== '') {
            return $creatorName;
        }

        $fallbackOrderCreator = trim((string) data_get($this, 'order.creator.name', ''));

        return $fallbackOrderCreator !== '' ? $fallbackOrderCreator : 'Guest';
    }
}
