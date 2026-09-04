<div class="relative flex h-full min-h-0 flex-col lg:overflow-hidden">
    <div
        class="billing-pos-no-scrollbar flex min-h-0 flex-1 flex-col space-y-4 overflow-y-auto py-4 pr-4 pl-0 pb-6 sm:py-5 sm:pr-5 sm:pl-0 lg:py-6 lg:pr-6 lg:pl-0">
        <section class="w-full rounded-lg border border-slate-400 p-4 sm:p-5 xl:w-full">
            <div class="text-center">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-950">Estimate Invoice</p>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-700">Invoice No: <span id="billingInvoiceNo" class="font-semibold">##</span></p>
                    <p class="mt-2 text-slate-700">Dine In: <span id="billingDineInTable" class="font-semibold">Table
                            N/A</span></p>
                </div>

                <div class="text-right">
                    <p class="text-slate-700">Date: <span id="billingInvoiceDate" class="font-semibold">Aug 11,
                            2026</span></p>
                </div>
            </div>

            <div class="mt-2 border-t border-dashed border-slate-300 pt-2 text-xs">
                <div class="font-medium text-slate-700">Customer: <span id="billingCustomerName">Cash Customer</span>
                </div>

                <div
                    class="mt-3 grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 border-b border-dashed border-slate-200 pb-2 text-xs font-bold text-slate-900">
                    <div>Particular</div>
                    <div>Rate</div>
                    <div class="text-center">QTY</div>
                    <div class="text-right">Amount</div>
                </div>

                <div id="billingInvoiceItems" class="divide-y divide-dashed divide-slate-200">
                    @forelse ($items as $item)
                        @php
                            $isRejected =
                                (bool) ($item['is_rejected'] ?? false) ||
                                strtolower((string) ($item['status'] ?? '')) === 'rejected' ||
                                strtolower((string) ($item['status'] ?? '')) === 'cancelled';
                            $rejectionReason = trim((string) ($item['rejection_reason'] ?? ''));
                            $itemRate = $isRejected
                                ? (float) ($item['original_rate'] ?? ($item['rate'] ?? 0))
                                : (float) ($item['rate'] ?? 0);
                            $itemAddons = collect(
                                $item['addons'] ?? ($item['order_item_addons'] ?? ($item['orderItemAddons'] ?? [])),
                            )->filter(function ($addon) {
                                return !empty(trim((string) ($addon['name'] ?? ($addon['addon_name'] ?? ''))));
                            });
                            $addonTotal = $itemAddons->sum(function ($addon) {
                                $addonQty = max((int) ($addon['quantity'] ?? 1), 1);
                                $addonPrice = (float) ($addon['price'] ?? 0);
                                $masterPrice = max((float) data_get($addon, 'masterAddon.price', 0), 0);
                                $addonPrice = $addonPrice > 0 ? $addonPrice : $masterPrice;
                                return (float) ($addon['total'] ?? $addonPrice * $addonQty);
                            });
                            $itemBaseAmount = $isRejected
                                ? 0
                                : (float) ($item['base_amount'] ?? $itemRate * (float) $item['qty']);
                            $itemLineAmount = $itemBaseAmount + $addonTotal;
                            $itemDiscount = $isRejected ? 0 : (float) ($item['discount'] ?? 0);
                            $itemTotal =
                                (float) ($item['amount'] ??
                                    ($item['total'] ?? max($itemLineAmount - $itemDiscount, 0)));
                        @endphp
                        <div class="space-y-1.5 py-1.5 text-xs {{ $isRejected ? 'text-slate-400' : '' }}"
                            data-item-id="{{ $item['id'] ?? '' }}" data-item-status="{{ $item['status'] ?? '' }}"
                            data-item-is-rejected="{{ $isRejected ? '1' : '0' }}"
                            data-item-rejection-reason="{{ $rejectionReason }}">
                            <div class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3">
                                <div class="font-medium {{ $isRejected ? 'text-slate-500' : 'text-slate-700' }}">
                                    <div>{{ $item['name'] }}</div>
                                    @if ($isRejected && $rejectionReason !== '')
                                        <div class="mt-1 flex items-start gap-1.5 text-[11px] leading-4 text-rose-500">
                                            <i class="fas fa-circle-xmark mt-0.5 text-[10px] text-rose-500"
                                                aria-hidden="true"></i>
                                            <span class="break-words">{{ $rejectionReason }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-slate-700">Rs {{ number_format($itemRate, 0) }}</div>
                                <div class="text-center text-slate-700">{{ $item['qty'] }}</div>
                                <div class="text-right text-slate-700">Rs
                                    {{ number_format(max($itemBaseAmount - $itemDiscount, 0), 0) }}</div>
                            </div>
                            @if ($itemAddons->isNotEmpty())
                                <div class="space-y-1 text-[11px] leading-4">
                                    @foreach ($itemAddons as $addon)
                                        @php
                                            $addonQty = max((int) ($addon['quantity'] ?? 1), 1);
                                            $addonPrice = max((float) ($addon['price'] ?? 0), 0);
                                            $addonName = trim(
                                                (string) ($addon['name'] ??
                                                    ($addon['addon_name'] ??
                                                        data_get($addon, 'masterAddon.name', 'Addon'))),
                                            );
                                            $addonName = preg_replace('/^[↳↲]+\s*/u', '', $addonName) ?? $addonName;
                                            $addonAmount = (float) ($addon['total'] ?? $addonPrice * $addonQty);
                                        @endphp
                                        <div
                                            data-addon-name="{{ $addonName }}"
                                            class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 rounded-md bg-yellow-50 px-2 py-1.5 text-xs text-slate-700 {{ $isRejected ? 'opacity-70 text-slate-400' : '' }}">
                                            <div
                                                class="flex items-center gap-2 pl-4 font-medium {{ $isRejected ? 'text-slate-500' : 'text-slate-950' }}">
                                                <span class="shrink-0 text-slate-600">↳</span>
                                                <span class="truncate">{{ $addonName }}@if ($addonQty > 1)
                                                        x{{ $addonQty }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="text-slate-700">Rs {{ number_format($addonPrice, 0) }}</div>
                                            <div class="text-center text-slate-700">{{ $addonQty }}</div>
                                            <div class="text-right font-medium text-slate-700">Rs
                                                {{ number_format($addonAmount, 0) }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @empty
                            <div class="py-3 text-center text-sm text-slate-500">No running order found.</div>
                        @endforelse
                    </div>

                    <div class="mt-3 border-t border-dashed border-slate-300">
                        <div
                            class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 border-b border-dashed border-slate-300 py-1 text-xs font-semibold text-slate-700">
                            <div class="text-[11px]">Total (Particular/QTY)</div>
                            <div></div>
                            <div id="billingRightItemCount" class="text-center">0/0</div>
                            <div id="billingRightItemTotal" class="text-right">Rs 0.00</div>
                        </div>
                        <div
                            class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 border-b border-dashed border-slate-300 py-1 text-xs font-semibold text-slate-700">
                            <div class="text-[11px]">Sub Total</div>
                            <div></div>
                            <div></div>
                            <div id="billingRightSubTotal" class="text-right">Rs 0.00</div>
                        </div>
                        <div
                            class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 border-b border-dashed border-slate-300 py-1.5 text-xs text-slate-700">
                            <div class="text-[11px]">Discount</div>
                            <div></div>
                            <div></div>
                            <div id="billingRightDiscount" class="text-right">Rs 0.00</div>
                        </div>
                        <div id="billingRightItemDiscountRow"
                            class="hidden grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 border-b border-dashed border-slate-300 py-1 text-xs text-slate-700"
                            aria-hidden="true">
                            <div class="text-[11px]">Item Discount</div>
                            <div></div>
                            <div></div>
                            <div id="billingRightLoyaltyDiscount" class="text-right">Rs 0.00</div>
                        </div>
                        <div
                            class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 border-b border-dashed border-slate-300 py-1.5 text-xs text-slate-700">
                            <div id="billingRightTaxLabel" class="text-[11px]">Tax</div>
                            <div></div>
                            <div></div>
                            <div id="billingRightManualDiscount" class="text-right">Rs 0.00</div>
                        </div>
                        <div
                            class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 border-b border-dashed border-slate-300 py-1.5 text-xs font-semibold text-slate-700">
                            <div class="text-[11px]">Total Amount</div>
                            <div></div>
                            <div></div>
                            <div id="billingRightTotalAmount" class="text-right">Rs 0.00</div>
                        </div>
                        <div
                            class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 border-b border-dashed border-slate-300 py-1.5 text-xs text-slate-700">
                            <div class="text-[11px]">Tender Amount</div>
                            <div></div>
                            <div></div>
                            <div id="billingRightTenderAmount" class="text-right">Rs 0.00</div>
                        </div>
                        <div
                            class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 border-b border-dashed border-slate-300 py-1.5 text-xs text-slate-700">
                            <div class="text-[11px]">Change to Return</div>
                            <div></div>
                            <div></div>
                            <div id="billingRightChangeAmount" class="text-right text-rose-600">-Rs 0.00</div>
                        </div>
                    </div>

                    <div id="billingAmountInWords" class="pt-2 text-xs text-slate-700">Zero Nepalese Rupees Only</div>
                </div>

                <div class="mt-2 space-y-2 border-t border-dashed border-slate-200 pt-3 text-xs">
                    <p><span class="font-normal text-slate-500">Payment Mode:</span> <span id="billingPaymentMode"
                            class="font-normal text-slate-900">Unpaid</span> <span id="billingPaymentModeAmount"
                            class="text-slate-500">(Rs 0.00)</span></p>
                    <p id="billingInvoiceRemarksRow" class="hidden">
                        <span class="font-normal text-slate-500">Remarks:</span>
                        <span id="billingInvoiceRemarksText"
                            class="font-normal text-slate-900 whitespace-pre-wrap break-words"></span>
                    </p>
                    <p><span class="font-normal text-slate-500">KOT No:</span> <span id="billingKotNo"
                            class="font-normal text-slate-900">10 (by Carla Estrada)</span></p>
                    <p><span class="font-normal text-slate-500">Assign:</span> <span id="billingAssignTo"
                            class="font-normal text-slate-900">N/A (Rs 0.00)</span></p>
                    <p><span class="font-normal text-slate-500">Billed By:</span> <span id="billingBilledBy"
                            class="font-normal text-slate-900">{{ auth()->user()->name ?? 'N/A' }}</span></p>
                    <p><span class="font-normal text-slate-500">Service Duration:</span> <span id="billingServiceDuration"
                            class="font-normal text-slate-900">N/A</span></p>
                </div>

                <div class="border-t border-dashed border-slate-300 pt-2">
                    <p class="text-lg font-bold text-slate-600">This is not a Tax Invoice!</p>
                    <p class="text-sm text-slate-600">Kindly accept the original bill from the counter.</p>
                </div>

                <div class="mt-3 text-center">
                    <p class="text-xs font-extrabold text-slate-900">Thank You</p>
                    <p class="mt-1 text-xs text-slate-600">Thank you for your visit! Visit again</p>
                </div>
            </section>
        </div>

        <div class="shrink-0 px-4 py-4 sm:px-2 lg:px-2">
            <div class="flex w-full flex-col gap-3">
                <div class="flex items-end justify-between gap-3">
                    <p class="text-[12px] font-semibold text-slate-700">Net sales amount</p>
                    <p id="billingNetSalesAmount" class="text-sm font-extrabold leading-none text-slate-950">Rs 0.00</p>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <button type="button" id="billingConfirmPrintBtn"
                        class="h-8 rounded-sm border border-slate-200 bg-slate-50 px-1 text-[13px] font-semibold text-slate-900 transition hover:bg-slate-100 cursor-pointer"
                        data-billing-action="print">
                        Confirm &amp; Print
                    </button>
                    <button type="button" id="billingConfirmCheckoutBtn"
                        class="h-8 rounded-sm border border-orange-500 bg-orange-500 px-1 text-[13px] font-semibold text-white transition hover:bg-orange-600 cursor-pointer"
                        data-billing-action="checkout">
                        Confirm Checkout
                    </button>
                </div>
                <button type="button" id="billingHoldBtn"
                    class="h-8 w-full rounded-sm border border-amber-500 bg-amber-50 px-1 text-[13px] font-semibold text-amber-600 transition hover:bg-amber-100 cursor-pointer"
                    data-billing-action="hold">
                    Hold Bill
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const invoiceBody = document.getElementById('billingInvoiceItems');
            const itemCountEl = document.getElementById('billingRightItemCount');
            const itemTotalEl = document.getElementById('billingRightItemTotal');
            const itemDiscountEl = document.getElementById('billingRightLoyaltyDiscount');
            const itemDiscountRow = document.getElementById('billingRightItemDiscountRow');
            const subTotalEl = document.getElementById('billingRightSubTotal');
            const overallDiscountEl = document.getElementById('billingRightDiscount');
            const taxEl = document.getElementById('billingRightManualDiscount');
            const totalAmountEl = document.getElementById('billingRightTotalAmount');
            const tenderAmountEl = document.getElementById('billingRightTenderAmount');
            const changeAmountEl = document.getElementById('billingRightChangeAmount');
            const netSalesAmountEl = document.getElementById('billingNetSalesAmount');
            const amountInWordsEl = document.getElementById('billingAmountInWords');
            const taxLabelEl = document.getElementById('billingRightTaxLabel');
            const remarksRow = document.getElementById('billingInvoiceRemarksRow');
            const remarksText = document.getElementById('billingInvoiceRemarksText');
            const holdBillingBtn = document.getElementById('billingHoldBtn');
            const confirmPrintBtn = document.getElementById('billingConfirmPrintBtn');
            const confirmCheckoutBtn = document.getElementById('billingConfirmCheckoutBtn');
            const estimateDownloadBtn = document.getElementById('billingDownloadEstimateBtn');
            const estimatePrintBtn = document.getElementById('billingPrintEstimateBtn');
            if (!invoiceBody || !itemCountEl || !itemTotalEl || !itemDiscountEl || !subTotalEl || !
                overallDiscountEl || !taxEl || !totalAmountEl || !tenderAmountEl || !changeAmountEl || !
                netSalesAmountEl) {
                return;
            }

            const parseNumber = (value) => {
                const cleaned = String(value ?? '').replace(/[^0-9.-]/g, '').trim();
                const number = Number(cleaned);
                return Number.isFinite(number) ? number : 0;
            };

            const pickNumber = (...values) => {
                for (const value of values) {
                    if (value === null || value === undefined || value === '') {
                        continue;
                    }

                    const number = parseNumber(value);
                    if (Number.isFinite(number)) {
                        return number;
                    }
                }

                return 0;
            };

            const pickPositiveNumber = (...values) => {
                for (const value of values) {
                    if (value === null || value === undefined || value === '') {
                        continue;
                    }

                    const number = parseNumber(value);
                    if (Number.isFinite(number) && number > 0) {
                        return number;
                    }
                }

                return 0;
            };

            const formatMoney = (value, fractionDigits = 2) => {
                const number = Number(value ?? 0);
                return number.toLocaleString('en-US', {
                    minimumFractionDigits: fractionDigits,
                    maximumFractionDigits: fractionDigits,
                });
            };

            const numberToWords = (value) => {
                const number = Math.floor(Math.max(parseNumber(value), 0));
                if (number === 0) {
                    return 'Zero';
                }

                const ones = [
                    '',
                    'One',
                    'Two',
                    'Three',
                    'Four',
                    'Five',
                    'Six',
                    'Seven',
                    'Eight',
                    'Nine',
                    'Ten',
                    'Eleven',
                    'Twelve',
                    'Thirteen',
                    'Fourteen',
                    'Fifteen',
                    'Sixteen',
                    'Seventeen',
                    'Eighteen',
                    'Nineteen',
                ];
                const tens = [
                    '',
                    '',
                    'Twenty',
                    'Thirty',
                    'Forty',
                    'Fifty',
                    'Sixty',
                    'Seventy',
                    'Eighty',
                    'Ninety',
                ];
                const units = [{
                        name: 'Crore',
                        value: 10000000
                    },
                    {
                        name: 'Lakh',
                        value: 100000
                    },
                    {
                        name: 'Thousand',
                        value: 1000
                    },
                    {
                        name: 'Hundred',
                        value: 100
                    },
                ];

                const convertUnderHundred = (n) => {
                    if (n < 20) {
                        return ones[n] || '';
                    }

                    const ten = Math.floor(n / 10);
                    const one = n % 10;
                    return `${tens[ten] || ''}${one ? ` ${ones[one] || ''}` : ''}`.trim();
                };

                let remaining = number;
                const words = [];

                units.forEach((unit) => {
                    if (remaining >= unit.value) {
                        const count = Math.floor(remaining / unit.value);
                        remaining %= unit.value;
                        words.push(count > 99 ? numberToWords(count) : convertUnderHundred(count));
                        words.push(unit.name);
                    }
                });

                if (remaining > 0) {
                    words.push(convertUnderHundred(remaining));
                }

                return words.filter(Boolean).join(' ').trim();
            };

            const formatAmountInWords = (amount) => {
                let normalizedAmount = Math.round(Math.max(parseNumber(amount), 0) * 100) / 100;
                let rupees = Math.floor(normalizedAmount);
                let paise = Math.round((normalizedAmount - rupees) * 100);

                if (paise === 100) {
                    rupees += 1;
                    paise = 0;
                }

                const rupeeLabel = rupees === 1 ? 'Nepalese Rupee' : 'Nepalese Rupees';
                const paiseLabel = paise === 1 ? 'Paisa' : 'Paise';
                const rupeeWords = numberToWords(rupees);

                if (rupees === 0 && paise === 0) {
                    return `Zero ${rupeeLabel} Only`;
                }

                if (paise > 0) {
                    return `${rupeeWords} ${rupeeLabel} and ${numberToWords(paise)} ${paiseLabel} Only`;
                }

                return `${rupeeWords} ${rupeeLabel} Only`;
            };

            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            } [char]));

            const cleanAddonLabel = (value) => String(value ?? '').replace(/^[↳↲]+\s*/u, '').trim();

            const normalizeBillingAddon = (addon = {}) => {
                const quantity = Math.max(pickNumber(addon?.quantity ?? 1), 1);
                const storedPrice = pickNumber(addon?.price, addon?.rate);
                const price = storedPrice > 0 ?
                    storedPrice :
                    pickPositiveNumber(addon?.masterAddon?.price, addon?.menu_item_addon_price, 0);
                const discount = Math.max(pickNumber(addon?.discount, addon?.applied_discount, 0), 0);
                const baseAmount = price * quantity;
                const total = Math.max(baseAmount - discount, 0);
                const addonName = cleanAddonLabel(
                    addon?.name ?? addon?.addon_name ?? addon?.masterAddon?.name ?? 'Addon'
                );

                return {
                    id: pickNumber(addon?.id, addon?.menu_item_addon_id, addon?.masterAddon?.id, 0),
                    name: addonName,
                    price,
                    quantity,
                    discount,
                    applied_discount: discount,
                    baseAmount,
                    base_amount: baseAmount,
                    lineTotalBeforeDiscount: baseAmount,
                    line_total_before_discount: baseAmount,
                    total,
                    amount: total,
                };
            };

            const normalizeBillingItem = (item = {}) => {
                const rawAddons = Array.isArray(item?.addons) ?
                    item.addons :
                    (Array.isArray(item?.order_item_addons) ?
                        item.order_item_addons :
                        (Array.isArray(item?.orderItemAddons) ? item.orderItemAddons : []));
                const status = String(item?.status ?? item?.item_status ?? '').toLowerCase();
                const isRejected = Boolean(item?.is_rejected) || status === 'rejected' || status ===
                'cancelled';
                const rejectionReason = String(
                    item?.rejection_reason ?? item?.cancel_reason ?? item?.reason ?? ''
                ).trim();
                const addons = rawAddons.map(normalizeBillingAddon).filter((addon) => addon.name.trim() !== '');
                const qty = pickNumber(item?.qty, item?.quantity, 0);
                const rate = Math.max(pickNumber(item?.rate), 0);
                const addonTotal = pickNumber(
                    item?.addonTotal,
                    item?.addon_total,
                    isRejected ?
                    0 :
                    addons.reduce((sum, addon) => sum + pickNumber(addon.baseAmount, addon.base_amount,
                        addon.total), 0)
                );
                const addonDiscountTotal = pickNumber(
                    item?.addonDiscountTotal,
                    item?.addon_discount_total,
                    isRejected ?
                    0 :
                    addons.reduce((sum, addon) => sum + pickNumber(addon.discount, addon.applied_discount),
                        0)
                );
                const baseAmount = Math.max(pickNumber(
                    item?.baseAmount,
                    item?.base_amount,
                    item?.lineBaseAmount,
                    item?.base_line_amount,
                    qty * rate
                ), 0);
                const lineTotalBeforeDiscount = Math.max(pickNumber(
                    item?.lineTotalBeforeDiscount,
                    item?.line_total_before_discount,
                    item?.pre_discount_total,
                    baseAmount + addonTotal
                ), 0);
                const discount = pickNumber(item?.discount, item?.applied_discount, 0);
                const total = Math.max(pickNumber(
                    item?.total,
                    item?.amount,
                    lineTotalBeforeDiscount - discount - addonDiscountTotal
                ), 0);
                const itemName = String(item?.name ?? item?.item_name ?? 'Item');
                const displayBaseAmount = isRejected ? 0 : baseAmount;
                const displayAddonTotal = isRejected ? 0 : addonTotal;
                const displayAddonDiscountTotal = isRejected ? 0 : addonDiscountTotal;
                const displayLineTotalBeforeDiscount = isRejected ? 0 : lineTotalBeforeDiscount;
                const displayDiscount = isRejected ? 0 : discount;
                const displayTotal = isRejected ? 0 : total;

                return {
                    id: pickNumber(item?.id, 0),
                    name: itemName,
                    status,
                    isRejected,
                    is_rejected: isRejected,
                    rejectionReason,
                    rejection_reason: rejectionReason,
                    qty,
                    quantity: qty,
                    rate,
                    discount: displayDiscount,
                    addons,
                    addonTotal: displayAddonTotal,
                    addon_total: displayAddonTotal,
                    addonDiscountTotal: displayAddonDiscountTotal,
                    addon_discount_total: displayAddonDiscountTotal,
                    baseAmount: displayBaseAmount,
                    base_amount: displayBaseAmount,
                    lineTotalBeforeDiscount: displayLineTotalBeforeDiscount,
                    line_total_before_discount: displayLineTotalBeforeDiscount,
                    total: displayTotal,
                    amount: displayTotal,
                };
            };

            const normalizeGroupKey = (value) => String(value ?? '')
                .trim()
                .replace(/\s+/g, ' ')
                .toLowerCase();

            const mergeBillingAddonRows = (existingAddons = [], incomingAddons = []) => {
                const groupedAddons = [];
                const addonMap = new Map();

                [...existingAddons, ...incomingAddons].forEach((addon = {}) => {
                    const normalizedAddon = normalizeBillingAddon(addon);
                    const signature = [
                        normalizeGroupKey(normalizedAddon.name),
                        normalizeGroupKey(normalizedAddon.id || normalizedAddon.menu_item_addon_id || ''),
                        Number(normalizedAddon.price || 0).toFixed(4),
                    ].join('::');

                    const existing = addonMap.get(signature);
                    if (!existing) {
                        const bucket = {
                            ...normalizedAddon,
                        };
                        addonMap.set(signature, bucket);
                        groupedAddons.push(bucket);
                        return;
                    }

                    existing.quantity += normalizedAddon.quantity;
                    existing.qty = existing.quantity;
                    existing.baseAmount += normalizedAddon.baseAmount;
                    existing.base_amount = existing.baseAmount;
                    existing.lineTotalBeforeDiscount += normalizedAddon.lineTotalBeforeDiscount;
                    existing.line_total_before_discount = existing.lineTotalBeforeDiscount;
                    existing.discount += normalizedAddon.discount;
                    existing.applied_discount = existing.discount;
                    existing.total += normalizedAddon.total;
                    existing.amount = existing.total;
                });

                return groupedAddons;
            };

            const groupBillingDisplayItems = (items = []) => {
                const groupedItems = [];
                const itemMap = new Map();

                items.forEach((item = {}) => {
                    const normalizedItem = normalizeBillingItem(item);
                    const addonSignature = (normalizedItem.addons || [])
                        .map((addon) => [
                            normalizeGroupKey(addon.name),
                            normalizeGroupKey(addon.id || addon.menu_item_addon_id || ''),
                            Number(addon.price || 0).toFixed(4),
                        ].join('::'))
                        .sort()
                        .join('~~');
                    const signature = [
                        normalizeGroupKey(normalizedItem.name),
                        Number(normalizedItem.rate || 0).toFixed(4),
                        normalizeGroupKey(normalizedItem.status),
                        normalizedItem.isRejected ? '1' : '0',
                        normalizeGroupKey(normalizedItem.rejectionReason),
                        addonSignature,
                    ].join('||');

                    const existing = itemMap.get(signature);
                    if (!existing) {
                        const bucket = {
                            ...normalizedItem,
                            addons: [],
                            qty: 0,
                            quantity: 0,
                            addonTotal: 0,
                            addon_total: 0,
                            addonDiscountTotal: 0,
                            addon_discount_total: 0,
                            baseAmount: 0,
                            base_amount: 0,
                            lineTotalBeforeDiscount: 0,
                            line_total_before_discount: 0,
                            total: 0,
                            amount: 0,
                        };
                        itemMap.set(signature, bucket);
                        groupedItems.push(bucket);
                    }

                    const bucket = itemMap.get(signature);
                    bucket.qty += normalizedItem.qty;
                    bucket.quantity = bucket.qty;
                    bucket.baseAmount += normalizedItem.baseAmount;
                    bucket.base_amount = bucket.baseAmount;
                    bucket.lineTotalBeforeDiscount += normalizedItem.lineTotalBeforeDiscount;
                    bucket.line_total_before_discount = bucket.lineTotalBeforeDiscount;
                    bucket.discount += normalizedItem.discount;
                    bucket.addonTotal += normalizedItem.addonTotal;
                    bucket.addon_total = bucket.addonTotal;
                    bucket.addonDiscountTotal += normalizedItem.addonDiscountTotal;
                    bucket.addon_discount_total = bucket.addonDiscountTotal;
                    bucket.total += normalizedItem.total;
                    bucket.amount = bucket.total;
                    bucket.addons = mergeBillingAddonRows(bucket.addons, normalizedItem.addons || []);
                });

                return groupedItems;
            };

            window.groupBillingDisplayItems = groupBillingDisplayItems;

            const renderAddonRows = (addons = [], isRejected = false) => {
                if (!Array.isArray(addons) || addons.length === 0) {
                    return '';
                }

                return `
                <div class="mt-1.5 space-y-1 text-[11px] leading-4">
                    ${addons.map((addon) => {
                        const qtyText = addon.quantity > 1 ? ` x${addon.quantity}` : '';
                        return `
                                    <div class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 rounded-md bg-yellow-50 px-2 py-1.5 text-xs text-slate-700 ${isRejected ? 'opacity-70 text-slate-400' : ''}">
                                        <div class="flex items-center gap-2 pl-4 font-medium ${isRejected ? 'text-slate-500' : 'text-slate-950'}">
                                            <span class="shrink-0 text-slate-600">↳</span>
                                            <span class="truncate">${escapeHtml(cleanAddonLabel(addon.name))}${qtyText}</span>
                                        </div>
                                        <div class="text-slate-700">Rs ${formatMoney(addon.price, 0)}</div>
                                        <div class="text-center text-slate-700">${formatMoney(addon.quantity, 0)}</div>
                                        <div class="text-right font-medium text-slate-700">Rs ${formatMoney(addon.total, 0)}</div>
                                    </div>
                                `;
                    }).join('')}
                </div>
            `;
            };

            const resolveTaxConfig = (tableNumber = null) => {
                const config = window.resolveBillingTaxConfig?.(tableNumber) || {
                    setting: String(window.billingTaxSetting || 'exclusive'),
                    ratePercent: Number(window.billingTaxRatePercent || 0),
                    rate: Number(window.billingTaxRate || 0),
                    label: String(window.billingTaxLabelName || 'Tax'),
                };
                return {
                    ...config,
                    label: window.formatBillingTaxLabel?.(config.label, config.ratePercent, config.setting) ||
                        config.label,
                };
            };

            const getInvoiceRemarks = () => String(
                window.billingInvoiceRemarks ??
                document.getElementById('billingInvoiceRemarks')?.value ??
                ''
            ).trim();

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const tenderInput = document.getElementById('billingTenderAmount');
            const saveBillingUrl = @json(route('admin.billing.checkout.store', [], false));
            const saveBillingDraftUrl = @json(route('admin.billing.drafts.store', [], false));
            const estimatePdfUrl = @json(route('admin.billing.estimate.pdf', [], false));
            const getCurrentBillingMode = () => String(window.billingPaymentMode || 'paid').toLowerCase();
            const getSelectedPaymentMethod = () => String(window.billingPaymentMethod || '').trim();
            const getTenderAmountValue = () => pickNumber(
                tenderInput?.value,
                window.billingTenderAmountValue,
                0
            );
            const isTenderAmountRequired = () => getCurrentBillingMode() === 'paid';
            const isPaymentMethodRequired = () => getCurrentBillingMode() === 'paid';
            const hasRequiredTenderAmount = () => !isTenderAmountRequired() || getTenderAmountValue() > 0;
            const hasRequiredPaymentMethod = () => !isPaymentMethodRequired() || getSelectedPaymentMethod() !== '';
            const showBlockingToast = (message) => {
                if (typeof window.showToast === 'function') {
                    window.showToast({
                        type: 'error',
                        message,
                        duration: 3000,
                    });
                    return;
                }

                alert(message);
            };
            const validateBeforeConfirm = () => {
                if (isPaymentMethodRequired() && !hasRequiredPaymentMethod()) {
                    showBlockingToast('Please select payment method before confirming the bill.');
                    return false;
                }

                if (isTenderAmountRequired() && !hasRequiredTenderAmount()) {
                    showBlockingToast('Please enter tender amount before confirming the bill.');
                    tenderInput?.focus();
                    return false;
                }

                return true;
            };
            const ensureTenderAmount = () => {
                if (hasRequiredTenderAmount()) {
                    return true;
                }

                showBlockingToast('Please enter tender amount before confirming the bill.');
                tenderInput?.focus();
                return false;
            };
            const saveBillingRequest = async (action = 'checkout') => {
                if (!validateBeforeConfirm()) {
                    return null;
                }

                const snapshot = buildSnapshot(window.billingEstimateInvoiceData || {});
                const tableNumber = String(window.currentOpenTable || document.getElementById(
                    'billingDineInTable')?.textContent?.replace(/^Table/i, '')?.trim() || '').trim();
                const currentCard = tableNumber ? document.querySelector(
                    `.table-card[data-table-number="${tableNumber}"]`) : null;
                const taxConfig = resolveTaxConfig(tableNumber);
                const tableId = window.currentOpenTableId || currentCard?.dataset?.id || null;
                const qrToken = window.currentOpenTableQrToken || currentCard?.dataset?.qrToken || null;
                const paymentMode = String(window.billingPaymentMode || 'paid');
                const paymentMethod = paymentMode === 'paid' ? getSelectedPaymentMethod() : '';
                if (paymentMode === 'paid' && !paymentMethod) {
                    showBlockingToast('Please select payment method before confirming the bill.');
                    return null;
                }
                const tenderAmount = pickNumber(document.getElementById('billingTenderAmount')?.value,
                    window.billingTenderAmountValue, snapshot.tenderAmount);
                const changeAmount = Math.max(tenderAmount - snapshot.grandTotal, 0);
                const notesSnapshot = getInvoiceRemarks();
                const itemRows = (snapshot.items || []).map((item) => normalizeBillingItem(item)).filter((
                    item) => item.id > 0);

                const response = await fetch(saveBillingUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        action,
                        table_id: tableId,
                        table_number: tableNumber,
                        qr_token: qrToken,
                        payment_mode: paymentMode,
                        payment_method: paymentMethod,
                        item_count: snapshot.itemCount,
                        total_qty: snapshot.totalQty,
                        subtotal_before_discount: snapshot.itemBaseTotal,
                        item_discount_amount: snapshot.itemDiscountTotal,
                        subtotal_after_item_discount: snapshot.subtotalAfterItemDiscount,
                        overall_discount_percent: snapshot.itemBaseTotal > 0 ? (snapshot
                            .overallDiscountAmount / snapshot.itemBaseTotal) * 100 : 0,
                        overall_discount_amount: snapshot.overallDiscountAmount,
                        tax_setting: taxConfig.setting,
                        tax_rate_snapshot: taxConfig.ratePercent,
                        tax_amount: snapshot.taxAmount,
                        grand_total: snapshot.grandTotal,
                        tender_amount: tenderAmount,
                        change_amount: changeAmount,
                        paid_amount: paymentMode === 'paid' ? snapshot.grandTotal : (
                            paymentMode === 'partial' ? tenderAmount : 0),
                        due_amount: paymentMode === 'unpaid' ? snapshot.grandTotal : Math
                            .max(snapshot.grandTotal - tenderAmount, 0),
                        customer_name_snapshot: document.getElementById(
                                'billingCustomerName')?.textContent?.trim() ||
                            'Cash Customer',
                        notes_snapshot: notesSnapshot,
                        transaction_ref: `${action}-${Date.now()}`,
                        items: itemRows,
                    }),
                });

                const result = await response.json();
                if (!response.ok || !result?.success) {
                    throw new Error(result?.message || 'Unable to save billing.');
                }

                return result.data || {};
            };

            const buildBillingDraftPayload = () => {
                const snapshot = buildSnapshot(window.billingEstimateInvoiceData || {});
                const tableNumber = String(window.currentOpenTable || document.getElementById(
                    'billingDineInTable')?.textContent?.replace(/^Table/i, '')?.trim() || '').trim();
                const currentCard = tableNumber ? document.querySelector(
                    `.table-card[data-table-number="${tableNumber}"]`) : null;
                const taxConfig = resolveTaxConfig(tableNumber);
                const tableId = window.currentOpenTableId || currentCard?.dataset?.id || null;
                const qrToken = window.currentOpenTableQrToken || currentCard?.dataset?.qrToken || null;
                const paymentMode = String(
                    window.billingPaymentMode ||
                    document.querySelector('[data-payment-mode-root]')?.dataset?.paymentMode ||
                    'paid'
                ).toLowerCase();
                const paymentMethod = paymentMode === 'paid'
                    ? String(
                        window.billingPaymentMethod ||
                        document.getElementById('billingSelectedPaymentMethod')?.value ||
                        ''
                    ).trim()
                    : '';
                const tenderAmount = pickNumber(document.getElementById('billingTenderAmount')?.value,
                    window.billingTenderAmountValue, snapshot.tenderAmount);
                const changeAmount = Math.max(tenderAmount - snapshot.grandTotal, 0);
                const paidAmount = paymentMode === 'paid' ? snapshot.grandTotal : (
                    paymentMode === 'partial' ? tenderAmount : 0);
                const dueAmount = paymentMode === 'unpaid' ? snapshot.grandTotal : Math.max(snapshot
                    .grandTotal - paidAmount, 0);
                const overallDiscountAmount = pickNumber(
                    document.getElementById('billingLeftOverallDiscount')?.value,
                    window.billingOverallDiscountAmount,
                    snapshot.overallDiscountAmount
                );
                const overallDiscountPercent = pickNumber(
                    document.getElementById('billingLeftDiscountAmount')?.value,
                    snapshot.itemBaseTotal > 0 ? (overallDiscountAmount / snapshot.itemBaseTotal) * 100 : 0
                );
                const discountMode = String(
                    window.billingDiscountSource ||
                    document.querySelector('[data-pos-discount-root]')?.dataset?.discountMode ||
                    'amount'
                ).toLowerCase();
                const multiplePaymentEnabled = document.getElementById('billingMultiplePaymentEnabled')?.value === '1';
                const notesSnapshot = getInvoiceRemarks();
                const currentOrder = window.billingCurrentOrderPayload || {};
                const billingState = {
                    payment_mode: paymentMode,
                    payment_method: paymentMethod,
                    discount_mode: discountMode,
                    multiple_payment_enabled: multiplePaymentEnabled,
                    tender_amount: tenderAmount,
                    change_amount: changeAmount,
                    paid_amount: paidAmount,
                    due_amount: dueAmount,
                    overall_discount_amount: overallDiscountAmount,
                    overall_discount_percent: overallDiscountPercent,
                    tax_amount: snapshot.taxAmount,
                    grand_total: snapshot.grandTotal,
                    notes_snapshot: notesSnapshot,
                };

                return {
                    id: currentOrder.id || currentOrder.order_id || null,
                    order_id: currentOrder.id || currentOrder.order_id || null,
                    tenant_id: currentOrder.tenant_id || null,
                    branch_id: currentOrder.branch_id || null,
                    table_id: tableId,
                    table_number: tableNumber,
                    qr_token: qrToken,
                    order_number: currentOrder.order_number || currentOrder.order_no || null,
                    order_by_label: currentOrder.order_by_label || currentOrder.order_by_label_name || currentOrder.order_by || 'Guest',
                    created_at: currentOrder.created_at || null,
                    ordered_at: currentOrder.ordered_at || currentOrder.ordered_at_iso || null,
                    status: currentOrder.status || 'running',
                    payment_status: currentOrder.payment_status || 'pending',
                    payment_mode: billingState.payment_mode,
                    payment_method: billingState.payment_method,
                    discount_mode: billingState.discount_mode,
                    multiple_payment_enabled: billingState.multiple_payment_enabled,
                    item_count: snapshot.itemCount,
                    total_qty: snapshot.totalQty,
                    subtotal_before_discount: snapshot.itemBaseTotal,
                    item_discount_amount: snapshot.itemDiscountTotal,
                    subtotal_after_item_discount: snapshot.subtotalAfterItemDiscount,
                    overall_discount_percent: billingState.overall_discount_percent,
                    overall_discount_amount: billingState.overall_discount_amount,
                    tax_setting: taxConfig.setting,
                    tax_rate_snapshot: taxConfig.ratePercent,
                    tax_amount: billingState.tax_amount,
                    grand_total: billingState.grand_total,
                    tender_amount: billingState.tender_amount,
                    change_amount: billingState.change_amount,
                    paid_amount: billingState.paid_amount,
                    due_amount: billingState.due_amount,
                    customer_name_snapshot: document.getElementById(
                            'billingCustomerName')?.textContent?.trim() ||
                        'Cash Customer',
                    notes_snapshot: billingState.notes_snapshot,
                    billing_state: billingState,
                    items: (snapshot.items || []).map((item) => normalizeBillingItem(item)),
                    held_at: new Date().toISOString(),
                };
            };

            const saveBillingDraft = async () => {
                if (typeof window.syncBillingEstimateInvoice === 'function') {
                    try {
                        window.syncBillingEstimateInvoice();
                    } catch (error) {
                        console.warn('Billing draft sync before hold failed', error);
                    }
                }

                const payload = buildBillingDraftPayload();
                const tableId = payload.table_id;
                const tableNumber = String(payload.table_number || '').trim();

                if (!tableId || !tableNumber) {
                    showBlockingToast('Unable to determine table for hold action.');
                    return null;
                }

                if (!Array.isArray(payload.items) || payload.items.length === 0) {
                    showBlockingToast('No billing items available to hold.');
                    return null;
                }

                const response = await fetch(saveBillingDraftUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        table_id: tableId,
                        table_number: tableNumber,
                        payload,
                    }),
                });

                const result = await response.json();
                if (!response.ok || !result?.success) {
                    throw new Error(result?.message || 'Unable to hold billing.');
                }

                const heldPayload = result?.data?.payload || payload;
                window.currentBillingDraftPayload = heldPayload;
                window.currentBillingDraftTableId = String(tableId);
                window.dispatchEvent(new CustomEvent('billing-draft-updated', {
                    detail: {
                        tableId,
                        payload: heldPayload,
                    },
                }));

                return result.data || {};
            };

            const buildEstimatePdfPayload = () => {
                const snapshot = buildSnapshot(window.billingEstimateInvoiceData || {});
                const tableNumber = String(
                    window.currentOpenTable ||
                    document.getElementById('billingDineInTable')?.textContent?.replace(/^Table/i, '')
                    ?.trim() ||
                    ''
                ).trim();
                const currentCard = tableNumber ? document.querySelector(
                    `.table-card[data-table-number="${tableNumber}"]`) : null;
                const taxConfig = resolveTaxConfig(tableNumber);
                const tableId = window.currentOpenTableId || currentCard?.dataset?.id || null;
                const qrToken = window.currentOpenTableQrToken || currentCard?.dataset?.qrToken || null;
                const paymentMode = String(window.billingPaymentMode || 'unpaid');
                const paymentMethod = paymentMode === 'paid' ? String(window.billingPaymentMethod || '')
                    .trim() : '';
                const tenderAmount = pickNumber(document.getElementById('billingTenderAmount')?.value, window
                    .billingTenderAmountValue, snapshot.tenderAmount);
                const changeAmount = Math.max(tenderAmount - snapshot.grandTotal, 0);
                const paidAmount = paymentMode === 'paid' ?
                    snapshot.grandTotal :
                    (paymentMode === 'partial' ? tenderAmount : 0);
                const dueAmount = paymentMode === 'unpaid' ?
                    snapshot.grandTotal :
                    Math.max(snapshot.grandTotal - paidAmount, 0);
                const notesSnapshot = getInvoiceRemarks();
                const kotNo = document.getElementById('billingKotNo')?.textContent?.trim() || '';
                const assignTo = document.getElementById('billingAssignTo')?.textContent?.trim() || '';
                const billedBy = document.getElementById('billingBilledBy')?.textContent?.trim() || '';
                const serviceDuration = document.getElementById('billingServiceDuration')?.textContent
                    ?.trim() || '';

                return {
                    table_id: tableId,
                    table_number: tableNumber,
                    qr_token: qrToken,
                    payment_mode: paymentMode,
                    payment_method: paymentMethod,
                    item_count: snapshot.displayItemCount ?? snapshot.itemCount,
                    total_qty: snapshot.totalQty,
                    subtotal_before_discount: snapshot.itemBaseTotal,
                    item_discount_amount: snapshot.itemDiscountTotal,
                    subtotal_after_item_discount: snapshot.subtotalAfterItemDiscount,
                    overall_discount_percent: snapshot.itemBaseTotal > 0 ? (snapshot.overallDiscountAmount /
                        snapshot.itemBaseTotal) * 100 : 0,
                    overall_discount_amount: snapshot.overallDiscountAmount,
                    tax_setting: taxConfig.setting,
                    tax_rate_snapshot: taxConfig.ratePercent,
                    tax_amount: snapshot.taxAmount,
                    grand_total: snapshot.grandTotal,
                    tender_amount: tenderAmount,
                    change_amount: changeAmount,
                    paid_amount: paidAmount,
                    due_amount: dueAmount,
                    customer_name_snapshot: document.getElementById('billingCustomerName')?.textContent
                        ?.trim() || 'Cash Customer',
                    notes_snapshot: notesSnapshot,
                    kot_no: kotNo,
                    assign_to: assignTo,
                    billed_by: billedBy,
                    service_duration: serviceDuration,
                    items_json: JSON.stringify(snapshot.displayItems || snapshot.items || []),
                };
            };

            const openEstimatePdf = (payload, outputMode = 'download', targetWindowName = '') => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = estimatePdfUrl;
                form.style.display = 'none';

                if (outputMode === 'download') {
                    const downloadFrameId = 'billingEstimateDownloadFrame';
                    let downloadFrame = document.getElementById(downloadFrameId);
                    if (!downloadFrame) {
                        downloadFrame = document.createElement('iframe');
                        downloadFrame.id = downloadFrameId;
                        downloadFrame.name = downloadFrameId;
                        downloadFrame.style.display = 'none';
                        document.body.appendChild(downloadFrame);
                    }
                    form.target = downloadFrame.name;
                } else {
                    if (targetWindowName) {
                        form.target = targetWindowName;
                    } else {
                    const printFrameId = 'billingEstimatePrintFrame';
                    let printFrame = document.getElementById(printFrameId);
                    if (!printFrame) {
                        printFrame = document.createElement('iframe');
                        printFrame.id = printFrameId;
                        printFrame.name = printFrameId;
                        printFrame.style.display = 'none';
                        document.body.appendChild(printFrame);
                    }

                    printFrame.onload = () => {
                        window.setTimeout(() => {
                            try {
                                printFrame.contentWindow?.focus();
                                printFrame.contentWindow?.print();
                            } catch (error) {
                                console.error('Billing estimate print failed', error);
                            }
                        }, 300);
                    };

                    form.target = printFrame.name;
                    }
                }

                const appendField = (name, value) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value === null || value === undefined ? '' : String(value);
                    form.appendChild(input);
                };

                appendField('_token', csrfToken);
                appendField('output_mode', outputMode);
                Object.entries(payload).forEach(([name, value]) => appendField(name, value));

                document.body.appendChild(form);
                form.submit();
                form.remove();
            };

            const printPdfInHiddenFrame = (pdfUrl) => {
                if (!pdfUrl) return;

                const frameId = 'billingReceiptPrintFrame';
                let printFrame = document.getElementById(frameId);
                if (!printFrame) {
                    printFrame = document.createElement('iframe');
                    printFrame.id = frameId;
                    printFrame.name = frameId;
                    printFrame.style.display = 'none';
                    document.body.appendChild(printFrame);
                }

                printFrame.onload = () => {
                    window.setTimeout(() => {
                        try {
                            printFrame.contentWindow?.focus();
                            printFrame.contentWindow?.print();
                        } catch (error) {
                            console.error('Billing receipt print failed', error);
                        }
                    }, 300);
                };

                printFrame.src = pdfUrl;
            };

            let syncFrame = null;

            const readLeftItems = () => {
                const rows = Array.from(document.querySelectorAll('#billingLeftItemsBody tr[data-row-kind]'));
                const items = [];
                let currentItem = null;

                rows.forEach((row) => {
                    const cell = row.querySelector('[data-discount-cell]');
                    if (!cell) return;

                    const rowKind = String(row.dataset.rowKind || 'item');
                    const isRejected = row.dataset.itemIsRejected === '1';
                    const nameCell = row.children?.[1];
                    const name = row.dataset.itemName ||
                        row.dataset.addonName ||
                        nameCell?.querySelector?.('div.font-medium')?.firstChild?.textContent?.trim() ||
                        nameCell?.querySelector?.('div.font-medium')?.textContent?.trim() ||
                        nameCell?.querySelector?.('.font-medium.text-slate-950')?.textContent?.trim() ||
                        nameCell?.textContent?.trim() ||
                        'Item';
                    const cleanName = rowKind === 'addon' ? cleanAddonLabel(name) : name;
                    const rejectionReason = String(
                        row.dataset.itemRejectionReason ||
                        ''
                    ).trim();
                    const rate = parseNumber(row?.dataset?.itemRate || row?.children?.[3]?.textContent);
                    const qty = parseNumber(row?.dataset?.itemQty || row?.children?.[2]?.textContent);
                    const discount = isRejected ? 0 : parseNumber(cell.dataset.itemDiscountAmount);
                    const baseAmount = isRejected ? 0 : parseNumber(cell.dataset.itemBaseAmount || row
                        ?.dataset
                        ?.itemBaseAmount || (qty * rate));
                    const lineTotalBeforeDiscount = parseNumber(
                        cell.dataset.itemLineTotalBeforeDiscount ||
                        row?.dataset?.itemLineTotalBeforeDiscount ||
                        baseAmount
                    );
                    const total = isRejected ? 0 : Math.max(baseAmount - discount, 0);

                    if (rowKind === 'item') {
                        currentItem = {
                            id: parseNumber(row?.dataset?.itemId),
                            name: cleanName,
                            status: String(row?.dataset?.itemStatus || (isRejected ? 'rejected' :
                                'new')).toLowerCase(),
                            isRejected,
                            is_rejected: isRejected,
                            rejectionReason,
                            rejection_reason: rejectionReason,
                            rate,
                            qty,
                            quantity: qty,
                            discount,
                            addons: [],
                            addonTotal: 0,
                            addon_total: 0,
                            addonDiscountTotal: 0,
                            addon_discount_total: 0,
                            baseAmount,
                            base_amount: baseAmount,
                            lineTotalBeforeDiscount,
                            line_total_before_discount: lineTotalBeforeDiscount,
                            total,
                            amount: total,
                        };
                        items.push(currentItem);
                        return;
                    }

                    if (rowKind === 'addon' && currentItem) {
                        const addon = {
                            id: parseNumber(row?.dataset?.addonId),
                            name,
                            rate,
                            price: rate,
                            qty,
                            quantity: qty,
                            discount,
                            applied_discount: discount,
                            baseAmount,
                            base_amount: baseAmount,
                            lineTotalBeforeDiscount,
                            line_total_before_discount: lineTotalBeforeDiscount,
                            total,
                            amount: total,
                        };

                        currentItem.addons.push(addon);

                        if (currentItem.isRejected) {
                            return;
                        }

                        currentItem.addonTotal += baseAmount;
                        currentItem.addon_total = currentItem.addonTotal;
                        currentItem.addonDiscountTotal += discount;
                        currentItem.addon_discount_total += discount;
                        currentItem.lineTotalBeforeDiscount += baseAmount;
                        currentItem.line_total_before_discount = currentItem.lineTotalBeforeDiscount;
                        currentItem.total += total;
                        currentItem.amount = currentItem.total;
                    }
                });

                return items;
            };

            const buildSnapshot = (detail = {}) => {
                const shared = window.billingEstimateInvoiceData || {};
                const leftItems = readLeftItems();
                const items = leftItems.length ? leftItems :
                    Array.isArray(detail.items) ? detail.items.map((item) => normalizeBillingItem(item)) :
                    Array.isArray(shared.items) ? shared.items.map((item) => normalizeBillingItem(item)) : [];
                const displayItems = groupBillingDisplayItems(items);
                const liveOverallDiscount = parseNumber(document.getElementById('billingLeftOverallDiscount')
                    ?.value);
                const liveTaxAmount = parseNumber(document.getElementById('billingLeftNoTax')?.textContent);
                const liveGrandTotal = parseNumber(document.getElementById('billingLeftGrandTotal')
                    ?.textContent);
                const liveTenderAmount = parseNumber(document.getElementById('billingTenderAmount')?.value);

                const itemCount = pickNumber(detail.itemCount, shared.itemCount, items.length);
                const displayItemCount = displayItems.length;
                const totalQty = pickNumber(
                    detail.totalQty,
                    shared.totalQty,
                    items.reduce((sum, item) => {
                        const addonQty = item.isRejected ? 0 : (item.addons || []).reduce((addonSum,
                            addon) => {
                            return addonSum + pickNumber(addon.qty, addon.quantity);
                        }, 0);
                        return sum + pickNumber(item.qty) + addonQty;
                    }, 0)
                );
                const itemBaseTotal = pickNumber(
                    detail.itemBaseTotal,
                    shared.itemBaseTotal,
                    items.reduce((sum, item) => sum + pickNumber(
                        item.lineTotalBeforeDiscount,
                        item.line_total_before_discount,
                        item.baseAmount,
                        item.base_amount,
                        0
                    ), 0)
                );
                const itemDiscountTotal = pickNumber(
                    window.billingItemDiscountTotal,
                    detail.itemDiscountTotal,
                    shared.itemDiscountTotal,
                    items.reduce((sum, item) => {
                        const addonDiscount = item.isRejected ? 0 : (item.addons || []).reduce((
                            addonSum, addon) => {
                            return addonSum + pickNumber(addon.discount, addon
                            .applied_discount);
                        }, 0);
                        return sum + pickNumber(item.discount) + addonDiscount;
                    }, 0)
                );
                const notesSnapshot = String(
                    window.billingInvoiceRemarks ??
                    detail.notesSnapshot ??
                    shared.notesSnapshot ??
                    document.getElementById('billingInvoiceRemarks')?.value ??
                    ''
                ).trim();
                const subtotalAfterItemDiscount = pickNumber(
                    detail.subtotalAfterItemDiscount,
                    shared.subtotalAfterItemDiscount,
                    Math.max(itemBaseTotal - itemDiscountTotal, 0)
                );
                const subtotal = pickNumber(detail.subtotal, shared.subtotal, itemBaseTotal);
                const overallDiscountAmount = pickNumber(
                    liveOverallDiscount,
                    window.billingOverallDiscountAmount,
                    detail.overallDiscountAmount,
                    shared.overallDiscountAmount,
                );
                const taxAmount = pickNumber(
                    liveTaxAmount,
                    window.billingTaxAmount,
                    detail.taxAmount,
                    shared.taxAmount,
                );
                const grandTotal = pickNumber(
                    liveGrandTotal,
                    window.billingGrandTotalAmount,
                    detail.grandTotal,
                    shared.grandTotal,
                );
                const tenderAmount = pickNumber(
                    liveTenderAmount,
                    window.billingTenderAmountValue,
                    detail.tenderAmount,
                );
                const changeAmount = Math.max(tenderAmount - grandTotal, 0);

                return {
                    items,
                    itemCount,
                    totalQty,
                    itemBaseTotal,
                    itemDiscountTotal,
                    subtotal,
                    subtotalAfterItemDiscount,
                    overallDiscountAmount,
                    taxAmount,
                    grandTotal,
                    tenderAmount,
                    changeAmount,
                    notesSnapshot,
                    displayItems,
                    displayItemCount,
                };
            };

            const renderItems = (items) => {
                if (!items.length) {
                    invoiceBody.innerHTML = `
                    <div class="py-3 text-center text-sm text-slate-500">No running order found.</div>
                `;
                    return;
                }

                invoiceBody.innerHTML = items.map((item) => {
                    const normalizedItem = normalizeBillingItem(item);
                    const addonRows = renderAddonRows(normalizedItem.addons, normalizedItem.isRejected);
                    const itemDisplayTotal = normalizedItem.isRejected ? 0 : Math.max(normalizedItem
                        .baseAmount - normalizedItem.discount, 0);
                    const reasonHtml = normalizedItem.isRejected && normalizedItem.rejectionReason ?
                        `
                            <div class="mt-1 flex items-start gap-1.5 text-[11px] leading-4 text-rose-500">
                                <i class="fas fa-circle-xmark mt-0.5 text-[10px] text-rose-500" aria-hidden="true"></i>
                                <span class="break-words">${escapeHtml(normalizedItem.rejectionReason)}</span>
                            </div>
                        ` :
                        '';
                    const itemClasses = normalizedItem.isRejected ?
                        'space-y-1.5 py-1.5 text-xs text-slate-400' :
                        'space-y-1.5 py-1.5 text-xs';

                    return `
                    <div class="${itemClasses}" data-item-id="${normalizedItem.id}"
                        data-item-status="${escapeHtml(normalizedItem.status || '')}"
                        data-item-is-rejected="${normalizedItem.isRejected ? '1' : '0'}"
                        data-item-rejection-reason="${escapeHtml(normalizedItem.rejectionReason || '')}">
                        <div class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3">
                            <div class="font-medium ${normalizedItem.isRejected ? 'text-slate-500' : 'text-slate-700'}">
                                <div>${escapeHtml(normalizedItem.name)}</div>
                                ${reasonHtml}
                            </div>
                            <div class="text-slate-700">Rs ${formatMoney(normalizedItem.rate, 0)}</div>
                            <div class="text-center text-slate-700">${normalizedItem.qty}</div>
                            <div class="text-right text-slate-700">Rs ${formatMoney(itemDisplayTotal, 0)}</div>
                        </div>
                        ${addonRows}
                    </div>
                `;
                }).join('');
            };

            const syncFromLeft = (event = null) => {
                const snapshot = buildSnapshot(event?.detail || {});
                renderItems(snapshot.displayItems || snapshot.items);
                const taxConfig = resolveTaxConfig(window.currentOpenTable);

                const itemCountLabel = snapshot.displayItemCount > 0 ?
                    `${snapshot.displayItemCount}/${Math.max(snapshot.totalQty, 1)}` :
                    '0/0';

                itemCountEl.textContent = itemCountLabel;
                itemTotalEl.textContent = `Rs ${formatMoney(snapshot.itemBaseTotal, 0)}`;
                itemDiscountEl.textContent = `Rs ${formatMoney(snapshot.itemDiscountTotal)}`;
                if (itemDiscountRow) {
                    const shouldShow = snapshot.itemDiscountTotal > 0;
                    itemDiscountRow.classList.toggle('hidden', !shouldShow);
                    itemDiscountRow.hidden = !shouldShow;
                    itemDiscountRow.style.display = shouldShow ? '' : 'none';
                }
                subTotalEl.textContent = `Rs ${formatMoney(snapshot.subtotal)}`;
                overallDiscountEl.textContent = `Rs ${formatMoney(snapshot.overallDiscountAmount)}`;
                taxEl.textContent = `Rs ${formatMoney(snapshot.taxAmount)}`;
                if (taxLabelEl) {
                    taxLabelEl.textContent = taxConfig.label || snapshot.taxLabel || 'Tax';
                }
                totalAmountEl.textContent = `Rs ${formatMoney(snapshot.grandTotal)}`;
                tenderAmountEl.textContent = `Rs ${formatMoney(snapshot.tenderAmount)}`;
                changeAmountEl.textContent = `-Rs ${formatMoney(snapshot.changeAmount)}`;
                netSalesAmountEl.textContent = `Rs ${formatMoney(snapshot.grandTotal)}`;
                const amountInWords = formatAmountInWords(snapshot.grandTotal);
                if (amountInWordsEl) {
                    amountInWordsEl.textContent = amountInWords;
                }
                if (remarksRow && remarksText) {
                    const shouldShowRemarks = Boolean(String(snapshot.notesSnapshot || '').trim());
                    remarksRow.classList.toggle('hidden', !shouldShowRemarks);
                    remarksRow.hidden = !shouldShowRemarks;
                    remarksRow.style.display = shouldShowRemarks ? '' : 'none';
                    remarksText.textContent = String(snapshot.notesSnapshot || '').trim();
                }
                window.billingInvoiceRemarks = String(snapshot.notesSnapshot || '').trim();

                window.billingEstimateInvoiceData = {
                    ...(window.billingEstimateInvoiceData || {}),
                    ...snapshot,
                    items: snapshot.items,
                    displayItems: snapshot.displayItems,
                    displayItemCount: snapshot.displayItemCount,
                    amountInWords,
                    amount_in_words: amountInWords,
                };
            };

            const requestSyncFromLeft = (event = null) => {
                if (syncFrame !== null) {
                    cancelAnimationFrame(syncFrame);
                }

                syncFrame = requestAnimationFrame(() => {
                    syncFrame = null;
                    syncFromLeft(event);
                });
            };

            const refreshConfirmActionState = () => {
                const tenderRequired = isTenderAmountRequired();
                const tenderValid = hasRequiredTenderAmount();
                const paymentMethodRequired = isPaymentMethodRequired();
                const paymentMethodValid = hasRequiredPaymentMethod();
                const blocked = (tenderRequired && !tenderValid) || (paymentMethodRequired && !paymentMethodValid);

                [confirmPrintBtn, confirmCheckoutBtn].forEach((button) => {
                    if (!button) return;

                    button.setAttribute('aria-disabled', String(blocked));
                    button.classList.toggle('opacity-60', blocked);
                    button.classList.toggle('cursor-not-allowed', blocked);
                    button.title = !paymentMethodValid
                        ? 'Please select payment method first.'
                        : (tenderRequired && !tenderValid ? 'Please enter tender amount first.' : '');
                });
            };

            window.syncBillingEstimateInvoice = syncFromLeft;
            window.requestBillingEstimateInvoiceSync = requestSyncFromLeft;
            window.addEventListener('billing-estimate-invoice-updated', requestSyncFromLeft);
            window.addEventListener('billing-discount-mode-changed', requestSyncFromLeft);
            window.addEventListener('billing-payment-mode-changed', requestSyncFromLeft);
            if (tenderInput) {
                tenderInput.addEventListener('input', requestSyncFromLeft);
                tenderInput.addEventListener('input', refreshConfirmActionState);
                tenderInput.addEventListener('change', refreshConfirmActionState);
            }
            requestSyncFromLeft();
            refreshConfirmActionState();
            const closeBillingModal = () => {
                document.getElementById('billingPosCloseBtn')?.click();
            };

            if (holdBillingBtn) {
                holdBillingBtn.addEventListener('click', async () => {
                    try {
                        holdBillingBtn.disabled = true;
                        holdBillingBtn.textContent = 'Saving...';
                        const result = await saveBillingDraft();
                        if (!result) {
                            return;
                        }
                        if (typeof window.showToast === 'function') {
                            window.showToast({
                                type: 'success',
                                message: 'Bill held successfully. You can resume it later.',
                                duration: 3000,
                            });
                        }
                        window.setDrawerGenerateBillButtonState?.(true);
                        closeBillingModal();
                    } catch (error) {
                        console.error(error);
                        showBlockingToast(error.message || 'Unable to hold billing.');
                    } finally {
                        holdBillingBtn.disabled = false;
                        holdBillingBtn.innerHTML = 'Hold Bill';
                    }
                });
            }

            if (confirmCheckoutBtn) {
                confirmCheckoutBtn.addEventListener('click', async () => {
                    if (!ensureTenderAmount()) {
                        return;
                    }

                    try {
                        confirmCheckoutBtn.disabled = true;
                        confirmCheckoutBtn.textContent = 'Saving...';
                        const result = await saveBillingRequest('checkout');
                        if (!result) {
                            return;
                        }
                        window.billingEstimateInvoiceData = {};
                        window.currentBillingDraftPayload = null;
                        window.currentBillingDraftTableId = null;
                        window.requestBillingEstimateInvoiceSync?.();
                        if (window.currentOpenTable && typeof window.markTableAsAvailable === 'function') {
                            window.markTableAsAvailable(window.currentOpenTable, true);
                        }
                        closeBillingModal();
                        if (window.currentOpenTable && typeof window.refreshFromServer === 'function') {
                            await window.refreshFromServer(window.currentOpenTable);
                        }
                        if (result?.print_url) {
                            window.open(result.print_url, '_blank', 'noopener,noreferrer');
                        }
                    } catch (error) {
                        console.error(error);
                        alert(error.message || 'Unable to save billing.');
                    } finally {
                        confirmCheckoutBtn.disabled = false;
                        confirmCheckoutBtn.innerHTML = 'Confirm Checkout';
                    }
                });
            }

            if (confirmPrintBtn) {
                confirmPrintBtn.addEventListener('click', async () => {
                    if (!ensureTenderAmount()) {
                        return;
                    }

                    try {
                        confirmPrintBtn.disabled = true;
                        confirmPrintBtn.textContent = 'Saving...';
                        const result = await saveBillingRequest('print');
                        if (!result) {
                            return;
                        }
                        window.billingEstimateInvoiceData = {};
                        window.currentBillingDraftPayload = null;
                        window.currentBillingDraftTableId = null;
                        window.requestBillingEstimateInvoiceSync?.();
                        if (window.currentOpenTable && typeof window.markTableAsAvailable === 'function') {
                            window.markTableAsAvailable(window.currentOpenTable, true);
                        }
                        if (result?.print_url) {
                            printPdfInHiddenFrame(result.print_url);
                        }
                        closeBillingModal();
                        if (window.currentOpenTable && typeof window.refreshFromServer === 'function') {
                            await window.refreshFromServer(window.currentOpenTable);
                        }
                    } catch (error) {
                        console.error(error);
                        alert(error.message || 'Unable to save billing.');
                    } finally {
                        confirmPrintBtn.disabled = false;
                        confirmPrintBtn.innerHTML = 'Confirm &amp; Print';
                    }
                });
            }

            window.addEventListener('billing-payment-mode-changed', refreshConfirmActionState);
            window.addEventListener('billing-payment-method-changed', refreshConfirmActionState);
            window.addEventListener('billing-estimate-invoice-updated', refreshConfirmActionState);

            if (estimateDownloadBtn) {
                estimateDownloadBtn.addEventListener('click', () => {
                    try {
                        openEstimatePdf(buildEstimatePdfPayload(), 'download');
                    } catch (error) {
                        console.error(error);
                        alert(error.message || 'Unable to download estimate PDF.');
                    }
                });
            }

            if (estimatePrintBtn) {
                estimatePrintBtn.addEventListener('click', () => {
                    try {
                        window.printCurrentBillingEstimate();
                    } catch (error) {
                        console.error(error);
                        alert(error.message || 'Unable to generate estimate PDF.');
                    }
                });
            }

            window.printCurrentBillingEstimate = (targetWindowName = '') => {
                openEstimatePdf(buildEstimatePdfPayload(), 'print', targetWindowName);
                return true;
            };
        });
    </script>
