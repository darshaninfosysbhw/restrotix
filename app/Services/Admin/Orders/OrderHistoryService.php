<?php

namespace App\Services\Admin\Orders;

use App\Http\Resources\Admin\Orders\OrderHistoryResource;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class OrderHistoryService
{
    public function buildPageData(int $tenantId, string $currencySymbol, ?Request $request = null): array
    {
        $request ??= request();
        $request->attributes->set('currency_symbol', $currencySymbol);

        $filters = $this->resolveFilters($request);

        $summary = $this->buildSummaryCards($tenantId, $currencySymbol);
        $statusTabs = $this->buildStatusTabs((string) ($filters['status'] ?? 'all'));
        $branches = $this->buildBranchOptions($tenantId);

        $baseQuery = $this->buildOrdersQuery($tenantId, $filters);
        $totalOrdersCount = (clone $baseQuery)->count();
        $limit = $this->resolveLimit($filters['limit'] ?? null);

        $ordersPaginator = (clone $baseQuery)
            ->paginate($limit)
            ->withQueryString();

        $orderModels = $ordersPaginator->getCollection();

        $orders = collect(OrderHistoryResource::collection($orderModels)->resolve($request));
        $orderRows = $orders->map(fn(array $row) => Arr::except($row, ['detail']))->values()->all();
        $orderDetails = $orders->mapWithKeys(function (array $row) {
            return [(string) ($row['order_no'] ?? '') => $row['detail'] ?? []];
        })->filter(fn($detail, string $key) => $key !== '')->all();

        $selectedOrderKey = (string) ($filters['order'] ?? '');
        if ($selectedOrderKey === '' || !isset($orderDetails[$selectedOrderKey])) {
            $selectedOrderKey = (string) ($orderRows[0]['order_no'] ?? '');
        }

        $selectedOrder = $selectedOrderKey !== '' && isset($orderDetails[$selectedOrderKey])
            ? $orderDetails[$selectedOrderKey]
            : $this->buildEmptySelectedOrder($currencySymbol);

        $displayedOrderCount = $ordersPaginator->count();
        $orderHistoryData = [
            'orderDetails' => $orderDetails,
            'defaultOrderKey' => $selectedOrderKey,
            'totalOrders' => $totalOrdersCount,
            'displayedOrders' => $displayedOrderCount,
            'filters' => $filters,
        ];

        return [
            'summaryCards' => $summary,
            'statusTabs' => $statusTabs,
            'branches' => $branches,
            'filters' => $filters,
            'orders' => $orderRows,
            'ordersPaginator' => $ordersPaginator,
            'orderCount' => $totalOrdersCount,
            'displayedOrderCount' => $displayedOrderCount,
            'orderHistoryData' => $orderHistoryData,
            'selectedOrderKey' => $selectedOrderKey,
            'selectedOrder' => $selectedOrder,
            'timeline' => $selectedOrder['timeline'] ?? [],
            'items' => $selectedOrder['items'] ?? [],
        ];
    }

    public function buildExportRows(int $tenantId, string $currencySymbol, ?Request $request = null): array
    {
        $request ??= request();
        $request->attributes->set('currency_symbol', $currencySymbol);

        $filters = $this->resolveFilters($request);
        unset($filters['limit']);

        $orders = collect(OrderHistoryResource::collection(
            $this->buildOrdersQuery($tenantId, $filters)->get()
        )->resolve($request));

        return [
            'filters' => $filters,
            'rows' => $orders->map(function (array $row) {
                return [
                    'order_no' => $row['order_no'] ?? 'N/A',
                    'table' => $row['table'] ?? '—',
                    'customer' => $row['customer'] ?? 'Guest',
                    'contact' => $row['subtext'] ?? '',
                    'source' => $row['source'] ?? '—',
                    'items' => $row['items'] ?? 0,
                    'amount' => $row['amount'] ?? '—',
                    'status' => $row['status'] ?? '—',
                    'paid' => $row['paid'] ?? '—',
                    'time' => $row['time'] ?? '—',
                    'detail' => $row['detail'] ?? [],
                ];
            })->values()->all(),
        ];
    }

    private function buildOrdersQuery(int $tenantId, array $filters = [])
    {
        $query = Order::query()
            ->where('tenant_id', $tenantId)
            ->with([
                'creator:id,name,email,phone_number',
                'invoice:id,order_id,invoice_number,subtotal_before_discount,subtotal,item_discount_amount,overall_discount_amount,discount_amount,subtotal_after_item_discount,taxable_amount,tax_amount,grand_total,status,payment_mode,payment_method,paid_amount,customer_name_snapshot,table_number_snapshot,notes_snapshot,updated_at,created_at',
                'paymentSessions:id,order_id,gateway_name,provider_reference,status,paid_at',
                'items:id,order_id,item_name,price,quantity,total,status,notes,started_at,ready_at,served_at,rejected_at',
            ])
            ->orderByDesc('ordered_at')
            ->orderByDesc('id');

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $needle = '%' . $search . '%';
                $subQuery->where('order_number', 'like', $needle)
                    ->orWhere('table_number', 'like', $needle)
                    ->orWhere('source', 'like', $needle)
                    ->orWhere('order_type', 'like', $needle)
                    ->orWhere('notes', 'like', $needle)
                    ->orWhereHas('creator', function ($creatorQuery) use ($needle) {
                        $creatorQuery->where('name', 'like', $needle)
                            ->orWhere('email', 'like', $needle)
                            ->orWhere('phone_number', 'like', $needle);
                    })
                    ->orWhereHas('invoice', function ($invoiceQuery) use ($needle) {
                        $invoiceQuery->where('invoice_number', 'like', $needle)
                            ->orWhere('customer_name_snapshot', 'like', $needle)
                            ->orWhere('table_number_snapshot', 'like', $needle);
                    })
                    ->orWhereHas('items', function ($itemQuery) use ($needle) {
                        $itemQuery->where('item_name', 'like', $needle);
                    });
            });
        }

        $status = strtolower(trim((string) ($filters['status'] ?? 'all')));
        if ($status !== '' && $status !== 'all') {
            $statusKey = match ($status) {
                'completed' => 'completed',
                'pending' => 'running',
                'cancelled', 'canceled' => 'cancelled',
                default => null,
            };

            if ($statusKey) {
                $query->where('status', $statusKey);
            }
        }

        $paymentStatus = strtolower(trim((string) ($filters['payment_status'] ?? 'all')));
        if ($paymentStatus !== '' && $paymentStatus !== 'all') {
            $paymentMap = match ($paymentStatus) {
                'paid' => ['paid', 'paid'],
                'partially_paid', 'partial' => ['partial', 'partially_paid'],
                'pending', 'unpaid' => ['pending', 'unpaid'],
                'refunded' => ['refunded', 'cancelled'],
                default => null,
            };

            if ($paymentMap) {
                if ($paymentStatus === 'refunded') {
                    $query->where('status', 'cancelled');
                } else {
                    $query->where(function ($paymentQuery) use ($paymentMap) {
                        $paymentQuery->where('payment_status', $paymentMap[0])
                            ->orWhereHas('invoice', function ($invoiceQuery) use ($paymentMap) {
                                $invoiceQuery->where('status', $paymentMap[1]);
                            });
                    });
                }
            }
        }

        $orderType = strtolower(trim((string) ($filters['order_type'] ?? 'all')));
        if ($orderType !== '' && $orderType !== 'all') {
            $this->applyOrderTypeFilter($query, $orderType);
        }

        $branchId = (int) ($filters['branch_id'] ?? 0);
        if ($branchId > 0) {
            $query->where('branch_id', $branchId);
        }

        $dateFrom = $this->parseDateFilter($filters['date_from'] ?? null);
        $dateTo = $this->parseDateFilter($filters['date_to'] ?? null);
        if ($dateFrom || $dateTo) {
            $start = $dateFrom ? $dateFrom->copy()->startOfDay() : ($dateTo ? $dateTo->copy()->startOfDay() : now()->startOfDay());
            $end = $dateTo ? $dateTo->copy()->endOfDay() : ($dateFrom ? $dateFrom->copy()->endOfDay() : now()->endOfDay());

            if ($start->greaterThan($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            $query->whereRaw('COALESCE(ordered_at, created_at) BETWEEN ? AND ?', [
                $start->toDateTimeString(),
                $end->toDateTimeString(),
            ]);
        }

        return $query;
    }

    private function buildBranchOptions(int $tenantId): array
    {
        return Branch::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('branch_name')
            ->get(['id', 'branch_name', 'city'])
            ->map(function (Branch $branch) {
                $label = trim((string) $branch->branch_name);
                $city = trim((string) ($branch->city ?? ''));

                if ($label === '') {
                    $label = 'Branch #' . $branch->id;
                }

                return [
                    'id' => (int) $branch->id,
                    'label' => $city !== '' ? ($label . ' · ' . $city) : $label,
                ];
            })
            ->values()
            ->all();
    }

    private function normalizeFilters(array $filters): array
    {
        $filters['search'] = trim((string) ($filters['search'] ?? ''));
        $filters['status'] = strtolower(trim((string) ($filters['status'] ?? 'all')));
        $filters['payment_status'] = strtolower(trim((string) ($filters['payment_status'] ?? 'all')));
        $filters['order_type'] = strtolower(trim((string) ($filters['order_type'] ?? 'all')));
        $filters['branch_id'] = (string) ((int) ($filters['branch_id'] ?? 0));
        $filters['date_from'] = $this->formatFilterDate($filters['date_from'] ?? null);
        $filters['date_to'] = $this->formatFilterDate($filters['date_to'] ?? null);

        return $filters;
    }

    private function resolveFilters(Request $request): array
    {
        return $this->normalizeFilters($request->only([
            'search',
            'status',
            'payment_status',
            'order_type',
            'branch_id',
            'date_from',
            'date_to',
            'order',
            'limit',
        ]));
    }

    private function formatFilterDate(mixed $value): string
    {
        $date = $this->parseDateFilter($value);

        return $date ? $date->format('Y-m-d') : '';
    }

    private function parseDateFilter(mixed $value): ?Carbon
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function applyOrderTypeFilter($query, string $orderType): void
    {
        if ($orderType === 'takeaway') {
            $query->where('order_type', 'takeaway');
            return;
        }

        if ($orderType === 'online') {
            $query->where(function ($subQuery) {
                $subQuery->where('source', 'web')
                    ->orWhere('order_type', 'direct');
            });

            return;
        }

        if ($orderType === 'dine_in') {
            $query->where(function ($subQuery) {
                $subQuery->where('order_type', 'dine_in')
                    ->orWhere(function ($sourceQuery) {
                        $sourceQuery->whereIn('source', ['qr', 'waiter', 'pos'])
                            ->where(function ($orderTypeQuery) {
                                $orderTypeQuery->whereNull('order_type')
                                    ->orWhere('order_type', '!=', 'takeaway');
                            });
                    });
            });
        }
    }

    private function buildSummaryCards(int $tenantId, string $currencySymbol): array
    {
        $now = now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfDay();
        $previousMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $ordersBaseQuery = Order::query()
            ->where('tenant_id', $tenantId);

        $currentOrders = (clone $ordersBaseQuery)
            ->whereBetween('ordered_at', [$currentMonthStart, $currentMonthEnd]);
        $previousOrders = (clone $ordersBaseQuery)
            ->whereBetween('ordered_at', [$previousMonthStart, $previousMonthEnd]);

        $currentTotalOrders = (clone $currentOrders)->count();
        $previousTotalOrders = (clone $previousOrders)->count();

        $currentCompletedOrders = (clone $currentOrders)
            ->where('status', 'completed')
            ->count();
        $previousCompletedOrders = (clone $previousOrders)
            ->where('status', 'completed')
            ->count();

        $currentPendingOrders = (clone $currentOrders)
            ->where(function ($query) {
                $query->where('status', 'running')
                    ->orWhere('payment_status', 'pending');
            })
            ->count();
        $previousPendingOrders = (clone $previousOrders)
            ->where(function ($query) {
                $query->where('status', 'running')
                    ->orWhere('payment_status', 'pending');
            })
            ->count();

        $currentRevenue = (float) OrderInvoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partially_paid', 'paid'])
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('grand_total');

        $previousRevenue = (float) OrderInvoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partially_paid', 'paid'])
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->sum('grand_total');

        return [
            [
                'label' => 'Total Orders',
                'value' => number_format($currentTotalOrders),
                'delta' => $this->buildTrend($currentTotalOrders, $previousTotalOrders, 'last month')['label'],
                'icon' => 'fa-bag-shopping',
                'iconClass' => 'border-orange-500/20 bg-orange-500/10 text-orange-400',
                'deltaClass' => $this->buildTrend($currentTotalOrders, $previousTotalOrders, 'last month')['class'],
            ],
            [
                'label' => 'Completed',
                'value' => number_format($currentCompletedOrders),
                'delta' => $this->buildTrend($currentCompletedOrders, $previousCompletedOrders, 'last month')['label'],
                'icon' => 'fa-circle-check',
                'iconClass' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400',
                'deltaClass' => $this->buildTrend($currentCompletedOrders, $previousCompletedOrders, 'last month')['class'],
            ],
            [
                'label' => 'Pending',
                'value' => number_format($currentPendingOrders),
                'delta' => $this->buildTrend($currentPendingOrders, $previousPendingOrders, 'last month')['label'],
                'icon' => 'fa-clock',
                'iconClass' => 'border-amber-500/20 bg-amber-500/10 text-amber-400',
                'deltaClass' => $this->buildTrend($currentPendingOrders, $previousPendingOrders, 'last month')['class'],
            ],
            [
                'label' => 'Revenue',
                'value' => $this->formatMoney($currentRevenue, $currencySymbol),
                'delta' => $this->buildTrend($currentRevenue, $previousRevenue, 'last month')['label'],
                'icon' => 'fa-sack-dollar',
                'iconClass' => 'border-violet-500/20 bg-violet-500/10 text-violet-400',
                'deltaClass' => $this->buildTrend($currentRevenue, $previousRevenue, 'last month')['class'],
            ],
        ];
    }

    private function buildStatusTabs(string $activeStatus): array
    {
        $activeStatus = strtolower(trim($activeStatus));

        return [
            ['label' => 'All', 'tone' => 'orange', 'active' => $activeStatus === '' || $activeStatus === 'all'],
            ['label' => 'Completed', 'tone' => 'emerald', 'active' => $activeStatus === 'completed'],
            ['label' => 'Pending', 'tone' => 'amber', 'active' => $activeStatus === 'pending'],
            ['label' => 'Cancelled', 'tone' => 'rose', 'active' => in_array($activeStatus, ['cancelled', 'canceled'], true)],
        ];
    }

    private function resolveLimit(mixed $limit): int
    {
        $resolvedLimit = (int) ($limit ?? 25);

        return max(5, min($resolvedLimit, 50));
    }

    private function buildTrend(float $current, float $previous, string $periodLabel): array
    {
        if ($previous > 0) {
            $change = (($current - $previous) / $previous) * 100;
        } elseif ($current > 0) {
            $change = 100.0;
        } else {
            $change = 0.0;
        }

        $direction = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat');
        $prefix = $change > 0 ? '+' : '';

        return [
            'label' => $prefix . number_format($change, 1) . '% vs ' . $periodLabel,
            'class' => $direction === 'down'
                ? 'text-red-400'
                : ($direction === 'flat' ? 'text-yellow-400' : 'text-green-400'),
        ];
    }

    private function formatMoney(float $amount, string $currencySymbol): string
    {
        $symbol = trim($currencySymbol);
        $formattedAmount = number_format($amount, 2, '.', ',');

        if ($symbol === '') {
            return $formattedAmount;
        }

        $needsSpace = preg_match('/[A-Za-z]/', $symbol) === 1;

        return $symbol . ($needsSpace ? ' ' : '') . $formattedAmount;
    }

    private function buildEmptySelectedOrder(string $currencySymbol): array
    {
        $emptyMoney = $this->formatMoney(0, $currencySymbol);

        return [
            'order_no' => '—',
            'date' => '—',
            'time' => '—',
            'type' => '—',
            'table' => '—',
            'waiter' => '—',
            'subtotal' => $emptyMoney,
            'discount' => $emptyMoney,
            'service' => $emptyMoney,
            'tax' => $emptyMoney,
            'total' => $emptyMoney,
            'payment_method' => '—',
            'payment_status_label' => 'Pending',
            'payment_status_class' => 'border-amber-500/20 bg-amber-500/10 text-amber-400',
            'amount_paid' => $emptyMoney,
            'paid_at' => '—',
            'transaction_id' => '—',
            'note' => 'No order selected.',
            'timeline' => [],
            'items' => [],
        ];
    }
}
