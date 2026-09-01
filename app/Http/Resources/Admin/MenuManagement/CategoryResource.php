<?php

namespace App\Http\Resources\Admin\MenuManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imageUrl = null;
        if (!empty($this->image)) {
            $imagePath = str_replace('\\', '/', trim((string) $this->image));

            if (Str::startsWith($imagePath, ['http://', 'https://'])) {
                $imageUrl = $imagePath;
            } elseif (Str::startsWith($imagePath, '/storage/')) {
                $imageUrl = asset(ltrim($imagePath, '/'));
            } elseif (Str::startsWith($imagePath, 'public/')) {
                $imageUrl = asset('storage/' . ltrim(Str::after($imagePath, 'public/'), '/'));
            } elseif (Str::contains($imagePath, '/public/')) {
                $imageUrl = asset('storage/' . ltrim(Str::after($imagePath, '/public/'), '/'));
            } elseif (Str::startsWith($imagePath, 'storage/')) {
                $imageUrl = asset($imagePath);
            } else {
                $imageUrl = asset('storage/' . ltrim($imagePath, '/'));
            }
        }

        return [
            'id'         => $this->id,
            'tenant_id'  => $this->tenant_id,
            'branch_id'  => $this->branch_id,
            'parent_id'  => $this->parent_id,
            'name'       => $this->name,
            'code'       => $this->code,
            'code_label' => $this->code ?? 'N/A',
            'slug'       => $this->slug,
            'image_url'  => $imageUrl,
            'branch_name' => $this->branch?->branch_name,
            'is_active'  => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            // Recursive loading for Sub-categories as resolved array payload
            'sub_categories' => $this->relationLoaded('children')
                ? CategoryResource::collection($this->children)->resolve($request)
                : [],
        ];
    }
}
