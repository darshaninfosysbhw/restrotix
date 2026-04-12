<?php

namespace App\Services\Admin\MenuManagement;

use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    public function getAllItems($tenantId)
    {
        return MenuItem::where('tenant_id', $tenantId)
            ->with(['category:id,name', 'branch:id,branch_name'])
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public function storeItem(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('menu-items', 'public');
        }
        return MenuItem::create($data);
    }

    public function updateItem(MenuItem $item, array $data)
    {
        if (isset($data['image'])) {
            // Old image delete
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $data['image']->store('menu-items', 'public');
        }
        return $item->update($data);
    }

    public function deleteItem(MenuItem $item): void
    {
        if (!empty($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();
    }

    public function setItemStatus(MenuItem $item, bool $isActive): MenuItem
    {
        $item->update(['is_active' => $isActive]);
        return $item->refresh();
    }
}
