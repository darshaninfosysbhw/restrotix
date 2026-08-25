<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estimate Invoice</title>
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

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            padding-bottom: 8px;
            border: none;
            table-layout: fixed;
        }

        .meta-table td {
            vertical-align: top;
            padding: 3px 0;
            line-height: 1.25;
        }

        .label {
            width: 78px;
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
            text-transform: none;
            letter-spacing: 0;
            white-space: nowrap;
            padding-right: 6px;
        }

        .value {
            font-size: 11px;
            font-weight: 400;
            color: #0f172a;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .value.no-wrap {
            white-space: nowrap;
        }

        .section {
            margin-top: 16px;
        }

        .section-title {
            margin-bottom: 8px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: #ea580c;
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
            width: 38%;
        }

        .order-table .qty-col {
            width: 8%;
        }

        .order-table .rate-col {
            width: 16%;
        }

        .order-table .amount-col {
            width: 32%;
        }

        .order-table td.item-cell {
            white-space: nowrap;
        }

        .order-table tr.addon-row td {
            background: #fffbeb;
        }

        .order-table tr.rejected-row td {
            color: #94a3b8;
        }

        .order-table tr.rejected-row td.amount-col {
            color: #64748b;
        }

        .strike {
            text-decoration: line-through;
        }

        .order-table tr.addon-row td.item-cell {
            white-space: normal;
        }

        .rejection-note {
            margin-top: 2px;
            font-size: 8.5px;
            line-height: 1.35;
            color: #dc2626;
            white-space: normal;
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
            padding-top: 10px;
            border-top: 1px dashed #e2e8f0;
            font-size: 14px;
            font-weight: 900;
        }

        .totals-row.total span:last-child {
            color: #ea580c;
        }
    </style>
</head>

<body>
    <div class="sheet">
        <div class="title">Estimate Invoice</div>

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

        <table class="meta-table">
            <tr>
                <td class="label">Invoice No:</td>
                <td class="value no-wrap">{{ $summary['invoice_number'] ?? '##' }}</td>
            </tr>
            <tr>
                <td class="label">Date:</td>
                <td class="value no-wrap">{{ $summary['invoice_date'] }}</td>
            </tr>
            <tr>
                <td class="label">Table No:</td>
                <td class="value no-wrap">{{ $summary['table'] }}</td>
            </tr>
            @if ($showCustomerRow)
                <tr>
                    <td class="label">Customer:</td>
                    <td class="value no-wrap">{{ $customerName }}</td>
                </tr>
            @endif
            @if (!empty($summary['notes_snapshot']))
                <tr>
                    <td class="label">Remarks:</td>
                    <td class="value">{{ $summary['notes_snapshot'] }}</td>
                </tr>
            @endif
        </table>

        <div class="section">
            <div class="section-title">Order Summary</div>
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
                            $addonTotal =
                                (float) ($item['addon_total'] ??
                                    $addons->sum(function ($addon) {
                                        $addonQty = max((int) ($addon['quantity'] ?? 1), 1);
                                        $addonPrice = (float) ($addon['price'] ?? 0);
                                        $masterPrice = max((float) data_get($addon, 'masterAddon.price', 0), 0);
                                        $menuItemAddonPrice = max((float) ($addon['menu_item_addon_price'] ?? 0), 0);
                                        $addonPrice =
                                            $addonPrice > 0 ? $addonPrice : max($masterPrice, $menuItemAddonPrice);
                                        return (float) ($addon['total'] ?? $addonPrice * $addonQty);
                                    }));
                            $discount = (float) ($item['discount'] ?? 0);
                            $addonDiscount =
                                (float) ($item['addon_discount_total'] ??
                                    $addons->sum(function ($addon) {
                                        return (float) ($addon['discount'] ?? ($addon['applied_discount'] ?? 0));
                                    }));
                        @endphp
                        <tr class="{{ $isRejected ? 'rejected-row' : '' }}">
                            <td class="item-cell">
                                <span class="{{ $isRejected ? 'strike' : '' }}">{{ $item['name'] }}</span>
                                @if ($isRejected && !empty($item['rejection_reason']))
                                    <div class="rejection-note">{{ $item['rejection_reason'] }}</div>
                                @endif
                            </td>
                            <td class="center qty-col">{{ (int) ($item['qty'] ?? 0) }}</td>
                            <td class="gap-col"></td>
                            <td class="right rate-col">{{ number_format((float) $item['rate'], 2) }}</td>
                            <td class="right amount-col">{{ number_format(max($baseAmount - $discount, 0), 2) }}</td>
                        </tr>
                        @if ($addons->isNotEmpty())
                            @foreach ($addons as $addon)
                                @php
                                    $addonQty = max((int) ($addon['quantity'] ?? 1), 1);
                                    $addonName = trim(
                                        (string) ($addon['name'] ??
                                            ($addon['addon_name'] ?? data_get($addon, 'masterAddon.name', 'Addon'))),
                                    );
                                    $addonPrice = (float) ($addon['price'] ?? 0);
                                    $masterPrice = max((float) data_get($addon, 'masterAddon.price', 0), 0);
                                    $menuItemAddonPrice = max((float) ($addon['menu_item_addon_price'] ?? 0), 0);
                                    $addonPrice =
                                        $addonPrice > 0 ? $addonPrice : max($masterPrice, $menuItemAddonPrice);
                                    $addonBaseAmount =
                                        (float) ($addon['line_total_before_discount'] ??
                                            ($addon['base_amount'] ?? ($addon['total'] ?? $addonPrice * $addonQty)));
                                    $addonDiscountAmount = max(
                                        (float) ($addon['discount'] ?? ($addon['applied_discount'] ?? 0)),
                                        0,
                                    );
                                    $addonAmount = max($addonBaseAmount - $addonDiscountAmount, 0);
                                @endphp
                                <tr class="addon-row">
                                    <td class="item-cell addon-item-cell">
                                        <span
                                            style="display:inline-flex; align-items:center; gap:4px; padding-left:14px;">
                                            <span style="color:#64748b;">↳</span>
                                            <span>{{ $addonName }}@if ($addonQty > 1)
                                                    x{{ $addonQty }}
                                                @endif
                                            </span>
                                        </span>
                                    </td>
                                    <td class="center qty-col">{{ $addonQty }}</td>
                                    <td class="gap-col"></td>
                                    <td class="right rate-col">{{ number_format($addonPrice, 2) }}</td>
                                    <td class="right amount-col">
                                        <div>{{ number_format($addonAmount, 2) }}</div>
                                        @if ($addonDiscountAmount > 0)
                                            <div style="margin-top:2px; font-size:8px; color:#b45309;">-Rs
                                                {{ number_format($addonDiscountAmount, 2) }}</div>
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
            $taxableAmount =
                (float) ($summary['taxable_amount'] ??
                    max(
                        (float) ($summary['subtotal_after_item_discount'] ?? 0) -
                            (float) ($summary['discount_amount'] ?? 0),
                        0,
                    ));
            $itemDiscountAmount = (float) ($summary['item_discount_amount'] ?? 0);
        @endphp

        <div class="totals">
            <div class="totals-row">
                <span>Total (Particular/QTY)</span>
                <span>{{ count($orderItems) }}/{{ array_sum(array_map(fn($item) => (int) ($item['qty'] ?? 0), $orderItems)) }}</span>
            </div>
            <div class="totals-row">
                <span>Subtotal</span>
                <span>&#8377;{{ number_format((float) ($summary['subtotal'] ?? 0), 2) }}</span>
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
                <span>&#8377;{{ number_format($taxableAmount, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>{{ $summary['tax_label'] ?? 'Tax' }}
                    {{ !empty($summary['tax_rate_percent']) ? '(' . number_format((float) $summary['tax_rate_percent'], 2) . '%)' : '' }}</span>
                <span>&#8377;{{ number_format((float) ($summary['tax_amount'] ?? 0), 2) }}</span>
            </div>
            <div class="totals-row total">
                <span>Total Amount</span>
                <span>&#8377;{{ number_format((float) ($summary['grand_total'] ?? 0), 2) }}</span>
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
            style="margin-top:10px; padding-bottom:8px; border-bottom:1px dashed #e2e8f0; border-top:1px dashed #e2e8f0; font-size:10px; color:#334155;">
            {{ $summary['amount_in_words'] ?? '' }}
        </div>

        <div style="margin-top:8px; font-size:10px; color:#334155;">
            <div style="display:table; width:100%; padding:3px 0;">
                <span style="display:table-cell; width:38%; color:#0f172a; font-size:11px;">Payment Method :</span>
                <span
                    style="display:table-cell; text-align:right; color:#0f172a; font-size:11px;">{{ $summary['payment_method'] ?: $summary['payment_mode'] ?? '-' }}</span>
            </div>
            <div style="display:table; width:100%; padding:3px 0;">
                <span style="display:table-cell; width:38%; color:#0f172a; font-size:11px;">Payment Status :</span>
                <span
                    style="display:table-cell; text-align:right; color:#0f172a; font-size:11px;">{{ $summary['payment_status'] ?? 'PAID' }}</span>
            </div>
            <div style="display:table; width:100%; padding:3px 0;">
                <span style="display:table-cell; width:38%; color:#0f172a; font-size:11px;">KOT No :</span>
                <span
                    style="display:table-cell; text-align:right; color:#0f172a; font-size:11px;">{{ $summary['kot_no'] ?: '-' }}</span>
            </div>
            <div style="display:table; width:100%; padding:3px 0;">
                <span style="display:table-cell; width:38%; color:#0f172a; font-size:11px;">Assign :</span>
                <span
                    style="display:table-cell; text-align:right; color:#0f172a; font-size:11px;">{{ $summary['assign_to'] ?: '-' }}</span>
            </div>
            <div style="display:table; width:100%; padding:3px 0;">
                <span style="display:table-cell; width:38%; color:#0f172a; font-size:11px;">Billed By :</span>
                <span
                    style="display:table-cell; text-align:right; color:#0f172a; font-size:11px;">{{ $summary['billed_by'] ?: '-' }}</span>
            </div>
            <div style="display:table; width:100%; padding:3px 0;">
                <span style="display:table-cell; width:38%; color:#0f172a; font-size:11px;">Service Duration :</span>
                <span
                    style="display:table-cell; text-align:right; color:#0f172a; font-size:11px;">{{ $summary['service_duration'] ?: '-' }}</span>
            </div>
        </div>

        <div style="margin-top:8px; text-align:center; color:#64748b; border-top:1px dashed #e2e8f0;">
            <div style="font-size:10px; line-height:1.4;">
                {{ $summary['note'] ?? 'This is not a Tax Invoice!' }}
            </div>
            <div style="margin-top:2px; font-size:9px; line-height:1.4;">
                Kindly accept the original bill from the counter.
            </div>
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
