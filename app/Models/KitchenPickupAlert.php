<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenPickupAlert extends Model
{
    protected $fillable = [
        'tenant_id', 'branch_id', 'order_id', 'table_id', 'kot_number',
        'status', 'ready_at', 'accepted_at', 'accepted_by_waiter_id',
    ];

    protected $casts = [
        'kot_number' => 'integer',
        'ready_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by_waiter_id');
    }
}
