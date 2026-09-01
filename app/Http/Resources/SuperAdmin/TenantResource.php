<?php

namespace App\Http\Resources\SuperAdmin;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HasNepaleseDate;

class TenantResource extends JsonResource
{
    use HasNepaleseDate;

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $subscriptionStatus = strtolower((string) ($this->subscription_status ?? ''));
        if ($subscriptionStatus === '') {
            if ($this->is_banned) {
                $subscriptionStatus = 'canceled';
            } elseif ($this->subscription_ends_at && now()->lt($this->subscription_ends_at)) {
                $subscriptionStatus = 'trial';
            } else {
                $subscriptionStatus = 'active';
            }
        }

        $statusLabel = match ($subscriptionStatus) {
            'trial' => 'Trial',
            'active' => 'Active',
            'pending', 'expired' => 'Expired',
            'canceled', 'cancelled' => 'Canceled',
            default => ucfirst($subscriptionStatus),
        };

        $joinedDate = $this->created_at;
        $joinedBs = $joinedDate ? $this->toFullBsDate($joinedDate) : '-';
        $adminUser = $this->users->firstWhere('role', 'admin') ?? $this->users->first();

        return [
            'id' => $this->id,
            'name' => $this->company_name,
            'owner' => $this->owner_name,
            'country_id' => $this->country_id,
            'owner_user_id' => optional($adminUser)->id,
            'email' => optional($adminUser)->email ?? '-',
            'phone' => optional($adminUser)->phone_number ?? '-',
            'city' => optional($this->branches->first())->city ?? '-',
            'slug' => $this->slug,
            'plan' => $this->plan?->name ?? ucfirst($this->subscription_plan ?? 'starter'),
            'plan_id' => $this->plan_id,
            'plan_key' => $this->plan?->slug ?? strtolower((string) ($this->subscription_plan ?? 'starter')),
            'billing_cycle' => $this->billing_cycle ?? 'monthly',
            'branches' => $this->branches_count ?? 0,
            'status' => $statusLabel,
            'status_key' => $subscriptionStatus,
            'joined' => optional($joinedDate)->format('d M Y') ?? '-',
            'joined_bs' => $joinedBs,
        ];
    }
}
