<div id="drawerOverlay" class="fixed inset-0 bg-black/50 hidden z-40 "></div>
@php
    $orderPanelRoute = request()->routeIs('waiter.*') ? route('order.index') : route('admin.order.index');
@endphp

<style>
    .empty-orders-icon {
        width: 5.25rem;
        height: 5.25rem;
        object-fit: contain;
        display: block;
        filter: none;
    }

    body:not(.light-theme) .empty-orders-icon {
        filter: brightness(0) saturate(100%) invert(1);
    }

    body.light-theme .empty-orders-icon {
        filter: none;
    }
</style>

<div id="drawer"
    class="fixed top-0 right-0 w-full sm:w-[400px] h-full bg-[#1a1c1e] bg-gray-900 border-l border-gray-800 transform translate-x-full transition-transform duration-300 z-50 shadow-2xl flex flex-col">

    <div class="p-5 border-b border-gray-800">
        <div class="flex justify-between items-start text-white">
            <div class="flex items-start gap-2">
                <span
                    class="inline-flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-orange-500/15 text-orange-500 ring-1 ring-orange-500/20">
                    <i class="fas fa-chair block text-[15px] leading-none" aria-hidden="true"></i>
                </span>
                <div class="flex flex-col leading-tight">
                    <h2 id="drawerTitle" class="font-bold text-lg">Order Details</h2>
                    <p id="drawerSubtitle" class="mt-1 flex items-center gap-2 text-xs text-gray-500">0 items ordered
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">

                <!-- Transfer Table Button -->
                <button id="transferTableBtn"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg
                           border border-orange-500/30
                           bg-orange-500/10
                           px-2 py-2
                           text-xs font-semibold text-orange-400
                           transition-all duration-200
                           hover:bg-orange-500 hover:text-white
                           cursor-pointer">

                    <i class="fas fa-right-left text-[10px]" aria-hidden="true"></i>

                    <span>{{ auth()->user()->role === 'waiter' ? 'Transfer' : 'Assign waiter' }}</span>
                </button>

                 <button id="closeDrawer" type="button"
                     class="text-lg leading-none text-gray-500 hover:text-red-400 cursor-pointer"
                     aria-label="Close drawer">
                     ✖
                </button>
            </div>
        </div>

        <div class="mt-3">
            <div class="grid grid-cols-2 rounded-xl border border-gray-700 bg-gray-800 p-1">
                <button id="drawerOrdersTabBtn" type="button"
                    class="rounded-lg px-4 py-2 text-sm font-semibold transition">
                    Orders
                </button>
                <button id="drawerKotTabBtn" type="button"
                    class="rounded-lg px-4 py-2 text-sm font-semibold transition">
                    KOT
                </button>
            </div>
        </div>
    </div>

    <div id="drawerContent" class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar bg-gray-900">
        <div id="activeOrdersList">
            <div class="flex min-h-[240px] items-center justify-center px-4 py-10 text-center">
                <div>
                    <div class="mx-auto flex items-center justify-center">
                        @include('core.components.table.partials.empty-orders-icon')
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-gray-500">No active orders</h3>
                    <p class="mt-1 text-sm text-gray-500">Fresh orders will appear here when the table starts cooking.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 border-t border-gray-700 space-y-3 ">
        <button id="drawerAddItemBtn"
            class="w-full bg-orange-500 hover:bg-orange-500/80 text-white font-bold py-3 rounded-lg shadow-lg cursor-pointer">
            Add Item
        </button>
        <button id="drawerGenerateBillBtn" type="button"
            class="block w-full text-center border border-orange-500 text-orange-400 bg-transparent font-semibold py-2.5 rounded-lg transition cursor-pointer opacity-60">
            @if (auth()->user()->role === 'waiter')
                <i class="fas fa-print mr-1.5"></i> Print Estimate
            @else
                Generate Bill
            @endif
        </button>
    </div>
</div>

@include('modules.billing.pos-modal')

<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        const currentBranchId = Number(@json((int) (auth()->user()->branch_id ?? 0)));
        const currentUserName = @json(auth()->user()->name ?? 'N/A');
        const orderSound = new Audio(@json(asset('sounds/ForOrder.m4a')));
        const waiterCallSound = new Audio(@json(asset('sounds/forWaiter.m4a')));
        const billRequestSound = new Audio(@json(asset('sounds/forBill.m4a')));
        const kitchenReadySound = new Audio(@json(asset('sounds/forKitchen.m4a')));
        const tableSoundStorageKey = 'table_alert_sound_enabled_v1';
        const tableSoundToggleBtn = document.getElementById('tableAudioToggleBtn');
        const tableSoundToggleLabel = document.getElementById('tableAudioBtnLabel');
        const tableSoundToggleCross = document.getElementById('tableAudioOffCross');

        let audioEnabled = localStorage.getItem(tableSoundStorageKey) === '1';

        orderSound.preload = 'auto';
        waiterCallSound.preload = 'auto';
        billRequestSound.preload = 'auto';
        kitchenReadySound.preload = 'auto';

        function playSound(audio) {
            if (!audio || !audioEnabled) return;

            audio.currentTime = 0;
            audio.play().catch(() => {});
        }

        function emitTableToast(type, message) {
            if (!message || typeof window.showToast !== 'function') return;

            window.showToast({
                type,
                message,
                duration: 3500,
            });
        }

        function formatTableToastLabel(tableNum) {
            const cleanTableNum = String(tableNum ?? '').trim();
            return cleanTableNum ? `Table ${cleanTableNum}` : 'Table';
        }

        function syncTableSoundButtonState() {
            if (!tableSoundToggleBtn || !tableSoundToggleCross || !tableSoundToggleLabel) return;

            if (audioEnabled) {
                tableSoundToggleCross.style.display = 'none';
                tableSoundToggleLabel.textContent = 'Sound On';
                tableSoundToggleBtn.classList.remove('text-orange-400', 'border-orange-500/30');
                tableSoundToggleBtn.classList.add('text-green-400', 'border-green-500/40', 'bg-green-500/10');
                tableSoundToggleBtn.setAttribute('aria-pressed', 'true');
                tableSoundToggleBtn.setAttribute('aria-label', 'Table sounds on');
                tableSoundToggleBtn.setAttribute('title', 'Table sounds on');
            } else {
                tableSoundToggleCross.style.display = 'inline-block';
                tableSoundToggleLabel.textContent = 'Sound Off';
                tableSoundToggleBtn.classList.remove('text-green-400', 'border-green-500/40', 'bg-green-500/10');
                tableSoundToggleBtn.classList.add('text-orange-400', 'border-orange-500/30');
                tableSoundToggleBtn.setAttribute('aria-pressed', 'false');
                tableSoundToggleBtn.setAttribute('aria-label', 'Table sounds off');
                tableSoundToggleBtn.setAttribute('title', 'Table sounds off');
            }
        }

        async function toggleTableSoundState() {
            if (audioEnabled) {
                audioEnabled = false;
                localStorage.setItem(tableSoundStorageKey, '0');
                syncTableSoundButtonState();
                return;
            }

            try {
                await orderSound.play();
                orderSound.pause();
                orderSound.currentTime = 0;
                audioEnabled = true;
                localStorage.setItem(tableSoundStorageKey, '1');
                syncTableSoundButtonState();
            } catch (error) {
                audioEnabled = false;
                localStorage.setItem(tableSoundStorageKey, '0');
                syncTableSoundButtonState();
                console.warn('Unable to enable table sounds:', error);
            }
        }

        window.tableSoundEnabled = () => audioEnabled;
        window.toggleTableSound = toggleTableSoundState;
        syncTableSoundButtonState();

        if (tableSoundToggleBtn) {
            tableSoundToggleBtn.addEventListener('click', toggleTableSoundState);
        }

        function playWaiterCallSound() {
            if (!audioEnabled) return;

            waiterCallSound.currentTime = 0;
            waiterCallSound.play().catch(() => {});
        }

        function playBillRequestSound() {
            if (!audioEnabled) return;

            billRequestSound.currentTime = 0;
            billRequestSound.play().catch(() => {});
        }

        if (window.Echo && currentBranchId > 0) {
            window.Echo.private(`orders.branch.${currentBranchId}`)
                .listen('NewOrderReceived', async (e) => {
                    // 1. Alert Admin
                    playSound(orderSound);

                    const tableNum = String(e.orderData.table_number);
                    emitTableToast('success', `${formatTableToastLabel(tableNum)}: New order received`);
                    const isCurrentTableOpen = window.currentOpenTable === tableNum;

                    // 2. Visual feedback on card
                    const card = document.querySelector(`.table-card[data-table-number="${tableNum}"]`);
                    if (card) card.classList.add('ring-2', 'ring-orange-500');
                    if (typeof window.markTableAsOccupied === 'function') {
                        window.markTableAsOccupied(tableNum);
                    }
                    if (!isCurrentTableOpen && typeof window.registerIncomingOrder === 'function') {
                        window.registerIncomingOrder(tableNum);
                    }

                    if (typeof window.refreshWaiterTableCard === 'function') {
                        window.refreshWaiterTableCard(tableNum, card?.dataset.branchId).catch(error =>
                            console.warn('Live waiter card refresh failed', error));
                    }

                    // 3. 🔥 THE TRIGGER: Agar wahi table open hai, toh fetch karo
                    // Ya phir hamesha fetch karo taaki background state update rahe
                    if (isCurrentTableOpen) {
                        await window.refreshFromServer(tableNum);
                    }
                })
                .listen('WaiterCalled', (e) => {
                    playWaiterCallSound();

                    const tableNum = String(e.callData.table_number);
                    emitTableToast('warning', `${formatTableToastLabel(tableNum)}: Waiter called`);
                    const card = document.querySelector(`.table-card[data-table-number="${tableNum}"]`);
                    if (card) {
                        card.classList.add('ring-2', 'ring-blue-500');
                    }

                    if (typeof window.registerWaiterCall === 'function') {
                        window.registerWaiterCall(tableNum);
                    }
                    if (typeof window.markTableAsCallingWaiter === 'function') {
                        window.markTableAsCallingWaiter(tableNum);
                    }
                })
                .listen('BillRequested', (e) => {
                    playBillRequestSound();

                    const payload = e?.requestData || e?.billData || e?.callData || e || {};
                    const tableNum = String(payload.table_number ?? payload.tableNum ?? payload
                        .table_number ?? '');
                    if (!tableNum) return;

                    emitTableToast('info', `${formatTableToastLabel(tableNum)}: Bill requested`);

                    const card = document.querySelector(`.table-card[data-table-number="${tableNum}"]`);
                    if (card) {
                        card.classList.add('ring-2', 'ring-orange-500');
                        card.classList.add('request-bill-active');
                        card.classList.remove('waiter-call-active');
                    }

                    if (typeof window.registerBillRequest === 'function') {
                        window.registerBillRequest(tableNum);
                    } else {
                        const billBell = card?.querySelector('.bill-request-bell');
                        const billCount = card?.querySelector('.bill-request-count');
                        if (billBell) {
                            billBell.classList.remove('hidden');
                            billBell.classList.add('flex');
                        }
                        if (billCount) {
                            billCount.textContent = '';
                        }
                    }
                    if (typeof window.markTableAsRequestBill === 'function') {
                        window.markTableAsRequestBill(tableNum);
                    }
                    window.markTableAsBillRequested?.(tableNum);

                    if (window.currentOpenTable === tableNum && typeof window.refreshFromServer ===
                        'function') {
                        window.refreshFromServer(tableNum);
                    }
                })
                .listen('KitchenStatusUpdated', async (e) => {
                    const payload = e?.kitchenData || {};
                    const tableNum = String(payload.table_number ?? '');
                    const kitchenStatus = String(payload.kitchen_status ?? '').toLowerCase();
                    const itemStatus = String(payload.item_status ?? '').toLowerCase();
                    if (!tableNum) return;

                    if ((itemStatus === 'preparing' || kitchenStatus === 'preparing') &&
                        typeof window.markTableAsKitchenPreparing === 'function') {
                        window.markTableAsKitchenPreparing(tableNum);
                    }

                    const isReadyEvent = itemStatus === 'ready';
                    const isServedEvent = itemStatus === 'served' || (kitchenStatus === 'served' && !isReadyEvent);

                    if ((isReadyEvent || isServedEvent) &&
                        typeof window.markTableAsKitchenReady === 'function') {
                        window.markTableAsKitchenReady(tableNum);
                    }

                    if (isReadyEvent) {
                        // The KOT-level pickup alert handles waiter sound/toast after the full batch is ready.
                    }

                    if (isServedEvent) {
                        emitTableToast('success', `${formatTableToastLabel(tableNum)}: Served`);
                    }

                    if (window.currentOpenTable === tableNum && typeof window.refreshFromServer ===
                        'function') {
                        await window.refreshFromServer(tableNum);
                    }
                });
        }

        const billingModal = document.getElementById('billingPosModal');
        const isWaiterPanel = @json(auth()->user()->role === 'waiter');
        const billingModalPanel = document.getElementById('billingPosPanel');
        const billingModalCloseBtn = document.getElementById('billingPosCloseBtn');
        const drawerGenerateBillBtn = document.getElementById('drawerGenerateBillBtn');
        const transferTableBtn = document.getElementById('transferTableBtn');
        const billingModalClosers = document.querySelectorAll('[data-billing-modal-close]');
        const billingOrdersBaseUrl = @json('/admin/get-table-orders');
        const billingDraftsBaseUrl = @json(url('/admin/billing/drafts'));
        const drawerGenerateBillEnabledClasses = [
            'opacity-100',
        ];
        const drawerGenerateBillDisabledClasses = [
            'opacity-60',
        ];

        if (transferTableBtn) {
            transferTableBtn.addEventListener('click', () => {
                const tableNumber = String(window.currentOpenTable || '').trim();
                const tableId = window.currentOpenTableId || null;

                if (!tableId && !tableNumber) return;

                window.dispatchEvent(new CustomEvent('open-transfer-modal', {
                    detail: {
                        tableId,
                        tableNumber,
                        currentWaiterName: currentUserName,
                    },
                }));
            });
        }

        const formatMoney = (value, fractionDigits = 2) => {
            const number = Number(value ?? 0);
            return number.toLocaleString('en-US', {
                minimumFractionDigits: fractionDigits,
                maximumFractionDigits: fractionDigits,
            });
        };

        const formatDateTime = (value) => {
            if (!value) return 'N/A';
            const normalized = normalizeBillingDateInput(value);
            const date = new Date(normalized);
            if (Number.isNaN(date.getTime())) return String(value);

            return date.toLocaleString('en-US', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        };

        const normalizeBillingDateInput = (value) => {
            if (value === null || value === undefined || value === '') {
                return '';
            }

            const raw = String(value).trim();
            if (!raw) return '';

            // Laravel can emit timestamps with microseconds or a space separator.
            let normalized = raw.replace(' ', 'T');
            normalized = normalized.replace(/(\.\d{3})\d+(?=Z$)/, '$1');
            normalized = normalized.replace(/(\.\d{3})\d+$/, '$1');

            return normalized;
        };

        const toBillingTimestamp = (value) => {
            if (typeof value === 'number' && Number.isFinite(value)) {
                return value;
            }

            const normalized = normalizeBillingDateInput(value);
            if (!normalized) return null;

            const timestamp = Date.parse(normalized);
            return Number.isFinite(timestamp) ? timestamp : null;
        };

        const formatServiceDuration = (startedAt, now = new Date()) => {
            if (startedAt === null || startedAt === undefined || startedAt === '') {
                return 'N/A';
            }

            const startTimestamp = toBillingTimestamp(startedAt);
            if (startTimestamp === null) return 'N/A';

            const start = new Date(startTimestamp);
            if (Number.isNaN(start.getTime())) return 'N/A';

            const elapsedMinutes = Math.max(Math.floor((now.getTime() - start.getTime()) / 60000), 0);
            const hours = Math.floor(elapsedMinutes / 60);
            const minutes = elapsedMinutes % 60;

            if (hours <= 0) {
                return `${elapsedMinutes} min${elapsedMinutes === 1 ? '' : 's'}`;
            }

            if (minutes === 0) {
                return `${hours} hr${hours === 1 ? '' : 's'}`;
            }

            return `${hours} hr${hours === 1 ? '' : 's'} ${minutes} min${minutes === 1 ? '' : 's'}`;
        };

        const resolveBillingTaxConfig = (tableNumber = null) => {
            return window.resolveBillingTaxConfig?.(tableNumber) || {
                setting: String(window.billingTaxSetting || 'exclusive'),
                ratePercent: Number(window.billingTaxRatePercent || 0),
                rate: Number(window.billingTaxRate || 0),
                label: String(window.billingTaxLabelName || 'Tax'),
            };
        };

        const setText = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        };

        const setGenerateBillButtonState = (enabled) => {
            if (!drawerGenerateBillBtn) return;

            const isEnabled = Boolean(enabled);
            drawerGenerateBillBtn.classList.remove(
                ...drawerGenerateBillEnabledClasses,
                ...drawerGenerateBillDisabledClasses
            );
            drawerGenerateBillBtn.classList.add(
                ...(isEnabled ? drawerGenerateBillEnabledClasses : drawerGenerateBillDisabledClasses)
            );
        };

        window.setDrawerGenerateBillButtonState = setGenerateBillButtonState;
        window.currentBillingDraftPayload = window.currentBillingDraftPayload || null;
        window.currentBillingDraftTableId = window.currentBillingDraftTableId || null;
        const hasMatchingBillingDraft = () => {
            const currentTableId = String(window.currentOpenTableId || '').trim();

            return Boolean(
                currentTableId &&
                window.currentBillingDraftPayload &&
                String(window.currentBillingDraftTableId || '').trim() === currentTableId
            );
        };

        setGenerateBillButtonState(Boolean(window.currentOpenTable || hasMatchingBillingDraft()));

        const syncGenerateBillButtonState = () => {
            const hasTableContext = Boolean(
                String(window.currentOpenTable || '').trim() || String(window.currentOpenTableId || '').trim()
            );
            const hasDraft = hasMatchingBillingDraft();
            setGenerateBillButtonState(hasTableContext || hasDraft);
        };

        const setCurrentBillingDraft = (draft = null, tableId = window.currentOpenTableId) => {
            const normalizedTableId = String(tableId || '').trim();
            window.currentBillingDraftPayload = draft;
            window.currentBillingDraftTableId = draft ? normalizedTableId : null;
            syncGenerateBillButtonState();
            return draft;
        };

        const fetchCurrentBillingDraft = async (force = false) => {
            const currentTableNumber = String(window.currentOpenTable || '').trim();
            let tableId = String(window.currentOpenTableId || '').trim();

            if (!tableId && currentTableNumber) {
                const matchingCard = Array.from(document.querySelectorAll('.table-card')).find((card) => {
                    return String(card?.dataset?.tableNumber || '').trim() === currentTableNumber;
                });

                tableId = String(matchingCard?.dataset?.id || '').trim();
            }

            if (!tableId) {
                setCurrentBillingDraft(null);
                return null;
            }

            if (!force && window.currentBillingDraftPayload && String(window.currentBillingDraftTableId || '') === tableId) {
                syncGenerateBillButtonState();
                return window.currentBillingDraftPayload;
            }

            try {
                const response = await fetch(`${billingDraftsBaseUrl}/${encodeURIComponent(tableId)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    setCurrentBillingDraft(null, tableId);
                    return null;
                }

                const result = await response.json();
                const draft = result?.data?.payload || null;
                setCurrentBillingDraft(draft, tableId);
                return draft;
            } catch (error) {
                console.warn('Billing draft load failed', error);
                syncGenerateBillButtonState();
                return null;
            }
        };

        window.refreshBillingDraftForCurrentTable = async (force = false) => {
            return fetchCurrentBillingDraft(force);
        };

        const setValue = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.value = value;
        };

        const renderBillingModal = (order, options = {}) => {
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            } [char]));

            const cleanAddonLabel = (value) => String(value ?? '').replace(/^[↳↲]+\s*/u, '').trim();

            window.billingCurrentOrderPayload = {
                ...(order || {}),
                items: Array.isArray(order?.items) ? order.items : [],
            };

            const normalizeBillingAddon = (addon = {}) => {
                const quantity = Math.max(Number(addon?.quantity ?? 1), 1);
                const storedPrice = Number(String(addon?.price ?? addon?.rate ?? '').replace(/,/g, '')
                    .trim());
                const masterPrice = Number(String(addon?.masterAddon?.price ?? addon
                    ?.menu_item_addon_price ?? 0).replace(/,/g, '').trim());
                const price = Number.isFinite(storedPrice) && storedPrice > 0 ?
                    storedPrice :
                    (Number.isFinite(masterPrice) && masterPrice > 0 ? masterPrice : 0);
                const discount = Math.max(Number(addon?.discount ?? addon?.applied_discount ?? 0), 0);
                const baseAmount = price * quantity;
                const total = Math.max(baseAmount - discount, 0);

                return {
                    id: Number(addon?.id ?? addon?.menu_item_addon_id ?? addon?.masterAddon?.id ?? 0),
                    name: cleanAddonLabel(addon?.name ?? addon?.addon_name ?? addon?.masterAddon
                        ?.name ??
                        'Addon'),
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
                const rawAddons = Array.isArray(item?.order_item_addons) ?
                    item.order_item_addons :
                    (Array.isArray(item?.orderItemAddons) ? item.orderItemAddons : (Array.isArray(item
                        ?.addons) ? item.addons : []));
                const status = String(item?.status ?? item?.item_status ?? '').toLowerCase();
                const isRejected = Boolean(item?.is_rejected) || status === 'rejected' || status ===
                    'cancelled';
                const rejectionReason = String(
                    item?.rejection_reason ?? item?.cancel_reason ?? item?.reason ?? ''
                ).trim();
                const addons = rawAddons.map(normalizeBillingAddon).filter((addon) => addon.name
                    .trim() !== '');
                const quantity = Number(item?.quantity ?? item?.qty ?? 0);
                const rate = Math.max(Number(item?.price ?? item?.rate ?? 0), 0);
                const addonTotal = isRejected ? 0 : addons.reduce((sum, addon) => sum + Number(addon
                    .line_total_before_discount ?? addon.baseAmount ?? addon.total ?? 0), 0);
                const addonDiscountTotal = isRejected ? 0 : addons.reduce((sum, addon) => sum + Math
                    .max(Number(addon
                        .discount ?? addon.applied_discount ?? 0), 0), 0);
                const baseAmount = Math.max(Number(item?.base_amount ?? item?.baseAmount ?? (quantity *
                    rate)), 0);
                const preDiscountTotal = Math.max(Number(
                    item?.line_total_before_discount ??
                    item?.lineTotalBeforeDiscount ??
                    (baseAmount + addonTotal)
                ), 0);
                const discount = Math.max(Number(item?.applied_discount ?? item?.discount ?? 0), 0);
                const total = Math.max(Number(item?.total ?? (preDiscountTotal - discount -
                    addonDiscountTotal)), 0);
                const displayBaseAmount = isRejected ? 0 : baseAmount;
                const displayLineTotalBeforeDiscount = isRejected ? 0 : preDiscountTotal;
                const displayDiscount = isRejected ? 0 : discount;
                const displayAddonTotal = isRejected ? 0 : addonTotal;
                const displayAddonDiscountTotal = isRejected ? 0 : addonDiscountTotal;
                const displayTotal = isRejected ? 0 : total;
                const displayRate = rate;

                return {
                    id: Number(item?.id ?? 0),
                    name: String(item?.item_name ?? item?.name ?? 'Item'),
                    status,
                    isRejected,
                    is_rejected: isRejected,
                    rejectionReason,
                    rejection_reason: rejectionReason,
                    qty: quantity,
                    quantity,
                    rate: displayRate,
                    discount: displayDiscount,
                    baseAmount: displayBaseAmount,
                    base_amount: displayBaseAmount,
                    addonTotal: displayAddonTotal,
                    addon_total: displayAddonTotal,
                    addonDiscountTotal: displayAddonDiscountTotal,
                    addon_discount_total: displayAddonDiscountTotal,
                    addons,
                    lineTotalBeforeDiscount: displayLineTotalBeforeDiscount,
                    line_total_before_discount: displayLineTotalBeforeDiscount,
                    total: displayTotal,
                    amount: displayTotal,
                };
            };

            const renderAddonRows = (addons = [], layout = 'list', parentId = '', isRejected = false) => {
                if (!Array.isArray(addons) || addons.length === 0) {
                    return '';
                }

                if (layout === 'table') {
                    return addons.map((addon) => {
                        const qtyText = addon.quantity > 1 ? ` x${addon.quantity}` : '';
                        const discountText = addon.discount > 0 ?
                            ` <span class="text-amber-600">-Rs ${formatMoney(addon.discount, 0)}</span>` :
                            '';
                        return `
                            <tr
                                class="border-b border-slate-200 bg-yellow-50/80 last:border-b-0 transition-colors hover:bg-yellow-50 ${isRejected ? 'opacity-70 text-slate-400' : ''}"
                                data-row-kind="addon"
                                data-parent-item-id="${parentId}"
                                data-parent-item-is-rejected="${isRejected ? '1' : '0'}"
                                data-addon-id="${addon.id}"
                                data-item-base-amount="${addon.baseAmount}"
                                data-item-line-total-before-discount="${addon.lineTotalBeforeDiscount}"
                                data-item-discount-amount="${addon.discount}"
                                data-item-discount-percent="${addon.lineTotalBeforeDiscount > 0 ? (addon.discount / addon.lineTotalBeforeDiscount) * 100 : 0}"
                                data-item-total="${addon.total}">
                                <td class="border-r border-slate-300 px-3 py-2 font-medium text-slate-700"></td>
                                <td class="border-r border-slate-300 px-3 py-2 font-medium text-slate-950">
                                    <div class="flex items-center gap-2 pl-4 ${isRejected ? 'text-slate-500' : 'text-slate-950'}">
                                        <span class="shrink-0 text-slate-600">↳</span>
                                        <span class="truncate font-medium ${isRejected ? 'text-slate-500' : 'text-slate-950'}">${escapeHtml(cleanAddonLabel(addon.name))}${qtyText}</span>
                                    </div>
                                </td>
                                <td class="border-r border-slate-300 px-3 py-2 text-center font-medium ${isRejected ? 'text-slate-500' : 'text-slate-950'}">${formatMoney(addon.quantity, 0)}</td>
                                <td class="border-r border-slate-300 px-3 py-2 font-medium ${isRejected ? 'text-slate-500' : 'text-slate-700'}">Rs ${formatMoney(addon.price, 0)}</td>
                                <td
                                    class="border-r border-slate-300 px-3 py-2 font-medium text-slate-400"
                                    data-discount-cell
                                    data-item-base-amount="${addon.baseAmount}"
                                    data-item-discount-amount="${addon.discount}"
                                    data-item-discount-percent="${addon.lineTotalBeforeDiscount > 0 ? (addon.discount / addon.lineTotalBeforeDiscount) * 100 : 0}"
                                    data-item-addon-total="0"
                                    data-item-line-total-before-discount="${addon.lineTotalBeforeDiscount}">
                                    <div data-discount-mode="amount" class="flex items-center gap-2">
                                        <span class="text-slate-600">Rs</span>
                                        <input type="text" inputmode="decimal"
                                            class="w-full min-w-0 bg-transparent text-right text-sm font-medium text-slate-500 outline-none"
                                            data-discount-input="amount"
                                            value="${formatMoney(addon.discount, 2)}" ${isRejected ? 'disabled aria-disabled="true"' : ''}>
                                    </div>
                                    <div data-discount-mode="percent" class="hidden items-center gap-2">
                                        <input type="text" inputmode="decimal"
                                            class="w-full min-w-0 bg-transparent text-right text-sm font-medium text-slate-500 outline-none"
                                            data-discount-input="percent"
                                            value="${formatMoney(addon.lineTotalBeforeDiscount > 0 ? (addon.discount / addon.lineTotalBeforeDiscount) * 100 : 0, 2)}" ${isRejected ? 'disabled aria-disabled="true"' : ''}>
                                        <span class="text-slate-600">%</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 font-medium ${isRejected ? 'text-slate-500' : 'text-slate-700'}">
                                    <span data-item-total-value>Rs ${formatMoney(addon.total, 0)}${discountText}</span>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }

                return `
                    <div class="mt-1.5 space-y-1 text-[11px] leading-4">
                        ${addons.map((addon) => {
                            const qtyText = addon.quantity > 1 ? ` x${addon.quantity}` : '';
                            const discountText = addon.discount > 0 ? ` <span class="text-amber-600">-Rs ${formatMoney(addon.discount, 0)}</span>` : '';
                            return `
                                <div class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3 rounded-md bg-yellow-50 px-2 py-1.5 text-xs text-slate-700 ${isRejected ? 'opacity-70 text-slate-400' : ''}">
                                    <div class="flex items-center gap-2 pl-4 font-medium ${isRejected ? 'text-slate-500' : 'text-slate-950'}">
                                        <span class="shrink-0 text-slate-600">↳</span>
                                        <span class="truncate">${escapeHtml(cleanAddonLabel(addon.name))}${qtyText}</span>
                                    </div>
                                    <div class="text-slate-700">Rs ${formatMoney(addon.price, 0)}</div>
                                    <div class="text-center text-slate-700">${formatMoney(addon.quantity, 0)}</div>
                                    <div class="text-right font-medium text-slate-700">Rs ${formatMoney(addon.total, 0)}${discountText}</div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                `;
            };

            const items = (Array.isArray(order?.items) ? order.items : []).map(normalizeBillingItem);
            const isDraftRestore = Boolean(options?.restoreDraft);
            const restoreSource = isDraftRestore && order?.billing_state && typeof order.billing_state === 'object'
                ? { ...order, ...order.billing_state }
                : order;
            const totals = items.reduce((carry, item) => {
                carry.totalQty += Number(item.qty ?? 0) + (item.isRejected ? 0 : (item.addons || [])
                    .reduce((addonSum, addon) => addonSum + Number(addon.quantity ?? 0), 0));
                carry.itemBaseTotal += Number(item.lineTotalBeforeDiscount ?? item.baseAmount ?? 0);
                carry.itemDiscountTotal += Number(item.discount ?? 0) + (item.isRejected ? 0 : (item
                        .addons || [])
                    .reduce((addonSum, addon) => addonSum + Number(addon.discount ?? addon
                        .applied_discount ?? 0), 0));
                return carry;
            }, {
                totalQty: 0,
                itemBaseTotal: 0,
                itemDiscountTotal: 0,
            });

            const itemBaseTotal = totals.itemBaseTotal;
            const itemDiscountTotal = totals.itemDiscountTotal;
            const subtotalAfterItemDiscount = Math.max(itemBaseTotal - itemDiscountTotal, 0);
            const overallDiscountAmount = Number(
                isDraftRestore ?
                (restoreSource?.overall_discount_amount ?? restoreSource?.discount_amount ?? 0) :
                (order?.discount_amount ?? 0)
            );
            const taxConfig = resolveBillingTaxConfig(order?.table_number || window.currentOpenTable || '');
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
            const discountedSubtotal = Math.max(itemBaseTotal - itemDiscountTotal - overallDiscountAmount,
                0);
            let taxableAmount = discountedSubtotal;
            let taxAmount = 0;
            let grandTotal = discountedSubtotal;

            if (taxSetting === 'inclusive') {
                taxAmount = taxRate > 0 ? discountedSubtotal - (discountedSubtotal / (1 + taxRate)) : 0;
                taxableAmount = Math.max(discountedSubtotal - taxAmount, 0);
                grandTotal = discountedSubtotal;
            } else {
                taxableAmount = discountedSubtotal;
                taxAmount = taxableAmount * taxRate;
                grandTotal = taxableAmount + taxAmount;
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

            const itemCount = items.length;
            const itemCountLabel = `${itemCount}/${Math.max(totals.totalQty || itemCount, 1)}`;
            const invoiceItems = items.map((item) => ({
                id: item.id,
                name: item.name,
                status: item.status,
                isRejected: item.isRejected,
                is_rejected: item.is_rejected,
                rejectionReason: item.rejectionReason,
                rejection_reason: item.rejection_reason,
                qty: item.qty,
                quantity: item.qty,
                rate: item.rate,
                discount: item.discount,
                baseAmount: item.baseAmount,
                base_amount: item.baseAmount,
                addonTotal: item.addonTotal,
                addon_total: item.addonTotal,
                addonDiscountTotal: item.addonDiscountTotal,
                addon_discount_total: item.addon_discount_total,
                addons: item.addons,
                lineTotalBeforeDiscount: item.lineTotalBeforeDiscount,
                line_total_before_discount: item.line_total_before_discount,
                total: item.total,
                amount: item.amount,
            }));
            const invoiceSnapshot = {
                items: invoiceItems,
                itemCount,
                totalQty: totals.totalQty,
                itemBaseTotal,
                itemDiscountTotal,
                subtotal: itemBaseTotal,
                subtotalAfterItemDiscount,
                overallDiscountAmount,
                taxableAmount,
                taxAmount,
                grandTotal,
                taxSetting,
                taxRatePercent: Number(taxConfig.ratePercent || 0),
                taxLabel,
            };
            window.billingEstimateInvoiceData = invoiceSnapshot;
            window.billingItemDiscountTotal = itemDiscountTotal;
            window.billingOverallDiscountAmount = overallDiscountAmount;
            window.billingTaxAmount = taxAmount;
            window.billingGrandTotalAmount = grandTotal;

            const leftItemsBody = document.getElementById('billingLeftItemsBody');
            if (leftItemsBody) {
                leftItemsBody.innerHTML = items.map((item, index) => {
                    const discountPercent = item.lineTotalBeforeDiscount > 0 && !item.isRejected ?
                        (item.discount / item.lineTotalBeforeDiscount) * 100 :
                        0;
                    const addonRows = renderAddonRows(item.addons, 'table', item.id, item
                        .isRejected);
                    const itemDisplayTotal = item.isRejected ? 0 : Math.max(item.baseAmount - item
                        .discount, 0);
                    const rowClasses = item.isRejected ?
                        'border-b border-slate-300 bg-slate-50/80 text-slate-400 last:border-b-0' :
                        'border-b border-slate-300 last:border-b-0 transition-colors hover:bg-slate-50';
                    const itemNameClasses = item.isRejected ?
                        'font-medium text-slate-500' :
                        'font-medium text-slate-950';
                    const reasonHtml = item.isRejected && item.rejectionReason ?
                        `
                            <div class="mt-1 flex items-start gap-1.5 text-[11px] leading-4 text-rose-500">
                                <i class="fas fa-circle-xmark mt-0.5 text-[10px] text-rose-500" aria-hidden="true"></i>
                                <span class="break-words">${escapeHtml(item.rejectionReason)}</span>
                            </div>
                        ` :
                        '';

                    return `
                        <tr
                            class="${rowClasses}"
                            data-row-kind="item"
                            data-item-id="${item.id}"
                            data-item-name="${escapeHtml(item.name)}"
                            data-item-status="${escapeHtml(item.status || '')}"
                            data-item-is-rejected="${item.isRejected ? '1' : '0'}"
                            data-item-rejection-reason="${escapeHtml(item.rejectionReason || '')}"
                            data-item-base-amount="${item.baseAmount}"
                            data-item-addon-total="${item.addonTotal}"
                            data-item-line-total-before-discount="${item.baseAmount}"
                            data-item-addons="${escapeHtml(JSON.stringify(item.addons || []))}"
                            data-item-rate="${item.rate}"
                            data-item-qty="${item.qty}"
                            data-item-total="${item.total}">
                            <td class="border-r border-slate-300 px-3 py-2 font-medium text-slate-700">${index + 1}</td>
                            <td class="border-r border-slate-300 px-3 py-2 ${item.isRejected ? 'text-slate-500' : 'text-slate-950'}">
                                <div class="${itemNameClasses}">${escapeHtml(item.name)}</div>
                                ${reasonHtml}
                            </td>
                            <td class="border-r border-slate-300 px-3 py-2 text-center font-medium text-slate-950">${item.qty}</td>
                            <td class="border-r border-slate-300 px-3 py-2 font-medium text-slate-700">Rs ${formatMoney(item.rate, 0)}</td>
                            <td
                                class="border-r border-slate-300 px-3 py-2 font-medium text-slate-400"
                                data-discount-cell
                                data-item-base-amount="${item.baseAmount}"
                                data-item-discount-amount="${item.discount}"
                                data-item-discount-percent="${item.baseAmount > 0 ? (item.discount / item.baseAmount) * 100 : 0}"
                                data-item-addon-total="${item.addonTotal}"
                                data-item-line-total-before-discount="${item.baseAmount}"
                                data-item-addons="${escapeHtml(JSON.stringify(item.addons || []))}">
                                <div data-discount-mode="amount" class="flex items-center gap-2">
                                    <span class="text-slate-600">Rs</span>
                                    <input type="text" inputmode="decimal"
                                        class="w-full min-w-0 bg-transparent text-right text-sm font-medium text-slate-500 outline-none"
                                        data-discount-input="amount"
                                        value="${formatMoney(item.discount, 2)}" ${item.isRejected ? 'disabled aria-disabled="true"' : ''}>
                                </div>
                                <div data-discount-mode="percent" class="hidden items-center gap-2">
                                    <input type="text" inputmode="decimal"
                                        class="w-full min-w-0 bg-transparent text-right text-sm font-medium text-slate-500 outline-none"
                                        data-discount-input="percent"
                                        value="${formatMoney(discountPercent, 2)}" ${item.isRejected ? 'disabled aria-disabled="true"' : ''}>
                                    <span class="text-slate-600">%</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 font-medium text-slate-700">
                                <span data-item-total-value>Rs ${formatMoney(itemDisplayTotal, 0)}</span>
                            </td>
                        </tr>
                        ${addonRows}
                    `;
                }).join('') || `
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-sm text-slate-500">No running order found.</td>
                    </tr>
                `;
            }

            const rightItemsBody = document.getElementById('billingInvoiceItems');
            if (rightItemsBody) {
                rightItemsBody.innerHTML = items.map((item) => {
                    const addonRows = renderAddonRows(item.addons, 'list', item.id, item
                        .isRejected);
                    const itemDisplayTotal = item.isRejected ? 0 : Math.max(item.baseAmount - item
                        .discount, 0);
                    const reasonHtml = item.isRejected && item.rejectionReason ?
                        `
                            <div class="mt-1 flex items-start gap-1.5 text-[11px] leading-4 text-rose-500">
                                <i class="fas fa-circle-xmark mt-0.5 text-[10px] text-rose-500" aria-hidden="true"></i>
                                <span class="break-words">${escapeHtml(item.rejectionReason)}</span>
                            </div>
                        ` :
                        '';
                    const itemClasses = item.isRejected ?
                        'space-y-1.5 py-1.5 text-xs text-slate-400' :
                        'space-y-1.5 py-1.5 text-xs';

                    return `
                        <div class="${itemClasses}" data-item-id="${item.id}"
                            data-item-status="${escapeHtml(item.status || '')}"
                            data-item-is-rejected="${item.isRejected ? '1' : '0'}"
                            data-item-rejection-reason="${escapeHtml(item.rejectionReason || '')}">
                            <div class="grid grid-cols-[1.4fr_0.8fr_0.5fr_0.8fr] gap-3">
                                <div class="font-medium ${item.isRejected ? 'text-slate-500' : 'text-slate-700'}">
                                    <div>${escapeHtml(item.name)}</div>
                                    ${reasonHtml}
                                </div>
                                <div class="text-slate-700">Rs ${formatMoney(item.rate, 0)}</div>
                                <div class="text-center text-slate-700">${item.qty}</div>
                                <div class="text-right text-slate-700">Rs ${formatMoney(itemDisplayTotal, 0)}</div>
                            </div>
                            ${addonRows}
                        </div>
                    `;
                }).join('') || `
                    <div class="py-3 text-center text-sm text-slate-500">No running order found.</div>
                `;
            }

            const billingTableNumber = String(
                restoreSource?.table_number ||
                order?.table_number ||
                window.currentOpenTable ||
                ''
            ).trim();

            setText('billingInvoiceNo', '##');
            setText('billingDineInTable', billingTableNumber ? `Table ${billingTableNumber}` : 'Table N/A');
            setText('billingInvoiceDate', formatDateTime(order?.created_at));
            setText('billingCustomerName', 'Cash Customer');
            const orderedAtSource = order?.ordered_at_iso || order?.ordered_at || '';
            const serviceDurationText = formatServiceDuration(orderedAtSource);
            setText('billingServiceDuration', serviceDurationText);

            const overallDiscountPercent = itemBaseTotal > 0 ?
                (overallDiscountAmount / itemBaseTotal) * 100 :
                0;
            window.billingDiscountSubtotalAfterItemDiscount = subtotalAfterItemDiscount;
            window.billingDiscountSource = window.billingDiscountSource || 'amount';
            setText('billingLeftItemTotal', `Rs ${formatMoney(itemBaseTotal)}`);
            setText('billingLeftItemDiscountAmount', `Rs ${formatMoney(itemDiscountTotal)}`);
            setText('billingLeftSubTotal', `Rs ${formatMoney(itemBaseTotal)}`);
            setText('billingLeftItemCount', `${itemCount} Items`);
            const itemDiscountRow = document.getElementById('billingLeftItemDiscountRow');
            if (itemDiscountRow) {
                const shouldShow = itemDiscountTotal > 0;
                itemDiscountRow.classList.toggle('hidden', !shouldShow);
                itemDiscountRow.hidden = !shouldShow;
                itemDiscountRow.style.display = shouldShow ? '' : 'none';
            }
            setValue('billingLeftDiscountAmount', formatMoney(overallDiscountPercent));
            setValue('billingLeftOverallDiscount', formatMoney(overallDiscountAmount));
            setText('billingLeftTaxableAmount', `Rs ${formatMoney(taxableAmount)}`);
            setText('billingLeftTaxLabelText', taxLabel);
            setText('billingLeftNoTax', `Rs ${formatMoney(taxAmount)}`);
            setText('billingLeftGrandTotal', `Rs ${formatMoney(grandTotal)}`);

            setText('billingRightItemCount', itemCountLabel);
            setText('billingRightItemTotal', `Rs ${formatMoney(itemBaseTotal, 0)}`);
            setText('billingRightLoyaltyDiscount', `Rs ${formatMoney(itemDiscountTotal, 2)}`);
            setText('billingRightSubTotal', `Rs ${formatMoney(itemBaseTotal, 2)}`);
            setText('billingRightDiscount', `Rs ${formatMoney(overallDiscountAmount, 2)}`);
            setText('billingRightTaxLabel', taxLabel);
            setText('billingRightManualDiscount', `Rs ${formatMoney(taxAmount, 2)}`);
            setText('billingRightTotalAmount', `Rs ${formatMoney(grandTotal, 2)}`);
            setText('billingNetSalesAmount', `Rs ${formatMoney(grandTotal, 2)}`);
            window.dispatchEvent(new CustomEvent('billing-estimate-invoice-updated', {
                detail: invoiceSnapshot,
            }));

            const paymentStatus = String(order?.payment_status || 'pending').toLowerCase();
            const restorePaymentMode = String(restoreSource?.payment_mode || '').toLowerCase();
            const paymentMode = isDraftRestore && ['paid', 'unpaid', 'partial'].includes(restorePaymentMode)
                ? restorePaymentMode
                : 'paid';
            const paidAmount = Number(restoreSource?.paid_amount ?? 0);
            const tenderAmount = Number(restoreSource?.tender_amount ?? (paymentMode === 'paid' ? grandTotal :
                paymentMode === 'partial' ? paidAmount : 0));
            const changeAmount = Number(restoreSource?.change_amount ?? Math.max(tenderAmount - grandTotal, 0));
            const restoreMultiplePaymentEnabled = Boolean(restoreSource?.multiple_payment_enabled);
            const restoreDiscountMode = String(restoreSource?.discount_mode || '').toLowerCase();
            const restoreOverallDiscountAmount = Number(
                restoreSource?.overall_discount_amount ??
                restoreSource?.discount_amount ??
                0
            );
            const restoreOverallDiscountPercent = Number(
                restoreSource?.overall_discount_percent ??
                (itemBaseTotal > 0 ? (restoreOverallDiscountAmount / itemBaseTotal) * 100 : 0)
            );
            window.billingGrandTotalAmount = grandTotal;
            window.billingPaymentMode = paymentMode;
            window.billingPaymentMethod = String(restoreSource?.payment_method || '').trim();
            window.billingTenderAmountValue = tenderAmount;
            window.billingChangeAmountValue = changeAmount;
            window.billingOverallDiscountAmount = isDraftRestore ? restoreOverallDiscountAmount : overallDiscountAmount;
            window.billingDiscountSource = isDraftRestore ? (restoreDiscountMode || 'amount') : (window.billingDiscountSource || 'amount');

            if (isDraftRestore) {
                const overallDiscountAmountInput = document.getElementById('billingLeftOverallDiscount');
                const overallDiscountPercentInput = document.getElementById('billingLeftDiscountAmount');

                if (overallDiscountAmountInput) {
                    overallDiscountAmountInput.value = formatMoney(restoreOverallDiscountAmount);
                }
                if (overallDiscountPercentInput) {
                    overallDiscountPercentInput.value = formatMoney(restoreOverallDiscountPercent);
                }

                window.billingInvoiceRemarks = String(restoreSource?.notes_snapshot || '').trim();
                const billingInvoiceRemarksInput = document.getElementById('billingInvoiceRemarks');
                if (billingInvoiceRemarksInput) {
                    billingInvoiceRemarksInput.value = window.billingInvoiceRemarks;
                }
            }

            setValue('billingTenderAmount', formatMoney(isDraftRestore ? tenderAmount : grandTotal));
            setText('billingChangeAmount', `-${formatMoney(isDraftRestore ? changeAmount : 0)}`);

            if (typeof window.updateBillingPaymentMode === 'function') {
                window.updateBillingPaymentMode(paymentMode, {
                    grandTotal,
                    resetPartial: false,
                });
                if (isDraftRestore && typeof window.updateBillingPaymentMethod === 'function') {
                    window.updateBillingPaymentMethod(String(restoreSource?.payment_method || ''), {
                        silent: true,
                    });
                }
                if (isDraftRestore && typeof window.updateBillingMultiplePayment === 'function') {
                    window.updateBillingMultiplePayment(restoreMultiplePaymentEnabled, {
                        silent: true,
                    });
                }
            } else {
                setText('billingPaymentMode', isDraftRestore && paymentMode === 'paid'
                    ? 'Paid'
                    : 'Unpaid / Credit');
                setText('billingPaymentModeAmount', `(Rs ${formatMoney(grandTotal, 2)})`);
            }
            if (isDraftRestore) {
                if (restoreDiscountMode && typeof window.updateBillingDiscountMode === 'function') {
                    try {
                        window.updateBillingDiscountMode(restoreDiscountMode);
                    } catch (error) {
                        console.warn('Billing discount restore sync failed', error);
                    }
                }
                window.requestBillingEstimateInvoiceSync?.();
            } else {
                window.requestBillingEstimateInvoiceSync?.();
                if (typeof window.updateBillingDiscountMode === 'function') {
                    try {
                        window.updateBillingDiscountMode(document.querySelector('[data-pos-discount-root]')
                            ?.dataset?.discountMode || 'amount');
                    } catch (error) {
                        console.warn('Billing discount mode sync failed', error);
                    }
                }
            }
            setText('billingKotNo',
                `${order?.order_number || 'N/A'}${order?.order_by_label ? ` (by ${order.order_by_label})` : ''}`
            );
            setText('billingAssignTo',
                `${order?.order_by_label || 'Guest'} (Rs ${formatMoney(grandTotal, 2)})`);
            setText('billingBilledBy', currentUserName || 'N/A');
        };

        const loadBillingModalData = async () => {
            const tableNumber = String(window.currentOpenTable || '').trim();
            if (!tableNumber) return false;

            try {
                const draft = await fetchCurrentBillingDraft(true);
                if (draft) {
                    const restoreDraft = draft?.billing_state && typeof draft.billing_state === 'object'
                        ? { ...draft, ...draft.billing_state }
                        : draft;
                    renderBillingModal(restoreDraft, { restoreDraft: true });
                    return true;
                }

                const branchId = Number(window.currentOpenTableBranchId || 0);
                const branchQuery = branchId > 0 ? `?branch_id=${encodeURIComponent(branchId)}` : '';
                const response = await fetch(
                    `${billingOrdersBaseUrl}/${encodeURIComponent(tableNumber)}${branchQuery}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                if (!response.ok) return false;

                const orders = await response.json();
                const order = Array.isArray(orders) ? orders[0] : orders;
                if (!order) {
                    if (typeof window.markTableAsAvailable === 'function') {
                        window.markTableAsAvailable(tableNumber);
                    }
                    window.currentOpenTableOrders = [];
                    syncGenerateBillButtonState();
                    return false;
                }

                renderBillingModal(order);
                return true;
            } catch (error) {
                console.warn('Billing modal order load failed', error);
                return false;
            }
        };
        window.prepareTableBillingEstimate = loadBillingModalData;

        const openBillingModal = async () => {
            if (!billingModal || !billingModalPanel) return false;
            const loaded = await loadBillingModalData();
            if (!loaded) {
                if (typeof window.showToast === 'function') {
                    window.showToast({
                        type: 'error',
                        message: 'No running order found for this table.',
                        duration: 3000,
                    });
                }
                closeBillingModal();
                return false;
            }

            billingModal.classList.remove('hidden');
            billingModalCloseBtn?.classList.remove('opacity-0', 'pointer-events-none', 'scale-90');
            document.body.classList.add('overflow-hidden');
            requestAnimationFrame(() => {
                billingModalPanel.classList.remove('translate-x-full');
                billingModalPanel.classList.add('translate-x-0');
            });
            return true;
        };
        window.openTableBillingModal = openBillingModal;

        const closeBillingModal = () => {
            if (!billingModal || !billingModalPanel || billingModal.classList.contains('hidden')) return;
            billingModalCloseBtn?.classList.add('opacity-0', 'pointer-events-none', 'scale-90');
            billingModalPanel.classList.remove('translate-x-0');
            billingModalPanel.classList.add('translate-x-full');
            document.body.classList.remove('overflow-hidden');
            window.billingDiscountSource = undefined;
        };
        window.closeTableBillingModal = closeBillingModal;

        if (billingModalPanel) {
            billingModalPanel.addEventListener('transitionend', () => {
                if (billingModalPanel.classList.contains('translate-x-full')) {
                    billingModal.classList.add('hidden');
                }
            });
        }

        if (drawerGenerateBillBtn) {
            drawerGenerateBillBtn.addEventListener('click', () => {
                if (!isWaiterPanel) {
                    openBillingModal();
                    return;
                }

                const currentCard = document.querySelector(
                    `.table-card[data-table-number="${CSS.escape(String(window.currentOpenTable || ''))}"]`);
                const printEstimateButton = currentCard?.querySelector('[data-print-bill-estimate]');
                if (printEstimateButton) {
                    printEstimateButton.click();
                }
            });
        }

        billingModalClosers.forEach((closer) => {
            closer.addEventListener('click', closeBillingModal);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeBillingModal();
            }
        });

        const drawerAddItemBtn = document.getElementById('drawerAddItemBtn');
        if (drawerAddItemBtn) {
            drawerAddItemBtn.addEventListener('click', () => {
                const tableNumber = window.currentOpenTable || '';
                const tableId = window.currentOpenTableId || '';

                const url = new URL(@json($orderPanelRoute), window.location.origin);
                if (tableNumber) {
                    url.searchParams.set('table', tableNumber);
                }
                if (tableId) {
                    url.searchParams.set('table_id', tableId);
                }

                window.location.href = url.toString();
            });
        }
    });
</script>
