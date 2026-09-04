<?php

namespace App\Http\Controllers\Modules\PublicMenu;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use App\Models\Tenant;
use App\Services\Admin\MenuManagement\OrderStatusService;
use App\Services\Payments\PaymentGatewayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderStatusController extends Controller
{
    public function __construct(
        protected OrderStatusService $orderStatusService,
        protected PaymentGatewayService $paymentGatewayService
    ) {
    }

    public function orderStatus(Request $request, string $qr_token)
    {
        $table = Table::query()
            ->with('branch')
            ->where('qr_token', $qr_token)
            ->where('is_active', true)
            ->firstOrFail();

        $paymentStatus = strtolower((string) $request->query('payment', ''));
        $order = null;
        $latestOrder = Order::query()
            ->where('table_id', $table->id)
            ->latest()
            ->with(['items.orderItemAddons.masterAddon', 'invoice'])
            ->first();

        if ($request->filled('payment')) {
            $order = $latestOrder;

            if (in_array($paymentStatus, ['completed', 'success', 'paid'], true)) {
                return $this->renderThankYou($table, $order, $request, [
                    'payment_status' => 'completed',
                    'payment_mode' => (string) $request->query('payment_mode', 'Online'),
                    'transaction_id' => (string) $request->query('transaction_id', ''),
                    'message' => (string) $request->query('payment_message', 'Payment completed successfully.'),
                ]);
            }
        }

        if (!$order) {
            try {
                [$table, $order] = $this->orderStatusService->resolveContext($qr_token);
            } catch (ModelNotFoundException $e) {
                if ($latestOrder && in_array(strtolower((string) ($latestOrder->status ?? '')), ['completed', 'paid', 'delivered'], true)) {
                    return $this->renderThankYou($table, $latestOrder, $request, [
                        'payment_status' => 'completed',
                        'payment_mode' => (string) ($latestOrder->invoice?->payment_mode ?? 'Online'),
                        'transaction_id' => (string) ($latestOrder->invoice?->transaction_ref ?? ''),
                        'message' => 'Payment completed successfully. Thank you for visiting.',
                    ]);
                }

                throw $e;
            }
        }

        $paymentFlow = $this->orderStatusService->resolvePaymentFlow($table);

        if ($request->wantsJson() || $request->ajax() || $request->boolean('snapshot')) {
            return response()->json($this->orderStatusService->buildSnapshot($table, $order));
        }

        $pageData = $this->orderStatusService->buildPageData($table, $order, $request->boolean('placed'));
        $publicMenuTheme = strtolower((string) ($table?->branch?->branch_menu_theme ?? 'dark')) === 'light'
            ? 'light'
            : 'dark';

        return view('modules.public-menu.order-status', [
            'table' => $pageData['table'],
            'order' => $pageData['order'],
            'showOrderPlaced' => $pageData['showOrderPlaced'],
            'orderNumber' => $pageData['orderNumber'],
            'orderPlacedAt' => $pageData['orderPlacedAt'],
            'tableNumber' => $pageData['tableNumber'],
            'qrToken' => $pageData['qrToken'],
            'liveItems' => $pageData['liveItems'],
            'runningCount' => $pageData['runningCount'],
            'readyCount' => $pageData['readyCount'],
            'orderItems' => $pageData['orderItems'],
            'subtotal' => $pageData['subtotal'],
            'taxAmount' => $pageData['taxAmount'],
            'grandTotal' => $pageData['grandTotal'],
            'taxRate' => $pageData['taxRate'],
            'branchTaxSetting' => $pageData['branchTaxSetting'],
            'branchTaxLabelName' => $pageData['branchTaxLabelName'],
            'showTaxAmount' => $pageData['showTaxAmount'],
            'kitchenStage' => $pageData['kitchenStage'],
            'statusPill' => $pageData['statusPill'],
            'paymentFlow' => $paymentFlow,
            'publicMenuTheme' => $publicMenuTheme,
            'paymentResult' => [
                'status' => (string) $request->query('payment', ''),
                'payment_mode' => (string) $request->query('payment_mode', ''),
                'transaction_id' => (string) $request->query('transaction_id', ''),
                'gateway_name' => (string) $request->query('gateway_name', ''),
                'message' => (string) $request->query('payment_message', ''),
            ],
        ]);
    }

    public function initiatePayment(Request $request, string $qr_token)
    {
        [$table, $order] = $this->orderStatusService->resolveContext($qr_token);

        try {
            $result = $this->paymentGatewayService->initiate($table, $order);

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated successfully.',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function paymentReturn(Request $request, string $qr_token)
    {
        $table = Table::query()
            ->with('branch')
            ->where('qr_token', $qr_token)
            ->firstOrFail();

        $order = Order::query()
            ->where('table_id', $table->id)
            ->with(['items.orderItemAddons.masterAddon', 'invoice'])
            ->latest()
            ->first();

        if (!$order) {
            return redirect()->route('public.order.thank-you', array_filter([
                'qr_token' => $qr_token,
                'payment_status' => 'completed',
                'payment_mode' => (string) $request->query('payment_mode', 'Online'),
                'transaction_id' => (string) $request->query('transaction_id', ''),
                'payment_message' => (string) $request->query('payment_message', 'Thank you for visiting.'),
            ]));
        }

        $result = $this->paymentGatewayService->handleReturn($table, $order, $request);

        if (($result['status'] ?? 'failed') === 'completed') {
            return redirect()->route('public.order.thank-you', array_filter([
                'qr_token' => $qr_token,
                'payment_status' => 'completed',
                'payment_mode' => $result['payment_mode'] ?? 'Online',
                'transaction_id' => $result['transaction_id'] ?? '',
                'payment_message' => $result['message'] ?? 'Payment completed successfully.',
            ]));
        }

        return redirect()->route('public.order.status', array_filter([
            'qr_token' => $qr_token,
            'payment' => $result['status'] ?? 'failed',
            'payment_mode' => $result['payment_mode'] ?? 'Online',
            'transaction_id' => $result['transaction_id'] ?? '',
            'gateway_name' => $result['gateway_name'] ?? '',
            'payment_message' => $result['message'] ?? '',
        ]));
    }

    public function thankYou(Request $request, string $qr_token)
    {
        $table = Table::query()
            ->with('branch')
            ->where('qr_token', $qr_token)
            ->firstOrFail();

        $latestOrder = Order::query()
            ->where('table_id', $table->id)
            ->with(['invoice'])
            ->latest()
            ->first();

        return $this->renderThankYou($table, $latestOrder, $request, [
            'payment_status' => (string) $request->query('payment_status', 'completed'),
            'payment_mode' => (string) $request->query('payment_mode', (string) ($latestOrder?->invoice?->payment_mode ?? 'Online')),
            'transaction_id' => (string) $request->query('transaction_id', (string) ($latestOrder?->invoice?->transaction_ref ?? '')),
            'message' => (string) $request->query('payment_message', 'Thank you for visiting.'),
        ]);
    }

    public function orderStatusPdf(Request $request, string $qr_token)
    {
        [$table, $order] = $this->resolveInvoiceContext($qr_token);
        $order->loadMissing(['invoice', 'items.orderItemAddons.masterAddon']);
        $pageData = $this->orderStatusService->buildPageData($table, $order, false);
        $excludeRejected = $request->boolean('exclude_rejected');
        $branch = $table->branch;
        $tenant = $branch?->tenant ?? Tenant::query()->find($table->tenant_id);
        $taxRatePercent = (float) ($branch?->tax_rate ?? 0);
        $taxLabel = strtolower((string) ($branch?->tax_setting ?? 'exclusive')) === 'inclusive'
            ? 'VAT'
            : ((float) $taxRatePercent === 13.0 ? 'VAT' : 'Tax');
        $invoice = $order?->invoice;
        $invoiceNumber = (string) ($invoice?->invoice_number ?? $pageData['orderNumber']);
        $subtotalBeforeDiscount = (float) ($invoice?->subtotal_before_discount ?? $pageData['subtotal'] ?? 0);
        $itemDiscountAmount = (float) ($invoice?->item_discount_amount ?? 0);
        $subtotalAfterItemDiscount = (float) ($invoice?->subtotal_after_item_discount ?? max($subtotalBeforeDiscount - $itemDiscountAmount, 0));
        $overallDiscountAmount = (float) ($invoice?->overall_discount_amount ?? max((float) ($invoice?->discount_amount ?? 0) - $itemDiscountAmount, 0));
        $taxableAmount = (float) ($invoice?->taxable_amount ?? max($subtotalAfterItemDiscount - $overallDiscountAmount, 0));
        $taxAmount = (float) ($invoice?->tax_amount ?? $pageData['taxAmount'] ?? 0);
        $grandTotal = (float) ($invoice?->grand_total ?? $pageData['grandTotal'] ?? ($taxableAmount + $taxAmount));
        $paymentModeLabel = ucfirst((string) ($invoice?->payment_mode ?? 'paid'));
        $paymentMethodLabel = match ((string) ($invoice?->payment_method ?? '')) {
            'cash' => 'Cash',
            'card' => 'Card',
            'fonepay_dynamic', 'static_qr' => 'Fonepay',
            'nepal_pay' => 'Nepal Pay',
            'bank_transfer' => 'Bank Transfer',
            default => '',
        };
        $customerName = trim((string) ($invoice?->customer_name_snapshot ?? 'Cash Customer')) ?: 'Cash Customer';
        $notesSnapshot = trim((string) ($invoice?->notes_snapshot ?? ''));
        $rawItemsById = $order->items->keyBy('id');
        $amountInWords = $this->amountToWords($grandTotal, 'Rupee', 'Rupees');
        $pdfOrderItems = collect($pageData['orderItems'])
            ->filter(function (object $item) use ($excludeRejected) {
                if (!$excludeRejected) {
                    return true;
                }

                return !((bool) ($item->is_rejected ?? false) || strtolower((string) ($item->status ?? '')) === 'rejected');
            })
            ->map(function (object $item) use ($rawItemsById) {
                $groupIds = collect($item->ids_group ?? [$item->id])->map(fn ($id) => (int) $id)->filter()->all();
                $itemDiscount = collect($groupIds)
                    ->sum(function (int $itemId) use ($rawItemsById) {
                        return (float) ($rawItemsById->get($itemId)?->applied_discount ?? 0);
                    });
                $baseAmount = (float) ($item->price ?? 0) * (int) ($item->quantity ?? 0);
                $addonsCollection = collect($item->addons ?? [])->filter(fn ($addon) => is_array($addon))->map(function (array $addon) {
                    $addonQty = max((int) ($addon['quantity'] ?? 1), 1);
                    $addonPrice = max((float) ($addon['price'] ?? 0), 0);
                    $addonDiscount = max((float) ($addon['discount'] ?? $addon['applied_discount'] ?? 0), 0);
                    $addonBaseAmount = $addonPrice * $addonQty;

                    return [
                        'id' => (int) ($addon['id'] ?? $addon['menu_item_addon_id'] ?? 0),
                        'name' => (string) ($addon['addon_name'] ?? $addon['name'] ?? ''),
                        'price' => $addonPrice,
                        'quantity' => $addonQty,
                        'discount' => $addonDiscount,
                        'applied_discount' => $addonDiscount,
                        'base_amount' => $addonBaseAmount,
                        'line_total_before_discount' => $addonBaseAmount,
                        'total' => max($addonBaseAmount - $addonDiscount, 0),
                    ];
                })->values();
                $isRejected = (bool) ($item->is_rejected ?? false) || strtolower((string) ($item->status ?? '')) === 'rejected';
                $addons = $isRejected ? [] : $addonsCollection->all();
                $displayAmount = $isRejected ? 0 : max($baseAmount - $itemDiscount, 0);

                return [
                    'name' => (string) $item->item_name,
                    'qty' => (int) $item->quantity,
                    'rate' => (float) $item->price,
                    'base_amount' => $baseAmount,
                    'display_amount' => $displayAmount,
                    'amount' => $displayAmount,
                    'discount' => $itemDiscount,
                    'addons' => $addons,
                    'notes' => (string) ($item->notes ?? ''),
                    'status' => $isRejected ? 'rejected' : (string) ($item->status ?? 'new'),
                    'is_rejected' => $isRejected,
                    'rejection_reason' => (string) ($item->rejection_reason ?? ''),
                ];
            })
            ->values()
            ->all();

        $pdf = Pdf::loadView('core.pdf.bill-summary', [
            'summary' => [
                'table' => $pageData['tableNumber'],
                'order_id' => $pageData['orderNumber'],
                'invoice_number' => $invoiceNumber,
                'customer_name' => $customerName,
                'subtotal' => $subtotalBeforeDiscount,
                'item_discount_amount' => $itemDiscountAmount,
                'subtotal_after_item_discount' => $subtotalAfterItemDiscount,
                'discount_amount' => $overallDiscountAmount,
                'taxable_amount' => $taxableAmount,
                'tax' => $taxAmount,
                'grand_total' => $grandTotal,
                'payment_mode' => $paymentModeLabel,
                'payment_method' => $paymentMethodLabel,
                'tender_amount' => (float) ($invoice?->tender_amount ?? 0),
                'change_amount' => (float) ($invoice?->change_amount ?? 0),
                'paid_amount' => (float) ($invoice?->paid_amount ?? 0),
                'due_amount' => (float) ($invoice?->due_amount ?? 0),
                'tax_label' => $taxLabel,
                'tax_rate_percent' => $taxRatePercent,
                'invoice_date' => optional($order->created_at)->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A'),
                'restaurant_name' => (string) ($tenant?->company_name ?? 'Restaurant'),
                'branch_name' => (string) ($branch?->branch_name ?? ''),
                'branch_address' => trim((string) ($branch?->full_address ?: implode(', ', array_filter([
                    $branch?->city,
                    $branch?->state,
                    $branch?->pincode,
                ])))),
                'branch_contact' => (string) ($branch?->contact_number ?? ''),
                'branch_email' => (string) ($branch?->branch_email ?? ''),
                'tax_registration' => 'N/A',
                'amount_in_words' => $amountInWords,
                'payment_status' => strtoupper((string) ($invoice?->status ?? 'PAID')),
                'invoice_date_only' => optional($order->created_at)->format('d M Y') ?? now()->format('d M Y'),
                'invoice_time' => optional($order->created_at)->format('h:i A') ?? now()->format('h:i A'),
                'notes_snapshot' => $notesSnapshot,
            ],
            'orderItems' => $pdfOrderItems,
        ])->setPaper($this->thermalReceiptPaper(count($pdfOrderItems)), 'portrait')->setOption('defaultFont', 'DejaVu Sans');

        $fileName = 'bill-summary-' . $pageData['orderNumber'] . '.pdf';

        return $request->boolean('print')
            ? $pdf->stream($fileName)
            : $pdf->download($fileName);
    }

    private function numberToWords(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

        $ones = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
        ];
        $tens = [
            0 => '', 1 => '', 2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety',
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
                $words[] = $count > 99 ? $this->numberToWords($count) : $convertUnderHundred($count);
                $words[] = $unit['name'];
            }
        }

        if ($remaining > 0) {
            $words[] = $convertUnderHundred($remaining);
        }

        return trim(implode(' ', array_filter($words)));
    }

    private function amountToWords(float $amount, string $currencySingular, string $currencyPlural, string $minorSingular = 'Paisa', string $minorPlural = 'Paise'): string
    {
        $amount = round(max($amount, 0), 2);
        $rupees = (int) floor($amount);
        $paise = (int) round(($amount - $rupees) * 100);

        if ($paise === 100) {
            $rupees++;
            $paise = 0;
        }

        $currencyLabel = $rupees === 1 ? $currencySingular : $currencyPlural;
        $minorLabel = $paise === 1 ? $minorSingular : $minorPlural;
        $rupeeWords = $this->numberToWords($rupees);

        if ($rupees === 0 && $paise === 0) {
            return 'Zero ' . $currencyLabel . ' Only';
        }

        if ($paise > 0) {
            return trim($rupeeWords . ' ' . $currencyLabel . ' and ' . $this->numberToWords($paise) . ' ' . $minorLabel . ' Only');
        }

        return trim($rupeeWords . ' ' . $currencyLabel . ' Only');
    }

    private function renderThankYou(Table $table, ?Order $order, Request $request, array $paymentResult = [])
    {
        $branch = $table->branch;
        $tenant = $branch?->tenant ?? Tenant::query()->find($table->tenant_id);
        $publicMenuTheme = strtolower((string) ($branch?->branch_menu_theme ?? 'dark')) === 'light'
            ? 'light'
            : 'dark';

        return view('modules.public-menu.thank-you', [
            'tenant' => $tenant,
            'table' => $table,
            'order' => $order,
            'tableNumber' => (string) ($table->table_number ?? ''),
            'qrToken' => (string) ($table->qr_token ?? ''),
            'publicMenuTheme' => $publicMenuTheme,
            'paymentResult' => array_merge([
                'status' => 'completed',
                'payment_mode' => 'Online',
                'transaction_id' => '',
                'message' => 'Thank you for visiting.',
            ], $paymentResult),
        ]);
    }

    private function resolveInvoiceContext(string $qr_token): array
    {
        $table = Table::query()
            ->with('branch')
            ->where('qr_token', $qr_token)
            ->where('is_active', true)
            ->firstOrFail();

        $order = Order::query()
            ->where('table_id', $table->id)
            ->with(['items.orderItemAddons.masterAddon', 'invoice'])
            ->latest()
            ->first();

        if (!$order) {
            [$table, $order] = $this->orderStatusService->resolveContext($qr_token);
            $order->loadMissing(['items.orderItemAddons.masterAddon', 'invoice']);
        }

        return [$table, $order];
    }

    private function thermalReceiptPaper(int $itemCount = 0): array
    {
        $widthMm = 80;
        $heightMm = max(260, 180 + ($itemCount * 12));
        $mmToPt = 72 / 25.4;

        return [
            0,
            0,
            $widthMm * $mmToPt,
            $heightMm * $mmToPt,
        ];
    }

}
