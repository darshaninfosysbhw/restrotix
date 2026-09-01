<?php

namespace App\Services\Admin;

use App\Models\Branch;
use App\Models\OrderInvoice;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\TableQrScan;
use App\Models\User;

class DashboardService
{
    public function buildDashboardPayload(int $tenantId, string $currencySymbol): array
    {
        $dashboardMetrics = $this->buildDashboardMetrics($tenantId, $currencySymbol);
        $qrScanStats = $this->buildQrScanStats($tenantId);
        $topBranches = $this->buildTopBranches($tenantId, $currencySymbol);
        $productSales = $this->buildProductSalesInsights($tenantId, $currencySymbol);

        return array_merge(
            compact('dashboardMetrics', 'qrScanStats', 'topBranches'),
            $productSales
        );
    }

    private function buildDashboardMetrics(int $tenantId, string $currencySymbol): array
    {
        $now = now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfDay();
        $previousMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $invoiceBaseQuery = OrderInvoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partially_paid', 'paid']);

        $currentMonthInvoices = (clone $invoiceBaseQuery)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);
        $previousMonthInvoices = (clone $invoiceBaseQuery)
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd]);

        $currentRevenue = (float) (clone $currentMonthInvoices)->sum('grand_total');
        $previousRevenue = (float) (clone $previousMonthInvoices)->sum('grand_total');
        $currentOrders = (int) (clone $currentMonthInvoices)->count();
        $previousOrders = (int) (clone $previousMonthInvoices)->count();
        $avgOrderValue = $currentOrders > 0 ? $currentRevenue / $currentOrders : 0.0;
        $previousAvgOrderValue = $previousOrders > 0 ? $previousRevenue / $previousOrders : 0.0;

        $branches = Branch::query()
            ->where('tenant_id', $tenantId)
            ->get(['id', 'branch_name']);

        $currentBranchRevenue = (clone $currentMonthInvoices)
            ->selectRaw('branch_id, SUM(grand_total) as total_revenue')
            ->groupBy('branch_id')
            ->pluck('total_revenue', 'branch_id');

        $activeBranchesCount = $branches->filter(function ($branch) use ($currentBranchRevenue) {
            return (float) ($currentBranchRevenue[$branch->id] ?? 0) > 0;
        })->count();

        $totalBranches = $branches->count();
        if ($totalBranches === 0) {
            $activeBranchesLabel = 'No branches yet';
            $activeBranchesClass = 'text-yellow-400';
        } elseif ($activeBranchesCount >= $totalBranches) {
            $activeBranchesLabel = 'All operational';
            $activeBranchesClass = 'text-green-400';
        } else {
            $activeBranchesLabel = 'Based on this month\'s orders';
            $activeBranchesClass = 'text-green-400';
        }

        $staffBaseQuery = User::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('role', ['manager', 'chef', 'waiter', 'cashier']);

        $totalStaff = (int) (clone $staffBaseQuery)->count();
        $onLeaveStaff = (int) (clone $staffBaseQuery)
            ->where('is_active', true)
            ->whereHas('detail', function ($query) {
                $query->whereNotNull('exit_date');
            })
            ->count();

        return [
            'revenue' => [
                'display' => $this->formatMoney($currentRevenue, $currencySymbol),
                'trend' => $this->buildTrend($currentRevenue, $previousRevenue, 'last month'),
            ],
            'orders' => [
                'display' => number_format($currentOrders),
                'trend' => $this->buildTrend((float) $currentOrders, (float) $previousOrders, 'last month'),
            ],
            'average_order' => [
                'display' => $this->formatMoney($avgOrderValue, $currencySymbol),
                'trend' => $this->buildTrend($avgOrderValue, $previousAvgOrderValue, 'last month'),
            ],
            'branches' => [
                'display' => $totalBranches > 0
                    ? $activeBranchesCount . ' / ' . $totalBranches
                    : '0 / 0',
                'note' => $activeBranchesLabel,
                'note_class' => $activeBranchesClass,
            ],
            'staff' => [
                'total' => number_format($totalStaff),
                'on_leave' => number_format($onLeaveStaff),
            ],
        ];
    }

    private function buildQrScanStats(int $tenantId): array
    {
        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        $baseQuery = TableQrScan::query()
            ->where('tenant_id', $tenantId);

        $uniqueSessionCountQuery = function ($query) {
            return $query->whereNotNull('table_access_session_id');
        };

        $totalScans = (int) $uniqueSessionCountQuery(clone $baseQuery)
            ->distinct()
            ->count('table_access_session_id');
        $todayScans = (int) (clone $baseQuery)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->whereNotNull('table_access_session_id')
            ->distinct()
            ->count('table_access_session_id');
        $uniqueTables = (int) (clone $baseQuery)->distinct()->count('table_id');

        $topScanRow = (clone $baseQuery)
            ->whereNotNull('table_access_session_id')
            ->selectRaw('table_id, COUNT(DISTINCT table_access_session_id) as total_scans')
            ->groupBy('table_id')
            ->orderByDesc('total_scans')
            ->first();

        $topTableLabel = 'N/A';
        $topTableScans = 0;

        if ($topScanRow) {
            $tableNumber = Table::query()
                ->whereKey((int) $topScanRow->table_id)
                ->value('table_number');

            $topTableLabel = $tableNumber ? 'Table ' . $tableNumber : 'Table #' . (int) $topScanRow->table_id;
            $topTableScans = (int) ($topScanRow->total_scans ?? 0);
        }

        return [
            'total_scans' => number_format($totalScans),
            'today_scans' => number_format($todayScans),
            'unique_tables' => number_format($uniqueTables),
            'top_table' => $topTableLabel,
            'top_table_scans' => number_format($topTableScans),
        ];
    }

    private function buildTopBranches(int $tenantId, string $currencySymbol): array
    {
        $now = now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfDay();
        $previousMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $branches = Branch::query()
            ->where('tenant_id', $tenantId)
            ->get(['id', 'branch_name']);

        $invoiceBaseQuery = OrderInvoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partially_paid', 'paid']);

        $currentBranchRevenue = (clone $invoiceBaseQuery)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->selectRaw('branch_id, SUM(grand_total) as total_revenue')
            ->groupBy('branch_id')
            ->pluck('total_revenue', 'branch_id');

        $previousBranchRevenue = (clone $invoiceBaseQuery)
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->selectRaw('branch_id, SUM(grand_total) as total_revenue')
            ->groupBy('branch_id')
            ->pluck('total_revenue', 'branch_id');

        return $branches
            ->map(function ($branch) use ($currentBranchRevenue, $previousBranchRevenue, $currencySymbol) {
                $currentRevenue = (float) ($currentBranchRevenue[$branch->id] ?? 0);
                $previousRevenue = (float) ($previousBranchRevenue[$branch->id] ?? 0);
                $trend = $this->buildTrend($currentRevenue, $previousRevenue, 'last month');
                $trendBadgeClass = match ($trend['class']) {
                    'text-red-400' => 'bg-red-900/50 text-red-400',
                    'text-yellow-400' => 'bg-yellow-900/50 text-yellow-400',
                    default => 'bg-green-900/50 text-green-400',
                };

                return [
                    'name' => (string) $branch->branch_name,
                    'revenue_value' => $currentRevenue,
                    'revenue_display' => $this->formatMoney($currentRevenue, $currencySymbol),
                    'trend_label' => $trend['label'],
                    'trend_class' => $trend['class'],
                    'trend_badge_class' => $trendBadgeClass,
                ];
            })
            ->sortByDesc('revenue_value')
            ->take(5)
            ->values()
            ->all();
    }

    private function buildProductSalesInsights(int $tenantId, string $currencySymbol): array
    {
        $now = now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfDay();
        $previousMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $baseQuery = OrderItem::query()
            ->join('order_invoices', 'order_items.invoice_id', '=', 'order_invoices.id')
            ->leftJoin('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('order_invoices.tenant_id', $tenantId)
            ->whereIn('order_invoices.status', ['unpaid', 'partially_paid', 'paid']);

        $currentRows = (clone $baseQuery)
            ->whereBetween('order_invoices.created_at', [$currentMonthStart, $currentMonthEnd])
            ->selectRaw('order_items.menu_item_id, order_items.item_name, menu_items.name as menu_item_name, SUM(order_items.quantity) as total_quantity, SUM(COALESCE(order_items.total, order_items.price * order_items.quantity, 0)) as total_revenue')
            ->groupBy('order_items.menu_item_id', 'order_items.item_name', 'menu_items.name')
            ->get();

        $previousRows = (clone $baseQuery)
            ->whereBetween('order_invoices.created_at', [$previousMonthStart, $previousMonthEnd])
            ->selectRaw('order_items.menu_item_id, order_items.item_name, menu_items.name as menu_item_name, SUM(order_items.quantity) as total_quantity, SUM(COALESCE(order_items.total, order_items.price * order_items.quantity, 0)) as total_revenue')
            ->groupBy('order_items.menu_item_id', 'order_items.item_name', 'menu_items.name')
            ->get()
            ->keyBy(function ($row) {
                return $this->buildProductSalesKey($row);
            });

        $productRows = $currentRows->map(function ($row) use ($previousRows, $currencySymbol) {
            $productName = $this->resolveProductSalesName($row);
            $productKey = $this->buildProductSalesKey($row);
            $currentQuantity = (int) round((float) ($row->total_quantity ?? 0));
            $currentRevenue = (float) ($row->total_revenue ?? 0);
            $previousRow = $previousRows->get($productKey);
            $previousQuantity = (int) round((float) ($previousRow?->total_quantity ?? 0));
            $previousRevenue = (float) ($previousRow?->total_revenue ?? 0);

            $quantityTrend = $this->buildTrend((float) $currentQuantity, (float) $previousQuantity, 'last month');
            $revenueTrend = $this->buildTrend($currentRevenue, $previousRevenue, 'last month');

            return [
                'name' => $productName,
                'quantity_value' => $currentQuantity,
                'quantity_display' => number_format($currentQuantity),
                'revenue_value' => $currentRevenue,
                'revenue_display' => $this->formatMoney($currentRevenue, $currencySymbol),
                'quantity_trend_label' => $quantityTrend['label'],
                'quantity_trend_class' => $quantityTrend['class'],
                'revenue_trend_label' => $revenueTrend['label'],
                'revenue_trend_class' => $revenueTrend['class'],
            ];
        });

        $topSellingProducts = $productRows
            ->sortByDesc('revenue_value')
            ->take(5)
            ->values()
            ->all();

        $mostSellingProducts = $productRows
            ->sortByDesc('quantity_value')
            ->take(5)
            ->values()
            ->all();

        return [
            'topSellingProducts' => $topSellingProducts,
            'mostSellingProducts' => $mostSellingProducts,
        ];
    }

    private function resolveProductSalesName(object $row): string
    {
        $itemName = trim((string) ($row->item_name ?? ''));
        if ($itemName !== '') {
            return $itemName;
        }

        $menuItemName = trim((string) ($row->menu_item_name ?? ''));
        if ($menuItemName !== '') {
            return $menuItemName;
        }

        $menuItemId = (int) ($row->menu_item_id ?? 0);

        return $menuItemId > 0 ? 'Item #' . $menuItemId : 'Item';
    }

    private function buildProductSalesKey(object $row): string
    {
        return implode('|', [
            (string) ($row->menu_item_id ?? 0),
            trim((string) ($row->item_name ?? '')),
            trim((string) ($row->menu_item_name ?? '')),
        ]);
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

        $direction = $change > 0
            ? 'up'
            : ($change < 0 ? 'down' : 'flat');

        $prefix = $change > 0 ? '+' : '';

        return [
            'label' => $prefix . number_format($change, 1) . '% vs ' . $periodLabel,
            'class' => $direction === 'down'
                ? 'text-red-400'
                : ($direction === 'flat' ? 'text-yellow-400' : 'text-green-400'),
            'icon' => $direction === 'down'
                ? 'fas fa-arrow-down'
                : ($direction === 'flat' ? 'fas fa-minus' : 'fas fa-arrow-up'),
        ];
    }

    private function formatMoney(float $amount, string $currencySymbol): string
    {
        return $currencySymbol . $this->formatIndianNumber($amount);
    }

    private function formatIndianNumber(float $amount): string
    {
        $rounded = (string) round($amount, 0);

        if (strlen($rounded) <= 3) {
            return $rounded;
        }

        $negative = str_starts_with($rounded, '-');
        $number = $negative ? ltrim($rounded, '-') : $rounded;
        $lastThree = substr($number, -3);
        $rest = substr($number, 0, -3);
        $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
        $formatted = trim($rest . ',' . $lastThree, ',');

        return $negative ? '-' . $formatted : $formatted;
    }
}
