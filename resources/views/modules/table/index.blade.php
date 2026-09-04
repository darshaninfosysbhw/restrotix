{{-- @extends(strtolower(auth()->user()->role ?? '') === 'waiter' ? 'core.layouts.waiter' : 'core.layouts.admin')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">

        <!-- 🔹 HEADER -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Table Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Table Directory</h1>
                    <p class="text-sm text-gray-400 mt-2">Manage restaurant tables & QR access</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 flex gap-4">
                    <button id="printAllQrBtn" type="button"
                        class="inline-flex justify-center items-center gap-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 border border-blue-500/30 px-4 py-2.5 rounded-lg text-sm">
                        <i class="fas fa-print"></i>
                        Print All QR
                    </button>

                    <button id="openTableModal" type="button"
                        class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium cursor-pointer">
                        <i class="fas fa-plus"></i>
                        Add Tables
                    </button>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            <!-- TOTAL -->
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total Tables</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats->total }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-orange-500/15 text-orange-500 flex items-center justify-center ">
                    <i class="fas fa-chair text-sm"></i>
                </div>
            </div>

            <!-- AVAILABLE -->
            <div class="bg-gray-800 bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Available</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats->available }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-green-500/15 flex items-center justify-center text-green-400">
                    <i class="fas fa-check-circle text-sm"></i>
                </div>
            </div>

            <!-- RESERVED -->
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Reserved</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats->reserved }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-yellow-500/15 flex items-center justify-center text-yellow-400">
                    <i class="fas fa-clock text-sm"></i>
                </div>
            </div>

            <!-- BOOKED -->
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Booked</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats->occupied }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-red-500/15 flex items-center justify-center text-red-400">
                    <i class="fas fa-times-circle text-sm"></i>
                </div>
            </div>

        </div>

        <!-- 🔹 TABLE CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5 ">
            @forelse ($tables as $table)
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 card-hover table-card cursor-pointer"
                    data-id="{{ $table['id'] }}" data-name="{{ $table['display_name'] }}"
                    data-table-number="{{ $table['table_number'] }}" data-qr-token="{{ $table['qr_token'] ?? '' }}"
                    data-orders='@json($table['active_orders'] ?? [])'>

                    <div class="flex justify-between items-center mb-1">
                        <h3 class="text-white font-semibold">
                            {{ $table['display_name'] }}

                        </h3>
                        <div class="flex items-center gap-2">
                            <span
                                class="waiter-call-bell hidden items-center gap-1 text-[10px] px-2 py-1 rounded-full border border-blue-500/60 bg-blue-500/20 text-blue-300 font-semibold">
                                <i class="fas fa-bell animate-bounce"></i>
                                <span class="waiter-call-count"></span>
                            </span>
                            <span
                                class="bill-request-bell hidden items-center gap-1 text-[10px] px-2 py-1 rounded-full border border-orange-500/60 bg-orange-500/20 text-orange-300 font-semibold">
                                <i class="fas fa-file-invoice-dollar animate-pulse"></i>
                                <span class="bill-request-count"></span>
                            </span>
                            <span
                                class="table-status-pill text-xs px-2 py-1 rounded-full
                                bg-{{ $table['status_color'] }}-500/20 text-{{ $table['status_color'] }}-400">
                                {{ $table['status_label'] }}
                            </span>
                            <span
                                class="new-order-badge hidden text-[10px] px-2 py-1 rounded-full border border-orange-400 bg-orange-100 text-orange-700 dark:border-orange-500/60 dark:bg-orange-500/20 dark:text-orange-300 font-semibold">
                                New
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mb-3">
                        Token: {{ $table['qr_token'] ?: 'N/A' }}
                    </p>
                    <p class="last-order-activity text-[11px] text-orange-700 dark:text-orange-300/80 mb-3 hidden"></p>

                    <hr class="mb-3 text-gray-600">

                    <div class="flex items-center justify-between">

                        <img class="qrPreview cursor-pointer border border-gray-600 rounded-lg p-2 card-hover"
                            src="{{ $table['qr_code_inline'] }}" data-name="Table {{ $table['table_number'] }}"
                            data-qr="{{ $table['qr_code_inline'] }}" />

                        <div class="flex gap-2">

                            <button class="viewQrBtn text-xs px-2 py-1 border border-gray-600 rounded-lg text-gray-300"
                                data-name="Table {{ $table['table_number'] }}" data-qr="{{ $table['qr_code_inline'] }}">
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

                        <button type="button" onclick="document.getElementById('openTableModal').click()"
                            class="mt-6 inline-flex items-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-400 border border-orange-500/30 px-5 py-2.5 rounded-lg text-sm font-medium">
                            <i class="fas fa-plus"></i>
                            Add Your First Table
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>


    <!-- Add Table MODAL -->
    <div id="tableModal" class="fixed inset-0 z-[120] hidden overflow-y-auto">
        <div id="tableModalBackdrop" class="absolute inset-0 bg-black/50"></div>

        <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-xl bg-gray-800 border border-gray-700 rounded-xl">

                <!-- HEADER -->
                <div class="px-5 py-4 border-b border-gray-700 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-white" id="modalTitle">Add Tables</h2>
                        <p class="text-xs text-gray-400">Bulk create tables with QR</p>
                    </div>
                    <button id="closeTableModal" class="text-gray-400 cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- FORM -->
                <form method="POST" action="{{ route('admin.tables.bulk-store') }}" class="p-4 sm:p-5 space-y-5"
                    id="tableForm">
                    @csrf
                    <input type="hidden" id="formMethodField" name="_method" value="POST">
                    <!-- 🔹 BRANCH -->
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
                            <option value="out_of_service">Out Of Service</option>
                        </select>
                    </div>

                    <!-- 🔹 BUTTONS -->
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


    @include('core.components.table.table-drawer')

    <!-- QR Modal -->
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

        .request-bill-active {
            animation: waiterCardNudge 1.2s ease-in-out infinite;
            box-shadow: 0 0 0 1px rgba(249, 115, 22, 0.45), 0 0 18px rgba(249, 115, 22, 0.2);
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
            backdrop.addEventListener('click', closeModal);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeModal();
            });

        })();
    </script>

    <!-- this script used for view menu card and download -->
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

            printAllQrBtn.addEventListener('click', () => {
                const cards = Array.from(viewButtons).map((btn) => {
                    return buildQrCardHtml(btn.dataset.name, btn.dataset.qr);
                });

                if (!cards.length) return;
                runFastPrint(cards.join(''), 'All Table QR Codes');
            });

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
            const BILL_ALERT_STORAGE_KEY = 'table_bill_request_activity_v1';
            const EMPTY_ORDERS_ICON_HTML = @json(trim(view('core.components.table.partials.empty-orders-icon')->render()));
            window.currentOpenTable = null;
            let activeFetchController = null;
            let activeRequestId = 0;
            let tableOrderActivity = {};
            let tableWaiterCallActivity = {};
            let tableBillRequestActivity = {};

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

            function loadBillRequestActivity() {
                try {
                    tableBillRequestActivity = JSON.parse(localStorage.getItem(BILL_ALERT_STORAGE_KEY) || '{}');
                } catch (e) {
                    tableBillRequestActivity = {};
                }
            }

            function saveBillRequestActivity() {
                localStorage.setItem(BILL_ALERT_STORAGE_KEY, JSON.stringify(tableBillRequestActivity));
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
                const billRequestActivity = tableBillRequestActivity[normalizedTableNum] || {};
                const badge = card.querySelector('.new-order-badge');
                const activityText = card.querySelector('.last-order-activity');
                const waiterBell = card.querySelector('.waiter-call-bell');
                const waiterCount = card.querySelector('.waiter-call-count');
                const billBell = card.querySelector('.bill-request-bell');
                const billCount = card.querySelector('.bill-request-count');
                const unreadCount = Number(activity.unread_count || 0);
                const waiterUnreadCount = Number(waiterActivity.unread_count || 0);
                const billUnreadCount = Number(billRequestActivity.unread_count || 0);

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

                if (billBell) {
                    if (billUnreadCount > 0) {
                        billBell.classList.remove('hidden');
                        billBell.classList.add('flex');
                        if (billCount) {
                            billCount.textContent = billUnreadCount > 1 ? `x${billUnreadCount}` : '';
                        }
                        card.classList.add('request-bill-active');
                    } else {
                        billBell.classList.add('hidden');
                        billBell.classList.remove('flex');
                        if (billCount) {
                            billCount.textContent = '';
                        }
                        card.classList.remove('request-bill-active');
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

            window.markTableAsAvailable = function(tableNum) {
                const normalizedTableNum = normalizeTableNum(tableNum);
                const card = document.querySelector(`.table-card[data-table-number="${normalizedTableNum}"]`);
                if (!card) return;

                const statusPill = card.querySelector('.table-status-pill');
                if (!statusPill) return;

                card.dataset.status = 'available';
                card.classList.remove('request-bill-active', 'waiter-call-active', 'kitchen-ready-active');
                const kitchenBadge = card.querySelector('.kitchen-status-badge');
                if (kitchenBadge) {
                    kitchenBadge.classList.add('hidden');
                    kitchenBadge.textContent = 'Kitchen';
                }

                statusPill.className =
                    'table-status-pill text-xs px-2 py-1 rounded-full bg-emerald-500/20 text-emerald-400';
                statusPill.textContent = 'Available';
            };

            window.markTableAsRequestBill = function(tableNum) {
                const normalizedTableNum = normalizeTableNum(tableNum);
                const card = document.querySelector(`.table-card[data-table-number="${normalizedTableNum}"]`);
                if (!card) return;

                const statusPill = card.querySelector('.table-status-pill');
                if (!statusPill) return;

                statusPill.className =
                    'table-status-pill text-xs px-2 py-1 rounded-full bg-orange-500/20 text-orange-400';
                statusPill.textContent = 'Request bill';
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

            function markBillRequestSeen(tableNum) {
                const normalizedTableNum = normalizeTableNum(tableNum);
                if (!tableBillRequestActivity[normalizedTableNum]) return;

                tableBillRequestActivity[normalizedTableNum].unread_count = 0;
                saveBillRequestActivity();
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

            window.registerBillRequest = function(tableNum) {
                const normalizedTableNum = normalizeTableNum(tableNum);
                const existing = tableBillRequestActivity[normalizedTableNum] || {};
                tableBillRequestActivity[normalizedTableNum] = {
                    unread_count: Number(existing.unread_count || 0) + 1,
                    requested_at: new Date().toISOString()
                };
                saveBillRequestActivity();
                applyCardActivityUi(normalizedTableNum);
            };

            function renderLoadingState(tableNum) {
                listArea.innerHTML = `
                    <div class="text-center p-10 text-gray-500">
                        Loading active orders for Table ${tableNum}...
                    </div>`;
            }

            function renderEmptyOrdersState() {
                return `
                    <div class="flex min-h-[240px] items-center justify-center px-4 py-10 text-center">
                        <div>
                            <div class="mx-auto flex items-center justify-center">
                                ${EMPTY_ORDERS_ICON_HTML}
                            </div>
                            <h3 class="mt-4 text-base font-semibold text-gray-700 dark:text-gray-100">No active orders</h3>
                            <p class="mt-1 text-sm text-gray-500">Fresh orders will appear here when the table starts cooking.</p>
                        </div>
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
                    listArea.innerHTML = `
                        <div class="flex min-h-[240px] items-center justify-center px-4 py-10 text-center">
                            <div>
                                <i class="fas fa-triangle-exclamation text-2xl text-red-400"></i>
                                <div class="mt-3 text-sm text-red-400">Unable to load active orders</div>
                            </div>
                        </div>`;
                }
            };

            function renderOrdersToDrawer(tableNum, orders) {
                if (!orders || orders.length === 0) {
                    if (typeof window.markTableAsAvailable === 'function') {
                        window.markTableAsAvailable(tableNum);
                    }
                    listArea.innerHTML = renderEmptyOrdersState();
                    return;
                }

                let html = '';
                orders.forEach(order => {
                    order.items.forEach(item => {
                        const addons = item.order_item_addons || item.orderItemAddons || [];
                        const addonsHtml = addons.length
                            ? `<div class="mt-1.5 text-[11px] text-orange-400/90 leading-5 pl-1">` +
                          addons.map(addon => {
                              const qty = Number(addon.quantity || 1);
                              const qtyText = qty > 1 ? ` x${qty}` : '';
                              const parsePrice = (value) => {
                                  const cleaned = String(value ?? '').replace(/,/g, '').trim();
                                  const number = Number(cleaned);
                                  return Number.isFinite(number) ? number : 0;
                              };
                              const storedPrice = parsePrice(addon.price);
                              const priceValue = storedPrice > 0
                                  ? storedPrice
                                  : parsePrice(addon.masterAddon?.price ?? addon.menu_item_addon_price ?? 0);
                              const price = priceValue.toFixed(2);
                              const name = addon.addon_name ?? addon.name ?? addon.masterAddon?.name ?? 'Addon';
                              return `<div class="flex items-center justify-between gap-3"><span>${name}${qtyText}</span><span class="text-orange-300">₹${price}</span></div>`;
                          }).join('') +
                              `</div>`
                            : '';

                        html += `
                        <div class="flex justify-between items-center p-3 rounded-xl border border-gray-800 mb-2">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-200">
                                    <span class="text-orange-500 font-bold">${item.quantity}x</span> ${item.item_name}
                                </span>
                                ${addonsHtml}
                                <span class="text-[10px] text-gray-500 mt-0.5">Table ${tableNum} • ${order.status}</span>
                            </div>
                            <div class="h-2 w-2 rounded-full bg-orange-500 animate-pulse"></div>
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
                    window.currentOpenTableQrToken = card.dataset.qrToken || null;
                    document.getElementById('drawerTitle').innerText = card.dataset.name;
                    renderLoadingState(tableNum);
                    markTableActivitySeen(tableNum);
                    markWaiterCallSeen(tableNum);
                    markBillRequestSeen(tableNum);

                    // Har click par server se fresh data lao
                    window.refreshFromServer(tableNum);

                    drawer.classList.remove('translate-x-full');
                    overlay.classList.remove('hidden');
                });
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
            };
            document.getElementById('closeDrawer').onclick = window.closeDrawer;
            overlay.onclick = window.closeDrawer;

            loadTableOrderActivity();
            loadWaiterCallActivity();
            loadBillRequestActivity();
            applyAllCardsActivityUi();
        })();
    </script>
@endsection --}}
