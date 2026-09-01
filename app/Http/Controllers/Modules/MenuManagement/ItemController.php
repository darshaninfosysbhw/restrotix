<?php

namespace App\Http\Controllers\Modules\MenuManagement;

use App\Http\Controllers\Controller;
use App\Services\Admin\MenuManagement\ItemService;
use App\Http\Resources\Admin\MenuManagement\ItemResource;
use App\Services\Admin\MenuManagement\CategoryService;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    protected $itemService;
    protected $categoryService;

    public function __construct(ItemService $itemService, CategoryService $categoryService)
    {
        $this->itemService = $itemService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $search = trim((string) $request->input('search', ''));

        $allItemModels = $this->itemService->getAllItems($tenantId);
        $allItems = collect(ItemResource::collection($allItemModels)->resolve());

        $itemsPaginator = $this->itemService->getPaginatedItems($tenantId, 25, $search);
        $items = ItemResource::collection($itemsPaginator->getCollection())->resolve();

        $itemStats = [
            'total' => $allItems->count(),
            'active' => $allItems->where('is_active', true)->count(),
            'out_of_stock' => $allItems->where('is_available', false)->count(),
            'inactive' => $allItems->where('is_active', false)->count(),
        ];

        $categories = $this->categoryService->getParentCategories($tenantId);
        $branches = $this->categoryService->getTenantBranches($tenantId);

        return view('modules.menu-management.items', compact('items', 'itemsPaginator', 'categories', 'branches', 'itemStats'));
    }

    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:150',
            'category_id'    => ['required', Rule::exists('menu_categories', 'id')->where('tenant_id', $tenantId)],
            'branch_id'      => ['nullable', Rule::exists('branches', 'id')->where('tenant_id', $tenantId)],
            'code'           => ['nullable', 'string', Rule::unique('menu_items', 'code')->where('tenant_id', $tenantId)],

            // 💰 Dynamic Pricing Validation
            'has_variants'   => 'nullable|boolean',
            'base_price'     => 'required_unless:has_variants,1|nullable|numeric|min:0',
            'sale_price'     => 'nullable|numeric|required_unless:has_variants,1|lt:base_price',
            'tax_percent'    => 'nullable|numeric|min:0|max:100',
            'type'           => 'required|in:veg,non-veg,egg,other',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description'    => 'nullable|string',
            'is_recommended' => 'boolean',
            'is_available'   => 'boolean',
            'is_active'      => 'boolean',

            // 🌳 Variants Array Validation
            'variants'              => 'required_if:has_variants,1|array',
            'variants.*.name'       => 'required_if:has_variants,1|string|max:150',
            'variants.*.base_price' => 'required_if:has_variants,1|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',

            // 🥗 Addons Array Validation
            'addons'         => 'nullable|array',
            'addons.*.name'  => 'required_with:addons|string|max:150',
            'addons.*.price' => 'required_with:addons|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [[
                'type' => 'error',
                'message' => $validator->errors()->first(),
                'duration' => 5000,
            ]]);
        }

        $validated = $validator->validated();
        $validated['tenant_id'] = $tenantId;

        try {
            $this->itemService->storeItem($validated);
            return redirect()->back()->with('toast', [[
                'type' => 'success',
                'message' => 'Item added successfully with variants!',
                'duration' => 3500,
            ]]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [[
                'type' => 'error',
                'message' => 'Item create failed: ' . $e->getMessage(),
                'duration' => 6000,
            ]]);
        }
    }

    public function update(Request $request, $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $item = MenuItem::query()->where('tenant_id', $tenantId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:150',
            'category_id'    => ['required', Rule::exists('menu_categories', 'id')->where('tenant_id', $tenantId)],
            'branch_id'      => ['nullable', Rule::exists('branches', 'id')->where('tenant_id', $tenantId)],
            'code'           => ['nullable', 'string', Rule::unique('menu_items', 'code')->where('tenant_id', $tenantId)->ignore($id)],

            // 💰 Dynamic Pricing Validation
            'has_variants'   => 'nullable|boolean',
            'base_price'     => 'required_unless:has_variants,1|nullable|numeric|min:0',
            'sale_price'     => 'nullable|numeric|required_unless:has_variants,1|lt:base_price',
            'tax_percent'    => 'nullable|numeric|min:0|max:100',
            'type'           => 'required|in:veg,non-veg,egg,other',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description'    => 'nullable|string',
            'is_recommended' => 'boolean',
            'is_available'   => 'boolean',
            'is_active'      => 'boolean',

            // 🌳 Variants Sync Validation
            'variants'              => 'required_if:has_variants,1|array',
            'variants.*.id'         => 'nullable',
            'variants.*.name'       => 'required_if:has_variants,1|string|max:150',
            'variants.*.base_price' => 'required_if:has_variants,1|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',

            // 🥗 Addons Sync Validation
            'addons'         => 'nullable|array',
            'addons.*.id'    => 'nullable',
            'addons.*.name'  => 'required_with:addons|string|max:150',
            'addons.*.price' => 'required_with:addons|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with([
                'edit_item_id' => $item->id,
                'toast' => [[
                    'type' => 'error',
                    'message' => $validator->errors()->first(),
                    'duration' => 5000,
                ]],
            ]);
        }

        $validated = $validator->validated();
        $validated['tenant_id'] = $tenantId;

        try {
            $this->itemService->updateItem($item, $validated);
            return redirect()->back()->with('toast', [[
                'type' => 'success',
                'message' => 'Item and modifications updated!',
                'duration' => 3500,
            ]]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with([
                'edit_item_id' => $item->id,
                'toast' => [[
                    'type' => 'error',
                    'message' => 'Item update failed: ' . $e->getMessage(),
                    'duration' => 6000,
                ]],
            ]);
        }
    }

    public function destroy(int $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $item = MenuItem::query()->where('tenant_id', $tenantId)->findOrFail($id);

        try {
            $this->itemService->deleteItem($item);
            return redirect()->back()->with('toast', [[
                'type' => 'success',
                'message' => 'Item deleted successfully!',
                'duration' => 3000,
            ]]);
        } catch (\Throwable $exception) {
            return redirect()->back()->with('toast', [[
                'type' => 'error',
                'message' => 'Delete failed: ' . $exception->getMessage(),
                'duration' => 5000,
            ]]);
        }
    }

    public function toggleStatus(Request $request, int $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $item = MenuItem::query()->where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        try {
            $this->itemService->setItemStatus($item, (bool) $validated['is_active']);
            return redirect()->back()->with('toast', [[
                'type' => 'success',
                'message' => 'Item status updated successfully!',
                'duration' => 2500,
            ]]);
        } catch (\Throwable $exception) {
            return redirect()->back()->with('toast', [[
                'type' => 'error',
                'message' => 'Status update failed: ' . $exception->getMessage(),
                'duration' => 5000,
            ]]);
        }
    }
}
