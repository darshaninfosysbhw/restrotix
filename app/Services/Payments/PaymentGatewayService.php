<?php

namespace App\Services\Payments;

use App\Models\BranchPaymentGateway;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\OrderPayment;
use App\Models\PaymentSession;
use App\Models\Table;
use App\Services\Admin\MenuManagement\OrderStatusService;
use App\Support\InvoiceNumberGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use RuntimeException;

class PaymentGatewayService
{
    public function __construct(
        protected OrderStatusService $orderStatusService
    ) {
    }

    public function initiate(Table $table, Order $order): array
    {
        $paymentFlow = $this->orderStatusService->resolvePaymentFlow($table);

        if (!($paymentFlow['self_payment_enabled'] ?? false)) {
            throw new RuntimeException('Self payment is disabled for this branch.');
        }

        $config = BranchPaymentGateway::query()
            ->where('tenant_id', (int) $table->tenant_id)
            ->where('branch_id', (int) $table->branch_id)
            ->where('is_active', true)
            ->whereIn('checkout_mode', ['static_qr', 'dynamic_api'])
            ->with('gateway')
            ->get()
            ->filter(fn (BranchPaymentGateway $gateway) => $gateway->gateway && (bool) $gateway->gateway->is_active)
            ->sortBy(fn (BranchPaymentGateway $gateway) => $gateway->checkout_mode === 'static_qr' ? 0 : 1)
            ->first();

        if (!$config) {
            throw new RuntimeException('No active payment gateway configuration found.');
        }

        $billData = $this->resolvePayableBillData($table, $order);
        $invoice = $this->resolveInvoice($order, $table, $billData);
        $amount = (float) $billData['grand_total'];
        $session = PaymentSession::create([
            'tenant_id' => (int) $table->tenant_id,
            'branch_id' => (int) $table->branch_id,
            'table_id' => (int) $table->id,
            'order_id' => (int) $order->id,
            'order_invoice_id' => (int) $invoice->id,
            'branch_payment_gateway_id' => (int) $config->id,
            'gateway_slug' => (string) $config->gateway?->slug,
            'gateway_name' => (string) $config->gateway?->name,
            'checkout_mode' => (string) $config->checkout_mode,
            'amount' => $amount,
            'currency_code' => 'NPR',
            'status' => 'pending',
        ]);

        if ($config->checkout_mode === 'static_qr') {
            return $this->buildStaticQrResponse($config, $session, $table, $order, $invoice);
        }

        return $this->buildDynamicResponse($config, $session, $table, $order, $invoice);
    }

    public function handleReturn(Table $table, Order $order, \Illuminate\Http\Request $request): array
    {
        $sessionId = (int) $request->query('session', 0);
        $session = $sessionId > 0
            ? PaymentSession::query()->where('order_id', $order->id)->find($sessionId)
            : PaymentSession::query()
                ->where('order_id', $order->id)
                ->latest()
                ->first();

        if (!$session) {
            return [
                'status' => 'failed',
                'message' => 'Payment session not found.',
            ];
        }

        $session->loadMissing(['branchPaymentGateway.gateway']);
        $gatewaySlug = (string) $session->gateway_slug;

        $result = match ($gatewaySlug) {
            'khalti' => $this->handleKhaltiReturn($session, $request),
            'esewa' => $this->handleEsewaReturn($session, $request),
            default => $this->handleGenericReturn($session, $request),
        };

        if (($result['status'] ?? 'failed') === 'completed') {
            $this->markCompleted($session, $result);
        } elseif (($result['status'] ?? 'failed') === 'cancelled') {
            $this->markCancelled($session, $result);
        } else {
            $this->markFailed($session, $result);
        }

        return array_merge($result, [
            'session_id' => $session->id,
            'gateway_name' => $session->gateway_name,
            'payment_mode' => $gatewaySlug === 'esewa'
                ? 'eSewa'
                : ($gatewaySlug === 'khalti' ? 'Khalti' : ucfirst($gatewaySlug ?: 'Online')),
        ]);
    }

    protected function resolveInvoice(Order $order, Table $table, array $billData): OrderInvoice
    {
        $invoice = OrderInvoice::query()->firstOrCreate(
            [
                'tenant_id' => (int) $table->tenant_id,
                'branch_id' => (int) $table->branch_id,
                'order_id' => (int) $order->id,
            ],
            [
                'invoice_number' => InvoiceNumberGenerator::generate(
                    (int) $table->tenant_id,
                    (string) data_get($table, 'tenant.company_name', ''),
                    (string) data_get($table, 'branch.branch_name', ''),
                    now((string) data_get($table, 'branch.timezone', config('app.timezone'))),
                    'TI'
                ),
                'subtotal' => (float) ($billData['subtotal'] ?? 0),
                'discount_amount' => (float) ($order->discount_amount ?? 0),
                'tax_rate' => (float) ($table->branch?->tax_rate ?? 0),
                'tax_amount' => (float) ($billData['tax_amount'] ?? 0),
                'grand_total' => (float) ($billData['grand_total'] ?? 0),
                'status' => 'unpaid',
            ]
        );

        $invoice->update([
            'subtotal' => (float) ($billData['subtotal'] ?? 0),
            'discount_amount' => (float) ($order->discount_amount ?? 0),
            'tax_rate' => (float) ($billData['tax_rate'] ?? ($table->branch?->tax_rate ?? 0)),
            'tax_amount' => (float) ($billData['tax_amount'] ?? 0),
            'grand_total' => (float) ($billData['grand_total'] ?? 0),
            'status' => $invoice->status === 'paid' ? 'paid' : 'unpaid',
        ]);

        return $invoice->fresh();
    }

    protected function resolvePayableBillData(Table $table, Order $order): array
    {
        $pageData = $this->orderStatusService->buildPageData($table, $order, false);

        return [
            'subtotal' => (float) ($pageData['subtotal'] ?? 0),
            'tax_amount' => (float) ($pageData['taxAmount'] ?? 0),
            'grand_total' => (float) ($pageData['grandTotal'] ?? 0),
            'tax_rate' => (float) ($pageData['taxRate'] ?? 0),
        ];
    }

    protected function buildStaticQrResponse(BranchPaymentGateway $config, PaymentSession $session, Table $table, Order $order, OrderInvoice $invoice): array
    {
        $qrPayload = trim((string) ($config->static_qr_label ?? ''));
        if ($qrPayload === '') {
            $qrPayload = sprintf(
                'Table %s | Order %s | Rs %s',
                $table->table_number,
                $order->order_number,
                number_format((float) $invoice->grand_total, 2, '.', '')
            );
        }

        $svg = QrCode::format('svg')
            ->size(320)
            ->margin(2)
            ->generate($qrPayload);

        $session->update([
            'provider_request' => [
                'static_qr_label' => $config->static_qr_label,
                'qr_payload' => $qrPayload,
            ],
            'provider_response' => [
                'static_qr_svg' => $svg,
            ],
            'status' => 'initiated',
            'initiated_at' => now(),
        ]);

        return [
            'status' => 'static_qr',
            'display_mode' => 'static_qr',
            'payment_url' => null,
            'redirect_url' => null,
            'provider_reference' => null,
            'payment_session_id' => $session->id,
            'gateway_name' => $session->gateway_name,
            'gateway_slug' => $session->gateway_slug,
            'static_qr_label' => $config->static_qr_label,
            'static_qr_svg' => $svg,
            'amount' => (float) $invoice->grand_total,
        ];
    }

    protected function buildDynamicResponse(BranchPaymentGateway $config, PaymentSession $session, Table $table, Order $order, OrderInvoice $invoice): array
    {
        $gatewaySlug = (string) $config->gateway?->slug;

        return match ($gatewaySlug) {
            'khalti' => $this->startKhalti($config, $session, $table, $order, $invoice),
            'esewa' => $this->startEsewa($config, $session, $table, $order, $invoice),
            default => $this->startGenericRedirect($config, $session, $table, $order, $invoice),
        };
    }

    protected function startKhalti(BranchPaymentGateway $config, PaymentSession $session, Table $table, Order $order, OrderInvoice $invoice): array
    {
        $credentials = $config->credentials ?? [];
        $secretKey = (string) data_get($credentials, 'secret_key', '');
        $baseUrl = $config->mode === 'live' ? 'https://khalti.com/api/v2' : 'https://dev.khalti.com/api/v2';
        $payload = [
            'return_url' => route('public.order.payment.return', ['qr_token' => $table->qr_token, 'session' => $session->id]),
            'website_url' => route('public.menu.scan', ['qr_token' => $table->qr_token]),
            'amount' => (int) round(((float) $invoice->grand_total) * 100),
            'purchase_order_id' => 'ORD-' . $order->order_number . '-S' . $session->id,
            'purchase_order_name' => 'Table ' . $table->table_number . ' Order ' . $order->order_number,
            'customer_info' => [
                'name' => trim((string) ($table->branch?->branch_name ?? 'Restaurant Customer')),
                'email' => (string) ($table->branch?->branch_email ?? ''),
                'phone' => (string) ($table->branch?->contact_number ?? ''),
            ],
        ];

        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => 'Key ' . $secretKey,
            ])
            ->post($baseUrl . '/epayment/initiate/', $payload);

        if (!$response->successful()) {
            throw new RuntimeException('Khalti payment initiation failed: ' . $response->body());
        }

        $body = $response->json();
        $paymentUrl = (string) data_get($body, 'payment_url', '');
        $pidx = (string) data_get($body, 'pidx', '');

        if ($paymentUrl === '' || $pidx === '') {
            throw new RuntimeException('Khalti payment response was incomplete.');
        }

        $session->update([
            'provider_reference' => $pidx,
            'payment_url' => $paymentUrl,
            'provider_request' => $payload,
            'provider_response' => $body,
            'status' => 'initiated',
            'initiated_at' => now(),
        ]);

        return [
            'status' => 'initiated',
            'display_mode' => 'redirect',
            'payment_url' => $paymentUrl,
            'redirect_url' => $paymentUrl,
            'provider_reference' => $pidx,
            'payment_session_id' => $session->id,
            'gateway_name' => $session->gateway_name,
            'gateway_slug' => $session->gateway_slug,
            'amount' => (float) $invoice->grand_total,
        ];
    }

    protected function startEsewa(BranchPaymentGateway $config, PaymentSession $session, Table $table, Order $order, OrderInvoice $invoice): array
    {
        $credentials = $config->credentials ?? [];
        $productCode = (string) data_get($credentials, 'product_code', data_get($credentials, 'merchant_code', 'INTENT'));
        $accessKey = (string) data_get($credentials, 'access_key', data_get($credentials, 'secret_key', ''));
        if ($accessKey === '') {
            throw new RuntimeException('eSewa access key is missing.');
        }

        $transactionUuid = 'ORD-' . $order->order_number . '-S' . $session->id;
        $amount = (float) $invoice->grand_total;
        $message = 'product_code=' . $productCode . ',amount=' . $amount . ',transaction_uuid=' . $transactionUuid;
        $signature = base64_encode(hash_hmac('sha256', $message, $accessKey, true));
        $payload = [
            'product_code' => $productCode,
            'amount' => $amount,
            'transaction_uuid' => $transactionUuid,
            'signed_field_names' => 'product_code,amount,transaction_uuid',
            'signature' => $signature,
            'callback_url' => route('public.order.payment.return', ['qr_token' => $table->qr_token, 'session' => $session->id]),
            'redirect_url' => route('public.order.payment.return', ['qr_token' => $table->qr_token, 'session' => $session->id]),
            'properties' => [
                'customer_id' => (string) $order->order_number,
                'remarks' => 'Table ' . $table->table_number . ' payment',
            ],
        ];

        $baseUrl = 'https://rc-checkout.esewa.com.np/api/client/intent/payment/book';
        $response = Http::acceptJson()->post($baseUrl, $payload);

        if (!$response->successful()) {
            throw new RuntimeException('eSewa payment initiation failed: ' . $response->body());
        }

        $body = $response->json();
        $bookingId = (string) data_get($body, 'data.booking_id', '');
        $deeplink = (string) data_get($body, 'data.deeplink', '');
        $correlationId = (string) data_get($body, 'data.correlation_id', '');

        if ($bookingId === '' || $deeplink === '') {
            throw new RuntimeException('eSewa payment response was incomplete.');
        }

        $session->update([
            'provider_reference' => $bookingId,
            'payment_url' => $deeplink,
            'provider_request' => $payload,
            'provider_response' => $body,
            'status' => 'initiated',
            'initiated_at' => now(),
        ]);

        return [
            'status' => 'initiated',
            'display_mode' => 'redirect',
            'payment_url' => $deeplink,
            'redirect_url' => $deeplink,
            'provider_reference' => $bookingId,
            'correlation_id' => $correlationId,
            'payment_session_id' => $session->id,
            'gateway_name' => $session->gateway_name,
            'gateway_slug' => $session->gateway_slug,
            'amount' => $amount,
        ];
    }

    protected function startGenericRedirect(BranchPaymentGateway $config, PaymentSession $session, Table $table, Order $order, OrderInvoice $invoice): array
    {
        $credentials = $config->credentials ?? [];
        $paymentUrl = (string) data_get($credentials, 'payment_url', data_get($credentials, 'redirect_url', ''));

        if ($paymentUrl === '') {
            throw new RuntimeException('Payment gateway redirect URL is missing.');
        }

        $paymentUrl = strtr($paymentUrl, [
            '{amount}' => (string) $invoice->grand_total,
            '{order_number}' => (string) $order->order_number,
            '{table_number}' => (string) $table->table_number,
            '{session_id}' => (string) $session->id,
        ]);

        $session->update([
            'provider_reference' => (string) $session->id,
            'payment_url' => $paymentUrl,
            'provider_request' => ['payment_url' => $paymentUrl],
            'provider_response' => ['redirect_url' => $paymentUrl],
            'status' => 'initiated',
            'initiated_at' => now(),
        ]);

        return [
            'status' => 'initiated',
            'display_mode' => 'redirect',
            'payment_url' => $paymentUrl,
            'redirect_url' => $paymentUrl,
            'provider_reference' => (string) $session->id,
            'payment_session_id' => $session->id,
            'gateway_name' => $session->gateway_name,
            'gateway_slug' => $session->gateway_slug,
            'amount' => (float) $invoice->grand_total,
        ];
    }

    protected function handleKhaltiReturn(PaymentSession $session, \Illuminate\Http\Request $request): array
    {
        $pidx = (string) $request->query('pidx', $session->provider_reference ?? '');
        $status = strtolower((string) $request->query('status', ''));
        if ($pidx === '') {
            return ['status' => 'failed', 'message' => 'Missing Khalti payment reference.'];
        }

        $response = $this->lookupKhaltiStatus($session, $pidx);
        $lookupStatus = strtolower((string) data_get($response, 'status', $status));
        $transactionId = (string) data_get($response, 'transaction_id', $request->query('transaction_id', ''));

        if ($lookupStatus === 'completed') {
            return [
                'status' => 'completed',
                'message' => 'Khalti payment completed successfully.',
                'provider_reference' => $pidx,
                'transaction_id' => $transactionId ?: $pidx,
                'gateway_payload' => $response,
            ];
        }

        if (in_array($lookupStatus, ['user canceled', 'expired', 'failed'], true)) {
            return [
                'status' => 'cancelled',
                'message' => 'Khalti payment was not completed.',
                'provider_reference' => $pidx,
                'transaction_id' => $transactionId,
                'gateway_payload' => $response,
            ];
        }

        return [
            'status' => 'failed',
            'message' => 'Khalti payment is pending or unknown.',
            'provider_reference' => $pidx,
            'transaction_id' => $transactionId,
            'gateway_payload' => $response,
        ];
    }

    protected function lookupKhaltiStatus(PaymentSession $session, string $pidx): array
    {
        $config = $session->branchPaymentGateway;
        $credentials = $config?->credentials ?? [];
        $secretKey = (string) data_get($credentials, 'secret_key', '');
        $baseUrl = $config?->mode === 'live' ? 'https://khalti.com/api/v2' : 'https://dev.khalti.com/api/v2';

        $response = Http::acceptJson()
            ->withHeaders(['Authorization' => 'Key ' . $secretKey])
            ->post($baseUrl . '/epayment/lookup/', ['pidx' => $pidx]);

        if (!$response->successful()) {
            throw new RuntimeException('Khalti lookup failed: ' . $response->body());
        }

        return $response->json() ?? [];
    }

    protected function handleEsewaReturn(PaymentSession $session, \Illuminate\Http\Request $request): array
    {
        $bookingId = (string) $request->query('booking_id', $session->provider_reference ?? '');
        $correlationId = (string) $request->query('correlation_id', data_get($session->provider_response, 'data.correlation_id', ''));
        $status = strtoupper((string) $request->query('status', ''));

        if ($bookingId === '') {
            return ['status' => 'failed', 'message' => 'Missing eSewa booking reference.'];
        }

        $lookup = $this->lookupEsewaStatus($session, $bookingId, $correlationId);
        $lookupStatus = strtoupper((string) data_get($lookup, 'data.status', $status));
        $transactionId = (string) data_get($lookup, 'data.transaction_id', $request->query('transaction_id', ''));
        $referenceCode = (string) data_get($lookup, 'data.reference_code', $request->query('reference_code', ''));

        if ($lookupStatus === 'SUCCESS') {
            return [
                'status' => 'completed',
                'message' => 'eSewa payment completed successfully.',
                'provider_reference' => $bookingId,
                'transaction_id' => $transactionId ?: $referenceCode,
                'gateway_payload' => $lookup,
            ];
        }

        if (in_array($lookupStatus, ['CANCELED', 'EXPIRED', 'FAILED'], true)) {
            return [
                'status' => 'cancelled',
                'message' => 'eSewa payment was not completed.',
                'provider_reference' => $bookingId,
                'transaction_id' => $transactionId,
                'gateway_payload' => $lookup,
            ];
        }

        return [
            'status' => 'failed',
            'message' => 'eSewa payment is pending or unknown.',
            'provider_reference' => $bookingId,
            'transaction_id' => $transactionId,
            'gateway_payload' => $lookup,
        ];
    }

    protected function lookupEsewaStatus(PaymentSession $session, string $bookingId, string $correlationId): array
    {
        $config = $session->branchPaymentGateway;
        $credentials = $config?->credentials ?? [];
        $productCode = (string) data_get($credentials, 'product_code', data_get($credentials, 'merchant_code', 'INTENT'));
        $accessKey = (string) data_get($credentials, 'access_key', data_get($credentials, 'secret_key', ''));
        $message = 'booking_id=' . $bookingId . ',product_code=' . $productCode . ',correlation_id=' . $correlationId;
        $signature = base64_encode(hash_hmac('sha256', $message, $accessKey, true));
        $payload = [
            'booking_id' => $bookingId,
            'product_code' => $productCode,
            'correlation_id' => $correlationId,
            'signed_field_names' => 'booking_id,product_code,correlation_id',
            'signature' => $signature,
        ];

        $response = Http::acceptJson()->post('https://rc-checkout.esewa.com.np/api/client/intent/payment/status', $payload);

        if (!$response->successful()) {
            throw new RuntimeException('eSewa lookup failed: ' . $response->body());
        }

        return $response->json() ?? [];
    }

    protected function handleGenericReturn(PaymentSession $session, \Illuminate\Http\Request $request): array
    {
        $status = strtolower((string) $request->query('status', ''));
        if (in_array($status, ['success', 'completed', 'paid'], true)) {
            return [
                'status' => 'completed',
                'message' => 'Payment completed successfully.',
                'provider_reference' => (string) $request->query('transaction_id', $session->provider_reference ?? ''),
                'transaction_id' => (string) $request->query('transaction_id', $session->provider_reference ?? ''),
                'gateway_payload' => $request->all(),
            ];
        }

        if (in_array($status, ['canceled', 'cancelled', 'expired', 'failed'], true)) {
            return [
                'status' => 'cancelled',
                'message' => 'Payment was not completed.',
                'provider_reference' => (string) $request->query('transaction_id', $session->provider_reference ?? ''),
                'transaction_id' => (string) $request->query('transaction_id', $session->provider_reference ?? ''),
                'gateway_payload' => $request->all(),
            ];
        }

        return [
            'status' => 'failed',
            'message' => 'Payment status is unknown.',
            'provider_reference' => (string) $request->query('transaction_id', $session->provider_reference ?? ''),
            'transaction_id' => (string) $request->query('transaction_id', $session->provider_reference ?? ''),
            'gateway_payload' => $request->all(),
        ];
    }

    protected function markCompleted(PaymentSession $session, array $result): void
    {
        DB::transaction(function () use ($session, $result) {
            $session->update([
                'status' => 'completed',
                'provider_response' => array_merge($session->provider_response ?? [], $result),
                'provider_reference' => (string) ($result['provider_reference'] ?? $session->provider_reference),
                'paid_at' => now(),
            ]);

            $invoice = $session->invoice;
            if ($invoice) {
                $invoice->update(['status' => 'paid']);

                $paymentMethod = $this->resolveLedgerPaymentMethod($session);

                OrderPayment::query()->firstOrCreate(
                    [
                        'invoice_id' => (int) $invoice->id,
                        'transaction_ref' => (string) ($result['transaction_id'] ?? $session->provider_reference ?? $session->id),
                    ],
                    [
                        'tenant_id' => (int) $session->tenant_id,
                        'payment_method' => $paymentMethod,
                        'amount' => (float) $session->amount,
                        'gateway_response' => array_merge($session->provider_response ?? [], $result),
                        'status' => 'success',
                        'verified_by_user_id' => null,
                    ]
                );
            }

            $order = $session->order;
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'paid_amount' => (float) $session->amount,
                    'status' => 'completed',
                ]);
            }

            $table = $session->table;
            if ($table) {
                $table->update([
                    'status' => 'available',
                ]);
            }
        });
    }

    protected function markCancelled(PaymentSession $session, array $result): void
    {
        $session->update([
            'status' => 'cancelled',
            'provider_response' => array_merge($session->provider_response ?? [], $result),
            'failure_reason' => $result['message'] ?? 'Payment cancelled.',
        ]);
    }

    protected function markFailed(PaymentSession $session, array $result): void
    {
        $session->update([
            'status' => 'failed',
            'provider_response' => array_merge($session->provider_response ?? [], $result),
            'failure_reason' => $result['message'] ?? 'Payment failed.',
        ]);
    }

    protected function resolveLedgerPaymentMethod(PaymentSession $session): string
    {
        return match ((string) $session->checkout_mode) {
            'static_qr' => 'static_qr',
            'dynamic_api' => 'fonepay_dynamic',
            default => 'card',
        };
    }
}
