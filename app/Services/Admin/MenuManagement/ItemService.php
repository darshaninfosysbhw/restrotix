<?php

namespace App\Services\Admin\MenuManagement;

use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ItemService
{
    public function getItemsQuery($tenantId)
    {
        return MenuItem::where('tenant_id', $tenantId)
            ->with(['category:id,name', 'branch:id,branch_name', 'variants', 'addons'])
            ->orderBy('sort_order', 'asc');
    }

    public function getAllItems($tenantId)
    {
        // 🌟 Eager load all variant and addon modifications
        return $this->getItemsQuery($tenantId)->get();
    }

    public function getPaginatedItems($tenantId, int $perPage = 25, ?string $search = null)
    {
        $query = $this->getItemsQuery($tenantId);

        $search = trim((string) $search);
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $like = '%' . $search . '%';

                $builder->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('type', 'like', $like)
                    ->orWhereHas('category', function ($categoryQuery) use ($like) {
                        $categoryQuery->where('name', 'like', $like);
                    })
                    ->orWhereHas('branch', function ($branchQuery) use ($like) {
                        $branchQuery->where('branch_name', 'like', $like);
                    });
            });
        }

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    public function storeItem(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['image'])) {
                $data['image'] = $data['image']->store('menu-items', 'public');
            }

            $hasVariants = isset($data['has_variants']) && $data['has_variants'] == 1;

            // Clear single rates if variants are active
            $itemData = array_merge($data, [
                'has_variants' => $hasVariants,
                'base_price'   => $hasVariants ? 0.00 : ($data['base_price'] ?? 0.00),
                'sale_price'   => $hasVariants ? null : ($data['sale_price'] ?? null),
            ]);

            $item = MenuItem::create($itemData);

            // Save Sub-variants
            if ($hasVariants && isset($data['variants'])) {
                foreach ($data['variants'] as $v) {
                    $item->variants()->create([
                        'name'       => $v['name'],
                        'base_price' => $v['base_price'],
                        'sale_price' => $v['sale_price'] ?? null,
                    ]);
                }
            }

            // Save Sub-addons
            if (isset($data['addons']) && count($data['addons']) > 0) {
                foreach ($data['addons'] as $a) {
                    if (!empty($a['name'])) {
                        $item->addons()->create([
                            'name'  => $a['name'],
                            'price' => $a['price'],
                        ]);
                    }
                }
            }

            return $item;
        });
    }

    public function updateItem(MenuItem $item, array $data)
    {
        return DB::transaction(function () use ($item, $data) {
            if (isset($data['image'])) {
                if ($item->image) {
                    Storage::disk('public')->delete($item->image);
                }
                $data['image'] = $data['image']->store('menu-items', 'public');
            }

            $hasVariants = isset($data['has_variants']) && $data['has_variants'] == 1;

            $item->update(array_merge($data, [
                'has_variants' => $hasVariants,
                'base_price'   => $hasVariants ? 0.00 : ($data['base_price'] ?? 0.00),
                'sale_price'   => $hasVariants ? null : ($data['sale_price'] ?? null),
            ]));

            // --- Safe Variants Sync ---
            if ($hasVariants && isset($data['variants'])) {
                $keepIds = [];
                foreach ($data['variants'] as $v) {
                    $variant = $item->variants()->updateOrCreate(
                        ['id' => $v['id'] ?? null],
                        [
                            'name'       => $v['name'],
                            'base_price' => $v['base_price'],
                            'sale_price' => $v['sale_price'] ?? null,
                        ]
                    );
                    $keepIds[] = $variant->id;
                }
                $item->variants()->whereNotIn('id', $keepIds)->delete();
            } else {
                $item->variants()->delete();
            }

            // --- Safe Addons Sync ---
            if (isset($data['addons'])) {
                $keepAddonIds = [];
                foreach ($data['addons'] as $a) {
                    if (!empty($a['name'])) {
                        $addon = $item->addons()->updateOrCreate(
                            ['id' => $a['id'] ?? null],
                            [
                                'name'  => $a['name'],
                                'price' => $a['price'],
                            ]
                        );
                        $keepAddonIds[] = $addon->id;
                    }
                }
                $item->addons()->whereNotIn('id', $keepAddonIds)->delete();
            } else {
                $item->addons()->delete();
            }

            return $item;
        });
    }

    public function deleteItem(MenuItem $item): void
    {
        DB::transaction(function () use ($item) {
            if (!empty($item->image)) {
                Storage::disk('public')->delete($item->image);
            }

            // Sub-tables cascade auto handling checks
            $item->variants()->delete();
            $item->addons()->delete();
            $item->delete();
        });
    }

    public function setItemStatus(MenuItem $item, bool $isActive): MenuItem
    {
        $item->update(['is_active' => $isActive]);
        return $item->refresh();
    }
}
