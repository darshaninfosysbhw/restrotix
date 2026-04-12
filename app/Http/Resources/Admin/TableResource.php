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

        // 2. Generate Base64 SVG (To stop N+1 Requests)
        // Isse browser alag se image fetch nahi karega, data HTML mein embed ho jayega.
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
                    'items'        => $order->items
                ];
            });
        }

        $computedStatus = (string) $this->status;
        if ($activeOrders->isNotEmpty() && $computedStatus === 'available') {
            $computedStatus = 'occupied';
        }


        return [
            'id'             => (int) $this->id,
            'table_number'   => (string) $this->table_number,
            'display_name'   => 'Table ' . $this->table_number,
            'capacity'       => (int) ($this->capacity ?? 0),
            'status'         => $computedStatus,
            'status_label'   => ucfirst(str_replace('_', ' ', $computedStatus)),

            // UI Visuals
            'status_color'   => $this->getStatusColor($computedStatus),
            'qr_code_inline' => $qrBase64, // 🔥 Ab ye use karo frontend pe

            'qr_token'       => (string) ($this->qr_token ?? ''),
            'menu_url'       => $menuUrl,
            'branch_id'      => (int) $this->branch_id,
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
            'available'      => 'green',
            'reserved'       => 'yellow',
            'booked', 'occupied' => 'red',
            'calling_waiter' => 'blue',
            'out_of_service' => 'gray',
            default          => 'gray'
        };
    }
}
