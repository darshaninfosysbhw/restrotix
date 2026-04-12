<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCountry;

class Branch extends Model
{
    use HasFactory;
    use BelongsToCountry;

    protected $fillable = [
        'tenant_id',
        'branch_name',
        'contact_number',
        'branch_email',
        'country_id',
        'currency_id',
        'timezone',
        'state',
        'city',
        'pincode',
        'full_address',
        'latitude',
        'longitude',
        'offline_billing_enabled',
    ];

    protected $casts = [
        'offline_billing_enabled' => 'boolean',
        'tenant_id' => 'integer',
        'country_id' => 'integer',
        'currency_id' => 'integer',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function manager()
    {
        return $this->hasOne(\App\Models\User::class)->where('role', 'manager');
    }
}
