<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'status',
    ];

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'tenant_service')
            ->using(TenantService::class)   // 👈 Important
            ->withPivot('status', 'expires_at')
            ->withTimestamps();
    }

    public function prices()
    {
        return $this->hasMany(ServicePrice::class);
    }

    public function getPriceForCurrency($currencyId)
    {
        return $this->prices()->where('currency_id', $currencyId)->first();
    }

    public function scopeUsageSummary(Builder $query): Builder
    {
        return $query->leftJoin('tenant_service', 'tenant_service.service_id', '=', 'services.id')
            ->select('services.name')
            ->selectRaw('COUNT(tenant_service.id) as total')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total');
    }
}
