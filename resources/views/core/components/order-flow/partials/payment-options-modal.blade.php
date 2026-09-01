<section id="paymentOptionsModal"
    class="fixed inset-0 z-[230] hidden items-end justify-center bg-black/70 backdrop-blur-xs"
    onclick="if (event.target === this) closePaymentOptionsModal();">
    @php
        $paymentFlow = $paymentFlow ?? [];
        $canProceedOnline = (bool) data_get($paymentFlow, 'can_proceed_online', true);
        $checkoutMode = (string) data_get($paymentFlow, 'checkout_mode', 'dynamic_api');
        $gatewayName = (string) data_get($paymentFlow, 'gateway_name', '');
        $isLightTheme = strtolower((string) ($publicMenuTheme ?? 'dark')) === 'light';
    @endphp
    <div role="dialog" aria-label="Payment Options"
        class="w-full max-w-lg flex max-h-[90vh] flex-col overflow-y-auto rounded-t-3xl {{ $isLightTheme ? 'bg-white text-slate-900' : 'bg-[#0f172a] text-white' }} shadow-2xl no-scrollbar">
        <div
            class="grid grid-cols-[1fr_auto_1fr] items-center border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10 text-white' }} px-4 py-4 flex-shrink-0">
            <button type="button" onclick="backToBillSummaryModal()"
                class="justify-self-start inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full border {{ $isLightTheme ? 'border-slate-200 bg-slate-100 text-slate-700 hover:bg-slate-200' : 'border-white/10 bg-white/5 text-white hover:bg-white/10' }} transition"
                aria-label="Back to bill summary">
                <i class="fas fa-arrow-left text-sm"></i>
            </button>
            <div class="text-center">
                <div class="text-sm font-black uppercase tracking-[0.2em]">Payment Options</div>
            </div>
            <button type="button" onclick="closePaymentOptionsModal()"
                class="justify-self-end inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full border {{ $isLightTheme ? 'border-slate-200 bg-slate-100 text-slate-700 hover:bg-slate-200' : 'border-white/10 bg-white/5 text-white hover:bg-white/10' }} transition"
                aria-label="Close payment options modal">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="p-4 {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">


            <div class="mt-5">
                <h3 class="text-xs font-black uppercase tracking-[0.1em] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-300' }}">Choose Payment Mode</h3>
                <div class="mt-3 space-y-3">
                    <button type="button" onclick="initiateOnlinePayment(this)"
                        class="w-full rounded-xl border border-orange-500/40 bg-orange-500/10 p-4 text-left transition hover:bg-orange-500/15 {{ $canProceedOnline ? '' : 'opacity-50 cursor-not-allowed' }}"
                        @if(!$canProceedOnline) disabled @endif>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 h-6 w-6 rounded-full border-4 border-orange-500 {{ $isLightTheme ? 'bg-white' : 'bg-white' }}"></div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-black {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">Pay Online</p>
                                    <span
                                        class="rounded-full bg-emerald-500 px-2 py-0.5 text-[6px] font-black uppercase text-white">Recommended</span>
                                </div>
                                <p class="mt-1 text-xs {{ $isLightTheme ? 'text-slate-500' : 'text-gray-300' }}">
                                    @if ($canProceedOnline)
                                        {{ $checkoutMode === 'static_qr' ? 'Static QR payment is active for this branch.' : 'Payment gateway API is active for this branch.' }}
                                        @if ($gatewayName !== '')
                                            <span class="block mt-1 text-[10px] {{ $isLightTheme ? 'text-slate-400' : 'text-gray-400' }}">{{ $gatewayName }}</span>
                                        @endif
                                    @else
                                        Online payment is not configured for this branch.
                                    @endif
                                </p>
                            </div>
                            <i class="fas fa-chevron-right {{ $isLightTheme ? 'text-slate-400' : 'text-gray-300' }}"></i>
                        </div>
                    </button>

                    <button type="button" onclick="requestBillFromPaymentOptions(this)"
                        class="w-full rounded-xl border {{ $isLightTheme ? 'border-slate-200 bg-slate-50 hover:bg-slate-100' : 'border-white/10 bg-white/5 hover:bg-white/10' }} p-4 text-left transition">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 h-6 w-6 rounded-full border-4 border-orange-500 bg-transparent"></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-black {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">Pay via Cash</p>
                                <p class="mt-1 text-xs {{ $isLightTheme ? 'text-slate-500' : 'text-gray-300' }}">Request waiter support for cash or terminal settlement</p>
                            </div>
                            <i class="fas fa-chevron-right {{ $isLightTheme ? 'text-slate-400' : 'text-gray-300' }}"></i>
                        </div>
                    </button>
                </div>
            </div>

            <div id="onlinePaymentInlinePanel" class="mt-4 hidden rounded-2xl border {{ $isLightTheme ? 'border-slate-200 bg-slate-50' : 'border-white/10 bg-white/5' }} p-4">
                <div data-online-qr-holder class="rounded-2xl border border-dashed {{ $isLightTheme ? 'border-slate-200 bg-white' : 'border-white/15 bg-black/20' }} p-4"></div>
                <div data-online-inline-message class="mt-3 text-sm {{ $isLightTheme ? 'text-slate-600' : 'text-gray-300' }}"></div>
                <div class="mt-4 flex items-center justify-between gap-3">
                    <button type="button" onclick="backToPaymentOptionsModal()"
                        class="flex-1 rounded-2xl border {{ $isLightTheme ? 'border-slate-200 bg-white text-slate-700' : 'border-white/10 bg-white/5 text-white' }} px-4 py-3 text-xs font-black uppercase tracking-[0.18em]">
                        Back
                    </button>
                    <button type="button" onclick="openPaymentSuccessModal()" class="hidden flex-1 rounded-2xl bg-orange-500 px-4 py-3 text-xs font-black uppercase tracking-[0.18em] text-white"
                        data-online-success-btn>
                        I Have Paid
                    </button>
                </div>
            </div>

            <div class="mt-5 rounded-xl border border-blue-500/20 bg-blue-500/10 p-4 text-[11px] {{ $isLightTheme ? 'text-blue-900' : 'text-blue-100' }}">
                @if ($canProceedOnline)
                    Only one online payment mode is active for this branch at a time.
                @else
                    Cash payment or waiter assistance will be used for this bill.
                @endif
            </div>

            <div class="mt-5 flex items-center justify-center gap-2 text-xs {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">
                <i class="fas fa-lock"></i>
                Secured by Restrotix Secure Payments
            </div>
        </div>
    </div>
</section>
