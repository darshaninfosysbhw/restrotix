<?php

namespace App\Http\Controllers\Kds;

use App\Events\KitchenStatusUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class KdsController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $layout = strtolower(trim($user->role)) === 'chef' ? 'core.layouts.chef' : 'core.layouts.admin';

        $statusFilter = (string) $request->query('status', 'all');

        $baseQuery = Order::where('branch_id', $branchId)
            ->where('status', 'running')
            ->with(['items']);

        $orders = $baseQuery->orderBy('kitchen_status', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $orderCards = $orders->map(fn($order) => $this->mapOrderForCard($order))
            ->when($statusFilter !== 'all', function ($collection) use ($statusFilter) {
                return $collection->filter(function ($card) use ($statusFilter) {
                    if ($statusFilter === 'new') {
                        return $card['status'] === 'pending' ||
                            collect($card['items'])->contains('status', 'new') ||
                            collect($card['items'])->contains('status', 'pending');
                    }

                    if ($statusFilter === 'preparing') {
                        $hasNewItems = collect($card['items'])->contains(fn($item) => in_array($item['status'], ['new', 'pending']));
                        return $card['status'] === 'preparing' && !$hasNewItems;
                    }

                    return $card['status'] === $this->mapFilterToInternal($statusFilter);
                });
            })->values();

        $stats = $this->getKdsStats($branchId);

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
            // Fallback: started_at nahi hai toh created_at use karo
            $startTime = $item->started_at ?? $item->created_at;
            $updateData['preparation_time'] = Carbon::parse($startTime)->diffInMinutes(now());
        }

        if ($status === 'rejected') {
            $updateData['rejection_reason'] = $request->reason ?? 'Item Unavailable';
        }

        DB::beginTransaction();
        try {
            $item->update($updateData);
            $this->syncOrderStatus($item->order_id);
            DB::commit();

            // Return order status taaki UI ko pata chale card move karna hai ya nahi
            $order = Order::find($item->order_id);
            if ($order) {
                broadcast(new KitchenStatusUpdated([
                    'order_id' => (int) $order->id,
                    'table_number' => (string) ($order->table_number ?? ''),
                    'branch_id' => (int) ($order->branch_id ?? 0),
                    'kitchen_status' => (string) ($order->kitchen_status ?? 'pending'),
                    'item_status' => (string) $status,
                ]))->toOthers();
            }

            return response()->json([
                'success' => true,
                'order_status' => $order->kitchen_status,
                'message' => "Item marked as {$status}"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id): RedirectResponse|JsonResponse
    {
        $order = Order::where('id', $id)->where('branch_id', Auth::user()->branch_id)->firstOrFail();
        $requestedStatus = $request->input('status');

        $nextStatus = $requestedStatus ?: match ($order->kitchen_status) {
            'pending', 'confirmed' => 'preparing',
            'preparing' => 'served',
            default => $order->kitchen_status,
        };
        $itemStatus = ($nextStatus === 'served') ? 'ready' : ($nextStatus === 'preparing' ? 'preparing' : 'new');

        DB::transaction(function () use ($order, $nextStatus) {
            $updateParams = ['kitchen_status' => $nextStatus];

            $order->update($updateParams);

            $itemStatus = ($nextStatus === 'served') ? 'ready' : ($nextStatus === 'preparing' ? 'preparing' : 'new');

            // Loop karke update taaki prep_time calculate ho sake bulk update ki jagah
            foreach ($order->items as $item) {
                $itemUpdates = ['status' => $itemStatus];
                if ($itemStatus === 'ready' && !$item->ready_at) {
                    $itemUpdates['ready_at'] = now();
                    $startTime = $item->started_at ?? $item->created_at;
                    $itemUpdates['preparation_time'] = Carbon::parse($startTime)->diffInMinutes(now());
                }
                if ($itemStatus === 'preparing' && !$item->started_at) {
                    $itemUpdates['started_at'] = now();
                }
                $item->update($itemUpdates);
            }
        });

        $order->refresh();
        broadcast(new KitchenStatusUpdated([
            'order_id' => (int) $order->id,
            'table_number' => (string) ($order->table_number ?? ''),
            'branch_id' => (int) ($order->branch_id ?? 0),
            'kitchen_status' => (string) ($order->kitchen_status ?? 'pending'),
            'item_status' => $itemStatus,
        ]))->toOthers();

        return $request->expectsJson()
            ? response()->json(['success' => true])
            : back();
    }

    /**
     * 🔥 FIX: Mark All Ready with individual Time Tracking
     */
    public function markAllReady(Request $request): RedirectResponse|JsonResponse
    {
        $branchId = Auth::user()->branch_id;
        $kitchenBroadcasts = [];

        DB::transaction(function () use ($branchId, &$kitchenBroadcasts) {
            $orders = Order::where('branch_id', $branchId)
                ->where('status', 'running')
                ->where('kitchen_status', 'preparing')
                ->get();

            foreach ($orders as $order) {
                $order->update([
                    'kitchen_status' => 'served',
                ]);

                // Loop items to calculate time for EACH item
                foreach ($order->items as $item) {
                    if ($item->status !== 'ready') {
                        $startTime = $item->started_at ?? $item->created_at;
                        $item->update([
                            'status' => 'ready',
                            'ready_at' => now(),
                            'preparation_time' => Carbon::parse($startTime)->diffInMinutes(now())
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
            broadcast(new KitchenStatusUpdated($payload))->toOthers();
        }

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => "All ready with time calculation"])
            : back()->with('success', "Orders updated.");
    }

    // ... (mapOrderForCard, getStatusVisual, getNextAction, getKdsStats same rahenge)

    private function mapOrderForCard(Order $order): array
    {
        $minutes = (int) $order->created_at->diffInMinutes(now());
        $isDelayed = ($order->kitchen_status !== 'served' && $minutes > 20);
        $isUrgent = $order->is_urgent || $minutes > 15;

        $kitchenStatus = (string) $order->kitchen_status ?: 'pending';
        $visual = $this->getStatusVisual($kitchenStatus);

        return [
            'id' => (int) $order->id,
            'order_number' => $order->order_number,
            'table_number' => $order->table_number,
            'order_type' => strtoupper(str_replace('_', ' ', $order->order_type)),
            'created_at_iso' => $order->created_at->toIso8601String(),
            'timer_text' => sprintf('%02d:%02dm', intdiv($minutes, 60), $minutes % 60),
            'is_urgent' => $isUrgent,
            'is_delayed' => $isDelayed,
            'status' => $kitchenStatus,
            'status_label' => $visual['label'],
            'status_badge_class' => $visual['badge_class'],
            'timer_class' => $isUrgent ? 'text-red-400 animate-pulse' : $visual['timer_class'],
            'card_class' => $isDelayed ? 'border-red-600 bg-red-900/10' : ($isUrgent ? 'border-orange-500/50 bg-orange-500/5' : 'border-gray-700 bg-gray-800'),
            'special_notes' => $order->notes,
            'items' => $order->items->map(fn($item) => [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'quantity' => $item->quantity,
                'status' => $item->status,
                'notes' => $item->notes,
                'rejection_reason' => $item->rejection_reason,
                'kitchen_type' => $item->kitchen_type,
            ])->all(),
            'action' => $this->getNextAction($kitchenStatus),
        ];
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

    private function getNextAction(string $status): ?array
    {
        if (in_array($status, ['pending', 'confirmed'])) {
            return ['next_status' => 'preparing', 'label' => 'START ALL', 'button_class' => 'border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white'];
        }
        if ($status === 'preparing') {
            return ['next_status' => 'served', 'label' => 'MARK ALL READY', 'button_class' => 'bg-orange-500 hover:bg-orange-600 text-white'];
        }
        return null;
    }

    private function getKdsStats($branchId): array
    {
        $base = Order::where('branch_id', $branchId)->where('status', 'running');

        $hasNewOrPendingItems = fn($q) => $q->whereIn('status', ['new', 'pending']);

        return [
            'all_orders' => (clone $base)->count(),
            'new_orders' => (clone $base)
                ->where(function ($q) use ($hasNewOrPendingItems) {
                    $q->whereIn('kitchen_status', ['pending', 'confirmed'])
                        ->orWhereHas('items', $hasNewOrPendingItems);
                })->count(),
            'preparing_orders' => (clone $base)
                ->where('kitchen_status', 'preparing')
                ->whereDoesntHave('items', $hasNewOrPendingItems)
                ->count(),
            'ready_orders' => (clone $base)
                ->where('kitchen_status', 'served')
                ->whereDoesntHave('items', $hasNewOrPendingItems)
                ->count(),
            'completed_today' => Order::where('branch_id', $branchId)->where('status', 'completed')->whereDate('created_at', Carbon::today())->count(),
        ];
    }

    private function syncOrderStatus($orderId)
    {
        $items = OrderItem::where('order_id', $orderId)->get();
        $order = Order::find($orderId);

        if ($items->every(fn($i) => in_array($i->status, ['ready', 'rejected']))) {
            $order->kitchen_status = 'served';
        } elseif ($items->every(fn($i) => in_array($i->status, ['preparing', 'ready', 'rejected']))) {
            $order->kitchen_status = 'preparing';
        } else {
            $order->kitchen_status = 'pending';
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
