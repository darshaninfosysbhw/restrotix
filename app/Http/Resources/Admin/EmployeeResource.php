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
            'branch_name' => (string) (optional($this->branch)->branch_name ?? '-'),
            'email' => (string) ($this->email ?? '-'),
            'phone_number' => (string) ($this->phone_number ?? '-'),
            'shift' => (string) (optional($detail)->shift ?? '-'),
            'status' => $status,
            'joined' => optional(optional($detail)->joining_date)->format('Y-m-d')
                ?? optional($this->created_at)->format('Y-m-d')
                ?? '-',
        ];
    }
}
