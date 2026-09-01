<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'event' => (string) ($this->event ?? ''),
            'description' => (string) ($this->description ?? 'Profile updated'),
            'meta' => $this->meta ?? [],
            'at_display' => optional($this->created_at)->format('d M Y, h:i A') ?? '-',
        ];
    }
}
