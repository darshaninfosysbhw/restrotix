<section id="paymentSuccessModal"
    class="fixed inset-0 z-[240] hidden items-end justify-center bg-black/70 backdrop-blur-xs"
    onclick="if (event.target === this) closePaymentSuccessModal();">
    @php
        $isLightTheme = strtolower((string) ($publicMenuTheme ?? 'dark')) === 'light';
    @endphp
    <div role="dialog" aria-label="Payment Successful"
        class="w-full max-w-lg flex max-h-[90vh] flex-col overflow-y-auto rounded-t-3xl {{ $isLightTheme ? 'bg-white text-slate-900' : 'bg-[#0f172a] text-white' }} shadow-2xl no-scrollbar">
        <div
            class="grid grid-cols-[1fr_auto_1fr] items-center border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10 text-white' }} px-4 py-4 flex-shrink-0">
            <button type="button" onclick="backToPaymentOptionsModal()"
                class="justify-self-start inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full border {{ $isLightTheme ? 'border-slate-200 bg-slate-100 text-slate-700 hover:bg-slate-200' : 'border-white/10 bg-white/5 text-white hover:bg-white/10' }} transition"
                aria-label="Back to payment options">
                <i class="fas fa-arrow-left text-sm"></i>
            </button>
            <div class="text-center">
                <div class="text-sm font-black uppercase tracking-[0.2em]">Payment Successful</div>
            </div>
            <button type="button" onclick="closePaymentSuccessModal()"
                class="justify-self-end inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full border {{ $isLightTheme ? 'border-slate-200 bg-slate-100 text-slate-700 hover:bg-slate-200' : 'border-white/10 bg-white/5 text-white hover:bg-white/10' }} transition"
                aria-label="Close payment success modal">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>

        <div
            class="{{ $isLightTheme ? 'bg-emerald-50 text-emerald-700' : 'bg-gradient-to-br from-emerald-600 via-green-600 to-emerald-700 text-white' }} px-4 py-6 text-center">
            <div
                class="mx-auto flex h-13 w-13 items-center justify-center rounded-full bg-white text-3xl font-black text-emerald-600 shadow-lg">
                <i class="fas fa-check"></i>
            </div>
            <h3 class="mt-4 text-xl font-black">Payment Successful!</h3>
            <p class="mx-auto mt-2 max-w-xs text-sm text-emerald-50">Your payment of
                ₹{{ number_format($summary['grand_total'], 2) }} was completed successfully.</p>
        </div>

        <div class="p-4 {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">
            <div
                class="rounded-xl border {{ $isLightTheme ? 'border-slate-200 bg-slate-50' : 'border-white/10 bg-white/5' }} p-4">
                <h4
                    class="text-sm font-black uppercase tracking-[0.2em] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-300' }}">
                    Payment Details</h4>
                <div class="mt-4 space-y-4 text-xs">
                    <div
                        class="flex items-center justify-between border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }} pb-3">
                        <span class="{{ $isLightTheme ? 'text-slate-500' : 'text-gray-300' }}">Amount Paid</span>
                        <span class="font-bold">₹{{ number_format($summary['grand_total'], 2) }}</span>
                    </div>
                    <div
                        class="flex items-center justify-between border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }} pb-3">
                        <span class="{{ $isLightTheme ? 'text-slate-500' : 'text-gray-300' }}">Payment Mode</span>
                        <span class="font-bold">{{ $summary['payment_mode'] }}</span>
                    </div>
                    <div
                        class="flex items-center justify-between border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }} pb-3">
                        <span class="{{ $isLightTheme ? 'text-slate-500' : 'text-gray-300' }}">Transaction ID</span>
                        <span class="font-bold">{{ $summary['transaction_id'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="{{ $isLightTheme ? 'text-slate-500' : 'text-gray-300' }}">Order ID</span>
                        <span class="font-bold">{{ $summary['order_id'] }}</span>
                    </div>
                </div>
            </div>

            <div
                class="mt-4 rounded-xl border border-emerald-500/25 {{ $isLightTheme ? 'bg-emerald-50 text-emerald-700' : 'bg-emerald-500/10 text-emerald-50' }} p-4">
                <div class="flex items-start gap-3">
                    <div
                        class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/25 text-emerald-100">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <div>
                        <p class="font-bold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }} text-sm">Your order
                            is marked as completed.</p>
                        <p class="mt-1 text-xs {{ $isLightTheme ? 'text-emerald-700' : 'text-emerald-50/90' }}">Thank
                            you! We hope to serve you again.</p>
                    </div>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3">

                <a href="{{ route('public.menu.scan', $qrToken ?? '') }}"
                    class="rounded-2xl border {{ $isLightTheme ? 'border-slate-200 bg-white text-slate-700' : 'border-white/15 bg-white/5 text-white' }} px-4 py-3 text-center text-sm font-black">
                    Back to Menu
                </a>
                <button type="button" onclick="openTaxInvoiceModal()"
                    class="rounded-2xl bg-orange-500 px-4 py-3 text-sm font-black text-white">
                    View Tax Invoice
                </button>
            </div>
        </div>
    </div>
</section>
