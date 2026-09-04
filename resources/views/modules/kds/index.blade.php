@extends($layout)

@section('content')
    <div id="kds-page" data-branch-id="{{ (int) (auth()->user()->branch_id ?? 0) }}"
        class="flex-1 overflow-y-auto bg-gray-900 space-y-4 sm:space-y-6 p-3 sm:p-6">
        @if (session('success'))
            <div class="px-4 py-3 rounded-xl border border-green-500/30 bg-green-500/10 text-green-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden">
            <div
                class="px-3 sm:px-6 py-3 sm:py-4 bg-gray-800 border-b border-gray-700/50 backdrop-blur-md flex flex-col xl:flex-row xl:items-center justify-between gap-3 sm:gap-4">

                <div id="kds-filters" data-current-filter="{{ $statusFilter ?? 'all' }}"
                    class="flex items-center gap-2 overflow-x-auto w-full xl:w-auto pb-1">
                    @include('modules.kds.partials.filters', [
                        'statusFilter' => $statusFilter,
                        'stats' => $stats,
                    ])
                </div>

                <div class="flex items-center justify-between sm:justify-end gap-2 sm:gap-3 w-full xl:w-auto flex-wrap">
                    <button id="enable-audio-btn" type="button" aria-pressed="false"
                        class="flex items-center gap-2 px-3 py-2 border border-orange-500/30 text-orange-400 rounded-lg hover:bg-orange-500/20 transition-all">
                        <span class="relative inline-flex items-center justify-center w-4 h-4">
                            <i class="fas fa-volume-up"></i>
                            <i id="audio-off-cross" class="fas fa-slash absolute text-[11px] text-red-400"></i>
                        </span>
                        <span id="audio-btn-label" class="text-xs font-bold">Sound Off</span>
                    </button>
                    <form action="{{ route('admin.kds.mark-all-ready') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 px-3 sm:px-4 py-2 border border-orange-500/30 text-gray-400 rounded-lg transition-all group cursor-pointer hover:bg-orange-500/20 hover:text-orange-500">
                            <i class="far fa-check-circle group-hover:scale-110 transition-transform"></i>
                            <span class="text-xs font-bold text-orange-500 hover:underline">Mark All Ready</span>
                        </button>
                    </form>
                    <span
                        class="px-2.5 sm:px-3 py-1 bg-green-900/30 text-green-400 rounded-full text-[11px] sm:text-xs font-bold animate-pulse">
                        ● Kitchen Live
                    </span>
                    <button id="kds-refresh-btn" type="button"
                        class="p-2 bg-gray-800 border border-gray-700 rounded-lg inline-flex items-center justify-center"
                        title="Refresh live data">
                        <i class="fas fa-sync-alt text-gray-400"></i>
                    </button>
                    <span id="kds-completed-today"
                        class="px-3 py-1 bg-orange-500/10 text-orange-300 border border-orange-500/30 rounded-lg text-[11px] sm:text-xs font-semibold">
                        Completed Today: {{ $stats['completed_today'] ?? 0 }}
                    </span>
                </div>
            </div>

            <div id="kds-cards"
                class="p-3 sm:p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-4">
                @include('modules.kds.partials.cards', ['orderCards' => $orderCards])
            </div>
        </div>
    </div>

    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            if (window.__kdsRealtimeInitialized) return;
            window.__kdsRealtimeInitialized = true;

            const pageRoot = document.getElementById('kds-page');
            const refreshBtn = document.getElementById('kds-refresh-btn');
            const enableAudioBtn = document.getElementById('enable-audio-btn');
            const audioBtnLabel = document.getElementById('audio-btn-label');
            const audioOffCross = document.getElementById('audio-off-cross');

            const currentBranchId = Number(pageRoot?.dataset?.branchId || 0);

            const orderSound = new Audio(@json(asset('sounds/forOrder.m4a')));
            const waiterCallSound = new Audio(@json(asset('sounds/forWaiter.m4a')));
            const kitchenReadySound = new Audio(@json(asset('sounds/forKitchen.m4a')));
            const audioStorageKey = currentBranchId > 0
                ? `kds_sound_enabled_v1:${currentBranchId}`
                : 'kds_sound_enabled_v1';
            orderSound.preload = 'auto';
            waiterCallSound.preload = 'auto';
            kitchenReadySound.preload = 'auto';

            let audioEnabled = localStorage.getItem(audioStorageKey) === '1';

            function setAudioButtonState() {
                if (!enableAudioBtn || !audioBtnLabel || !audioOffCross) return;

                if (audioEnabled) {
                    audioBtnLabel.textContent = 'Sound On';
                    audioOffCross.style.display = 'none';
                    enableAudioBtn.classList.remove('text-orange-400', 'border-orange-500/30');
                    enableAudioBtn.classList.add('text-green-400', 'border-green-500/40', 'bg-green-500/10');
                    enableAudioBtn.setAttribute('aria-pressed', 'true');
                } else {
                    audioBtnLabel.textContent = 'Sound Off';
                    audioOffCross.style.display = 'inline-block';
                    enableAudioBtn.classList.remove('text-green-400', 'border-green-500/40', 'bg-green-500/10');
                    enableAudioBtn.classList.add('text-orange-400', 'border-orange-500/30');
                    enableAudioBtn.setAttribute('aria-pressed', 'false');
                }
            }

            async function toggleAudioState() {
                if (audioEnabled) {
                    audioEnabled = false;
                    localStorage.setItem(audioStorageKey, '0');
                    setAudioButtonState();
                    return;
                }

                try {
                    await orderSound.play();
                    orderSound.pause();
                    orderSound.currentTime = 0;
                    audioEnabled = true;
                    localStorage.setItem(audioStorageKey, '1');
                    setAudioButtonState();
                } catch (error) {
                    audioEnabled = false;
                    localStorage.setItem(audioStorageKey, '0');
                    setAudioButtonState();
                    console.warn('Unable to enable sound:', error);
                }
            }

            setAudioButtonState();

            if (enableAudioBtn) {
                enableAudioBtn.addEventListener('click', toggleAudioState);
            }

            function playOrderSound() {
                if (!audioEnabled) {
                    console.warn('⚠️ Audio is disabled');
                    return;
                }

                orderSound.currentTime = 0;
                orderSound.play().catch(() => {});

                // 📳 vibration (mobile UX)
                if (navigator.vibrate) {
                    navigator.vibrate([200, 100, 200]);
                }
            }

            function playKitchenReadySound() {
                if (!audioEnabled) {
                    console.warn('⚠️ Audio is disabled');
                    return;
                }

                kitchenReadySound.currentTime = 0;
                kitchenReadySound.play().catch(() => {});
            }

            function playWaiterCallSound() {
                if (!audioEnabled) return;

                waiterCallSound.currentTime = 0;
                waiterCallSound.play().catch(() => {});
            }

            function emitKdsToast(type, message) {
                if (!message || typeof window.showToast !== 'function') return;

                window.showToast({
                    type,
                    message,
                    duration: 3500,
                });
            }

            function formatKdsTableLabel(tableNum) {
                const cleanTableNum = String(tableNum ?? '').trim();
                return cleanTableNum ? `Table ${cleanTableNum}` : 'Table';
            }

            function animateHighlight(highlight = {}) {
                const tableNumber = String(highlight?.tableNumber ?? '').trim();
                const kotNumber = String(highlight?.kotNumber ?? '').trim();
                const batchKey = String(highlight?.batchKey ?? (
                    tableNumber && kotNumber ? `${tableNumber}:${kotNumber}` : ''
                )).trim();
                const type = String(highlight?.type ?? 'order');
                const ringClass = type === 'waiter'
                    ? 'ring-blue-500'
                    : (type === 'ready' ? 'ring-green-500' : 'ring-orange-500');
                const selector = batchKey
                    ? `[data-batch-key="${batchKey}"]`
                    : (tableNumber ? `[data-table-number="${tableNumber}"]` : '');

                if (!selector) return;

                document.querySelectorAll(selector).forEach((card) => {
                    card.classList.add('ring-2', ringClass, 'ring-offset-1', 'ring-offset-gray-900');

                    setTimeout(() => {
                        card.classList.remove('ring-2', ringClass, 'ring-offset-1',
                            'ring-offset-gray-900');
                    }, 3000);
                });
            }

            function getCurrentFilter() {
                const filterRoot = document.getElementById('kds-filters');
                return filterRoot?.dataset?.currentFilter || 'all';
            }

            function updateTimers() {
                const cards = document.querySelectorAll('#kds-cards [data-batch-key]');

                cards.forEach((card) => {
                    const createdAt = card.getAttribute('data-created-at');
                    const timerNode = card.querySelector('.kds-timer');

                    if (!createdAt || !timerNode) return;

                    const startedAt = new Date(createdAt);
                    if (Number.isNaN(startedAt.getTime())) return;

                    const elapsedMs = Date.now() - startedAt.getTime();
                    const elapsedMinutes = Math.max(0, Math.floor(elapsedMs / 60000));

                    const hours = String(Math.floor(elapsedMinutes / 60)).padStart(2, '0');
                    const mins = String(elapsedMinutes % 60).padStart(2, '0');

                    timerNode.textContent = `Timer: ${hours}:${mins}m`;
                });
            }

            async function refreshKdsData(highlight = null) {
                if (!pageRoot) return;

                const previousBtnHtml = refreshBtn ? refreshBtn.innerHTML : '';

                if (refreshBtn) {
                    refreshBtn.disabled = true;
                    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-gray-400"></i>';
                }

                try {
                    const url = new URL(window.location.href);
                    url.searchParams.set('ajax', '1');
                    url.searchParams.set('status', getCurrentFilter());

                    const response = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        cache: 'no-store'
                    });

                    if (!response.ok) throw new Error(`Refresh failed: ${response.status}`);

                    const data = await response.json();

                    const currentFilters = document.getElementById('kds-filters');
                    const currentCards = document.getElementById('kds-cards');
                    const currentCompleted = document.getElementById('kds-completed-today');

                    if (currentFilters && typeof data.filters === 'string') {
                        currentFilters.innerHTML = data.filters;
                    }

                    if (currentCards && typeof data.cards === 'string') {
                        currentCards.innerHTML = data.cards;
                    }

                    if (currentCompleted) {
                        currentCompleted.textContent = `Completed Today: ${Number(data.completed_today || 0)}`;
                    }

                    if (highlight && (highlight.tableNumber || highlight.batchKey || highlight.kotNumber)) {
                        animateHighlight(highlight);
                    }

                    updateTimers();

                } catch (error) {
                    console.error('KDS live refresh error:', error);
                } finally {
                    if (refreshBtn) {
                        refreshBtn.disabled = false;
                        refreshBtn.innerHTML = previousBtnHtml;
                    }
                }
            }

            if (refreshBtn) {
                refreshBtn.addEventListener('click', () => refreshKdsData());
            }

            updateTimers();
            setInterval(updateTimers, 60000);

            // 🔥 Realtime (PRIVATE CHANNEL FIXED)
            if (!window.Echo || currentBranchId <= 0) return;

            window.Echo.private(`orders.branch.${currentBranchId}`)
                .listen('NewOrderReceived', async (e) => {
                    playOrderSound();

                    await refreshKdsData({
                        tableNumber: e?.orderData?.table_number ?? '',
                        kotNumber: e?.orderData?.kot_number ?? '',
                        batchKey: e?.orderData?.batch_key ?? '',
                        type: 'order'
                    });
                })
                .listen('KitchenStatusUpdated', async (e) => {
                    const payload = e?.kitchenData || {};
                    const itemStatus = String(payload?.item_status ?? '').toLowerCase();
                    const kitchenStatus = String(payload?.kitchen_status ?? '').toLowerCase();
                    const isReady = itemStatus === 'ready';
                    const isServed = itemStatus === 'served' || (kitchenStatus === 'served' && !isReady);
                    await refreshKdsData({
                        tableNumber: payload?.table_number ?? '',
                        kotNumber: payload?.kot_number ?? '',
                        batchKey: payload?.batch_key ?? '',
                        type: isReady || isServed ? 'ready' : 'order'
                    });

                    if (isReady) {
                        playKitchenReadySound();
                        emitKdsToast('success', `${formatKdsTableLabel(payload?.table_number)}: Kitchen ready`);
                    }

                    if (isServed) {
                        emitKdsToast('success', `${formatKdsTableLabel(payload?.table_number)}: Served`);
                    }
                })
                .listen('WaiterCalled', async (e) => {
                    playWaiterCallSound();

                    await refreshKdsData({
                        tableNumber: e?.callData?.table_number ?? '',
                        type: 'waiter'
                    });
                });

        });
    </script>
@endsection
