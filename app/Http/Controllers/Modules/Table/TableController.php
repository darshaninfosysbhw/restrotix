<?php

namespace App\Http\Controllers\Modules\Table;

use App\Http\Controllers\Controller;
use App\Models\KotPrintLog;
use App\Models\Order;
use App\Models\Table;
use App\Models\Branch;
use App\Services\Admin\TableService; // Service ko import kiya
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Resources\Admin\TableResource;

class TableController extends Controller
{
    protected $tableService;

    // Constructor mein Service ko inject kar diya (Dependency Injection)
    public function __construct(TableService $tableService)
    {
        $this->tableService = $tableService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = Auth::user()->tenant_id;

        $branches = Branch::where('tenant_id', $tenantId)->get();

        // 🔥 CHANGE: optimized stats query + out_of_service added
        $stats = Table::where('tenant_id', $tenantId)
            ->selectRaw("
            COUNT(*) as total,
            SUM(status = 'available') as available,
            SUM(status = 'reserved') as reserved,
            SUM(status = 'occupied') as occupied,
            SUM(status = 'calling_waiter') as calling_waiter,
            SUM(status = 'request_bill') as request_bill,
            SUM(status = 'out_of_service') as out_of_service
        ")
            ->first();


        $tableModels = Table::where('tenant_id', $tenantId)
            ->with(['branch', 'orders' => function ($q) {
                // Sirf wo orders jo abhi tak finish nahi huye
                // $q->whereIn('status', ['pending', 'preparing', 'ready', 'served'])
                $q->where('status', 'running')
                    ->with(['items.orderItemAddons.masterAddon']);
            }])
            ->when($request->branch_id, function ($q) use ($request) {
                return $q->where('branch_id', $request->branch_id);
            })
            ->orderBy('table_number', 'asc')
            ->get();


        // 🔥 CHANGE: resource applied
        $data = [
            'tables' => collect(TableResource::collection($tableModels)->resolve()),
            'branches' => $branches,
            'stats' => $stats
        ];
        if ($user->role === 'waiter') {
            return view('modules.table.waiter.index', $data);
        }

        return view('modules.table.admin.index', $data);
    }

    public function kotPdf(Request $request, string $table_number)
    {
        $user = Auth::user();
        $tenantId = (int) ($user?->tenant_id ?? 0);
        $requestedOrderId = $request->filled('order_id') ? (int) $request->query('order_id') : null;
        $requestedKotNumber = $request->filled('kot_number') ? (int) $request->query('kot_number') : null;
        $requestedBranchId = $request->filled('branch_id') ? (int) $request->query('branch_id') : null;
        $tableNumber = trim((string) $table_number);
        $ordersQuery = Order::query()
            ->where('tenant_id', $tenantId)
            ->with(['items.orderItemAddons.masterAddon', 'items.creator', 'creator']);

        if ($requestedOrderId) {
            $primaryOrder = (clone $ordersQuery)
                ->whereKey($requestedOrderId)
                ->firstOrFail();

            $tableNumber = trim((string) ($primaryOrder->table_number ?? $tableNumber));
            $orders = collect([$primaryOrder]);
        } else {
            $orders = (clone $ordersQuery)
                ->where('table_number', $tableNumber)
                ->where('status', 'running')
                ->latest()
                ->get();
        }

        $targetKotNumber = $requestedKotNumber;
        if ($targetKotNumber <= 0) {
            $targetKotNumber = (int) $orders
                ->flatMap(function (Order $order) {
                    return $order->items->pluck('kot_number');
                })
                ->map(fn ($value) => (int) $value)
                ->filter(fn (int $value) => $value > 0)
                ->max();
        }

        if ($targetKotNumber > 0) {
            $filteredOrders = $orders->map(function (Order $order) use ($targetKotNumber) {
                $filteredItems = $order->items
                    ->filter(function ($item) use ($targetKotNumber) {
                        return (int) ($item->kot_number ?? 0) === $targetKotNumber;
                    })
                    ->values();

                if ($filteredItems->isEmpty()) {
                    return null;
                }

                $order->setRelation('items', $filteredItems);

                return $order;
            })->filter()->values();

            if ($filteredOrders->isNotEmpty()) {
                $orders = $filteredOrders;
            }
        }

        $table = Table::query()
            ->with(['branch', 'tenant'])
            ->where('tenant_id', $tenantId)
            ->where('table_number', $tableNumber)
            ->when($requestedBranchId, function ($query) use ($requestedBranchId) {
                $query->where('branch_id', $requestedBranchId);
            })
            ->firstOrFail();

        $primaryOrder = $orders->first();

        if ($request->boolean('print') && $targetKotNumber > 0) {
            try {
                KotPrintLog::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => (int) ($table->branch_id ?? 0),
                    'table_id' => (int) $table->id,
                    'table_number' => (string) ($table->table_number ?? $tableNumber),
                    'order_id' => $primaryOrder?->id,
                    'kot_number' => $targetKotNumber,
                    'printed_by' => $user?->id,
                    'printed_by_name' => $user?->name,
                    'print_source' => 'drawer',
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $cleanReceiptBrand = function (?string $value, string $fallback): string {
            $brand = trim((string) $value);
            if ($brand === '') {
                return $fallback;
            }

            if (preg_match('/restochainerp|restaurant/i', $brand)) {
                return $fallback;
            }

            $brand = preg_replace('/\s*-\s*MAIN$/i', '', $brand) ?? $brand;

            return trim($brand) !== '' ? trim($brand) : $fallback;
        };

        $restaurantName = $cleanReceiptBrand(data_get($table, 'tenant.company_name'), 'FOOD PANDA');
        $branchName = $cleanReceiptBrand(data_get($table, 'branch.branch_name'), 'HOT KITCHEN');
        $showBranchName = strcasecmp($restaurantName, $branchName) !== 0;
        $kotBatchCreatedAt = $orders
            ->flatMap(function (Order $order) use ($targetKotNumber) {
                return $order->items
                    ->filter(function ($item) use ($targetKotNumber) {
                        return (int) ($item->kot_number ?? 0) === $targetKotNumber;
                    })
                    ->map(fn ($item) => $item->created_at)
                    ->values();
            })
            ->filter()
            ->first();
        $items = $orders->flatMap(function (Order $order) use ($targetKotNumber) {
            return $order->items
                ->filter(function ($item) {
                    $status = strtolower(trim((string) ($item->status ?? '')));
                    return !in_array($status, ['rejected', 'cancelled'], true);
                })
                ->map(function ($item) {
                    $addons = collect($item->orderItemAddons ?? [])
                        ->map(function ($addon) {
                            $addonName = trim((string) (
                                $addon->addon_name
                                ?? data_get($addon, 'masterAddon.name', '')
                            ?? $addon->name
                            ?? 'Addon'
                        ));

                        return [
                            'name' => $addonName,
                            'quantity' => max((int) ($addon->quantity ?? 1), 1),
                        ];
                    })
                    ->filter(fn (array $addon) => trim((string) ($addon['name'] ?? '')) !== '')
                    ->values()
                    ->all();

                return [
                    'quantity' => max((int) ($item->quantity ?? 1), 1),
                    'name' => trim((string) ($item->item_name ?? 'Item')),
                    'notes' => trim((string) ($item->notes ?? '')),
                    'addons' => $addons,
                    'order_by_label' => trim((string) data_get($item, 'order_by_label', '')) ?: 'Guest',
                    'created_at' => optional($item->created_at)->toIso8601String(),
                ];
                });
        })->filter(fn (array $item) => trim((string) ($item['name'] ?? '')) !== '')->values();

        $kotNumber = (int) ($primaryOrder?->items?->max('kot_number') ?? 0);
        $kotCode = $kotNumber > 0
            ? (string) $kotNumber
            : '';

        $orderCode = $orders->count() === 1
            ? (string) ($primaryOrder?->order_number ?? 'N/A')
            : 'MULTIPLE';
        $orderByLabel = $items
            ->pluck('order_by_label')
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->first() ?: (trim((string) data_get($primaryOrder, 'order_by_label', '')) ?: 'Guest');

        $receiptDateTime = $kotBatchCreatedAt ?? $primaryOrder?->ordered_at ?? $primaryOrder?->created_at ?? now();
        $receiptTimeLabel = $receiptDateTime instanceof \Carbon\CarbonInterface
            ? $receiptDateTime->format('h:i A')
            : now()->format('h:i A');
        $receiptDateLabel = $receiptDateTime instanceof \Carbon\CarbonInterface
            ? $receiptDateTime->format('d/m/Y')
            : now()->format('d/m/Y');

        $pdf = Pdf::loadView('core.pdf.kot-summary', [
            'restaurantName' => $restaurantName,
            'branchName' => $branchName,
            'showBranchName' => $showBranchName,
            'kotCode' => $kotCode,
            'tableNumber' => $tableNumber,
            'orderCode' => $orderCode,
            'orderByLabel' => $orderByLabel,
            'receiptTimeLabel' => $receiptTimeLabel,
            'receiptDateLabel' => $receiptDateLabel,
            'items' => $items->all(),
        ])->setPaper($this->thermalReceiptPaperForKot((int) $items->count(), $items->all()), 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');

        $fileName = 'kot-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', $tableNumber) . '.pdf';

        return $request->boolean('print')
            ? $pdf->stream($fileName)
            : $pdf->download($fileName);
    }


    public function bulkStore(Request $request)
    {
        $request->validate([
            'branch_id'    => 'required',
            'capacity'     => 'required|integer|min:1',
            'table_count'  => 'required|integer|min:1',
            'start_number' => 'required|integer|min:1',
        ]);

        $count = $this->tableService->generateBulkTables($request->all(), Auth::user()->tenant_id);

        return redirect()->back()->with('success', "$count tables generate ho gayi hain!");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id'    => 'required',
            'table_number' => 'required|string',
            'capacity'     => 'required|integer|min:1',
            'status'       => 'required|in:available,occupied,reserved,calling_waiter,request_bill,out_of_service',
        ]);

        try {
            // Service ko call kiya
            $this->tableService->updateTable($id, $request->all(), Auth::user()->tenant_id);

            return redirect()->back()->with('success', 'Table updated successfully!');
        } catch (\Exception $e) {
            // Agar Service ne exception throw kiya (jaise duplicate name)
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function thermalReceiptPaperForKot(int $itemCount = 0, array $items = []): array
    {
        $lineCount = 0;

        foreach ($items as $item) {
            $lineCount += $this->estimateKotTextLines((string) ($item['name'] ?? ''), 24);
            $lineCount += $this->estimateKotTextLines((string) ($item['notes'] ?? ''), 24);

            foreach ((array) ($item['addons'] ?? []) as $addon) {
                $lineCount += $this->estimateKotTextLines((string) ($addon['name'] ?? ''), 24);
            }
        }

        $widthMm = 80;
        $heightMm = max(180, 120 + max($lineCount, $itemCount) * 8);
        $mmToPt = 72 / 25.4;

        return [
            0,
            0,
            $widthMm * $mmToPt,
            $heightMm * $mmToPt,
        ];
    }

    private function estimateKotTextLines(string $text, int $charsPerLine = 24): int
    {
        $raw = trim($text);
        if ($raw === '') {
            return 0;
        }

        return max(1, (int) ceil(strlen($raw) / max($charsPerLine, 1)));
    }
}
