<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCountry;

class Tenant extends Model
{
    use HasFactory;
    use BelongsToCountry;

    protected $fillable = [
        'plan_id',
        'country_id',
        'currency_id',
        'company_name',
        'slug',
        'owner_name',
        'logo',
        'subscription_status',
        'billing_cycle',
        'subscription_ends_at', // Main Expiry Date
        'is_banned',
    ];

    protected $casts = [
        'subscription_ends_at' => 'datetime',
        'country_id' => 'integer',
        'currency_id' => 'integer',
        'plan_id' => 'integer',
        'is_banned' => 'boolean',
    ];

    // --- Relationships ---
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'tenant_service')
            ->using(TenantService::class)
            ->withPivot('status', 'expires_at')
            ->withTimestamps();
    }

    public function branchPaymentGateways()
    {
        return $this->hasMany(BranchPaymentGateway::class);
    }

    // --- Scopes ---
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_banned', false)
            ->where(function ($q) {
                $q->whereDate('subscription_ends_at', '>=', now())
                    ->orWhere('subscription_status', 'active');
            });
    }

    // --- Business Logic Methods ---

    /**
     * क्या मेन गेट खुला है? (Trial or Paid)
     */
    public function hasActiveInternalSubscription(): bool
    {
        if ($this->is_banned) return false;

        // अगर एक्सपायरी डेट अभी भविष्य में है, तो एक्सेस दो
        return $this->subscription_ends_at && now()->lessThanOrEqualTo($this->subscription_ends_at);
    }

    /**
     * क्या सबस्क्रिप्शन खत्म हो गया है?
     */
    public function isExpired(): bool
    {
        $status = strtolower((string) ($this->subscription_status ?? ''));

        if ($status === 'active') {
            return false;
        }

        if ($status === 'expired') {
            return true;
        }

        if ($this->is_banned) {
            return true;
        }

        if (!$this->subscription_ends_at) return true;
        return now()->greaterThan($this->subscription_ends_at);
    }

    /**
     * ट्रायल के कितने दिन बचे हैं? (UI के लिए)
     */
    public function daysRemaining(): int
    {
        if (!$this->subscription_ends_at) return 0;
        return now()->diffInDays($this->subscription_ends_at, false);
    }

    public function canAddBranch(): bool
    {
        if (!$this->plan) return false;
        return $this->branches()->count() < $this->plan->max_branches;
    }


    // ----SUPER ADMIN DASHBOARD SCOPES----
    public function scopeRecentForDashboard(Builder $query): Builder
    {
        return $query->withCount('branches')
            ->with(['users:id,tenant_id,email,role'])
            ->latest();
    }

    public function scopeTrial(Builder $query): Builder
    {
        return $query->where('subscription_status', 'trial')
            ->where('subscription_ends_at', '>=', now());
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where(function (Builder $subQuery) {
            $subQuery->where('is_banned', true)
                ->orWhere('subscription_status', 'pending');
        });
    }

    public function scopeBanned(Builder $query): Builder
    {
        return $query->where('is_banned', true);
    }
    public function scopePlanBreakdown(Builder $query): Builder
    {
        return $query->select('subscription_status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('subscription_status')
            ->orderByDesc('total');
    }
}
