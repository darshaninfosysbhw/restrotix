<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'currency_id',
        'price',
    ];

    protected $casts = [
        'service_id' => 'integer',
        'currency_id' => 'integer',
        'price' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
