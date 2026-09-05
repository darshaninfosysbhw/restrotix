<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenNotificationLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'branch_id',
        'order_id',
        'order_item_id',
        'cancelled_by',
        'item_name',
        'table_number',
        'reason',
        'cancelled_at',
        'opened_at',
        'opened_by',
        'cleared_at',
        'cleared_by',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'opened_at' => 'datetime',
        'cleared_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function clearedBy()
    {
        return $this->belongsTo(User::class, 'cleared_by');
    }
}
