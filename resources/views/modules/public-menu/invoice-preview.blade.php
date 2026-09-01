@extends('core.layouts.menu-public')

@section('content')
    @php
        $invoiceBranding = $invoiceBranding ?? [];
        $summary = $summary ?? [];
        $orderItems = $orderItems ?? [];
        $amountInWords = $amountInWords ?? '';
        $autoprint = request()->boolean('autoprint');
    @endphp

    <style>
        .invoice-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            .invoice-shell {
                background: #fff !important;
            }
        }
    </style>

    <div class="min-h-screen invoice-shell px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-lg">
            <div class="no-print mb-4 flex items-center justify-between gap-3">
                <a href="{{ route('public.order.status', $qrToken ?? '') }}"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-700 shadow-sm">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    Back
                </a>
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-full bg-orange-500 px-4 py-2 text-xs font-black text-white shadow-sm">
                    <i class="fas fa-print text-[10px]"></i>
                    Print / Save PDF
                </button>
            </div>

            <div class="rounded-[1.9rem] border border-slate-200 bg-white p-4 text-slate-900 shadow-2xl sm:p-5">
                <div class="text-center">
                    <h3 class="text-2xl font-black text-orange-600">
                        {{ $invoiceBranding['restaurant_name'] ?? 'Restaurant' }}
                    </h3>
                    @php
                        $locationLines = array_filter([
                            trim((string) ($invoiceBranding['branch_name'] ?? '')),
                            trim((string) ($invoiceBranding['branch_address'] ?? '')),
                        ]);
                    @endphp
                    @if (!empty($locationLines))
                        <p class="mt-1 text-xs leading-5 text-slate-500">{{ implode(' · ', $locationLines) }}</p>
                    @endif
                    @if (!empty($invoiceBranding['tax_registration'] ?? ''))
                        <p class="text-xs text-slate-500">GSTIN: {{ $invoiceBranding['tax_registration'] }}</p>
                    @endif
                </div>

                <div class="mt-3 border-b border-slate-200"></div>

                <div class="mt-4 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Tax Invoice</p>
                    </div>
                    <div
                        class="rotate-[-10deg] rounded-lg border-4 border-emerald-600 px-3 py-1 text-sm font-black text-emerald-600">
                        PAID
                    </div>
                </div>

                <div class="mt-4 overflow-hidden border-t border-b border-slate-200">
                    <div class="grid grid-cols-2 divide-x divide-slate-200">
                        <div class="px-3 py-1 text-left">
                            <p class="text-xs text-slate-500">Invoice No.</p>
                            <p class="mt-1 text-[11px] font-black text-slate-950">
                                {{ $summary['invoice_number'] ?? ($summary['order_id'] ?? 'N/A') }}</p>
                        </div>
                        <div class="px-3 py-1 text-left">
                            <p class="text-xs text-slate-500">Date</p>
                            <p class="mt-1 text-[11px] font-black text-slate-950">{{ $summary['invoice_date'] ?? '—' }}</p>
                        </div>
                        <div class="col-span-2 border-t border-slate-200"></div>
                        <div class="px-3 py-1 text-left">
                            <p class="text-xs text-slate-500">Table No.</p>
                            <p class="mt-1 text-[11px] font-black text-slate-950">{{ $summary['table'] ?? 'N/A' }}</p>
                        </div>
                        <div class="px-3 py-1 text-left">
                            <p class="text-xs text-slate-500">Order ID</p>
                            <p class="mt-1 text-[11px] font-black text-slate-950 break-all">
                                {{ $summary['order_id'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                    <div
                        class="grid grid-cols-[minmax(0,1.7fr)_minmax(0,.35fr)_minmax(0,.55fr)_minmax(0,.65fr)] border-b border-slate-200 bg-slate-50 px-3 py-2 text-[11px] font-bold text-slate-500">
                        <span class="min-w-0">Item</span>
                        <span class="min-w-0 text-center">Qty</span>
                        <span class="min-w-0 text-right">Rate</span>
                        <span class="min-w-0 text-right">Amount</span>
                    </div>
                    @foreach ($orderItems as $item)
                        <div
                            class="grid grid-cols-[minmax(0,1.7fr)_minmax(0,.35fr)_minmax(0,.55fr)_minmax(0,.65fr)] border-b border-slate-100 px-3 py-2 text-[11px] {{ $item['status'] === 'Rejected' ? 'text-red-500' : 'text-slate-700' }}">
                            <span
                                class="min-w-0 truncate {{ $item['status'] === 'Rejected' ? 'line-through' : '' }}">{{ $item['name'] }}</span>
                            <span class="min-w-0 text-center tabular-nums">{{ $item['qty'] }}</span>
                            <span class="min-w-0 text-right tabular-nums">{{ number_format($item['rate'], 2) }}</span>
                            <span class="min-w-0 text-right tabular-nums">{{ number_format($item['amount'], 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>₹{{ number_format((float) ($summary['subtotal'] ?? 0), 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>VAT (13%)</span>
                        <span>₹{{ number_format((float) ($summary['vat'] ?? 0), 2) }}</span>
                    </div>
                </div>

                <div class="mt-4 border-t border-slate-200 pt-3">
                    <div class="flex items-center justify-between text-base font-black">
                        <span>TOTAL PAYABLE</span>
                        <span
                            class="text-orange-600">₹{{ number_format((float) ($summary['grand_total'] ?? 0), 2) }}</span>
                    </div>
                </div>

                <div class="mt-2 text-[11px] leading-5 text-slate-500">
                    Amount in words: <span class="font-semibold text-slate-700">{{ $amountInWords }}</span>
                </div>

                <div class="mt-3 rounded-2xl bg-emerald-50 px-3 py-3 text-center">
                    <div class="text-sm font-black uppercase tracking-[0.18em] text-emerald-700">Thank You</div>
                    <p class="mt-1 text-[11px] font-medium text-emerald-700">Thank you for your visit! Visit again</p>
                </div>
            </div>
        </div>
    </div>

    @if ($autoprint)
        <script>
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 400);
            });
        </script>
    @endif
@endsection
