<div class="space-y-6">
    @php
        $managerOperational = $managerOperational ?? [];
        $dashboardMetrics = $dashboardMetrics ?? [];
        $staffMetric = $dashboardMetrics['staff'] ?? [];
        $todaySales = $managerOperational['today_sales'] ?? '₹0';
        $todayOrders = $managerOperational['today_orders'] ?? '0';
        $pendingOrders = $managerOperational['pending_orders'] ?? '0';
        $activeKots = $managerOperational['active_kots'] ?? '0';
        $occupiedTables = (int) ($managerOperational['occupied_tables'] ?? 0);
        $totalTables = (int) ($managerOperational['total_tables'] ?? 0);
        $tableUsagePercent = (int) ($managerOperational['table_usage_percent'] ?? 0);
        $recentOrders = $managerOperational['recent_orders'] ?? collect();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="card-hover bg-gray-800 p-6 rounded-lg border border-gray-700/50 shadow-sm relative overflow-hidden">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Today's Sales</p>
                <i class="fas fa-receipt text-blue-500/30"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">{{ $todaySales }}</h2>
            <p class="text-[10px] text-gray-500 mt-4 uppercase font-bold tracking-widest">{{ $todayOrders }} orders today</p>
        </div>

        <div class="card-hover bg-gray-800 p-6 rounded-lg border border-gray-700/50 shadow-sm">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Staff Present</p>
                <i class="fas fa-users text-orange-500/30"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">{{ $staffMetric['total'] ?? '0' }}</h2>
            <p class="text-[10px] text-gray-500 mt-4 uppercase font-bold tracking-widest">{{ $staffMetric['on_leave'] ?? '0' }} on leave</p>
        </div>

        <div class="card-hover bg-gray-800 p-6 rounded-lg border border-gray-700/50 shadow-sm">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Live Tables</p>
                <i class="fas fa-chair text-purple-500/30"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">{{ $occupiedTables }} / {{ $totalTables }}</h2>
            <div class="w-full bg-gray-700 h-1.5 mt-4 rounded-full overflow-hidden">
                <div class="bg-purple-500 h-full" style="width: {{ $tableUsagePercent }}%"></div>
            </div>
        </div>

        <div class="card-hover bg-gray-800 p-6 rounded-lg border border-gray-700/50 shadow-sm">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Active KOTs</p>
                <i class="fas fa-fire-burner text-red-500/50"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">{{ $activeKots }}</h2>
            <p class="text-[10px] text-red-400 mt-4 uppercase font-bold tracking-widest">In Kitchen</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="card-hover min-w-0 overflow-hidden bg-gray-800 rounded-lg border border-gray-700/50 p-6 shadow-xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-white font-bold text-lg">Current Branch Orders</h3>
                <a href="{{ route('manager.orders.history') }}" class="text-xs text-orange-500 font-bold cursor-pointer hover:underline">View All Orders →</a>
            </div>
            <div class="min-w-0 overflow-x-auto xl:overflow-x-hidden">
                <table class="min-w-[600px] w-full table-fixed text-left text-sm text-gray-400 xl:!min-w-0">
                    <thead>
                        <tr class="border-b border-gray-800 text-[10px] uppercase font-bold tracking-widest">
                            <th class="w-[30%] px-1 pb-3">Order ID</th>
                            <th class="w-[17%] px-1 pb-3 text-center">Table</th>
                            <th class="w-[27%] px-1 pb-3 text-center">Status</th>
                            <th class="w-[26%] px-1 pb-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr class="branch-order-row border-b border-gray-800/50 hover:bg-gray-700/50 transition-colors">
                                <td class="truncate px-1 py-4 font-bold text-white">#ORD-{{ substr((string) ($order->order_number ?: $order->id), -4) }}</td>
                                <td class="truncate px-1 py-4 text-center">{{ $order->table_number ?: 'Takeaway' }}</td>
                                <td class="px-1 py-4 text-center">
                                    <span
                                        class="whitespace-nowrap rounded border border-orange-500/20 bg-orange-500/10 px-1 py-0.5 text-[9px] font-bold text-orange-500">
                                        {{ strtoupper($order->status ?: 'pending') }}
                                    </span>
                                </td>
                                <td class="truncate px-1 py-4 text-right font-bold text-white">{{ number_format((float) $order->grand_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-gray-500">No orders found for this branch.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-hover bg-gray-800 rounded-lg border border-gray-700/50 p-6 shadow-xl">
            <h3 class="text-white font-bold text-lg mb-6 flex items-center gap-2">
                <i class="fas fa-bell text-orange-500"></i> Order Attention
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-xl border border-gray-700/30">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-gray-800 rounded-lg flex items-center justify-center text-gray-500">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <p class="text-white font-bold text-sm">Pending payments</p>
                            <p class="text-[10px] text-orange-500 font-bold uppercase tracking-tighter italic">{{ $pendingOrders }} orders need attention</p>
                        </div>
                    </div>
                    <a href="{{ route('manager.orders.history') }}"
                        class="bg-gray-800 hover:bg-orange-600 text-white text-[10px] font-black px-4 py-2 rounded-lg transition-all border border-gray-700">VIEW</a>
                </div>
            </div>

            <p class="w-full mt-6 py-3 text-gray-500 text-xs font-bold text-center uppercase tracking-widest">
                Live branch status
            </p>
        </div>

    </div> 
</div>
