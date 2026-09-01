<?php

namespace App\Http\Resources\Admin\Orders;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Order $order */
        $order = $this->resource;

        $currencySymbol = trim((string) $request->attributes->get('currency_symbol', '₹'));
        $orderStatus = strtolower((string) ($order->status ?? 'running'));
        $paymentStatus = strtolower((string) ($order->invoice?->status ?? $order->payment_status ?? 'pending'));
        $invoice = $order->invoice;
        $creator = $order->creator;
        $paymentSession = $order->relationLoaded('paymentSessions')
            ? $order->paymentSessions
                ->sortByDesc(fn ($session) => $session->paid_at?->timestamp ?? 0)
                ->first()
            : null;

        $tableNumber = trim((string) ($invoice?->table_number_snapshot ?? $order->table_number ?? ''));
        $customerName = trim((string) ($invoice?->customer_name_snapshot ?? ''));

        if ($customerName === '') {
            $customerName = trim((string) ($order->order_by_label ?? 'Guest'));
        }

        $subtext = trim((string) ($creator?->phone_number ?? ''));
        if ($subtext === '' && !empty($creator?->email)) {
            $subtext = (string) $creator->email;
        }

        $items = $order->relationLoaded('items') ? $order->items : collect();
        $subtotalBeforeDiscount = (float) ($invoice?->subtotal_before_discount ?? $invoice?->subtotal ?? $order->subtotal ?? 0);
        $itemDiscountAmount = (float) ($invoice?->item_discount_amount ?? 0);
        $overallDiscountAmount = (float) ($invoice?->overall_discount_amount ?? max((float) ($invoice?->discount_amount ?? 0) - $itemDiscountAmount, 0));
        $discountAmount = (float) ($invoice?->discount_amount ?? ($itemDiscountAmount + $overallDiscountAmount));
        $taxAmount = (float) ($invoice?->tax_amount ?? $order->tax_amount ?? 0);
        $grandTotal = (float) ($invoice?->grand_total ?? $order->grand_total ?? max($subtotalBeforeDiscount + $taxAmount, 0));
        $paidAmount = (float) ($invoice?->paid_amount ?? $order->paid_amount ?? 0);
        $paidAt = $paymentSession?->paid_at ?? $invoice?->updated_at ?? $order->updated_at;

        return [
            'order_no' => (string) ($order->order_number ?? $invoice?->invoice_number ?? 'N/A'),
            'table' => $tableNumber !== '' ? $tableNumber : '—',
            'customer' => $customerName !== '' ? $customerName : 'Guest',
            'subtext' => $subtext,
            'source' => $this->resolveSourceLabel($order),
            'items' => $items->count(),
            'amount' => $this->formatMoney($grandTotal, $currencySymbol),
            'status' => $this->resolveOrderStatusLabel($orderStatus),
            'statusClass' => $this->resolveStatusClass($orderStatus),
            'paid' => $this->resolvePaymentLabel($orderStatus, $paymentStatus, $paidAmount, $grandTotal),
            'paidClass' => $this->resolvePaymentClass($orderStatus, $paymentStatus, $paidAmount, $grandTotal),
            'time' => $this->formatDateTime($order->ordered_at ?? $order->created_at),
            'detail' => [
                'order_no' => (string) ($order->order_number ?? $invoice?->invoice_number ?? 'N/A'),
                'date' => $this->formatDate($order->ordered_at ?? $order->created_at),
                'time' => $this->formatTime($order->ordered_at ?? $order->created_at),
                'type' => $this->resolveSourceLabel($order),
                'table' => $tableNumber !== '' ? $tableNumber : '—',
                'waiter' => trim((string) ($creator?->name ?? '')) !== '' ? (string) $creator->name : 'Guest',
                'subtotal' => $this->formatMoney($subtotalBeforeDiscount, $currencySymbol),
                'discount' => $this->formatMoney($discountAmount, $currencySymbol),
                'service' => $this->formatMoney(0, $currencySymbol),
                'tax' => $this->formatMoney($taxAmount, $currencySymbol),
                'total' => $this->formatMoney($grandTotal, $currencySymbol),
                'payment_method' => $this->resolvePaymentMethodLabel($invoice?->payment_method ?? '', $paymentSession?->gateway_name ?? ''),
                'payment_status_label' => $this->resolvePaymentLabel($orderStatus, $paymentStatus, $paidAmount, $grandTotal),
                'payment_status_class' => $this->resolvePaymentClass($orderStatus, $paymentStatus, $paidAmount, $grandTotal),
                'amount_paid' => $this->formatMoney($paidAmount, $currencySymbol),
                'paid_at' => $this->formatDateTime($paidAt),
                'transaction_id' => trim((string) ($paymentSession?->provider_reference ?? $invoice?->invoice_number ?? $order->order_number ?? '—')),
                'note' => trim((string) ($invoice?->notes_snapshot ?? $order->notes ?? '')),
                'timeline' => $this->buildTimeline($order, $invoice, $paymentSession),
                'items' => $items->map(function ($item) use ($currencySymbol) {
                    return [
                        'qty' => (int) ($item->quantity ?? 0),
                        'name' => (string) ($item->item_name ?? ''),
                        'price' => $this->formatMoney((float) ($item->total ?? ((float) ($item->price ?? 0) * (int) ($item->quantity ?? 0))), $currencySymbol),
                    ];
                })->values()->all(),
            ],
        ];
    }

    private function resolveSourceLabel(Order $order): string
    {
        $source = strtolower(trim((string) ($order->source ?? '')));
        $orderType = strtolower(trim((string) ($order->order_type ?? '')));

        return match ($source) {
            'web' => 'Online',
            'qr', 'waiter', 'pos' => $orderType === 'takeaway' ? 'Take Away' : 'Dine In',
            default => match ($orderType) {
                'takeaway' => 'Take Away',
                'direct' => 'Online',
                default => 'Dine In',
            },
        };
    }

    private function resolveOrderStatusLabel(string $status): string
    {
        return match ($status) {
            'running' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($status ?: 'Pending'),
        };
    }

    private function resolveStatusClass(string $status): string
    {
        return match ($status) {
            'completed' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400',
            'running' => 'border-amber-500/20 bg-amber-500/10 text-amber-400',
            'cancelled' => 'border-rose-500/20 bg-rose-500/10 text-rose-400',
            default => 'border-gray-500/20 bg-gray-500/10 text-gray-300',
        };
    }

    private function resolvePaymentLabel(string $orderStatus, string $paymentStatus, float $paidAmount, float $grandTotal): string
    {
        if ($orderStatus === 'cancelled' || $paymentStatus === 'cancelled') {
            return 'Refunded';
        }

        return match ($paymentStatus) {
            'paid' => 'Paid',
            'partially_paid' => 'Partially Paid',
            'unpaid', 'pending' => 'Pending',
            default => $paidAmount >= $grandTotal && $grandTotal > 0 ? 'Paid' : ucfirst($paymentStatus ?: 'Pending'),
        };
    }

    private function resolvePaymentClass(string $orderStatus, string $paymentStatus, float $paidAmount, float $grandTotal): string
    {
        if ($orderStatus === 'cancelled' || $paymentStatus === 'cancelled') {
            return 'border-gray-500/20 bg-gray-500/10 text-gray-300';
        }

        return match ($paymentStatus) {
            'paid' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400',
            'partially_paid' => 'border-sky-500/20 bg-sky-500/10 text-sky-400',
            'unpaid', 'pending' => 'border-amber-500/20 bg-amber-500/10 text-amber-400',
            default => $paidAmount >= $grandTotal && $grandTotal > 0
                ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400'
                : 'border-gray-500/20 bg-gray-500/10 text-gray-300',
        };
    }

    private function resolvePaymentMethodLabel(string $paymentMethod, string $gatewayName): string
    {
        $paymentMethod = strtolower(trim($paymentMethod));
        $gatewayName = trim($gatewayName);

        return match ($paymentMethod) {
            'cash' => 'Cash',
            'card' => 'Card',
            'fonepay_dynamic', 'static_qr' => 'Fonepay',
            'nepal_pay' => 'Nepal Pay',
            'bank_transfer' => 'Bank Transfer',
            default => $gatewayName !== '' ? $gatewayName : '—',
        };
    }

    private function buildTimeline(Order $order, $invoice, $paymentSession): array
    {
        $placedAt = $order->ordered_at ?? $order->created_at;
        $items = $order->relationLoaded('items') ? $order->items : collect();
        $events = [];

        $this->appendTimelineEvent($events, 'Order Placed', $placedAt, 10);

        // Keep confirmation stable instead of letting later updates rewrite it.
        $this->appendTimelineEvent($events, 'Order Confirmed', $placedAt, 20);

        $preparationStartedAt = $this->firstItemTimestamp($items, 'started_at')
            ?? (
                in_array((string) ($order->kitchen_status ?? ''), ['preparing', 'served'], true)
                    ? ($order->updated_at ?? $placedAt)
                    : null
            );

        if ($preparationStartedAt) {
            $this->appendTimelineEvent($events, 'Preparation Started', $preparationStartedAt, 30);
        }

        $servedAt = $this->firstItemTimestamp($items, 'served_at')
            ?? $order->served_at
            ?? ((string) ($order->status ?? '') === 'completed' ? ($order->updated_at ?? $placedAt) : null);

        if ($servedAt) {
            $this->appendTimelineEvent($events, 'Order Served', $servedAt, 40);
        }

        $paidAt = $paymentSession?->paid_at ?? $invoice?->updated_at ?? null;
        if ($paidAt) {
            $this->appendTimelineEvent($events, 'Payment Completed', $paidAt, 50);
        }

        if ((string) ($order->status ?? '') === 'cancelled') {
            $cancelledAt = $this->firstItemTimestamp($items, 'rejected_at')
                ?? $order->updated_at
                ?? $placedAt;

            $this->appendTimelineEvent($events, 'Cancelled', $cancelledAt, 60);
        }

        usort($events, function (array $left, array $right): int {
            $leftTime = $left['timestamp']?->timestamp ?? 0;
            $rightTime = $right['timestamp']?->timestamp ?? 0;

            if ($leftTime === $rightTime) {
                return ($left['sequence'] ?? 0) <=> ($right['sequence'] ?? 0);
            }

            return $leftTime <=> $rightTime;
        });

        return array_values(array_map(function (array $event): array {
            return [
                'label' => $event['label'],
                'time' => $this->formatTime($event['timestamp']),
            ];
        }, $events));
    }

    private function appendTimelineEvent(array &$events, string $label, $timestamp, int $sequence): void
    {
        if (!$timestamp) {
            return;
        }

        $events[] = [
            'label' => $label,
            'timestamp' => $timestamp,
            'sequence' => $sequence,
        ];
    }

    private function firstItemTimestamp($items, string $field)
    {
        return collect($items)
            ->map(fn ($item) => data_get($item, $field))
            ->filter()
            ->sortBy(fn ($value) => $value?->timestamp ?? 0)
            ->first();
    }

    private function formatMoney(float $amount, string $currencySymbol): string
    {
        $symbol = trim($currencySymbol);
        $formattedAmount = number_format($amount, 2, '.', ',');

        if ($symbol === '') {
            return $formattedAmount;
        }

        $needsSpace = preg_match('/[A-Za-z]/', $symbol) === 1;

        return $symbol . ($needsSpace ? ' ' : '') . $formattedAmount;
    }

    private function formatDateTime($value): string
    {
        return $value ? optional($value)->format('M d, Y h:i A') : '—';
    }

    private function formatDate($value): string
    {
        return $value ? optional($value)->format('M d, Y') : '—';
    }

    private function formatTime($value): string
    {
        return $value ? optional($value)->format('h:i A') : '—';
    }
}
