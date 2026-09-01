<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'symbol' => $this->symbol,
            'country_id' => $this->country_id,
            'country_name' => optional($this->country)->name,
            'exchange_rate' => $this->exchange_rate,
            'position' => $this->symbol_position ?? 'Prefix',
            'status' => $this->is_active ? 'Active' : 'Inactive',
            'is_default' => (bool) $this->is_default,
            'decimals' => $this->decimal_places ?? 2,
        ];
    }
}
