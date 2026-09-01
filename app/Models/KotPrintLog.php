<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KotPrintLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'branch_id',
        'table_id',
        'table_number',
        'order_id',
        'kot_number',
        'printed_by',
        'printed_by_name',
        'print_source',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'branch_id' => 'integer',
        'table_id' => 'integer',
        'order_id' => 'integer',
        'kot_number' => 'integer',
        'printed_by' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function printer()
    {
        return $this->belongsTo(User::class, 'printed_by');
    }
}
