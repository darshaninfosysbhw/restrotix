<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'city',
        'commission_rate',
        'currency_id',
        'is_verified',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
