<main class="flex-1 flex flex-col overflow-hidden">

    <header
        class="bg-gray-800 border-b border-gray-700 px-4 md:px-6 py-4 flex items-center justify-between sticky top-0 ">

        <div class="flex items-center min-w-0">
            <button id="hamburgerBtn" class="lg:hidden text-gray-400 mr-3 focus:outline-none flex-shrink-0">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <h1 class="text-sm md:text-xl font-semibold text-white truncate">
                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'superadmin')
                    <span class="sm:hidden text-orange-500">Admin</span>
                @else
                    {{ ucfirst(auth()->user()->role) }} Panel
                @endif
            </h1>
        </div>

        <div class="hidden sm:flex items-center mx-4 flex-1 justify-center max-w-xs md:max-w-md ">
            @if (($canSwitchBranches ?? false) && ($availableBranches ?? collect())->isNotEmpty())
                <form action="{{ route('admin.branch.switch') }}" method="POST" id="branchSwitcherForm" data-branch-switcher
                    class="relative">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $activeBranch?->id }}">
                    <button type="button" data-branch-trigger aria-haspopup="listbox" aria-expanded="false"
                        class="flex items-center gap-2 min-w-[10rem] max-w-[14rem] px-3 py-2 rounded-xl border border-gray-600/80 bg-gray-800 text-white shadow-sm hover:border-orange-400 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500/40 transition-all">
                        <span class="w-6 h-6 rounded-lg bg-orange-500/15 text-orange-400 flex items-center justify-center shrink-0">
                            <i class="fas fa-building text-[11px]"></i>
                        </span>
                        <span class="flex-1 min-w-0 text-left">
                            <span class="block text-[9px] uppercase tracking-[0.14em] text-gray-500 font-bold leading-none mb-1">Branch</span>
                            <span data-branch-label class="block truncate text-xs md:text-sm font-semibold leading-none">
                                {{ $activeBranch?->branch_name ?? 'Select branch' }}
                            </span>
                        </span>
                        <i data-branch-chevron class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform"></i>
                    </button>

                    <div data-branch-menu role="listbox" class="hidden fixed w-64 overflow-hidden rounded-xl border border-gray-700 bg-gray-900 shadow-2xl shadow-black/30 z-[2147483646]">
                        <div class="px-3.5 py-3 border-b border-gray-800 bg-gray-900">
                            <p class="text-[10px] uppercase tracking-[0.16em] text-gray-500 font-bold">Switch branch</p>
                            <p class="text-[11px] text-gray-400 mt-1">Choose the workspace you want to view</p>
                        </div>
                        @foreach ($availableBranches as $branch)
                            <button type="button" role="option" data-branch-option="{{ $branch->id }}"
                                data-branch-name="{{ $branch->branch_name }}"
                                aria-selected="{{ (int) ($activeBranch?->id ?? 0) === (int) $branch->id ? 'true' : 'false' }}"
                                class="w-full flex items-center gap-3 px-3.5 py-3 text-left text-xs text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ (int) ($activeBranch?->id ?? 0) === (int) $branch->id ? 'bg-orange-500/10 text-orange-300' : '' }}">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center {{ (int) ($activeBranch?->id ?? 0) === (int) $branch->id ? 'bg-orange-500 text-white' : 'bg-gray-800 text-gray-500' }}">
                                    <i class="fas fa-store text-[11px]"></i>
                                </span>
                                <span class="flex-1 min-w-0 truncate font-semibold">{{ $branch->branch_name }}</span>
                                <i class="fas fa-check text-[11px] text-orange-400 {{ (int) ($activeBranch?->id ?? 0) === (int) $branch->id ? '' : 'hidden' }}"></i>
                            </button>
                        @endforeach
                    </div>
                </form>
            @elseif (auth()->user()->role == 'admin' || auth()->user()->role == 'superadmin')
                <div class="flex items-center space-x-2 bg-gray-900/50 px-3 py-1 rounded-lg border border-gray-700">
                    <i class="fas fa-building text-orange-400 text-xs"></i>
                    <span class="text-[10px] md:text-sm text-gray-300 font-semibold truncate">
                        {{ $activeBranch?->branch_name ?? 'No branch assigned' }}
                    </span>
                </div>
            @else
                <div
                    class="flex items-center space-x-2 px-3 py-1 bg-orange-500/10 border border-orange-500/20 rounded-lg">
                    <i class="fas fa-store text-[10px] text-orange-500"></i>
                    <span class="text-[10px] md:text-xs font-bold text-orange-500 uppercase tracking-wider truncate">
                        {{ auth()->user()->branch?->branch_name ?? 'Main Outlet' }}
                    </span>
                </div>
            @endif
        </div>
        <div class="flex items-center space-x-2 md:space-x-4">
            @if (session()->has('impersonated_by'))
                <div class="hidden lg:flex items-center bg-orange-500/10 shadow-sm">
                    <a href="{{ route('impersonate.leave') }}"
                        class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide bg-orange-500 text-white hover:bg-orange-400 transition-colors inline-flex items-center gap-1.5">
                        <i class="fas fa-arrow-left text-[10px]"></i>
                        Return
                    </a>
                </div>
            @endif
            <div class="flex items-center space-x-2 md:space-x-4 flex-shrink-0">
                <button id="theme-toggle"
                    class="hidden sm:block text-gray-400 hover:text-orange-500 text-lg transition-colors">
                    <i id="theme-icon" class="fas fa-sun"></i>
                </button>

                <div id="adminNotificationBell" class="relative z-[9999]">
                    <button id="adminNotificationBellBtn" type="button"
                        class="relative p-1 text-gray-400 hover:text-orange-500 transition-colors"
                        aria-label="Notifications" aria-expanded="false">
                        <i class="fas fa-bell text-lg"></i>
                        <span id="adminNotificationCount"
                            class="hidden absolute -top-1 -right-1 min-w-4 h-4 px-1 bg-orange-500 text-white text-[10px] rounded-full items-center justify-center border-2 border-gray-800">0</span>
                    </button>

                    <div id="adminNotificationMenu"
                        class="hidden fixed right-2 top-16 w-[calc(100vw-1rem)] max-w-sm sm:absolute sm:right-0 sm:top-full sm:mt-3 sm:w-96 bg-gray-800 border border-gray-700 rounded-xl shadow-2xl z-[99999] overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700  z-[9999]">
                            <div>
                                <p class="text-sm font-bold text-white">Notifications</p>
                                <p id="adminNotificationSubtitle" class="text-[11px] text-gray-400">No new notifications</p>
                            </div>
                            <button id="clearAdminNotificationsBtn" type="button"
                                class="text-[11px] font-semibold text-orange-400 hover:text-orange-300">Clear</button>
                        </div>
                        <div id="adminNotificationList" class="max-h-[65vh] overflow-y-auto"></div>
                        <div id="adminNotificationEmpty" class="px-5 py-8 text-center text-sm text-gray-400">
                            <i class="fas fa-check-circle block mb-2 text-xl text-green-500"></i>
                            No new notifications
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
                            <span
                                class="text-sm font-semibold text-white group-hover:text-orange-500 transition-colors">
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

                                @if (session()->has('impersonated_by'))
                                    <a href="{{ route('impersonate.leave') }}"
                                        class="lg:hidden group flex items-center px-4 py-2.5 text-sm text-orange-400 hover:bg-orange-500/10 hover:text-orange-300 transition-all">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-orange-500/10 group-hover:bg-orange-500/20 flex items-center justify-center mr-3 transition-all">
                                            <i
                                                class="fas fa-arrow-left text-orange-400 group-hover:text-orange-300"></i>
                                        </div>
                                        Return
                                    </a>
                                @endif
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
        </div>
    </header>

<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        const branchSwitcher = document.querySelector('[data-branch-switcher]');
        const branchTrigger = branchSwitcher?.querySelector('[data-branch-trigger]');
        const branchMenu = branchSwitcher?.querySelector('[data-branch-menu]');
        const branchInput = branchSwitcher?.querySelector('input[name="branch_id"]');
        const branchChevron = branchSwitcher?.querySelector('[data-branch-chevron]');

        const positionBranchMenu = () => {
            if (!branchMenu || !branchTrigger || branchMenu.classList.contains('hidden')) return;
            const triggerRect = branchTrigger.getBoundingClientRect();
            const menuWidth = branchMenu.offsetWidth || 256;
            const left = Math.min(triggerRect.left, window.innerWidth - menuWidth - 8);
            const top = triggerRect.bottom + 8;

            branchMenu.style.left = `${Math.max(left, 8)}px`;
            branchMenu.style.top = `${top}px`;
        };

        if (branchMenu) document.body.appendChild(branchMenu);

        const closeBranchMenu = () => {
            if (!branchMenu || !branchTrigger) return;
            branchMenu.classList.add('hidden');
            branchTrigger.setAttribute('aria-expanded', 'false');
            branchChevron?.classList.remove('rotate-180');
        };

        branchTrigger?.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = !branchMenu.classList.contains('hidden');
            branchMenu.classList.toggle('hidden', isOpen);
            branchTrigger.setAttribute('aria-expanded', String(!isOpen));
            branchChevron?.classList.toggle('rotate-180', !isOpen);
            if (!isOpen) positionBranchMenu();
        });

        branchMenu?.querySelectorAll('[data-branch-option]').forEach((option) => {
            option.addEventListener('click', () => {
                if (!branchInput || !branchSwitcher) return;
                branchInput.value = option.dataset.branchOption;
                branchSwitcher.classList.add('opacity-60', 'pointer-events-none');
                branchTrigger?.querySelector('[data-branch-label]')?.replaceChildren(
                    document.createTextNode('Switching...')
                );
                branchSwitcher.submit();
            });
        });

        document.addEventListener('click', (event) => {
            if (branchSwitcher && !branchTrigger?.contains(event.target) && !branchMenu?.contains(event.target)) {
                closeBranchMenu();
            }
        });

        window.addEventListener('resize', positionBranchMenu);
        window.addEventListener('scroll', positionBranchMenu, true);

        const branchId = Number(@json((int) session('active_branch_id', auth()->user()->branch_id ?? 0)));
        const currentUserId = Number(@json(auth()->id()));
        const storageKey = `admin-notifications:${branchId}:${currentUserId}`;
        const bell = document.getElementById('adminNotificationBell');
        const button = document.getElementById('adminNotificationBellBtn');
        const menu = document.getElementById('adminNotificationMenu');
        const list = document.getElementById('adminNotificationList');
        const empty = document.getElementById('adminNotificationEmpty');
        const count = document.getElementById('adminNotificationCount');
        const subtitle = document.getElementById('adminNotificationSubtitle');
        const clearButton = document.getElementById('clearAdminNotificationsBtn');
        const notifications = new Map();

        const positionNotificationMenu = () => {
            if (!menu || menu.classList.contains('hidden') || !button) return;

            const buttonRect = button.getBoundingClientRect();
            menu.style.position = 'fixed';
            menu.style.top = `${buttonRect.bottom + 12}px`;
            if (window.innerWidth < 640) {
                menu.style.left = '8px';
                menu.style.right = '8px';
                menu.style.width = 'auto';
                menu.style.maxWidth = 'none';
            } else {
                menu.style.width = '24rem';
                menu.style.maxWidth = 'calc(100vw - 1rem)';
                menu.style.right = `${Math.max(window.innerWidth - buttonRect.right, 8)}px`;
                menu.style.left = 'auto';
            }
            menu.style.zIndex = '2147483647';
        };

        if (menu) document.body.appendChild(menu);

        const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, char => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
        })[char]);

        const timeText = (value) => {
            const date = new Date(value);
            return Number.isNaN(date.getTime()) ? 'Just now' : date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        };

        try {
            JSON.parse(localStorage.getItem(storageKey) || '[]').forEach(item => notifications.set(item.id, item));
        } catch (error) {
            console.warn('Unable to restore admin notifications:', error);
        }

        const persist = () => {
            try {
                localStorage.setItem(storageKey, JSON.stringify([...notifications.values()].slice(0, 20)));
            } catch (error) {
                console.warn('Unable to store admin notifications:', error);
            }
        };

        const render = () => {
            const items = [...notifications.values()].sort((a, b) => new Date(b.time) - new Date(a.time));
            const total = items.length;
            count.textContent = total > 99 ? '99+' : String(total);
            count.classList.toggle('hidden', total === 0);
            count.classList.toggle('flex', total > 0);
            empty.classList.toggle('hidden', total > 0);
            subtitle.textContent = total ? `${total} notification${total === 1 ? '' : 's'}` : 'No new notifications';
            list.innerHTML = items.map(item => `
                <div class="px-4 py-3 border-b border-gray-700/70 last:border-0">
                    <div class="flex items-start gap-3">
                        <i class="fas ${escapeHtml(item.icon)} mt-1 text-orange-400"></i>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-white">${escapeHtml(item.title)}</p>
                            <p class="mt-1 text-xs text-gray-300">${escapeHtml(item.message)}</p>
                            <p class="mt-1 text-[11px] text-gray-500">${escapeHtml(timeText(item.time))}</p>
                        </div>
                    </div>
                </div>`).join('');
        };

        const addNotification = (id, title, message, icon = 'fa-bell') => {
            notifications.set(String(id), { id: String(id), title, message, icon, time: new Date().toISOString() });
            while (notifications.size > 20) notifications.delete(notifications.keys().next().value);
            persist();
            render();
        };

        button?.addEventListener('click', event => {
            event.stopPropagation();
            menu.classList.toggle('hidden');
            button.setAttribute('aria-expanded', String(!menu.classList.contains('hidden')));
            positionNotificationMenu();
        });
        clearButton?.addEventListener('click', () => {
            notifications.clear();
            persist();
            render();
        });
        document.addEventListener('click', event => {
            if (!bell?.contains(event.target) && !menu?.contains(event.target)) {
                menu?.classList.add('hidden');
                button?.setAttribute('aria-expanded', 'false');
            }
        });
        window.addEventListener('resize', positionNotificationMenu);

        if (window.Echo && branchId > 0) {
            window.Echo.private(`orders.branch.${branchId}`)
                .listen('NewOrderReceived', event => {
                    const data = event?.orderData || {};
                    addNotification(`order:${data.id || data.order_id || Date.now()}`, 'New order received', `Table ${data.table_number || '--'} has a new order.`, 'fa-receipt');
                })
                .listen('WaiterCalled', event => {
                    const data = event?.callData || {};
                    addNotification(`call:${data.id || data.table_id || data.table_number}:${Date.now()}`, 'Waiter called', `Table ${data.table_number || '--'} requested a waiter.`, 'fa-bell');
                })
                .listen('BillRequested', event => {
                    const data = event?.requestData || event?.billData || event?.callData || {};
                    addNotification(`bill:${data.id || data.table_id || data.table_number}:${Date.now()}`, 'Bill requested', `Table ${data.table_number || '--'} requested the bill.`, 'fa-file-invoice-dollar');
                })
                .listen('KitchenPickupAlertUpdated', event => {
                    const data = event?.alertData || {};
                    const alertId = Number(data.id || 0);
                    if (!alertId) return;

                    const notificationId = `pickup:${alertId}`;
                    if (data.status === 'pending') {
                        addNotification(
                            notificationId,
                            'Food ready for pickup',
                            `Table ${data.table_number || '--'} KOT #${data.kot_number || '--'} is ready.`,
                            'fa-bell-concierge'
                        );
                        return;
                    }

                    if (data.status === 'accepted') {
                        const notification = notifications.get(notificationId) || {
                            id: notificationId,
                            icon: 'fa-bell-concierge',
                        };
                        notification.title = 'Food pickup accepted';
                        notification.message = `Table ${data.table_number || '--'} KOT #${data.kot_number || '--'} accepted by ${data.accepted_by_waiter || 'waiter'} at ${timeText(data.accepted_at || data.updated_at)}.`;
                        notification.time = data.accepted_at || data.updated_at || notification.time || new Date().toISOString();
                        notifications.set(notificationId, notification);
                        persist();
                        render();
                    }
                })
                .listen('TableTransferRequestUpdated', event => {
                    const data = event?.transferData || {};
                    const transferId = Number(data.id || 0);
                    if (!transferId) return;

                    const notificationId = `transfer:${transferId}`;
                    if (data.status === 'pending') {
                        addNotification(
                            notificationId,
                            'Waiter transfer request',
                            `Table ${data.table_number || '--'} transfer requested from ${data.from_waiter || 'waiter'} to ${data.target_waiter || 'waiter'}.`,
                            'fa-right-left'
                        );
                        return;
                    }

                    if (data.status === 'accepted') {
                        const notification = notifications.get(notificationId) || {
                            id: notificationId,
                            icon: 'fa-right-left',
                        };
                        notification.title = 'Waiter transfer accepted';
                        notification.message = `Table ${data.table_number || '--'} accepted by ${data.target_waiter || 'waiter'} at ${timeText(data.accepted_at || data.updated_at)}.`;
                        notification.time = data.accepted_at || data.updated_at || notification.time || new Date().toISOString();
                        notifications.set(notificationId, notification);
                        persist();
                        render();
                    }
                })
                .listen('KitchenStatusUpdated', event => {
                    const data = event?.kitchenData || {};
                    const status = data.item_status || data.kitchen_status;
                    if (!status || !data.table_number) return;
                    addNotification(`kitchen:${data.id || data.item_id || data.table_number}:${Date.now()}`, 'Kitchen update', `Table ${data.table_number}: ${status}.`, 'fa-kitchen-set');
                });
        }

        render();
    });
</script>
