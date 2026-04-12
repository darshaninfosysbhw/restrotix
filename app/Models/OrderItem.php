<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'item_name',
        'price',
        'quantity',
        'total',
        'notes',
        'status',
        'started_at',
        'ready_at',
        'served_at',
        'rejection_reason',
        'kitchen_type',
        'preparation_time',
        'is_delayed',
        'priority'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        // Agar tera model 'MenuItem' naam se hai:
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}
