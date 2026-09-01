<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order History</title>
    <style>
        @page {
            margin: 12mm 10mm 12mm 10mm;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 10px;
            background: #ffffff;
        }

        .sheet {
            width: 100%;
        }

        .header {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f97316;
        }

        .eyebrow {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: #f97316;
            margin: 0 0 4px;
        }

        .title {
            margin: 0;
            font-size: 20px;
            line-height: 1.1;
            font-weight: 900;
            color: #111827;
        }

        .subtitle {
            margin: 4px 0 0;
            font-size: 10px;
            color: #64748b;
        }

        .meta {
            margin-top: 10px;
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 2px 8px 2px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 90px;
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            white-space: nowrap;
        }

        .meta-value {
            font-size: 9px;
            color: #111827;
        }

        .summary {
            margin: 10px 0 12px;
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            width: 25%;
            padding: 6px 8px;
            vertical-align: top;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .summary-label {
            display: block;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .summary-value {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
        }

        table.orders {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .orders thead th {
            background: #111827;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            text-align: left;
            padding: 7px 6px;
            border: 1px solid #111827;
        }

        .orders tbody td {
            font-size: 9px;
            padding: 7px 6px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .muted {
            display: block;
            margin-top: 2px;
            color: #64748b;
            font-size: 8px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 999px;
            font-size: 8px;
            font-weight: 800;
            white-space: nowrap;
        }

        .badge-completed,
        .badge-paid {
            background: #dcfce7;
            color: #166534;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-partial {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-cancelled,
        .badge-refunded {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-default {
            background: #e5e7eb;
            color: #374151;
        }

        .footnote {
            margin-top: 10px;
            font-size: 8px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>

<body>
    @php
        $filters = $filters ?? [];
        $statusMap = [
            'all' => 'All Status',
            'completed' => 'Completed',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
        ];
        $paymentMap = [
            'all' => 'All Payment Status',
            'paid' => 'Paid',
            'pending' => 'Pending',
            'partially_paid' => 'Partially Paid',
            'refunded' => 'Refunded',
        ];
        $orderTypeMap = [
            'all' => 'All Order Types',
            'dine_in' => 'Dine In',
            'takeaway' => 'Take Away',
            'online' => 'Online',
        ];

        $statusLabel = $statusMap[$filters['status'] ?? 'all'] ?? ucfirst((string) ($filters['status'] ?? 'All'));
        $paymentLabel = $paymentMap[$filters['payment_status'] ?? 'all'] ?? ucfirst(str_replace('_', ' ', (string) ($filters['payment_status'] ?? 'all')));
        $orderTypeLabel = $orderTypeMap[$filters['order_type'] ?? 'all'] ?? ucfirst(str_replace('_', ' ', (string) ($filters['order_type'] ?? 'all')));
        $branchLabel = empty($filters['branch_id']) ? 'All Branches' : ('Branch #' . $filters['branch_id']);

        $dateFrom = !empty($filters['date_from']) ? \Illuminate\Support\Carbon::parse($filters['date_from'])->format('d M Y') : 'Any';
        $dateTo = !empty($filters['date_to']) ? \Illuminate\Support\Carbon::parse($filters['date_to'])->format('d M Y') : 'Any';
    @endphp

    <div class="sheet">
        <div class="header">
            <p class="eyebrow">Order Management</p>
            <h1 class="title">Order History</h1>
            <p class="subtitle">Exported orders with status, payment, and timing details.</p>

            <table class="meta">
                <tr>
                    <td class="meta-label">Generated At</td>
                    <td class="meta-value">{{ $generatedAt ?? now()->format('d M Y, h:i A') }}</td>
                    <td class="meta-label">Date Range</td>
                    <td class="meta-value">{{ $dateFrom }} - {{ $dateTo }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Status</td>
                    <td class="meta-value">{{ $statusLabel }}</td>
                    <td class="meta-label">Branch</td>
                    <td class="meta-value">{{ $branchLabel }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Order Type</td>
                    <td class="meta-value">{{ $orderTypeLabel }}</td>
                    <td class="meta-label">Payment</td>
                    <td class="meta-value">{{ $paymentLabel }}</td>
                </tr>
            </table>
        </div>

        <table class="summary">
            <tr>
                <td>
                    <span class="summary-label">Total Rows</span>
                    <span class="summary-value">{{ count($rows ?? []) }}</span>
                </td>
                <td>
                    <span class="summary-label">Completed</span>
                    <span class="summary-value">{{ collect($rows ?? [])->filter(fn ($row) => strtolower((string) ($row['status'] ?? '')) === 'completed')->count() }}</span>
                </td>
                <td>
                    <span class="summary-label">Pending</span>
                    <span class="summary-value">{{ collect($rows ?? [])->filter(fn ($row) => strtolower((string) ($row['status'] ?? '')) === 'pending')->count() }}</span>
                </td>
                <td>
                    <span class="summary-label">Cancelled</span>
                    <span class="summary-value">{{ collect($rows ?? [])->filter(fn ($row) => strtolower((string) ($row['status'] ?? '')) === 'cancelled')->count() }}</span>
                </td>
            </tr>
        </table>

        <table class="orders">
            <thead>
                <tr>
                    <th style="width: 10%;">Order #</th>
                    <th style="width: 8%;">Table</th>
                    <th style="width: 18%;">Customer / Guest</th>
                    <th style="width: 12%;">Source</th>
                    <th class="center" style="width: 6%;">Items</th>
                    <th class="right" style="width: 10%;">Amount</th>
                    <th style="width: 11%;">Status</th>
                    <th style="width: 10%;">Paid</th>
                    <th style="width: 15%;">Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows ?? [] as $row)
                    @php
                        $statusKey = strtolower(trim((string) ($row['status'] ?? '')));
                        $paidKey = strtolower(trim((string) ($row['paid'] ?? '')));
                        $statusClass = match ($statusKey) {
                            'completed' => 'badge-completed',
                            'pending' => 'badge-pending',
                            'cancelled' => 'badge-cancelled',
                            default => 'badge-default',
                        };
                        $paidClass = match ($paidKey) {
                            'paid' => 'badge-paid',
                            'pending' => 'badge-pending',
                            'partially paid', 'partially_paid', 'partial' => 'badge-partial',
                            'refunded' => 'badge-refunded',
                            default => 'badge-default',
                        };
                    @endphp
                    <tr>
                        <td>{{ $row['order_no'] ?? 'N/A' }}</td>
                        <td>{{ $row['table'] ?? '—' }}</td>
                        <td>
                            <strong>{{ $row['customer'] ?? 'Guest' }}</strong>
                            @if (!empty($row['contact']))
                                <span class="muted">{{ $row['contact'] }}</span>
                            @endif
                        </td>
                        <td>{{ $row['source'] ?? '—' }}</td>
                        <td class="center">{{ $row['items'] ?? 0 }}</td>
                        <td class="right">{{ $row['amount'] ?? '—' }}</td>
                        <td><span class="badge {{ $statusClass }}">{{ $row['status'] ?? '—' }}</span></td>
                        <td><span class="badge {{ $paidClass }}">{{ $row['paid'] ?? '—' }}</span></td>
                        <td>{{ $row['time'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="center" style="padding: 18px 10px; color: #64748b;">
                            No orders found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footnote">Generated from RestoChainERP order history export.</div>
    </div>
</body>

</html>
