<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItemVariant extends Model
{
    protected $fillable = [
        'menu_item_id',
        'name',
        'base_price',
        'sale_price',
        'is_available'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    // Yeh variant kis item (dish) ka hai
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
