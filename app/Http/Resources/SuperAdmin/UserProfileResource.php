<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $role = (string) ($this->role ?? '');

        return [
            'id' => (int) $this->id,
            'name' => (string) ($this->name ?? ''),
            'email' => (string) ($this->email ?? ''),
            'phone_number' => $this->phone_number,
            'phone' => $this->phone_number,
            'role' => $role,
            'role_label' => $role !== '' ? ucfirst($role) : '-',
            'is_active' => (bool) ($this->is_active ?? false),
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
