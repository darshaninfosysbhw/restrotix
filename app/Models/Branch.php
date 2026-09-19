<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToCountry;

class Branch extends Model
{
    use HasFactory;
    use BelongsToCountry;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'branch_name',
        'mask_scope',
        'display_name',
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
        'is_vat_registered',
        'auto_accept_qr_orders',
        'tax_setting',
        'tax_rate',
        'pan_vat_number',
        'branch_menu_theme',
    ];

    protected $casts = [
        'is_vat_registered' => 'boolean',
        'offline_billing_enabled' => 'boolean',
        'auto_accept_qr_orders' => 'boolean',
        'tenant_id' => 'integer',
        'country_id' => 'integer',
        'currency_id' => 'integer',
    ];

    public function getEffectiveTaxRateAttribute(): float
    {
        return $this->is_vat_registered ? max(0, (float) $this->tax_rate) : 0.0;
    }

    public function getCustomerTitleAttribute(): string
    {
        if (in_array($this->mask_scope, ['public_only', 'everywhere'], true)
            && $this->tenant?->canMaskIdentity()) {
            return $this->display_name ?: 'Guest Check';
        }

        return (string) $this->branch_name;
    }

    public function getSystemTitleAttribute(): string
    {
        if ($this->mask_scope === 'everywhere' && $this->tenant?->canMaskIdentity()) {
            return $this->display_name ?: (string) $this->branch_name;
        }

        return (string) $this->branch_name;
    }

    public function getIsCustomerIdentityMaskedAttribute(): bool
    {
        return in_array($this->mask_scope, ['public_only', 'everywhere'], true)
            && $this->tenant?->canMaskIdentity();
    }

    public function getCustomerBrandNameAttribute(): string
    {
        if ($this->is_customer_identity_masked) {
            return $this->customer_title;
        }

        return trim((string) ($this->tenant?->company_name ?? '')) ?: $this->customer_title;
    }

    public function getCustomerBranchSubtitleAttribute(): string
    {
        return $this->is_customer_identity_masked ? '' : (string) $this->branch_name;
    }

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

    public function paymentGateways()
    {
        return $this->hasMany(BranchPaymentGateway::class);
    }
}
