<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableAccessSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'table_id',
        'session_token',
        'status',
        'started_at',
        'last_activity_at',
        'expires_at',
        'grace_expires_at',
        'client_latitude',
        'client_longitude',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'branch_id' => 'integer',
        'table_id' => 'integer',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'expires_at' => 'datetime',
        'grace_expires_at' => 'datetime',
        'client_latitude' => 'decimal:8',
        'client_longitude' => 'decimal:8',
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

    public function isExpired(): bool
    {
        if ((string) ($this->status ?? '') === 'expired') {
            return true;
        }

        if ((string) ($this->status ?? '') === 'grace') {
            return $this->grace_expires_at && now()->greaterThanOrEqualTo($this->grace_expires_at);
        }

        return $this->expires_at && now()->greaterThanOrEqualTo($this->expires_at);
    }

    public function isCoolingDown(): bool
    {
        return (string) ($this->status ?? '') === 'grace'
            && $this->grace_expires_at
            && now()->lessThan($this->grace_expires_at);
    }

    public function isActive(): bool
    {
        return (string) ($this->status ?? '') === 'active'
            && !$this->isExpired();
    }
}
