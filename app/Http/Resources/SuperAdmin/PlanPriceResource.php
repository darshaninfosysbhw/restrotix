<?php

namespace App\Http\Resources\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'currency_id' => (int) $this->currency_id,
            'monthly' => (string) $this->monthly_price,
            'yearly' => (string) $this->yearly_price,
        ];
    }
}
