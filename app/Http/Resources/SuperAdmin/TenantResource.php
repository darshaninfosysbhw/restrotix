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
        $isTrial = !$this->is_banned && $this->trial_ends_at && now()->lt($this->trial_ends_at);
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
            'plan' => ucfirst($this->subscription_plan ?? 'starter'),
            'plan_key' => strtolower($this->subscription_plan ?? 'starter'),
            'branches' => $this->branches_count ?? 0,
            'status' => $this->is_banned ? 'Pending' : ($isTrial ? 'Trial' : 'Active'),
            'joined' => optional($joinedDate)->format('d M Y') ?? '-',
            'joined_bs' => $joinedBs,
        ];
    }
}
