<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'code',
        'is_active',
    ];

    public function tables()
    {
        return $this->hasMany(Table::class, 'area_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}