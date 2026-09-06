<?php

namespace App\Services\Admin\MenuManagement;

use App\Models\Branch;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    public function getCategoryQuery($tenantId, ?int $branchId = null)
    {
        return MenuCategory::query()
            ->where('tenant_id', $tenantId)
            ->when($branchId !== null, fn ($query) => $query->where(function ($query) use ($branchId) {
                $query->whereNull('branch_id')->orWhere('branch_id', $branchId);
            }))
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
                    ->when($branchId !== null, fn ($query) => $query->where(function ($query) use ($branchId) {
                        $query->whereNull('branch_id')->orWhere('branch_id', $branchId);
                    }))
                        ->with('branch:id,branch_name'),
            ])
            ->orderBy('sort_order', 'asc');
    }

    /**
     * Fetch all parent categories with their children (Tree Structure)
     */
    public function getAllCategories($tenantId, ?int $branchId = null)
    {
        return $this->getCategoryQuery($tenantId, $branchId)->get();
    }

    public function getPaginatedCategories($tenantId, int $perPage = 25, ?string $search = null, ?int $branchId = null)
    {
        $query = $this->getCategoryQuery($tenantId, $branchId);

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

    public function getParentCategories($tenantId, ?int $branchId = null)
    {
        return MenuCategory::query()
            ->where('tenant_id', $tenantId)
            ->when($branchId !== null, fn ($query) => $query->where(function ($query) use ($branchId) {
                $query->whereNull('branch_id')->orWhere('branch_id', $branchId);
            }))
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getTenantBranches($tenantId, ?int $branchId = null)
    {
        return Branch::query()
            ->where('tenant_id', $tenantId)
            ->when($branchId !== null, fn ($query) => $query->where('id', $branchId))
            ->orderBy('branch_name')
            ->get(['id', 'branch_name']);
    }

    public function getCategoryStats($tenantId, ?int $branchId = null)
    {
        $baseQuery = MenuCategory::query()
            ->where('tenant_id', $tenantId)
            ->when($branchId !== null, fn ($query) => $query->where(function ($query) use ($branchId) {
                $query->whereNull('branch_id')->orWhere('branch_id', $branchId);
            }));
        $total = (clone $baseQuery)->count();

        return [
            'total' => $total,
            'active' => (clone $baseQuery)->where('is_active', true)->count(),
            'inactive' => (clone $baseQuery)->where('is_active', false)->count(),
            'global' => $branchId === null
                ? (clone $baseQuery)->whereNull('branch_id')->count()
                : 0,
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
