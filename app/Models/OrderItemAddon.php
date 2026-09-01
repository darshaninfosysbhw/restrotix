<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemAddon extends Model
{
    protected $fillable = [
        'order_item_id',
        'menu_item_addon_id',
        'addon_name',
        'price',
        'quantity',
        'applied_discount',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'applied_discount' => 'decimal:2',
    ];

    // Yeh addon kis ordered item ka part hai
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function masterAddon(): BelongsTo
    {
        return $this->belongsTo(MenuItemAddon::class, 'menu_item_addon_id');
    }
}
