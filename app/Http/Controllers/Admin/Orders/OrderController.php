<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use App\Events\NewOrderReceived;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.id'        => 'required',
            'items.*.name'      => 'required',
            'items.*.price'     => 'required|numeric',
            'items.*.quantity'  => 'required|integer|min:1',
            'table_id'          => 'nullable|integer|exists:tables,id',
            'order_type'        => 'required|string',
            'source'            => 'nullable|in:waiter,qr,web,pos',
        ]);

        DB::beginTransaction();

        try {
            $cartItems = $request->items;
            $user = Auth::user();

            // 🔥 STEP 1: Same table ka running order find karo
            $order = null;
            if ($request->order_type === 'dine_in' && $request->table_id) {
                $order = Order::where('table_id', $request->table_id)
                    ->where('status', 'running')
                    ->first();
            }

            // 🔥 STEP 2: Agar nahi mila → New Order Create
            if (!$order) {
                $order = Order::create([
                    'tenant_id'            => $user->tenant_id ?? 1,
                    'branch_id'            => $user->branch_id ?? 1,
                    'table_id'             => $request->table_id,
                    'order_number'         => 'ORD-' . strtoupper(uniqid()),
                    'table_number'         => $request->table_number,
                    'order_type'           => $request->order_type ?? 'dine_in',
                    'subtotal'             => 0,
                    'discount_amount'      => 0,
                    'tax_amount'           => 0,
                    'grand_total'          => 0,
                    'status'               => 'running',
                    'payment_status'       => 'pending',
                    'notes'                => $request->notes,
                    'source'               => $request->source,
                    'created_by'           => Auth::id(),
                ]);
            } elseif ($request->filled('source')) {
                $order->update([
                    'source' => $request->source,
                ]);
            }

            // 🔥 STEP 3: Items add karo (Note-wise separation logic)
            foreach ($cartItems as $item) {
                $itemNote = $item['note'] ?? null;
                $existingItem = OrderItem::where('order_id', $order->id)
                    ->where('menu_item_id', $item['id'])
                    ->where('notes', $itemNote)
                    // Served/ready/rejected line me merge nahi karna; warna new KOT hide ho jata hai.
                    ->whereIn('status', ['new', 'pending', 'preparing'])
                    ->first();

                if ($existingItem) {
                    $existingItem->quantity += $item['quantity'];
                    $existingItem->total = $existingItem->quantity * $existingItem->price;
                    $existingItem->save();
                } else {
                    OrderItem::create([
                        'order_id'            => $order->id,
                        'menu_item_id'        => $item['id'],
                        'item_name'           => $item['name'],
                        'price'               => $item['price'],
                        'quantity'            => $item['quantity'],
                        'total'               => $item['price'] * $item['quantity'],
                        'notes'               => $itemNote,
                    ]);
                }
            }

            // 🔥 STEP 4: Subtotal recalculate (Database se fresh sum uthao)
            $newSubtotal = OrderItem::where('order_id', $order->id)->sum('total');

            $order->update([
                'subtotal'             => $newSubtotal,
                'grand_total'          => $newSubtotal,
            ]);

            // Existing running order me naya item aaya ho to KDS card ko wapas NEW flow me lao.
            if ($order->items()->whereIn('status', ['new', 'pending'])->exists()) {
                $order->update(['kitchen_status' => 'pending']);
            }

            // 🔥 STEP 5: Table occupied mark karo
            if ($order->order_type === 'dine_in' && $order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'occupied']);
            }

            // 🔥 STEP 6: Real-time Notification (Broadcasting)
            broadcast(new \App\Events\NewOrderReceived([
                'table_id'              => $order->table_id,
                'table_number'          => $order->table_number,
                'order_number'          => $order->order_number,
                'items_count'           => OrderItem::where('order_id', $order->id)->sum('quantity'),
                'tenant_id'             => $order->tenant_id,
                'branch_id'             => $order->branch_id,
            ]))->toOthers();

            DB::commit();

            return response()->json([
                'success'               => true,
                'message'               => 'Order processed successfully',
                'order_id'              => $order->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'               => false,
                'message'               => 'Something went wrong',
                'error'                 => $e->getMessage()
            ], 500);
        }
    }
}
