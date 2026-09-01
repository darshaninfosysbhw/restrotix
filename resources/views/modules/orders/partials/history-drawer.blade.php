<div id="orderHistoryDrawerOverlay" class="fixed inset-0 z-40 hidden bg-black/50"></div>

<aside id="orderHistoryDrawer" aria-hidden="true"
    class="order-history-drawer fixed top-0 right-0 z-50 flex h-full w-full translate-x-full transform flex-col sm:w-[420px]">
    <section class="flex h-full flex-col overflow-hidden border-l border-gray-800 bg-gray-900 shadow-2xl">
        <div class="flex items-start justify-between border-b border-gray-800 px-5 py-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Order Details</p>
                <h2 class="mt-1 text-lg font-bold text-white">
                    Order <span id="orderDrawerOrderNo">{{ $selectedOrder['order_no'] }}</span>
                </h2>
                <p class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                    <i class="fas fa-calendar-days text-[11px]"></i>
                    <span id="orderDrawerDate">{{ $selectedOrder['date'] }}</span>
                    <span>•</span>
                    <span id="orderDrawerTime">{{ $selectedOrder['time'] }}</span>
                    <span
                        class="rounded-full border border-orange-500/30 bg-orange-500/10 px-2 py-0.5 text-[11px] text-orange-500">
                        <span id="orderDrawerType">{{ $selectedOrder['type'] }}</span>
                    </span>
                </p>
            </div>
            <button type="button" id="orderHistoryDrawerClose"
                class="rounded-lg border border-gray-700 bg-gray-900 p-2 text-gray-400 transition hover:bg-gray-800 hover:text-white"
                aria-label="Close order detail">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto p-5">
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-xl border border-gray-700 bg-gray-800 p-3">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-400">Table</p>
                    <p id="orderDrawerTable" class="mt-1 text-sm font-medium text-white">{{ $selectedOrder['table'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-700 bg-gray-800 p-3">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-400">Waiter</p>
                    <p id="orderDrawerWaiter" class="mt-1 text-sm font-medium text-white">{{ $selectedOrder['waiter'] }}</p>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <h3 class="text-sm font-semibold text-white">Order Timeline</h3>
                <div id="orderDrawerTimeline" class="mt-3 space-y-3">
                    @foreach ($timeline as $step)
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <div class="flex items-center gap-3">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-400">
                                    <i class="fas fa-check text-[10px]"></i>
                                </span>
                                <span class="text-gray-200">{{ $step['label'] }}</span>
                            </div>
                            <span class="text-gray-400">{{ $step['time'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <h3 class="text-sm font-semibold text-white">Items Ordered</h3>
                <div id="orderDrawerItems" class="mt-3 space-y-2">
                    @foreach ($items as $item)
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <div class="flex items-start gap-3">
                                <span class="w-4 text-gray-400">{{ $item['qty'] }}</span>
                                <span class="text-gray-200">{{ $item['name'] }}</span>
                            </div>
                            <span class="text-gray-400">{{ $item['price'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between text-gray-400">
                        <span>Subtotal</span>
                        <span id="orderDrawerSubtotal" class="text-white">{{ $selectedOrder['subtotal'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-400">
                        <span>Discount</span>
                        <span id="orderDrawerDiscount" class="text-orange-400">
                            {{ $selectedOrder['discount'] ?? '—' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-gray-400">
                        <span>Service Charge</span>
                        <span id="orderDrawerService" class="text-white">{{ $selectedOrder['service'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-400">
                        <span>Tax</span>
                        <span id="orderDrawerTax" class="text-white">{{ $selectedOrder['tax'] }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 text-base font-semibold">
                        <span class="text-white">Total Amount</span>
                        <span id="orderDrawerTotal" class="text-orange-500">{{ $selectedOrder['total'] }}</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white">Payment Summary</h3>
                    <span
                        class="rounded-full border px-2.5 py-1 text-[11px] {{ $selectedOrder['payment_status_class'] ?? 'border-amber-500/30 bg-amber-500/10 text-amber-400' }}">
                        {{ $selectedOrder['payment_status_label'] ?? 'Pending' }}
                    </span>
                </div>

                <div class="mt-3 space-y-2 text-sm">
                    <div class="flex items-center justify-between text-gray-400">
                        <span>Payment Method</span>
                        <span id="orderDrawerPaymentMethod" class="text-white">{{ $selectedOrder['payment_method'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-400">
                        <span>Amount Paid</span>
                        <span id="orderDrawerAmountPaid" class="text-white">{{ $selectedOrder['amount_paid'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-400">
                        <span>Paid At</span>
                        <span id="orderDrawerPaidAt" class="text-white">{{ $selectedOrder['paid_at'] }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-gray-400">
                        <span>Transaction ID</span>
                        <span class="flex items-center gap-2 text-white">
                            <span id="orderDrawerTransactionId">{{ $selectedOrder['transaction_id'] }}</span>
                            <i class="fas fa-copy text-xs text-gray-500"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <h3 class="text-sm font-semibold text-white">Notes</h3>
                <p class="mt-2 flex items-start gap-2 text-sm text-gray-400">
                    <i class="fas fa-note-sticky mt-0.5 text-xs text-gray-500"></i>
                    <span id="orderDrawerNote">{{ $selectedOrder['note'] }}</span>
                </p>
            </div>
        </div>
    </section>
</aside>
