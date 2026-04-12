<div id="drawerOverlay" class="fixed inset-0 bg-black/50 hidden z-40 "></div>
@php
    $orderPanelRoute = request()->routeIs('waiter.*') ? route('order.index') : route('admin.order.index');
@endphp

<div id="drawer"
    class="fixed top-0 right-0 w-full sm:w-[400px] h-full bg-[#1a1c1e] bg-gray-900 border-l border-gray-800 transform translate-x-full transition-transform duration-300 z-50 shadow-2xl flex flex-col">

    <div class="p-5 border-b border-gray-800">
        <div class="flex justify-between items-center text-white">
            <div class="flex items-center gap-2">
                <span class="text-orange-500 text-xl">🍌</span>
                <h2 id="drawerTitle" class="font-bold text-lg">Order Details</h2>
            </div>
            <button id="closeDrawer" class="text-gray-500 hover:text-red-500 cursor-pointer">✖</button>
        </div>
        <p id="drawerSubtitle" class="text-xs text-gray-500 mt-1">Select a table to view orders</p>
    </div>

    <div id="drawerContent" class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar bg-gray-900">
        <div id="activeOrdersList">
            <div class="text-center p-10 text-gray-500">Select a table to view active orders</div>
        </div>
    </div>

    <div class="p-4 border-t border-gray-700 space-y-3">
        <button id="drawerAddItemBtn"
            class="w-full bg-orange-500 hover:bg-orange-700 text-white font-bold py-3 rounded-lg shadow-lg">
            Add Item
        </button>
        <button class="w-full border border-orange-500 text-orange-400 font-semibold py-2.5 rounded-lg">
            Generate Bill
        </button>
    </div>
</div>

<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        const currentBranchId = Number(@json((int) (auth()->user()->branch_id ?? 0)));
        const orderSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
        const kitchenReadySound = new Audio(
            'https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');

        function playWaiterCallSound() {
            // Distinct tone from order sound: short double-beep
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;

            const ctx = new AudioCtx();
            const now = ctx.currentTime;

            const beep = (start, duration, freq) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.value = freq;
                gain.gain.setValueAtTime(0.0001, start);
                gain.gain.exponentialRampToValueAtTime(0.08, start + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, start + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(start);
                osc.stop(start + duration + 0.02);
            };

            beep(now, 0.15, 880);
            beep(now + 0.2, 0.18, 1046);

            setTimeout(() => {
                ctx.close().catch(() => {});
            }, 800);
        }

        if (window.Echo && currentBranchId > 0) {
            window.Echo.private(`orders.branch.${currentBranchId}`)
                .listen('NewOrderReceived', async (e) => {
                    // 1. Alert Admin
                    orderSound.play().catch(() => {});

                    const tableNum = String(e.orderData.table_number);
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

                    // 3. 🔥 THE TRIGGER: Agar wahi table open hai, toh fetch karo
                    // Ya phir hamesha fetch karo taaki background state update rahe
                    if (isCurrentTableOpen) {
                        await window.refreshFromServer(tableNum);
                    }
                })
                .listen('WaiterCalled', (e) => {
                    playWaiterCallSound();

                    const tableNum = String(e.callData.table_number);
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

                    if ((itemStatus === 'ready' || kitchenStatus === 'served') &&
                        typeof window.markTableAsKitchenReady === 'function') {
                        window.markTableAsKitchenReady(tableNum);
                        kitchenReadySound.currentTime = 0;
                        kitchenReadySound.play().catch(() => {});
                    }

                    if (window.currentOpenTable === tableNum && typeof window.refreshFromServer ===
                        'function') {
                        await window.refreshFromServer(tableNum);
                    }
                });
        }

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
