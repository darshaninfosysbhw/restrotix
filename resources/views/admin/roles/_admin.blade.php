@php
    $dashboardMetrics = $dashboardMetrics ?? [];
    $revenueMetric = $dashboardMetrics['revenue'] ?? [];
    $ordersMetric = $dashboardMetrics['orders'] ?? [];
    $averageOrderMetric = $dashboardMetrics['average_order'] ?? [];
    $branchMetric = $dashboardMetrics['branches'] ?? [];
    $staffMetric = $dashboardMetrics['staff'] ?? [];
    $qrScanStats = $qrScanStats ?? [];
    $topBranchItems = collect($topBranches ?? []);
    $topProductItems = collect($topSellingProducts ?? []);
    $mostProductItems = collect($mostSellingProducts ?? []);
@endphp

<!-- Key Metrics Cards -->
<form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="min-w-0">
        <h2 class="text-lg font-semibold text-white break-words">{{ $dashboardBranchLabel ?? 'All Branches' }} Overview</h2>
        <p class="text-xs text-gray-400">This month</p>
    </div>
    <div class="flex flex-wrap items-center gap-2 min-w-0">
        <label for="dashboardBranchFilter" class="text-sm text-gray-400">Dashboard branch</label>
        <select id="dashboardBranchFilter" name="dashboard_branch" class="min-w-0 max-w-full bg-gray-800 text-white border border-gray-700 rounded-lg px-3 py-2 text-sm">
            <option value="all" @selected(($dashboardBranchId ?? null) === null)>All Branches</option>
            @foreach ($dashboardBranches ?? [] as $dashboardBranch)
                <option value="{{ $dashboardBranch->id }}" @selected(($dashboardBranchId ?? null) === (int) $dashboardBranch->id)>{{ $dashboardBranch->branch_name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white rounded-lg px-3 py-2 text-sm">Apply</button>
    </div>
</form>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- ... same cards ... (I'll keep them concise for space) -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Total Revenue ({{ $dashboardBranchLabel ?? 'All Branches' }})</p>
                <h3 class="text-2xl font-bold text-white mt-1">{{ $revenueMetric['display'] ?? '₹0' }}</h3>
                <p class="text-xs {{ $revenueMetric['trend']['class'] ?? 'text-green-400' }} mt-2"><i
                        class="{{ $revenueMetric['trend']['icon'] ?? 'fas fa-arrow-up' }} mr-1"></i>
                    {{ $revenueMetric['trend']['label'] ?? '+0.0% vs last month' }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center"><i
                    class="fas fa-rupee-sign text-orange-500 text-xl"></i></div>
        </div>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Total Orders</p>
                <h3 class="text-2xl font-bold text-white mt-1">{{ $ordersMetric['display'] ?? '0' }}</h3>
                <p class="text-xs {{ $ordersMetric['trend']['class'] ?? 'text-green-400' }} mt-2"><i
                        class="{{ $ordersMetric['trend']['icon'] ?? 'fas fa-arrow-up' }} mr-1"></i>
                    {{ $ordersMetric['trend']['label'] ?? '+0.0% vs last month' }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center"><i
                    class="fas fa-shopping-cart text-blue-500 text-xl"></i></div>
        </div>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Avg. Order Value</p>
                <h3 class="text-2xl font-bold text-white mt-1">{{ $averageOrderMetric['display'] ?? '₹0' }}</h3>
                <p class="text-xs {{ $averageOrderMetric['trend']['class'] ?? 'text-yellow-400' }} mt-2"><i
                        class="{{ $averageOrderMetric['trend']['icon'] ?? 'fas fa-minus' }} mr-1"></i>
                    {{ $averageOrderMetric['trend']['label'] ?? '0.0% vs last month' }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center"><i
                    class="fas fa-chart-pie text-purple-500 text-xl"></i></div>
        </div>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Active Branches</p>
                <h3 class="text-2xl font-bold text-white mt-1">{{ $branchMetric['display'] ?? '0 / 0' }}</h3>
                <p class="text-xs {{ $branchMetric['note_class'] ?? 'text-green-400' }} mt-2">
                    {{ $branchMetric['note'] ?? 'No branches yet' }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center"><i
                    class="fas fa-store text-green-500 text-xl"></i></div>
        </div>
    </div>
</div>

<!-- QR Scan Activity -->
<div class="bg-gray-800 rounded-xl p-6 border border-gray-700 mb-6 card-hover">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
        <div>
            <p class="text-sm text-gray-400">QR Analytics</p>
            <h3 class="text-lg font-semibold text-white mt-1">QR Scan Activity</h3>
            <p class="text-xs text-gray-500 mt-1">Live scan counting for table QR codes across all branches</p>
        </div>
        <div class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center text-orange-500">
            <i class="fas fa-qrcode text-xl"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-gray-700/50 rounded-xl p-4 border border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400">Total QR Scans</p>
            <p class="text-2xl font-bold text-white mt-2">{{ $qrScanStats['total_scans'] ?? '0' }}</p>
            <p class="text-xs text-green-400 mt-2">All scans recorded so far</p>
        </div>

        <div class="bg-gray-700/50 rounded-xl p-4 border border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400">Today's Scans</p>
            <p class="text-2xl font-bold text-white mt-2">{{ $qrScanStats['today_scans'] ?? '0' }}</p>
            <p class="text-xs text-blue-400 mt-2">Scans since midnight</p>
        </div>

        <div class="bg-gray-700/50 rounded-xl p-4 border border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400">Unique Tables</p>
            <p class="text-2xl font-bold text-white mt-2">{{ $qrScanStats['unique_tables'] ?? '0' }}</p>
            <p class="text-xs text-yellow-400 mt-2">Different tables scanned</p>
        </div>

        <div class="bg-gray-700/50 rounded-xl p-4 border border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400">Top Scanned Table</p>
            <p class="text-xl font-bold text-white mt-2">{{ $qrScanStats['top_table'] ?? 'N/A' }}</p>
            <p class="text-xs text-orange-400 mt-2">
                {{ $qrScanStats['top_table_scans'] ?? '0' }} scans
            </p>
        </div>
    </div>
</div>

<!-- Map & Branch Performance -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 lg:col-span-2">
        <div class="flex items-center justify-between gap-3 mb-4">
            <h3 class="text-lg font-semibold text-white">Branch Locations & Performance</h3>
            <a href="{{ route('admin.branches.index') }}" class="text-sm text-orange-500 hover:text-orange-400">View All Branches &rarr;</a>
        </div>
        <div class="relative bg-gray-700 rounded-lg h-64 overflow-auto" aria-label="Branch performance overview">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-600 to-gray-800 opacity-50"></div>
            @php
                $locationBranches = collect($branchLocations ?? []);
                $originalPinPositions = [[25, 25], [67, 33], [33, 67], [75, 75]];
            @endphp
            <div class="relative h-full" style="min-width: {{ max(0, $locationBranches->count() * 45) }}px">
                @forelse ($locationBranches as $branch)
                    @php
                        if ($locationBranches->count() <= 4) {
                            [$pinX, $pinY] = $originalPinPositions[$loop->index];
                        } else {
                            $angle = (2 * M_PI * $loop->index / $locationBranches->count()) - M_PI / 2;
                            $pinX = 50 + 35 * cos($angle);
                            $pinY = 50 + 30 * sin($angle);
                        }
                        $pinColor = $branch['revenue_value'] <= 0 ? 'bg-yellow-500' : 'bg-green-500';
                    @endphp
                    <details class="absolute group hover:z-20 focus-within:z-20 open:z-20" style="left: {{ $pinX }}%; top: {{ $pinY }}%">
                        <summary class="list-none cursor-pointer w-6 h-6 rounded-full {{ $pinColor }} border-2 border-gray-900 flex items-center justify-center focus-visible:outline focus-visible:outline-orange-500 [&::-webkit-details-marker]:hidden"
                            aria-label="{{ $branch['name'] }}: {{ $branch['revenue_display'] }} this month">
                            <i class="fas fa-store text-white text-xs" aria-hidden="true"></i>
                        </summary>
                        <div class="absolute {{ $pinY < 50 ? 'top-full mt-2' : 'bottom-full mb-2' }} {{ $pinX > 50 ? 'right-0' : 'left-0' }} hidden group-hover:block group-open:block group-focus-within:block bg-gray-900 text-white text-xs rounded py-2 px-3 w-40 break-words shadow-lg">
                            <p class="font-semibold">{{ $branch['name'] }}: {{ $branch['revenue_display'] }}</p>
                            <p class="text-gray-400 mt-1">This month &middot; {{ $branch['orders_display'] }} billed orders</p>
                            <p class="text-gray-400 mt-1">{{ $branch['address'] }}</p>
                            @if ($branch['map_url'])
                                <a href="{{ $branch['map_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-block mt-2 text-orange-500 hover:text-orange-400">View location &rarr;</a>
                            @endif
                        </div>
                    </details>
                @empty
                    <p class="absolute inset-0 flex items-center justify-center px-4 text-sm text-gray-400 text-center">No branches found for the current selection.</p>
                @endforelse
                @if ($locationBranches->isNotEmpty())
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <div class="w-10 h-10 rounded-full bg-orange-500 border-4 border-gray-900 flex items-center justify-center">
                            <i class="fas fa-user-tie text-white" aria-hidden="true"></i>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Top Performing Branches</h3>
        <div class="space-y-4">
            @forelse ($topBranchItems as $branch)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white">{{ $branch['name'] ?? 'Branch' }}</p>
                        <p class="text-xs text-gray-400">Revenue: {{ $branch['revenue_display'] ?? '₹0' }}</p>
                    </div><span
                        class="text-xs {{ $branch['trend_badge_class'] ?? 'bg-green-900/50 text-green-400' }} px-2 py-1 rounded-full">{{ $branch['trend_label'] ?? '0.0% vs last month' }}</span>
                </div>
            @empty
                <div class="text-sm text-gray-400">No branch revenue data yet.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Products & Staff -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Top Selling Products</h3>
        <div class="space-y-3">
            @forelse ($topProductItems as $product)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white">{{ $product['name'] ?? 'Product' }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $product['quantity_display'] ?? '0' }} sold · Revenue
                            {{ $product['revenue_display'] ?? '₹0' }}
                        </p>
                    </div>
                    <span class="text-xs bg-green-900/50 text-green-400 px-2 py-1 rounded-full">
                        {{ $product['revenue_trend_label'] ?? '0.0% vs last month' }}
                    </span>
                </div>
            @empty
                <div class="text-sm text-gray-400">No product sales data yet.</div>
            @endforelse
        </div>
        <button class="w-full mt-4 text-sm text-orange-500 hover:text-orange-400">View Sales Report
            →</button>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Staff Overview</h3>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                <p class="text-2xl font-bold text-white">{{ $staffMetric['total'] ?? '0' }}</p>
                <p class="text-xs text-gray-400">Total Staff</p>
            </div>
            <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                <p class="text-2xl font-bold text-white">{{ $staffMetric['on_leave'] ?? '0' }}</p>
                <p class="text-xs text-gray-400">On Leave</p>
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex justify-between items-center"><span class="text-sm text-gray-400">Downtown</span><span
                    class="text-sm text-white">14/16</span><span class="text-xs text-green-400">+2</span>
            </div>
            <div class="flex justify-between items-center"><span class="text-sm text-gray-400">Westside</span><span
                    class="text-sm text-white">12/12</span><span class="text-xs text-green-400">Full</span>
            </div>
            <div class="flex justify-between items-center"><span class="text-sm text-gray-400">East
                    End</span><span class="text-sm text-white">8/10</span><span class="text-xs text-red-400">-2</span>
            </div>
            <div class="flex justify-between items-center"><span class="text-sm text-gray-400">Uptown</span><span
                    class="text-sm text-white">10/12</span><span class="text-xs text-yellow-400">-2</span>
            </div>
        </div>
        <button class="w-full mt-4 text-sm text-orange-500 hover:text-orange-400">Manage Staff
            →</button>
    </div>
</div>

<!-- Product Sales & Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 lg:col-span-2">
        <h3 class="text-lg font-semibold text-white mb-4">Most Selling Products</h3>
        <div class="space-y-3">
            @forelse ($mostProductItems as $product)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white">{{ $product['name'] ?? 'Product' }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $product['quantity_display'] ?? '0' }} sold · {{ $product['revenue_display'] ?? '₹0' }}
                        </p>
                    </div>
                    <span class="text-xs bg-blue-900/50 text-blue-400 px-2 py-1 rounded-full">
                        {{ $product['quantity_trend_label'] ?? '0.0% vs last month' }}
                    </span>
                </div>
            @empty
                <div class="text-sm text-gray-400">No product sales data yet.</div>
            @endforelse
        </div>
        <button class="w-full mt-4 text-sm text-orange-500 hover:text-orange-400">View Product Sales
            →</button>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
        <div class="space-y-3">
            <button
                class="w-full bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium py-3 px-4 rounded-lg flex items-center justify-between"><span><i
                        class="fas fa-plus-circle mr-2 text-orange-500"></i> Add New Branch</span><i
                    class="fas fa-chevron-right text-gray-400"></i></button>
            <button
                class="w-full bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium py-3 px-4 rounded-lg flex items-center justify-between"><span><i
                        class="fas fa-sync-alt mr-2 text-orange-500"></i> Sync Menu to All
                    Branches</span><i class="fas fa-chevron-right text-gray-400"></i></button>
            <button
                class="w-full bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium py-3 px-4 rounded-lg flex items-center justify-between"><span><i
                        class="fas fa-truck mr-2 text-orange-500"></i> Bulk Order Supplies</span><i
                    class="fas fa-chevron-right text-gray-400"></i></button>
            <button
                class="w-full bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium py-3 px-4 rounded-lg flex items-center justify-between"><span><i
                        class="fas fa-file-pdf mr-2 text-orange-500"></i> Generate Consolidated
                    Report</span><i class="fas fa-chevron-right text-gray-400"></i></button>
        </div>
    </div>
</div>
