<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardServiceUsageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => (string) ($this->name ?? ''),
            'total' => (int) ($this->total ?? 0),
        ];
    }
}
