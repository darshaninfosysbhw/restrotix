<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bill Summary</title>
    <style>
        @page {
            margin: 4mm;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            background: #ffffff;
            font-size: 11px;
        }

        .sheet {
            width: 100%;
        }

        .topbar {
            display: table;
            width: 100%;
            padding-bottom: 10px;
        }

        .topbar-left,
        .topbar-right {
            display: table-cell;
            vertical-align: top;
        }

        .topbar-left {
            width: 100%;
            text-align: center;
        }

        .topbar-right {
            width: 28%;
            text-align: center;
        }

        .restaurant {
            font-size: 18px;
            font-weight: 900;
            color: #f97316;
            line-height: 1.15;
        }

        .branch {
            margin-top: 4px;
            font-size: 11px;
            color: #475569;
            line-height: 1.45;
        }

        .invoice-badge {
            display: inline-block;
            padding: 6px 10px;
            border: 1px solid #16a34a;
            color: #15803d;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            border-radius: 6px;
        }

        .paid-badge {
            display: inline-block;
            margin-top: 32px;
            padding: 7px 12px;
            border: 2px solid #16a34a;
            color: #15803d;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            border-radius: 6px;
            transform: rotate(-8deg);
        }

        .title {
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .meta {
            display: table;
            width: 100%;
            margin-top: 10px;
            padding-bottom: 10px;
            table-layout: fixed;
        }

        .meta-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .meta-col.right {
            text-align: right;
        }


        .meta-row-right {
            display: inline-block;
            text-align: left;
            /* Text ko left-align rakhega taaki red line se start ho */
            width: 220px;
            /* Fixed width for perfect alignment */
        }

        .meta-row-right .label {
            display: inline-block;
            width: 95px;
            /* Fixed width for label titles */
            text-align: left;
        }

        .meta-row-right .value {
            display: inline-block;
        }

        .label {
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
            text-transform: none;
            letter-spacing: 0;
            margin-right: 4px;
        }

        .value {
            font-size: 11px;
            font-weight: 400;
            color: #0f172a;
        }

        .invoice-meta {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            table-layout: fixed;
        }

        .invoice-meta td {
            padding: 2px 0;
            vertical-align: top;
            line-height: 1.25;
        }

        .invoice-meta-label {
            width: 78px;
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
            white-space: nowrap;
            padding-right: 6px;
        }

        .invoice-meta-value {
            font-size: 11px;
            color: #0f172a;
            font-weight: 400;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .invoice-meta-value.no-wrap {
            white-space: nowrap;
        }



        .section {
            margin-top: 10px;
        }

        .section-title {
            margin-bottom: 8px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: #f97316;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 8.5px;
            font-weight: 900;
            text-align: left;
            padding: 4px 3px;
            border-top: 1px dashed #e2e8f0;
            border-bottom: 1px dashed #e2e8f0;
            white-space: nowrap;
        }

        .order-table thead th.rate-col,
        .order-table thead th.amount-col {
            text-align: right;
        }

        .order-table .gap-col {
            width: 6%;
            padding: 0;
            font-size: 0;
        }

        .order-table tbody td {
            font-size: 9px;
            padding: 5px 3px;
            border-bottom: 1px dashed #e2e8f0;
            vertical-align: top;
            line-height: 1.2;
        }

        .order-table {
            table-layout: fixed;
        }

        .order-table .item-col {
            width: 40%;
        }

        .order-table .qty-col {
            width: 8%;
        }

        .order-table .rate-col {
            width: 16%;
        }

        .order-table .amount-col {
            width: 30%;
        }

        .order-table .item-name {
            white-space: nowrap;
        }

        .order-table tr.addon-row td {
            background: #fffbeb;
        }

        .order-table tr.addon-row td.item-cell {
            white-space: normal;
        }

        .addon-name {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding-left: 14px;
        }

        .addon-discount {
            margin-top: 2px;
            font-size: 8px;
            color: #b45309;
        }

        .order-table th:first-child,
        .order-table td:first-child {
            padding-left: 2px;
        }

        .order-table th:last-child,
        .order-table td:last-child {
            padding-right: 2px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .rejected {
            color: #dc2626;
        }

        .strike {
            text-decoration: line-through;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            padding-bottom: 8px;
            border: none;
        }

        .meta-table td {
            vertical-align: top;
            padding: 3px 0;
        }

        .meta-text {
            display: block;
            margin-top: 3px;
            font-size: 9px;
            line-height: 1.35;
            color: #64748b;
        }

        .totals {
            margin-top: 14px;
            font-size: 12px;
        }

        .totals-row {
            display: table;
            width: 100%;
            padding: 5px 0;
        }

        .totals-row span {
            display: table-cell;
        }

        .totals-row span:last-child {
            text-align: right;
        }

        .totals-row.total {
            margin-top: 4px;
            padding-top: 8px;
            border-top: 1px dashed #e2e8f0;
            font-size: 12px;
            font-weight: 600;
        }

        .totals-row.total span:last-child {
            color: #ea580c;
        }
    </style>
</head>

<body>
    <div class="sheet">
        <div class="topbar">
            <div class="topbar-left">
                <div class="restaurant">{{ $summary['restaurant_name'] }}</div>
                <div class="branch">
                    @if (!empty($summary['branch_address']))
                        {{ $summary['branch_address'] }}<br>
                    @endif
                    @if (!empty($summary['branch_contact']))
                        Phone: {{ $summary['branch_contact'] }}
                        @if (!empty($summary['branch_email']))
                            | Email: {{ $summary['branch_email'] }}
                        @endif
                        <br>
                    @endif
                    PAN / VAT No.: {{ $summary['tax_registration'] }}
                </div>
            </div>
        </div>

        <div class="title">Tax Invoice</div>

        @php
            $customerName = trim((string) ($summary['customer_name'] ?? ''));
            $showCustomerRow = $customerName !== '' && !in_array(strtolower($customerName), [
                'cash customer',
                'walk-in customer',
                'walk in customer',
                'walk-in',
                'walk in',
            ], true);
        @endphp

        <table class="invoice-meta">
            <tr>
                <td class="invoice-meta-label">Invoice No:</td>
                <td class="invoice-meta-value">{{ $summary['invoice_number'] ?? $summary['order_id'] }}</td>
            </tr>
            <tr>
                <td class="invoice-meta-label">Date:</td>
                <td class="invoice-meta-value no-wrap">{{ $summary['invoice_date_only'] ?? $summary['invoice_date'] }}
                </td>
            </tr>
            @if (!empty($summary['invoice_time']))
                <tr>
                    <td class="invoice-meta-label">Time:</td>
                    <td class="invoice-meta-value no-wrap">{{ $summary['invoice_time'] }}</td>
                </tr>
            @endif
            <tr>
                <td class="invoice-meta-label">Table No:</td>
                <td class="invoice-meta-value">{{ $summary['table'] }}</td>
            </tr>
            @if ($showCustomerRow)
                <tr>
                    <td class="invoice-meta-label">Customer:</td>
                    <td class="invoice-meta-value">{{ $customerName }}</td>
                </tr>
            @endif
            <tr>
                <td class="invoice-meta-label">Payment:</td>
                <td class="invoice-meta-value no-wrap">
                    {{ $summary['payment_method'] ?: $summary['payment_mode'] ?? '-' }}</td>
            </tr>
            @if (!empty($summary['notes_snapshot']))
                <tr>
                    <td class="invoice-meta-label">Remarks:</td>
                    <td class="invoice-meta-value">{{ $summary['notes_snapshot'] }}</td>
                </tr>
            @endif
        </table>

        <div class="section">
            <table class="order-table">
                <thead>
                    <tr>
                        <th class="item-col">Item</th>
                        <th class="center qty-col">Qty</th>
                        <th class="gap-col"></th>
                        <th class="right rate-col">Rate</th>
                        <th class="right amount-col">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderItems as $item)
                        @php $isRejected = strtolower((string) ($item['status'] ?? '')) === 'rejected'; @endphp
                        @php
                            $addons = collect($item['addons'] ?? [])->filter(function ($addon) {
                                return !empty(trim((string) ($addon['name'] ?? ($addon['addon_name'] ?? ''))));
                            });
                            $baseAmount =
                                (float) ($item['base_amount'] ??
                                    (float) ($item['rate'] ?? 0) * (float) ($item['qty'] ?? 0));
                            $itemDiscount = (float) ($item['discount'] ?? 0);
                            $itemDisplayAmount =
                                (float) ($item['display_amount'] ?? max($baseAmount - $itemDiscount, 0));
                        @endphp
                        <tr class="{{ $isRejected ? 'rejected' : '' }}">
                            <td class="item-cell {{ $isRejected ? 'strike' : '' }}">
                                <span class="item-name">{{ $item['name'] }}</span>
                                @if (!empty($item['notes']))
                                    <span class="meta-text">{{ $item['notes'] }}</span>
                                @endif
                            </td>
                            <td class="center qty-col">{{ $item['qty'] }}</td>
                            <td class="gap-col"></td>
                            <td class="right rate-col">{{ number_format($item['rate'], 2) }}</td>
                            <td class="right amount-col">{{ number_format($itemDisplayAmount, 2) }}</td>
                        </tr>
                        @if ($addons->isNotEmpty())
                            @foreach ($addons as $addon)
                                @php
                                    $addonQty = max((int) ($addon['quantity'] ?? 1), 1);
                                    $addonPrice = max((float) ($addon['price'] ?? 0), 0);
                                    $addonDiscount = max(
                                        (float) ($addon['discount'] ?? ($addon['applied_discount'] ?? 0)),
                                        0,
                                    );
                                    $addonAmount =
                                        (float) ($addon['total'] ?? max($addonPrice * $addonQty - $addonDiscount, 0));
                                    $addonName = trim((string) ($addon['name'] ?? ($addon['addon_name'] ?? 'Addon')));
                                @endphp
                                <tr class="addon-row {{ $isRejected ? 'rejected' : '' }}">
                                    <td class="item-cell {{ $isRejected ? 'strike' : '' }}">
                                        <span class="addon-name">
                                            <span style="color:#64748b;">↳</span>
                                            <span>{{ $addonName }}@if ($addonQty > 1)
                                                    x{{ $addonQty }}
                                                @endif
                                            </span>
                                        </span>
                                    </td>
                                    <td class="center qty-col {{ $isRejected ? 'strike' : '' }}">{{ $addonQty }}
                                    </td>
                                    <td class="gap-col"></td>
                                    <td class="right rate-col {{ $isRejected ? 'strike' : '' }}">
                                        {{ number_format($addonPrice, 2) }}</td>
                                    <td class="right amount-col {{ $isRejected ? 'strike' : '' }}">
                                        <div>{{ number_format($addonAmount, 2) }}</div>
                                        @if ($addonDiscount > 0)
                                            <div class="addon-discount">-Rs {{ number_format($addonDiscount, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        @php
            $itemDiscountAmount = (float) ($summary['item_discount_amount'] ?? 0);
        @endphp

        <div class="totals">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>&#8377;{{ number_format($summary['subtotal'], 2) }}</span>
            </div>
            @if ($itemDiscountAmount > 0)
                <div class="totals-row">
                    <span>Item Discount</span>
                    <span>&#8377;{{ number_format($itemDiscountAmount, 2) }}</span>
                </div>
            @endif
            <div class="totals-row">
                <span>Discount</span>
                <span>&#8377;{{ number_format((float) ($summary['discount_amount'] ?? 0), 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Taxable Amount</span>
                <span>&#8377;{{ number_format((float) ($summary['taxable_amount'] ?? 0), 2) }}</span>
            </div>
            <div class="totals-row">
                <span>{{ $summary['tax_label'] ?? 'Tax' }}
                    ({{ number_format((float) ($summary['tax_rate_percent'] ?? 0), 0) }}%)</span>
                <span>&#8377;{{ number_format((float) ($summary['tax'] ?? 0), 2) }}</span>
            </div>
            <div class="totals-row total">
                <span>Total Amount</span>
                <span>&#8377;{{ number_format($summary['grand_total'], 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Tender Amount</span>
                <span>&#8377;{{ number_format((float) ($summary['tender_amount'] ?? 0), 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Change to Return</span>
                <span>&#8377;{{ number_format((float) ($summary['change_amount'] ?? 0), 2) }}</span>
            </div>
        </div>

        <div
            style="margin-top:10px; padding-bottom:4px; border-bottom: 1px dashed #e2e8f0; border-top: 1px dashed #e2e8f0; font-size:8px; color:#64748b;">
            <strong style="color:#334155;">{{ $summary['amount_in_words'] ?? '' }}</strong>
        </div>


        <div style="margin-top:14px; text-align:center;">
            <div
                style="font-size:12px; font-weight:900; letter-spacing:0.18em; color:#0f172a; text-transform:uppercase;">
                Thank You</div>
            <div style="margin-top:4px; font-size:10px; color:#475569;">Thank you for your visit! Visit again</div>
        </div>
    </div>
</body>

</html>
