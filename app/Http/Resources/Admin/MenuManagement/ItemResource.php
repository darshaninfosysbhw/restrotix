<?php

namespace App\Http\Resources\Admin\MenuManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'code'           => $this->code,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'image_url'      => $this->image ? asset('storage/' . $this->image) : asset('images/default-food.png'),
            'category_id'    => $this->category_id,
            'branch_id'      => $this->branch_id,
            'has_variants'   => (bool) $this->has_variants,

            // 💰 Pricing Logic (Fallback handling if variants active)
            'base_price'       => number_format($this->base_price, 2),
            'sale_price'       => $this->sale_price ? number_format($this->sale_price, 2) : null,
            'base_price_value' => $this->base_price !== null ? (float) $this->base_price : null,
            'sale_price_value' => $this->sale_price !== null ? (float) $this->sale_price : null,
            'tax_percent'      => $this->tax_percent . '%',

            // 🥗 Type & Status
            'type'           => ucfirst($this->type),
            'type_value'     => $this->type,
            'is_available'   => (bool) $this->is_available,
            'is_active'      => (bool) $this->is_active,
            'is_recommended' => (bool) $this->is_recommended,
            'updated_at'     => optional($this->updated_at)?->format('Y-m-d'),

            // 🌳 Relations & Sub-Arrays (Binding with Alpine View)
            'category_name'  => $this->category->name ?? 'Uncategorized',
            'branch_name'    => $this->branch->branch_name ?? 'All Branches',

            'variants' => $this->variants->map(function ($v) {
                return [
                    'id'         => $v->id,
                    'name'       => $v->name,
                    'base_price' => (float) $v->base_price,
                    'sale_price' => $v->sale_price ? (float) $v->sale_price : null,
                ];
            })->toArray(),

            'addons' => $this->addons->map(function ($a) {
                return [
                    'id'    => $a->id,
                    'name'  => $a->name,
                    'price' => (float) $a->price,
                ];
            })->toArray(),
        ];
    }
}
