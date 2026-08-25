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
                            <option value="request_bill">Request Bill</option>
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
            class="bg-transparent rounded-3xl shadow-none p-0 w-full max-w-[560px] text-center relative">

            <!-- ❌ CLOSE BUTTON -->
            <button id="qrCloseBtn"
                class="absolute -top-3 -right-3 z-20 h-10 w-10 rounded-full bg-white text-gray-500 shadow-lg border border-gray-200 hover:text-black text-2xl leading-none cursor-pointer">
                &times;
            </button>

            <div
                class="rounded-[30px] bg-white/10 backdrop-blur-sm p-0 overflow-hidden border border-white/20 shadow-[0_35px_90px_rgba(0,0,0,0.35)]">
                <img id="qrPosterImage" src="" alt="Table QR Poster"
                    class="block w-full h-auto max-h-[78vh] object-contain bg-white">
            </div>

            <!-- BUTTONS -->
            <div class="flex flex-col sm:flex-row justify-center gap-3 mt-5 px-1">
                <a id="downloadBtn" href="#" download
                    class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-md">
                    Download PNG
                </a>

                <button id="printSingleBtn" type="button"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold shadow-md">
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

    #printSheet {
        position: fixed;
        inset: 0;
        z-index: -1;
        background: #fff;
    }

    #printSheet .poster-page {
        width: 100%;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        page-break-after: always;
        break-after: page;
        box-sizing: border-box;
    }

    #printSheet .poster-page:last-child {
        page-break-after: auto;
        break-after: auto;
    }

    #printSheet .poster-sheet {
        width: 100%;
        height: calc(297mm - 16mm);
        min-height: calc(297mm - 16mm);
        display: block;
        padding: 0;
        page-break-after: always;
        break-after: page;
        box-sizing: border-box;
    }

    #printSheet .poster-sheet:last-child {
        page-break-after: auto;
        break-after: auto;
    }

    #printSheet .poster-grid {
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        grid-template-rows: repeat(2, minmax(0, 1fr));
        row-gap: 3mm;
        column-gap: 0mm;
        align-content: stretch;
        justify-content: stretch;
        box-sizing: border-box;
    }

    #printSheet .poster-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        break-inside: avoid;
        page-break-inside: avoid;
        padding: 0;
        height: 100%;
        min-height: 0;
    }

    #printSheet .poster-cell .poster-image {
        width: 100%;
        max-width: 100%;
        max-height: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    #printSheet .poster-image {
        width: 100%;
        max-width: 190mm;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 4mm;
        }

        body.print-mode > *:not(#printSheet) {
            display: none !important;
        }

        body.print-mode #printSheet,
        body.print-mode #printSheet * {
            visibility: visible !important;
        }

        body.print-mode #printSheet {
            display: block !important;
            position: relative !important;
            inset: auto !important;
            z-index: 1 !important;
            width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            padding: 0 !important;
            background: #fff;
        }

        body.print-mode #printSheet,
        body.print-mode #printSheet * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body.print-mode #printSheet .poster-sheet {
            width: 100% !important;
            height: calc(297mm - 8mm) !important;
            min-height: calc(297mm - 8mm) !important;
        }

        body.print-mode #printSheet .poster-grid {
            gap: 4mm !important;
        }

        body.print-mode #printSheet .poster-cell {
            overflow: hidden !important;
        }

        body.print-mode #printSheet .poster-cell .poster-image {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain !important;
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
        const qrPosterImage = document.getElementById('qrPosterImage');
        const downloadBtn = document.getElementById('downloadBtn');
        const printSingleBtn = document.getElementById('printSingleBtn');
        const printSheet = document.getElementById('printSheet');

        const viewButtons = document.querySelectorAll('.viewQrBtn');
        const qrImages = document.querySelectorAll('.qrPreview');
        let activeQr = null;
        let activePosterDataUrl = '';
        let activePosterKey = '';
        let posterRequestToken = 0;
        const posterTemplateUrl = @json(asset('images/RestoTix.png'));

        if (!qrModal || !qrBox || !qrCloseBtn || !qrPosterImage || !downloadBtn || !printSingleBtn ||
            !printSheet) {
            return;
        }

        function downloadDataUrl(fileName, dataUrl) {
            const tempLink = document.createElement('a');
            tempLink.href = dataUrl;
            tempLink.download = fileName;
            document.body.appendChild(tempLink);
            tempLink.click();
            tempLink.remove();
        }

        function normalizeTableNumber(data) {
            const rawValue = String(data?.tableNumber ?? data?.table_number ?? data?.name ?? '').trim();
            if (!rawValue) {
                return 'T-01';
            }

            return rawValue.replace(/^Table\s*/i, '').trim() || rawValue;
        }

        function normalizePosterFileName(data) {
            const tableNumber = normalizeTableNumber(data);
            const safeSlug = tableNumber.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '') ||
                'table-qr';
            return `table-${safeSlug}-poster.png`;
        }

        function loadImageElement(src) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => resolve(img);
                img.onerror = () => reject(new Error(`Unable to load image: ${src}`));
                img.src = src;
            });
        }

        function roundRectPath(ctx, x, y, width, height, radius) {
            const r = Math.max(0, Math.min(radius, Math.min(width, height) / 2));
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.arcTo(x + width, y, x + width, y + height, r);
            ctx.arcTo(x + width, y + height, x, y + height, r);
            ctx.arcTo(x, y + height, x, y, r);
            ctx.arcTo(x, y, x + width, y, r);
            ctx.closePath();
        }

        function drawTextFit(ctx, text, centerX, centerY, maxWidth, fontFamily, baseSize, color = '#fff') {
            let size = baseSize;
            while (size > 18) {
                ctx.font = `700 ${size}px ${fontFamily}`;
                if (ctx.measureText(text).width <= maxWidth) {
                    break;
                }
                size -= 2;
            }

            ctx.fillStyle = color;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(text, centerX, centerY);
        }

        async function buildPosterDataUrl(data) {
            const [templateImg, qrImg] = await Promise.all([
                loadImageElement(posterTemplateUrl),
                loadImageElement(data?.qr || '')
            ]);

            const canvas = document.createElement('canvas');
            canvas.width = templateImg.naturalWidth || templateImg.width || 1054;
            canvas.height = templateImg.naturalHeight || templateImg.height || 1492;

            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(templateImg, 0, 0, canvas.width, canvas.height);

            // Table badge - replace the template placeholder with live table number.
            const badgeX = 389;
            const badgeY = 443;
            const badgeW = 277;
            const badgeH = 101;
            ctx.save();
            ctx.shadowColor = 'rgba(0, 0, 0, 0.22)';
            ctx.shadowBlur = 14;
            ctx.shadowOffsetY = 8;
            ctx.fillStyle = '#5f0710';
            ctx.strokeStyle = '#d8aa4d';
            ctx.lineWidth = 5;
            roundRectPath(ctx, badgeX, badgeY, badgeW, badgeH, 16);
            ctx.fill();
            ctx.stroke();
            ctx.restore();

            drawTextFit(
                ctx,
                normalizeTableNumber(data),
                badgeX + badgeW / 2,
                badgeY + badgeH / 2 + 1,
                220,
                "Georgia, 'Times New Roman', serif",
                64,
                '#fff8f2'
            );

            // QR panel - keep template frame, fill inner area and place QR inside.
            const panelX = 295;
            const panelY = 555;
            const panelW = 464;
            const panelH = 444;
            ctx.save();
            ctx.fillStyle = '#fffaf2';
            roundRectPath(ctx, panelX, panelY, panelW, panelH, 28);
            ctx.fill();
            ctx.restore();

            const qrSize = 350;
            const qrX = panelX + Math.round((panelW - qrSize) / 2);
            const qrY = panelY + Math.round((panelH - qrSize) / 2);
            ctx.drawImage(qrImg, qrX, qrY, qrSize, qrSize);

            return canvas.toDataURL('image/png');
        }

        function buildPrintSheetHtml(posterDataUrls) {
            const cells = posterDataUrls.map((posterDataUrl, index) => `
                <div class="poster-cell">
                    <img class="poster-image" src="${posterDataUrl}" alt="Table QR Poster ${index + 1}" />
                </div>
            `).join('');

            return `
                <div class="poster-sheet">
                    <div class="poster-grid">
                        ${cells}
                    </div>
                </div>
            `;
        }

        function chunkArray(items, size) {
            const chunks = [];

            for (let index = 0; index < items.length; index += size) {
                chunks.push(items.slice(index, index + size));
            }

            return chunks;
        }

        function buildPrintDocumentHtml(posterPagesHtml) {
            return `
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Table QR Posters</title>
                    <style>
                        @page {
                            size: A4 portrait;
                            margin: 4mm;
                        }

                        html,
                        body {
                            margin: 0;
                            padding: 0;
                            width: 100%;
                            background: #fff;
                            font-family: Arial, sans-serif;
                        }

                        * {
                            box-sizing: border-box;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }

                        .poster-sheet {
                            width: 100%;
                            height: calc(297mm - 8mm);
                            break-after: page;
                            page-break-after: always;
                        }

                        .poster-sheet:last-child {
                            break-after: auto;
                            page-break-after: auto;
                        }

                        .poster-grid {
                            width: 100%;
                            height: 100%;
                            display: grid;
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                            grid-template-rows: repeat(2, minmax(0, 1fr));
                            row-gap: 3mm;
                            column-gap: 0mm;
                        }

                        .poster-cell {
                            width: 100%;
                            height: 100%;
                            overflow: hidden;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            break-inside: avoid;
                            page-break-inside: avoid;
                        }

                        .poster-cell-empty {
                            opacity: 0;
                        }

                        .poster-image {
                            width: 100%;
                            height: 100%;
                            object-fit: contain;
                            display: block;
                        }
                    </style>
                </head>
                <body>
                    ${posterPagesHtml.join('')}
                </body>
                </html>
            `;
        }

        function printInIframe(posterPagesHtml) {
            const frame = document.createElement('iframe');
            frame.setAttribute('aria-hidden', 'true');
            frame.style.position = 'fixed';
            frame.style.right = '0';
            frame.style.bottom = '0';
            frame.style.width = '0';
            frame.style.height = '0';
            frame.style.border = '0';
            frame.style.opacity = '0';
            frame.style.pointerEvents = 'none';

            const cleanup = () => {
                if (frame.parentNode) {
                    frame.parentNode.removeChild(frame);
                }
            };

            frame.addEventListener('load', () => {
                const frameDoc = frame.contentDocument;
                const frameWin = frame.contentWindow;
                if (!frameDoc || !frameWin) {
                    cleanup();
                    return;
                }

                waitForImages(frameDoc).then(() => {
                    setTimeout(() => {
                        try {
                            frameWin.focus();
                            frameWin.print();
                        } catch (error) {
                            cleanup();
                        }
                    }, 150);
                });

                frameWin.addEventListener('afterprint', cleanup, { once: true });
            }, { once: true });

            document.body.appendChild(frame);
            frame.srcdoc = buildPrintDocumentHtml(posterPagesHtml);
        }

        function waitForImages(root) {
            const images = Array.from(root?.querySelectorAll('img') || []);
            if (!images.length) {
                return Promise.resolve();
            }

            return Promise.all(images.map((img) => {
                if (img.complete && img.naturalWidth > 0) {
                    return Promise.resolve();
                }

                return new Promise((resolve) => {
                    const done = () => resolve();
                    img.addEventListener('load', done, { once: true });
                    img.addEventListener('error', done, { once: true });
                });
            })).then(() => undefined);
        }

        function runFastPrint(posterPagesHtml) {
            closeQR();
            printInIframe(posterPagesHtml);
        }

        async function ensurePosterDataUrl(data) {
            const cacheKey = `${data?.name ?? ''}::${data?.qr ?? ''}::${data?.tableNumber ?? data?.table_number ?? ''}`;

            if (activePosterDataUrl && activePosterKey === cacheKey) {
                return activePosterDataUrl;
            }

            const requestToken = ++posterRequestToken;

            try {
                const posterDataUrl = await buildPosterDataUrl(data);

                if (requestToken === posterRequestToken) {
                    activePosterDataUrl = posterDataUrl;
                    activePosterKey = cacheKey;
                    qrPosterImage.src = posterDataUrl;
                }

                return posterDataUrl;
            } catch (error) {
                if (requestToken === posterRequestToken) {
                    activePosterDataUrl = data?.qr || '';
                    activePosterKey = cacheKey;
                    qrPosterImage.src = data?.qr || '';
                }

                return data?.qr || '';
            }
        }

        function openQR(data) {
            activeQr = {
                name: data?.name || `Table ${normalizeTableNumber(data)}`,
                qr: data?.qr || '',
                tableNumber: data?.tableNumber || data?.table_number || normalizeTableNumber(data),
            };
            qrPosterImage.src = '';
            qrModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            ensurePosterDataUrl(activeQr);
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
                    qr: btn.dataset.qr,
                    tableNumber: btn.dataset.tableNumber
                });
            });
        });

        // 👉 QR image click
        qrImages.forEach(img => {
            img.addEventListener('click', () => {
                openQR({
                    name: img.dataset.name,
                    qr: img.dataset.qr,
                    tableNumber: img.dataset.tableNumber
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
            const posterDataUrl = await ensurePosterDataUrl(activeQr);
            downloadDataUrl(normalizePosterFileName(activeQr), posterDataUrl);
        });

        printSingleBtn.addEventListener('click', async () => {
            if (!activeQr) return;
            const posterDataUrl = await ensurePosterDataUrl(activeQr);
            runFastPrint([buildPrintSheetHtml([posterDataUrl])]);
        });

        if (printAllQrBtn) {
            printAllQrBtn.addEventListener('click', async () => {
                const posterDataUrls = [];

                for (const btn of viewButtons) {
                    const data = {
                        name: btn.dataset.name,
                        qr: btn.dataset.qr,
                        tableNumber: btn.dataset.tableNumber
                    };
                    const posterDataUrl = await ensurePosterDataUrl({
                        name: data.name,
                        qr: data.qr,
                        tableNumber: data.tableNumber
                    });
                    posterDataUrls.push(posterDataUrl);
                }

                if (!posterDataUrls.length) return;

                const sheets = chunkArray(posterDataUrls, 4).map(buildPrintSheetHtml);
                runFastPrint(sheets);
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
        const drawerSubtitle = document.getElementById('drawerSubtitle');
        const drawerOrdersTabBtn = document.getElementById('drawerOrdersTabBtn');
        const drawerKotTabBtn = document.getElementById('drawerKotTabBtn');
        const drawerPrintKotBtn = document.getElementById('drawerPrintKotBtn');
        const drawerKotSelectorWrap = document.getElementById('drawerKotSelectorWrap');
        const drawerKotSelector = document.getElementById('drawerKotSelector');
        const drawerPrintKotLabel = document.getElementById('drawerPrintKotLabel');
        const currentTenantName = @json(optional(auth()->user()->tenant)->company_name ?? 'FOOD PANDA');
        const currentBranchName = @json(optional(auth()->user()->branch)->branch_name ?? 'HOT KITCHEN');
        const EMPTY_ORDERS_ICON_HTML = @json(trim(view('core.components.table.partials.empty-orders-icon')->render()));
        const ALERT_STORAGE_KEY = 'table_order_activity_v1';
        const WAITER_ALERT_STORAGE_KEY = 'table_waiter_call_activity_v1';
        window.currentOpenTable = null;
        window.currentOpenTableOrders = [];
        window.currentOpenTableDrawerView = 'orders';
        window.currentOpenTableBranchId = null;
        window.currentOpenTableKotNumbers = [];
        window.currentOpenTableSelectedKotNumber = null;
        window.currentOpenTableKotSelectionManual = false;
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

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            } [char]));
        }

        function setDrawerSubtitle(text, status = '') {
            if (drawerSubtitle) {
                const safeText = escapeHtml(text);
                const safeStatus = escapeHtml(status);

                if (safeStatus) {
                    drawerSubtitle.innerHTML = `
                        <span>${safeText}</span>
                        <span class="mx-1 h-4 w-px shrink-0 bg-gray-400/50"></span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            <span>${safeStatus}</span>
                        </span>
                    `;
                } else {
                    drawerSubtitle.textContent = text;
                }
            }
        }

        function getOrderItemCount(orders) {
            if (!Array.isArray(orders) || orders.length === 0) {
                return 0;
            }

            return orders.reduce((total, order) => {
                const items = Array.isArray(order?.items) ? order.items : [];

                return total + items.reduce((itemTotal, item) => {
                    const quantity = Number(item?.quantity ?? item?.qty ?? 1);
                    return itemTotal + (Number.isFinite(quantity) && quantity > 0 ? quantity : 1);
                }, 0);
            }, 0);
        }

        function formatOrderItemCount(count) {
            const safeCount = Number.isFinite(Number(count)) ? Number(count) : 0;
            return `${safeCount} item${safeCount === 1 ? '' : 's'} ordered`;
        }

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        }

        function closeItemActionMenus(exceptMenu = null) {
            document.querySelectorAll('[data-item-action-menu]').forEach((menu) => {
                if (menu !== exceptMenu) {
                    menu.classList.add('hidden');
                }
            });
        }

        async function refreshCurrentTableDrawer() {
            if (window.currentOpenTable && typeof window.refreshFromServer === 'function') {
                await window.refreshFromServer(
                    window.currentOpenTable,
                    window.currentOpenTableBranchId
                );
            }
        }

        async function postDrawerItemAction(url, payload = {}) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify(payload),
            });

            let data = {};
            try {
                data = await response.json();
            } catch (error) {
                data = {};
            }

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to process item action');
            }

            return data;
        }

        async function serveDrawerItem(itemId) {
            try {
                await postDrawerItemAction(`/admin/order-items/${itemId}/serve`);
                await refreshCurrentTableDrawer();
            } catch (error) {
                alert(error.message || 'Unable to mark item as served');
            }
        }

        async function cancelDrawerItem(itemId) {
            const reason = prompt('Enter cancellation reason:');
            if (reason === null) return;
            if (reason.trim() === '') {
                alert('Reason required!');
                return;
            }

            try {
                await postDrawerItemAction(`/admin/order-items/${itemId}/cancel`, {
                    reason: reason.trim(),
                });
                await refreshCurrentTableDrawer();
            } catch (error) {
                alert(error.message || 'Unable to cancel item');
            }
        }

        function getPrimaryOrderStatus(orders) {
            if (!Array.isArray(orders) || orders.length === 0) {
                return '';
            }

            return orders
                .map((order) => String(order?.status ?? '').trim())
                .find(Boolean) || '';
        }

        function getKotGroupsFromOrders(orders) {
            const groupMap = new Map();
            const resolveOrderByLabel = (item, order) => {
                const rawLabel = String(
                    item?.order_by_label ??
                    order?.order_by_label ??
                    order?.creator?.name ??
                    order?.creator_name ??
                    order?.created_by_name ??
                    ''
                ).trim();

                return rawLabel || 'Guest';
            };
            const resolveOrderType = (order) => String(order?.order_type ?? 'dine_in').trim() || 'dine_in';
            const resolveOrderAt = (item, order) => item?.created_at || item?.createdAt || order?.ordered_at_iso || order?.ordered_at || order?.created_at || '';

            (Array.isArray(orders) ? orders : []).forEach((order, orderIndex) => {
                (Array.isArray(order?.items) ? order.items : []).forEach((item, itemIndex) => {
                    const kotNumber = Number(item?.kot_number ?? 0);
                    if (!Number.isFinite(kotNumber) || kotNumber <= 0) {
                        return;
                    }

                    const quantity = Number(item?.quantity ?? item?.qty ?? 1);
                    const safeQuantity = Number.isFinite(quantity) && quantity > 0 ? quantity : 1;
                    const existing = groupMap.get(kotNumber) || {
                        kotNumber,
                        items: [],
                        itemCount: 0,
                        qtyCount: 0,
                        printCount: 0,
                        lastPrintedAt: '',
                        firstSeenIndex: orderIndex * 1000 + itemIndex,
                        orderId: Number(order?.id ?? 0) || null,
                        orderNumber: String(order?.order_number ?? '').trim(),
                        orderType: resolveOrderType(order),
                        orderByLabel: resolveOrderByLabel(item, order),
                        orderAt: resolveOrderAt(item, order),
                    };

                    if (!existing.orderByLabel) {
                        existing.orderByLabel = resolveOrderByLabel(item, order);
                    }
                    if (!existing.orderType) {
                        existing.orderType = resolveOrderType(order);
                    }
                    if (!existing.orderAt) {
                        existing.orderAt = resolveOrderAt(item, order);
                    }

                    const itemPrintCount = Number(item?.kot_print_count ?? 0);
                    const itemLastPrintedAt = String(item?.kot_last_printed_at ?? '').trim();
                    existing.items.push({
                        ...item,
                        quantity: safeQuantity,
                    });
                    existing.itemCount += 1;
                    existing.qtyCount += safeQuantity;
                    existing.printCount = Math.max(existing.printCount, Number.isFinite(itemPrintCount) ? itemPrintCount : 0);
                    if (itemLastPrintedAt && (!existing.lastPrintedAt || itemLastPrintedAt > existing.lastPrintedAt)) {
                        existing.lastPrintedAt = itemLastPrintedAt;
                    }
                    groupMap.set(kotNumber, existing);
                });
            });

            return Array.from(groupMap.values())
                .sort((a, b) => a.kotNumber - b.kotNumber)
                .map((group) => ({
                    ...group,
                    items: group.items.sort((a, b) => {
                        const aTime = String(a?.created_at ?? a?.createdAt ?? '');
                        const bTime = String(b?.created_at ?? b?.createdAt ?? '');
                        if (aTime && bTime && aTime !== bTime) {
                            return aTime.localeCompare(bTime);
                        }

                        return Number(a?.id ?? 0) - Number(b?.id ?? 0);
                    }),
                }));
        }

        function getItemStatusMeta(status) {
            const rawStatus = String(status ?? '').trim();
            const normalizedStatus = rawStatus.toLowerCase();

            switch (normalizedStatus) {
                case 'ready':
                    return {
                        label: 'Ready',
                            icon: 'fa-circle-check',
                            tone: 'text-green-500',
                    };
                case 'new':
                    return {
                        label: 'New',
                            icon: 'fa-circle-plus',
                            tone: 'text-orange-500',
                    };
                case 'preparing':
                    return {
                        label: 'Preparing',
                            icon: 'fa-utensils',
                            tone: 'text-blue-500',
                    };
                case 'running':
                    return {
                        label: 'Running',
                            icon: 'fa-person-running',
                            tone: 'text-green-500',
                    };
                case 'served':
                    return {
                        label: 'Served',
                            icon: 'fa-circle-check',
                            tone: 'text-emerald-500',
                    };
                default:
                    return {
                        label: rawStatus ?
                            rawStatus.charAt(0).toUpperCase() + rawStatus.slice(1) :
                            'New',
                            icon: 'fa-circle-dot',
                            tone: 'text-gray-500',
                    };
            }
        }

        function isItemActionLocked(status) {
            const normalizedStatus = String(status ?? '').trim().toLowerCase();
            return ['served', 'rejected', 'cancelled'].includes(normalizedStatus);
        }

        function normalizeRelativeDateInput(value) {
            if (value === null || value === undefined || value === '') {
                return '';
            }

            const raw = String(value).trim();
            if (!raw) return '';

            let normalized = raw.replace(' ', 'T');
            normalized = normalized.replace(/(\.\d{3})\d+(?=Z$)/, '$1');
            normalized = normalized.replace(/(\.\d{3})\d+$/, '$1');

            return normalized;
        }

        function formatRelativeTime(value) {
            const normalized = normalizeRelativeDateInput(value);
            if (!normalized) return '';

            const date = new Date(normalized);
            if (Number.isNaN(date.getTime())) return '';

            const diffMs = Math.max(Date.now() - date.getTime(), 0);
            const diffMinutes = Math.floor(diffMs / 60000);

            if (diffMinutes < 1) {
                return 'just now';
            }

            if (diffMinutes < 60) {
                return `${diffMinutes} min${diffMinutes === 1 ? '' : 's'} ago`;
            }

            const diffHours = Math.floor(diffMinutes / 60);
            if (diffHours < 24) {
                return `${diffHours} hr${diffHours === 1 ? '' : 's'} ago`;
            }

            const diffDays = Math.floor(diffHours / 24);
            if (diffDays < 30) {
                return `${diffDays} day${diffDays === 1 ? '' : 's'} ago`;
            }

            return date.toLocaleDateString('en-US', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            });
        }

        function formatPrintDateTime(value) {
            const normalized = normalizeRelativeDateInput(value);
            if (!normalized) return '';

            const date = new Date(normalized);
            if (Number.isNaN(date.getTime())) return '';

            return date.toLocaleString('en-US', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        }

        function setKotPrintButtonState(enabled) {
            if (!drawerPrintKotBtn) return;
            drawerPrintKotBtn.disabled = !enabled;
        }

        function syncDrawerViewTabs() {
            const isKotView = window.currentOpenTableDrawerView === 'kot';

            if (drawerOrdersTabBtn) {
                drawerOrdersTabBtn.className = isKotView ?
                    'rounded-lg px-4 py-2 text-sm font-semibold text-gray-300 transition hover:text-white cursor-pointer' :
                    'rounded-lg px-4 py-2 text-sm font-semibold bg-orange-500 text-white shadow-sm transition cursor-pointer';
            }

            if (drawerKotTabBtn) {
                drawerKotTabBtn.className = isKotView ?
                    'rounded-lg px-4 py-2 text-sm font-semibold bg-orange-500 text-white shadow-sm transition cursor-pointer' :
                    'rounded-lg px-4 py-2 text-sm font-semibold text-gray-300 transition hover:text-white cursor-pointer';
            }
        }

        function setDrawerView(view) {
            window.currentOpenTableDrawerView = view === 'kot' ? 'kot' : 'orders';
            syncDrawerViewTabs();

            if (window.currentOpenTable && Array.isArray(window.currentOpenTableOrders)) {
                renderCurrentDrawerView(window.currentOpenTable, window.currentOpenTableOrders);
            }
        }

        function updateDrawerKotPrintLabel(kotNumber = null) {
            const safeKotNumber = Number(kotNumber ?? 0);
            const hasKotNumber = Number.isFinite(safeKotNumber) && safeKotNumber > 0;
            const labelText = hasKotNumber ? `Print KOT-${safeKotNumber}` : 'Print KOT';

            if (drawerPrintKotLabel) {
                drawerPrintKotLabel.textContent = labelText;
            }

            if (drawerPrintKotBtn) {
                drawerPrintKotBtn.title = labelText;
            }
        }

        function resetDrawerKotSelectionState() {
            window.currentOpenTableKotNumbers = [];
            window.currentOpenTableSelectedKotNumber = null;
            window.currentOpenTableKotSelectionManual = false;

            if (drawerKotSelector) {
                drawerKotSelector.innerHTML = '';
            }

            if (drawerKotSelectorWrap) {
                drawerKotSelectorWrap.classList.add('hidden');
            }

            updateDrawerKotPrintLabel(null);
        }

        function getLatestKotNumberFromOrders(orders) {
            const kotNumbers = [];

            (Array.isArray(orders) ? orders : []).forEach((order) => {
                (Array.isArray(order?.items) ? order.items : []).forEach((item) => {
                    const kotNumber = Number(item?.kot_number ?? 0);
                    if (Number.isFinite(kotNumber) && kotNumber > 0) {
                        kotNumbers.push(kotNumber);
                    }
                });
            });

            return kotNumbers.length ? Math.max(...kotNumbers) : null;
        }

        function getKotSummariesFromOrders(orders) {
            const kotMap = new Map();

            (Array.isArray(orders) ? orders : []).forEach((order) => {
                (Array.isArray(order?.items) ? order.items : []).forEach((item) => {
                    const kotNumber = Number(item?.kot_number ?? 0);
                    if (!Number.isFinite(kotNumber) || kotNumber <= 0) {
                        return;
                    }

                    const quantity = Number(item?.quantity ?? item?.qty ?? 1);
                    const safeQuantity = Number.isFinite(quantity) && quantity > 0 ? quantity : 1;
                    const printCount = Number(item?.kot_print_count ?? 0);
                    const lastPrintedAt = String(item?.kot_last_printed_at ?? '').trim();
                    const current = kotMap.get(kotNumber) || {
                        kotNumber,
                        itemCount: 0,
                        qtyCount: 0,
                        printCount: 0,
                        lastPrintedAt: '',
                    };

                    current.itemCount += 1;
                    current.qtyCount += safeQuantity;
                    current.printCount = Math.max(current.printCount, Number.isFinite(printCount) ? printCount : 0);
                    if (lastPrintedAt && (!current.lastPrintedAt || lastPrintedAt > current.lastPrintedAt)) {
                        current.lastPrintedAt = lastPrintedAt;
                    }
                    kotMap.set(kotNumber, current);
                });
            });

            return Array.from(kotMap.values()).sort((a, b) => a.kotNumber - b.kotNumber);
        }

        function renderDrawerKotSelector(orders) {
            const summaries = getKotSummariesFromOrders(orders);
            window.currentOpenTableKotNumbers = summaries.map((summary) => summary.kotNumber);

            if (!Array.isArray(orders) || orders.length === 0) {
                resetDrawerKotSelectionState();
                return {
                    activeKotNumber: null,
                    summaries: [],
                };
            }

            const selectedKotNumber = Number(window.currentOpenTableSelectedKotNumber ?? 0);
            const manualSelection = Boolean(window.currentOpenTableKotSelectionManual);
            const selectedStillExists = summaries.some((summary) => summary.kotNumber === selectedKotNumber);
            const latestKotNumber = getLatestKotNumberFromOrders(orders);

            let activeKotNumber = latestKotNumber;
            if (manualSelection && selectedStillExists) {
                activeKotNumber = selectedKotNumber;
            } else {
                window.currentOpenTableKotSelectionManual = false;
            }

            window.currentOpenTableSelectedKotNumber = activeKotNumber;
            updateDrawerKotPrintLabel(activeKotNumber);

            if (!drawerKotSelectorWrap || !drawerKotSelector) {
                return {
                    activeKotNumber,
                    summaries,
                };
            }

            if (summaries.length < 2) {
                drawerKotSelector.innerHTML = '';
                drawerKotSelectorWrap.classList.add('hidden');
                return {
                    activeKotNumber,
                    summaries,
                };
            }

            drawerKotSelectorWrap.classList.remove('hidden');
            const chipTone = (isActive) => isActive ?
                'border-orange-400 bg-orange-500 text-white shadow-sm' :
                'border-gray-700 bg-gray-800 text-gray-200 hover:bg-gray-700';
            const countTone = (isActive) => isActive ?
                'text-white/90' :
                'text-gray-400';

            drawerKotSelector.innerHTML = summaries.map((summary) => {
                const isActive = summary.kotNumber === activeKotNumber;
                const printCount = Number(summary.printCount ?? 0);
                const hasPrinted = printCount > 0;
                const printBadge = hasPrinted ?
                    `<span class="inline-flex items-center justify-center rounded-full bg-green-500/15 px-2 py-0.5 text-[9px] font-bold text-green-300 ring-1 ring-green-500/30">
                        <i class="fas fa-check text-[10px]" aria-hidden="true"></i>
                    </span>` :
                    '';

                return `
                    <button type="button"
                        data-kot-number="${summary.kotNumber}"
                        class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[10px] font-semibold transition ${chipTone(isActive)}"
                        aria-pressed="${isActive ? 'true' : 'false'}"
                        title="Show KOT-${summary.kotNumber}">
                        <span>KOT-${summary.kotNumber}</span>
                        <span class="${countTone(isActive)}">(${summary.itemCount})</span>
                        ${printBadge}
                    </button>`;
            }).join('');

            return {
                activeKotNumber,
                summaries,
            };
        }

        function printCurrentTableKot() {
            const tableNumber = String(window.currentOpenTable ?? '').trim();
            if (!tableNumber) return;

            const branchId = Number(window.currentOpenTableBranchId ?? 0);
            const selectedKotNumber = Number(window.currentOpenTableSelectedKotNumber ?? 0);
            const kotNumber = Number.isFinite(selectedKotNumber) && selectedKotNumber > 0 ?
                selectedKotNumber :
                getLatestKotNumberFromOrders(window.currentOpenTableOrders);
            const pdfUrl = getKotPdfUrl(tableNumber, kotNumber, 'print', branchId);
            if (!pdfUrl) return;

            printKotPdfInHiddenFrame(pdfUrl, {
                tableNumber,
                branchId,
                kotNumber,
            });
        }

        const kotPdfRouteTemplate = @json(route('admin.tables.kot.pdf', ['table_number' => '__TABLE__'], false));

        function getKotPdfUrl(tableNumber, kotNumber = null, outputMode = 'print', branchId = null) {
            const safeTableNumber = String(tableNumber ?? '').trim();
            if (!safeTableNumber) return '';

            const baseUrl = kotPdfRouteTemplate.replace('__TABLE__', encodeURIComponent(safeTableNumber));
            const params = new URLSearchParams();
            const safeBranchId = Number(branchId ?? 0);
            if (Number.isFinite(safeBranchId) && safeBranchId > 0) {
                params.set('branch_id', String(safeBranchId));
            }

            if (outputMode !== 'download') {
                params.set('print', '1');
            }

            const safeKotNumber = String(kotNumber ?? '').trim();
            if (safeKotNumber) {
                params.set('kot_number', safeKotNumber);
            }

            const query = params.toString();
            return query ? `${baseUrl}?${query}` : baseUrl;
        }

        function printKotPdfInHiddenFrame(pdfUrl, context = {}) {
            if (!pdfUrl) return;

            const frameId = 'kot-print-frame';
            document.getElementById(frameId)?.remove();

            const printFrame = document.createElement('iframe');
            printFrame.id = frameId;
            printFrame.title = 'KOT Print Frame';
            printFrame.setAttribute('aria-hidden', 'true');
            printFrame.style.position = 'fixed';
            printFrame.style.right = '0';
            printFrame.style.bottom = '0';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = '0';
            printFrame.style.opacity = '0';
            printFrame.style.pointerEvents = 'none';

            printFrame.addEventListener('load', () => {
                try {
                    setTimeout(() => {
                        try {
                            printFrame.contentWindow?.focus();
                            printFrame.contentWindow?.print();
                        } catch (error) {
                            console.error('KOT PDF print failed', error);
                            return;
                        }

                        const tableNumber = String(context?.tableNumber ?? '').trim();
                        const branchId = Number(context?.branchId ?? 0);
                        if (tableNumber && typeof window.refreshFromServer === 'function') {
                            window.setTimeout(() => {
                                if (window.currentOpenTable === tableNumber) {
                                    window.refreshFromServer(tableNumber, branchId);
                                }
                            }, 150);
                        }
                    }, 200);
                } catch (error) {
                    console.error('KOT PDF frame failed', error);
                }
            });

            printFrame.src = pdfUrl;
            document.body.appendChild(printFrame);
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
            window.currentOpenTableOrders = [];
            resetDrawerKotSelectionState();
            setKotPrintButtonState(false);
            setDrawerSubtitle(formatOrderItemCount(0));
            listArea.innerHTML = `
                <div class="text-center p-10 text-gray-500">
                    Loading active orders for Table ${tableNum}...
                </div>`;
        }

        function renderCurrentDrawerView(tableNum, orders) {
            syncDrawerViewTabs();

            if (window.currentOpenTableDrawerView === 'kot') {
                renderKotViewToDrawer(tableNum, orders);
                return;
            }

            renderOrdersToDrawer(tableNum, orders);
        }

        // 🔥 Global Refresh Function: Server se taaza data lane ke liye
        window.refreshFromServer = async function(tableNum, branchId = window.currentOpenTableBranchId) {
            const normalizedTableNum = String(tableNum);
            const normalizedBranchId = Number(branchId ?? 0) > 0 ? String(Number(branchId)) : '';
            const requestId = ++activeRequestId;

            if (activeFetchController) {
                activeFetchController.abort();
            }

            activeFetchController = new AbortController();

            try {
                const params = new URLSearchParams();
                if (normalizedBranchId) {
                    params.set('branch_id', normalizedBranchId);
                }

                const requestUrl = params.toString() ?
                    `/admin/get-table-orders/${normalizedTableNum}?${params.toString()}` :
                    `/admin/get-table-orders/${normalizedTableNum}`;

                const response = await fetch(requestUrl, {
                    signal: activeFetchController.signal
                });

                if (!response.ok) {
                    throw new Error(`Failed to fetch orders: ${response.status}`);
                }

                const orders = await response.json();

                // Sirf latest request + currently open table ka result render hoga
                if (requestId !== activeRequestId || window.currentOpenTable !== normalizedTableNum ||
                    String(window.currentOpenTableBranchId ?? '') !== normalizedBranchId) {
                    return;
                }

                // UI Render karo
                renderCurrentDrawerView(normalizedTableNum, orders);
            } catch (err) {
                if (err.name === 'AbortError') {
                    return;
                }

                if (requestId !== activeRequestId || window.currentOpenTable !== normalizedTableNum ||
                    String(window.currentOpenTableBranchId ?? '') !== normalizedBranchId) {
                    return;
                }

                console.error("Order fetch error:", err);
                window.currentOpenTableOrders = [];
                resetDrawerKotSelectionState();
                setKotPrintButtonState(false);
                setDrawerSubtitle(formatOrderItemCount(0));
                listArea.innerHTML =
                    `<div class="text-center p-10 text-red-400">Unable to load active orders</div>`;
            }
        };

        function renderOrdersToDrawer(tableNum, orders) {
            if (!orders || orders.length === 0) {
                window.currentOpenTableOrders = [];
                resetDrawerKotSelectionState();
                setKotPrintButtonState(false);
                setDrawerSubtitle(formatOrderItemCount(0));
                listArea.innerHTML = `
                    <div class="flex min-h-[240px] items-center justify-center px-4 py-10 text-center">
                        <div>
                            <div class="mx-auto flex items-center justify-center">
                                ${EMPTY_ORDERS_ICON_HTML}
                            </div>
                            <h3 class="mt-4 text-base font-semibold text-gray-500 dark:text-gray-100" style="color: #6b7280;">No active orders</h3>
                            <p class="mt-1 text-sm text-gray-500">Fresh orders will appear here.Click 'Add Item' to place an order.</p>
                        </div>
                    </div>`;
                return;
            }

            window.currentOpenTableOrders = orders;
            setKotPrintButtonState(true);
            const totalItems = getOrderItemCount(orders);
            const primaryStatus = getPrimaryOrderStatus(orders);
            setDrawerSubtitle(formatOrderItemCount(totalItems), primaryStatus);

            let html = '';
            orders.forEach(order => {
                order.items.forEach(item => {
                    const addons = item.order_item_addons || item.orderItemAddons || [];
                    const rawStatus = String(item.status ?? '').trim();
                    const isReadyForServe = rawStatus.toLowerCase() === 'ready';
                    const actionLocked = isItemActionLocked(rawStatus);
                    const statusMeta = getItemStatusMeta(rawStatus);
                    const itemTimeSource = item?.created_at || item?.createdAt || order
                        ?.ordered_at_iso ||
                        order?.ordered_at || order?.created_at || '';
                    const itemTimeLabel = formatRelativeTime(itemTimeSource);
                    const addonsHtml = addons.length ?
                        `<div class="mt-1.5 text-[11px] text-orange-400/90 leading-5 pl-1">` +
                        addons.map(addon => {
                            const qty = Number(addon.quantity || 1);
                            const qtyText = qty > 1 ? ` x${qty}` : '';
                            const parsePrice = (value) => {
                                const cleaned = String(value ?? '').replace(/,/g, '')
                                    .trim();
                                const number = Number(cleaned);
                                return Number.isFinite(number) ? number : 0;
                            };
                            const storedPrice = parsePrice(addon.price);
                            const priceValue = storedPrice > 0 ?
                                storedPrice :
                                parsePrice(addon.masterAddon?.price ?? addon
                                    .menu_item_addon_price ?? 0);
                            const price = priceValue.toFixed(2);
                            const name = addon.addon_name ?? addon.name ?? addon.masterAddon
                                ?.name ?? 'Addon';
                            return `<div class="flex items-center justify-between gap-3"><span>${name}${qtyText}</span><span class="text-orange-300">₹${price}</span></div>`;
                        }).join('') +
                        `</div>` :
                        '';

                    html += `
                        <div class="flex justify-between items-start p-3 rounded-lg border border-l-2 border-l-orange-500 mb-2">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-200">
                                    <span class="text-orange-500 font-bold">${item.quantity}x</span> ${item.item_name}
                                </span>
                                ${addonsHtml}
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1 text-[10px] text-gray-500 tracking-wider capitalize">
                                        <i class="fas ${statusMeta.icon} ${statusMeta.tone} text-[9px]" aria-hidden="true"></i>
                                        <span>${statusMeta.label}</span>
                                    </span>
                                    ${isReadyForServe ? `
                                        <button type="button" data-item-action="served" data-item-id="${item.id}"
                                            class="inline-flex items-center gap-1 rounded-full border border-green-200 bg-green-50 px-2 py-0.5 text-[10px] font-semibold text-green-600 transition hover:border-green-300 hover:bg-green-100"
                                            title="Mark as served" aria-label="Mark item as served">
                                            <i class="fas fa-check text-[9px]" aria-hidden="true"></i>
                                            <span>Serve</span>
                                        </button>
                                    ` : ''}
                                    <span class="h-1.5 w-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                </div>
                                ${itemTimeLabel
                                    ? `
                                        <div class="mt-1 flex items-center gap-1 text-[10px] text-gray-500">
                                            <i class="fa-regular fa-clock text-[9px]" aria-hidden="true"></i>
                                            <span>${itemTimeLabel}</span>
                                        </div>
                                    `
                                    : ''
                                }
                            </div>
                            <div class="relative flex shrink-0 flex-col items-end gap-1"
                                data-item-action-menu-wrapper="${item.id}">
                                <span class="text-[13px] text-gray-400 font-semibold">
                                    Rs. ${item.total}
                                </span>

                                <button
                                    type="button"
                                    class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-100 hover:text-gray-800"
                                    aria-label="More actions"
                                    title="More actions"
                                    data-item-action-menu-trigger="${item.id}">
                                    <i class="fas fa-ellipsis-v text-[11px]"></i>
                                </button>

                                <div data-item-action-menu
                                    class="hidden absolute right-0 top-full mt-2 w-40 rounded-xl border border-gray-200 bg-white p-1.5 shadow-xl z-30">
                                    <button type="button" data-item-action="served" data-item-id="${item.id}"
                                        ${actionLocked ? 'disabled aria-disabled="true"' : ''}
                                        class="flex w-full items-center gap-2 rounded-lg px-2.5 py-2 text-left text-xs font-semibold transition ${actionLocked ? 'cursor-not-allowed bg-gray-50 text-gray-400 opacity-50' : 'text-green-600 hover:bg-green-50'}">
                                        <i class="fas fa-check w-3.5"></i>
                                        <span>Served</span>
                                    </button>
                                    <button type="button" data-item-action="cancelled" data-item-id="${item.id}"
                                        ${actionLocked ? 'disabled aria-disabled="true"' : ''}
                                        class="flex w-full items-center gap-2 rounded-lg px-2.5 py-2 text-left text-xs font-semibold transition ${actionLocked ? 'cursor-not-allowed bg-gray-50 text-gray-400 opacity-50' : 'text-red-600 hover:bg-red-50'}">
                                        <i class="fas fa-ban w-3.5"></i>
                                        <span>Cancelled</span>
                                    </button>
                                </div>
                            </div>
                     </div>`;
                });
            });
            listArea.innerHTML = html;
        }

        function renderKotViewToDrawer(tableNum, orders) {
            window.currentOpenTableOrders = Array.isArray(orders) ? orders : [];

            if (!orders || orders.length === 0) {
                setKotPrintButtonState(false);
                setDrawerSubtitle(formatOrderItemCount(0));
                listArea.innerHTML = `
                    <div class="flex min-h-[240px] items-center justify-center px-4 py-10 text-center">
                        <div>
                            <div class="mx-auto flex items-center justify-center">
                                ${EMPTY_ORDERS_ICON_HTML}
                            </div>
                            <h3 class="mt-4 text-base font-semibold text-gray-500 dark:text-gray-100" style="color: #6b7280;">No active orders</h3>
                            <p class="mt-1 text-sm text-gray-500">Fresh orders will appear here when the table starts cooking.</p>
                        </div>
                    </div>`;
                return;
            }

            const groups = getKotGroupsFromOrders(orders);
            if (!groups.length) {
                setKotPrintButtonState(false);
                setDrawerSubtitle('0 KOT batches');
                listArea.innerHTML = `
                    <div class="flex min-h-[240px] items-center justify-center px-4 py-10 text-center">
                        <div>
                            <div class="mx-auto flex items-center justify-center">
                                ${EMPTY_ORDERS_ICON_HTML}
                            </div>
                            <h3 class="mt-4 text-base font-semibold text-gray-500">No KOT batches yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Orders will appear here once a KOT is generated.</p>
                        </div>
                    </div>`;
                return;
            }

            setKotPrintButtonState(false);
            const totalItems = groups.reduce((total, group) => total + Number(group.itemCount || 0), 0);
            setDrawerSubtitle(
                `${groups.length} KOT batch${groups.length === 1 ? '' : 'es'} • ${totalItems} item${totalItems === 1 ? '' : 's'}`
            );
            const humanizeLabel = (value, fallback = 'N/A') => {
                const raw = String(value ?? '').replace(/_/g, ' ').trim();
                if (!raw) return fallback;

                return raw
                    .split(/\s+/)
                    .map((part) => part ? part.charAt(0).toUpperCase() + part.slice(1).toLowerCase() : part)
                    .join(' ');
            };

            const renderKotItemRow = (item, index) => {
                const quantity = Number(item?.quantity ?? item?.qty ?? 1);
                const safeQuantity = Number.isFinite(quantity) && quantity > 0 ? quantity : 1;
                const itemName = escapeHtml(item?.item_name ?? item?.name ?? 'Item');
                const notes = String(item?.notes ?? '').trim();
                const addons = Array.isArray(item?.order_item_addons) ? item.order_item_addons : (Array.isArray(
                    item?.orderItemAddons) ? item.orderItemAddons : []);
                const addonsLabel = addons.length ?
                    addons.map((addon) => {
                        const addonName = escapeHtml(
                            addon?.addon_name ??
                            addon?.name ??
                            addon?.masterAddon?.name ??
                            'Addon'
                        );
                        const addonQty = Number(addon?.quantity ?? 1);
                        const safeAddonQty = Number.isFinite(addonQty) && addonQty > 0 ? addonQty : 1;
                        const addonQtyLabel = safeAddonQty > 1 ? ` x${safeAddonQty}` : '';

                        return `${addonName}${addonQtyLabel}`;
                    }).join('') :
                    '';
                const extraLines = [];
                if (notes) {
                    extraLines.push(`↳ ${escapeHtml(notes)}`);
                }
                if (addonsLabel) {
                    extraLines.push(`↳ ${addonsLabel}`);
                }

                return `
                    <div class="py-1">
                        <div class="grid grid-cols-[22px_minmax(0,1fr)_30px] gap-2 items-start">
                            <div class="text-[12px]">${index}.</div>
                            <div class="min-w-0 text-[12px]">
                                <div>${itemName}</div>
                                ${extraLines.length ? `<div class="mt-1 pl-4 text-[10px] text-black">${extraLines.join('<br>')}</div>` : ''}
                            </div>
                            <div class="text-right text-[12px]">${safeQuantity}</div>
                        </div>
                    </div>`;
            };

            const html = groups.map((group, groupIndex) => {
                const printCount = Number(group.printCount ?? 0);
                const hasPrinted = printCount > 0;
                const paperOrderType = humanizeLabel(group.orderType ?? 'dine_in', 'Dine In');
                const paperOrderBy = String(group.orderByLabel ?? '').trim() || 'Guest';
                const paperOrderAt = formatPrintDateTime(group.orderAt) || 'Now';
                const printButtonClass = hasPrinted ?
                    'absolute right-0 top-0 inline-flex items-center gap-1.5 rounded-lg border border-green-500 bg-green-50 px-3 py-1 text-green-700 shadow-sm cursor-pointer' :
                    'absolute right-0 top-0 inline-flex items-center gap-1 rounded-lg border border-green-500 bg-white px-3 py-1 text-green-600 shadow-sm cursor-pointer';
                const printButtonBadge = hasPrinted ?
                    `<i class="fas fa-check text-[12px] text-green-600" aria-hidden="true"></i>` :
                    '';

                return `
                    <section class="rounded-lg border border-gray-300 bg-white p-3 text-black card-hover hover:bg-orange-50 hover:border-orange-300 ${groupIndex < groups.length - 1 ? 'mb-3' : ''}">
                        <div class="relative pb-2">
                            <div class="text-center">
                                <div class="text-[14px] leading-5">KOT ${group.kotNumber}</div>
                            </div>
                            <button type="button"
                                data-kot-print="${group.kotNumber}"
                                class="${printButtonClass}"
                                aria-label="Print KOT-${group.kotNumber}${hasPrinted ? ' printed' : ''}"
                                title="Print KOT-${group.kotNumber}">
                                <i class="fas fa-print text-[11px]" aria-hidden="true"></i>
                                ${printButtonBadge}
                            </button>
                        </div>
                        <div class="space-y-1 text-[12px] leading-5">
                            <div>Type: ${escapeHtml(paperOrderType)}</div>
                            <div>Order By: ${escapeHtml(paperOrderBy)}</div>
                            <div>Order At: ${escapeHtml(paperOrderAt)}</div>
                        </div>
                        <div class="my-1 border-t border-dashed border-gray-300"></div>
                        <div class="grid grid-cols-[22px_minmax(0,1fr)_30px] gap-2 text-[12px] leading-5">
                            <div>S.N</div>
                            <div>Dishes</div>
                            <div class="text-right">QTY</div>
                        </div>
                        <div class=" border-t border-dashed border-gray-300"></div>
                        <div class="divide-y divide-gray-100">
                            ${group.items.map((item, index) => renderKotItemRow(item, index + 1)).join('')}
                        </div>
                        <div class="my-1 border-t border-dashed border-gray-300"></div>
                        <div class="grid grid-cols-[1fr_auto] items-center gap-3 text-[12px] leading-5">
                            <div>Total (Dishes/QTY)</div>
                            <div class="text-right">${group.itemCount}/${group.qtyCount}</div>
                        </div>
                        <div class=" border-t border-dashed border-gray-300"></div>
                        <div class="py-1 text-center text-[10px] leading-5">
                            Thank You!
                        </div>
                    </section>`;
            }).join('');

            listArea.innerHTML = html;
        }

        if (drawerOrdersTabBtn) {
            drawerOrdersTabBtn.addEventListener('click', () => setDrawerView('orders'));
        }

        if (drawerKotTabBtn) {
            drawerKotTabBtn.addEventListener('click', () => setDrawerView('kot'));
        }

        // Table Card Click
        document.querySelectorAll('.table-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (e.target.closest('button') || e.target.closest('img')) return;

                const tableNum = card.dataset.tableNumber;
                window.currentOpenTable = tableNum;
                window.currentOpenTableId = card.dataset.id || null;
                window.currentOpenTableBranchId = card.dataset.branchId || null;
                window.currentOpenTableDrawerView = 'orders';
                syncDrawerViewTabs();
                const drawerTitle = document.getElementById('drawerTitle');
                if (drawerTitle) {
                    drawerTitle.innerText = card.dataset.name;
                }
                renderLoadingState(tableNum);
                markTableActivitySeen(tableNum);
                markWaiterCallSeen(tableNum);

                // Har click par server se fresh data lao
                window.refreshFromServer(tableNum, window.currentOpenTableBranchId);

                drawer.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
            });
        });

        if (drawerPrintKotBtn) {
            drawerPrintKotBtn.addEventListener('click', printCurrentTableKot);
        }

        document.addEventListener('click', async (event) => {
            const kotPrintButton = event.target.closest('[data-kot-print]');
            if (kotPrintButton) {
                event.preventDefault();
                event.stopPropagation();

                const kotNumber = Number(kotPrintButton.dataset.kotPrint ?? 0);
                const tableNumber = String(window.currentOpenTable ?? '').trim();
                const branchId = Number(window.currentOpenTableBranchId ?? 0);
                if (!tableNumber || !Number.isFinite(kotNumber) || kotNumber <= 0) {
                    return;
                }

                const pdfUrl = getKotPdfUrl(tableNumber, kotNumber, 'print', branchId);
                if (!pdfUrl) return;

                printKotPdfInHiddenFrame(pdfUrl, {
                    tableNumber,
                    branchId,
                    kotNumber,
                });
                return;
            }

            const trigger = event.target.closest('[data-item-action-menu-trigger]');
            if (trigger) {
                event.preventDefault();
                event.stopPropagation();

                const wrapper = trigger.closest('[data-item-action-menu-wrapper]');
                const menu = wrapper?.querySelector('[data-item-action-menu]');
                if (!menu) return;

                const willOpen = menu.classList.contains('hidden');
                closeItemActionMenus(menu);

                if (willOpen) {
                    menu.classList.remove('hidden');
                }

                return;
            }

            const actionButton = event.target.closest('[data-item-action]');
            if (actionButton) {
                event.preventDefault();
                event.stopPropagation();

                const itemId = actionButton.dataset.itemId;
                const action = actionButton.dataset.itemAction;
                if (actionButton.disabled || actionButton.getAttribute('aria-disabled') === 'true') {
                    return;
                }
                closeItemActionMenus();

                if (!itemId || !action) return;

                if (action === 'served') {
                    await serveDrawerItem(itemId);
                } else if (action === 'cancelled') {
                    await cancelDrawerItem(itemId);
                }

                return;
            }

            if (!event.target.closest('[data-item-action-menu]')) {
                closeItemActionMenus();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeItemActionMenus();
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
            window.currentOpenTableBranchId = null;
            window.currentOpenTableDrawerView = 'orders';
            syncDrawerViewTabs();
            resetDrawerKotSelectionState();
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
