<?php

namespace App\Services;

use App\Events\KitchenPickupAlertUpdated;
use App\Models\KitchenPickupAlert;
use App\Models\Order;

class KitchenPickupAlertService
{
    public function syncBatch(Order $order, int $kotNumber): ?KitchenPickupAlert
    {
        if ($kotNumber <= 0) {
            return null;
        }

        $order->loadMissing('items');
        $items = $order->items->where('kot_number', $kotNumber)->values();
        $activeItems = $items->where('status', '!=', 'rejected');
        $isReady = $activeItems->isNotEmpty()
            && $activeItems->every(fn ($item) => in_array((string) $item->status, ['ready', 'served'], true));

        $alert = KitchenPickupAlert::where('order_id', $order->id)
            ->where('kot_number', $kotNumber)
            ->first();

        if (! $isReady || $order->status !== 'running') {
            if ($alert && $alert->status === 'pending') {
                $alert->update(['status' => 'cancelled']);
                broadcast(new KitchenPickupAlertUpdated($this->payload($alert)))->toOthers();
            }

            return $alert;
        }

        if (! $alert) {
            $alert = KitchenPickupAlert::create([
                'tenant_id' => $order->tenant_id,
                'branch_id' => $order->branch_id,
                'order_id' => $order->id,
                'table_id' => $order->table_id,
                'kot_number' => $kotNumber,
                'status' => 'pending',
                'ready_at' => $activeItems->max('ready_at') ?? now(),
            ]);
            broadcast(new KitchenPickupAlertUpdated($this->payload($alert)))->toOthers();
        }

        return $alert;
    }

    public function syncOrder(Order $order): void
    {
        $order->loadMissing('items');
        $order->items->pluck('kot_number')->filter()->unique()->each(
            fn ($kotNumber) => $this->syncBatch($order, (int) $kotNumber)
        );
    }

    public function completePickupForServed(Order $order, int $kotNumber, ?int $waiterId = null): ?KitchenPickupAlert
    {
        if ($kotNumber <= 0) {
            return null;
        }

        $alert = KitchenPickupAlert::query()
            ->where('order_id', $order->id)
            ->where('kot_number', $kotNumber)
            ->where('status', 'pending')
            ->first();

        if (! $alert) {
            return null;
        }

        $alert->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'accepted_by_waiter_id' => $waiterId,
        ]);

        return $alert->fresh();
    }

    public function payload(KitchenPickupAlert $alert): array
    {
        $alert->loadMissing(['order.items', 'acceptedBy:id,name']);
        $items = $alert->order->items
            ->where('kot_number', $alert->kot_number)
            ->where('status', '!=', 'rejected')
            ->values();

        return [
            'id' => (int) $alert->id,
            'branch_id' => (int) $alert->branch_id,
            'order_id' => (int) $alert->order_id,
            'table_id' => $alert->table_id ? (int) $alert->table_id : null,
            'table_number' => (string) ($alert->order->table_number ?? ''),
            'kot_number' => (int) $alert->kot_number,
            'status' => (string) $alert->status,
            'ready_at' => optional($alert->ready_at)->toIso8601String(),
            'accepted_at' => optional($alert->accepted_at)->toIso8601String(),
            'accepted_by_waiter_id' => $alert->accepted_by_waiter_id ? (int) $alert->accepted_by_waiter_id : null,
            'accepted_by_waiter' => $alert->acceptedBy?->name,
            'items' => $items->map(fn ($item) => [
                'name' => (string) $item->item_name,
                'quantity' => (int) $item->quantity,
            ])->all(),
        ];
    }
}
