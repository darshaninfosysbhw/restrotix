<section id="billSummaryModal" class="fixed inset-0 z-[220] hidden items-end justify-center bg-black/70 backdrop-blur-xs"
    onclick="if (event.target === this) closeBillSummaryModal();">
    @php
        $paymentFlow = $paymentFlow ?? [];
        $canProceedToPayment = (bool) data_get($paymentFlow, 'can_proceed_online', true);
        $selfPaymentEnabled = (bool) data_get($paymentFlow, 'self_payment_enabled', true);
        $isLightTheme = strtolower((string) ($publicMenuTheme ?? 'dark')) === 'light';
    @endphp
    <div role="dialog" aria-label="Bill Summary"
        class="w-full max-w-lg flex max-h-[90vh] flex-col overflow-y-auto rounded-t-3xl {{ $isLightTheme ? 'bg-white text-slate-900' : 'bg-[#0f172a] text-white' }} shadow-2xl no-scrollbar">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }} px-4 py-4 flex-shrink-0">
            <div></div>
            <div class="text-center">
                <div class="text-sm font-black uppercase tracking-[0.2em] {{ $isLightTheme ? 'text-slate-700' : 'text-white' }}">Bill Summary</div>
            </div>
            <button type="button" onclick="closeBillSummaryModal()"
                class="justify-self-end inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full {{ $isLightTheme ? 'text-slate-400 hover:bg-slate-100 hover:text-slate-700' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition"
                aria-label="Close bill summary modal">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="px-4 pt-3 {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="min-w-0">
                    <div class="text-[11px] uppercase tracking-[0.18em] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">Table No.</div>
                    <div class="mt-1 text-[10px] font-black {{ $isLightTheme ? 'text-slate-900' : 'text-white' }} truncate">{{ $summary['table'] }}</div>
                </div>
                <div class="min-w-0 text-right">
                    <div class="text-[11px] uppercase tracking-[0.18em] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">Order ID</div>
                    <div class="mt-1 text-[10px] font-black {{ $isLightTheme ? 'text-slate-900' : 'text-white' }} truncate">{{ $summary['order_id'] }}</div>
                </div>
            </div>

            <div class="mt-3 border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }}"></div>

            <div class="mt-4">
                <div class="mb-2 text-[11px] font-bold uppercase tracking-[0.18em] text-orange-500">Order Summary</div>
                <div class="overflow-hidden rounded-2xl border {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }}">
                    <div
                        class="grid grid-cols-[minmax(0,1.7fr)_minmax(0,.35fr)_minmax(0,.55fr)_minmax(0,.65fr)] border-b {{ $isLightTheme ? 'border-slate-200 bg-slate-50 text-slate-500' : 'border-white/10 bg-white/5 text-gray-300' }} px-3 py-2 text-[11px] font-bold">
                        <span class="min-w-0">Item</span>
                        <span class="min-w-0 text-center">Qty</span>
                        <span class="min-w-0 text-right">Rate</span>
                        <span class="min-w-0 text-right">Amount</span>
                    </div>
                    @foreach ($orderItems as $item)
                        <div
                            class="grid grid-cols-[minmax(0,1.7fr)_minmax(0,.35fr)_minmax(0,.55fr)_minmax(0,.65fr)] border-b {{ $isLightTheme ? 'border-slate-100' : 'border-white/10' }} px-3 py-2 text-[11px] {{ $item['status'] === 'Rejected' ? 'text-red-500' : ($isLightTheme ? 'text-slate-700' : 'text-gray-200') }}">
                            <span
                                class="min-w-0 truncate {{ $item['status'] === 'Rejected' ? 'line-through' : '' }}">{{ $item['name'] }}</span>
                            <span class="min-w-0 text-center tabular-nums">{{ $item['qty'] }}</span>
                            <span class="min-w-0 text-right tabular-nums">{{ number_format($item['rate'], 2) }}</span>
                            <span class="min-w-0 text-right tabular-nums">{{ number_format($item['amount'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 space-y-2 text-sm">
                <div class="flex items-center justify-between {{ $isLightTheme ? 'text-slate-600' : 'text-gray-300' }}">
                    <span>Subtotal</span>
                    <span class="{{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">₹{{ number_format($summary['subtotal'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between {{ $isLightTheme ? 'text-slate-600' : 'text-gray-300' }}">
                    <span>Tax (5%)</span>
                    <span class="{{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">₹{{ number_format($summary['tax'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between border-t {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }} pt-3 text-base font-black">
                    <span>Total Payable</span>
                    <span class="text-orange-600">₹{{ number_format($summary['grand_total'], 2) }}</span>
                </div>
            </div>

            <div class="mt-3 rounded-2xl {{ $isLightTheme ? 'bg-emerald-50 text-emerald-800' : 'bg-emerald-500/10 text-emerald-100' }} px-3 py-2 text-[11px]">
                Taxes and charges are included as per government norms.
            </div>

            <div class="mt-4 mb-3 grid grid-cols-2 gap-3">
                <a href="{{ route('public.order.status.pdf', $qrToken) }}" target="_blank" rel="noopener"
                    class="inline-flex items-center justify-center rounded-2xl border border-orange-500 px-4 py-3 text-sm font-black text-orange-600">
                    Download PDF
                </a>
                @if ($canProceedToPayment && $selfPaymentEnabled)
                    <button type="button" onclick="openPaymentOptionsModal()"
                        class="inline-flex items-center justify-center rounded-2xl bg-orange-500 px-4 py-3 text-xs font-black text-white">
                        Proceed to Payment
                    </button>
                @else
                    <button type="button" onclick="requestBillFromBillSummary(this)"
                        class="inline-flex items-center justify-center rounded-2xl bg-emerald-500 px-4 py-3 text-xs font-black text-white">
                        Request Bill
                    </button>
                @endif
            </div>
        </div>
    </div>
</section>
