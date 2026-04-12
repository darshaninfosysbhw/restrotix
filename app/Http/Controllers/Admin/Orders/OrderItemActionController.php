<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Events\KitchenStatusUpdated;

class OrderItemActionController extends Controller
{
    public function serve($id)
    {
        $item = OrderItem::with('order')->findOrFail($id);

        // safety check
        if ($item->status !== 'ready') {
            return response()->json([
                'success' => false,
                'message' => 'Item is not ready yet'
            ], 400);
        }

        DB::transaction(function () use ($item) {

            $item->update([
                'status' => 'served',
                'served_at' => now(),
            ]);

            $order = $item->order;

            if ($order) {
                $this->syncOrderStatus($order);

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

        return response()->json([
            'success' => true,
            'message' => 'Item served successfully'
        ]);
    }

    /**
     * Sync order status based on items
     */
    private function syncOrderStatus($order)
    {
        $items = $order->items;

        if ($items->every(fn($i) => $i->status === 'served')) {
            $order->kitchen_status = 'served';
        } elseif ($items->contains(fn($i) => $i->status === 'ready')) {
            $order->kitchen_status = 'preparing';
        } else {
            $order->kitchen_status = 'pending';
        }

        $order->save();
    }
}
