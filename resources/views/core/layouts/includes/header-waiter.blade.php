<main class="flex h-screen flex-1 flex-col overflow-hidden">

    <header
        class="bg-gray-800 border-b border-gray-700 px-4 md:px-6 py-4 flex items-center justify-between sticky top-0 z-40">

        <div class="flex items-center min-w-0 gap-2">
            <button id="hamburgerBtn" class="lg:hidden text-gray-400 mr-3 focus:outline-none flex-shrink-0">
                <i class="fas fa-bars text-xl"></i>
            </button>
            @if (auth()->user()->role == 'waiter')
                @php
                    $waiter = auth()->user();
                    $restaurantName = $waiter->tenant?->company_name ?? 'Restaurant';
                @endphp
                <a href="{{ route('waiter.tables.index') }}"
                    class="flex min-w-0 flex-col group transition-all">
                    <h1 class="max-w-40 truncate text-sm font-semibold text-white group-hover:text-orange-500 md:max-w-72 md:text-xl"
                        title="{{ $restaurantName }}">
                        {{ $restaurantName }}
                    </h1>
                </a>
            @else
                <span class="px-3 py-2 bg-orange-500 rounded-lg shadow-lg shadow-orange-500/20">
                    <i class="fas fa-utensils text-white text-xs md:text-sm"></i>
                </span>
                <h1 class="text-sm md:text-xl font-semibold text-white truncate">
                    @if (auth()->user()->role == 'admin' || auth()->user()->role == 'superadmin')
                        <span class="hidden sm:inline">Multi-Branch Dashboard</span>
                        <span class="sm:hidden text-orange-500">Admin</span>
                    @else
                        {{ ucfirst(auth()->user()->role) }} Panel
                    @endif
                </h1>
            @endif
        </div>

        <div class="hidden sm:flex items-center mx-4 flex-1 justify-center max-w-xs md:max-w-md">

            <div class="flex items-center space-x-2 px-3 py-1 bg-orange-500/10 border border-orange-500/20 rounded-lg">
                <i class="fas fa-store text-[10px] text-orange-500"></i>
                <span class="text-[10px] md:text-xs font-bold text-orange-500 uppercase tracking-wider truncate">
                    {{ auth()->user()->branch_name ?? 'Main Outlet' }}
                </span>
            </div>

        </div>
        @if (session()->has('impersonated_by'))
            <div
                class="hidden lg:flex hidden sm:flex items-center px-3 py-2 rounded-lg border border-orange-500/30 bg-orange-500/10 shadow-sm">
                <a href="{{ route('impersonate.leave') }}"
                    class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide bg-orange-500 text-white hover:bg-orange-400 transition-colors inline-flex items-center gap-1.5">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    Back to SuperAdmin
                </a>
            </div>
        @endif
        <div class="flex items-center space-x-2 md:space-x-4 flex-shrink-0">

            <div class="flex items-center justify-start sm:justify-end gap-3">
                @include('core.components.table.table-sound-toggle')
            </div>
            <button id="theme-toggle"
                class="hidden sm:block text-gray-400 hover:text-orange-500 text-lg transition-colors">
                <i id="theme-icon" class="fas fa-sun"></i>
            </button>

            <div id="pickupAlertBell" class="relative">
                <button id="pickupAlertBellBtn" type="button"
                    class="relative p-1 text-gray-400 hover:text-orange-500 transition-colors"
                    aria-label="Kitchen pickup alerts" aria-expanded="false">
                    <i class="fas fa-bell text-lg"></i>
                    <span id="pickupAlertCount"
                        class="hidden absolute -top-1 -right-1 min-w-4 h-4 px-1 bg-red-500 text-white text-[10px] rounded-full items-center justify-center border-2 border-gray-800">0</span>
                </button>

                <div id="pickupAlertMenu"
                    class="hidden fixed right-2 top-16 w-[calc(100vw-1rem)] max-w-sm sm:absolute sm:right-0 sm:top-full sm:mt-3 sm:w-96 bg-gray-800 border border-gray-700 rounded-xl shadow-2xl z-[120] overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
                        <div>
                            <p class="text-sm font-bold text-white">Kitchen Alerts</p>
                            <p id="pickupAlertSubtitle" class="text-[11px] text-gray-400">No food waiting</p>
                        </div>
                        <i class="fas fa-bell-concierge text-orange-500"></i>
                    </div>
                    <div id="pickupAlertList" class="max-h-[65vh] overflow-y-auto"></div>
                    <div id="pickupAlertEmpty" class="px-5 py-8 text-center text-sm text-gray-400">
                        <i class="fas fa-check-circle block mb-2 text-xl text-green-500"></i>
                        No pending notifications
                    </div>
                </div>
            </div>

            <div class="relative border-l border-gray-700 pl-2 md:pl-4 flex items-center">
                <div id="profileBtn" class="flex items-center space-x-3 cursor-pointer group">

                    <div
                        class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center border-2 border-transparent group-hover:border-orange-500/50 transition-all shadow-lg overflow-hidden">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>

                    <div class="hidden md:flex flex-col items-start leading-none pointer-events-none">
                        <span class="text-sm font-semibold text-white group-hover:text-orange-500 transition-colors">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="text-[10px] text-gray-500 uppercase mt-1 font-bold tracking-tight">
                            {{ auth()->user()->role }}
                        </span>
                    </div>

                    <i
                        class="fas fa-chevron-down text-[10px] text-gray-400 group-hover:text-white transition-all transform group-hover:translate-y-0.5"></i>
                </div>

                <div id="profileMenu"
                    class="hidden absolute right-0 mt-3 w-48 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-[100] overflow-hidden top-full">
                    <div class="py-1">

                        <div class="px-4 py-3 border-b border-gray-700 md:hidden bg-gray-900/40">
                            <p class="text-xs text-white font-bold truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[9px] text-gray-500 uppercase mt-0.5">{{ auth()->user()->role }}</p>
                        </div>

                        <div class="mt-1">
                            <a href="{{ route('admin.profile') }}"
                                class="group flex items-center px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-orange-500 transition-all">
                                <div
                                    class="w-8 h-8 rounded-lg bg-gray-700 group-hover:bg-orange-500/10 flex items-center justify-center mr-3 transition-all">
                                    <i class="fas fa-user-circle text-gray-500 group-hover:text-orange-500"></i>
                                </div>
                                My Profile
                            </a>

                            <a href="#"
                                class="group flex items-center px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-orange-500 transition-all">
                                <div
                                    class="w-8 h-8 rounded-lg bg-gray-700 group-hover:bg-orange-500/10 flex items-center justify-center mr-3 transition-all">
                                    <i class="fas fa-cog text-gray-500 group-hover:text-orange-500"></i>
                                </div>
                                Settings
                            </a>
                        </div>

                        <div class="px-2 my-1">
                            <hr class="border-gray-700">
                        </div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 transition-colors font-bold">
                                <div class="w-8 h-8 rounded-lg bg-red-500/5 flex items-center justify-center mr-3">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        const branchId = Number(@json((int) (auth()->user()->branch_id ?? 0)));
        const listUrl = @json(route('waiter.pickup-alerts.index'));
        const acceptUrlTemplate = @json(route('waiter.pickup-alerts.accept', ['alert' => '__ALERT__']));
        const transferListUrl = @json(route('waiter.table-transfers.index'));
        const transferActivityUrl = @json(route('waiter.table-transfers.activity'));
        const transferResponseUrlTemplate = @json(route('waiter.table-transfers.respond', ['transfer' => '__TRANSFER__']));
        const currentUserId = Number(@json(auth()->id()));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const bell = document.getElementById('pickupAlertBell');
        const button = document.getElementById('pickupAlertBellBtn');
        const menu = document.getElementById('pickupAlertMenu');
        const list = document.getElementById('pickupAlertList');
        const empty = document.getElementById('pickupAlertEmpty');
        const count = document.getElementById('pickupAlertCount');
        const subtitle = document.getElementById('pickupAlertSubtitle');
        const readySound = new Audio(@json(asset('sounds/forKitchen.m4a')));
        const alerts = new Map();
        const transfers = new Map();
        const announcedTransferUpdates = new Set();
        const transferUpdateStorageKey = `table_transfer_updates_${branchId}_${currentUserId}`;
        const lastReminderAt = new Map();

        try {
            JSON.parse(sessionStorage.getItem(transferUpdateStorageKey) || '[]')
                .forEach(key => announcedTransferUpdates.add(key));
        } catch (error) {
            console.warn('Unable to restore transfer notification state:', error);
        }

        readySound.preload = 'auto';

        const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, char => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
        })[char]);

        const soundEnabled = () => typeof window.tableSoundEnabled === 'function'
            ? window.tableSoundEnabled()
            : localStorage.getItem('table_alert_sound_enabled_v1') === '1';

        const playReadySound = () => {
            if (!soundEnabled()) return;
            readySound.currentTime = 0;
            readySound.play().catch(() => {});
        };

        const rememberTransferUpdate = (updateKey) => {
            announcedTransferUpdates.add(updateKey);
            try {
                sessionStorage.setItem(transferUpdateStorageKey, JSON.stringify([...announcedTransferUpdates]));
            } catch (error) {
                console.warn('Unable to store transfer notification state:', error);
            }
        };

        const ageText = (iso) => {
            const seconds = Math.max(0, Math.floor((Date.now() - new Date(iso).getTime()) / 1000));
            if (seconds < 60) return 'Ready just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `Ready ${minutes}m ago`;
            const hours = Math.floor(minutes / 60);
            return `Ready ${hours}h ${minutes % 60}m ago`;
        };

        function render() {
            const pending = [...alerts.values()].filter(alert => alert.status === 'pending')
                .sort((a, b) => new Date(a.ready_at) - new Date(b.ready_at));
            const pendingTransfers = [...transfers.values()].filter(transfer => transfer.status === 'pending');
            const totalPending = pending.length + pendingTransfers.length;
            count.textContent = totalPending > 99 ? '99+' : totalPending;
            count.classList.toggle('hidden', totalPending === 0);
            count.classList.toggle('flex', totalPending > 0);
            empty.classList.toggle('hidden', totalPending > 0);
            subtitle.textContent = totalPending ? `${totalPending} notification${totalPending === 1 ? '' : 's'}` : 'No pending notifications';
            list.innerHTML = pendingTransfers.map(transfer => `
                <div class="pickup-alert-item px-4 py-3 border-b border-gray-700/70 last:border-0" data-transfer-id="${transfer.id}">
                    <p class="text-sm font-bold text-white">Table ${escapeHtml(transfer.table_number || '--')} <span class="text-orange-400">&bull; Transfer Request</span></p>
                    <p class="mt-1 text-xs text-gray-300">From: <span class="font-semibold text-white">${escapeHtml(transfer.from_waiter)}</span></p>
                    <p class="mt-1 text-xs text-gray-400">Note: ${escapeHtml(transfer.notes || 'No note provided')}</p>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <button type="button" data-respond-transfer="${transfer.id}" data-decision="accepted"
                            class="rounded-lg bg-green-600 px-3 py-2 text-xs font-bold text-white hover:bg-green-500 disabled:opacity-60">
                            <i class="fas fa-check mr-1"></i> Accept
                        </button>
                        <button type="button" data-respond-transfer="${transfer.id}" data-decision="cancelled"
                            class="rounded-lg bg-red-600/90 px-3 py-2 text-xs font-bold text-white hover:bg-red-500 disabled:opacity-60">
                            <i class="fas fa-xmark mr-1"></i> Decline
                        </button>
                    </div>
                </div>`).join('') + pending.map(alert => {
                const items = (alert.items || []).map(item =>
                    `<span>${escapeHtml(item.quantity)}x ${escapeHtml(item.name)}</span>`
                ).join('<span class="text-gray-600">, </span>');
                return `<div class="pickup-alert-item px-4 py-3 border-b border-gray-700/70 last:border-0" data-id="${alert.id}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-white">Table ${escapeHtml(alert.table_number || '--')} <span class="text-orange-400">&bull; KOT #${escapeHtml(alert.kot_number)}</span></p>
                            <div class="mt-1 text-xs text-gray-300 leading-5">${items || 'Items ready'}</div>
                            <p class="mt-1 text-[11px] font-medium text-green-400" data-ready-at="${escapeHtml(alert.ready_at)}">${ageText(alert.ready_at)}</p>
                        </div>
                    </div>
                    <button type="button" data-accept-alert="${alert.id}"
                        class="mt-3 w-full rounded-lg bg-orange-500 px-3 py-2 text-xs font-bold text-white hover:bg-orange-600 disabled:opacity-60">
                        <i class="fas fa-check mr-1"></i> Accept & Pick Up
                    </button>
                </div>`;
            }).join('');
        }

        async function loadAlerts() {
            try {
                const response = await fetch(listUrl, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) return;
                const data = await response.json();
                alerts.clear();
                (data.alerts || []).forEach(alert => alerts.set(Number(alert.id), alert));
                render();
            } catch (error) {
                console.warn('Unable to load kitchen pickup alerts:', error);
            }
        }

        async function loadTransfers() {
            try {
                const response = await fetch(transferListUrl, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) return;
                const data = await response.json();
                transfers.clear();
                (data.transfers || []).forEach(transfer => transfers.set(Number(transfer.id), transfer));
                render();
            } catch (error) {
                console.warn('Unable to load table transfer notifications:', error);
            }
        }

        async function syncTransferActivity() {
            try {
                const response = await fetch(transferActivityUrl, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) return;
                const data = await response.json();
                (data.transfers || []).forEach(transfer => {
                    const isSender = Number(transfer.handled_by_waiter_id) === currentUserId;
                    if (!isSender || transfer.status === 'pending') return;

                    const updateKey = `${transfer.id}:${transfer.status}:${transfer.updated_at || ''}`;
                    if (announcedTransferUpdates.has(updateKey)) return;
                    rememberTransferUpdate(updateKey);
                    playReadySound();
                    window.showToast?.({
                        type: transfer.status === 'accepted' ? 'success' : 'warning',
                        message: transfer.status === 'accepted'
                            ? `${transfer.target_waiter || 'Waiter'} accepted Table ${transfer.table_number || '--'}.`
                            : `${transfer.target_waiter || 'Waiter'} is busy / Transfer declined for Table ${transfer.table_number || '--'}.`,
                        duration: 4500,
                    });
                });
            } catch (error) {
                console.warn('Unable to sync table transfer activity:', error);
            }
        }

        async function acceptAlert(id, acceptButton) {
            acceptButton.disabled = true;
            try {
                const response = await fetch(acceptUrlTemplate.replace('__ALERT__', id), {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok && response.status !== 409) throw new Error(data.message || 'Unable to accept pickup');
                alerts.delete(Number(id));
                lastReminderAt.delete(Number(id));
                render();
                window.showToast?.({ type: response.ok ? 'success' : 'info', message: data.message || 'KOT already picked up', duration: 3000 });
            } catch (error) {
                acceptButton.disabled = false;
                window.showToast?.({ type: 'error', message: error.message, duration: 3500 });
            }
        }

        async function respondToTransfer(id, decision, responseButton) {
            responseButton.disabled = true;
            try {
                const response = await fetch(transferResponseUrlTemplate.replace('__TRANSFER__', id), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ decision }),
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok && response.status !== 409) throw new Error(data.message || 'Unable to respond to transfer');
                transfers.delete(Number(id));
                render();
                window.showToast?.({ type: response.ok ? 'success' : 'info', message: data.message || 'Transfer already handled', duration: 3000 });
            } catch (error) {
                responseButton.disabled = false;
                window.showToast?.({ type: 'error', message: error.message, duration: 3500 });
            }
        }

        button?.addEventListener('click', (event) => {
            event.stopPropagation();
            menu.classList.toggle('hidden');
            button.setAttribute('aria-expanded', String(!menu.classList.contains('hidden')));
        });
        document.addEventListener('click', event => {
            if (!bell?.contains(event.target)) {
                menu?.classList.add('hidden');
                button?.setAttribute('aria-expanded', 'false');
            }
        });
        list?.addEventListener('click', event => {
            const acceptButton = event.target.closest('[data-accept-alert]');
            if (acceptButton) acceptAlert(Number(acceptButton.dataset.acceptAlert), acceptButton);
            const transferButton = event.target.closest('[data-respond-transfer]');
            if (transferButton) {
                respondToTransfer(Number(transferButton.dataset.respondTransfer), transferButton.dataset.decision, transferButton);
            }
        });

        if (window.Echo && branchId > 0) {
            window.Echo.private(`orders.branch.${branchId}`)
                .listen('KitchenPickupAlertUpdated', event => {
                    const alert = event?.alertData || {};
                    const id = Number(alert.id || 0);
                    if (!id) return;
                    if (alert.status === 'pending') {
                        const isNew = !alerts.has(id);
                        alerts.set(id, alert);
                        if (isNew) {
                            playReadySound();
                            window.showToast?.({ type: 'success', message: `Table ${alert.table_number}: KOT #${alert.kot_number} is ready`, duration: 4000 });
                        }
                    } else {
                        alerts.delete(id);
                        lastReminderAt.delete(id);
                    }
                    render();
                })
                .listen('TableTransferRequestUpdated', event => {
                    const transfer = event?.transferData || {};
                    const id = Number(transfer.id || 0);
                    if (!id) return;
                    window.dispatchEvent(new CustomEvent('table-transfer-assigned', { detail: transfer }));
                    const isSender = Number(transfer.handled_by_waiter_id) === currentUserId;
                    if (isSender && transfer.status !== 'pending') {
                        const updateKey = `${transfer.id}:${transfer.status}:${transfer.updated_at || ''}`;
                        if (announcedTransferUpdates.has(updateKey)) return;
                        rememberTransferUpdate(updateKey);
                        playReadySound();
                        window.showToast?.({
                            type: transfer.status === 'accepted' ? 'success' : 'warning',
                            message: transfer.status === 'accepted'
                                ? `${transfer.target_waiter || 'Waiter'} accepted Table ${transfer.table_number || '--'}.`
                                : `${transfer.target_waiter || 'Waiter'} is busy / Transfer declined for Table ${transfer.table_number || '--'}.`,
                            duration: 4500,
                        });
                    }
                    if (Number(transfer.target_waiter_id) !== Number(@json(auth()->id()))) return;
                    if (transfer.status === 'pending') {
                        transfers.set(id, transfer);
                        playReadySound();
                        window.showToast?.({
                            type: 'warning',
                            message: `Table ${transfer.table_number || '--'} transfer request from ${transfer.from_waiter || 'waiter'}`,
                            duration: 5000,
                        });
                    } else {
                        transfers.delete(id);
                    }
                    render();
                });
        }

        window.addEventListener('kitchen-pickup-alert-resolved', event => {
            const id = Number(event.detail?.id || 0);
            if (!id) return;
            alerts.delete(id);
            lastReminderAt.delete(id);
            render();
        });

        setInterval(() => {
            const now = Date.now();
            let shouldRemind = false;
            alerts.forEach(alert => {
                if (alert.status !== 'pending') return;
                const id = Number(alert.id);
                const readyAt = new Date(alert.ready_at).getTime();
                const lastAt = lastReminderAt.get(id) || readyAt;
                if (now - readyAt >= 120000 && now - lastAt >= 45000) {
                    lastReminderAt.set(id, now);
                    shouldRemind = true;
                }
            });
            if (shouldRemind) playReadySound();
            document.querySelectorAll('#pickupAlertList [data-ready-at]').forEach(node => {
                node.textContent = ageText(node.dataset.readyAt);
            });
        }, 15000);

        loadAlerts();
        loadTransfers();
        syncTransferActivity();
        setInterval(syncTransferActivity, 15000);
    });
</script>
