<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantService extends Pivot
{
    protected $table = 'tenant_service';

    // अगर पिवोट टेबल में 'id' कॉलम है (जो कि तुमने माइग्रेशन में रखा था)
    public $incrementing = true;

    protected $fillable = [
        'tenant_id',
        'service_id',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
