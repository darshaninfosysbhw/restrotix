<?php

namespace App\Http\Resources\SuperAdmin;

use App\Traits\HasNepaleseDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardRecentTenantResource extends JsonResource
{
    use HasNepaleseDate;

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

        $status = match ($subscriptionStatus) {
            'trial' => 'Trial',
            'active' => 'Active',
            'pending', 'expired' => 'Expired',
            'canceled', 'cancelled' => 'Canceled',
            default => ucfirst($subscriptionStatus),
        };
        $ownerEmail = optional($this->users->firstWhere('role', 'admin') ?? $this->users->first())->email;
        $joinedDate = $this->created_at;

        return [
            'id' => $this->id,
            'name' => $this->company_name,
            'owner' => $this->owner_name,
            'owner_user_id' => optional($this->users->firstWhere('role', 'admin') ?? $this->users->first())->id,
            'email' => $ownerEmail ?: '-',
            'plan' => ucfirst($this->subscription_plan ?? 'starter'),
            'billing_cycle' => $this->billing_cycle ?? 'monthly',
            'branches_count' => (int) ($this->branches_count ?? 0),
            'status' => $status,
            'joined_at' => optional($joinedDate)->format('d M Y') ?? '-',
            'joined_bs' => $joinedDate ? $this->toFullBsDate($joinedDate) : '-',
        ];
    }
}
