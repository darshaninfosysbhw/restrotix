<div class="h-full min-h-0 lg:overflow-hidden">
    <div class="flex h-full min-h-0 flex-col p-1 sm:p-2 lg:p-2">
        <div class="billing-pos-no-scrollbar min-h-0 flex-1 space-y-2 overflow-y-auto pr-1">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-950">Items</p>
                    <span id="billingLeftItemCount" class="text-xs font-semibold text-slate-500">({{ count($items) }}
                        Items)</span>
                </div>
                <button type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-2.5 py-1.5 text-xs font-bold text-orange-600 transition hover:bg-orange-100 cursor-pointer">
                    <i class="fas fa-plus text-xs"></i>
                    Add Extra charges
                </button>
            </div>

            <section class="overflow-hidden rounded-xl border border-slate-300" data-pos-discount-root
                data-discount-mode="amount">
                <div class="overflow-x-auto">
                    <table class="min-w-[760px] w-full table-fixed text-left">
                        <thead class="bg-white text-[14px] font-medium text-slate-700">
                            <tr class="border-b border-slate-200">
                                <th class="w-12 border-r border-slate-200 px-4 py-2">S.N</th>
                                <th class="border-r border-slate-200 px-4 py-2">Item</th>
                                <th class="w-16 border-r border-slate-200 px-4 py-2 text-center">QTY</th>
                                <th class="w-28 border-r border-slate-200 px-4 py-2">Rate</th>
                                <th class="w-44 border-r border-slate-200 px-4 py-2">
                                    <div class="flex items-center justify-between gap-2 ">
                                        <span>Discount</span>
                                        <div
                                            class="inline-flex w-[76px] overflow-hidden rounded-md border border-slate-200 bg-white p-0.5 text-[11px] font-bold ">
                                            <button type="button"
                                                class="flex-1 rounded-sm bg-rose-500 px-2 py-1 leading-none text-white cursor-pointer"
                                                data-discount-mode-btn="amount">Rs</button>
                                            <button type="button"
                                                class="flex-1 rounded-sm bg-white px-2 py-1 leading-none text-slate-400 cursor-pointer"
                                                data-discount-mode-btn="percent">%</button>
                                        </div>
                                    </div>
                                </th>
                                <th class="w-28 px-4 py-2">Item Total</th>
                            </tr>
                        </thead>
                        <tbody id="billingLeftItemsBody" class="text-[15px]">
                            @foreach ($items as $index => $item)
                                @php
                                    $isRejected = (bool) ($item['is_rejected'] ?? false) ||
                                        strtolower((string) ($item['status'] ?? '')) === 'rejected' ||
                                        strtolower((string) ($item['status'] ?? '')) === 'cancelled';
                                    $rejectionReason = trim((string) ($item['rejection_reason'] ?? ''));
                                    $itemRate = $isRejected
                                        ? (float) ($item['original_rate'] ?? $item['rate'] ?? 0)
                                        : (float) ($item['rate'] ?? 0);
                                    $itemAddons = collect(
                                        $item['addons'] ??
                                            $item['order_item_addons'] ??
                                            $item['orderItemAddons'] ??
                                            []
                                    )->filter(function ($addon) {
                                        return !empty(trim((string) ($addon['name'] ?? ($addon['addon_name'] ?? ''))));
                                    });
                                    $addonTotal = $itemAddons->sum(function ($addon) {
                                        $addonQty = max((int) ($addon['quantity'] ?? 1), 1);
                                        $addonPrice = (float) ($addon['price'] ?? 0);
                                        $masterPrice = max((float) data_get($addon, 'masterAddon.price', 0), 0);
                                        $menuItemAddonPrice = max((float) ($addon['menu_item_addon_price'] ?? 0), 0);
                                        $addonPrice =
                                            $addonPrice > 0 ? $addonPrice : max($masterPrice, $menuItemAddonPrice);
                                        return (float) ($addon['total'] ?? $addonPrice * $addonQty);
                                    });
                                    $itemBaseAmount =
                                        $isRejected
                                            ? 0
                                            : (float) ($item['base_amount'] ?? $itemRate * (float) $item['qty']);
                                    $itemDiscount = $isRejected ? 0 : (float) ($item['discount'] ?? 0);
                                @endphp
                                <tr data-row-kind="item" data-item-id="{{ $item['id'] ?? '' }}"
                                    data-item-name="{{ $item['name'] ?? '' }}"
                                    data-item-status="{{ $item['status'] ?? '' }}"
                                    data-item-is-rejected="{{ $isRejected ? '1' : '0' }}"
                                    data-item-rejection-reason="{{ $rejectionReason }}"
                                    data-item-rate="{{ $itemRate }}" data-item-qty="{{ $item['qty'] }}"
                                    data-item-base-amount="{{ $itemBaseAmount }}"
                                    data-item-addon-total="{{ $addonTotal }}"
                                    data-item-line-total-before-discount="{{ $itemBaseAmount }}"
                                    data-item-addons='@json($itemAddons->values())'
                                    data-item-total="{{ max($itemBaseAmount - $itemDiscount, 0) }}"
                                    class="border-b border-slate-300 last:border-b-0 transition-colors hover:bg-slate-50 {{ $isRejected ? 'bg-slate-50/80 text-slate-400' : '' }}">
                                    <td class="border-r border-slate-200 px-4 py-2 font-medium text-slate-700">
                                        {{ $index + 1 }}</td>
                                    <td class="border-r border-slate-200 px-4 py-2 {{ $isRejected ? 'text-slate-500' : 'font-medium text-slate-950' }}">
                                        <div class="{{ $isRejected ? 'font-medium text-slate-500' : 'font-medium text-slate-950' }}">
                                            {{ $item['name'] }}
                                        </div>
                                        @if ($isRejected && $rejectionReason !== '')
                                            <div class="mt-1 flex items-start gap-1.5 text-[11px] leading-4 text-rose-500">
                                                <i class="fas fa-circle-xmark mt-0.5 text-[10px] text-rose-500"
                                                    aria-hidden="true"></i>
                                                <span class="break-words">{{ $rejectionReason }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td
                                        class="border-r border-slate-200 px-4 py-2 text-center font-medium text-slate-950">
                                        {{ $item['qty'] }}</td>
                                    <td class="border-r border-slate-200 px-4 py-2 font-medium text-slate-700">Rs
                                        {{ number_format($itemRate, 0) }}</td>
                                    <td class="border-r border-slate-200 px-4 py-2 font-medium text-slate-400"
                                        data-discount-cell data-item-base-amount="{{ $itemBaseAmount }}"
                                        data-item-addon-total="{{ $addonTotal }}"
                                        data-item-line-total-before-discount="{{ $itemBaseAmount }}"
                                        data-item-discount-amount="{{ number_format($itemDiscount, 2, '.', '') }}"
                                        data-item-discount-percent="{{ number_format($itemBaseAmount > 0 ? ($itemDiscount / $itemBaseAmount) * 100 : 0, 2, '.', '') }}">
                                        <div data-discount-mode="amount" class="flex items-center gap-2">
                                            <span class="text-slate-400">Rs</span>
                                            <input type="text" inputmode="decimal"
                                                class="w-full min-w-0 bg-transparent text-right text-sm font-medium text-slate-400 outline-none"
                                                data-discount-input="amount"
                                                value="{{ number_format($itemDiscount, 2, '.', '') }}" {{ $isRejected ? 'disabled aria-disabled=true' : '' }}>
                                        </div>
                                        <div data-discount-mode="percent" class="hidden items-center gap-2">
                                            <input type="text" inputmode="decimal"
                                                class="w-full min-w-0 bg-transparent text-right text-sm font-medium text-slate-400 outline-none"
                                                data-discount-input="percent"
                                                value="{{ number_format($itemBaseAmount > 0 ? ($itemDiscount / $itemBaseAmount) * 100 : 0, 2, '.', '') }}" {{ $isRejected ? 'disabled aria-disabled=true' : '' }}>
                                            <span class="text-slate-400">%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 font-medium text-slate-700">
                                        <span data-item-total-value>Rs
                                            {{ number_format(max($itemBaseAmount - $itemDiscount, 0), 0) }}</span>
                                    </td>
                                </tr>
                                @if ($itemAddons->isNotEmpty())
                                    @foreach ($itemAddons as $addon)
                                        @php
                                            $addonQty = max((int) ($addon['quantity'] ?? 1), 1);
                                            $addonPrice = (float) ($addon['price'] ?? 0);
                                            $masterPrice = max((float) data_get($addon, 'masterAddon.price', 0), 0);
                                            $menuItemAddonPrice = max(
                                                (float) ($addon['menu_item_addon_price'] ?? 0),
                                                0,
                                            );
                                            $addonPrice =
                                                $addonPrice > 0 ? $addonPrice : max($masterPrice, $menuItemAddonPrice);
                                            $addonName = trim(
                                                (string) ($addon['name'] ??
                                                    ($addon['addon_name'] ??
                                                        data_get($addon, 'masterAddon.name', 'Addon'))),
                                            );
                                            $addonName = preg_replace('/^[↳↲]+\s*/u', '', $addonName) ?? $addonName;
                                            $addonBaseAmount =
                                                (float) ($addon['base_amount'] ?? $addonPrice * $addonQty);
                                            $addonDiscount =
                                                (float) ($addon['applied_discount'] ?? ($addon['discount'] ?? 0));
                                        @endphp
                                        <tr data-row-kind="addon" data-parent-item-id="{{ $item['id'] ?? '' }}"
                                            data-parent-item-is-rejected="{{ $isRejected ? '1' : '0' }}"
                                            data-addon-name="{{ $addonName }}"
                                            data-addon-id="{{ $addon['id'] ?? ($addon['menu_item_addon_id'] ?? '') }}"
                                            data-item-rate="{{ $addonPrice }}" data-item-qty="{{ $addonQty }}"
                                            data-item-base-amount="{{ $addonBaseAmount }}" data-item-addon-total="0"
                                            data-item-line-total-before-discount="{{ $addonBaseAmount }}"
                                            data-item-total="{{ max($addonBaseAmount - $addonDiscount, 0) }}"
                                            class="border-b border-slate-200 bg-slate-50/80 last:border-b-0 transition-colors hover:bg-slate-50 {{ $isRejected ? 'text-slate-400 opacity-80' : '' }}">
                                            <td class="border-r border-slate-200 px-4 py-2 font-medium text-slate-700">
                                            </td>
                                            <td class="border-r border-slate-200 px-4 py-2 font-medium text-slate-950">
                                                <div class="flex items-center gap-2 pl-4 text-slate-950">
                                                    <span class="shrink-0 text-slate-600">↳</span>
                                                    <div class="font-medium {{ $isRejected ? 'text-slate-500' : 'text-slate-950' }}">
                                                        {{ $addonName }}@if ($addonQty > 1)
                                                            x{{ $addonQty }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="border-r border-slate-200 px-4 py-2 text-center font-medium text-slate-950">
                                                {{ $addonQty }}
                                            </td>
                                            <td class="border-r border-slate-200 px-4 py-2 font-medium text-slate-700">
                                                Rs {{ number_format($addonPrice, 0) }}
                                            </td>
                                            <td class="border-r border-slate-200 px-4 py-2 font-medium text-slate-400"
                                                data-discount-cell data-item-base-amount="{{ $addonBaseAmount }}"
                                                data-item-addon-total="0"
                                                data-item-line-total-before-discount="{{ $addonBaseAmount }}"
                                                data-item-discount-amount="{{ number_format($addonDiscount, 2, '.', '') }}"
                                                data-item-discount-percent="{{ number_format($addonBaseAmount > 0 ? ($addonDiscount / $addonBaseAmount) * 100 : 0, 2, '.', '') }}">
                                                <div data-discount-mode="amount" class="flex items-center gap-2">
                                                    <span class="text-slate-400">Rs</span>
                                                    <input type="text" inputmode="decimal"
                                                        class="w-full min-w-0 bg-transparent text-right text-sm font-medium text-slate-400 outline-none"
                                                        data-discount-input="amount"
                                                        value="{{ number_format($addonDiscount, 2, '.', '') }}" {{ $isRejected ? 'disabled aria-disabled=true' : '' }}>
                                                </div>
                                                <div data-discount-mode="percent" class="hidden items-center gap-2">
                                                    <input type="text" inputmode="decimal"
                                                        class="w-full min-w-0 bg-transparent text-right text-sm font-medium text-slate-400 outline-none"
                                                        data-discount-input="percent"
                                                        value="{{ number_format($addonBaseAmount > 0 ? ($addonDiscount / $addonBaseAmount) * 100 : 0, 2, '.', '') }}" {{ $isRejected ? 'disabled aria-disabled=true' : '' }}>
                                                    <span class="text-slate-400">%</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 font-medium text-slate-700">
                                                <span data-item-total-value>Rs
                                                    {{ number_format(max($addonBaseAmount - $addonDiscount, 0), 0) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <script>
                (function() {
                    const root = document.querySelector('[data-pos-discount-root]');
                    if (!root) return;

                    const buttons = root.querySelectorAll('[data-discount-mode-btn]');
                    const modeCells = () => root.querySelectorAll('[data-discount-cell]');
                    const rowTotals = () => root.querySelectorAll('[data-item-total-value]');

                    const parseNumber = (value) => {
                        const cleaned = String(value ?? '').replace(/,/g, '').trim();
                        const number = Number(cleaned);
                        return Number.isFinite(number) ? number : 0;
                    };

                    const getItemTotals = () => {
                        let itemBaseTotal = 0;
                        let itemDiscountTotal = 0;

                        root.querySelectorAll('[data-discount-cell]').forEach((cell) => {
                            const row = cell.closest('tr');
                            if (row?.dataset?.parentItemIsRejected === '1') {
                                return;
                            }

                            const baseAmount = parseNumber(
                                cell.dataset.itemLineTotalBeforeDiscount ||
                                cell.dataset.itemBaseAmount
                            );
                            const discountAmount = parseNumber(cell.dataset.itemDiscountAmount);
                            itemBaseTotal += baseAmount;
                            itemDiscountTotal += discountAmount;
                        });

                        return {
                            itemBaseTotal,
                            itemDiscountTotal,
                            subtotalAfterItemDiscount: Math.max(itemBaseTotal - itemDiscountTotal, 0),
                        };
                    };

                    const getSubtotalAfterItemDiscount = () => {
                        return getItemTotals().subtotalAfterItemDiscount;
                    };

                    const updateSummary = (overallDiscountOverride = null, activeField = null, preserveBlank = false) => {
                        const itemTotals = getItemTotals();
                        const itemBaseTotal = itemTotals.itemBaseTotal;
                        const itemDiscountTotal = itemTotals.itemDiscountTotal;
                        const subtotalAfterItemDiscount = itemTotals.subtotalAfterItemDiscount;
                        const overallDiscountInput = document.getElementById('billingLeftOverallDiscount');
                        const overallDiscountPercentInput = document.getElementById('billingLeftDiscountAmount');
                        const discountSource = window.billingDiscountSource || 'amount';
                        const sourceValue = discountSource === 'percent' ?
                            parseNumber(overallDiscountPercentInput?.value) :
                            parseNumber(overallDiscountInput?.value);
                        const overallDiscount = overallDiscountOverride === null ?
                            (discountSource === 'percent' ?
                                (itemBaseTotal > 0 ? (itemBaseTotal * sourceValue) / 100 : 0) :
                                sourceValue) :
                            parseNumber(overallDiscountOverride);
                        const overallDiscountPercent = itemBaseTotal > 0 ? (overallDiscount / itemBaseTotal) * 100 : 0;
                        const taxConfig = window.resolveBillingTaxConfig?.(window.currentOpenTable) || {
                            setting: String(window.billingTaxSetting || 'exclusive'),
                            ratePercent: Number(window.billingTaxRatePercent || 0),
                            rate: Number(window.billingTaxRate || 0),
                            label: String(window.billingTaxLabelName || 'Tax'),
                        };
                        const taxableBaseAmount = Math.max(subtotalAfterItemDiscount - overallDiscount, 0);
                        const taxSetting = String(taxConfig.setting || 'exclusive').toLowerCase() === 'inclusive' ?
                            'inclusive' :
                            'exclusive';
                        const taxRate = Number.isFinite(Number(taxConfig.rate)) ?
                            Number(taxConfig.rate) :
                            (Number(taxConfig.ratePercent) / 100);
                        const taxLabel = window.formatBillingTaxLabel?.(
                            taxConfig.label ||
                            ((taxSetting === 'inclusive' || Number(taxConfig.ratePercent) === 13) ? 'VAT' : 'Tax'),
                            taxConfig.ratePercent,
                            taxSetting
                        ) || String(
                            taxConfig.label ||
                            ((taxSetting === 'inclusive' || Number(taxConfig.ratePercent) === 13) ? 'VAT' : 'Tax')
                        );
                        let taxableAmount = taxableBaseAmount;
                        let taxAmount = 0;
                        let totalAmount = taxableBaseAmount;

                        if (taxSetting === 'inclusive') {
                            taxAmount = taxRate > 0 ? taxableBaseAmount - (taxableBaseAmount / (1 + taxRate)) : 0;
                            taxableAmount = Math.max(taxableBaseAmount - taxAmount, 0);
                            totalAmount = taxableBaseAmount;
                        } else {
                            taxableAmount = taxableBaseAmount;
                            taxAmount = taxableAmount * taxRate;
                            totalAmount = taxableAmount + taxAmount;
                        }

                        window.billingTaxConfig = {
                            setting: taxSetting,
                            ratePercent: Number(taxConfig.ratePercent || 0),
                            rate: taxRate,
                            label: taxLabel,
                        };
                        window.billingTaxSetting = taxSetting;
                        window.billingTaxRatePercent = Number(taxConfig.ratePercent || 0);
                        window.billingTaxRate = taxRate;
                        window.billingTaxLabelName = taxLabel;

                        const setValue = (id, value) => {
                            const el = document.getElementById(id);
                            if (!el) return;
                            if ('value' in el) {
                                el.value = value;
                            } else {
                                el.textContent = value;
                            }
                        };

                        setValue('billingLeftItemTotal', `Rs ${itemBaseTotal.toFixed(2)}`);
                        setValue('billingLeftItemDiscountAmount', `Rs ${itemDiscountTotal.toFixed(2)}`);
                        setValue('billingLeftSubTotal', `Rs ${itemBaseTotal.toFixed(2)}`);
                        setValue('billingLeftItemCount', `${root.querySelectorAll('tr[data-row-kind="item"]').length} Items`);
                        const itemDiscountRow = document.getElementById('billingLeftItemDiscountRow');
                        if (itemDiscountRow) {
                            const shouldShow = itemDiscountTotal > 0;
                            itemDiscountRow.classList.toggle('hidden', !shouldShow);
                            itemDiscountRow.hidden = !shouldShow;
                            itemDiscountRow.style.display = shouldShow ? '' : 'none';
                        }
                        window.billingDiscountSubtotalAfterItemDiscount = subtotalAfterItemDiscount;
                        if (!preserveBlank) {
                            if (discountSource === 'percent') {
                                setValue('billingLeftOverallDiscount', overallDiscount.toFixed(2));
                            } else if (discountSource === 'amount') {
                                setValue('billingLeftDiscountAmount', overallDiscountPercent.toFixed(2));
                            } else {
                                setValue('billingLeftDiscountAmount', overallDiscountPercent.toFixed(2));
                                setValue('billingLeftOverallDiscount', overallDiscount.toFixed(2));
                            }
                        }
                        setValue('billingLeftTaxableAmount', `Rs ${taxableAmount.toFixed(2)}`);
                        setValue('billingLeftTaxLabelText', taxLabel);
                        setValue('billingLeftNoTax', `Rs ${taxAmount.toFixed(2)}`);
                        setValue('billingLeftGrandTotal', `Rs ${totalAmount.toFixed(2)}`);
                        window.billingItemDiscountTotal = itemDiscountTotal;
                        window.billingOverallDiscountAmount = overallDiscount;
                        window.billingTaxAmount = taxAmount;
                        window.billingGrandTotalAmount = totalAmount;
                        window.billingEstimateInvoiceData = {
                            ...(window.billingEstimateInvoiceData || {}),
                            itemBaseTotal,
                            itemDiscountTotal,
                            subtotal: itemBaseTotal,
                            subtotalAfterItemDiscount,
                            overallDiscountAmount: overallDiscount,
                            taxableAmount,
                            taxAmount,
                            grandTotal: totalAmount,
                            taxSetting,
                            taxRatePercent: Number(taxConfig.ratePercent || 0),
                            taxLabel,
                        };

                        if (typeof window.syncBillingPaymentMode === 'function') {
                            try {
                                window.syncBillingPaymentMode(totalAmount);
                            } catch (error) {
                                console.warn('Billing payment mode sync failed', error);
                            }
                        } else {
                            setValue('billingTenderAmount', '');
                            setValue('billingChangeAmount', '-0.00');
                        }

                        const tenderAmount = parseNumber(document.getElementById('billingTenderAmount')?.value);
                        const changeAmount = Math.max(tenderAmount - totalAmount, 0);
                        window.billingTenderAmountValue = tenderAmount;
                        window.billingChangeAmountValue = changeAmount;
                        window.billingEstimateInvoiceData = {
                            ...(window.billingEstimateInvoiceData || {}),
                            tenderAmount,
                            changeAmount,
                        };
                        window.dispatchEvent(new CustomEvent('billing-estimate-invoice-updated', {
                            detail: {
                                itemBaseTotal,
                                itemDiscountTotal,
                                subtotal: itemBaseTotal,
                                subtotalAfterItemDiscount,
                                overallDiscountAmount: overallDiscount,
                                taxableAmount,
                                taxAmount,
                                grandTotal: totalAmount,
                                taxSetting,
                                taxRatePercent: Number(taxConfig.ratePercent || 0),
                                taxLabel,
                                tenderAmount,
                                changeAmount,
                            },
                        }));

                        try {
                            window.requestBillingEstimateInvoiceSync?.();
                        } catch (error) {
                            console.warn('Billing estimate invoice sync failed', error);
                        }

                        const rightItemRows = Array.from(root.querySelectorAll('tr[data-row-kind="item"]'));
                        const rightTotalQty = rightItemRows.reduce((sum, row) => {
                            return sum + parseNumber(row?.dataset?.itemQty || row?.children?.[2]?.textContent);
                        }, 0);
                        const rightItemCountLabel = rightItemRows.length > 0 ?
                            `${rightItemRows.length}/${Math.max(rightTotalQty, 1)}` :
                            '0/0';
                        const setRightText = (id, value) => {
                            const el = document.getElementById(id);
                            if (el) {
                                el.textContent = value;
                            }
                        };
                        const rightItemDiscountRow = document.getElementById('billingRightItemDiscountRow');

                        setRightText('billingRightItemCount', rightItemCountLabel);
                        setRightText('billingRightItemTotal', `Rs ${itemBaseTotal.toFixed(0)}`);
                        setRightText('billingRightLoyaltyDiscount', `Rs ${itemDiscountTotal.toFixed(2)}`);
                        if (rightItemDiscountRow) {
                            const shouldShow = itemDiscountTotal > 0;
                            rightItemDiscountRow.classList.toggle('hidden', !shouldShow);
                            rightItemDiscountRow.hidden = !shouldShow;
                            rightItemDiscountRow.style.display = shouldShow ? '' : 'none';
                        }
                        setRightText('billingRightSubTotal', `Rs ${itemBaseTotal.toFixed(2)}`);
                        setRightText('billingRightDiscount', `Rs ${overallDiscount.toFixed(2)}`);
                        setRightText('billingRightManualDiscount', `Rs ${taxAmount.toFixed(2)}`);
                        setRightText('billingRightTotalAmount', `Rs ${totalAmount.toFixed(2)}`);
                        setRightText('billingNetSalesAmount', `Rs ${totalAmount.toFixed(2)}`);
                        setRightText('billingRightTenderAmount', `Rs ${tenderAmount.toFixed(2)}`);
                        setRightText('billingRightChangeAmount', `-Rs ${changeAmount.toFixed(2)}`);
                        window.billingEstimateInvoiceData = {
                            ...(window.billingEstimateInvoiceData || {}),
                            itemBaseTotal,
                            itemDiscountTotal,
                            subtotal: itemBaseTotal,
                            subtotalAfterItemDiscount,
                            overallDiscountAmount: overallDiscount,
                            taxableAmount,
                            taxAmount,
                            grandTotal: totalAmount,
                            tenderAmount,
                            changeAmount,
                        };

                    };

                    const syncCell = (cell) => {
                        const amountView = cell.querySelector('[data-discount-mode="amount"]');
                        const percentView = cell.querySelector('[data-discount-mode="percent"]');
                        const amountInput = cell.querySelector('[data-discount-input="amount"]');
                        const percentInput = cell.querySelector('[data-discount-input="percent"]');
                        const baseAmount = parseNumber(cell.dataset.itemBaseAmount);
                        const lineTotalBeforeDiscount = parseNumber(
                            cell.dataset.itemLineTotalBeforeDiscount ||
                            baseAmount
                        );
                        const amount = parseNumber(cell.dataset.itemDiscountAmount);
                        const percent = lineTotalBeforeDiscount > 0 ? (amount / lineTotalBeforeDiscount) * 100 : 0;

                        if (amountInput && amountInput !== document.activeElement) {
                            amountInput.value = amount.toFixed(2);
                        }
                        if (percentInput && percentInput !== document.activeElement) {
                            percentInput.value = percent.toFixed(2);
                        }
                        if (amountView && percentView) {
                            amountView.classList.toggle('hidden', root.dataset.discountMode !== 'amount');
                            amountView.classList.toggle('flex', root.dataset.discountMode === 'amount');
                            percentView.classList.toggle('hidden', root.dataset.discountMode !== 'percent');
                            percentView.classList.toggle('flex', root.dataset.discountMode === 'percent');
                        }

                        const totalEl = cell.parentElement?.querySelector('[data-item-total-value]');
                        if (totalEl) {
                            totalEl.textContent = `Rs ${Math.max(baseAmount - amount, 0).toFixed(0)}`;
                        }
                    };

                    const setButtonState = (mode) => {
                        buttons.forEach((button) => {
                            const isActive = button.dataset.discountModeBtn === mode;
                            button.classList.toggle('bg-rose-500', isActive);
                            button.classList.toggle('text-white', isActive);
                            button.classList.toggle('bg-white', !isActive);
                            button.classList.toggle('text-slate-400', !isActive);
                        });
                    };

                    const setCellState = (mode) => {
                        modeCells().forEach((cell) => {
                            const amount = cell.querySelector('[data-discount-mode="amount"]');
                            const percent = cell.querySelector('[data-discount-mode="percent"]');
                            if (amount) amount.classList.toggle('hidden', mode !== 'amount');
                            if (amount) amount.classList.toggle('flex', mode === 'amount');
                            if (percent) percent.classList.toggle('hidden', mode !== 'percent');
                            if (percent) percent.classList.toggle('flex', mode === 'percent');
                            syncCell(cell);
                        });
                        updateSummary();
                    };

                    const applyMode = (mode) => {
                        root.dataset.discountMode = mode;
                        setButtonState(mode);
                        setCellState(mode);
                        window.dispatchEvent(new CustomEvent('billing-discount-mode-changed', {
                            detail: {
                                mode
                            }
                        }));
                    };

                    window.updateBillingDiscountMode = applyMode;

                    buttons.forEach((button) => {
                        button.addEventListener('click', () => {
                            applyMode(button.dataset.discountModeBtn || 'amount');
                        });
                    });

                    root.addEventListener('input', (event) => {
                        const input = event.target.closest('[data-discount-input]');
                        if (!input) return;

                        const cell = input.closest('[data-discount-cell]');
                        if (!cell) return;

                        const baseAmount = parseNumber(cell.dataset.itemBaseAmount);
                        const lineTotalBeforeDiscount = parseNumber(
                            cell.dataset.itemLineTotalBeforeDiscount ||
                            baseAmount
                        );
                        const amountInput = cell.querySelector('[data-discount-input="amount"]');
                        const percentInput = cell.querySelector('[data-discount-input="percent"]');

                        if (input.dataset.discountInput === 'amount') {
                            const amount = parseNumber(input.value);
                            const percent = lineTotalBeforeDiscount > 0 ? (amount / lineTotalBeforeDiscount) * 100 : 0;
                            cell.dataset.itemDiscountAmount = String(amount);
                            cell.dataset.itemDiscountPercent = String(percent);
                            if (percentInput && percentInput !== input) {
                                percentInput.value = percent.toFixed(2);
                            }
                        } else {
                            const percent = parseNumber(input.value);
                            const amount = (lineTotalBeforeDiscount * percent) / 100;
                            cell.dataset.itemDiscountAmount = String(amount);
                            cell.dataset.itemDiscountPercent = String(percent);
                            if (amountInput && amountInput !== input) {
                                amountInput.value = amount.toFixed(2);
                            }
                        }

                        syncCell(cell);
                        updateSummary();
                    });

                    const bindSummaryDiscountInputs = () => {
                        const overallDiscountInput = document.getElementById('billingLeftOverallDiscount');
                        const overallDiscountPercentInput = document.getElementById('billingLeftDiscountAmount');
                        if (!overallDiscountInput || !overallDiscountPercentInput) return false;
                        if (overallDiscountInput.dataset.discountBound === '1' ||
                            overallDiscountPercentInput.dataset.discountBound === '1') {
                            return true;
                        }

                        const syncDiscountFields = (source, rawValue) => {
                            const totalBase = getItemTotals().itemBaseTotal;
                            const rawText = String(rawValue ?? '').replace(/,/g, '').trim();
                            if (rawText === '') {
                                window.billingDiscountSource = source;
                                overallDiscountInput.value = '';
                                overallDiscountPercentInput.value = '';
                                updateSummary(0, source, true);
                                return;
                            }

                            const value = parseNumber(rawText);
                            window.billingDiscountSource = source;
                            const amount = source === 'percent' ?
                                (totalBase > 0 ? (totalBase * value) / 100 : 0) :
                                value;
                            const percent = totalBase > 0 ? (amount / totalBase) * 100 : 0;

                            if (source === 'percent') {
                                overallDiscountInput.value = amount.toFixed(2);
                                updateSummary(amount, 'percent');
                            } else {
                                overallDiscountPercentInput.value = percent.toFixed(2);
                                updateSummary(amount, 'amount');
                            }
                        };

                        overallDiscountPercentInput.addEventListener('keydown', (event) => {
                            if ((event.key === 'Backspace' || event.key === 'Delete') &&
                                /^0*\.?0*$/.test(overallDiscountPercentInput.value)) {
                                event.preventDefault();
                                overallDiscountPercentInput.value = '';
                                syncDiscountFields('percent', '');
                            }
                        });
                        overallDiscountPercentInput.addEventListener('input', (event) => {
                            syncDiscountFields('percent', event.target.value);
                        });

                        overallDiscountInput.addEventListener('keydown', (event) => {
                            if ((event.key === 'Backspace' || event.key === 'Delete') &&
                                /^0*\.?0*$/.test(overallDiscountInput.value)) {
                                event.preventDefault();
                                overallDiscountInput.value = '';
                                syncDiscountFields('amount', '');
                            }
                        });
                        overallDiscountInput.addEventListener('input', (event) => {
                            syncDiscountFields('amount', event.target.value);
                        });

                        overallDiscountInput.dataset.discountBound = '1';
                        overallDiscountPercentInput.dataset.discountBound = '1';
                        return true;
                    };

                    const initSummaryDiscountBindings = () => {
                        if (bindSummaryDiscountInputs()) {
                            applyMode(root.dataset.discountMode || 'amount');
                        }
                    };

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initSummaryDiscountBindings, {
                            once: true
                        });
                    } else {
                        initSummaryDiscountBindings();
                    }
                })();
            </script>

            <script>
                (function() {
                    const initRemarksBinding = () => {
                        const remarksInput = document.getElementById('billingInvoiceRemarks');
                        if (!remarksInput) return;

                        const syncRemarks = () => {
                            const remarks = String(remarksInput.value ?? '').trim();
                            window.billingInvoiceRemarks = remarks;
                            window.billingEstimateInvoiceData = {
                                ...(window.billingEstimateInvoiceData || {}),
                                notesSnapshot: remarks,
                            };

                            window.dispatchEvent(new CustomEvent('billing-estimate-invoice-updated', {
                                detail: {
                                    notesSnapshot: remarks,
                                },
                            }));
                        };

                        remarksInput.addEventListener('input', syncRemarks);
                        remarksInput.addEventListener('change', syncRemarks);
                        syncRemarks();
                    };

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initRemarksBinding, {
                            once: true
                        });
                    } else {
                        initRemarksBinding();
                    }
                })();
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const root = document.querySelector('[data-party-mode-root]');
                    if (!root) return;

                    const buttons = root.querySelectorAll('[data-party-mode-btn]');
                    const searchInput = root.querySelector('[data-party-search-input]');
                    if (!buttons.length || !searchInput) return;

                    const activeClasses = ['bg-orange-100', 'text-orange-600'];
                    const inactiveClasses = ['bg-transparent', 'text-slate-500'];

                    const applyPartyMode = (mode) => {
                        buttons.forEach((button) => {
                            const isActive = button.dataset.partyModeBtn === mode;
                            button.classList.remove(...activeClasses, ...inactiveClasses);
                            button.classList.add(...(isActive ? activeClasses : inactiveClasses));
                        });

                        searchInput.placeholder = mode === 'staff' ?
                            'Search staff by name or phone...' :
                            'Search customer by name or phone...';
                    };

                    window.applyBillingPartyMode = applyPartyMode;
                    applyPartyMode('customer');
                });
            </script>

            <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div>
                    <div class="rounded-lg border border-slate-300 mt-2 p-3" data-party-mode-root>
                        <div
                            class="mt-2 grid grid-cols-2 overflow-hidden rounded-lg border border-slate-200 bg-slate-50 p-1 text-xs font-bold">
                            <button type="button"
                                class="rounded-lg bg-orange-100 px-3 py-2 text-orange-600 cursor-pointer"
                                data-party-mode-btn="customer"
                                onclick="window.applyBillingPartyMode && window.applyBillingPartyMode('customer')">Customer</button>
                            <button type="button"
                                class="rounded-lg px-3 py-2 text-slate-500 transition hover:text-slate-800 cursor-pointer"
                                data-party-mode-btn="staff"
                                onclick="window.applyBillingPartyMode && window.applyBillingPartyMode('staff')">Staff</button>
                        </div>

                        <label class="mt-4 block">
                            <div
                                class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-slate-500">
                                <i class="fas fa-search text-xs"></i>
                                <input type="text" data-party-search-input
                                    class="w-full bg-transparent text-sm outline-none placeholder:text-slate-400"
                                    placeholder="Search customer by name or phone...">
                                <i class="fas fa-user-plus text-xs cursor-pointer"></i>
                            </div>
                        </label>
                    </div>

                    <div class="mt-2">
                        <textarea id="billingInvoiceRemarks" name="notes_snapshot" rows="3"
                            class="w-full resize-none rounded-lg border border-slate-300 px-3 py-3 text-sm text-slate-700 outline-none"
                            placeholder="Add remarks to invoice"></textarea>
                    </div>
                </div>

                <div>
                    <div class="mt-2 space-y-4">
                        <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-utensils text-slate-400"></i>
                                Item total
                            </span>
                            <span id="billingLeftItemTotal" class="text-slate-950">Rs
                                {{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                            <span>Sub Total</span>
                            <span id="billingLeftSubTotal" class="text-slate-950">Rs
                                {{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-semibold text-slate-700">Discount (−)</span>
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex w-20 items-center rounded-sm border border-slate-200 bg-white px-3 py-1.5">
                                    <input id="billingLeftDiscountAmount" type="text" value="0.00"
                                        inputmode="decimal" onfocus="this.select()"
                                        class="w-full bg-transparent text-center text-sm font-semibold text-slate-500 outline-none">
                                    <span class="ml-2 text-sm font-semibold text-slate-500">%</span>
                                </div>
                                <i class="fas fa-link text-xs text-slate-400"></i>
                                <div
                                    class="flex w-36 items-center rounded-sm border border-slate-200 bg-white px-3 py-1.5">
                                    <span class="mr-2 text-sm font-semibold text-slate-500">Rs</span>
                                    <input id="billingLeftOverallDiscount" type="text" inputmode="decimal"
                                        onfocus="this.select()" value="{{ number_format($overallDiscount, 2) }}"
                                        class="w-full bg-transparent text-sm font-semibold text-slate-500 outline-none">
                                </div>
                            </div>
                        </div>

                        <div id="billingLeftItemDiscountRow"
                            class="hidden flex items-center justify-between text-sm font-semibold text-slate-700"
                            aria-hidden="true">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-tags text-slate-400"></i>
                                Item Discount
                            </span>
                            <span id="billingLeftItemDiscountAmount" class="text-slate-950">Rs 0.00</span>
                        </div>

                        <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                            <span class="text-sm font-bold text-slate-900">Taxable Amount</span>
                            <span id="billingLeftTaxableAmount" class="text-sm font-bold text-slate-950">Rs
                                {{ number_format($taxableAmount, 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-plus text-slate-400"></i>
                                <span id="billingLeftTaxLabelText">Tax</span>
                            </span>
                            <span id="billingLeftNoTax" class="text-slate-950">Rs 0.00</span>
                        </div>

                        <div class="flex items-center justify-between gap-3 border-t border-slate-200 pt-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-slate-900">Total Amount</span>
                                <button type="button"
                                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 cursor-pointer">
                                    Add Round Off/Tips?
                                </button>
                            </div>
                            <span id="billingLeftGrandTotal" class="text-sm font-black text-slate-950">Rs
                                {{ number_format($grandTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-t border-dashed border-slate-400 mb-3">
                <div class="grid grid-cols-1 gap-4 xl:grid-cols-2 mt-3">
                    <div class="order-2 xl:order-1">
                        <div class="hidden rounded-lg border border-slate-300 p-2" data-tender-section>
                            <label class="block">
                                <span id="billingTenderLabel"
                                    class="text-xs font-bold uppercase tracking-wider text-slate-500">Tender Amount
                                    (Cash Received)</span>
                                <div
                                    class="mt-2 flex items-center rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                                    <span class="mr-2 text-sm font-semibold text-slate-400">Rs.</span>
                                    <input id="billingTenderAmount" type="text" value=""
                                        class="w-full bg-transparent text-base font-bold text-slate-950 outline-none">
                                </div>

                                <div class="mt-3">
                                    <div class="flex items-center justify-between gap-3 text-red-800">
                                        <p class="text-xs font-bold uppercase tracking-wider text-red-700">
                                            Change to Return
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold">Rs.</span>
                                            <span id="billingChangeAmount"
                                                class="text-sm font-extrabold">-{{ number_format($change, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="order-1 xl:order-2">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="rounded-lg border border-slate-300 p-3" data-payment-mode-root
                                data-payment-mode="{{ $paymentType }}">
                                <p class="text-sm font-semibold text-slate-700">Payment Mode <span
                                        class="text-rose-500">*</span></p>
                                <div
                                    class="mt-2 grid grid-cols-3 overflow-hidden rounded-lg border border-slate-200 bg-white p-1 text-xs font-semibold text-slate-700">
                                    <button type="button"
                                        class="rounded-sm bg-rose-100 px-4 py-1.5 text-rose-600 transition hover:bg-rose-50 cursor-pointer"
                                        data-payment-mode-btn="paid" aria-pressed="true">Paid</button>
                                    <button type="button"
                                        class="rounded-sm bg-white px-4 py-1.5 text-slate-700 transition hover:bg-slate-50 cursor-pointer"
                                        data-payment-mode-btn="unpaid" aria-pressed="false">Unpaid /
                                        Credit</button>
                                    <button type="button"
                                        class="rounded-sm bg-white px-4 py-1.5 text-slate-700 transition hover:bg-slate-50 cursor-pointer"
                                        data-payment-mode-btn="partial" aria-pressed="false">Partial</button>
                                </div>
                                <div class="mt-3 hidden space-y-3" data-unpaid-credit-section aria-hidden="true">
                                    <div class="flex items-center justify-between text-sm font-medium text-slate-700">
                                        <span data-unpaid-customer-label>Cash Customer</span>
                                        <span data-unpaid-receivable-amount class="font-semibold text-slate-950">Rs
                                            {{ number_format($grandTotal, 2) }}</span>
                                    </div>

                                    <div
                                        class="rounded-md bg-emerald-100 px-2 py-2 text-[10px] leading-[14px] text-emerald-700">
                                        <div class="flex items-start gap-2">
                                            <span
                                                class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full border border-emerald-200 bg-white text-[8px] font-bold text-emerald-600">
                                                <i class="fas fa-user-check"></i>
                                            </span>
                                            <p>By selecting customer, the above net sales amount will be added to same
                                                customer/staff as receivable.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 hidden space-y-3" data-partial-payment-section aria-hidden="true">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-slate-700">Multiple Payment</p>
                                            <span
                                                class="inline-flex h-4 w-4 items-center justify-center rounded-full border border-slate-300 text-[10px] font-bold leading-none text-slate-500">?</span>
                                        </div>
                                        <button type="button" data-multiple-payment-toggle aria-pressed="false"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full bg-slate-300 transition-colors duration-150 cursor-pointer">
                                            <span data-multiple-payment-knob
                                                class="inline-block h-5 w-5 translate-x-0.5 rounded-full bg-white shadow transition-transform duration-150"></span>
                                            <span class="sr-only">Toggle multiple payment</span>
                                        </button>
                                    </div>

                                    <div
                                        class="rounded-md bg-emerald-100 px-2 py-2 text-[10px] leading-[14px] text-emerald-700">
                                        <div class="flex items-start gap-2">
                                            <span
                                                class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full border border-emerald-200 bg-white text-[8px] font-bold text-emerald-600">
                                                <i class="fas fa-layer-group"></i>
                                            </span>
                                            <p>Turn this ON, if customer pay via multiple payment mode or make partial
                                                payments.</p>
                                        </div>
                                    </div>

                                    <input type="hidden" id="billingMultiplePaymentEnabled" value="0">
                                </div>

                                <div class="mt-3 hidden space-y-3" data-payment-method-section aria-hidden="true">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Payment
                                            Method</p>
                                        <span class="text-[10px] font-medium text-slate-400">Visible only for
                                            Paid</span>
                                    </div>

                                    <input type="hidden" id="billingSelectedPaymentMethod" value="">

                                    <div class="grid grid-cols-3 gap-2" data-payment-method-root
                                        data-payment-method="">
                                        <button type="button"
                                            class="inline-flex items-center gap-2 rounded-sm border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-800 transition hover:border-slate-300 hover:bg-slate-100 cursor-pointer"
                                            data-payment-method-btn="cash" aria-pressed="false">
                                            <span
                                                class="flex h-5 w-5 items-center justify-center rounded-md bg-white text-[7px] font-black text-orange-600 shadow-sm">CA</span>
                                            Cash
                                        </button>
                                        <button type="button"
                                            class="inline-flex items-center gap-2 rounded-sm border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-800 transition hover:border-slate-300 hover:bg-slate-100 cursor-pointer"
                                            data-payment-method-btn="fonepay_dynamic" aria-pressed="false">
                                            <span
                                                class="flex h-5 w-5 items-center justify-center rounded-md bg-white text-[7px] font-black text-slate-700 shadow-sm">QR</span>
                                            Fonepay
                                        </button>
                                        <button type="button"
                                            class="inline-flex items-center gap-2 rounded-sm border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-800 transition hover:border-slate-300 hover:bg-slate-100 cursor-pointer"
                                            data-payment-method-btn="card" aria-pressed="false">
                                            <span
                                                class="flex h-5 w-5 items-center justify-center rounded-md bg-indigo-100 text-[7px] font-black text-indigo-700">CA</span>
                                            Card
                                        </button>
                                        <button type="button"
                                            class="inline-flex items-center gap-2 rounded-sm border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-800 transition hover:border-slate-300 hover:bg-slate-100 cursor-pointer"
                                            data-payment-method-btn="nepal_pay" aria-pressed="false">
                                            <span
                                                class="flex h-5 w-5 items-center justify-center rounded-md bg-amber-100 text-[7px] font-black text-amber-700">NP</span>
                                            Nepal Pay
                                        </button>
                                        <button type="button"
                                            class="inline-flex items-center gap-2 rounded-sm border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-800 transition hover:border-slate-300 hover:bg-slate-100 cursor-pointer"
                                            data-payment-method-btn="bank_transfer" aria-pressed="false">
                                            <span
                                                class="flex h-5 w-5 items-center justify-center rounded-md bg-indigo-100 text-[7px] font-black text-indigo-700">BT</span>
                                            Bank Transfer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const root = document.querySelector('[data-payment-mode-root]');
                    if (!root) return;

                    const buttons = root.querySelectorAll('[data-payment-mode-btn]');
                    const tenderSection = document.querySelector('[data-tender-section]');
                    const tenderLabel = document.getElementById('billingTenderLabel');
                    const tenderInput = document.getElementById('billingTenderAmount');
                    const changeAmount = document.getElementById('billingChangeAmount');
                    const paymentModeLabel = document.getElementById('billingPaymentMode');
                    const paymentModeAmount = document.getElementById('billingPaymentModeAmount');
                    const paymentModeValue = document.getElementById('billingSelectedPaymentMode');
                    const unpaidCreditSection = document.querySelector('[data-unpaid-credit-section]');
                    const unpaidCustomerLabel = document.querySelector('[data-unpaid-customer-label]');
                    const unpaidReceivableAmount = document.querySelector('[data-unpaid-receivable-amount]');

                    if (!buttons.length || !tenderSection || !tenderLabel || !tenderInput || !changeAmount || !
                        paymentModeLabel || !paymentModeAmount || !unpaidCreditSection || !unpaidCustomerLabel || !
                        unpaidReceivableAmount) {
                        return;
                    }

                    const normalizeMode = (mode) => {
                        return ['paid', 'unpaid', 'partial'].includes(mode) ? mode : 'paid';
                    };

                    const parseNumber = (value) => {
                        const cleaned = String(value ?? '').replace(/[^0-9.-]/g, '').trim();
                        const number = Number(cleaned);
                        return Number.isFinite(number) ? number : 0;
                    };

                    const formatMoney = (value) => {
                        const number = Number(value ?? 0);
                        return number.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });
                    };

                    const paymentMethodLabels = {
                        cash: 'Cash',
                        fonepay_dynamic: 'Fonepay',
                        card: 'Card',
                        nepal_pay: 'Nepal Pay',
                        bank_transfer: 'Bank Transfer',
                    };

                    window.syncBillingPaymentSummary = (grandTotal = resolveGrandTotal()) => {
                        const paymentMode = String(window.billingPaymentMode || 'paid');
                        const paymentMethod = String(window.billingPaymentMethod || '').trim();
                        const summaryLabel = paymentMode === 'paid' ?
                            (paymentMethod ? (paymentMethodLabels[paymentMethod] || 'Unpaid') : 'Unpaid') :
                            'Unpaid';

                        paymentModeLabel.textContent = summaryLabel;
                        paymentModeAmount.textContent = `(Rs ${formatMoney(grandTotal)})`;
                    };

                    const resolveGrandTotal = (override = null) => {
                        if (override !== null && override !== undefined && override !== '') {
                            return Math.max(parseNumber(override), 0);
                        }

                        if (typeof window.billingGrandTotalAmount === 'number') {
                            return Math.max(window.billingGrandTotalAmount, 0);
                        }

                        return Math.max(parseNumber(document.getElementById('billingLeftGrandTotal')?.textContent), 0);
                    };

                    const setTenderEditableState = () => {
                        tenderInput.readOnly = false;
                        tenderInput.classList.add('cursor-text', 'text-slate-950');
                        tenderInput.classList.remove('cursor-not-allowed', 'text-slate-500');
                    };

                    const updateTenderChangeAmount = (grandTotal = resolveGrandTotal()) => {
                        const tenderAmount = parseNumber(tenderInput.value);
                        const changeAmountValue = Math.max(tenderAmount - grandTotal, 0);
                        changeAmount.textContent = `-${formatMoney(changeAmountValue)}`;
                    };

                    let currentMode = 'paid';

                    const renderPaymentMode = (grandTotal = resolveGrandTotal(), resetPartial = false) => {
                        const isPaid = currentMode === 'paid';
                        const isPartial = currentMode === 'partial';
                        const isUnpaid = currentMode === 'unpaid';
                        const customerName = document.getElementById('billingCustomerName')?.textContent?.trim() ||
                            'Cash Customer';

                        tenderSection.classList.toggle('hidden', isUnpaid);
                        tenderSection.setAttribute('aria-hidden', String(isUnpaid));
                        unpaidCreditSection.classList.toggle('hidden', !isUnpaid);
                        unpaidCreditSection.setAttribute('aria-hidden', String(!isUnpaid));
                        unpaidCustomerLabel.textContent = customerName;
                        unpaidReceivableAmount.textContent = `Rs ${formatMoney(grandTotal)}`;
                        tenderLabel.textContent = isPartial ?
                            'Tender Amount (Amount Paid Now)' :
                            'Tender Amount (Cash Received)';

                        if (currentMode === 'paid') {
                            tenderInput.placeholder = 'Enter amount';
                            setTenderEditableState();
                        } else if (isUnpaid) {
                            tenderInput.placeholder = 'Enter amount';
                            setTenderEditableState();
                        } else {
                            tenderInput.placeholder = 'Enter amount';
                            setTenderEditableState();
                            if (resetPartial) {
                                tenderInput.value = '';
                            }
                        }

                        buttons.forEach((button) => {
                            const isActive = button.dataset.paymentModeBtn === currentMode;
                            button.classList.toggle('bg-rose-100', isActive);
                            button.classList.toggle('text-rose-600', isActive);
                            button.classList.toggle('bg-white', !isActive);
                            button.classList.toggle('text-slate-700', !isActive);
                            button.setAttribute('aria-pressed', String(isActive));
                        });

                        updateTenderChangeAmount(grandTotal);

                        if (paymentModeValue) {
                            paymentModeValue.value = currentMode;
                        }

                        root.dataset.paymentMode = currentMode;
                        window.billingPaymentMode = currentMode;
                        window.billingGrandTotalAmount = grandTotal;
                        window.syncBillingPaymentSummary?.(grandTotal);
                        window.syncBillingPaymentMethodVisibility?.(currentMode);
                        window.dispatchEvent(new CustomEvent('billing-estimate-invoice-updated', {
                            detail: {
                                grandTotal,
                            },
                        }));
                    };

                    window.updateBillingPaymentMode = (mode = currentMode, options = {}) => {
                        const nextMode = normalizeMode(mode);
                        const grandTotal = resolveGrandTotal(options.grandTotal);
                        const resetPartial = options.resetPartial !== undefined ?
                            options.resetPartial :
                            nextMode === 'partial' && currentMode !== 'partial';

                        currentMode = nextMode;
                        renderPaymentMode(grandTotal, resetPartial);

                        window.dispatchEvent(new CustomEvent('billing-payment-mode-changed', {
                            detail: {
                                mode: currentMode,
                                grandTotal,
                                tenderAmount: parseNumber(tenderInput.value),
                            },
                        }));
                    };

                    window.syncBillingPaymentMode = (grandTotal = null) => {
                        renderPaymentMode(resolveGrandTotal(grandTotal), false);
                    };

                    tenderInput.addEventListener('input', () => {
                        updateTenderChangeAmount(resolveGrandTotal());
                    });

                    buttons.forEach((button) => {
                        button.addEventListener('click', () => {
                            window.updateBillingPaymentMode(button.dataset.paymentModeBtn || 'paid');
                        });
                    });

                    window.updateBillingPaymentMode(currentMode, {
                        grandTotal: window.billingGrandTotalAmount ?? resolveGrandTotal(),
                        resetPartial: false,
                    });
                });
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const section = document.querySelector('[data-payment-method-section]');
                    if (!section) return;

                    const root = section.querySelector('[data-payment-method-root]');
                    const buttons = root ? root.querySelectorAll('[data-payment-method-btn]') : [];
                    const hiddenInput = document.getElementById('billingSelectedPaymentMethod');
                    const partialSection = document.querySelector('[data-partial-payment-section]');
                    const partialToggle = document.querySelector('[data-multiple-payment-toggle]');
                    const partialKnob = document.querySelector('[data-multiple-payment-knob]');
                    const multiplePaymentInput = document.getElementById('billingMultiplePaymentEnabled');
                    if (!root || !buttons.length || !hiddenInput || !partialSection || !partialToggle || !partialKnob || !
                        multiplePaymentInput) return;

                    const allowedMethods = ['cash', 'fonepay_dynamic', 'card', 'nepal_pay', 'bank_transfer'];

                    const normalizeMethod = (value, allowBlank = false) => {
                        const normalized = String(value ?? '').trim();
                        if (!normalized && allowBlank) {
                            return '';
                        }

                        return allowedMethods.includes(normalized) ? normalized : '';
                    };

                    let currentMethod = normalizeMethod(root.dataset.paymentMethod || hiddenInput.value || '', true);
                    let currentPaymentMode = ['paid', 'unpaid', 'partial'].includes(window.billingPaymentMode) ?
                        window.billingPaymentMode :
                        (['paid', 'unpaid', 'partial'].includes(root.closest('[data-payment-mode-root]')?.dataset
                                ?.paymentMode) ?
                            root.closest('[data-payment-mode-root]')?.dataset?.paymentMode :
                            'paid');
                    let multiplePaymentEnabled = multiplePaymentInput.value === '1';

                    const applyButtonState = () => {
                        buttons.forEach((button) => {
                            const isActive = button.dataset.paymentMethodBtn === currentMethod;
                            button.classList.toggle('border-orange-200', isActive);
                            button.classList.toggle('bg-orange-100', isActive);
                            button.classList.toggle('text-orange-700', isActive);
                            button.classList.toggle('shadow-sm', isActive);
                            button.classList.toggle('border-slate-200', !isActive);
                            button.classList.toggle('bg-slate-50', !isActive);
                            button.classList.toggle('text-slate-800', !isActive);
                            button.setAttribute('aria-pressed', String(isActive));
                        });
                    };

                    const renderMultiplePaymentToggle = () => {
                        partialToggle.setAttribute('aria-pressed', String(multiplePaymentEnabled));
                        partialToggle.classList.toggle('bg-emerald-500', multiplePaymentEnabled);
                        partialToggle.classList.toggle('bg-slate-300', !multiplePaymentEnabled);
                        partialKnob.classList.toggle('translate-x-5', multiplePaymentEnabled);
                        partialKnob.classList.toggle('translate-x-0.5', !multiplePaymentEnabled);
                    };

                    const syncVisibility = (paymentMode = currentPaymentMode) => {
                        currentPaymentMode = ['paid', 'unpaid', 'partial'].includes(paymentMode) ?
                            paymentMode :
                            'paid';
                        const isPaid = currentPaymentMode === 'paid';
                        const isPartial = currentPaymentMode === 'partial';

                        section.classList.toggle('hidden', !isPaid);
                        section.hidden = !isPaid;
                        section.style.display = isPaid ? '' : 'none';
                        section.setAttribute('aria-hidden', String(!isPaid));

                        partialSection.classList.toggle('hidden', !isPartial);
                        partialSection.hidden = !isPartial;
                        partialSection.style.display = isPartial ? '' : 'none';
                        partialSection.setAttribute('aria-hidden', String(!isPartial));

                        if (isPaid) {
                            currentMethod = normalizeMethod(currentMethod);
                            hiddenInput.value = currentMethod;
                            root.dataset.paymentMethod = currentMethod;
                            window.billingPaymentMethod = currentMethod;
                            applyButtonState();
                        } else {
                            hiddenInput.value = '';
                            root.dataset.paymentMethod = currentMethod;
                            window.billingPaymentMethod = '';
                        }

                        if (!isPartial) {
                            multiplePaymentEnabled = false;
                            multiplePaymentInput.value = '0';
                        }

                        renderMultiplePaymentToggle();
                        window.syncBillingPaymentSummary?.(window.billingGrandTotalAmount ?? 0);
                    };

                    window.updateBillingPaymentMethod = (method = currentMethod, options = {}) => {
                        currentMethod = normalizeMethod(method, true);
                        root.dataset.paymentMethod = currentMethod;

                        if (currentPaymentMode === 'paid' && currentMethod) {
                            hiddenInput.value = currentMethod;
                            window.billingPaymentMethod = currentMethod;
                        } else {
                            hiddenInput.value = '';
                            window.billingPaymentMethod = '';
                        }

                        applyButtonState();
                        window.syncBillingPaymentSummary?.(window.billingGrandTotalAmount ?? 0);

                        if (options.silent !== true) {
                            window.dispatchEvent(new CustomEvent('billing-payment-method-changed', {
                                detail: {
                                    method: currentMethod,
                                    visible: currentPaymentMode === 'paid',
                                },
                            }));
                        }
                    };

                    window.syncBillingPaymentMethodVisibility = syncVisibility;

                    window.updateBillingMultiplePayment = (enabled = !multiplePaymentEnabled, options = {}) => {
                        multiplePaymentEnabled = Boolean(enabled);
                        multiplePaymentInput.value = multiplePaymentEnabled ? '1' : '0';
                        renderMultiplePaymentToggle();

                        if (options.silent !== true) {
                            window.dispatchEvent(new CustomEvent('billing-multiple-payment-changed', {
                                detail: {
                                    enabled: multiplePaymentEnabled,
                                    paymentMode: currentPaymentMode,
                                },
                            }));
                        }
                    };

                    buttons.forEach((button) => {
                        button.addEventListener('click', () => {
                            const method = button.dataset.paymentMethodBtn || 'cash';
                            const nextMethod = currentMethod === method ? '' : method;
                            window.updateBillingPaymentMethod(nextMethod);
                        });
                    });

                    partialToggle.addEventListener('click', () => {
                        if (currentPaymentMode !== 'partial') return;
                        window.updateBillingMultiplePayment(!multiplePaymentEnabled);
                    });

                    window.addEventListener('billing-payment-mode-changed', (event) => {
                        currentPaymentMode = ['paid', 'unpaid', 'partial'].includes(event?.detail?.mode) ?
                            event.detail.mode :
                            currentPaymentMode;
                        syncVisibility(currentPaymentMode);
                    });

                    applyButtonState();
                    renderMultiplePaymentToggle();
                    syncVisibility(currentPaymentMode);
                });
            </script>
        </div>
    </div>
</div>
