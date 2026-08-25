<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KOT {{ $kotCode ?? '' }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #111111;
            background: #ffffff;
            font-size: 10.5px;
        }

        .sheet {
            width: 100%;
            padding: 2mm 2.2mm 3mm;
        }

        .brand {
            text-align: center;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            line-height: 1;
            margin: 0;
        }

        .kot-no {
            text-align: center;
            font-size: 17px;
            font-weight: 400;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            line-height: 1;
            margin: 0 0 1mm;
        }

        .brand-sub {
            text-align: center;
            margin-top: 0;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .divider {
            border-top: 1px dashed #7f7d7d;
            margin: 3px 0;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 8.8px;
            line-height: 1.2;
        }

        .meta-table td {
            padding: 0.15px 0;
            vertical-align: baseline;
        }

        .meta-label {
            width: 12mm;
            font-weight: 700;
            white-space: nowrap;
            padding-right: 2.5mm;
        }

        .meta-value {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .line-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .qty-col {
            width: 11mm;
            text-align: left;
            white-space: nowrap;
        }

        .gap-col {
            width: 10mm;
            font-size: 0;
            line-height: 0;
        }

        .item-col {
            width: auto;
            text-align: left;
        }

        .head {
            font-family: DejaVu Sans, sans-serif;
            width: 100%;
            font-size: 8.8px;
            font-weight: 800;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            line-height: 1;
            margin: 0;
        }

        .head td {
            padding: 0;
        }

        .head .item-col {
            padding-left: 0;
        }

        .item {
            padding: 2px 0 3px;
            border-bottom: 1px dashed #111111;
            page-break-inside: avoid;
        }

        .item-row {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            font-weight: 700;
            line-height: 1.08;
        }

        .qty {
            line-height: 1.05;
        }

        .name {
            line-height: 1.1;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .subline {
            margin-top: 0;
            padding-left: 21mm;
            font-size: 8.4px;
            line-height: 1.16;
            color: #161616;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .item-table td {
            padding: 0;
            vertical-align: top;
        }

        .item-table .subline-cell {
            padding-top: 0;
        }

        .empty {
            text-align: center;
            font-size: 8.8px;
            color: #444444;
            padding: 6px 0;
        }

        .footer {
            margin-top: 4px;
            text-align: center;
            font-size: 8.8px;
            font-weight: 700;
            letter-spacing: 0.15em;
        }
    </style>
</head>

<body>
    <div class="sheet">
        <div class="kot-no">KOT: {{ $kotCode ?? '' }}</div>
        <div class="brand">{{ $restaurantName ?? 'FOOD PANDA' }}</div>
        @if (!empty($showBranchName) && !empty($branchName))
            <div class="brand-sub">{{ $branchName }}</div>
        @endif

        <div class="divider"></div>

        <table class="meta-table">
            <tr>
                <td class="meta-label">Table:</td>
                <td class="meta-value">{{ $tableNumber ?? '' }}</td>
            </tr>
            <tr>
                <td class="meta-label">Order:</td>
                <td class="meta-value">{{ $orderCode ?? '' }}</td>
            </tr>
            <tr>
                <td class="meta-label">Order By:</td>
                <td class="meta-value">{{ $orderByLabel ?? 'Guest' }}</td>
            </tr>
            <tr>
                <td class="meta-label">Date:</td>
                <td class="meta-value">{{ $receiptDateLabel ?? '' }}</td>
            </tr>
            <tr>
                <td class="meta-label">Time:</td>
                <td class="meta-value">{{ $receiptTimeLabel ?? '' }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="line-table head">
            <tr>
                <td class="qty-col">QTY</td>
                <td class="gap-col">&nbsp;</td>
                <td class="item-col">ITEM</td>
            </tr>
        </table>

        <div class="divider"></div>

        @forelse ($items ?? [] as $item)
            <div class="item">
                <table class="line-table item-table">
                    <tr class="item-row">
                        <td class="qty qty-col">{{ (int) ($item['quantity'] ?? 1) }}</td>
                        <td class="gap-col">&nbsp;</td>
                        <td class="name item-col">{{ $item['name'] ?? 'Item' }}</td>
                    </tr>

                    @if (!empty($item['notes']))
                        <tr>
                            <td class="qty-col"></td>
                            <td class="gap-col">&nbsp;</td>
                            <td class="subline subline-cell item-col">↳ {{ $item['notes'] }}</td>
                        </tr>
                    @endif

                    @foreach ($item['addons'] ?? [] as $addon)
                        <tr>
                            <td class="qty-col"></td>
                            <td class="gap-col">&nbsp;</td>
                            <td class="subline subline-cell item-col">
                                ↳ {{ $addon['name'] ?? 'Addon' }}@if ((int) ($addon['quantity'] ?? 1) > 1)
                                    x{{ (int) ($addon['quantity'] ?? 1) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
            @empty
                <div class="empty">No printable kitchen items</div>
            @endforelse



            <div class="footer">*** THANK YOU ***</div>
        </div>
    </body>

    </html>
