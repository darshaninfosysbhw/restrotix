<?php

namespace App\Http\Controllers\Modules\Orders;

use App\Http\Controllers\Controller;
use App\Services\KitchenPickupAlertService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\KitchenStatusUpdated;

class OrderItemActionController extends Controller
{
    public function serve($id): JsonResponse
    {
        $item = OrderItem::with('order')->findOrFail($id);
        $pickupAlert = null;

        if ($item->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Cancelled item cannot be served'
            ], 400);
        }

        if ($item->status === 'served') {
            return response()->json([
                'success' => true,
                'message' => 'Item already served'
            ]);
        }

        DB::transaction(function () use ($item, &$pickupAlert) {
            $updateData = [
                'status' => 'served',
                'served_at' => now(),
            ];

            if (!$item->ready_at) {
                $updateData['ready_at'] = now();
            }

            if (!$item->started_at) {
                $updateData['started_at'] = now();
            }

            $item->update($updateData);

            $order = $item->order;

            if ($order) {
                $this->syncOrderStatus($order);
                $pickupAlert = app(KitchenPickupAlertService::class)->completePickupForServed(
                    $order,
                    (int) ($item->kot_number ?? 0),
                    Auth::id()
                );

                broadcast(new KitchenStatusUpdated([
                    'order_id' => $order->id,
                    'table_number' => (string) $order->table_number,
                    'branch_id' => (int) $order->branch_id,
                    'kitchen_status' => $order->kitchen_status,
                    'item_status' => 'served',
                    'served_at' => $item->served_at,
                ]))->toOthers();
            }
        });

        if ($pickupAlert) {
            broadcast(new \App\Events\KitchenPickupAlertUpdated(
                app(KitchenPickupAlertService::class)->payload($pickupAlert)
            ))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => 'Item served successfully',
            'pickup_alert' => $pickupAlert
                ? app(KitchenPickupAlertService::class)->payload($pickupAlert)
                : null,
        ]);
    }

    public function cancel(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $item = OrderItem::with('order')->findOrFail($id);

        if ($item->status === 'rejected') {
            return response()->json([
                'success' => true,
                'message' => 'Item already cancelled',
            ]);
        }

        if ($item->status === 'served') {
            return response()->json([
                'success' => false,
                'message' => 'Served item cannot be cancelled',
            ], 400);
        }

        $reason = trim((string) ($validated['reason'] ?? ''));
        if ($reason === '') {
            $reason = 'Item cancelled by staff';
        }

        DB::transaction(function () use ($item, $reason) {
            $item->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $order = $item->order;

            if ($order) {
                $this->syncOrderStatus($order);

                broadcast(new KitchenStatusUpdated([
                    'order_id' => (int) $order->id,
                    'table_number' => (string) ($order->table_number ?? ''),
                    'branch_id' => (int) ($order->branch_id ?? 0),
                    'kitchen_status' => (string) ($order->kitchen_status ?? 'pending'),
                    'item_status' => 'rejected',
                    'item_id' => (int) $item->id,
                    'rejection_reason' => $reason,
                ]))->toOthers();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Item cancelled successfully',
        ]);
    }

    /**
     * Sync order status based on items
     */
    private function syncOrderStatus($order)
    {
        $items = $order->items;

        $actionableItems = $items->whereNotIn('status', ['rejected']);

        if ($actionableItems->isEmpty()) {
            return;
        }

        if ($actionableItems->every(fn($i) => in_array($i->status, ['ready', 'served'], true))) {
            $order->kitchen_status = 'served';
        } elseif ($actionableItems->contains(fn($i) => $i->status === 'preparing')) {
            $order->kitchen_status = 'preparing';
        } elseif ($actionableItems->contains(fn($i) => in_array($i->status, ['new', 'pending']))) {
            $order->kitchen_status = 'pending';
        } else {
            return;
        }

        $order->save();
    }
}
