<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'iso_code',
        'phone_code',
        'currency_id',
        'timezone',
        'flag',
        'is_active',
    ];

    protected $casts = [
        'currency_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
