<?php

namespace App\Http\Resources\Admin\MenuManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'code'          => $this->code,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'image_url'     => $this->image ? asset('storage/' . $this->image) : asset('images/default-food.png'),
            'category_id'   => $this->category_id,
            'branch_id'     => $this->branch_id,

            // 💰 Pricing Logic
            'base_price'    => number_format($this->base_price, 2),
            'sale_price'    => $this->sale_price ? number_format($this->sale_price, 2) : null,
            'base_price_value' => $this->base_price !== null ? (float) $this->base_price : null,
            'sale_price_value' => $this->sale_price !== null ? (float) $this->sale_price : null,
            'tax_percent'   => $this->tax_percent . '%',

            // 🥗 Type & Status
            'type'          => ucfirst($this->type), // Veg, Non-veg
            'type_value'    => $this->type,
            'is_available'  => (bool) $this->is_available,
            'is_active'     => (bool) $this->is_active,
            'is_recommended' => (bool) $this->is_recommended,
            'updated_at'    => optional($this->updated_at)?->format('Y-m-d'),

            // 🌳 Relationships
            'category_name' => $this->category->name ?? 'Uncategorized',
            'branch_name'   => $this->branch->branch_name ?? 'All Branches',
        ];
    }
}
