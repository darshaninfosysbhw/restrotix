<?php

namespace App\Http\Controllers\Modules\Kds;

use App\Events\KitchenStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\KitchenPickupAlertService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KdsController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $user = Auth::user();
        $branchId = (int) session('active_branch_id', $user->branch_id ?? 0);
        $layout = strtolower(trim($user->role)) === 'chef' ? 'core.layouts.chef' : 'core.layouts.admin';

        $statusFilter = (string) $request->query('status', 'all');

        $baseQuery = Order::where('branch_id', $branchId)
            ->where('status', 'running')
            ->with(['items.orderItemAddons.masterAddon']);

        $orders = $baseQuery->orderBy('created_at', 'asc')->get();

        $allOrderCards = $this->buildKitchenBatchCards($orders);

        $orderCards = $allOrderCards
            ->when($statusFilter !== 'all', function ($collection) use ($statusFilter) {
                return $collection->filter(function ($card) use ($statusFilter) {
                    if ($statusFilter === 'new') {
                        return $card['status'] === 'pending';
                    }

                    if ($statusFilter === 'preparing') {
                        return $card['status'] === 'preparing';
                    }

                    return $card['status'] === $this->mapFilterToInternal($statusFilter);
                });
            })->values();

        $stats = $this->getKdsStats($branchId, $allOrderCards);

        if ($request->ajax()) {
            return response()->json([
                'cards' => view('modules.kds.partials.cards', ['orderCards' => $orderCards])->render(),
                'filters' => view('modules.kds.partials.filters', compact('statusFilter', 'stats'))->render(),
                'stats' => $stats,
            ]);
        }

        return view('modules.kds.index', compact('orderCards', 'stats', 'layout', 'statusFilter'));
    }

    /**
     * 🔥 FIX: Responsive Last Item & Accurate Time Calculation
     */
    public function updateItemStatus(Request $request, $id): JsonResponse
    {
        $item = OrderItem::findOrFail($id);
        $status = $request->status;

        $updateData = ['status' => $status];

        if ($status === 'preparing') {
            $updateData['started_at'] = now();
        }

        if ($status === 'ready') {
            $updateData['ready_at'] = now();
            $startTime = $item->started_at ?? $item->created_at;
            $updateData['preparation_time'] = Carbon::parse($startTime)->diffInMinutes(now());
        }

        if ($status === 'rejected') {
            $updateData['rejection_reason'] = $request->reason ?? 'Item Unavailable';
            $updateData['rejected_at'] = now();
        }

        DB::beginTransaction();
        try {
            $item->update($updateData);
            $this->syncOrderStatus($item->order_id);
            DB::commit();

            $order = Order::find($item->order_id);
            if ($order) {
                app(KitchenPickupAlertService::class)->syncBatch($order, (int) ($item->kot_number ?? 0));
                broadcast(new KitchenStatusUpdated([
                    'order_id' => (int) $order->id,
                    'table_number' => (string) ($order->table_number ?? ''),
                    'branch_id' => (int) ($order->branch_id ?? 0),
                    'kitchen_status' => (string) ($order->kitchen_status ?? 'pending'),
                    'item_status' => (string) $status,
                    'item_id' => (int) $item->id,
                    'item_name' => (string) $item->item_name,
                    'rejection_reason' => (string) ($item->rejection_reason ?? $request->reason ?? ''),
                    'kot_number' => (int) ($item->kot_number ?? 0),
                    'batch_key' => ((int) $order->id).':'.((int) ($item->kot_number ?? 0)),
                ]))->toOthers();
            }

            return response()->json([
                'success' => true,
                'order_status' => $order->kitchen_status,
                'message' => "Item marked as {$status}",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id): RedirectResponse|JsonResponse
    {
        $order = Order::where('id', $id)
            ->where('branch_id', (int) session('active_branch_id', Auth::user()->branch_id ?? 0))
            ->firstOrFail();
        $requestedKotNumber = $request->filled('kot_number') ? (int) $request->input('kot_number') : null;
        $requestedStatus = $request->input('status');
        $targetItems = $order->items;

        if ($requestedKotNumber > 0) {
            $targetItems = $order->items->where('kot_number', $requestedKotNumber)->values();
        }

        if ($targetItems->isEmpty()) {
            $targetItems = $order->items;
            $requestedKotNumber = null;
        }

        $currentBatchStatus = $this->resolveBatchStatus($targetItems);
        $nextStatus = $requestedStatus ?: match ($currentBatchStatus) {
            'pending' => 'preparing',
            'preparing' => 'served',
            default => $currentBatchStatus,
        };
        $itemStatus = ($nextStatus === 'served') ? 'ready' : ($nextStatus === 'preparing' ? 'preparing' : 'new');

        DB::transaction(function () use ($order, $targetItems, $itemStatus) {
            foreach ($targetItems->whereNotIn('status', ['rejected']) as $item) {
                $itemUpdates = ['status' => $itemStatus];
                if ($itemStatus === 'ready' && ! $item->ready_at) {
                    $itemUpdates['ready_at'] = now();
                    $startTime = $item->started_at ?? $item->created_at;
                    $itemUpdates['preparation_time'] = Carbon::parse($startTime)->diffInMinutes(now());
                }
                if ($itemStatus === 'preparing' && ! $item->started_at) {
                    $itemUpdates['started_at'] = now();
                }
                $item->update($itemUpdates);
            }

            $this->syncOrderStatus($order->id);
        });

        $order->refresh();
        $batchKotNumber = $requestedKotNumber ?: (int) ($targetItems->first()?->kot_number ?? 0);
        if ($requestedKotNumber) {
            app(KitchenPickupAlertService::class)->syncBatch($order, $batchKotNumber);
        } else {
            app(KitchenPickupAlertService::class)->syncOrder($order);
        }
        broadcast(new KitchenStatusUpdated([
            'order_id' => (int) $order->id,
            'table_number' => (string) ($order->table_number ?? ''),
            'branch_id' => (int) ($order->branch_id ?? 0),
            'kitchen_status' => (string) ($order->kitchen_status ?? 'pending'),
            'item_status' => $itemStatus,
            'kot_number' => $batchKotNumber,
            'batch_key' => $batchKotNumber > 0 ? ((int) $order->id).':'.$batchKotNumber : null,
        ]))->toOthers();

        return $request->expectsJson() ? response()->json(['success' => true]) : back();
    }

    /**
     * 🔥 FIX: Mark All Ready with individual Time Tracking
     */
    public function markAllReady(Request $request): RedirectResponse|JsonResponse
    {
        $branchId = (int) session('active_branch_id', Auth::user()->branch_id ?? 0);
        $kitchenBroadcasts = [];

        DB::transaction(function () use ($branchId, &$kitchenBroadcasts) {
            $orders = Order::where('branch_id', $branchId)
                ->where('status', 'running')
                ->where('kitchen_status', 'preparing')
                ->get();

            foreach ($orders as $order) {
                $order->update(['kitchen_status' => 'served']);

                foreach ($order->items as $item) {
                    if ($item->status !== 'ready') {
                        $startTime = $item->started_at ?? $item->created_at;
                        $item->update([
                            'status' => 'ready',
                            'ready_at' => now(),
                            'preparation_time' => Carbon::parse($startTime)->diffInMinutes(now()),
                        ]);
                    }
                }
                $kitchenBroadcasts[] = [
                    'order_id' => (int) $order->id,
                    'table_number' => (string) ($order->table_number ?? ''),
                    'branch_id' => (int) ($order->branch_id ?? 0),
                    'kitchen_status' => 'served',
                    'item_status' => 'ready',
                ];
            }
        });

        foreach ($kitchenBroadcasts as $payload) {
            $readyOrder = Order::with('items')->find($payload['order_id']);
            if ($readyOrder) {
                app(KitchenPickupAlertService::class)->syncOrder($readyOrder);
            }
            broadcast(new KitchenStatusUpdated($payload))->toOthers();
        }

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'All ready with time calculation'])
            : back()->with('success', 'Orders updated.');
    }

    private function mapOrderForCard(Order $order): array
    {
        $minutes = (int) $order->created_at->diffInMinutes(now());
        $isDelayed = ($order->kitchen_status !== 'served' && $minutes > 20);
        $isUrgent = $order->is_urgent || $minutes > 15;
        $kotNumber = $this->resolveKotNumber($order);

        $kitchenStatus = (string) $order->kitchen_status ?: 'pending';
        $visual = $this->getStatusVisual($kitchenStatus);
        $hasRejectedItems = $order->items->contains(fn ($item) => $item->status === 'rejected');
        $hasStartableItems = $order->items->contains(fn ($item) => in_array($item->status, ['new', 'pending']));
        $hasUnfinishedItems = $order->items->contains(fn ($item) => in_array($item->status, ['new', 'pending', 'preparing']));
        $isFinalized = ! $hasUnfinishedItems;

        // 🌟 FINAL ENTERPRISE CONSOLIDATION ENGINE: Group by item id + status + normalized notes + addons
        $consolidatedItems = $order->items->groupBy(function ($item) {
            $notesKey = trim((string) ($item->notes ?? ''));
            $addonsKey = $item->orderItemAddons
                ->map(function ($addon) {
                    $addonPrice = $this->resolveAddonPrice($addon);

                    return implode('-', [
                        (int) ($addon->menu_item_addon_id ?? 0),
                        trim((string) ($addon->addon_name ?? $addon->masterAddon?->name ?? '')),
                        $addonPrice,
                        (int) ($addon->quantity ?? 1),
                    ]);
                })
                ->sort()
                ->values()
                ->implode('|');

            return implode('|', [
                (int) $item->menu_item_id,
                strtolower(trim((string) ($item->status ?? 'new'))),
                strtolower($notesKey),
                strtolower($addonsKey),
            ]);
        })->map(function ($group) {
            $first = $group->first();

            $isCompleted = $group->every(fn ($item) => in_array($item->status, ['ready', 'served', 'rejected']));

            $addons = $group->flatMap(function ($item) {
                return $item->orderItemAddons->map(function ($addon) {
                    $addonPrice = $this->resolveAddonPrice($addon);

                    return [
                        'id' => (int) $addon->menu_item_addon_id,
                        'addon_name' => (string) ($addon->addon_name ?? $addon->masterAddon?->name ?? ''),
                        'price' => $addonPrice,
                        'quantity' => (int) $addon->quantity,
                    ];
                });
            })->groupBy(function ($addon) {
                return implode('|', [
                    (int) ($addon['id'] ?? 0),
                    strtolower(trim((string) ($addon['addon_name'] ?? ''))),
                    number_format((float) ($addon['price'] ?? 0), 2, '.', ''),
                ]);
            })->map(function ($addonGroup) {
                $firstAddon = $addonGroup->first();

                return [
                    'id' => $firstAddon['id'],
                    'addon_name' => $firstAddon['addon_name'],
                    'price' => (float) $firstAddon['price'],
                    'quantity' => (int) $addonGroup->sum('quantity'),
                ];
            })->values()->all();

            return [
                'id' => $first->id,
                'item_name' => $first->item_name,
                'quantity' => (int) $group->sum('quantity'),
                'status' => (string) $first->status,
                'is_completed' => $isCompleted,
                'notes' => $first->notes,
                'addons' => $addons,
                'rejection_reason' => $first->rejection_reason,
                'kitchen_type' => $first->kitchen_type,
                'ids_group' => $group->pluck('id')->all(),
            ];
        })->values()->all();

        return [
            'id' => (int) $order->id,
            'order_number' => $order->order_number,
            'table_number' => $order->table_number,
            'order_type' => strtoupper(str_replace('_', ' ', $order->order_type)),
            'created_at_iso' => $order->created_at->toIso8601String(),
            'timer_text' => sprintf('%02d:%02dm', intdiv($minutes, 60), $minutes % 60),
            'kot_number' => $kotNumber,
            'kot_label' => $kotNumber ? 'KOT: '.$kotNumber : null,
            'is_urgent' => $isUrgent,
            'is_delayed' => $isDelayed,
            'status' => $kitchenStatus,
            'status_label' => $visual['label'],
            'status_badge_class' => $visual['badge_class'],
            'timer_class' => $isUrgent ? 'text-red-400 animate-pulse' : $visual['timer_class'],
            'card_class' => $isDelayed ? 'border-red-600 bg-red-900/10' : ($isUrgent ? 'border-orange-500/50 bg-orange-500/5' : 'border-gray-700 bg-gray-800'),
            'special_notes' => $order->notes,
            'items' => $consolidatedItems,
            'has_rejected_items' => $hasRejectedItems,
            'action' => $this->getNextAction($kitchenStatus, $isFinalized, $hasStartableItems),
        ];
    }

    private function resolveKotNumber(Order $order): ?int
    {
        $kotNumber = collect($order->items ?? [])
            ->pluck('kot_number')
            ->map(fn ($value) => (int) $value)
            ->filter(fn (int $value) => $value > 0)
            ->unique()
            ->first();

        return $kotNumber > 0 ? $kotNumber : null;
    }

    private function getStatusVisual(string $status): array
    {
        return match ($status) {
            'pending', 'confirmed' => ['label' => 'NEW', 'badge_class' => 'bg-orange-500', 'timer_class' => 'text-orange-400'],
            'preparing' => ['label' => 'PREPARING', 'badge_class' => 'bg-blue-500', 'timer_class' => 'text-blue-400'],
            'served' => ['label' => 'READY', 'badge_class' => 'bg-green-500', 'timer_class' => 'text-green-400'],
            default => ['label' => strtoupper($status), 'badge_class' => 'bg-gray-500', 'timer_class' => 'text-gray-400'],
        };
    }

    private function resolveAddonPrice(object $addon): float
    {
        $addonPrice = (float) ($addon->price ?? 0);
        if ($addonPrice > 0) {
            return $addonPrice;
        }

        $masterPrice = (float) ($addon->masterAddon?->price ?? 0);

        return $masterPrice > 0 ? $masterPrice : 0;
    }

    private function getNextAction(string $status, bool $isFinalized = false, bool $hasStartableItems = true): ?array
    {
        if (in_array($status, ['pending', 'confirmed'])) {
            return [
                'next_status' => 'preparing',
                'label' => 'START ALL',
                'button_class' => 'border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white',
                'disabled' => ! $hasStartableItems,
            ];
        }
        if ($isFinalized || $status === 'served') {
            return [
                'next_status' => 'served',
                'label' => 'READY',
                'button_class' => 'bg-green-500 hover:bg-green-600 text-white',
                'disabled' => true,
            ];
        }
        if ($status === 'preparing') {
            return [
                'next_status' => 'served',
                'label' => 'MARK ALL READY',
                'button_class' => 'bg-orange-500 hover:bg-orange-600 text-white',
                'disabled' => false,
            ];
        }

        return null;
    }

    private function getKdsStats(int $branchId, ?Collection $orderCards = null): array
    {
        if ($orderCards instanceof Collection) {
            return [
                'all_orders' => $orderCards->count(),
                'new_orders' => $orderCards->where('status', 'pending')->count(),
                'preparing_orders' => $orderCards->where('status', 'preparing')->count(),
                'ready_orders' => $orderCards->where('status', 'served')->count(),
                'completed_today' => Order::where('branch_id', $branchId)
                    ->where('status', 'completed')
                    ->whereDate('created_at', Carbon::today())
                    ->count(),
            ];
        }

        $orders = Order::where('branch_id', $branchId)
            ->where('status', 'running')
            ->with(['items.orderItemAddons.masterAddon'])
            ->orderBy('created_at', 'asc')
            ->get();

        $cards = $this->buildKitchenBatchCards($orders);

        return [
            'all_orders' => $cards->count(),
            'new_orders' => $cards->where('status', 'pending')->count(),
            'preparing_orders' => $cards->where('status', 'preparing')->count(),
            'ready_orders' => $cards->where('status', 'served')->count(),
            'completed_today' => Order::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->whereDate('created_at', Carbon::today())
                ->count(),
        ];
    }

    private function buildKitchenBatchCards(Collection $orders): Collection
    {
        return $orders->flatMap(function (Order $order) {
            return $order->items
                ->groupBy(function ($item) {
                    $kotNumber = (int) ($item->kot_number ?? 0);

                    return $kotNumber > 0 ? $kotNumber : 1;
                })
                ->map(function (Collection $items, $kotNumber) use ($order) {
                    return $this->mapBatchForCard($order, (int) $kotNumber, $items->values());
                });
        })->filter(fn ($card) => ! empty($card))->values();
    }

    private function mapBatchForCard(Order $order, int $kotNumber, Collection $items): array
    {
        if ($items->isEmpty()) {
            return [];
        }

        $firstItem = $items->sortBy(fn ($item) => optional($item->created_at)->timestamp ?? 0)->first();
        $batchCreatedAt = $firstItem?->created_at ?? $order->created_at ?? now();
        $batchStatus = $this->resolveBatchStatus($items);
        $visual = $this->getStatusVisual($batchStatus);
        $hasRejectedItems = $items->contains(fn ($item) => $item->status === 'rejected');
        $hasStartableItems = $items->contains(fn ($item) => in_array($item->status, ['new', 'pending'], true));
        $hasUnfinishedItems = $items->contains(fn ($item) => in_array($item->status, ['new', 'pending', 'preparing'], true));
        $isFinalized = ! $hasUnfinishedItems;

        $minutes = $batchCreatedAt instanceof \Carbon\CarbonInterface
            ? (int) $batchCreatedAt->diffInMinutes(now())
            : 0;
        $isDelayed = ($batchStatus !== 'served' && $minutes > 20);
        $isUrgent = (bool) ($order->is_urgent ?? false) || $minutes > 15;

        $consolidatedItems = $this->consolidateKitchenItems($items);
        $statusSort = match ($batchStatus) {
            'pending' => 0,
            'preparing' => 1,
            'served' => 2,
            default => 3,
        };

        return [
            'id' => (int) $order->id,
            'batch_key' => ((int) $order->id).':'.$kotNumber,
            'order_number' => $order->order_number,
            'table_number' => $order->table_number,
            'order_type' => strtoupper(str_replace('_', ' ', (string) $order->order_type)),
            'created_at_iso' => $batchCreatedAt instanceof \Carbon\CarbonInterface
                ? $batchCreatedAt->toIso8601String()
                : now()->toIso8601String(),
            'timer_text' => sprintf('%02d:%02dm', intdiv($minutes, 60), $minutes % 60),
            'kot_number' => $kotNumber,
            'kot_label' => $kotNumber ? 'KOT: '.$kotNumber : null,
            'is_urgent' => $isUrgent,
            'is_delayed' => $isDelayed,
            'status' => $batchStatus,
            'status_label' => $visual['label'],
            'status_badge_class' => $visual['badge_class'],
            'timer_class' => $isUrgent ? 'text-red-400 animate-pulse' : $visual['timer_class'],
            'card_class' => $isDelayed ? 'border-red-600 bg-red-900/10' : ($isUrgent ? 'border-orange-500/50 bg-orange-500/5' : 'border-gray-700 bg-gray-800'),
            'special_notes' => $order->notes,
            'items' => $consolidatedItems,
            'has_rejected_items' => $hasRejectedItems,
            'action' => $this->getNextAction($batchStatus, $isFinalized, $hasStartableItems),
            'status_sort' => $statusSort,
            'created_at_sort' => $batchCreatedAt instanceof \Carbon\CarbonInterface ? $batchCreatedAt->timestamp : 0,
        ];
    }

    private function consolidateKitchenItems(Collection $items): array
    {
        return $items->groupBy(function ($item) {
            $notesKey = trim((string) ($item->notes ?? ''));
            $addonsKey = $item->orderItemAddons
                ->map(function ($addon) {
                    $addonPrice = $this->resolveAddonPrice($addon);

                    return implode('-', [
                        (int) ($addon->menu_item_addon_id ?? 0),
                        trim((string) ($addon->addon_name ?? $addon->masterAddon?->name ?? '')),
                        $addonPrice,
                        (int) ($addon->quantity ?? 1),
                    ]);
                })
                ->sort()
                ->values()
                ->implode('|');

            return implode('|', [
                (int) $item->menu_item_id,
                strtolower(trim((string) ($item->status ?? 'new'))),
                strtolower($notesKey),
                strtolower($addonsKey),
            ]);
        })->map(function ($group) {
            $first = $group->first();

            $isCompleted = $group->every(fn ($item) => in_array($item->status, ['ready', 'served', 'rejected'], true));

            $addons = $group->flatMap(function ($item) {
                return $item->orderItemAddons->map(function ($addon) {
                    $addonPrice = $this->resolveAddonPrice($addon);

                    return [
                        'id' => (int) $addon->menu_item_addon_id,
                        'addon_name' => (string) ($addon->addon_name ?? $addon->masterAddon?->name ?? ''),
                        'price' => $addonPrice,
                        'quantity' => (int) $addon->quantity,
                    ];
                });
            })->groupBy(function ($addon) {
                return implode('|', [
                    (int) ($addon['id'] ?? 0),
                    strtolower(trim((string) ($addon['addon_name'] ?? ''))),
                    number_format((float) ($addon['price'] ?? 0), 2, '.', ''),
                ]);
            })->map(function ($addonGroup) {
                $firstAddon = $addonGroup->first();

                return [
                    'id' => $firstAddon['id'],
                    'addon_name' => $firstAddon['addon_name'],
                    'price' => (float) $firstAddon['price'],
                    'quantity' => (int) $addonGroup->sum('quantity'),
                ];
            })->values()->all();

            return [
                'id' => $first->id,
                'item_name' => $first->item_name,
                'quantity' => (int) $group->sum('quantity'),
                'status' => (string) $first->status,
                'is_completed' => $isCompleted,
                'notes' => $first->notes,
                'addons' => $addons,
                'rejection_reason' => $first->rejection_reason,
                'kitchen_type' => $first->kitchen_type,
                'ids_group' => $group->pluck('id')->all(),
            ];
        })->values()->all();
    }

    private function resolveBatchStatus(Collection $items): string
    {
        $actionableItems = $items->whereNotIn('status', ['rejected']);

        if ($actionableItems->isEmpty()) {
            return 'served';
        }

        if ($actionableItems->every(fn ($item) => in_array((string) $item->status, ['ready', 'served'], true))) {
            return 'served';
        }

        if ($actionableItems->contains(fn ($item) => in_array($item->status, ['new', 'pending'], true))) {
            return 'pending';
        }

        if ($actionableItems->contains(fn ($item) => in_array($item->status, ['preparing'], true))) {
            return 'preparing';
        }

        return 'pending';
    }

    private function syncOrderStatus($orderId)
    {
        $items = OrderItem::where('order_id', $orderId)->get();
        $order = Order::find($orderId);

        $actionableItems = $items->whereNotIn('status', ['rejected']);

        if ($actionableItems->isEmpty()) {
            return;
        }

        if ($actionableItems->every(fn ($i) => in_array($i->status, ['ready', 'served'], true))) {
            $order->kitchen_status = 'served';
        } elseif ($actionableItems->contains(fn ($i) => $i->status === 'preparing')) {
            $order->kitchen_status = 'preparing';
        } elseif ($actionableItems->contains(fn ($i) => in_array($i->status, ['new', 'pending']))) {
            $order->kitchen_status = 'pending';
        } else {
            return;
        }

        $order->save();
    }

    private function mapFilterToInternal($filter)
    {
        return match ($filter) {
            'new' => 'pending',
            'preparing' => 'preparing',
            'ready' => 'served',
            default => 'all'
        };
    }
}
