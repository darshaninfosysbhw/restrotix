<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use App\Models\OrderPayment;
use App\Models\Table;
use App\Models\TableServiceRequest;
use App\Events\TableTransferRequestUpdated;
use App\Support\InvoiceNumberGenerator;
use App\Services\Admin\Billing\BillingDraftService;
use App\Services\PublicMenu\TableAccessSessionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillingCheckoutController extends Controller
{
    public function __construct(
        protected TableAccessSessionService $tableAccessSessionService,
        protected BillingDraftService $billingDraftService
    ) {}

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        $validated = $request->validate([
            'table_id' => 'nullable|integer|exists:tables,id',
            'table_number' => 'required|string|max:50',
            'qr_token' => 'nullable|string|max:255',
            'payment_mode' => 'required|in:paid,unpaid,partial',
            'payment_method' => 'nullable|string|max:50',
            'item_count' => 'nullable|integer|min:0',
            'total_qty' => 'nullable|integer|min:0',
            'subtotal_before_discount' => 'required|numeric|min:0',
            'item_discount_amount' => 'required|numeric|min:0',
            'subtotal_after_item_discount' => 'required|numeric|min:0',
            'overall_discount_percent' => 'nullable|numeric|min:0',
            'overall_discount_amount' => 'required|numeric|min:0',
            'tax_setting' => 'nullable|string|max:20',
            'tax_rate_snapshot' => 'nullable|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'tender_amount' => 'nullable|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'due_amount' => 'nullable|numeric|min:0',
            'customer_name_snapshot' => 'nullable|string|max:255',
            'notes_snapshot' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:order_items,id',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.base_amount' => 'nullable|numeric|min:0',
            'items.*.addon_total' => 'nullable|numeric|min:0',
            'items.*.line_total_before_discount' => 'nullable|numeric|min:0',
            'items.*.total' => 'nullable|numeric|min:0',
            'items.*.status' => 'nullable|string|max:30',
            'items.*.is_rejected' => 'nullable|boolean',
            'items.*.rejection_reason' => 'nullable|string|max:1000',
            'items.*.addons' => 'nullable|array',
            'items.*.addons.*.id' => 'nullable|integer',
            'items.*.addons.*.name' => 'nullable|string|max:255',
            'items.*.addons.*.addon_name' => 'nullable|string|max:255',
            'items.*.addons.*.menu_item_addon_id' => 'nullable|integer',
            'items.*.addons.*.price' => 'nullable|numeric|min:0',
            'items.*.addons.*.quantity' => 'nullable|integer|min:1',
            'items.*.addons.*.discount' => 'nullable|numeric|min:0',
            'items.*.addons.*.applied_discount' => 'nullable|numeric|min:0',
            'transaction_ref' => 'nullable|string|max:100',
        ]);

        $tableId = (int) ($validated['table_id'] ?? 0);
        $tableNumber = trim((string) $validated['table_number']);
        $paymentMode = strtolower((string) $validated['payment_mode']);
        $paymentMethod = trim(strtolower((string) ($validated['payment_method'] ?? '')));
        $allowedPaymentMethods = ['cash', 'card', 'fonepay_dynamic', 'static_qr', 'nepal_pay', 'bank_transfer'];

        if ($paymentMode === 'paid' && $paymentMethod === '') {
            return response()->json([
                'success' => false,
                'message' => 'Please select payment method before confirming the bill.',
            ], 422);
        }

        if ($paymentMethod !== '' && !in_array($paymentMethod, $allowedPaymentMethods, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Unsupported payment method.',
            ], 422);
        }

        $orderQuery = Order::query()
            ->where('tenant_id', (int) $user->tenant_id)
            ->with(['items.orderItemAddons', 'invoice', 'creator']);

        if ($tableId > 0) {
            $orderQuery->where('table_id', $tableId);
        } else {
            $orderQuery->where('table_number', $tableNumber);
        }

        $order = $orderQuery
            ->where('status', 'running')
            ->latest('id')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Running order not found for this table.',
            ], 404);
        }

        $itemsPayload = collect($validated['items'])
            ->map(fn(array $item) => $this->normalizeBillingItem($item))
            ->filter(fn(array $item) => $item['id'] > 0)
            ->values();

        if ($itemsPayload->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No billing items were provided.',
            ], 422);
        }

        $itemCount = (int) $itemsPayload->sum(function (array $item) {
            return 1 + count($item['addons'] ?? []);
        });
        $totalQty = (int) $itemsPayload->sum(function (array $item) {
            $addonQty = collect($item['addons'] ?? [])->sum(function ($addon) {
                return (int) ($addon['quantity'] ?? 0);
            });

            return (int) ($item['qty'] ?? 0) + (int) $addonQty;
        });
        $subtotalBeforeDiscount = (float) $itemsPayload->sum('line_total_before_discount');
        $itemDiscountAmount = (float) $itemsPayload->sum(function (array $item) {
            $itemDiscount = max((float) ($item['discount'] ?? 0), 0);
            $addonDiscount = (float) ($item['addon_discount_total'] ?? collect($item['addons'] ?? [])->sum(function ($addon) {
                return max((float) ($addon['discount'] ?? $addon['applied_discount'] ?? 0), 0);
            }));

            return $itemDiscount + $addonDiscount;
        });
        $subtotalAfterItemDiscount = max($subtotalBeforeDiscount - $itemDiscountAmount, 0);
        $overallDiscountAmount = max((float) ($validated['overall_discount_amount'] ?? 0), 0);
        $overallDiscountPercent = $subtotalBeforeDiscount > 0
            ? ($overallDiscountAmount / $subtotalBeforeDiscount) * 100
            : (float) ($validated['overall_discount_percent'] ?? 0);
        $table = $order->table_id ? Table::query()->with('branch')->find($order->table_id) : null;
        $taxContext = $this->resolveBillingTaxContext($table, $validated);
        $taxTotals = $this->calculateBillingTaxTotals(
            max($subtotalAfterItemDiscount - $overallDiscountAmount, 0),
            $taxContext
        );
        $taxableAmount = (float) $taxTotals['taxable_amount'];
        $taxSetting = (string) $taxTotals['tax_setting'];
        $taxRateSnapshot = (float) $taxTotals['tax_rate_percent'];
        $taxAmount = (float) $taxTotals['tax_amount'];
        $grandTotal = (float) $taxTotals['grand_total'];
        $tenderAmount = max((float) ($validated['tender_amount'] ?? 0), 0);

        if ($paymentMode === 'paid' && $tenderAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter tender amount before confirming the bill.',
            ], 422);
        }

        $paidAmount = max((float) ($validated['paid_amount'] ?? ($paymentMode === 'unpaid' ? 0 : $tenderAmount)), 0);
        $dueAmount = max((float) ($validated['due_amount'] ?? max($grandTotal - $paidAmount, 0)), 0);
        $changeAmount = max((float) ($validated['change_amount'] ?? max($tenderAmount - $grandTotal, 0)), 0);
        $customerName = trim((string) ($validated['customer_name_snapshot'] ?? 'Cash Customer'));
        $notesSnapshot = trim((string) ($validated['notes_snapshot'] ?? ''));
        $paymentStatus = $paidAmount <= 0
            ? 'unpaid'
            : ($dueAmount > 0 ? 'partially_paid' : 'paid');

        $result = DB::transaction(function () use (
            $validated,
            $order,
            $itemsPayload,
            $itemCount,
            $totalQty,
            $subtotalBeforeDiscount,
            $itemDiscountAmount,
            $subtotalAfterItemDiscount,
            $overallDiscountPercent,
            $overallDiscountAmount,
            $taxableAmount,
            $taxSetting,
            $taxRateSnapshot,
            $taxAmount,
            $grandTotal,
            $tenderAmount,
            $paidAmount,
            $dueAmount,
            $changeAmount,
            $customerName,
            $notesSnapshot,
            $paymentMode,
            $paymentMethod,
            $paymentStatus,
            $user,
            $table
        ) {
            $invoice = OrderInvoice::query()->firstOrNew([
                'tenant_id' => (int) $order->tenant_id,
                'branch_id' => (int) $order->branch_id,
                'order_id' => (int) $order->id,
            ]);

            if (!$invoice->exists) {
                $invoice->invoice_number = $this->generateInvoiceNumber($order);
            }

            $invoice->fill([
                'item_count' => $itemCount,
                'total_qty' => $totalQty,
                'subtotal_before_discount' => $subtotalBeforeDiscount,
                'subtotal' => $taxableAmount,
                'item_discount_amount' => $itemDiscountAmount,
                'subtotal_after_item_discount' => $subtotalAfterItemDiscount,
                'overall_discount_percent' => $overallDiscountPercent,
                'discount_amount' => $itemDiscountAmount + $overallDiscountAmount,
                'overall_discount_amount' => $overallDiscountAmount,
                'taxable_amount' => $taxableAmount,
                'tax_setting' => $taxSetting,
                'tax_rate' => $taxRateSnapshot,
                'tax_rate_snapshot' => $taxRateSnapshot,
                'tax_amount' => $taxAmount,
                'payment_mode' => $paymentMode,
                'payment_method' => $paymentMethod ?: null,
                'tender_amount' => $tenderAmount,
                'change_amount' => $changeAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'customer_name_snapshot' => $customerName,
                'table_number_snapshot' => (string) ($validated['table_number'] ?? $order->table_number ?? ''),
                'cashier_user_id' => (int) $user->id,
                'notes_snapshot' => $notesSnapshot ?: null,
                'grand_total' => $grandTotal,
                'status' => $paymentStatus,
            ]);
            $invoice->save();

            foreach ($itemsPayload as $itemData) {
                $orderItem = OrderItem::query()
                    ->where('order_id', $order->id)
                    ->whereKey($itemData['id'])
                    ->first();

                if (!$orderItem) {
                    continue;
                }

                $orderItem->fill([
                    'invoice_id' => $invoice->id,
                    'applied_discount' => $itemData['discount'],
                    'total' => $itemData['total'],
                ]);
                $orderItem->save();

                $addonsPayload = collect($itemData['addons'] ?? [])
                    ->filter(fn($addon) => is_array($addon))
                    ->values();

                if ($addonsPayload->isNotEmpty()) {
                    $orderItem->loadMissing('orderItemAddons');

                    foreach ($addonsPayload as $addonData) {
                        $addonDiscount = max((float) ($addonData['discount'] ?? $addonData['applied_discount'] ?? 0), 0);
                        $addonRowId = (int) ($addonData['id'] ?? 0);
                        $addonMenuItemId = (int) ($addonData['menu_item_addon_id'] ?? 0);

                        $orderItemAddon = $orderItem->orderItemAddons->first(function (OrderItemAddon $addon) use ($addonRowId, $addonMenuItemId) {
                            if ($addonRowId > 0 && (int) $addon->id === $addonRowId) {
                                return true;
                            }

                            return $addonMenuItemId > 0 && (int) $addon->menu_item_addon_id === $addonMenuItemId;
                        });

                        if (!$orderItemAddon && $addonMenuItemId > 0) {
                            $orderItemAddon = OrderItemAddon::query()
                                ->where('order_item_id', $orderItem->id)
                                ->where('menu_item_addon_id', $addonMenuItemId)
                                ->first();
                        }

                        if (!$orderItemAddon && $addonRowId > 0) {
                            $orderItemAddon = OrderItemAddon::query()
                                ->whereKey($addonRowId)
                                ->where('order_item_id', $orderItem->id)
                                ->first();
                        }

                        if ($orderItemAddon) {
                            $orderItemAddon->fill([
                                'applied_discount' => $addonDiscount,
                            ]);
                            $orderItemAddon->save();
                        }
                    }
                }
            }

            $order->update([
                'subtotal' => $subtotalBeforeDiscount,
                'discount_amount' => $itemDiscountAmount + $overallDiscountAmount,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'payment_status' => $paymentStatus === 'paid' ? 'paid' : ($paymentStatus === 'partially_paid' ? 'partial' : 'pending'),
                'status' => 'completed',
            ]);

            if ($order->table_id) {
                $tableTransfers = TableServiceRequest::query()
                    ->where('table_id', $order->table_id)
                    ->where('type', 'table_transfer')
                    ->whereIn('status', ['pending', 'accepted'])
                    ->with(['table', 'handledByWaiter', 'targetWaiter'])
                    ->get();

                foreach ($tableTransfers as $tableTransfer) {
                    $tableTransfer->update([
                        'status' => 'cancelled',
                        'completed_at' => now(),
                    ]);
                    broadcast(new TableTransferRequestUpdated([
                        'id' => $tableTransfer->id,
                        'branch_id' => $tableTransfer->branch_id,
                        'table_id' => $tableTransfer->table_id,
                        'table_number' => $tableTransfer->table?->table_number,
                        'from_waiter' => $tableTransfer->handledByWaiter?->name ?? 'Unknown waiter',
                        'handled_by_waiter_id' => $tableTransfer->handled_by_waiter_id,
                        'target_waiter_id' => $tableTransfer->target_waiter_id,
                        'target_waiter' => $tableTransfer->targetWaiter?->name,
                        'notes' => $tableTransfer->notes,
                        'status' => 'cancelled',
                    ]));
                }

                Table::query()
                    ->whereKey($order->table_id)
                    ->update([
                        'status' => 'available',
                        'is_calling_waiter' => false,
                        'is_bill_requested' => false,
                    ]);

                $releasedTable = $table instanceof Table
                    ? $table
                    : Table::query()->with('branch')->find($order->table_id);

                if ($releasedTable instanceof Table) {
                    $this->tableAccessSessionService->releaseTable($releasedTable, 2);
                    $this->billingDraftService->clearForTable($releasedTable);
                }
            }

            $paymentRecord = null;
            if ($paidAmount > 0) {
                $transactionRef = trim((string) ($validated['transaction_ref'] ?? ''));
                if ($transactionRef === '') {
                    $transactionRef = 'POS-' . $invoice->invoice_number;
                }

                $paymentRecord = OrderPayment::query()->updateOrCreate(
                    [
                        'invoice_id' => (int) $invoice->id,
                        'transaction_ref' => $transactionRef,
                    ],
                    [
                        'tenant_id' => (int) $order->tenant_id,
                        'payment_mode' => $paymentMode,
                        'payment_method' => $paymentMethod ?: 'cash',
                        'amount' => $paidAmount,
                        'tender_amount' => $tenderAmount,
                        'change_amount' => $changeAmount,
                        'gateway_response' => [
                            'source' => 'pos_checkout',
                            'table_number' => $order->table_number,
                            'order_id' => $order->id,
                        ],
                        'status' => 'success',
                        'verified_by_user_id' => (int) $user->id,
                    ]
                );
            }

            return [
                'invoice' => $invoice->fresh(),
                'payment' => $paymentRecord ? $paymentRecord->fresh() : null,
                'order' => $order->fresh(['items', 'invoice']),
            ];
        });

        $table = $table ?? ($result['order']->table_id ? Table::query()->find($result['order']->table_id) : null);
        $printUrl = $table?->qr_token
            ? route('public.order.status.pdf', ['qr_token' => $table->qr_token, 'print' => 1, 'exclude_rejected' => 1], false)
            : null;

        return response()->json([
            'success' => true,
            'message' => 'Billing saved successfully.',
            'data' => [
                'invoice_id' => (int) $result['invoice']->id,
                'invoice_number' => (string) $result['invoice']->invoice_number,
                'order_id' => (int) $result['order']->id,
                'payment_status' => (string) $result['invoice']->status,
                'print_url' => $printUrl,
            ],
        ]);
    }

    public function estimatePdf(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        $validated = $request->validate([
            'table_id' => 'nullable|integer|exists:tables,id',
            'table_number' => 'required|string|max:50',
            'qr_token' => 'nullable|string|max:255',
            'payment_mode' => 'required|in:paid,unpaid,partial',
            'payment_method' => 'nullable|string|max:50',
            'item_count' => 'nullable|integer|min:0',
            'total_qty' => 'nullable|integer|min:0',
            'subtotal_before_discount' => 'required|numeric|min:0',
            'item_discount_amount' => 'required|numeric|min:0',
            'subtotal_after_item_discount' => 'required|numeric|min:0',
            'overall_discount_percent' => 'nullable|numeric|min:0',
            'overall_discount_amount' => 'required|numeric|min:0',
            'tax_setting' => 'nullable|string|max:20',
            'tax_rate_snapshot' => 'nullable|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'tender_amount' => 'nullable|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'due_amount' => 'nullable|numeric|min:0',
            'customer_name_snapshot' => 'nullable|string|max:255',
            'notes_snapshot' => 'nullable|string',
            'kot_no' => 'nullable|string|max:120',
            'assign_to' => 'nullable|string|max:255',
            'billed_by' => 'nullable|string|max:255',
            'service_duration' => 'nullable|string|max:50',
            'output_mode' => 'nullable|in:download,print',
            'items_json' => 'required|string',
        ]);

        $table = null;
        if (!empty($validated['table_id'])) {
            $table = Table::query()
                ->with('branch.tenant')
                ->where('tenant_id', (int) $user->tenant_id)
                ->when($user->role === 'waiter' && $user->branch_id, fn($query) => $query->where('branch_id', $user->branch_id))
                ->find((int) $validated['table_id']);
        }

        if (!$table && !empty($validated['qr_token'])) {
            $table = Table::query()
                ->with('branch.tenant')
                ->where('tenant_id', (int) $user->tenant_id)
                ->when($user->role === 'waiter' && $user->branch_id, fn($query) => $query->where('branch_id', $user->branch_id))
                ->where('qr_token', (string) $validated['qr_token'])
                ->first();
        }

        if (!$table) {
            $table = Table::query()
                ->with('branch.tenant')
                ->where('tenant_id', (int) $user->tenant_id)
                ->when($user->role === 'waiter' && $user->branch_id, fn($query) => $query->where('branch_id', $user->branch_id))
                ->where('table_number', (string) $validated['table_number'])
                ->first();
        }

        abort_unless($table, 404, 'Table not found.');

        $itemsPayload = collect(json_decode((string) $validated['items_json'], true) ?: [])
            ->map(fn(array $item) => $this->normalizeBillingItem($item))
            ->filter(fn(array $item) => $item['name'] !== '')
            ->values();

        if ($itemsPayload->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No estimate items were provided.',
            ], 422);
        }

        $subtotalBeforeDiscount = (float) $itemsPayload->sum('line_total_before_discount');
        $itemDiscountAmount = (float) $itemsPayload->sum(function (array $item) {
            $itemDiscount = max((float) ($item['discount'] ?? 0), 0);
            $addonDiscount = (float) ($item['addon_discount_total'] ?? collect($item['addons'] ?? [])->sum(function ($addon) {
                return max((float) ($addon['discount'] ?? $addon['applied_discount'] ?? 0), 0);
            }));

            return $itemDiscount + $addonDiscount;
        });
        $subtotalAfterItemDiscount = max($subtotalBeforeDiscount - $itemDiscountAmount, 0);
        $overallDiscountAmount = max((float) $validated['overall_discount_amount'], 0);
        $overallDiscountPercent = $subtotalBeforeDiscount > 0
            ? ($overallDiscountAmount / $subtotalBeforeDiscount) * 100
            : max((float) ($validated['overall_discount_percent'] ?? 0), 0);
        $tenderAmount = max((float) ($validated['tender_amount'] ?? 0), 0);
        $paidAmount = max((float) ($validated['paid_amount'] ?? 0), 0);
        $dueAmount = max((float) ($validated['due_amount'] ?? 0), 0);
        $paymentMode = strtolower((string) $validated['payment_mode']);
        $paymentMethod = strtolower(trim((string) ($validated['payment_method'] ?? '')));
        $notesSnapshot = trim((string) ($validated['notes_snapshot'] ?? ''));
        $kotNo = trim((string) ($validated['kot_no'] ?? ''));
        $assignTo = trim((string) ($validated['assign_to'] ?? ''));
        $billedBy = trim((string) ($validated['billed_by'] ?? ''));
        $serviceDuration = trim((string) ($validated['service_duration'] ?? ''));
        $outputMode = strtolower((string) ($validated['output_mode'] ?? 'download'));

        $paymentModeLabel = match ($paymentMode) {
            'paid' => 'Paid',
            'partial' => 'Partial',
            default => 'Unpaid / Credit',
        };

        $paymentMethodLabel = match ($paymentMethod) {
            'cash' => 'Cash',
            'card' => 'Card',
            'fonepay_dynamic', 'static_qr' => 'Fonepay',
            'nepal_pay' => 'Nepal Pay',
            'bank_transfer' => 'Bank Transfer',
            default => '',
        };
        $paymentModeDisplay = $paymentMethodLabel !== ''
            ? $paymentMethodLabel
            : ($paymentMode === 'partial' ? 'Partial' : 'Unpaid / Credit');
        $paymentStatusLabel = $paymentMode === 'paid' && $paymentMethodLabel !== ''
            ? 'PAID'
            : ($paymentMode === 'partial' ? 'PARTIAL' : 'UNPAID');

        $taxContext = $this->resolveBillingTaxContext($table, $validated);
        $taxTotals = $this->calculateBillingTaxTotals(
            max($subtotalAfterItemDiscount - $overallDiscountAmount, 0),
            $taxContext
        );
        $taxSetting = (string) $taxTotals['tax_setting'];
        $taxRatePercent = (float) $taxTotals['tax_rate_percent'];
        $taxAmount = (float) $taxTotals['tax_amount'];
        $taxableAmount = (float) $taxTotals['taxable_amount'];
        $grandTotal = (float) $taxTotals['grand_total'];
        $changeAmount = max((float) ($validated['change_amount'] ?? max($tenderAmount - $grandTotal, 0)), 0);
        $restaurantName = (string) ($table?->branch?->tenant?->company_name ?? 'Restaurant');
        $branchName = (string) ($table?->branch?->branch_name ?? '');
        $branchAddress = trim((string) ($table?->branch?->full_address ?: implode(', ', array_filter([
            $table?->branch?->city,
            $table?->branch?->state,
            $table?->branch?->pincode,
        ]))));
        $branchContact = (string) ($table?->branch?->contact_number ?? '');
        $branchEmail = (string) ($table?->branch?->branch_email ?? '');

        $summary = [
            'invoice_number' => '##',
            'invoice_date' => now()->format('d M Y, h:i A'),
            'table' => (string) ($validated['table_number'] ?? $table?->table_number ?? 'N/A'),
            'customer_name' => trim((string) ($validated['customer_name_snapshot'] ?? 'Cash Customer')) ?: 'Cash Customer',
            'subtotal' => $subtotalBeforeDiscount,
            'item_discount_amount' => $itemDiscountAmount,
            'subtotal_after_item_discount' => $subtotalAfterItemDiscount,
            'discount_percent' => $overallDiscountPercent,
            'discount_amount' => $overallDiscountAmount,
            'tax_amount' => $taxAmount,
            'tax_rate_percent' => $taxRatePercent,
            'tax_setting' => $taxSetting,
            'tax_label' => $taxTotals['tax_label'],
            'taxable_amount' => $taxableAmount,
            'grand_total' => $grandTotal,
            'payment_mode' => $paymentModeDisplay,
            'payment_method' => $paymentMethodLabel,
            'tender_amount' => $tenderAmount,
            'change_amount' => $changeAmount,
            'paid_amount' => $paidAmount,
            'due_amount' => $dueAmount,
            'amount_in_words' => $this->amountToWords($grandTotal, 'Nepalese Rupee', 'Nepalese Rupees'),
            'restaurant_name' => $restaurantName,
            'branch_name' => $branchName,
            'branch_address' => $branchAddress,
            'branch_contact' => $branchContact,
            'branch_email' => $branchEmail,
            'tax_registration' => 'N/A',
            'payment_status' => $paymentStatusLabel,
            'notes_snapshot' => $notesSnapshot,
            'kot_no' => $kotNo,
            'assign_to' => $assignTo,
            'billed_by' => $billedBy,
            'service_duration' => $serviceDuration,
            'note' => 'This is not a Tax Invoice!',
        ];

        $displayItems = $this->groupBillingDisplayItems($itemsPayload->all());

        $pdf = Pdf::loadView('core.pdf.estimate-summary', [
            'summary' => $summary,
            'orderItems' => $displayItems,
        ])->setPaper($this->thermalReceiptPaper((int) collect($displayItems)->sum(fn(array $item) => 1 + count($item['addons'] ?? []))), 'portrait')->setOption('defaultFont', 'DejaVu Sans');

        $safeTableNumber = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) ($validated['table_number'] ?? 'table'));

        $fileName = 'estimate-invoice-' . $safeTableNumber . '.pdf';

        return $outputMode === 'print'
            ? $pdf->stream($fileName)
            : $pdf->download($fileName);
    }

    private function normalizeBillingAddon(array $addon): array
    {
        $quantity = max((int) ($addon['quantity'] ?? 1), 1);
        $price = (float) ($addon['price'] ?? $addon['rate'] ?? 0);
        if ($price <= 0) {
            $masterPrice = max((float) data_get($addon, 'masterAddon.price', 0), 0);
            $menuItemAddonPrice = max((float) ($addon['menu_item_addon_price'] ?? 0), 0);
            $price = $masterPrice > 0 ? $masterPrice : $menuItemAddonPrice;
        }
        $name = trim((string) ($addon['name'] ?? $addon['addon_name'] ?? 'Addon'));
        $name = preg_replace('/^[↳↲]+\s*/u', '', $name) ?? $name;
        $discount = max((float) ($addon['discount'] ?? $addon['applied_discount'] ?? 0), 0);
        $baseAmount = $price * $quantity;
        $total = max($baseAmount - $discount, 0);

        return [
            'id' => (int) ($addon['id'] ?? $addon['menu_item_addon_id'] ?? 0),
            'menu_item_addon_id' => (int) ($addon['menu_item_addon_id'] ?? 0),
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'discount' => $discount,
            'applied_discount' => $discount,
            'base_amount' => $baseAmount,
            'line_total_before_discount' => $baseAmount,
            'total' => $total,
            'amount' => $total,
        ];
    }

    private function normalizeBillingItem(array $item): array
    {
        $qty = max((int) ($item['qty'] ?? $item['quantity'] ?? 0), 0);
        $status = strtolower(trim((string) ($item['status'] ?? '')));
        $isRejected = (bool) ($item['is_rejected'] ?? false) || in_array($status, ['rejected', 'cancelled'], true);
        $rejectionReason = trim((string) ($item['rejection_reason'] ?? ''));
        $originalRate = max((float) ($item['rate'] ?? 0), 0);
        $rate = $isRejected ? 0.0 : $originalRate;
        $discount = max((float) ($item['discount'] ?? 0), 0);

        $addonSource = $item['addons'] ?? $item['order_item_addons'] ?? $item['orderItemAddons'] ?? [];
        $addons = collect($addonSource)
            ->filter(fn($addon) => is_array($addon))
            ->map(fn(array $addon) => $this->normalizeBillingAddon($addon))
            ->filter(fn(array $addon) => trim((string) ($addon['name'] ?? '')) !== '')
            ->values();

        $addonTotal = $isRejected ? 0.0 : (float) $addons->sum(function (array $addon) {
            return (float) ($addon['line_total_before_discount'] ?? $addon['base_amount'] ?? $addon['total'] ?? 0);
        });
        $addonDiscountTotal = $isRejected ? 0.0 : (float) ($item['addon_discount_total'] ?? $addons->sum(function (array $addon) {
            return max((float) ($addon['discount'] ?? $addon['applied_discount'] ?? 0), 0);
        }));
        $baseAmount = $isRejected ? 0.0 : max((float) ($item['base_amount'] ?? ($qty * $rate)), 0);
        $lineTotalBeforeDiscount = $isRejected
            ? 0.0
            : max((float) ($item['line_total_before_discount'] ?? ($baseAmount + $addonTotal)), 0);
        $lineTotal = $isRejected ? 0.0 : max((float) ($item['total'] ?? $lineTotalBeforeDiscount - $discount - $addonDiscountTotal), 0);
        $name = trim((string) ($item['name'] ?? $item['item_name'] ?? 'Item'));

        return [
            'id' => (int) ($item['id'] ?? 0),
            'name' => $name,
            'status' => $isRejected ? 'rejected' : ($status !== '' ? $status : 'new'),
            'is_rejected' => $isRejected,
            'rejection_reason' => $rejectionReason,
            'qty' => $qty,
            'quantity' => $qty,
            'original_rate' => $originalRate,
            'rate' => $rate,
            'discount' => $isRejected ? 0.0 : $discount,
            'addons' => $addons->all(),
            'addon_total' => $addonTotal,
            'addon_discount_total' => $addonDiscountTotal,
            'base_amount' => $baseAmount,
            'line_total_before_discount' => $lineTotalBeforeDiscount,
            'line_total' => $lineTotal,
            'total' => $lineTotal,
            'amount' => $lineTotal,
        ];
    }

    private function normalizeBillingGroupKey(mixed $value): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim((string) $value));

        return strtolower($normalized === null ? '' : $normalized);
    }

    private function billingAddonDisplaySignature(array $addon): string
    {
        $addonId = (int) ($addon['id'] ?? $addon['menu_item_addon_id'] ?? 0);
        $addonName = $this->normalizeBillingGroupKey($addon['name'] ?? $addon['addon_name'] ?? 'Addon');
        $addonPrice = number_format((float) ($addon['price'] ?? 0), 4, '.', '');

        return implode('::', [
            $addonName,
            (string) $addonId,
            $addonPrice,
        ]);
    }

    private function mergeBillingDisplayAddons(array $existingAddons, array $incomingAddons): array
    {
        $groupedAddons = [];
        $addonMap = [];

        foreach (array_merge($existingAddons, $incomingAddons) as $addon) {
            $normalizedAddon = $this->normalizeBillingAddon((array) $addon);
            $signature = $this->billingAddonDisplaySignature($normalizedAddon);

            if (!isset($addonMap[$signature])) {
                $groupedAddons[] = $normalizedAddon;
                $addonMap[$signature] = count($groupedAddons) - 1;
                continue;
            }

            $bucketIndex = $addonMap[$signature];
            $bucket = $groupedAddons[$bucketIndex];
            $bucket['quantity'] = (int) ($bucket['quantity'] ?? 0) + (int) ($normalizedAddon['quantity'] ?? 0);
            $bucket['base_amount'] = (float) ($bucket['base_amount'] ?? 0) + (float) ($normalizedAddon['base_amount'] ?? 0);
            $bucket['line_total_before_discount'] = (float) ($bucket['line_total_before_discount'] ?? 0) + (float) ($normalizedAddon['line_total_before_discount'] ?? 0);
            $bucket['discount'] = (float) ($bucket['discount'] ?? 0) + (float) ($normalizedAddon['discount'] ?? 0);
            $bucket['applied_discount'] = $bucket['discount'];
            $bucket['total'] = (float) ($bucket['total'] ?? 0) + (float) ($normalizedAddon['total'] ?? 0);
            $bucket['amount'] = $bucket['total'];
            $groupedAddons[$bucketIndex] = $bucket;
        }

        return array_values($groupedAddons);
    }

    private function billingItemDisplaySignature(array $item): string
    {
        $name = $this->normalizeBillingGroupKey($item['name'] ?? $item['item_name'] ?? 'Item');
        $rate = number_format((float) ($item['rate'] ?? 0), 4, '.', '');
        $status = $this->normalizeBillingGroupKey($item['status'] ?? '');
        $isRejected = (bool) ($item['is_rejected'] ?? false) ? '1' : '0';
        $rejectionReason = $this->normalizeBillingGroupKey($item['rejection_reason'] ?? '');
        $addonSignatures = array_map(
            fn(array $addon) => $this->billingAddonDisplaySignature($addon),
            $item['addons'] ?? []
        );
        sort($addonSignatures);

        return implode('||', [
            $name,
            $rate,
            $status,
            $isRejected,
            $rejectionReason,
            implode('~~', $addonSignatures),
        ]);
    }

    private function groupBillingDisplayItems(iterable $items): array
    {
        $groupedItems = [];
        $itemMap = [];

        foreach ($items as $item) {
            $normalizedItem = $this->normalizeBillingItem((array) $item);
            $signature = $this->billingItemDisplaySignature($normalizedItem);

            if (!isset($itemMap[$signature])) {
                $groupedItems[] = $normalizedItem;
                $itemMap[$signature] = count($groupedItems) - 1;
                continue;
            }

            $bucketIndex = $itemMap[$signature];
            $bucket = $groupedItems[$bucketIndex];
            $bucket['qty'] = (int) ($bucket['qty'] ?? 0) + (int) ($normalizedItem['qty'] ?? 0);
            $bucket['quantity'] = $bucket['qty'];
            $bucket['base_amount'] = (float) ($bucket['base_amount'] ?? 0) + (float) ($normalizedItem['base_amount'] ?? 0);
            $bucket['line_total_before_discount'] = (float) ($bucket['line_total_before_discount'] ?? 0) + (float) ($normalizedItem['line_total_before_discount'] ?? 0);
            $bucket['discount'] = (float) ($bucket['discount'] ?? 0) + (float) ($normalizedItem['discount'] ?? 0);
            $bucket['addon_total'] = (float) ($bucket['addon_total'] ?? 0) + (float) ($normalizedItem['addon_total'] ?? 0);
            $bucket['addon_discount_total'] = (float) ($bucket['addon_discount_total'] ?? 0) + (float) ($normalizedItem['addon_discount_total'] ?? 0);
            $bucket['total'] = (float) ($bucket['total'] ?? 0) + (float) ($normalizedItem['total'] ?? 0);
            $bucket['amount'] = $bucket['total'];
            $bucket['line_total'] = $bucket['total'];
            $bucket['addons'] = $this->mergeBillingDisplayAddons($bucket['addons'] ?? [], $normalizedItem['addons'] ?? []);
            $groupedItems[$bucketIndex] = $bucket;
        }

        return array_values($groupedItems);
    }

    private function numberToWords(int $number): string
    {
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

    private function resolveBillingTaxContext(?Table $table, array $fallback = []): array
    {
        $branch = $table?->branch;
        $taxSetting = strtolower((string) ($branch?->tax_setting ?? ($fallback['tax_setting'] ?? 'exclusive')));
        $taxSetting = $taxSetting === 'inclusive' ? 'inclusive' : 'exclusive';
        $taxRatePercent = max((float) ($branch?->tax_rate ?? ($fallback['tax_rate_snapshot'] ?? $fallback['tax_rate_percent'] ?? 0)), 0);
        $taxRate = $taxRatePercent / 100;
        $taxLabel = $taxSetting === 'inclusive' || (float) $taxRatePercent === 13.0
            ? 'VAT'
            : 'Tax';

        return [
            'tax_setting' => $taxSetting,
            'tax_rate_percent' => $taxRatePercent,
            'tax_rate' => $taxRate,
            'tax_label' => $taxLabel,
        ];
    }

    private function calculateBillingTaxTotals(float $discountedSubtotal, array $taxContext): array
    {
        $taxSetting = strtolower((string) ($taxContext['tax_setting'] ?? 'exclusive'));
        $taxSetting = $taxSetting === 'inclusive' ? 'inclusive' : 'exclusive';
        $taxRatePercent = max((float) ($taxContext['tax_rate_percent'] ?? 0), 0);
        $taxRate = max((float) ($taxContext['tax_rate'] ?? ($taxRatePercent / 100)), 0);
        $taxLabel = (string) ($taxContext['tax_label'] ?? ($taxSetting === 'inclusive' || $taxRatePercent === 13.0 ? 'VAT' : 'Tax'));

        if ($taxSetting === 'inclusive') {
            $taxAmount = $taxRate > 0 ? $discountedSubtotal - ($discountedSubtotal / (1 + $taxRate)) : 0;
            $taxableAmount = max($discountedSubtotal - $taxAmount, 0);
            $grandTotal = $discountedSubtotal;
        } else {
            $taxableAmount = $discountedSubtotal;
            $taxAmount = $taxableAmount * $taxRate;
            $grandTotal = $taxableAmount + $taxAmount;
        }

        return [
            'tax_setting' => $taxSetting,
            'tax_rate_percent' => $taxRatePercent,
            'tax_rate' => $taxRate,
            'tax_label' => $taxLabel,
            'taxable_amount' => $taxableAmount,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
        ];
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

    private function generateInvoiceNumber(Order $order): string
    {
        $table = Table::query()->with(['branch', 'tenant'])->find($order->table_id);

        return InvoiceNumberGenerator::generate(
            (int) $order->tenant_id,
            (string) data_get($table, 'tenant.company_name', ''),
            (string) data_get($table, 'branch.branch_name', ''),
            now((string) data_get($table, 'branch.timezone', config('app.timezone'))),
            'TI'
        );
    }
}
