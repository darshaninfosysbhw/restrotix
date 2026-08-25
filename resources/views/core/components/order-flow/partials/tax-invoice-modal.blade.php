<section id="taxInvoiceModal" class="fixed inset-0 z-[250] hidden items-end justify-center bg-black/70 backdrop-blur-xs"
    onclick="if (event.target === this) closeTaxInvoiceModal();">
    @php
        $invoiceBranding = $invoiceBranding ?? [];
        $invoiceData = $invoiceData ?? [];
        $isLightTheme = strtolower((string) ($publicMenuTheme ?? 'dark')) === 'light';
        $restaurantName = (string) ($invoiceBranding['restaurant_name'] ?? 'Restaurant');
        $branchName = trim((string) ($invoiceBranding['branch_name'] ?? ''));
        $branchAddress = trim((string) ($invoiceBranding['branch_address'] ?? ''));
        $branchContact = trim((string) ($invoiceBranding['branch_contact'] ?? ''));
        $branchEmail = trim((string) ($invoiceBranding['branch_email'] ?? ''));
        $taxRegistration = trim((string) ($invoiceBranding['tax_registration'] ?? ''));
        $invoiceNumber = trim(
            (string) ($invoiceData['invoice_number'] ?? ($summary['invoice_number'] ?? ($summary['order_id'] ?? ''))),
        );
        $invoiceDate = trim((string) ($invoiceData['invoice_date'] ?? ($summary['invoice_date'] ?? '')));
        $vatAmount = (float) ($summary['vat'] ?? ($summary['tax'] ?? 0));
        $grandTotalAmount = (float) ($summary['grand_total'] ?? 0);
        $numberToWords = null;
        $numberToWords = function (int $number) use (&$numberToWords): string {
            if ($number === 0) {
                return 'Zero';
            }

            $ones = [
                0 => '',
                1 => 'One',
                2 => 'Two',
                3 => 'Three',
                4 => 'Four',
                5 => 'Five',
                6 => 'Six',
                7 => 'Seven',
                8 => 'Eight',
                9 => 'Nine',
                10 => 'Ten',
                11 => 'Eleven',
                12 => 'Twelve',
                13 => 'Thirteen',
                14 => 'Fourteen',
                15 => 'Fifteen',
                16 => 'Sixteen',
                17 => 'Seventeen',
                18 => 'Eighteen',
                19 => 'Nineteen',
            ];
            $tens = [
                0 => '',
                1 => '',
                2 => 'Twenty',
                3 => 'Thirty',
                4 => 'Forty',
                5 => 'Fifty',
                6 => 'Sixty',
                7 => 'Seventy',
                8 => 'Eighty',
                9 => 'Ninety',
            ];
            $units = [
                ['name' => 'Crore', 'value' => 10000000],
                ['name' => 'Lakh', 'value' => 100000],
                ['name' => 'Thousand', 'value' => 1000],
                ['name' => 'Hundred', 'value' => 100],
            ];

            $convertUnderHundred = function (int $n) use ($ones, $tens): string {
                if ($n < 20) {
                    return $ones[$n] ?? '';
                }

                $ten = intdiv($n, 10);
                $one = $n % 10;

                return trim($tens[$ten] . ($one ? ' ' . ($ones[$one] ?? '') : ''));
            };

            $words = [];
            $remaining = $number;

            foreach ($units as $unit) {
                if ($remaining >= $unit['value']) {
                    $count = intdiv($remaining, $unit['value']);
                    $remaining %= $unit['value'];
                    $words[] = $count > 99 ? $numberToWords($count) : $convertUnderHundred($count);
                    $words[] = $unit['name'];
                }
            }

            if ($remaining > 0) {
                $words[] = $convertUnderHundred($remaining);
            }

            return trim(implode(' ', array_filter($words)));
        };
        $amountToWords = function (float $amount) use (&$numberToWords): string {
            $amount = round(max($amount, 0), 2);
            $rupees = (int) floor($amount);
            $paise = (int) round(($amount - $rupees) * 100);

            if ($paise === 100) {
                $rupees++;
                $paise = 0;
            }

            $currencyLabel = $rupees === 1 ? 'Rupee' : 'Rupees';
            $minorLabel = $paise === 1 ? 'Paisa' : 'Paise';
            $rupeeWords = $numberToWords($rupees);

            if ($rupees === 0 && $paise === 0) {
                return 'Zero ' . $currencyLabel . ' Only';
            }

            if ($paise > 0) {
                return trim($rupeeWords . ' ' . $currencyLabel . ' and ' . $numberToWords($paise) . ' ' . $minorLabel . ' Only');
            }

            return trim($rupeeWords . ' ' . $currencyLabel . ' Only');
        };
        $amountInWords = $amountToWords($grandTotalAmount);
        $invoiceLocationLines = array_filter([
            $branchName,
            $branchAddress,
            $branchContact ? 'Phone: ' . $branchContact : null,
            $branchEmail ? 'Email: ' . $branchEmail : null,
        ]);
    @endphp

    <div role="dialog" aria-label="Tax Invoice"
        class="w-full max-w-lg flex max-h-[90vh] flex-col overflow-y-auto rounded-t-[2rem] {{ $isLightTheme ? 'bg-white text-slate-900' : 'bg-[#0f172a] text-white' }} shadow-2xl no-scrollbar">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }} px-4 py-5 flex-shrink-0">
            <button type="button" onclick="backToPaymentSuccessModal()"
                class="justify-self-start inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full {{ $isLightTheme ? 'text-slate-400 hover:bg-slate-100 hover:text-slate-700' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition"
                aria-label="Back to payment success">
                <i class="fas fa-arrow-left text-sm"></i>
            </button>
            <div class="text-center">
                <div class="text-[14px] font-black uppercase tracking-[0.28em] {{ $isLightTheme ? 'text-slate-700' : 'text-white' }}">Tax Invoice</div>
            </div>
            <button type="button" onclick="closeTaxInvoiceModal()"
                class="justify-self-end inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full {{ $isLightTheme ? 'text-slate-400 hover:bg-slate-100 hover:text-slate-700' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition"
                aria-label="Close tax invoice modal">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="px-4 pt-6 pb-4 {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">
            <div class="text-center">
                <h3 class="text-[1.6rem] leading-none font-black text-orange-600">{{ $restaurantName }}</h3>
                @if (!empty($invoiceLocationLines))
                    <p class="mt-2 text-[13px] leading-6 {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">{{ implode(' · ', $invoiceLocationLines) }}</p>
                @endif
                @if ($taxRegistration !== '')
                    <p class="text-[13px] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">GSTIN: {{ $taxRegistration }}</p>
                @endif
            </div>

            <div class="mt-4 border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }}"></div>

            <div class="mt-4 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[13px] font-black uppercase tracking-[0.28em] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">Tax Invoice</p>
                </div>
                <div
                    class="rotate-[-10deg] rounded-lg border-4 border-emerald-600 px-3 py-1 text-[14px] font-black text-emerald-600">
                    PAID
                </div>
            </div>

            <div class="mt-3 overflow-hidden border-t border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }}">
                <div class="grid grid-cols-2 divide-x {{ $isLightTheme ? 'divide-slate-200' : 'divide-white/10' }}">
                    <div class="px-3 py-2 text-left">
                        <p class="text-[12px] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">Invoice No.</p>
                        <p class="mt-1 text-[11px] font-black {{ $isLightTheme ? 'text-slate-950' : 'text-white' }}">{{ $invoiceNumber }}</p>
                    </div>
                    <div class="px-3 py-2 text-left {{ $isLightTheme ? 'border-r border-r-white' : 'border-r border-r-white/10' }}">
                        <p class="text-[12px] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">Date</p>
                        <p class="mt-1 text-[11px] font-black {{ $isLightTheme ? 'text-slate-950' : 'text-white' }}">{{ $invoiceDate ?: '—' }}</p>
                    </div>
                    <div class="col-span-2 border-t {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }}"></div>
                    <div class="px-3 py-2 text-left">
                        <p class="text-[12px] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">Table No.</p>
                        <p class="mt-1 text-[11px] font-black {{ $isLightTheme ? 'text-slate-950' : 'text-white' }}">{{ $summary['table'] }}</p>
                    </div>
                    <div class="px-3 py-2 text-left">
                        <p class="text-[12px] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">Order ID</p>
                        <p class="mt-1 text-[11px] font-black {{ $isLightTheme ? 'text-slate-950' : 'text-white' }} break-all">{{ $summary['order_id'] }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-5 overflow-hidden rounded-xl border {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }}">
                <div
                    class="grid grid-cols-[minmax(0,1.7fr)_minmax(0,.35fr)_minmax(0,.55fr)_minmax(0,.65fr)] border-b {{ $isLightTheme ? 'border-slate-200 bg-slate-50 text-slate-500' : 'border-white/10 bg-white/5 text-gray-300' }} px-3 py-2 text-[11px] font-bold">
                    <span class="min-w-0">Item</span>
                    <span class="min-w-0 text-center">Qty</span>
                    <span class="min-w-0 text-right">Rate</span>
                    <span class="min-w-0 text-right">Amount</span>
                </div>
                @foreach ($orderItems as $item)
                    <div
                        class="grid grid-cols-[minmax(0,1.7fr)_minmax(0,.35fr)_minmax(0,.55fr)_minmax(0,.65fr)] border-b {{ $isLightTheme ? 'border-slate-100' : 'border-white/10' }} px-3 py-2 text-[11px] {{ $item['status'] === 'Rejected' ? 'text-red-500' : ($isLightTheme ? 'text-slate-700' : 'text-gray-200') }}">
                            <span
                                class="min-w-0 truncate {{ $item['status'] === 'Rejected' ? 'line-through' : '' }}">{{ $item['name'] }}</span>
                            <span class="min-w-0 text-center tabular-nums">{{ $item['qty'] }}</span>
                            <span class="min-w-0 text-right tabular-nums">{{ number_format($item['rate'], 2) }}</span>
                            <span class="min-w-0 text-right tabular-nums">{{ number_format($item['amount'], 2) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 space-y-2 text-sm">
                <div class="flex items-center justify-between {{ $isLightTheme ? 'text-slate-600' : 'text-gray-300' }}">
                    <span>Subtotal</span>
                    <span class="{{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">₹{{ number_format($summary['subtotal'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between {{ $isLightTheme ? 'text-slate-600' : 'text-gray-300' }}">
                    <span>VAT (13%)</span>
                    <span class="{{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">₹{{ number_format($vatAmount, 2) }}</span>
                </div>
            </div>

            <div class="mt-5 border-t border-b border-dashed {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }} pt-3">
                <div class="flex items-center justify-between text-base font-black">
                    <span>TOTAL PAYABLE</span>
                    <span class="text-orange-600">₹{{ number_format($grandTotalAmount, 2) }}</span>
                </div>
            </div>

            <div class="mt-2 text-[12px] leading-5 border-b border-dashed {{ $isLightTheme ? 'border-slate-200 text-slate-500' : 'border-white/10 text-gray-400' }}">
                <span class="font-semibold {{ $isLightTheme ? 'text-slate-700' : 'text-white' }}">{{ $amountInWords }}</span>
            </div>

            <div class="mt-2 rounded-2xl {{ $isLightTheme ? 'bg-emerald-50' : 'bg-emerald-500/10' }} px-3 py-4 text-center">
                <div class="text-[14px] font-black uppercase tracking-[0.22em] text-emerald-700">Thank You</div>
                <p class="mt-1 text-[13px] font-medium {{ $isLightTheme ? 'text-emerald-700' : 'text-emerald-100' }}">Thank you for your visit! Visit again</p>
            </div>

            <div class="my-4 border-t border-dashed {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }}"></div>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('public.order.status.pdf', $qrToken ?? '') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-orange-500 px-4 py-3 text-[13px] font-black text-orange-600">
                    <i class="fas fa-download text-xs"></i>
                    Download PDF
                </a>
                <button type="button" onclick="shareTaxInvoiceReceipt(this)"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-orange-500 px-4 py-3 text-[13px] font-black text-orange-600">
                    <i class="fas fa-share-nodes text-xs"></i>
                    Share Invoice
                </button>
            </div>
        </div>

    </div>
</section>

@php
    $sharePayload = [
        'restaurant_name' => $restaurantName,
        'branch_name' => $branchName,
        'branch_address' => $branchAddress,
        'branch_contact' => $branchContact,
        'branch_email' => $branchEmail,
        'tax_registration' => $taxRegistration,
        'invoice_number' => $invoiceNumber ?? ($summary['order_id'] ?? 'invoice'),
        'invoice_date' => $invoiceDate ?? '',
        'table' => $summary['table'] ?? '',
        'order_id' => $summary['order_id'] ?? '',
        'subtotal' => (float) ($summary['subtotal'] ?? 0),
        'vat' => (float) ($vatAmount ?? 0),
        'grand_total' => (float) ($grandTotalAmount ?? 0),
        'amount_in_words' => $amountInWords ?? '',
        'items' => $orderItems ?? [],
    ];
@endphp

<script>
    async function shareTaxInvoiceReceipt(buttonEl) {
        const shareData = @json($sharePayload);
        const invoiceNumber = String(shareData.invoice_number || 'invoice');
        const fileName = `${invoiceNumber.replace(/[^a-z0-9-_]+/gi, '_') || 'invoice'}.png`;

        if (buttonEl) {
            buttonEl.disabled = true;
            buttonEl.dataset.originalText = buttonEl.innerHTML;
            buttonEl.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Sharing...';
        }

        try {
            if (!window.isSecureContext) {
                throw new Error('Share works only on HTTPS or localhost. Please open this page in a secure browser context to use WhatsApp/Instagram sharing.');
            }

            const canvas = await buildTaxInvoiceShareCanvas(shareData);
            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png', 1));
            if (!blob) {
                throw new Error('Unable to create invoice image.');
            }

            const file = new File([blob], fileName, { type: 'image/png' });

            if (navigator.share && (!navigator.canShare || navigator.canShare({ files: [file] }))) {
                await navigator.share({
                    title: 'Tax Invoice',
                    text: 'Please find the tax invoice receipt attached.',
                    files: [file],
                });
                return;
            }

            throw new Error('Your browser does not support native share for files.');
        } catch (error) {
            alert(error.message || 'Unable to share invoice right now.');
        } finally {
            if (buttonEl) {
                buttonEl.disabled = false;
                buttonEl.innerHTML = buttonEl.dataset.originalText ||
                    '<i class="fas fa-share-nodes text-xs"></i> Share Invoice';
            }
        }
    }

    async function buildTaxInvoiceShareCanvas(data) {
        const width = 1080;
        const padding = 56;
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = Math.max(1800, 1220 + (Array.isArray(data.items) ? data.items.length * 64 : 0));
        const ctx = canvas.getContext('2d');

        const colors = {
            bg: '#ffffff',
            text: '#0f172a',
            muted: '#64748b',
            border: '#e2e8f0',
            accent: '#f97316',
            green: '#059669',
            softGreen: '#ecfdf5',
            softOrange: '#fff7ed',
        };

        const drawRoundedRect = (x, y, w, h, r, fill = true, stroke = false) => {
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.arcTo(x + w, y, x + w, y + h, r);
            ctx.arcTo(x + w, y + h, x, y + h, r);
            ctx.arcTo(x, y + h, x, y, r);
            ctx.arcTo(x, y, x + w, y, r);
            ctx.closePath();
            if (fill) ctx.fill();
            if (stroke) ctx.stroke();
        };

        const wrapText = (text, x, y, maxWidth, lineHeight, font, fillStyle = colors.text, align = 'left') => {
            ctx.font = font;
            ctx.fillStyle = fillStyle;
            ctx.textAlign = align;
            const words = String(text || '').split(/\s+/);
            const lines = [];
            let line = '';

            words.forEach((word) => {
                const testLine = line ? `${line} ${word}` : word;
                if (ctx.measureText(testLine).width > maxWidth && line) {
                    lines.push(line);
                    line = word;
                } else {
                    line = testLine;
                }
            });
            if (line) lines.push(line);

            lines.forEach((ln, index) => {
                ctx.fillText(ln, x, y + (index * lineHeight));
            });
            return y + ((lines.length - 1) * lineHeight);
        };

        ctx.fillStyle = colors.bg;
        ctx.fillRect(0, 0, width, canvas.height);

        let y = 0;
        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(0, 0, width, 20);

        ctx.strokeStyle = colors.border;
        ctx.fillStyle = colors.text;

        ctx.font = 'bold 52px DejaVu Sans, Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillStyle = colors.text;
        ctx.fillText('TAX INVOICE', width / 2, y + 90);

        y += 140;
        ctx.textAlign = 'center';
        ctx.fillStyle = colors.accent;
        ctx.font = '900 54px DejaVu Sans, Arial, sans-serif';
        ctx.fillText(String(data.restaurant_name || 'Restaurant'), width / 2, y + 30);

        ctx.fillStyle = colors.muted;
        ctx.font = '28px DejaVu Sans, Arial, sans-serif';
        const branchLines = [
            [data.branch_name, data.branch_address].filter(Boolean).join(' · '),
            [data.branch_contact ? `Phone: ${data.branch_contact}` : '', data.branch_email ? `Email: ${data.branch_email}` : ''].filter(Boolean).join(' · '),
            data.tax_registration ? `GSTIN: ${data.tax_registration}` : '',
        ].filter(Boolean);
        branchLines.forEach((line, index) => {
            ctx.fillText(line, width / 2, y + 78 + (index * 36));
        });

        y += 200;
        ctx.strokeStyle = colors.border;
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(padding, y);
        ctx.lineTo(width - padding, y);
        ctx.stroke();

        ctx.textAlign = 'left';
        ctx.font = '900 34px DejaVu Sans, Arial, sans-serif';
        ctx.fillStyle = colors.muted;
        ctx.fillText('TAX INVOICE', padding, y + 60);

        ctx.save();
        ctx.translate(width - padding - 70, y + 18);
        ctx.rotate(-0.12);
        ctx.strokeStyle = colors.green;
        ctx.lineWidth = 8;
        drawRoundedRect(0, 0, 160, 70, 12, false, true);
        ctx.fillStyle = colors.green;
        ctx.font = '900 32px DejaVu Sans, Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('PAID', 80, 44);
        ctx.restore();

        y += 100;
        const boxH = 118;
        ctx.strokeStyle = colors.border;
        ctx.lineWidth = 2;
        drawRoundedRect(padding, y, width - (padding * 2), boxH, 10, false, true);
        ctx.beginPath();
        ctx.moveTo(width / 2, y);
        ctx.lineTo(width / 2, y + boxH);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(padding, y + boxH / 2);
        ctx.lineTo(width - padding, y + boxH / 2);
        ctx.stroke();

        ctx.fillStyle = colors.muted;
        ctx.font = '28px DejaVu Sans, Arial, sans-serif';
        ctx.fillText('Invoice No.', padding + 24, y + 36);
        ctx.fillText('Date', width / 2 + 24, y + 36);
        ctx.fillText('Table No.', padding + 24, y + boxH / 2 + 36);
        ctx.fillText('Order ID', width / 2 + 24, y + boxH / 2 + 36);

        ctx.fillStyle = colors.text;
        ctx.font = '900 30px DejaVu Sans, Arial, sans-serif';
        ctx.fillText(String(data.invoice_number || ''), padding + 24, y + 72);
        ctx.fillText(String(data.invoice_date || '—'), width / 2 + 24, y + 72);
        ctx.fillText(String(data.table || ''), padding + 24, y + boxH / 2 + 72);
        ctx.fillText(String(data.order_id || ''), width / 2 + 24, y + boxH / 2 + 72);

        y += boxH + 28;
        const tableX = padding;
        const tableW = width - (padding * 2);
        const itemHeaderH = 64;
        ctx.fillStyle = '#f8fafc';
        drawRoundedRect(tableX, y, tableW, itemHeaderH + (data.items.length * 64), 18, true, false);
        ctx.strokeStyle = colors.border;
        ctx.strokeRect(tableX, y, tableW, itemHeaderH + (data.items.length * 64));

        ctx.fillStyle = colors.muted;
        ctx.font = '900 28px DejaVu Sans, Arial, sans-serif';
        ctx.fillText('Item', tableX + 28, y + 40);
        ctx.fillText('Qty', tableX + tableW * 0.52, y + 40);
        ctx.fillText('Rate', tableX + tableW * 0.68, y + 40);
        ctx.fillText('Amount', tableX + tableW * 0.84, y + 40);

        let rowY = y + itemHeaderH;
        data.items.forEach((item) => {
            ctx.strokeStyle = colors.border;
            ctx.beginPath();
            ctx.moveTo(tableX, rowY);
            ctx.lineTo(tableX + tableW, rowY);
            ctx.stroke();

            const isRejected = String(item.status || '').toLowerCase() === 'rejected';
            ctx.fillStyle = isRejected ? '#dc2626' : colors.text;
            ctx.font = `${isRejected ? 'normal' : 'normal'} 28px DejaVu Sans, Arial, sans-serif`;
            ctx.textAlign = 'left';
            wrapText(item.name, tableX + 28, rowY + 38, tableW * 0.42, 30, '28px DejaVu Sans, Arial, sans-serif', ctx.fillStyle);
            ctx.font = '28px DejaVu Sans, Arial, sans-serif';
            ctx.fillText(String(item.qty), tableX + tableW * 0.52, rowY + 38);
            ctx.fillText(Number(item.rate || 0).toFixed(2), tableX + tableW * 0.68, rowY + 38);
            ctx.fillText(Number(item.amount || 0).toFixed(2), tableX + tableW * 0.84, rowY + 38);
            rowY += 64;
        });

        y = rowY + 34;
        ctx.fillStyle = colors.text;
        ctx.font = '34px DejaVu Sans, Arial, sans-serif';
        ctx.fillText('Subtotal', padding, y);
        ctx.textAlign = 'right';
        ctx.fillText(`₹${Number(data.subtotal || 0).toFixed(2)}`, width - padding, y);

        y += 56;
        ctx.fillStyle = colors.text;
        ctx.textAlign = 'left';
        ctx.fillText('VAT (13%)', padding, y);
        ctx.textAlign = 'right';
        ctx.fillText(`₹${Number(data.vat || 0).toFixed(2)}`, width - padding, y);

        y += 56;
        ctx.strokeStyle = colors.border;
        ctx.beginPath();
        ctx.moveTo(padding, y);
        ctx.lineTo(width - padding, y);
        ctx.stroke();

        y += 54;
        ctx.fillStyle = colors.text;
        ctx.font = '900 38px DejaVu Sans, Arial, sans-serif';
        ctx.textAlign = 'left';
        ctx.fillText('TOTAL PAYABLE', padding, y);
        ctx.textAlign = 'right';
        ctx.fillStyle = colors.accent;
        ctx.fillText(`₹${Number(data.grand_total || 0).toFixed(2)}`, width - padding, y);

        y += 42;
        ctx.fillStyle = colors.muted;
        ctx.font = '26px DejaVu Sans, Arial, sans-serif';
        ctx.textAlign = 'left';
        ctx.fillText(`Amount in words: `, padding, y);
        ctx.font = '900 26px DejaVu Sans, Arial, sans-serif';
        ctx.fillStyle = colors.text;
        wrapText(String(data.amount_in_words || ''), padding + 220, y, width - padding - 220, 30, '900 26px DejaVu Sans, Arial, sans-serif', colors.text);

        y += 84;
        drawRoundedRect(padding, y, width - (padding * 2), 128, 20, true, false);
        ctx.fillStyle = colors.softGreen;
        ctx.fillRect(padding, y, width - (padding * 2), 128);
        ctx.fillStyle = colors.green;
        ctx.textAlign = 'center';
        ctx.font = '900 32px DejaVu Sans, Arial, sans-serif';
        ctx.fillText('THANK YOU', width / 2, y + 56);
        ctx.font = '26px DejaVu Sans, Arial, sans-serif';
        ctx.fillText('Thank you for your visit! Visit again', width / 2, y + 92);

        return canvas;
    }
</script>
