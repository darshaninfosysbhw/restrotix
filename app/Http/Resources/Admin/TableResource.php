<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // 1. QR Code URL (For scanning by customers)
        $appUrl = rtrim((string) config('app.url'), '/');
        $menuPath = route('public.menu.scan', ['qr_token' => $this->qr_token], false);
        $menuUrl = $appUrl . $menuPath;
        $qrData = $menuUrl;

        // 2. Generate Base64 SVG
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode(
            QrCode::size(80)
                ->margin(1)
                ->generate($qrData)
        );

        $activeOrders = collect();
        if ($this->relationLoaded('orders')) {
            $activeOrders = $this->orders->map(function ($order) {
                return [
                    'id'           => $order->id,
                    'order_number' => $order->order_number,
                    'status'       => $order->status,

                    // 🌟 FIX: Items transform kar ke nested orderItemAddons pass kiye
                    'items'        => $order->items->map(function ($item) {
                        return [
                            'id'                => $item->id,
                            'item_name'         => $item->item_name,
                            'quantity'          => (int) $item->quantity,
                            'price'             => (float) $item->price,
                            'total'             => (float) $item->total,
                            'status'            => $item->status,
                            'notes'             => $item->notes,

                            // 🔥 Explicitly pass Addons Array to Frontend JS Drawer
                            'order_item_addons' => $item->orderItemAddons->map(function ($addon) {
                                $addonPrice = (float) ($addon->price ?? 0);
                                if ($addonPrice <= 0) {
                                    $addonPrice = max((float) ($addon->masterAddon?->price ?? 0), 0);
                                }
                                return [
                                    'id'         => $addon->id,
                                    'addon_name' => $addon->addon_name ?? $addon->masterAddon?->name ?? '',
                                    'price'      => $addonPrice,
                                    'quantity'   => (int) $addon->quantity,
                                    'applied_discount' => (float) ($addon->applied_discount ?? 0),
                                ];
                            })->values()->all(),
                        ];
                    })->values()->all()
                ];
            });
        }

        $computedStatus = (string) $this->status;
        if ($activeOrders->isNotEmpty() && $computedStatus === 'available') {
            $computedStatus = 'occupied';
        }

        $branch = $this->relationLoaded('branch') ? $this->branch : null;
        $branchTaxSetting = strtolower((string) ($branch?->tax_setting ?? 'exclusive'));
        $branchTaxSetting = $branchTaxSetting === 'inclusive' ? 'inclusive' : 'exclusive';
        $branchTaxRatePercent = max((float) ($branch?->tax_rate ?? 0), 0);
        $branchTaxLabelName = $branchTaxSetting === 'inclusive' || (float) $branchTaxRatePercent === 13.0
            ? 'VAT'
            : 'Tax';

        return [
            'id'             => (int) $this->id,
            'table_number'   => (string) $this->table_number,
            'display_name'   => 'Table ' . $this->table_number,
            'capacity'       => (int) ($this->capacity ?? 0),
            'status'         => $computedStatus,
            'status_label'   => ucfirst(str_replace('_', ' ', $computedStatus)),

            // UI Visuals
            'status_color'   => $this->getStatusColor($computedStatus),
            'qr_code_inline' => $qrBase64,

            'qr_token'       => (string) ($this->qr_token ?? ''),
            'menu_url'       => $menuUrl,
            'branch_id'      => (int) $this->branch_id,
            'branch_tax_setting' => $branchTaxSetting,
            'branch_tax_rate' => $branchTaxRatePercent,
            'branch_tax_label' => $branchTaxLabelName,
            'created_at'     => optional($this->created_at)->format('Y-m-d'),

            'active_orders'  => $this->whenLoaded('orders', fn() => $activeOrders),
        ];
    }

    /**
     * Helper to keep toArray clean
     */
    private function getStatusColor($status): string
    {
        return match ($status) {
            'available'          => 'green',
            'reserved'           => 'yellow',
            'booked', 'occupied' => 'red',
            'calling_waiter'     => 'blue',
            'request_bill'       => 'orange',
            'out_of_service'     => 'gray',
            default              => 'gray'
        };
    }
}
