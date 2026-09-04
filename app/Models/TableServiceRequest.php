<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableServiceRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'branch_id',
        'table_id',
        'type',
        'notes',
        'handled_by_waiter_id',
        'target_waiter_id',
        'status',
        'requested_at',
        'accepted_at',
        'completed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function handledByWaiter()
    {
        return $this->belongsTo(User::class, 'handled_by_waiter_id');
    }
    public function targetWaiter()
    {
        return $this->belongsTo(User::class, 'target_waiter_id');
    }
}
