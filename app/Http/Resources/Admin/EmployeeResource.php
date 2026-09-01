<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $detail = $this->detail;
        $exitDate = optional($detail)->exit_date;

        $status = 'Inactive';
        if ($this->is_active) {
            $status = $exitDate ? 'Leave' : 'Active';
        }

        return [
            'id' => (int) $this->id,
            'name' => (string) ($this->name ?? '-'),
            'employee_code' => (string) (optional($detail)->employee_id ?? '-'),
            'role' => (string) ($this->role ?? ''),
            'role_label' => ucwords(str_replace('_', ' ', (string) ($this->role ?? ''))),
            'branch_id' => $this->branch_id,
            'branch_name' => (string) (optional($this->branch)->branch_name ?? '-'),
            'email' => (string) ($this->email ?? '-'),
            'phone_number' => (string) ($this->phone_number ?? '-'),
            'pin_code' => (string) (optional($detail)->pin_code ?? ''),
            'designation' => (string) (optional($detail)->designation ?? ''),
            'id_type' => (string) (optional($detail)->id_type ?? ''),
            'id_number' => (string) (optional($detail)->id_number ?? ''),
            'emergency_contact_number' => (string) (optional($detail)->emergency_contact_number ?? ''),
            'current_address' => (string) (optional($detail)->current_address ?? ''),
            'permanent_address' => (string) (optional($detail)->permanent_address ?? ''),
            'base_salary' => (string) (optional($detail)->base_salary ?? '0'),
            'bank_name' => (string) (optional($detail)->bank_name ?? ''),
            'account_number' => (string) (optional($detail)->account_number ?? ''),
            'shift' => (string) (optional($detail)->shift ?? '-'),
            'status' => $status,
            'is_active' => (bool) $this->is_active,
            'joined' => optional(optional($detail)->joining_date)->format('Y-m-d')
                ?? optional($this->created_at)->format('Y-m-d')
                ?? '-',
        ];
    }
}
