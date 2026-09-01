<?php

namespace App\Http\Controllers\Modules\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MenuManagement\ItemResource;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Table;
use App\Models\Tenant;
use App\Services\Admin\MenuManagement\CategoryService;
use App\Services\Admin\MenuManagement\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class PosController extends Controller
{
    protected $categoryService;
    protected $itemService;

    public function __construct(
        CategoryService $categoryService,
        ItemService $itemService
    ) {
        $this->categoryService = $categoryService;
        $this->itemService = $itemService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        $tenantId = (int) $user->tenant_id;
        $branchId = $request->query('branch_id')
            ? (int) $request->query('branch_id')
            : ($user->branch_id ? (int) $user->branch_id : null);

        if (!$branchId) {
            $branchId = Branch::where('tenant_id', $tenantId)->value('id');
        }
        $branch = Branch::where('id', $branchId)->first();

        $userRole = strtolower(trim($user->role));
        $layout = $userRole === 'waiter'
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
                    'variants' => $item['variants'] ?? [],
                    'addons' => $item['addons'] ?? [],
                ];
            })
            ->values();

        $switchableTables = $this->resolveSwitchableTables($tenantId, $branchId, $request);
        $selectedSwitchTable = collect($switchableTables)->first(fn (array $table) => !empty($table['is_current']));

        if (!$selectedSwitchTable) {
            $selectedTableId = $request->filled('table_id') ? (int) $request->query('table_id') : null;
            $selectedTableNumber = trim((string) $request->query('table_number', $request->query('table', '5')));

            $fallbackTableQuery = Table::query()
                ->where('tenant_id', $tenantId)
                ->when($branchId, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->select(['id', 'table_number', 'capacity']);

            if ($selectedTableId) {
                $selectedSwitchTable = $fallbackTableQuery->whereKey($selectedTableId)->first();
            } elseif ($selectedTableNumber !== '') {
                $selectedSwitchTable = $fallbackTableQuery->where('table_number', $selectedTableNumber)->first();
            }
        }

        $selectedTableCapacity = (int) data_get($selectedSwitchTable, 'capacity', 0);

        $sessionContext = $this->resolveSessionContext(
            tenantId: $tenantId,
            branchId: $branchId,
            request: $request
        );

        return view('modules.orders.index', [
            'layout' => $layout,
            'backUrl' => $backUrl,
            'dynamicCategories' => $categories->pluck('name')->values(),
            'dynamicMenuItems' => $dynamicMenuItems,
            'selectedTableNumber' => (string) $request->query('table', '5'),
            'selectedTableId' => $request->query('table_id'),
            'selectedTableCapacity' => $selectedTableCapacity,
            'selectedTableCapacityLabel' => $selectedTableCapacity > 0
                ? $selectedTableCapacity . ' Seater'
                : 'Seater',
            'switchableTables' => $switchableTables,
            'tableOrdersCount' => $sessionContext['tableOrdersCount'],
            'tableIsActive' => $sessionContext['tableIsActive'],
            'tableStatusLabel' => $sessionContext['tableStatusLabel'],
            'tableStatusBadgeClass' => $sessionContext['tableStatusBadgeClass'],
            'sessionOrderId' => $sessionContext['sessionOrderId'],
            'sessionStartedAt' => $sessionContext['sessionStartedAt'],
            'sessionEndedAt' => $sessionContext['sessionEndedAt'],
            'branch' => $branch,
        ]);
    }

    private function resolveSessionContext(int $tenantId, ?int $branchId, Request $request): array
    {
        $baseQuery = Order::query()->where('tenant_id', $tenantId);

        if ($branchId) {
            $baseQuery->where('branch_id', $branchId);
        }

        $sessionOrder = null;

        $requestedOrderId = $request->filled('order_id') ? (int) $request->query('order_id') : null;
        if ($requestedOrderId) {
            $sessionOrder = (clone $baseQuery)
                ->with(['invoice.payments', 'paymentSessions'])
                ->whereKey($requestedOrderId)
                ->first();
        } else {
            $tableId = $request->filled('table_id') ? (int) $request->query('table_id') : null;
            $tableNumber = null;

            if ($request->filled('table_number')) {
                $tableNumber = trim((string) $request->query('table_number'));
            } elseif ($request->filled('table')) {
                $tableNumber = trim((string) $request->query('table'));
            }

            if ($tableId || $tableNumber !== null && $tableNumber !== '') {
                $sessionQuery = (clone $baseQuery)->with(['invoice.payments', 'paymentSessions']);

                if ($tableId) {
                    $sessionQuery->where('table_id', $tableId);
                } else {
                    $sessionQuery->where('table_number', $tableNumber);
                }

                $sessionOrder = (clone $sessionQuery)
                    ->where('status', 'running')
                    ->orderByDesc('ordered_at')
                    ->orderByDesc('created_at')
                    ->first()
                    ?? (clone $sessionQuery)
                        ->where('status', 'completed')
                        ->orderByDesc('ordered_at')
                        ->orderByDesc('created_at')
                        ->first();
            }
        }

        if (!$sessionOrder) {
            return [
                'tableOrdersCount' => $this->resolveTableOrdersCount($tenantId, $branchId, null, $request),
                'tableIsActive' => false,
                'tableStatusLabel' => 'Inactive',
                'tableStatusBadgeClass' => 'border-red-400/60 bg-red-500/15 text-red-400',
                'sessionOrderId' => null,
                'sessionStartedAt' => null,
                'sessionEndedAt' => null,
            ];
        }

        $tableIsActive = (string) ($sessionOrder->status ?? '') === 'running';

        $sessionStartedAt = $sessionOrder->ordered_at
            ?? $sessionOrder->created_at
            ?? null;

        return [
            'tableOrdersCount' => $this->resolveTableOrdersCount($tenantId, $branchId, $sessionOrder, $request),
            'tableIsActive' => $tableIsActive,
            'tableStatusLabel' => $tableIsActive ? 'Active' : 'Inactive',
            'tableStatusBadgeClass' => $tableIsActive
                ? 'border-green-400/60 bg-green-500/15 text-green-400'
                : 'border-red-400/60 bg-red-500/15 text-red-400',
            'sessionOrderId' => (int) $sessionOrder->id,
            'sessionStartedAt' => $sessionStartedAt instanceof Carbon
                ? $sessionStartedAt->toIso8601String()
                : null,
            'sessionEndedAt' => $this->resolveSessionEndedAt($sessionOrder),
        ];
    }

    private function resolveTableOrdersCount(int $tenantId, ?int $branchId, ?Order $sessionOrder, Request $request): int
    {
        $baseQuery = Order::query()->where('tenant_id', $tenantId);

        if ($branchId) {
            $baseQuery->where('branch_id', $branchId);
        }

        if ($sessionOrder) {
            if (!empty($sessionOrder->table_id)) {
                return (int) (clone $baseQuery)
                    ->where('table_id', $sessionOrder->table_id)
                    ->count();
            }

            $sessionTableNumber = trim((string) ($sessionOrder->table_number ?? ''));
            if ($sessionTableNumber !== '') {
                return (int) (clone $baseQuery)
                    ->where('table_number', $sessionTableNumber)
                    ->count();
            }
        }

        $requestTableId = $request->filled('table_id') ? (int) $request->query('table_id') : null;
        if ($requestTableId) {
            return (int) (clone $baseQuery)
                ->where('table_id', $requestTableId)
                ->count();
        }

        $requestTableNumber = trim((string) $request->query('table', $request->query('table_number', '')));
        if ($requestTableNumber !== '') {
            return (int) (clone $baseQuery)
                ->where('table_number', $requestTableNumber)
                ->count();
        }

        return 0;
    }

    private function resolveSessionEndedAt(Order $order): ?string
    {
        if ($order->status === 'running') {
            return null;
        }

        $paidAt = $order->paymentSessions
            ->filter(fn ($session) => !empty($session->paid_at))
            ->sortByDesc(fn ($session) => $session->paid_at)
            ->first()?->paid_at;

        if ($paidAt instanceof Carbon) {
            return $paidAt->toIso8601String();
        }

        $invoicePaidAt = $order->invoice?->payments
            ->sortByDesc('created_at')
            ->first()?->created_at;

        if ($invoicePaidAt instanceof Carbon) {
            return $invoicePaidAt->toIso8601String();
        }

        $fallbackAt = $order->updated_at ?? $order->created_at ?? null;

        return $fallbackAt instanceof Carbon ? $fallbackAt->toIso8601String() : null;
    }

    private function resolveSwitchableTables(int $tenantId, ?int $branchId, Request $request): array
    {
        $selectedTableId = $request->filled('table_id') ? (int) $request->query('table_id') : null;
        $selectedTableNumber = trim((string) $request->query('table_number', $request->query('table', '5')));

        return Table::query()
            ->where('tenant_id', $tenantId)
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->select(['id', 'table_number', 'capacity', 'status'])
            ->orderBy('table_number')
            ->get()
            ->map(function (Table $table) use ($selectedTableId, $selectedTableNumber) {
                $status = $this->normalizeTableStatus((string) ($table->status ?? 'available'));

                return [
                    'id' => (int) $table->id,
                    'table_number' => (string) $table->table_number,
                    'capacity' => (int) ($table->capacity ?? 0),
                    'status' => $status,
                    'status_label' => $this->formatTableStatusLabel($status),
                    'status_badge_class' => $this->formatTableStatusBadgeClass($status),
                    'is_current' => $selectedTableId
                        ? (int) $table->id === $selectedTableId
                        : ($selectedTableNumber !== '' && (string) $table->table_number === $selectedTableNumber),
                    'is_disabled' => $status === 'out_of_service',
                ];
            })
            ->values()
            ->all();
    }

    private function normalizeTableStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return in_array($status, [
            'available',
            'occupied',
            'reserved',
            'calling_waiter',
            'request_bill',
            'out_of_service',
        ], true) ? $status : 'available';
    }

    private function formatTableStatusLabel(string $status): string
    {
        return match ($status) {
            'calling_waiter' => 'Calling waiter',
            'request_bill' => 'Bill requested',
            'out_of_service' => 'Out of service',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    private function formatTableStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'available' => 'border-emerald-400/40 bg-emerald-500/10 text-emerald-300',
            'occupied' => 'border-red-400/40 bg-red-500/10 text-red-300',
            'reserved' => 'border-yellow-400/40 bg-yellow-500/10 text-yellow-300',
            'calling_waiter' => 'border-sky-400/40 bg-sky-500/10 text-sky-300',
            'request_bill' => 'border-orange-400/40 bg-orange-500/10 text-orange-300',
            'out_of_service' => 'border-gray-500/50 bg-gray-500/10 text-gray-300',
            default => 'border-gray-500/50 bg-gray-500/10 text-gray-300',
        };
    }
}
