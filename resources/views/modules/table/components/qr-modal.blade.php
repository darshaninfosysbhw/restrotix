@if ($isAdmin ?? false)
    <!-- Add Table MODAL -->
    <div id="tableModal" class="fixed inset-0 z-[120] hidden overflow-y-auto">
        <div id="tableModalBackdrop" class="absolute inset-0 bg-black/50"></div>

        <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-xl bg-gray-800 border border-gray-700 rounded-xl">
                <div class="px-5 py-4 border-b border-gray-700 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-white" id="modalTitle">Add Tables</h2>
                        <p class="text-xs text-gray-400">Bulk create tables with QR</p>
                    </div>
                    <button id="closeTableModal" class="text-gray-400 cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.tables.bulk-store') }}" class="p-4 sm:p-5 space-y-5"
                    id="tableForm">
                    @csrf
                    <input type="hidden" id="formMethodField" name="_method" value="POST">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="relative group">
                            <label class="block text-sm text-gray-400 mb-1.5 font-medium">
                                Branch <span class="text-orange-500">*</span>
                            </label>

                            <div class="relative">
                                <select id="branchSelect" name="branch_id"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500 appearance-none cursor-pointer transition-all hover:border-gray-600">
                                    <option value="" disabled selected class="bg-gray-800 text-gray-500">Select
                                        Branch
                                    </option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" class="bg-gray-800 text-white py-2">
                                            {{ $branch->branch_name }}
                                        </option>
                                    @endforeach
                                </select>

                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                    <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div id="tableCountGroup">
                            <label class="block text-sm text-gray-400 mb-1.5 font-medium">
                                Number Of Tables <span class="text-orange-500">*</span>
                            </label>
                            <input type="text" placeholder="ex. 10" id="tableCount" name="table_count"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div id="startNumberGroup">
                            <label class="block text-sm text-gray-400 mb-1.5 font-medium">
                                Starting Number <span class="text-orange-500">*</span>
                            </label>
                            <input type="text" name="start_number" placeholder="ex. 1" id="startNumber"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        <div id="tableNumberGroup" class="hidden">
                            <label class="block text-sm text-gray-400 mb-1.5 font-medium">
                                Table Number <span class="text-orange-500">*</span>
                            </label>
                            <input type="text" name="table_number" placeholder="ex. T-01" id="tableNumber"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-400 mb-1.5 font-medium">
                                Capacity <span class="text-orange-500">*</span>
                            </label>
                            <input type="text" name="capacity" placeholder="ex. 4" id="capacity"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div id="statusGroup" class="hidden">
                        <label class="block text-sm text-gray-400 mb-1.5 font-medium">
                            Status <span class="text-orange-500">*</span>
                        </label>
                        <select id="status" name="status"
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                            <option value="occupied">Occupied</option>
                            <option value="calling_waiter">Calling Waiter</option>
                            <option value="out_of_service">Out Of Service</option>
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-2 pt-2">
                        <button type="button" id="closeTableModal"
                            class="w-full sm:w-auto px-4 py-2.5 rounded text-sm bg-white/5 hover:bg-white/10 text-gray-300 border border-gray-500/30 transition cursor-pointer">
                            Cancel
                        </button>

                        <button type="submit" id="submitBtn"
                            class="w-full sm:w-auto px-4 py-2.5 rounded text-sm bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 transition cursor-pointer">
                            <i class="fas fa-save mr-2"></i> Generate Tables
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@include('core.components.table.table-drawer')

<div id="qrModal" class="fixed inset-0 z-[130] hidden">

    <!-- BACKDROP -->
    <div class="absolute inset-0 bg-black/60"></div>

    <!-- MODAL WRAPPER -->
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">

        <!-- INNER BOX -->
        <div id="qrBox"
            class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-md text-center relative border border-orange-100">

            <!-- ❌ CLOSE BUTTON -->
            <button id="qrCloseBtn"
                class="absolute top-3 right-5 text-gray-400 hover:text-black text-2xl leading-none cursor-pointer">
                &times;
            </button>

            <!-- QR -->
            <div class="rounded-2xl border-4 border-orange-400 p-5 inline-block bg-white shadow-sm">
                <img id="qrImage" src="" class="w-56 h-56 object-contain mx-auto">
            </div>

            <!-- TEXT -->
            <p id="qrTitle" class="text-base font-semibold text-gray-800 mt-5"></p>
            <p class="text-xs text-gray-500 mt-1">Scan to open menu</p>

            <!-- BUTTONS -->
            <div class="flex flex-col sm:flex-row justify-center gap-3 mt-6">
                <a id="downloadBtn" href="#" download
                    class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium">
                    Download PNG
                </a>

                <button id="printSingleBtn" type="button"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium">
                    Print PDF
                </button>
            </div>

        </div>
    </div>
</div>

<div id="printSheet" style="display: none;"></div>

<style>
    @keyframes waiterCardNudge {
        0% {
            transform: translateX(0);
        }

        20% {
            transform: translateX(1.5px);
        }

        40% {
            transform: translateX(-1.5px);
        }

        60% {
            transform: translateX(1px);
        }

        80% {
            transform: translateX(-1px);
        }

        100% {
            transform: translateX(0);
        }
    }

    .waiter-call-active {
        animation: waiterCardNudge 1.2s ease-in-out infinite;
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.45), 0 0 18px rgba(59, 130, 246, 0.2);
    }

    @keyframes kitchenReadyBlink {
        0% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(34, 197, 94, 0.08);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5);
        }
    }

    .kitchen-ready-active {
        animation: kitchenReadyBlink 1s ease-in-out infinite;
        border-color: rgba(34, 197, 94, 0.65) !important;
    }

    #printSheet .sheet-title {
        text-align: center;
        font-size: 22px;
        margin-bottom: 18px;
        font-weight: 700;
        color: #1f2937;
    }

    #printSheet .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 14px;
    }

    #printSheet .card {
        border: 2px solid #fb923c;
        border-radius: 14px;
        padding: 12px;
        text-align: center;
        page-break-inside: avoid;
        background: #fff;
    }

    #printSheet .card h3 {
        margin: 0 0 8px;
        font-size: 16px;
        color: #111827;
    }

    #printSheet .qr-box {
        border: 2px solid #fdba74;
        border-radius: 10px;
        padding: 10px;
        display: inline-block;
        background: #fff;
    }

    #printSheet .qr-box img {
        width: 180px;
        height: 180px;
        object-fit: contain;
    }

    #printSheet .note {
        margin-top: 8px;
        font-size: 11px;
        color: #6b7280;
    }

    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }

        body.print-mode * {
            visibility: hidden !important;
        }

        body.print-mode #printSheet,
        body.print-mode #printSheet * {
            visibility: visible !important;
        }

        body.print-mode #printSheet {
            display: block !important;
            position: absolute;
            inset: 0;
            padding: 0;
            background: #fff;
        }
    }
</style>


<!-- This Script Used for closed open modal and edit form-->
<script>
    (function() {
        const modal = document.getElementById('tableModal');
        const openBtn = document.getElementById('openTableModal');
        const closeBtns = document.querySelectorAll('#closeTableModal');
        const backdrop = document.getElementById('tableModalBackdrop');

        const modalTitle = document.getElementById('modalTitle');
        const tableForm = document.getElementById('tableForm');
        const formMethodField = document.getElementById('formMethodField');
        const branchSelect = document.getElementById('branchSelect');
        const tableCount = document.getElementById('tableCount');
        const startNumber = document.getElementById('startNumber');
        const tableNumber = document.getElementById('tableNumber');
        const capacity = document.getElementById('capacity');
        const status = document.getElementById('status');
        const submitBtn = document.getElementById('submitBtn');
        const tableCountGroup = document.getElementById('tableCountGroup');
        const startNumberGroup = document.getElementById('startNumberGroup');
        const tableNumberGroup = document.getElementById('tableNumberGroup');
        const statusGroup = document.getElementById('statusGroup');

        const editButtons = document.querySelectorAll('.editBtn');

        if (!modal || !tableForm || !formMethodField || !branchSelect || !tableCount || !startNumber || !
            tableNumber ||
            !capacity || !status || !submitBtn || !tableCountGroup || !startNumberGroup || !tableNumberGroup ||
            !statusGroup) {
            return;
        }

        function setAddMode() {
            modalTitle.innerText = "Add Tables";
            tableForm.action = "{{ route('admin.tables.bulk-store') }}";
            formMethodField.value = "POST";
            submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Generate Tables';

            tableCountGroup.classList.remove('hidden');
            startNumberGroup.classList.remove('hidden');
            tableNumberGroup.classList.add('hidden');
            statusGroup.classList.add('hidden');

            tableCount.required = true;
            startNumber.required = true;
            tableNumber.required = false;
            status.required = false;
        }

        function setEditMode(updateUrl) {
            modalTitle.innerText = "Edit Table";
            tableForm.action = updateUrl;
            formMethodField.value = "PUT";
            submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Update Table';

            tableCountGroup.classList.add('hidden');
            startNumberGroup.classList.add('hidden');
            tableNumberGroup.classList.remove('hidden');
            statusGroup.classList.remove('hidden');

            tableCount.required = false;
            startNumber.required = false;
            tableNumber.required = true;
            status.required = true;
        }

        function openModal() {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // 👉 ADD MODE
        if (openBtn) {
            openBtn.addEventListener('click', () => {
                setAddMode();
                branchSelect.value = "";
                tableCount.value = "";
                startNumber.value = "";
                tableNumber.value = "";
                capacity.value = "";
                status.value = "available";
                openModal();
            });
        }

        // 👉 EDIT MODE
        editButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                setEditMode(btn.dataset.updateUrl);
                branchSelect.value = btn.dataset.branch;
                tableNumber.value = btn.dataset.tableNumber;
                capacity.value = btn.dataset.capacity;
                status.value = btn.dataset.status;

                openModal();
            });
        });

        // 👉 CLOSE EVENTS
        closeBtns.forEach(btn => btn.addEventListener('click', closeModal));
        if (backdrop) {
            backdrop.addEventListener('click', closeModal);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });

    })();
</script>

{{-- this script used for view menu card and download --}}
<script>
    (function() {

        const qrModal = document.getElementById('qrModal');
        const qrBox = document.getElementById('qrBox');
        const qrCloseBtn = document.getElementById('qrCloseBtn');
        const printAllQrBtn = document.getElementById('printAllQrBtn');

        const qrImage = document.getElementById('qrImage');
        const qrTitle = document.getElementById('qrTitle');
        const downloadBtn = document.getElementById('downloadBtn');
        const printSingleBtn = document.getElementById('printSingleBtn');
        const printSheet = document.getElementById('printSheet');

        const viewButtons = document.querySelectorAll('.viewQrBtn');
        const qrImages = document.querySelectorAll('.qrPreview');
        let activeQr = null;

        if (!qrModal || !qrBox || !qrCloseBtn || !qrImage || !qrTitle || !downloadBtn || !printSingleBtn ||
            !printSheet) {
            return;
        }

        async function convertImageUrlToPngDataUrl(imageUrl) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';

                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const size = 900;
                    canvas.width = size;
                    canvas.height = size;

                    const ctx = canvas.getContext('2d');
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, size, size);

                    // Modal jaisa simple orange border QR box
                    const qrBox = 760;
                    const qrBoxX = (size - qrBox) / 2;
                    const qrBoxY = (size - qrBox) / 2;
                    ctx.lineWidth = 12;
                    ctx.strokeStyle = '#fb923c';
                    ctx.strokeRect(qrBoxX, qrBoxY, qrBox, qrBox);

                    const padding = 48;
                    ctx.drawImage(
                        img,
                        qrBoxX + padding,
                        qrBoxY + padding,
                        qrBox - (padding * 2),
                        qrBox - (padding * 2)
                    );

                    resolve(canvas.toDataURL('image/png'));
                };

                img.onerror = () => reject(new Error('Unable to load QR image.'));
                img.src = imageUrl;
            });
        }

        async function downloadQrAsPng(qrUrl, fileName) {
            const pngDataUrl = await convertImageUrlToPngDataUrl(qrUrl);
            const tempLink = document.createElement('a');
            tempLink.href = pngDataUrl;
            tempLink.download = fileName;
            document.body.appendChild(tempLink);
            tempLink.click();
            tempLink.remove();
        }

        function runFastPrint(cardsHtml, title = 'QR Codes') {
            printSheet.innerHTML = `
                <div class="sheet-title">${title}</div>
                <div class="grid">${cardsHtml}</div>
            `;

            closeQR();
            document.body.classList.add('print-mode');

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    window.print();
                });
            });
        }

        function buildQrCardHtml(name, qrUrl) {
            return `
                <div class="card">
                    <h3>${name}</h3>
                    <div class="qr-box">
                        <img src="${qrUrl}" alt="${name}" />
                    </div>
                    <div class="note">Scan for menu access</div>
                </div>
            `;
        }

        function openQR(data) {
            qrImage.src = data.qr;
            qrTitle.innerText = "Scan for Menu - " + data.name;
            activeQr = data;

            qrModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeQR() {
            qrModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // 👉 View button
        viewButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                openQR({
                    name: btn.dataset.name,
                    qr: btn.dataset.qr
                });
            });
        });

        // 👉 QR image click
        qrImages.forEach(img => {
            img.addEventListener('click', () => {
                openQR({
                    name: img.dataset.name,
                    qr: img.dataset.qr
                });
            });
        });

        // ✅ OUTSIDE CLICK CLOSE (BEST LOGIC)
        qrModal.addEventListener('click', (e) => {
            if (!qrBox.contains(e.target)) {
                closeQR();
            }
        });

        // ✅ CROSS BUTTON
        qrCloseBtn.addEventListener('click', closeQR);

        // ✅ ESC KEY
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeQR();
        });

        downloadBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!activeQr) return;
            const safeName = activeQr.name.replace(/\s+/g, '-').toLowerCase();
            await downloadQrAsPng(activeQr.qr, `${safeName}.png`);
        });

        printSingleBtn.addEventListener('click', () => {
            if (!activeQr) return;
            const cardHtml = buildQrCardHtml(activeQr.name, activeQr.qr);
            runFastPrint(cardHtml, `${activeQr.name} QR`);
        });

        if (printAllQrBtn) {
            printAllQrBtn.addEventListener('click', () => {
                const cards = Array.from(viewButtons).map((btn) => {
                    return buildQrCardHtml(btn.dataset.name, btn.dataset.qr);
                });

                if (!cards.length) return;
                runFastPrint(cards.join(''), 'All Table QR Codes');
            });
        }

        window.addEventListener('afterprint', () => {
            document.body.classList.remove('print-mode');
            printSheet.innerHTML = '';
        });
        document.querySelectorAll('.viewQrBtn, .editBtn, .qrPreview')
            .forEach(el => {
                el.addEventListener('click', (e) => e.stopPropagation());
            });
    })();
</script>
<script>
    (function() {
        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('drawerOverlay');
        const listArea = document.getElementById('activeOrdersList');
        const ALERT_STORAGE_KEY = 'table_order_activity_v1';
        const WAITER_ALERT_STORAGE_KEY = 'table_waiter_call_activity_v1';
        window.currentOpenTable = null;
        let activeFetchController = null;
        let activeRequestId = 0;
        let tableOrderActivity = {};
        let tableWaiterCallActivity = {};

        if (!drawer || !overlay || !listArea) {
            return;
        }

        function normalizeTableNum(tableNum) {
            return String(tableNum ?? '');
        }

        function loadTableOrderActivity() {
            try {
                tableOrderActivity = JSON.parse(localStorage.getItem(ALERT_STORAGE_KEY) || '{}');
            } catch (e) {
                tableOrderActivity = {};
            }
        }

        function saveTableOrderActivity() {
            localStorage.setItem(ALERT_STORAGE_KEY, JSON.stringify(tableOrderActivity));
        }

        function loadWaiterCallActivity() {
            try {
                tableWaiterCallActivity = JSON.parse(localStorage.getItem(WAITER_ALERT_STORAGE_KEY) || '{}');
            } catch (e) {
                tableWaiterCallActivity = {};
            }
        }

        function saveWaiterCallActivity() {
            localStorage.setItem(WAITER_ALERT_STORAGE_KEY, JSON.stringify(tableWaiterCallActivity));
        }

        function formatActivityTime(timestamp) {
            if (!timestamp) return '';
            const dt = new Date(timestamp);
            if (Number.isNaN(dt.getTime())) return '';
            return dt.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function applyCardActivityUi(tableNum) {
            const normalizedTableNum = normalizeTableNum(tableNum);
            const card = document.querySelector(`.table-card[data-table-number="${normalizedTableNum}"]`);
            if (!card) return;

            const activity = tableOrderActivity[normalizedTableNum] || {};
            const waiterActivity = tableWaiterCallActivity[normalizedTableNum] || {};
            const badge = card.querySelector('.new-order-badge');
            const activityText = card.querySelector('.last-order-activity');
            const waiterBell = card.querySelector('.waiter-call-bell');
            const waiterCount = card.querySelector('.waiter-call-count');
            const unreadCount = Number(activity.unread_count || 0);
            const waiterUnreadCount = Number(waiterActivity.unread_count || 0);

            if (badge) {
                if (unreadCount > 0) {
                    badge.classList.remove('hidden');
                    badge.textContent = unreadCount > 1 ? `New x${unreadCount}` : 'New';
                } else {
                    badge.classList.add('hidden');
                    badge.textContent = 'New';
                }
            }

            if (activityText) {
                if (activity.last_order_at) {
                    const formattedTime = formatActivityTime(activity.last_order_at);
                    activityText.classList.remove('hidden');
                    activityText.textContent = formattedTime ?
                        `Last incoming order: ${formattedTime}` :
                        'Last incoming order received';
                } else {
                    activityText.classList.add('hidden');
                    activityText.textContent = '';
                }
            }

            if (waiterBell) {
                if (waiterUnreadCount > 0) {
                    waiterBell.classList.remove('hidden');
                    waiterBell.classList.add('flex');
                    if (waiterCount) {
                        waiterCount.textContent = waiterUnreadCount > 1 ? `x${waiterUnreadCount}` : '';
                    }
                    card.classList.add('waiter-call-active');
                } else {
                    waiterBell.classList.add('hidden');
                    waiterBell.classList.remove('flex');
                    if (waiterCount) {
                        waiterCount.textContent = '';
                    }
                    card.classList.remove('waiter-call-active');
                }
            }
        }

        function applyAllCardsActivityUi() {
            document.querySelectorAll('.table-card').forEach((card) => {
                applyCardActivityUi(card.dataset.tableNumber);
            });
        }

        window.markTableAsCallingWaiter = function(tableNum) {
            const normalizedTableNum = normalizeTableNum(tableNum);
            const card = document.querySelector(`.table-card[data-table-number="${normalizedTableNum}"]`);
            if (!card) return;

            const statusPill = card.querySelector('.table-status-pill');
            if (!statusPill) return;

            statusPill.className =
                'table-status-pill text-xs px-2 py-1 rounded-full bg-blue-500/20 text-blue-400';
            statusPill.textContent = 'Calling waiter';
        };

        function setKitchenStatusBadge(tableNum, className, label) {
            const normalizedTableNum = normalizeTableNum(tableNum);
            const card = document.querySelector(`.table-card[data-table-number="${normalizedTableNum}"]`);
            if (!card) return;

            const kitchenBadge = card.querySelector('.kitchen-status-badge');
            if (!kitchenBadge) return;

            kitchenBadge.className =
                `kitchen-status-badge text-[10px] px-2 py-1 rounded-full border font-semibold ${className}`;
            kitchenBadge.textContent = label;
            kitchenBadge.classList.remove('hidden');
        }

        function clearKitchenReadyState(tableNum) {
            const normalizedTableNum = normalizeTableNum(tableNum);
            const card = document.querySelector(`.table-card[data-table-number="${normalizedTableNum}"]`);
            if (!card) return;
            card.classList.remove('kitchen-ready-active');
        }

        window.markTableAsOccupied = function(tableNum) {
            const normalizedTableNum = normalizeTableNum(tableNum);
            const card = document.querySelector(`.table-card[data-table-number="${normalizedTableNum}"]`);
            const kitchenBadge = card?.querySelector('.kitchen-status-badge');
            if (kitchenBadge) {
                kitchenBadge.classList.add('hidden');
            }
            clearKitchenReadyState(tableNum);
        };

        window.markTableAsKitchenPreparing = function(tableNum) {
            setKitchenStatusBadge(tableNum, 'border-blue-500/50 bg-blue-500/20 text-blue-300', 'Preparing');
            clearKitchenReadyState(tableNum);
        };

        window.markTableAsKitchenReady = function(tableNum) {
            const normalizedTableNum = normalizeTableNum(tableNum);
            setKitchenStatusBadge(normalizedTableNum, 'border-green-500/50 bg-green-500/20 text-green-300',
                'Ready');

            const card = document.querySelector(`.table-card[data-table-number="${normalizedTableNum}"]`);
            if (!card) return;
            card.classList.add('kitchen-ready-active');
        };

        function markTableActivitySeen(tableNum) {
            const normalizedTableNum = normalizeTableNum(tableNum);
            if (!tableOrderActivity[normalizedTableNum]) return;

            tableOrderActivity[normalizedTableNum].unread_count = 0;
            saveTableOrderActivity();
            applyCardActivityUi(normalizedTableNum);
        }

        function markWaiterCallSeen(tableNum) {
            const normalizedTableNum = normalizeTableNum(tableNum);
            if (!tableWaiterCallActivity[normalizedTableNum]) return;

            tableWaiterCallActivity[normalizedTableNum].unread_count = 0;
            saveWaiterCallActivity();
            applyCardActivityUi(normalizedTableNum);
        }

        window.registerIncomingOrder = function(tableNum) {
            const normalizedTableNum = normalizeTableNum(tableNum);
            const existing = tableOrderActivity[normalizedTableNum] || {};
            tableOrderActivity[normalizedTableNum] = {
                unread_count: Number(existing.unread_count || 0) + 1,
                last_order_at: new Date().toISOString()
            };
            saveTableOrderActivity();
            applyCardActivityUi(normalizedTableNum);
        };

        window.registerWaiterCall = function(tableNum) {
            const normalizedTableNum = normalizeTableNum(tableNum);
            const existing = tableWaiterCallActivity[normalizedTableNum] || {};
            tableWaiterCallActivity[normalizedTableNum] = {
                unread_count: Number(existing.unread_count || 0) + 1,
                called_at: new Date().toISOString()
            };
            saveWaiterCallActivity();
            applyCardActivityUi(normalizedTableNum);
        };

        function renderLoadingState(tableNum) {
            listArea.innerHTML = `
                <div class="text-center p-10 text-gray-500">
                    Loading active orders for Table ${tableNum}...
                </div>`;
        }

        // 🔥 Global Refresh Function: Server se taaza data lane ke liye
        window.refreshFromServer = async function(tableNum) {
            const normalizedTableNum = String(tableNum);
            const requestId = ++activeRequestId;

            if (activeFetchController) {
                activeFetchController.abort();
            }

            activeFetchController = new AbortController();

            try {
                const response = await fetch(`/admin/get-table-orders/${normalizedTableNum}`, {
                    signal: activeFetchController.signal
                });

                if (!response.ok) {
                    throw new Error(`Failed to fetch orders: ${response.status}`);
                }

                const orders = await response.json();

                // Sirf latest request + currently open table ka result render hoga
                if (requestId !== activeRequestId || window.currentOpenTable !== normalizedTableNum) {
                    return;
                }

                // UI Render karo
                renderOrdersToDrawer(normalizedTableNum, orders);
            } catch (err) {
                if (err.name === 'AbortError') {
                    return;
                }

                if (requestId !== activeRequestId || window.currentOpenTable !== normalizedTableNum) {
                    return;
                }

                console.error("Order fetch error:", err);
                listArea.innerHTML =
                    `<div class="text-center p-10 text-red-400">Unable to load active orders</div>`;
            }
        };

        function renderOrdersToDrawer(tableNum, orders) {
            if (!orders || orders.length === 0) {
                listArea.innerHTML = `<div class="text-center p-10 text-gray-600">No active orders</div>`;
                return;
            }

            let html = '';
            orders.forEach(order => {
                order.items.forEach(item => {
                    html += `
                    <div class="flex justify-between items-center p-3 rounded-lg border border-l-2 border-l-orange-500 mb-2">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-200">
                                <span class="text-orange-500 font-bold">${item.quantity}x</span> ${item.item_name}
                            </span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[10px] text-gray-500 tracking-wider">
                                    Table ${tableNum} • ${order.status}
                                </span>
                                <span class="h-1.5 w-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                            </div>
                        </div>
                        <!-- 🔥 RIGHT SIDE ACTION -->
                        <!-- <span class="text-[14px] text-gray-500 font-bold">Rs. ${item.total}</span>-->
                            <div class="flex flex-col items-end gap-1">

                               <span class="text-[13px] text-gray-400 font-semibold">
                                   Rs. ${item.total}
                               </span>

                               ${
                                   item.status === 'ready'
                                   ? `
                                       <button
                                           class="serve-btn bg-green-600 hover:bg-green-700 text-white text-[11px] px-2.5 py-1 rounded-md"
                                           data-item-id="${item.id}">
                                           Serve
                                       </button>
                                   `
                                   : item.status === 'served'
                                   ? `
                                       <span class="text-green-400 text-[11px] font-semibold">
                                           ✔ Served
                                       </span>
                                   `
                                   : `
                                       <span class="text-gray-500 text-[10px] capitalize">
                                           ${item.status}
                                       </span>
                                   `
                               }

                           </div>
                    </div>`;
                });
            });
            listArea.innerHTML = html;
        }

        // Table Card Click
        document.querySelectorAll('.table-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (e.target.closest('button') || e.target.closest('img')) return;

                const tableNum = card.dataset.tableNumber;
                window.currentOpenTable = tableNum;
                window.currentOpenTableId = card.dataset.id || null;
                const drawerTitle = document.getElementById('drawerTitle');
                if (drawerTitle) {
                    drawerTitle.innerText = card.dataset.name;
                }
                renderLoadingState(tableNum);
                markTableActivitySeen(tableNum);
                markWaiterCallSeen(tableNum);

                // Har click par server se fresh data lao
                window.refreshFromServer(tableNum);

                drawer.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
            });
        });


        // document.addEventListener('click', async (e) => {
        //     const btn = e.target.closest('.serve-btn');
        //     console.log('Document clicked!');
        //     if (!btn) return;

        //     const itemId = btn.dataset.itemId;
        //     console.log('Serve Button Clicked! Item ID:', itemId);
        //     // UI instant feedback
        //     btn.disabled = true;
        //     btn.innerText = '...';

        //     try {
        //         const res = await fetch(`admin/order-items/${itemId}/serve`, {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
        //                     .content
        //             }
        //         });
        //         console.log('Button Clicked!', itemId);

        //         const data = await res.json();

        //         if (data.success) {
        //             btn.outerHTML =
        //                 `<span class="text-green-400 text-[11px] font-semibold">✔ Served</span>`;
        //         } else {
        //             btn.disabled = false;
        //             btn.innerText = 'Serve';
        //             alert(data.message || 'Error');
        //         }

        //     } catch (err) {
        //         console.error(err);
        //         btn.disabled = false;
        //         btn.innerText = 'Serve';
        //     }

        // });

        // Is pure block ko purane click listener se replace karo
        document.addEventListener('click', async function(e) {
            // 1. Ye log har click par aana chahiye, check karne ke liye ki JS active hai
            console.log("Element Clicked:", e.target);

            const btn = e.target.closest('.serve-btn');

            // Agar click '.serve-btn' par nahi hai, toh yahan se exit
            if (!btn) return;

            // 2. Agar button mil gaya toh ye log aayega
            const itemId = btn.dataset.itemId;
            console.log("Serve Triggered for ID:", itemId);

            e.preventDefault();
            e.stopPropagation(); // Doosre events ko rokne ke liye

            // UI Feedback
            btn.disabled = true;
            const originalHtml = btn.innerHTML;
            btn.innerText = '...';

            try {
                // CSRF Token Check
                const tokenTag = document.querySelector('meta[name="csrf-token"]');
                if (!tokenTag) {
                    console.error("CSRF Token missing in <head>!");
                    alert("Security token missing!");
                    return;
                }

                const res = await fetch(`/admin/order-items/${itemId}/serve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokenTag.getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                console.log("Response Data:", data);

                if (data.success) {
                    btn.outerHTML =
                        `<span class="text-green-400 text-[11px] font-semibold">✔ Served</span>`;
                } else {
                    alert(data.message || 'Error serving item');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (err) {
                console.error("Fetch Error:", err);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });

        // Close logic (Same as before)
        window.closeDrawer = () => {
            if (activeFetchController) {
                activeFetchController.abort();
                activeFetchController = null;
            }
            drawer.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            window.currentOpenTable = null;
            window.currentOpenTableId = null;
        };
        const closeDrawerBtn = document.getElementById('closeDrawer');
        if (closeDrawerBtn) {
            closeDrawerBtn.onclick = window.closeDrawer;
        }
        overlay.onclick = window.closeDrawer;

        loadTableOrderActivity();
        loadWaiterCallActivity();
        applyAllCardsActivityUi();
    })();
</script>
