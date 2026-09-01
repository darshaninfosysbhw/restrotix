<?php

namespace App\Services\Admin\MenuManagement;

use App\Models\BranchPaymentGateway;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class OrderStatusService
{
    public function resolveContext(string $qrToken): array
    {
        $table = Table::query()
            ->with('branch')
            ->where('qr_token', $qrToken)
            ->where('is_active', true)
            ->firstOrFail();

        $order = Order::query()
            ->where('table_id', $table->id)
            ->where('status', 'running')
            ->with(['items.orderItemAddons.masterAddon'])
            ->latest()
            ->first();

        if (!$order) {
            throw new ModelNotFoundException('Running order not found for table.');
        }

        return [$table, $order];
    }

    public function buildPageData(Table $table, Order $order, bool $showOrderPlaced = false): array
    {
        $order->loadMissing(['items.orderItemAddons.masterAddon', 'invoice']);

        $items = $order->items ?? collect();
        $orderItems = $this->consolidateOrderItems($items);
        $branch = $table->branch;
        $taxSetting = strtolower((string) ($branch?->tax_setting ?? 'exclusive'));
        $taxRate = (float) ($branch?->tax_rate ?? 0) / 100;
        $taxLabelName = ((float) ($branch?->tax_rate ?? 0)) === 13.0 ? 'VAT' : 'Tax';

        $originalSubtotal = (float) $items->sum(fn (OrderItem $item) => $this->itemTotal($item));
        $activeSubtotal = (float) $orderItems
            ->reject(fn (object $item) => (string) ($item->status ?? '') === 'rejected')
            ->sum(fn (object $item) => (float) ($item->total ?? 0));

        $invoice = $order->invoice;
        if ($invoice) {
            $taxSetting = strtolower((string) ($invoice->tax_setting ?? $taxSetting));
            $taxRate = (float) ($invoice->tax_rate_snapshot ?? $invoice->tax_rate ?? $taxRate);
            $activeSubtotal = (float) ($invoice->taxable_amount ?? $invoice->subtotal ?? $activeSubtotal);
            $taxAmount = (float) ($invoice->tax_amount ?? 0);
            $grandTotal = (float) ($invoice->grand_total ?? ($activeSubtotal + $taxAmount));
        } elseif ($taxSetting === 'inclusive') {
            $taxAmount = $taxRate > 0 ? $activeSubtotal - ($activeSubtotal / (1 + $taxRate)) : 0;
            $grandTotal = $activeSubtotal;
        } else {
            $taxAmount = $activeSubtotal * $taxRate;
            $grandTotal = $activeSubtotal + $taxAmount;
        }

        $showTaxAmount = $taxSetting !== 'inclusive' && $taxRate > 0;
        $kitchenStatusKey = strtolower((string) ($order->kitchen_status ?? 'pending'));
        $kitchenStage = $this->mapKitchenStage($kitchenStatusKey);
        $statusPill = $this->mapStatusPill((string) ($order->status ?? 'running'));

        return [
            'table' => $table,
            'order' => $order,
            'showOrderPlaced' => $showOrderPlaced,
            'orderNumber' => (string) ($order->order_number ?? 'N/A'),
            'orderPlacedAt' => optional($order->created_at)->format('d M Y, h:i A') ?? 'N/A',
            'tableNumber' => (string) ($table->table_number ?? 'N/A'),
            'qrToken' => (string) ($table->qr_token ?? ''),
            'liveItems' => $this->countTotalQuantity($orderItems),
            'runningCount' => $this->countItemsByStatuses($orderItems, ['new', 'pending', 'preparing']),
            'readyCount' => $this->countItemsByStatuses($orderItems, ['ready', 'served']),
            'orderItems' => $orderItems,
            'subtotal' => $activeSubtotal,
            'taxAmount' => $taxAmount,
            'grandTotal' => $grandTotal,
            'taxRate' => $taxRate,
            'branchTaxSetting' => $taxSetting,
            'branchTaxLabelName' => $taxLabelName,
            'showTaxAmount' => $showTaxAmount,
            'kitchenStage' => $kitchenStage,
            'statusPill' => $statusPill,
            'snapshot' => $this->buildSnapshot($table, $order),
        ];
    }

    public function resolvePaymentFlow(Table $table): array
    {
        $branch = $table->branch;
        $tenant = $branch?->tenant ?? Tenant::query()->find($table->tenant_id);
        $selfPaymentEnabled = (bool) ($tenant?->plan?->hasFeature('self_payment_enabled') ?? false);

        $config = BranchPaymentGateway::query()
            ->where('tenant_id', (int) $table->tenant_id)
            ->where('branch_id', (int) $table->branch_id)
            ->where('is_active', true)
            ->whereIn('checkout_mode', ['static_qr', 'dynamic_api'])
            ->with(['gateway'])
            ->get()
            ->filter(function (BranchPaymentGateway $branchPaymentGateway) {
                return $branchPaymentGateway->gateway
                    && (bool) $branchPaymentGateway->gateway->is_active
                    && $branchPaymentGateway->gateway->slug !== 'stripe';
            })
            ->sortBy(function (BranchPaymentGateway $branchPaymentGateway) {
                return $branchPaymentGateway->checkout_mode === 'static_qr' ? 0 : 1;
            })
            ->first();

        $checkoutMode = (string) ($config?->checkout_mode ?? 'disabled');
        $gatewayName = (string) ($config?->gateway?->name ?? '');
        $gatewaySlug = (string) ($config?->gateway?->slug ?? '');
        $staticQrImageUrl = $config?->static_qr_image ? asset('storage/' . $config->static_qr_image) : null;
        $staticQrLabel = trim((string) ($config?->static_qr_label ?? ''));

        if ($staticQrLabel === '') {
            $staticQrLabel = $gatewayName !== '' ? $gatewayName . ' QR' : 'Static QR';
        }

        $canProceedOnline = $selfPaymentEnabled && $config !== null && in_array($checkoutMode, ['static_qr', 'dynamic_api'], true);

        return [
            'self_payment_enabled' => $selfPaymentEnabled,
            'checkout_mode' => $checkoutMode,
            'gateway_name' => $gatewayName,
            'gateway_slug' => $gatewaySlug,
            'static_qr_image_url' => $staticQrImageUrl,
            'static_qr_label' => $staticQrLabel,
            'can_proceed_online' => $canProceedOnline,
            'has_config' => $config !== null,
            'mode_label' => $checkoutMode === 'static_qr'
                ? 'Static QR'
                : ($checkoutMode === 'dynamic_api' ? 'Dynamic API' : 'Disabled'),
        ];
    }

    public function buildSnapshot(Table $table, Order $order): array
    {
        $order->loadMissing(['items']);

        $kitchenStatusKey = strtolower((string) ($order->kitchen_status ?? 'pending'));
        $items = $this->consolidateOrderItems($order->items ?? collect());

        return [
            'table_number' => (string) ($table->table_number ?? ''),
            'order_number' => (string) ($order->order_number ?? ''),
            'order_status' => (string) ($order->status ?? 'running'),
            'kitchen_status' => $kitchenStatusKey,
            'kitchen_stage' => $this->mapKitchenStage($kitchenStatusKey),
            'items' => $items->map(function (object $item) {
                $rejectedAt = data_get($item, 'rejected_at');

                return [
                    'id' => (int) $item->id,
                    'ids_group' => $item->ids_group ?? [],
                    'item_name' => (string) $item->item_name,
                    'quantity' => (int) ($item->quantity ?? 0),
                    'status' => (string) ($item->status ?? 'new'),
                    'rejected_at' => $rejectedAt ? optional($rejectedAt)->toIso8601String() : null,
                    'rejection_reason' => (string) ($item->rejection_reason ?? ''),
                    'meta_text' => (string) ($item->meta_text ?? ''),
                ];
            })->values(),
        ];
    }

    private function consolidateOrderItems(Collection $items): Collection
    {
        return $items
            ->groupBy(function (OrderItem $item) {
                $status = strtolower((string) ($item->status ?? 'new'));

                if ($status === 'rejected') {
                    return 'rejected|' . (int) $item->id;
                }

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
                    $status,
                    (int) $item->menu_item_id,
                    strtolower(trim((string) ($item->item_name ?? ''))),
                    strtolower($notesKey),
                    strtolower($addonsKey),
                ]);
            })
            ->map(function (Collection $group) {
                $first = $group->first();
                $groupTotal = (float) $group->sum(fn (OrderItem $item) => $this->itemTotal($item));
                $groupStatus = $this->resolveGroupStatus($group);
                $isRejected = $groupStatus === 'rejected';

                return $this->mapOrderItemGroup($group, $groupTotal, $groupStatus, $isRejected);
            })
            ->values();
    }

    private function mapOrderItemGroup(Collection $group, float $groupTotal, string $groupStatus, bool $isRejected): object
    {
        $first = $group->first();
        $addons = $group->flatMap(function (OrderItem $item) {
            return $item->orderItemAddons->map(function ($addon) {
                $addonPrice = $this->resolveAddonPrice($addon);
                $addonQuantity = (int) $addon->quantity;
                $addonDiscount = max((float) ($addon->applied_discount ?? 0), 0);
                $addonBaseAmount = $addonPrice * $addonQuantity;
                return [
                    'id' => (int) $addon->menu_item_addon_id,
                    'addon_name' => (string) ($addon->addon_name ?? $addon->masterAddon?->name ?? ''),
                    'price' => $addonPrice,
                    'quantity' => $addonQuantity,
                    'discount' => $addonDiscount,
                    'applied_discount' => $addonDiscount,
                    'base_amount' => $addonBaseAmount,
                    'line_total_before_discount' => $addonBaseAmount,
                    'total' => max($addonBaseAmount - $addonDiscount, 0),
                ];
            });
        })->groupBy(function (array $addon) {
            return implode('|', [
                (int) ($addon['id'] ?? 0),
                strtolower(trim((string) ($addon['addon_name'] ?? ''))),
                number_format((float) ($addon['price'] ?? 0), 2, '.', ''),
                number_format((float) ($addon['discount'] ?? $addon['applied_discount'] ?? 0), 2, '.', ''),
            ]);
        })->map(function (Collection $addonGroup) {
            $firstAddon = $addonGroup->first();
            $discountTotal = (float) $addonGroup->sum(function (array $addon) {
                return (float) ($addon['discount'] ?? $addon['applied_discount'] ?? 0);
            });
            $quantityTotal = (int) $addonGroup->sum('quantity');
            $price = (float) $firstAddon['price'];
            $baseAmount = $price * $quantityTotal;
            return [
                'id' => $firstAddon['id'],
                'addon_name' => $firstAddon['addon_name'],
                'price' => $price,
                'quantity' => $quantityTotal,
                'discount' => $discountTotal,
                'applied_discount' => $discountTotal,
                'base_amount' => $baseAmount,
                'line_total_before_discount' => $baseAmount,
                'total' => max($baseAmount - $discountTotal, 0),
            ];
        })->values()->all();

        return (object) [
            'id' => (int) $first->id,
            'ids_group' => $group->pluck('id')->map(fn ($id) => (int) $id)->all(),
            'item_name' => (string) $first->item_name,
            'quantity' => (int) $group->sum('quantity'),
            'price' => (float) $first->price,
            'total' => $groupTotal,
            'display_total' => $isRejected ? 0 : $groupTotal,
            'status' => $groupStatus,
            'is_rejected' => $isRejected,
            'rejected_at' => $group->map(fn (OrderItem $item) => $item->rejected_at)->filter()->sortDesc()->first(),
            'rejection_reason' => (string) ($first->rejection_reason ?? 'Item cancelled by kitchen.'),
            'notes' => (string) ($first->notes ?? ''),
            'meta_text' => $this->buildItemMetaText($addons, (string) ($first->notes ?? '')),
            'image_url' => optional($first->menuItem)->image
                ? asset('storage/' . $first->menuItem->image)
                : asset('images/default-food.png'),
            'addons' => $addons,
        ];
    }

    private function resolveGroupStatus(Collection $group): string
    {
        $statuses = $group->map(fn (OrderItem $item) => strtolower((string) ($item->status ?? 'new')));

        if ($statuses->every(fn (string $status) => $status === 'rejected')) {
            return 'rejected';
        }

        if ($statuses->contains(fn (string $status) => in_array($status, ['new', 'pending'], true))) {
            return 'pending';
        }

        if ($statuses->contains(fn (string $status) => $status === 'preparing')) {
            return 'preparing';
        }

        if ($statuses->contains(fn (string $status) => in_array($status, ['ready', 'served'], true))) {
            return 'served';
        }

        return $statuses->first() ?: 'new';
    }

    private function countItemsByStatuses(Collection $items, array $statuses): int
    {
        $allowed = array_fill_keys(array_map('strtolower', $statuses), true);

        return $items->filter(function (object $item) use ($allowed) {
            $status = strtolower((string) ($item->status ?? ''));
            return isset($allowed[$status]);
        })->count();
    }

    private function countTotalQuantity(Collection $items): int
    {
        return (int) $items->sum(fn (object $item) => (int) ($item->quantity ?? 0));
    }

    private function itemTotal(OrderItem $item): float
    {
        return (float) ($item->total ?? ((float) $item->price * (int) $item->quantity));
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

    private function buildItemMetaText(array $addons, string $notes): string
    {
        $parts = [];

        if (!empty($addons)) {
            $addonText = collect($addons)
                ->map(function (array $addon) {
                    $name = trim((string) ($addon['addon_name'] ?? ''));
                    $qty = (int) ($addon['quantity'] ?? 1);
                    $price = isset($addon['price']) ? ' +Rs ' . number_format((float) $addon['price'], 0) : '';

                    return trim($name . ($qty > 1 ? ' x' . $qty : '') . $price);
                })
                ->filter()
                ->implode(', ');

            if ($addonText !== '') {
                $parts[] = $addonText;
            }
        }

        $notes = trim($notes);
        if ($notes !== '') {
            $parts[] = $notes;
        }

        return implode(', ', $parts);
    }

    private function mapKitchenStage(string $kitchenStatusKey): array
    {
        return match ($kitchenStatusKey) {
            'pending', 'confirmed' => [
                'label' => 'Accepted',
                'note' => 'Kitchen has accepted your order.',
                'step' => 'accepted',
            ],
            'preparing' => [
                'label' => 'Preparing',
                'note' => 'Kitchen is preparing your order.',
                'step' => 'preparing',
            ],
            'served' => [
                'label' => 'Served',
                'note' => 'Your order has been served.',
                'step' => 'served',
            ],
            default => [
                'label' => ucfirst($kitchenStatusKey ?: 'Pending'),
                'note' => 'Kitchen status is being updated live.',
                'step' => 'accepted',
            ],
        };
    }

    private function mapStatusPill(string $orderStatus): array
    {
        return match (strtolower($orderStatus)) {
            'completed', 'delivered', 'paid' => [
                'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                'fa-circle-check',
            ],
            'cancelled', 'rejected' => ['bg-red-500/15 text-red-400 border-red-500/30', 'fa-circle-xmark'],
            default => ['bg-orange-500/15 text-orange-400 border-orange-500/30', 'fa-clock'],
        };
    }
}
