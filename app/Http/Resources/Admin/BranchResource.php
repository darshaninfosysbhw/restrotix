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
        $rawCountryCode = (string) ($this->getAttribute('country_code') ?? '');
        $countryIsoCode = optional($this->country)->iso_code;
        $countryCode = match (true) {
            in_array($rawCountryCode, ['Ind', 'Nep', 'UAE'], true) => $rawCountryCode,
            $rawCountryCode === 'IN' => 'Ind',
            $rawCountryCode === 'NP' => 'Nep',
            $rawCountryCode === 'AE' => 'UAE',
            $countryIsoCode === 'IN' => 'Ind',
            $countryIsoCode === 'NP' => 'Nep',
            $countryIsoCode === 'AE' => 'UAE',
            default => 'Ind',
        };

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
            'branch_email' => (string) ($this->branch_email ?? ''),
            'country_code' => $countryCode,
            'city' => (string) ($this->city ?? '-'),
            'state' => (string) ($this->state ?? ''),
            'pincode' => (string) ($this->pincode ?? ''),
            'full_address' => (string) ($this->full_address ?? ''),
            'contact_number' => (string) ($this->contact_number ?? '-'),
            'manager_name' => (string) (optional($manager)->name ?? 'Not Assigned'),
            // 'manager_email' => (string) (optional($manager)->email ?? '-'),
            'manager_email' => (string) $displayEmail,
            'employees' => $usersCount,
            'status' => $status,
            'created' => optional($this->created_at)->format('Y-m-d') ?? '-',
            'offline_billing_enabled' => (bool) ($this->offline_billing_enabled ?? false),
            'tax_setting' => (string) ($this->tax_setting ?? 'exclusive'),
            'tax_rate' => (string) ($this->tax_rate ?? 5.0),
        ];
    }
}
