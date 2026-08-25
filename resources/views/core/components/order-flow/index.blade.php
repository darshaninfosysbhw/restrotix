@extends('core.layouts.menu-public')

@section('content')
    @include('core.components.order-flow.partials.theme-overrides')
    @php
        $summary = [
            'table' => 'T-01',
            'order_id' => '#1045',
            'subtotal' => 560,
            'tax' => 28,
            'grand_total' => 588,
            'payment_mode' => 'eSewa',
            'transaction_id' => 'Esewa1234567890123',
            'invoice_date' => '26 May 2025',
        ];

        $qrToken = 'demo-qr-token';

        $paymentFlow = [
            'self_payment_enabled' => true,
            'checkout_mode' => 'static_qr',
            'gateway_name' => 'eSewa',
            'gateway_slug' => 'esewa',
            'static_qr_image_url' => 'https://placehold.co/480x480/png?text=QR+Payment',
            'static_qr_label' => 'eSewa QR',
            'can_proceed_online' => true,
            'has_config' => true,
            'mode_label' => 'Static QR',
        ];

        $orderItems = [
            ['name' => 'Hot Coffee', 'qty' => 2, 'rate' => 150, 'amount' => 300, 'status' => 'Preparing'],
            ['name' => 'Masala Chai', 'qty' => 1, 'rate' => 40, 'amount' => 40, 'status' => 'Preparing'],
            ['name' => 'Veg Momos (Full)', 'qty' => 1, 'rate' => 200, 'amount' => 200, 'status' => 'Preparing'],
            ['name' => 'Extra Cheese', 'qty' => 1, 'rate' => 20, 'amount' => 20, 'status' => 'Preparing'],
            ['name' => 'Cold Coffee', 'qty' => 1, 'rate' => 120, 'amount' => 0, 'status' => 'Rejected'],
        ];
    @endphp

    @php
        $invoiceBranding = [
            'restaurant_name' => 'Restaurant',
            'branch_name' => 'Main Branch',
            'branch_address' => '',
            'branch_contact' => '',
            'branch_email' => '',
            'tax_registration' => '',
        ];
    @endphp

    <div class="min-h-screen bg-[#080d19] px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-6 rounded-3xl border border-white/10 bg-white/5 px-5 py-4 backdrop-blur">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-500/15 text-orange-400">
                        <i class="fas fa-layer-group"></i>
                    </span>
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-white sm:text-2xl">Order Flow UI</h1>
                        <p class="mt-1 text-sm text-gray-300">Final UI only. No backend logic is wired yet.</p>
                    </div>
                </div>
            </div>

            <div class="mx-auto max-w-[430px]">
                @include('core.components.order-flow.partials.bill-summary-modal', ['summary' => $summary, 'orderItems' => $orderItems, 'paymentFlow' => $paymentFlow, 'qrToken' => $qrToken])
                @include('core.components.order-flow.partials.payment-options-modal', ['summary' => $summary, 'orderItems' => $orderItems, 'paymentFlow' => $paymentFlow])
                @include('core.components.order-flow.partials.payment-success-modal', ['summary' => $summary])
                @include('core.components.order-flow.partials.tax-invoice-modal', ['summary' => $summary, 'orderItems' => $orderItems, 'invoiceBranding' => $invoiceBranding, 'invoiceData' => ['invoice_date' => $summary['invoice_date']]])
            </div>
        </div>
    </div>
@endsection
