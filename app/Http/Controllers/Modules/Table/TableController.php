<?php

namespace App\Http\Controllers\Modules\Table;

use App\Events\TableTransferRequestUpdated;
use App\Http\Controllers\Controller;
use App\Models\KotPrintLog;
use App\Models\Order;
use App\Models\Table;
use App\Models\TableServiceRequest;
use App\Models\User;
use App\Models\Branch;
use App\Services\Admin\TableService; // Service ko import kiya
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
        $activeBranchId = (int) session('active_branch_id', $user->branch_id ?? 0);

        $branches = Branch::where('tenant_id', $tenantId)->get();
        $activeWaiters = User::query()
            ->where('tenant_id', $tenantId)
            ->where('branch_id', $activeBranchId)
            ->where('role', 'waiter')
            ->where('is_active', true)
            ->when($user->role === 'waiter', fn ($query) => $query->where('id', '!=', $user->id))
            ->orderBy('name')
            ->get(['id', 'name']);

        // 🔥 CHANGE: optimized stats query + out_of_service added
        $stats = Table::where('tenant_id', $tenantId)
            ->where('branch_id', $activeBranchId)
            ->selectRaw("
            COUNT(*) as total,
            SUM(status = 'available') as available,
            SUM(status = 'reserved') as reserved,
            SUM(status = 'occupied') as occupied,
            SUM(is_calling_waiter = 1) as calling_waiter,
            SUM(is_bill_requested = 1) as request_bill,
            SUM(status = 'out_of_service') as out_of_service
        ")
            ->first();


        $tableModels = Table::where('tenant_id', $tenantId)
            ->where('branch_id', $activeBranchId)
            ->with(['branch', 'orders' => function ($q) {
                // Sirf wo orders jo abhi tak finish nahi huye
                // $q->whereIn('status', ['pending', 'preparing', 'ready', 'served'])
                $q->where('status', 'running')
                    ->with(['items.orderItemAddons.masterAddon']);
            }])
            ->orderBy('table_number', 'asc')
            ->get();

        $activeTransfers = TableServiceRequest::query()
            ->whereIn('table_id', $tableModels->pluck('id'))
            ->where('type', 'table_transfer')
            ->where(function ($query) {
                $query->where('status', 'accepted')
                    ->orWhere(function ($pending) {
                        $pending->where('status', 'pending')
                            ->where('requested_at', '>=', now()->subMinutes(2));
                    });
            })
            ->with(['targetWaiter:id,name', 'table:id,table_number', 'handledByWaiter:id,name'])
            ->latest('requested_at')
            ->get()
            ->unique('table_id')
            ->keyBy('table_id');

        $tableModels->each(function ($table) use ($activeTransfers) {
            $transfer = $activeTransfers->get($table->id);
            $table->setAttribute('transfer_state', $transfer ? $this->transferPayload($transfer) : null);
        });


        // 🔥 CHANGE: resource applied
        $data = [
            'tables' => collect(TableResource::collection($tableModels)->resolve()),
            'branches' => $branches,
            'stats' => $stats,
            'activeWaiters' => $activeWaiters,
        ];
        if ($user->role === 'waiter') {
            return view('modules.table.waiter.index', $data);
        }

        return view('modules.table.admin.index', $data);
    }

    public function waiterSummaries()
    {
        $user = Auth::user();
        $tenantId = (int) $user->tenant_id;

        $tables = Table::query()
            ->where('tenant_id', $tenantId)
            ->where('branch_id', (int) session('active_branch_id', $user->branch_id ?? 0))
            ->with(['orders' => function ($query) {
                $query->where('status', 'running')
                    ->select(['id', 'table_id', 'ordered_at', 'created_at', 'grand_total'])
                    ->with(['items:id,order_id,quantity']);
            }])
            ->get(['id', 'table_number', 'status', 'is_calling_waiter', 'is_bill_requested']);

        return response()->json($tables->map(function ($table) {
            $orders = $table->orders->map(fn($order) => [
                'grand_total' => (float) $order->grand_total,
                'ordered_at_iso' => optional($order->ordered_at ?? $order->created_at)->toIso8601String(),
                'items' => $order->items->map(fn($item) => [
                    'quantity' => (int) $item->quantity,
                ])->values(),
            ])->values();

            $status = (string) $table->status;
            if ($orders->isNotEmpty() && $status === 'available') {
                $status = 'occupied';
            }

            return [
                'table_number' => (string) $table->table_number,
                'status' => $status,
                'is_calling_waiter' => (bool) $table->is_calling_waiter,
                'is_bill_requested' => (bool) $table->is_bill_requested,
                'orders' => $orders,
            ];
        })->values());
    }

    public function transfer(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'table_id' => ['required', 'integer', 'exists:tables,id'],
            'target_waiter_id' => ['required', 'integer', 'exists:users,id'],
            'notes' => [
                Rule::requiredIf(fn () => ! $request->boolean('is_force_assign')),
                'nullable',
                'string',
                'max:255',
            ],
            'is_force_assign' => ['sometimes', 'boolean'],
        ]);

        $isManagerAssignment = (bool) ($validated['is_force_assign'] ?? false);
        abort_unless(! $isManagerAssignment || in_array($user->role, ['admin', 'manager'], true), 403);

        $table = Table::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('branch_id', (int) session('active_branch_id', $user->branch_id ?? 0))
            ->findOrFail($validated['table_id']);

        $targetWaiter = User::query()
            ->whereKey($validated['target_waiter_id'])
            ->when(! $isManagerAssignment, fn ($query) => $query->where('id', '!=', $user->id))
            ->where('tenant_id', $user->tenant_id)
            ->where('branch_id', $table->branch_id)
            ->where('role', 'waiter')
            ->where('is_active', true)
            ->firstOrFail();

        $transfer = TableServiceRequest::create([
            'tenant_id' => $table->tenant_id,
            'branch_id' => $table->branch_id,
            'table_id' => $table->id,
            'type' => 'table_transfer',
            'notes' => $validated['notes'] ?? null,
            'handled_by_waiter_id' => $user->id,
            'target_waiter_id' => $targetWaiter->id,
            'status' => $isManagerAssignment ? 'accepted' : 'pending',
            'requested_at' => now(),
            'accepted_at' => $isManagerAssignment ? now() : null,
        ]);

        broadcast(new TableTransferRequestUpdated($this->transferPayload(
            $transfer->load(['table', 'handledByWaiter', 'targetWaiter'])
        )));

        return response()->json([
            'success' => true,
            'message' => $isManagerAssignment
                ? 'Waiter assigned successfully.'
                : 'Table transfer request sent successfully.',
        ]);
    }

    public function transferRequests(Request $request)
    {
        $user = $request->user();
        $transfers = TableServiceRequest::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('branch_id', $user->branch_id)
            ->where('target_waiter_id', $user->id)
            ->where('status', 'pending')
            ->where('requested_at', '>=', now()->subMinutes(2))
            ->with(['table:id,table_number', 'handledByWaiter:id,name'])
            ->latest('requested_at')
            ->get()
            ->map(fn ($transfer) => $this->transferPayload($transfer));

        return response()->json(['transfers' => $transfers]);
    }

    public function transferActivity(Request $request)
    {
        $user = $request->user();
        $expiredTransfers = TableServiceRequest::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('branch_id', $user->branch_id)
            ->where('type', 'table_transfer')
            ->where('status', 'pending')
            ->where('requested_at', '<', now()->subMinutes(2))
            ->with(['table', 'handledByWaiter', 'targetWaiter'])
            ->get();

        foreach ($expiredTransfers as $transfer) {
            $transfer->update(['status' => 'cancelled', 'completed_at' => now()]);
            broadcast(new TableTransferRequestUpdated($this->transferPayload($transfer->fresh())));
        }

        $transfers = TableServiceRequest::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('branch_id', $user->branch_id)
            ->where('type', 'table_transfer')
            ->where('updated_at', '>=', now()->subMinutes(2))
            ->with(['table', 'handledByWaiter', 'targetWaiter'])
            ->latest('updated_at')
            ->get()
            ->map(fn ($transfer) => $this->transferPayload($transfer));

        return response()->json(['transfers' => $transfers]);
    }

    public function respondToTransfer(Request $request, TableServiceRequest $transfer)
    {
        $user = $request->user();
        abort_unless((int) $transfer->tenant_id === (int) $user->tenant_id
            && (int) $transfer->branch_id === (int) $user->branch_id
            && (int) $transfer->target_waiter_id === (int) $user->id
            && $transfer->type === 'table_transfer', 404);

        $validated = $request->validate(['decision' => ['required', 'in:accepted,cancelled']]);
        $updated = TableServiceRequest::query()
            ->whereKey($transfer->id)
            ->where('status', 'pending')
            ->update([
                'status' => $validated['decision'],
                'accepted_at' => $validated['decision'] === 'accepted' ? now() : null,
                'completed_at' => $validated['decision'] === 'cancelled' ? now() : null,
                'updated_at' => now(),
            ]);

        if (! $updated) {
            return response()->json(['message' => 'This transfer request has already been handled.'], 409);
        }

        $transfer->refresh();
        broadcast(new TableTransferRequestUpdated($this->transferPayload(
            $transfer->load(['table', 'handledByWaiter', 'targetWaiter'])
        )));

        return response()->json(['message' => $validated['decision'] === 'accepted'
            ? 'Table transfer accepted.'
            : 'Table transfer declined.']);
    }

    private function transferPayload(TableServiceRequest $transfer): array
    {
        return [
            'id' => $transfer->id,
            'branch_id' => $transfer->branch_id,
            'table_id' => $transfer->table_id,
            'table_number' => $transfer->table?->table_number,
            'from_waiter' => $transfer->handledByWaiter?->name ?? 'Unknown waiter',
            'handled_by_waiter_id' => $transfer->handled_by_waiter_id,
            'target_waiter_id' => $transfer->target_waiter_id,
            'target_waiter' => $transfer->targetWaiter?->name,
            'notes' => $transfer->notes,
            'status' => $transfer->status,
            'requested_at' => optional($transfer->requested_at)->toIso8601String(),
            'accepted_at' => optional($transfer->accepted_at)->toIso8601String(),
            'updated_at' => optional($transfer->updated_at)->toIso8601String(),
        ];
    }

    public function acceptWaiterCall(Table $table)
    {
        $user = Auth::user();
        abort_unless((int) $table->tenant_id === (int) $user->tenant_id, 403);
        abort_if($user->branch_id && (int) $table->branch_id !== (int) $user->branch_id, 403);

        $table->update(['is_calling_waiter' => false]);

        $handledAt = now();
        $serviceRequest = TableServiceRequest::query()
            ->where('table_id', $table->id)
            ->where('type', 'call_waiter')
            ->where('status', 'pending')
            ->latest('requested_at')
            ->first();
        $serviceRequest?->update([
            'status' => 'completed',
            'handled_by_waiter_id' => $user->id,
            'accepted_at' => $handledAt,
            'completed_at' => $handledAt,
        ]);

        return response()->json([
            'status' => (string) $table->status,
            'is_calling_waiter' => false,
            'is_bill_requested' => (bool) $table->is_bill_requested,
            'service_request' => $serviceRequest?->fresh(),
        ]);
    }

    public function clearBillRequest(Table $table)
    {
        $user = Auth::user();
        abort_unless((int) $table->tenant_id === (int) $user->tenant_id, 403);
        abort_if($user->branch_id && (int) $table->branch_id !== (int) $user->branch_id, 403);

        $table->update(['is_bill_requested' => false]);

        $completedAt = now();
        $serviceRequest = TableServiceRequest::query()
            ->where('table_id', $table->id)
            ->where('type', 'bill_request')
            ->where('status', 'pending')
            ->latest('requested_at')
            ->first();
        $serviceRequest?->update([
            'status' => 'completed',
            'handled_by_waiter_id' => $user->id,
            'accepted_at' => $completedAt,
            'completed_at' => $completedAt,
        ]);

        return response()->json([
            'status' => (string) $table->status,
            'is_calling_waiter' => (bool) $table->is_calling_waiter,
            'is_bill_requested' => false,
            'service_request' => $serviceRequest?->fresh(),
        ]);
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
            'status'       => 'required|in:available,occupied,reserved,out_of_service',
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
