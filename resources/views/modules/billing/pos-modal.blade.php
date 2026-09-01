@php
    $tableNo = 'T-01';
    $token = 'RT-MU93ER-29';
    $invoiceNo = 'INV-1045';
    $invoiceDate = '26 May 2025, 06:25 PM';
    $cashier = auth()->user()->name ?? 'Food Panda (Admin)';
    $kotReference = 'KOT-250526-014';

    $items = $items ?? [];
    if (!is_array($items)) {
        $items = [];
    }
    $items = array_values($items);

    $items = array_map(function ($item) {
        $item = is_array($item) ? $item : (array) $item;
        $status = strtolower(trim((string) ($item['status'] ?? $item['item_status'] ?? '')));
        $isRejected = (bool) ($item['is_rejected'] ?? false) || in_array($status, ['rejected', 'cancelled'], true);
        $rejectionReason = trim((string) ($item['rejection_reason'] ?? $item['cancel_reason'] ?? $item['reason'] ?? ''));
        $qty = max((int) ($item['qty'] ?? 0), 0);
        $originalRate = max((float) ($item['rate'] ?? 0), 0);
        $rate = $isRejected ? 0.0 : $originalRate;

        $addonSource = $item['addons'] ?? $item['order_item_addons'] ?? $item['orderItemAddons'] ?? [];

        $addons = array_values(array_filter(array_map(function ($addon) {
            if (!is_array($addon)) {
                return null;
            }

            $quantity = max((int) ($addon['quantity'] ?? 1), 1);
            $price = (float) ($addon['price'] ?? 0);
            $masterPrice = max((float) data_get($addon, 'masterAddon.price', 0), 0);
            $menuItemAddonPrice = max((float) ($addon['menu_item_addon_price'] ?? 0), 0);
            $price = $price > 0 ? $price : max($masterPrice, $menuItemAddonPrice);
            $name = trim((string) ($addon['name'] ?? $addon['addon_name'] ?? data_get($addon, 'masterAddon.name', 'Addon')));
            $name = preg_replace('/^[↳↲]+\s*/u', '', $name) ?? $name;
            $discount = max((float) ($addon['discount'] ?? $addon['applied_discount'] ?? 0), 0);
            $baseAmount = $price * $quantity;

            return [
                'id' => (int) ($addon['id'] ?? $addon['menu_item_addon_id'] ?? 0),
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'discount' => $discount,
                'applied_discount' => $discount,
                'base_amount' => $baseAmount,
                'line_total_before_discount' => $baseAmount,
                'total' => max($baseAmount - $discount, 0),
            ];
        }, $addonSource), fn ($addon) => is_array($addon) && trim((string) ($addon['name'] ?? '')) !== ''));

        $addonTotal = $isRejected
            ? 0.0
            : array_sum(array_map(fn ($addon) => (float) ($addon['line_total_before_discount'] ?? ($addon['base_amount'] ?? ((float) ($addon['price'] ?? 0) * (int) ($addon['quantity'] ?? 1)))), $addons));
        $addonDiscountTotal = $isRejected
            ? 0.0
            : array_sum(array_map(fn ($addon) => max((float) ($addon['discount'] ?? $addon['applied_discount'] ?? 0), 0), $addons));
        $baseAmount = $isRejected ? 0.0 : (float) ($item['base_amount'] ?? ($qty * $rate));
        $lineTotalBeforeDiscount = $isRejected ? 0.0 : ($baseAmount + $addonTotal);
        $discount = $isRejected ? 0.0 : (float) ($item['discount'] ?? 0);
        $total = $isRejected ? 0.0 : max($lineTotalBeforeDiscount - $discount - $addonDiscountTotal, 0);

        $item['status'] = $isRejected ? 'rejected' : ($status !== '' ? $status : 'new');
        $item['is_rejected'] = $isRejected;
        $item['rejection_reason'] = $rejectionReason;
        $item['original_rate'] = $originalRate;
        $item['addons'] = $addons;
        $item['qty'] = $qty;
        $item['rate'] = $rate;
        $item['discount'] = $discount;
        $item['addon_total'] = $addonTotal;
        $item['addon_discount_total'] = $addonDiscountTotal;
        $item['base_amount'] = $baseAmount;
        $item['line_total_before_discount'] = $lineTotalBeforeDiscount;
        $item['total'] = $total;
        $item['amount'] = $total;
        return $item;
    }, $items);

    $subtotal = array_sum(array_map(fn($item) => (float) ($item['line_total_before_discount'] ?? (($item['base_amount'] ?? (($item['qty'] ?? 0) * ($item['rate'] ?? 0))) + ($item['addon_total'] ?? 0))), $items));
    $itemDiscount = 0.0;
    $overallDiscount = 0.0;
    $taxableAmount = $subtotal - $itemDiscount - $overallDiscount;
    $vat = round($taxableAmount * 0.13, 2);
    $serviceCharge = round($taxableAmount * 0.05, 2);
    $grandTotal = round($taxableAmount + $vat + $serviceCharge, 2);

    $paymentType = 'paid';
    $paymentMode = 'cash';
    $tendered = null;
    $change = 0;
@endphp

<script>
    window.formatBillingTaxLabel = window.formatBillingTaxLabel || function(label, ratePercent = 0, setting = 'exclusive') {
        const baseLabel = String(label ?? '').trim() || (
            String(setting).toLowerCase() === 'inclusive' || Number(ratePercent) === 13 ? 'VAT' : 'Tax'
        );
        const parsedRate = Number(ratePercent);

        if (!Number.isFinite(parsedRate) || parsedRate <= 0 || /%/.test(baseLabel)) {
            return baseLabel;
        }

        const formattedRate = String(Number(parsedRate.toFixed(2)));
        return `${baseLabel} (${formattedRate}%)`;
    };

    window.resolveBillingTaxConfig = window.resolveBillingTaxConfig || function(tableNumber = null, fallback = {}) {
        const parseNumber = (value) => {
            const cleaned = String(value ?? '').replace(/,/g, '').trim();
            const number = Number(cleaned);
            return Number.isFinite(number) ? number : 0;
        };

        const normalizeSetting = (value) => {
            const resolved = String(value ?? fallback.setting ?? window.billingTaxSetting ?? 'exclusive')
                .toLowerCase();
            return resolved === 'inclusive' ? 'inclusive' : 'exclusive';
        };

        const resolveCard = () => {
            const normalizedTableNumber = String(tableNumber ?? window.currentOpenTable ?? '').trim();
            if (normalizedTableNumber) {
                const escapedTableNumber = normalizedTableNumber.replace(/"/g, '\\"');
                const matchingCard = document.querySelector(
                    `.table-card[data-table-number="${escapedTableNumber}"]`
                );
                if (matchingCard) {
                    return matchingCard;
                }
            }

            if (window.currentOpenTable) {
                const escapedTableNumber = String(window.currentOpenTable).replace(/"/g, '\\"');
                const openCard = document.querySelector(`.table-card[data-table-number="${escapedTableNumber}"]`);
                if (openCard) {
                    return openCard;
                }
            }

            return document.querySelector('.table-card[data-branch-tax-setting]') || document.querySelector('.table-card');
        };

        const card = resolveCard();
        const ratePercent = parseNumber(
            card?.dataset?.branchTaxRate ??
            fallback.ratePercent ??
            fallback.taxRatePercent ??
            window.billingTaxRatePercent ??
            0
        );
        const setting = normalizeSetting(card?.dataset?.branchTaxSetting ?? fallback.setting);
        const label = String(
            card?.dataset?.branchTaxLabel ??
            fallback.label ??
            window.billingTaxLabelName ??
            ((setting === 'inclusive' || Number(ratePercent) === 13) ? 'VAT' : 'Tax')
        );
        const formattedLabel = window.formatBillingTaxLabel(label, ratePercent, setting);
        const config = {
            setting,
            ratePercent,
            rate: ratePercent / 100,
            label: formattedLabel,
        };

        window.billingTaxConfig = config;
        window.billingTaxSetting = config.setting;
        window.billingTaxRatePercent = config.ratePercent;
        window.billingTaxRate = config.rate;
        window.billingTaxLabelName = config.label;

        return config;
    };
</script>

<style>
    .billing-pos-no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .billing-pos-no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div id="billingPosModal" class="fixed inset-0 z-[300] hidden">
    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm" data-billing-modal-close></div>

    <button id="billingPosCloseBtn" type="button" data-billing-modal-close
        class="absolute top-2 left-4 z-[302] inline-flex h-9 w-9 items-center justify-center rounded-sm border border-slate-200 bg-white text-slate-500 shadow-xl transition-all duration-150 hover:bg-slate-50 hover:border-orange-500 hover:text-slate-800 sm:left-[calc(5vw-3rem)] cursor-pointer">
        <i class="fas fa-xmark text-red-500"></i>
    </button>

    <div id="billingPosPanel"
        class="absolute inset-y-0 right-0 z-[301] flex h-full w-full translate-x-full flex-col overflow-hidden bg-white text-slate-900 shadow-2xl transition-transform duration-300 ease-out sm:w-[min(1650px,95vw)] billing-pos-no-scrollbar">
        <div class="flex h-full min-h-0 flex-1 flex-col overflow-hidden pb-28 p-1 sm:p-2 lg:p-2">
            <div class="flex h-full min-h-0 flex-1 flex-col">
                @include('modules.billing.pos-modal.partials.header')

                <div class="grid flex-1 min-h-0 grid-cols-1 gap-0 lg:grid-cols-[minmax(0,1.4fr)_minmax(280px,0.6fr)]">
                    @include('modules.billing.pos-modal.partials.left-column')
                    @include('modules.billing.pos-modal.partials.right-column')
                </div>
            </div>
        </div>
    </div>
</div>
