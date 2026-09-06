<main class="flex h-screen flex-1 flex-col overflow-hidden">

    <header
        class="bg-gray-800 border-b border-gray-700 px-4 md:px-6 py-4 flex items-center justify-between sticky top-0 z-[110]">

        <div class="flex items-center min-w-0 gap-2">
            @if (auth()->user()->role == 'chef')
                <a href="{{ route('chef.kds.index') }}" class="flex items-center gap-2 group transition-all">
                    <span
                        class="px-3 py-2 bg-orange-500 rounded-lg shadow-lg shadow-orange-500/20 group-hover:bg-orange-600">
                        <i class="fas fa-utensils text-white text-xs md:text-sm"></i>
                    </span>
                    <h1 class="text-sm md:text-xl font-semibold text-white truncate group-hover:text-orange-500">
                        KDS <span class="hidden sm:inline">Module</span>
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
                    {{ auth()->user()->branch?->branch_name ?? 'No branch assigned' }}
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
            <button id="theme-toggle"
                class="hidden sm:block text-gray-400 hover:text-orange-500 text-lg transition-colors">
                <i id="theme-icon" class="fas fa-sun"></i>
            </button>

            <div id="chefNotificationBell" class="relative z-[9999]">
                <button id="chefNotificationBellBtn" type="button"
                    class="relative p-1 text-gray-400 hover:text-orange-500 transition-colors"
                    aria-label="Chef notifications" aria-expanded="false">
                    <i class="fas fa-bell text-lg"></i>
                    <span id="chefNotificationCount"
                        class="hidden absolute -top-1 -right-1 min-w-4 h-4 px-1 bg-orange-500 text-white text-[10px] rounded-full items-center justify-center border-2 border-gray-800">0</span>
                </button>

                <div id="chefNotificationMenu"
                    class="hidden fixed right-2 top-16 w-[calc(100vw-1rem)] max-w-sm sm:absolute sm:right-0 sm:top-full sm:mt-3 sm:w-96 bg-gray-800 border border-gray-700 rounded-xl shadow-2xl z-[99999] overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
                        <div>
                            <p class="text-sm font-bold text-white">Kitchen notifications</p>
                            <p id="chefNotificationSubtitle" class="text-[11px] text-gray-400">No new notifications</p>
                        </div>
                        <button id="clearChefNotificationsBtn" type="button"
                            class="text-[11px] font-semibold text-orange-400 hover:text-orange-300">Clear all</button>
                    </div>
                    <div id="chefNotificationList" class="max-h-[65vh] overflow-y-auto"></div>
                    <div id="chefNotificationEmpty" class="px-5 py-8 text-center text-sm text-gray-400">
                        <i class="fas fa-check-circle block mb-2 text-xl text-green-500"></i>
                        No new notifications
                    </div>
                </div>
            </div>

            <div id="chefNotificationOverlay"
                class="hidden fixed inset-0 bg-black/20 backdrop-blur-[1px] z-[9990] transition-opacity"
                aria-hidden="true"></div>

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
                            <a href="{{ route('chef.profile') }}"
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
            if (window.__chefNotificationsInitialized) return;
            window.__chefNotificationsInitialized = true;

            const branchId = Number(@json((int) (auth()->user()->branch_id ?? 0)));
            const soundStorageKey = branchId > 0 ? `kds_sound_enabled_v1:${branchId}` : 'kds_sound_enabled_v1';
            const notificationUrl = @json(route('admin.kds.notifications.index'));
            const notificationOpenedUrl = @json(route('admin.kds.notifications.opened'));
            const notificationClearUrl = @json(route('admin.kds.notifications.clear'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const bell = document.getElementById('chefNotificationBell');
            const button = document.getElementById('chefNotificationBellBtn');
            const menu = document.getElementById('chefNotificationMenu');
            const overlay = document.getElementById('chefNotificationOverlay');
            const list = document.getElementById('chefNotificationList');
            const empty = document.getElementById('chefNotificationEmpty');
            const count = document.getElementById('chefNotificationCount');
            const subtitle = document.getElementById('chefNotificationSubtitle');
            const clearButton = document.getElementById('clearChefNotificationsBtn');
            const cancellationSound = new Audio(@json(asset('Sounds/forNotification.mp3')));
            const notifications = new Map();

            cancellationSound.preload = 'auto';

            const escapeHtml = (value) => String(value ?? '').replace(/[&<>\'"]/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
            })[char]);
            const timeText = (value) => {
                const date = new Date(value);
                return Number.isNaN(date.getTime()) ? 'Just now' : date.toLocaleTimeString([], {
                    hour: '2-digit', minute: '2-digit'
                });
            };
            const soundEnabled = () => localStorage.getItem(soundStorageKey) === '1';
            const unlockNotificationSound = () => {
                cancellationSound.muted = true;
                cancellationSound.currentTime = 0;
                cancellationSound.play().then(() => {
                    cancellationSound.pause();
                    cancellationSound.currentTime = 0;
                    cancellationSound.muted = false;
                    localStorage.setItem(soundStorageKey, '1');
                }).catch(() => {
                    cancellationSound.muted = false;
                });
            };
            const playCancellationSound = () => {
                if (!soundEnabled()) return;
                cancellationSound.currentTime = 0;
                cancellationSound.play().catch(() => {});
                if (navigator.vibrate) navigator.vibrate([180, 90, 180]);
            };

            const loadNotifications = async () => {
                try {
                    const response = await fetch(notificationUrl, {
                        headers: { Accept: 'application/json' },
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    notifications.clear();
                    (data.notifications || []).forEach(item => notifications.set(item.id, {
                        ...item,
                        itemName: item.item_name,
                        tableNumber: item.table_number,
                        cancelledBy: item.cancelled_by,
                        reason: item.reason,
                        time: item.cancelled_at,
                        read: Boolean(item.opened_at),
                    }));
                    render();
                } catch (error) {
                    console.warn('Unable to load chef notification history:', error);
                }
            };

            const render = () => {
                const items = [...notifications.values()].sort((a, b) => new Date(b.time) - new Date(a.time));
                const unreadCount = items.filter(item => !item.read).length;
                count.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
                count.classList.toggle('hidden', unreadCount === 0);
                count.classList.toggle('flex', unreadCount > 0);
                empty.classList.toggle('hidden', items.length > 0);
                subtitle.textContent = unreadCount
                    ? `${unreadCount} unread notification${unreadCount === 1 ? '' : 's'}`
                    : 'All notifications read';
                list.innerHTML = items.map(item => `
                    <div class="px-4 py-3 border-b border-gray-700/70 last:border-0 ${item.read ? 'opacity-65' : 'bg-orange-500/5'}">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 w-7 h-7 rounded-lg flex items-center justify-center ${item.read ? 'bg-gray-700 text-gray-400' : 'bg-red-500/15 text-red-400'}">
                                <i class="fas fa-ban text-xs"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold ${item.read ? 'text-gray-300' : 'text-white'}">Item cancelled by ${escapeHtml(item.cancelledBy || 'staff')}</p>
                                <p class="mt-1 text-xs text-gray-300 truncate">${escapeHtml(item.itemName)} &bull; Table ${escapeHtml(item.tableNumber || '--')}</p>
                                <p class="mt-1 text-[11px] text-gray-400">${escapeHtml(item.cancelledBy)} &bull; ${timeText(item.time)}</p>
                                <p class="mt-1 text-[11px] text-red-300/80">Reason: ${escapeHtml(item.reason || 'No reason provided')}</p>
                            </div>
                            ${item.read ? '<span class="text-[10px] text-gray-500 uppercase font-bold">Read</span>' : '<span class="text-[10px] text-orange-400 uppercase font-bold">New</span>'}
                        </div>
                    </div>`).join('');
            };

            const markAllRead = async () => {
                try {
                    await fetch(notificationOpenedUrl, {
                        method: 'POST',
                        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    });
                    notifications.forEach(item => { item.read = true; });
                    render();
                } catch (error) {
                    console.warn('Unable to mark chef notifications as opened:', error);
                }
            };

            const clearAllNotifications = async () => {
                try {
                    await fetch(notificationClearUrl, {
                        method: 'DELETE',
                        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    });
                    notifications.clear();
                    render();
                } catch (error) {
                    console.warn('Unable to clear chef notifications:', error);
                }
            };

            button?.addEventListener('click', () => {
                const isOpen = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden', isOpen);
                overlay?.classList.toggle('hidden', isOpen);
                button.setAttribute('aria-expanded', String(!isOpen));
                unlockNotificationSound();
                if (!isOpen) markAllRead();
            });
            clearButton?.addEventListener('click', clearAllNotifications);
            overlay?.addEventListener('click', () => {
                menu.classList.add('hidden');
                overlay.classList.add('hidden');
                button?.setAttribute('aria-expanded', 'false');
            });
            document.addEventListener('click', event => {
                if (bell && !bell.contains(event.target)) {
                    menu.classList.add('hidden');
                    overlay?.classList.add('hidden');
                    button?.setAttribute('aria-expanded', 'false');
                }
            });
            render();
            loadNotifications();

            if (!window.Echo || branchId <= 0) return;
            window.Echo.private(`orders.branch.${branchId}`).listen('KitchenStatusUpdated', event => {
                const payload = event?.kitchenData || {};
                if (String(payload.item_status || '').toLowerCase() !== 'rejected') return;

                const notification = {
                    itemName: payload.item_name || 'Item',
                    cancelledBy: payload.cancelled_by || 'Waiter',
                };
                loadNotifications();
                playCancellationSound();
                window.showToast?.({
                    type: 'warning',
                    message: `${notification.itemName} cancelled by ${notification.cancelledBy}.`,
                    duration: 5000,
                });
            });
        });
    </script>
