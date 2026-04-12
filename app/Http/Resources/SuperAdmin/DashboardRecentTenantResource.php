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
        $isTrial = !$this->is_banned && $this->trial_ends_at && now()->lt($this->trial_ends_at);
        $status = $this->is_banned ? 'Pending' : ($isTrial ? 'Trial' : 'Active');
        $ownerEmail = optional($this->users->firstWhere('role', 'admin') ?? $this->users->first())->email;
        $joinedDate = $this->created_at;

        return [
            'id' => $this->id,
            'name' => $this->company_name,
            'owner' => $this->owner_name,
            'owner_user_id' => optional($this->users->firstWhere('role', 'admin') ?? $this->users->first())->id,
            'email' => $ownerEmail ?: '-',
            'plan' => ucfirst($this->subscription_plan ?? 'starter'),
            'branches_count' => (int) ($this->branches_count ?? 0),
            'status' => $status,
            'joined_at' => optional($joinedDate)->format('d M Y') ?? '-',
            'joined_bs' => $joinedDate ? $this->toFullBsDate($joinedDate) : '-',
        ];
    }
}
