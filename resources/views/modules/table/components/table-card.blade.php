@forelse ($tables as $table)
    @php
        $activeOrders = collect($table['active_orders'] ?? []);
        $totalOrderedItems = $activeOrders->sum(function ($order) {
            return collect($order['items'] ?? [])->sum(fn($item) => max((int) ($item['quantity'] ?? 1), 1));
        });
        $totalOrderAmount = (float) $activeOrders->sum('grand_total');
        $currencySymbol = auth()->user()->branch?->currency?->symbol
            ?? auth()->user()->tenant?->currency?->symbol
            ?? session('currency_symbol', 'Rs.');
        $formattedOrderAmount = fmod($totalOrderAmount, 1.0) === 0.0
            ? number_format($totalOrderAmount, 0)
            : number_format($totalOrderAmount, 2);
        $orderStartedAt = $activeOrders->pluck('ordered_at_timestamp')->filter()->min();
        $runningMinutes = $orderStartedAt
            ? max((int) floor((now()->timestamp - $orderStartedAt) / 60), 0)
            : null;
        $runningDuration = $runningMinutes === null
            ? '--'
            : ($runningMinutes >= 60
                ? intdiv($runningMinutes, 60) . ' hr ' . ($runningMinutes % 60) . ' min'
                : $runningMinutes . ' min');
    @endphp
    <div class="bg-gray-800 border rounded-xl p-4 card-hover table-card cursor-pointer {{ ($isAdmin ?? false) ? 'border-gray-700' : (!empty($table['is_calling_waiter']) ? 'border-blue-500/30' : (!empty($table['is_bill_requested']) ? 'border-orange-500/30' : (($table['status'] ?? '') === 'occupied' ? 'border-red-500/30' : 'border-green-500/20'))) }}"
        data-id="{{ $table['id'] }}" data-name="{{ $table['display_name'] }}"
        data-table-number="{{ $table['table_number'] }}" data-qr-token="{{ $table['qr_token'] ?? '' }}"
        data-branch-id="{{ $table['branch_id'] ?? 0 }}"
        data-branch-tax-setting="{{ $table['branch_tax_setting'] ?? 'exclusive' }}"
        data-branch-tax-rate="{{ $table['branch_tax_rate'] ?? 0 }}"
        data-branch-tax-label="{{ $table['branch_tax_label'] ?? 'Tax' }}"
        data-status="{{ $table['status'] ?? '' }}" data-capacity="{{ $table['capacity'] ?? 0 }}"
        data-is-calling-waiter="{{ !empty($table['is_calling_waiter']) ? '1' : '0' }}"
        data-is-bill-requested="{{ !empty($table['is_bill_requested']) ? '1' : '0' }}"
        data-transfer-state='@json($table['transfer_state'] ?? null)'
        data-currency-symbol="{{ $currencySymbol }}"
        data-orders='@json($table['active_orders'] ?? [])'>

        <div class="flex justify-between items-center mb-1">
            <h3 class="text-white font-semibold">
                {{ $table['display_name'] }}
            </h3>
            <div class="flex items-center gap-2">
                <span class="waiter-call-bell items-center gap-1 text-[10px] px-2 py-1 rounded-full border border-blue-500/60 bg-blue-500/20 text-blue-300 font-semibold"
                    style="display: {{ !empty($table['is_calling_waiter']) ? 'flex' : 'none' }};">
                    <i class="fas fa-bell animate-bounce"></i>
                    <span>Calling Waiter</span>
                    <span class="waiter-call-count"></span>
                </span>
                <span class="bill-request-bell items-center gap-1 text-[10px] px-2 py-1 rounded-full border border-orange-500/60 bg-orange-500/20 text-orange-300 font-semibold"
                    style="display: {{ !empty($table['is_bill_requested']) ? 'flex' : 'none' }};">
                    <i class="fas fa-file-invoice-dollar animate-pulse"></i>
                    <span>Bill Requested</span>
                    <span class="bill-request-count"></span>
                </span>
                <span
                    class="table-status-pill text-xs px-2 py-1 rounded-full
                bg-{{ $table['status_color'] }}-500/20 text-{{ $table['status_color'] }}-400">
                    {{ $table['status_label'] }}
                </span>
                <span
                    class="kitchen-status-badge hidden text-[10px] px-2 py-1 rounded-full border border-gray-500/50 bg-gray-500/10 text-gray-300 font-semibold">
                    Kitchen
                </span>
                <span class="transfer-status-badge {{ ($table['transfer_state']['status'] ?? '') === 'pending' ? 'flex' : 'hidden' }} items-center gap-1 text-[10px] px-2 py-1 rounded-full border border-yellow-500/60 bg-yellow-500/15 text-yellow-300 font-semibold">
                    @if (($table['transfer_state']['status'] ?? '') === 'pending')
                        <i class="fas fa-right-left"></i> Transfer Pending (To: {{ $table['transfer_state']['target_waiter'] ?? '--' }})
                    @endif
                </span>
                <span class="assigned-waiter-badge {{ ($table['transfer_state']['status'] ?? '') === 'accepted' ? '' : 'hidden' }} text-[10px] px-2 py-1 rounded-full border border-green-500/50 bg-green-500/10 text-green-300 font-semibold">
                    @if (($table['transfer_state']['status'] ?? '') === 'accepted')
                        Assigned: {{ $table['transfer_state']['target_waiter'] ?? '--' }}
                    @endif
                </span>
                <span
                    class="new-order-badge hidden text-[10px] px-2 py-1 rounded-full border border-orange-400 bg-orange-100 text-orange-700 dark:border-orange-500/60 dark:bg-orange-500/20 dark:text-orange-300 font-semibold">
                    New
                </span>
            </div>
        </div>

        @if ($isAdmin ?? false)
            <p class="text-xs text-gray-400 mb-3">
                Token: {{ $table['qr_token'] ?: 'N/A' }}
            </p>
        @endif
        <p class="last-order-activity text-[11px] text-orange-700 dark:text-orange-300/80 mb-3 hidden"></p>

        @if ($isAdmin ?? false)
            <hr class="mb-3 text-gray-600">

            <div class="flex items-center justify-between">

                <img class="qrPreview cursor-pointer border border-gray-600 rounded-lg p-2 card-hover"
                    src="{{ $table['qr_code_inline'] }}" alt="Table {{ $table['table_number'] }} QR"
                    data-name="Table {{ $table['table_number'] }}" data-table-number="{{ $table['table_number'] }}"
                    data-qr="{{ $table['qr_code_inline'] }}" />

                <div class="flex gap-2">

                    <button class="viewQrBtn text-xs px-2 py-1 border border-gray-600 rounded-lg text-gray-300"
                        data-name="Table {{ $table['table_number'] }}" data-table-number="{{ $table['table_number'] }}"
                        data-qr="{{ $table['qr_code_inline'] }}">
                        View
                    </button>

                    <button class="editBtn text-xs px-2 py-1 border border-orange-500/40 text-orange-400 rounded-lg"
                        data-id="{{ $table['id'] }}" data-table-number="{{ $table['table_number'] }}"
                        data-branch="{{ $table['branch_id'] }}" data-capacity="{{ $table['capacity'] }}"
                        data-status="{{ $table['status'] }}"
                        data-update-url="{{ route('admin.tables.update', $table['id']) }}">
                        Edit
                    </button>

                </div>
            </div>
        @else
            <div class="mb-2 flex items-center gap-1.5 text-xs text-gray-400">
                <i class="fas fa-users text-[10px]"></i>
                <span class="waiter-capacity-text">{{ $table['capacity'] }} {{ (int) $table['capacity'] === 1 ? 'Seat' : 'Seats' }}</span>
            </div>

            <hr class="waiter-card-divider mb-3 text-gray-700">

            <div class="waiter-order-summary grid grid-cols-2 gap-3"
                style="display: {{ $activeOrders->isEmpty() ? 'none' : 'grid' }};">
                <div class="text-center">
                    <p class="waiter-order-amount text-sm font-bold text-orange-400">
                        {{ $currencySymbol }} {{ $formattedOrderAmount }}
                    </p>
                    <p class="mt-0.5 text-[10px] text-gray-500">Total Amount</p>
                </div>
                <div class="text-center">
                    <p class="waiter-order-items text-sm font-bold text-blue-400">
                        {{ $totalOrderedItems }} {{ $totalOrderedItems === 1 ? 'Item' : 'Items' }}
                    </p>
                    <p class="mt-0.5 text-[10px] text-gray-500">Items</p>
                </div>
                <div class="col-span-2 flex items-center justify-center gap-1.5 text-gray-400">
                    <i class="far fa-clock text-[10px]"></i>
                    <p class="order-running-time text-xs font-semibold" data-started-at="{{ $orderStartedAt ?? '' }}">
                        {{ $runningDuration }}
                    </p>
                    <span class="text-[10px] text-gray-500">Duration</span>
                </div>
            </div>

            <div class="waiter-capacity py-3 text-center {{ $activeOrders->isEmpty() ? '' : 'hidden' }}">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-500/15 text-green-400">
                    <i class="fas fa-chair text-xl"></i>
                </div>
                <p class="mt-2 text-[11px] text-gray-400">
                    {{ ($table['status'] ?? '') === 'available' ? 'Table is ready for order' : 'No active order' }}
                </p>
            </div>

            <div class="waiter-card-actions mt-3 grid grid-cols-2 gap-2">
                <button type="button"
                    class="waiter-view-order {{ $activeOrders->isEmpty() ? 'hidden' : '' }} rounded-md border border-blue-500/40 px-2 py-1.5 text-[11px] font-semibold text-blue-400 hover:bg-blue-500/10">
                    <i class="far fa-eye mr-1"></i> View Order
                </button>

                <button type="button" data-accept-waiter-call
                    data-url="{{ route('waiter.tables.accept-call', $table['id']) }}"
                    class="{{ !empty($table['is_calling_waiter']) ? '' : 'hidden' }} rounded-md bg-blue-500 px-2 py-1.5 text-[11px] font-semibold text-white hover:bg-blue-600">
                    <i class="far fa-check-circle mr-1"></i> Accept Call
                </button>

                <button type="button" data-print-bill-estimate
                    data-url="{{ route('waiter.tables.clear-bill-request', $table['id']) }}"
                    class="{{ !empty($table['is_bill_requested']) && $activeOrders->isNotEmpty() ? '' : 'hidden' }} rounded-md bg-orange-500 px-2 py-1.5 text-[11px] font-semibold text-white hover:bg-orange-600">
                    <i class="fas fa-print mr-1"></i> Print Estimate
                </button>

                <a data-waiter-add-items href="{{ route('order.index', ['table' => $table['table_number'], 'table_id' => $table['id']]) }}"
                    class="{{ $activeOrders->isNotEmpty() && empty($table['is_calling_waiter']) && empty($table['is_bill_requested']) ? '' : 'hidden' }} rounded-md border border-orange-500/40 px-2 py-1.5 text-center text-[11px] font-semibold text-orange-400 hover:bg-orange-500/10">
                    <i class="fas fa-plus-circle mr-1"></i> Add Items
                </a>

                <a data-waiter-start-order href="{{ route('order.index', ['table' => $table['table_number'], 'table_id' => $table['id']]) }}"
                    class="{{ $activeOrders->isEmpty() && ($table['status'] ?? '') === 'available' ? '' : 'hidden' }} col-span-2 rounded-md bg-green-500 px-2 py-2 text-center text-xs font-semibold text-white hover:bg-green-600">
                    <i class="far fa-play-circle mr-1"></i> Start Order
                </a>
            </div>
        @endif
    </div>
@empty
    <div class="col-span-1 sm:col-span-2 md:col-span-3 xl:col-span-4">
        <div
            class="bg-gray-800/80 border border-dashed border-gray-600 rounded-2xl p-8 md:p-12 text-center flex flex-col items-center">
            <div
                class="w-20 h-20 rounded-2xl bg-orange-500/15 border border-orange-500/30 text-orange-400 flex items-center justify-center mb-5">
                <i class="fas fa-chair text-3xl"></i>
            </div>

            <h3 class="text-2xl font-bold text-white">No Tables Found</h3>
            <p class="text-sm text-gray-400 mt-2 max-w-md">
                Your table directory is empty right now. Add your first table set to start generating QR access.
            </p>

            @if ($isAdmin ?? false)
                <button type="button" onclick="document.getElementById('openTableModal').click()"
                    class="mt-6 inline-flex items-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-400 border border-orange-500/30 px-5 py-2.5 rounded-lg text-sm font-medium">
                    <i class="fas fa-plus"></i>
                    Add Your First Table
                </button>
            @endif
        </div>
    </div>
@endforelse

@once
    <script>
        (() => {
            const formatAmount = (amount) => {
                const safeAmount = Number(amount || 0);
                return Number.isInteger(safeAmount) ? safeAmount.toLocaleString() : safeAmount.toLocaleString(
                    undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
            };

            const formatDuration = (totalMinutes) => {
                if (totalMinutes < 60) return `${totalMinutes} min`;

                const hours = Math.floor(totalMinutes / 60);
                const minutes = totalMinutes % 60;
                return `${hours} hr ${minutes} min`;
            };

            window.updateWaiterTableCard = (tableNumber, orders, status = null, flags = {}) => {
                const card = document.querySelector(
                    `.table-card[data-table-number="${CSS.escape(String(tableNumber))}"]`);
                if (!card || !card.querySelector('.waiter-order-summary')) return;

                const safeOrders = Array.isArray(orders) ? orders : [];
                const hasOrders = safeOrders.length > 0;
                const currentStatus = String(status || card.dataset.status || 'available').toLowerCase();
                const effectiveStatus = hasOrders && currentStatus === 'available' ? 'occupied' : currentStatus;
                const isCallingWaiter = flags.is_calling_waiter ?? (card.dataset.isCallingWaiter === '1');
                const isBillRequested = flags.is_bill_requested ?? (card.dataset.isBillRequested === '1');
                const totalItems = safeOrders.reduce((total, order) => total + (Array.isArray(order?.items) ?
                    order.items.reduce((itemTotal, item) => itemTotal + Math.max(Number(item?.quantity || 1),
                        1), 0) : 0), 0);
                const totalAmount = safeOrders.reduce((total, order) => total + Number(order?.grand_total || 0), 0);
                const startTimes = safeOrders.map(order => Date.parse(order?.ordered_at_iso || order?.ordered_at ||
                    order?.created_at || '')).filter(Number.isFinite);
                const startedAt = startTimes.length ? Math.floor(Math.min(...startTimes) / 1000) : 0;
                const summary = card.querySelector('.waiter-order-summary');
                const capacity = card.querySelector('.waiter-capacity');
                const divider = card.querySelector('.waiter-card-divider');
                const timer = card.querySelector('.order-running-time');
                const amount = summary?.querySelector('.waiter-order-amount');
                const items = summary?.querySelector('.waiter-order-items');
                const statusPill = card.querySelector('.table-status-pill');
                const viewOrder = card.querySelector('.waiter-view-order');
                const addItems = card.querySelector('[data-waiter-add-items]');
                const startOrder = card.querySelector('[data-waiter-start-order]');
                const acceptCall = card.querySelector('[data-accept-waiter-call]');
                const printEstimate = card.querySelector('[data-print-bill-estimate]');
                const waiterBell = card.querySelector('.waiter-call-bell');
                const billBell = card.querySelector('.bill-request-bell');
                const transferStatusBadge = card.querySelector('.transfer-status-badge');
                const assignedWaiterBadge = card.querySelector('.assigned-waiter-badge');
                let transferState = Object.prototype.hasOwnProperty.call(flags, 'transfer_state')
                    ? flags.transfer_state
                    : JSON.parse(card.dataset.transferState || 'null');
                const assignedWaiter = transferState?.status === 'accepted' ? (transferState.target_waiter || '') : '';

                card.dataset.status = effectiveStatus;
                card.dataset.isCallingWaiter = isCallingWaiter ? '1' : '0';
                card.dataset.isBillRequested = isBillRequested ? '1' : '0';
                card.dataset.transferState = JSON.stringify(transferState || null);
                card.dataset.orders = JSON.stringify(safeOrders);
                if (transferStatusBadge) {
                    const isPending = transferState?.status === 'pending';
                    transferStatusBadge.classList.toggle('hidden', !isPending);
                    transferStatusBadge.classList.toggle('flex', isPending);
                    transferStatusBadge.innerHTML = isPending
                        ? `<i class="fas fa-right-left"></i> Transfer Pending (To: ${transferState.target_waiter || '--'})`
                        : '';
                }
                if (assignedWaiterBadge) {
                    assignedWaiterBadge.classList.toggle('hidden', !assignedWaiter);
                    assignedWaiterBadge.textContent = assignedWaiter ? `Assigned: ${assignedWaiter}` : '';
                }
                if (summary) summary.style.display = hasOrders ? 'grid' : 'none';
                if (capacity) capacity.style.display = hasOrders ? 'none' : 'block';
                divider?.classList.remove('hidden');
                viewOrder?.classList.toggle('hidden', !hasOrders);
                addItems?.classList.toggle('hidden', !hasOrders || isCallingWaiter || isBillRequested);
                startOrder?.classList.toggle('hidden', hasOrders || effectiveStatus !== 'available');
                acceptCall?.classList.toggle('hidden', !isCallingWaiter);
                printEstimate?.classList.toggle('hidden', !isBillRequested || !hasOrders);
                if (waiterBell) waiterBell.style.display = isCallingWaiter ? 'flex' : 'none';
                if (billBell) billBell.style.display = isBillRequested ? 'flex' : 'none';

                if (timer) {
                    timer.dataset.startedAt = String(startedAt || '');
                    timer.textContent = startedAt > 0 ? formatDuration(Math.max(Math.floor((Date.now() / 1000 -
                        startedAt) / 60), 0)) : '--';
                }
                if (amount) {
                    const symbol = card.dataset.currencySymbol || 'Rs.';
                    amount.textContent = `${symbol} ${formatAmount(totalAmount)}`;
                }
                if (items) items.textContent = `${totalItems} ${totalItems === 1 ? 'Item' : 'Items'}`;

                const statusStyles = {
                    available: ['bg-green-500/20 text-green-400', 'Available'],
                    occupied: ['bg-red-500/20 text-red-400', 'Occupied'],
                    reserved: ['bg-yellow-500/20 text-yellow-400', 'Reserved'],
                    out_of_service: ['bg-gray-500/20 text-gray-400', 'Out of service']
                };
                if (statusPill && statusStyles[effectiveStatus]) {
                    statusPill.className =
                        `table-status-pill text-xs px-2 py-1 rounded-full ${statusStyles[effectiveStatus][0]}`;
                    statusPill.textContent = statusStyles[effectiveStatus][1];
                }
                card.classList.remove('border-red-500/30', 'border-blue-500/30', 'border-orange-500/30',
                    'border-green-500/20');
                card.classList.add(isCallingWaiter ? 'border-blue-500/30' :
                    (isBillRequested ? 'border-orange-500/30' :
                        (effectiveStatus === 'occupied' ? 'border-red-500/30' : 'border-green-500/20')));
                window.syncTableStatsFromCards?.();
            };

            window.refreshWaiterTableCard = async (tableNumber, branchId = null, status = null, flags = {}) => {
                const params = new URLSearchParams();
                if (Number(branchId || 0) > 0) params.set('branch_id', String(Number(branchId)));
                const suffix = params.toString() ? `?${params.toString()}` : '';
                const response = await fetch(`/admin/get-table-orders/${encodeURIComponent(tableNumber)}${suffix}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) throw new Error(`Table summary refresh failed: ${response.status}`);
                window.updateWaiterTableCard(tableNumber, await response.json(), status, flags);
            };

            window.addEventListener('table-transfer-assigned', (event) => {
                const transfer = event.detail || {};
                if (!['pending', 'accepted', 'cancelled'].includes(transfer.status) || !transfer.table_id) return;

                const card = document.querySelector(`.table-card[data-id="${CSS.escape(String(transfer.table_id))}"]`);
                if (!card) return;

                window.updateWaiterTableCard(card.dataset.tableNumber, JSON.parse(card.dataset.orders || '[]'), null, {
                    transfer_state: transfer.status === 'cancelled' ? null : transfer,
                });
            });

            document.addEventListener('click', async (event) => {
                const viewButton = event.target.closest('.waiter-view-order');
                if (viewButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    viewButton.closest('.table-card')?.click();
                    return;
                }

                const acceptButton = event.target.closest('[data-accept-waiter-call]');
                const printEstimateButton = event.target.closest('[data-print-bill-estimate]');
                if (!acceptButton && !printEstimateButton) return;
                event.preventDefault();
                event.stopPropagation();

                if (printEstimateButton) {
                    printEstimateButton.disabled = true;
                    const card = printEstimateButton.closest('.table-card');
                    const useMobilePdfViewer = window.matchMedia('(max-width: 767px)').matches ||
                        window.matchMedia('(pointer: coarse)').matches;
                    const mobileWindowName = useMobilePdfViewer ? `waiterEstimate_${Date.now()}` : '';
                    const mobilePrintWindow = useMobilePdfViewer ? window.open('about:blank', mobileWindowName) : null;
                    try {
                        if (useMobilePdfViewer && !mobilePrintWindow) {
                            throw new Error('The PDF window was blocked by the browser.');
                        }
                        window.currentOpenTable = card.dataset.tableNumber;
                        window.currentOpenTableId = card.dataset.id || null;
                        window.currentOpenTableBranchId = card.dataset.branchId || null;
                        window.currentOpenTableQrToken = card.dataset.qrToken || null;

                        const prepared = await window.prepareTableBillingEstimate?.();
                        if (!prepared || typeof window.printCurrentBillingEstimate !== 'function') {
                            throw new Error('Estimate billing data could not be loaded.');
                        }

                        window.printCurrentBillingEstimate(mobileWindowName);

                        const clearResponse = await fetch(printEstimateButton.dataset.url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            }
                        });
                        if (!clearResponse.ok) throw new Error(`Bill request clear failed: ${clearResponse.status}`);
                        const result = await clearResponse.json();
                        window.updateWaiterTableCard(card.dataset.tableNumber,
                            JSON.parse(card.dataset.orders || '[]'), result.status, result);
                    } catch (error) {
                        console.warn('Unable to print estimate', error);
                        if (mobilePrintWindow && !mobilePrintWindow.closed) mobilePrintWindow.close();
                        const message = error?.message === 'Estimate billing data could not be loaded.'
                            ? 'No active order found for this table. Estimate can only be printed after an order is placed.'
                            : (error?.message === 'The PDF window was blocked by the browser.'
                                ? 'Please allow pop-ups to open the estimate PDF on mobile.'
                                : 'Unable to print the estimate right now.');

                        if (typeof window.showToast === 'function') {
                            window.showToast({
                                type: 'warning',
                                message,
                                duration: 3500
                            });
                        } else {
                            alert(message);
                        }
                    } finally {
                        printEstimateButton.disabled = false;
                    }
                    return;
                }

                acceptButton.disabled = true;

                try {
                    const response = await fetch(acceptButton.dataset.url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        }
                    });
                    if (!response.ok) throw new Error(`Accept call failed: ${response.status}`);
                    const result = await response.json();
                    const card = acceptButton.closest('.table-card');
                    await window.refreshWaiterTableCard(card.dataset.tableNumber, card.dataset.branchId, result.status,
                        result);
                } catch (error) {
                    console.warn('Unable to accept waiter call', error);
                } finally {
                    acceptButton.disabled = false;
                }
            });

            const updateRunningTimes = () => {
                const nowInSeconds = Math.floor(Date.now() / 1000);

                document.querySelectorAll('.order-running-time').forEach((element) => {
                    const startedAt = Number(element.dataset.startedAt || 0);
                    element.textContent = startedAt > 0
                        ? formatDuration(Math.max(Math.floor((nowInSeconds - startedAt) / 60), 0))
                        : '--';
                });
            };

            updateRunningTimes();
            window.setInterval(updateRunningTimes, 30000);

            const summaryUrl = @json(request()->routeIs('waiter.*') ? route('waiter.tables.summaries') : null);
            if (summaryUrl) {
                const refreshAllWaiterCards = async () => {
                    try {
                        const response = await fetch(summaryUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!response.ok) return;
                        const summaries = await response.json();
                        summaries.forEach(summary => window.updateWaiterTableCard(summary.table_number, summary.orders,
                            summary.status, summary));
                    } catch (error) {
                        console.warn('Waiter table summaries refresh failed', error);
                    }
                };

                window.setInterval(refreshAllWaiterCards, 60000);
            }
        })();
    </script>
@endonce
