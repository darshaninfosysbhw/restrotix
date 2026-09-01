<?php

namespace App\Services\Admin\MenuManagement;

use App\Models\Branch;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    public function getCategoryQuery($tenantId)
    {
        return MenuCategory::query()
            ->where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->with([
                'branch:id,branch_name',
                'children' => fn($query) => $query
                    ->select([
                        'id',
                        'tenant_id',
                        'branch_id',
                        'parent_id',
                        'name',
                        'slug',
                        'code',
                        'image',
                        'sort_order',
                        'is_active',
                    ])
                    ->with('branch:id,branch_name'),
            ])
            ->orderBy('sort_order', 'asc');
    }

    /**
     * Fetch all parent categories with their children (Tree Structure)
     */
    public function getAllCategories($tenantId)
    {
        return $this->getCategoryQuery($tenantId)->get();
    }

    public function getPaginatedCategories($tenantId, int $perPage = 25, ?string $search = null)
    {
        $query = $this->getCategoryQuery($tenantId);

        $search = trim((string) $search);
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $like = '%' . $search . '%';

                $builder->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhereHas('branch', function ($branchQuery) use ($like) {
                        $branchQuery->where('branch_name', 'like', $like);
                    })
                    ->orWhereHas('children', function ($childQuery) use ($like) {
                        $childQuery->where('name', 'like', $like)
                            ->orWhere('code', 'like', $like)
                            ->orWhere('slug', 'like', $like)
                            ->orWhereHas('branch', function ($branchQuery) use ($like) {
                                $branchQuery->where('branch_name', 'like', $like);
                            });
                    });
            });
        }

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getParentCategories($tenantId)
    {
        return MenuCategory::query()
            ->where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getTenantBranches($tenantId)
    {
        return Branch::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('branch_name')
            ->get(['id', 'branch_name']);
    }

    public function getCategoryStats($tenantId)
    {
        $total = MenuCategory::query()->where('tenant_id', $tenantId)->count();

        return [
            'total' => $total,
            'active' => MenuCategory::query()->where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => MenuCategory::query()->where('tenant_id', $tenantId)->where('is_active', false)->count(),
            'global' => MenuCategory::query()->where('tenant_id', $tenantId)->whereNull('branch_id')->count(),
        ];
    }

    /**
     * Store new category
     */
    public function storeCategory(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('categories', 'public');
        }

        return MenuCategory::create($data);
    }

    /**
     * Update existing category
     */
    public function updateCategory(MenuCategory $category, array $data)
    {
        if (isset($data['image'])) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $data['image']->store('categories', 'public');
        }

        $category->update($data);
        return $category;
    }

    public function setCategoryStatus(MenuCategory $category, bool $isActive): MenuCategory
    {
        $category->update(['is_active' => $isActive]);
        return $category->refresh();
    }

    public function deleteCategory(MenuCategory $category): void
    {
        $ids = [];
        $images = [];
        $this->collectCategoryTree($category, $ids, $images);

        if (!empty($images)) {
            Storage::disk('public')->delete($images);
        }

        MenuCategory::query()
            ->whereIn('id', $ids)
            ->delete();
    }

    private function collectCategoryTree(MenuCategory $category, array &$ids, array &$images): void
    {
        $category->loadMissing('children:id,parent_id,image');

        $ids[] = $category->id;
        if (!empty($category->image)) {
            $images[] = $category->image;
        }

        foreach ($category->children as $child) {
            $this->collectCategoryTree($child, $ids, $images);
        }
    }
}
