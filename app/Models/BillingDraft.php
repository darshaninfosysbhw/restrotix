<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'table_id',
        'table_number',
        'order_id',
        'held_by_user_id',
        'payload_json',
        'held_at',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'branch_id' => 'integer',
        'table_id' => 'integer',
        'order_id' => 'integer',
        'held_by_user_id' => 'integer',
        'payload_json' => 'array',
        'held_at' => 'datetime',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function heldBy()
    {
        return $this->belongsTo(User::class, 'held_by_user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
