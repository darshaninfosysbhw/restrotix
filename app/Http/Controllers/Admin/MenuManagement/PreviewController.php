<?php

namespace App\Http\Controllers\Admin\MenuManagement;

use App\Http\Controllers\Controller;
use App\Events\WaiterCalled;
use App\Services\Admin\MenuManagement\CategoryService;
use App\Services\Admin\MenuManagement\ItemService;
use App\Models\Tenant;
use App\Models\Table;
use App\Http\Resources\Admin\MenuManagement\ItemResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PreviewController extends Controller
{
    protected $categoryService;
    protected $itemService;

    public function __construct(CategoryService $categoryService, ItemService $itemService)
    {
        $this->categoryService = $categoryService;
        $this->itemService = $itemService;
    }

    public function show(Request $request, $tenant_slug, $branch_id = null)
    {
        $tenant = Tenant::where('slug', $tenant_slug)->firstOrFail();
        $tableNumber = $request->query('table', 'N/A');
        $tableId = $request->query('table_id') ? (int) $request->query('table_id') : null;
        return $this->renderPreview($request, $tenant, $branch_id ? (int) $branch_id : null, $tableNumber, $tableId);
    }

    public function showByQr(Request $request, string $qr_token)
    {
        $table = Table::query()
            ->where('qr_token', $qr_token)
            ->where('is_active', true)
            ->firstOrFail();

        $tenant = Tenant::query()->findOrFail($table->tenant_id);
        $tableNumber = (string) $table->table_number;

        return $this->renderPreview($request, $tenant, (int) $table->branch_id, $tableNumber, (int) $table->id);
    }

    public function showAdmin(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        $tenant = Tenant::query()->findOrFail($user->tenant_id);
        $branchId = $request->query('branch_id')
            ? (int) $request->query('branch_id')
            : ($user->branch_id ? (int) $user->branch_id : null);
        $tableNumber = (string) $request->query('table', 'N/A');
        $tableId = $request->query('table_id') ? (int) $request->query('table_id') : null;

        return $this->renderPreview($request, $tenant, $branchId, $tableNumber, $tableId);
    }

    public function showWaiterOrderPanel(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        $tenantId = (int) $user->tenant_id;
        $branchId = $request->query('branch_id')
            ? (int) $request->query('branch_id')
            : ($user->branch_id ? (int) $user->branch_id : null);

        $userRole = strtolower(trim($user->role));
        $layout =  $userRole === 'waiter'
            ? 'core.layouts.waiter'
            : 'core.layouts.admin';

        $backUrl = $userRole === 'waiter'
            ? route('waiter.tables.index')
            : route('admin.tables.index');

        $categories = $this->categoryService
            ->getAllCategories($tenantId)
            ->filter(function ($category) {
                return (bool) $category->is_active;
            })
            ->filter(function ($category) use ($branchId) {
                if (!$branchId) {
                    return true;
                }

                return $category->branch_id === null || (int) $category->branch_id === $branchId;
            })
            ->sortBy('sort_order')
            ->values();

        $categoryIds = $categories->pluck('id')->all();
        $categoryNameMap = $categories->pluck('name', 'id');

        $items = $this->itemService
            ->getAllItems($tenantId)
            ->filter(function ($item) {
                return (bool) $item->is_active && (bool) $item->is_available;
            })
            ->filter(function ($item) use ($branchId) {
                if (!$branchId) {
                    return true;
                }

                return $item->branch_id === null || (int) $item->branch_id === $branchId;
            })
            ->filter(function ($item) use ($categoryIds) {
                return in_array((int) $item->category_id, $categoryIds, true);
            })
            ->sortBy('sort_order')
            ->values();

        $dynamicMenuItems = collect(ItemResource::collection($items)->resolve())
            ->map(function (array $item) use ($categoryNameMap) {
                return [
                    'id' => (int) ($item['id'] ?? 0),
                    'name' => (string) ($item['name'] ?? ''),
                    'price' => (float) ($item['sale_price_value'] ?? $item['base_price_value'] ?? 0),
                    'image' => (string) ($item['image_url'] ?? asset('images/default-food.png')),
                    'category' => (string) ($item['category_name'] ?? ($categoryNameMap[(int) ($item['category_id'] ?? 0)] ?? 'All Items')),
                ];
            })
            ->values();

        return view('modules.orders.index', [
            'layout'               => $layout,
            'backUrl'              => $backUrl,
            'dynamicCategories'    => $categories->pluck('name')->values(),
            'dynamicMenuItems'     => $dynamicMenuItems,
            'selectedTableNumber'  => (string) $request->query('table', '5'),
            'selectedTableId'      => $request->query('table_id'),
        ]);
    }

    public function callWaiter(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'nullable|integer',
            'table_number' => 'nullable|string|max:50',
            'tenant_id' => 'nullable|integer',
        ]);

        if (empty($validated['table_id']) && empty($validated['table_number'])) {
            return response()->json([
                'success' => false,
                'message' => 'Table identifier is required.',
            ], 422);
        }

        $table = Table::query()
            ->when(!empty($validated['tenant_id']), function ($query) use ($validated) {
                $query->where('tenant_id', (int) $validated['tenant_id']);
            })
            ->when(!empty($validated['table_id']), function ($query) use ($validated) {
                $query->where('id', (int) $validated['table_id']);
            }, function ($query) use ($validated) {
                $query->where('table_number', (string) $validated['table_number'])
                    ->where('is_active', true);
            })
            ->first();

        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => 'Table not found.',
            ], 404);
        }

        $table->update(['status' => 'calling_waiter']);

        broadcast(new WaiterCalled([
            'table_id' => (int) $table->id,
            'table_number' => (string) $table->table_number,
            'tenant_id' => (int) $table->tenant_id,
            'branch_id' => (int) $table->branch_id,
            'called_at' => now()->toIso8601String(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Waiter has been called.',
        ]);
    }

    private function renderPreview(Request $request, Tenant $tenant, ?int $branchId, string $tableNumber, ?int $tableId = null)
    {
        $tenantId = (int) $tenant->id;

        if (!$tableId && $tableNumber !== 'N/A') {
            $tableId = Table::query()
                ->where('tenant_id', $tenantId)
                ->where('table_number', $tableNumber)
                ->when($branchId, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->value('id');
        }

        $categories = $this->categoryService
            ->getAllCategories($tenantId)
            ->filter(function ($category) {
                return (bool) $category->is_active;
            })
            ->filter(function ($category) use ($branchId) {
                if (!$branchId) {
                    return true;
                }

                return $category->branch_id === null || (int) $category->branch_id === $branchId;
            })
            ->sortBy('sort_order')
            ->values();

        $categoryIds = $categories->pluck('id')->all();

        $items = $this->itemService
            ->getAllItems($tenantId)
            ->filter(function ($item) {
                return (bool) $item->is_active && (bool) $item->is_available;
            })
            ->filter(function ($item) use ($branchId) {
                if (!$branchId) {
                    return true;
                }

                return $item->branch_id === null || (int) $item->branch_id === $branchId;
            })
            ->filter(function ($item) use ($categoryIds) {
                return in_array((int) $item->category_id, $categoryIds, true);
            })
            ->sortBy('sort_order')
            ->groupBy('category_id');

        $menuCategories = $categories
            ->map(function ($category) use ($items) {
                $categoryItems = $items->get((int) $category->id, collect())->values();
                return [
                    'id' => (int) $category->id,
                    'name' => $category->name,
                    'slug' => Str::slug($category->name),
                    'items' => $categoryItems,
                    'items_count' => $categoryItems->count(),
                ];
            })
            ->values();

        $selectedCategoryId = (int) $request->query('category', 0);
        $selectedCategory = $menuCategories->firstWhere('id', $selectedCategoryId) ?? $menuCategories->first();

        return view('modules.menu-management.preview', [
            'tenant' => $tenant,
            'tableNumber' => $tableNumber,
            'tableId' => $tableId ? (int) $tableId : null,
            'menuCategories' => $menuCategories,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
