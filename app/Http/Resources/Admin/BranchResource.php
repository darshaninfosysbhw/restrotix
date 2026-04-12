<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $manager = $this->manager;
        $usersCount = (int) ($this->users_count ?? 0);

        $status = 'Setup';
        if ($usersCount > 0) {
            $status = optional($manager)->is_active === false ? 'Inactive' : 'Active';
        }
        $displayEmail = $this->branch_email
            ? $this->branch_email
            : optional($this->tenant->users->where('role', 'admin')->first())->email ?? '-';

        return [
            'id' => (int) $this->id,
            'code' => 'BR-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT),
            'name' => (string) ($this->branch_name ?? '-'),
            'city' => (string) ($this->city ?? '-'),
            'contact_number' => (string) ($this->contact_number ?? '-'),
            'manager_name' => (string) (optional($manager)->name ?? 'Not Assigned'),
            // 'manager_email' => (string) (optional($manager)->email ?? '-'),
            'manager_email' => (string) $displayEmail,
            'employees' => $usersCount,
            'status' => $status,
            'created' => optional($this->created_at)->format('Y-m-d') ?? '-',
            'offline_billing_enabled' => (bool) ($this->offline_billing_enabled ?? false),
        ];
    }
}
