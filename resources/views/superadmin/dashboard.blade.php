@extends('core.layouts.superadmin')
@section('content')
    <div class="relative z-0 flex-1 overflow-y-auto p-4 md:p-6 space-y-6">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="glass-panel card-hover p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-400">Total Registered Restaurants</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['total_tenants'] ?? 0 }}</p>
                        <p class="text-xs text-slate-400 mt-2">Includes active, trial and expired</p>
                        <a href="{{ route('superadmin.tenants.index') }}"
                            class="inline-block text-[11px] text-orange-500 hover:text-orange-400 mt-2">View Details</a>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center">
                        <i class="fas fa-layer-group text-orange-500"></i>
                    </div>
                </div>
            </div>
            <div class="glass-panel card-hover p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-400">Active Restaurants</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['active_tenants'] ?? 0 }}</p>
                        @php $activeGrowth = $stats['active_tenants_growth_percent'] ?? 0; @endphp
                        <p class="text-xs mt-2 {{ $activeGrowth >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            <i class="fas {{ $activeGrowth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ $activeGrowth >= 0 ? '+' : '' }}{{ $activeGrowth }}% vs last month
                        </p>
                        <a href="{{ route('superadmin.tenants.index', ['status' => 'active']) }}"
                            class="inline-block text-[11px] text-orange-500 hover:text-orange-400 mt-2">View Details</a>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center">
                        <i class="fas fa-store text-orange-500"></i>
                    </div>
                </div>
            </div>
            <div class="glass-panel card-hover p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-400">Urgent Renewals</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['urgent_renewals'] ?? 0 }}</p>
                        <p class="text-xs text-amber-400 mt-2">
                            {{ ($stats['urgent_renewals'] ?? 0) > 0 ? 'Trials expiring in next 7 days' : 'No urgent renewals right now' }}
                        </p>
                        <a href="{{ route('superadmin.tenants.index', ['status' => 'trial', 'expiring_days' => 7]) }}"
                            class="inline-block text-[11px] text-orange-500 hover:text-orange-400 mt-2">View Details</a>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-orange-500"></i>
                    </div>
                </div>
            </div>
            <div class="glass-panel card-hover p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-400">Monthly Revenue (MTD)</p>
                        <p class="text-2xl font-bold text-white mt-1">
                            {{ $activeCountry ? $activeCountry->currency->symbol : '$' }}
                            {{ number_format($stats['monthly_revenue_mtd'] ?? 0, 2) }}
                        </p>
                        <p class="text-xs text-orange-500 mt-2">
                            {{ ($stats['monthly_revenue_mtd'] ?? 0) > 0 ? 'Active subscription billings this month' : 'No billed subscriptions this month' }}
                        </p>
                        <a href="" class="inline-block text-[11px] text-orange-500 hover:text-orange-400 mt-2">View
                            Details</a>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center">
                        <i class="fas fa-sack-dollar text-orange-500"></i>
                    </div>
                </div>
            </div>
            <div class="glass-panel card-hover p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-400">ARPU (MTD)</p>
                        <p class="text-2xl font-bold text-white mt-1">
                            {{ $activeCountry ? $activeCountry->currency->symbol : '$' }}{{ number_format($stats['arpu_mtd'] ?? 0, 2) }}
                        </p>
                        <p class="text-xs text-orange-500 mt-2">
                            {{ ($stats['arpu_mtd'] ?? 0) > 0 ? 'Average revenue per active restaurant' : 'ARPU will appear after billings' }}
                        </p>
                        <a href="" class="inline-block text-[11px] text-orange-500 hover:text-orange-400 mt-2">View
                            Details</a>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center">
                        <i class="fas fa-chart-line text-orange-500"></i>
                    </div>
                </div>
            </div>
            <div class="glass-panel card-hover p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-400">System Usage (Live)</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['active_service_subscriptions'] ?? 0 }}</p>
                        <p class="text-xs text-orange-500 mt-2">
                            {{ ($stats['active_service_subscriptions'] ?? 0) > 0 ? 'Active service subscriptions' : 'No live subscription activity' }}
                        </p>
                        <a href="" class="inline-block text-[11px] text-orange-500 hover:text-orange-400 mt-2">View
                            Details</a>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center">
                        <i class="fas fa-bolt text-orange-500"></i>
                    </div>
                </div>
            </div>
            <div class="glass-panel card-hover p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-400">Active Socket Connections</p>
                        <p class="text-2xl font-bold text-white mt-1">1,422</p>
                        <p class="text-xs text-orange-500 mt-2">Real-time live tenant activity</p>
                        <a href="" class="inline-block text-[11px] text-orange-500 hover:text-orange-400 mt-2">View
                            Details</a>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center">
                        <i class="fas fa-plug text-orange-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-panel p-5">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-white">Tenant Governance · Restaurant Owners</h3>
                    <p class="text-xs text-slate-400 mt-1">Filter by period to inspect onboarding trend.</p>
                </div>
                <div class="flex items-center flex-wrap gap-2 text-xs">
                    <a href="{{ route('superadmin.dashboard', ['range' => 'today']) }}"
                        class="px-3 py-1 rounded-full transition {{ ($selectedRange ?? 'month') === 'today' ? 'bg-orange-500/20 text-orange-400' : 'bg-white/5 hover:bg-white/10 text-slate-300' }}">
                        Today
                    </a>
                    <a href="{{ route('superadmin.dashboard', ['range' => 'week']) }}"
                        class="px-3 py-1 rounded-full transition {{ ($selectedRange ?? 'month') === 'week' ? 'bg-orange-500/20 text-orange-400' : 'bg-white/5 hover:bg-white/10 text-slate-300' }}">
                        This Week
                    </a>
                    <a href="{{ route('superadmin.dashboard', ['range' => 'month']) }}"
                        class="px-3 py-1 rounded-full transition {{ ($selectedRange ?? 'month') === 'month' ? 'bg-orange-500/20 text-orange-400' : 'bg-white/5 hover:bg-white/10 text-slate-300' }}">
                        This Month
                    </a>
                    <a href="{{ route('superadmin.tenants.index') }}"
                        class="bg-white/5 hover:bg-white/10 text-slate-300 px-3 py-1 rounded-full transition">
                        Manage Restaurants
                    </a>
                    <span class="bg-orange-500/10 text-orange-500 px-3 py-1 rounded-full">Active</span>
                    <span class="bg-amber-400/10 text-amber-400 px-3 py-1 rounded-full">Trial</span>
                    <span class="bg-rose-400/10 text-rose-400 px-3 py-1 rounded-full">Expired</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm table-auto">
                    <thead class="text-xs text-slate-400 border-b border-white/5">
                        <tr>
                            <th class="text-left py-3 pr-4 font-medium">#</th>
                            <th class="text-left py-3 px-4 font-medium">Tenant Name</th>
                            <th class="text-left py-3 px-4 font-medium">Subscription Health</th>
                            <th class="text-left py-3 px-4 font-medium">Branch Count</th>
                            <th class="text-left py-3 px-4 font-medium">Plan</th>
                            <th class="text-left py-3 px-4 font-medium">Joined</th>
                            <th class="text-left py-3 pl-4 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse (($recentTenants ?? collect()) as $tenant)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-4 pr-4 text-slate-300">{{ $loop->iteration }}</td>
                                <td class="py-4 px-4 font-medium text-white">{{ $tenant['name'] }}</td>
                                <td class="py-4 px-4">
                                    @if ($tenant['status'] === 'Active')
                                        <span class="badge-orange px-2 py-1 rounded-full text-xs">Active</span>
                                    @elseif($tenant['status'] === 'Trial')
                                        <span class="badge-amber px-2 py-1 rounded-full text-xs">Trial</span>
                                    @else
                                        <span
                                            class="bg-rose-400/10 text-rose-400 px-2 py-1 rounded-full text-xs">Expired</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4"><span
                                        class="bg-white/5 px-2 py-1 rounded-full text-xs">{{ $tenant['branches_count'] }}
                                        branches</span></td>
                                <td class="py-4 px-4">{{ $tenant['plan'] }}</td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <p class="text-slate-300">{{ $tenant['joined_at'] }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $tenant['joined_bs'] }}</p>
                                </td>
                                <td class="py-4 pl-4">
                                    <div class="flex items-center gap-2 whitespace-nowrap">
                                        <a href="{{ route('superadmin.tenants.index') }}"
                                            class="text-xs bg-white/5 hover:bg-white/10 text-slate-300 px-3 py-1.5 rounded-full transition">
                                            Manage
                                        </a>
                                        @if (!empty($tenant['owner_user_id']))
                                            <a href="{{ route('impersonate', $tenant['owner_user_id']) }}"
                                                class="text-xs bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 px-3 py-1.5 rounded-full transition inline-flex items-center"><i
                                                    class="fas fa-user-lock mr-1"></i>Login as Owner</a>
                                        @else
                                            <button type="button" disabled
                                                class="text-xs bg-slate-500/10 text-slate-500 px-3 py-1.5 rounded-full border border-slate-500/20 cursor-not-allowed inline-flex items-center"><i
                                                    class="fas fa-user-lock mr-1"></i>Login as Owner</button>
                                        @endif
                                        <button class="text-xs bg-white/5 hover:bg-white/10 px-3 py-1.5 rounded-full"><i
                                                class="fas fa-cog"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    No restaurants found for selected range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="glass-panel p-5 lg:col-span-2">
                <h3 class="text-lg font-semibold text-white mb-4">Plan Editor · Define limits</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-white/5 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-white">Enterprise</span>
                            <span class="badge-orange text-xs px-2 py-0.5">most popular</span>
                        </div>
                        <div class="mt-3 space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Max branches</span>
                                <span class="text-white font-medium">unlimited</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Staff accounts</span>
                                <span class="text-white font-medium">250</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Offline sync</span>
                                <span class="text-orange-500"><i class="fas fa-check"></i> enabled</span>
                            </div>
                        </div>
                        <button
                            class="w-full mt-3 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 text-sm py-2 rounded-lg transition">Edit
                            limits</button>
                    </div>
                    <div class="border border-white/5 rounded-xl p-4">
                        <span class="font-medium text-white">Professional</span>
                        <div class="mt-3 space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Max branches</span>
                                <span class="text-white font-medium">5</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Staff accounts</span>
                                <span class="text-white font-medium">50</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Offline sync</span>
                                <span class="text-slate-400"><i class="fas fa-times"></i> disabled</span>
                            </div>
                        </div>
                        <button
                            class="w-full mt-3 bg-white/5 hover:bg-white/10 text-slate-300 text-sm py-2 rounded-lg transition">Edit
                            limits</button>
                    </div>
                </div>
            </div>
            <div class="glass-panel p-5">
                <h3 class="text-lg font-semibold text-white mb-4">Revenue Breakdown</h3>
                <div class="flex justify-center mb-4">
                    <div
                        class="w-24 h-24 rounded-full border-4 border-orange-500/30 border-t-orange-500 animate-spin-slow">
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300"><i
                                class="fas fa-circle text-orange-500 text-xs mr-2"></i>Subscriptions</span>
                        <span class="text-white font-medium">NPR
                            {{ number_format($stats['monthly_revenue_mtd'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300"><i class="fas fa-circle text-orange-500 text-xs mr-2"></i>ARPU
                            (MTD)</span>
                        <span class="text-white font-medium">NPR {{ number_format($stats['arpu_mtd'] ?? 0, 2) }}</span>
                    </div>
                </div>
                <a href="" class="inline-block text-xs text-orange-500 hover:text-orange-400 mt-4">View Details</a>
            </div>
        </div>

        <div class="glass-panel p-5">
            <h3 class="text-lg font-semibold text-white mb-4">Top 5 Service Add-ons</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
                @forelse (($serviceUsage ?? collect())->take(5) as $addon)
                    <div class="bg-white/5 border border-white/10 rounded-xl px-3 py-3">
                        <p class="text-sm text-slate-300 truncate">{{ $addon['name'] }}</p>
                        <p class="text-lg font-semibold text-white mt-1">{{ $addon['total'] }}</p>
                        <p class="text-xs text-slate-400 mt-1">Active subscriptions</p>
                    </div>
                @empty
                    <div class="col-span-full text-sm text-slate-400 bg-white/5 rounded-lg px-3 py-3">
                        No add-on subscriptions yet. Popular services will appear here.
                    </div>
                @endforelse
            </div>
            <a href="" class="inline-block text-xs text-orange-500 hover:text-orange-400 mt-4">View Details</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="glass-panel p-5">
                <h3 class="text-lg font-semibold text-white mb-3">Global Marketplace · Suppliers</h3>
                <p class="text-xs text-slate-400 mb-4">Add once, available to all restaurant branches</p>
                <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                    <div class="flex items-center justify-between bg-white/5 p-3 rounded-lg">
                        <span class="text-sm text-white">Metro Foods</span>
                        <span class="text-xs bg-orange-500/10 text-orange-500 px-2 py-0.5 rounded-full">active</span>
                    </div>
                    <div class="flex items-center justify-between bg-white/5 p-3 rounded-lg">
                        <span class="text-sm text-white">Sysco India</span>
                        <span class="text-xs bg-orange-500/10 text-orange-500 px-2 py-0.5 rounded-full">active</span>
                    </div>
                    <div class="flex items-center justify-between bg-white/5 p-3 rounded-lg">
                        <span class="text-sm text-white">Local Co-op (organic)</span>
                        <span class="text-xs bg-orange-500/10 text-orange-500 px-2 py-0.5 rounded-full">new</span>
                    </div>
                </div>
                <button
                    class="w-full mt-4 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 text-sm py-2.5 rounded-lg transition"><i
                        class="fas fa-plus mr-2"></i>Add new supplier</button>
            </div>
            <div class="glass-panel p-5">
                <h3 class="text-lg font-semibold text-white mb-3">Send Global Notification</h3>
                <textarea rows="3"
                    class="w-full bg-[#0f172a] border border-white/5 rounded-lg p-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                    placeholder="Type your message to all restaurant owners..."></textarea>
                <div class="flex flex-wrap gap-3 mt-4">
                    <button
                        class="bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 px-4 py-2 rounded-lg text-sm transition"><i
                            class="fas fa-bullhorn mr-2"></i>Broadcast to all</button>
                    <button class="bg-white/5 hover:bg-white/10 text-slate-300 px-4 py-2 rounded-lg text-sm transition"><i
                            class="fas fa-users mr-2"></i>Specific branch managers</button>
                </div>
            </div>
        </div>

        <div class="glass-panel p-5">
            <h3 class="text-lg font-semibold text-white mb-4">Branch Performance Heatmap · Global Accounts</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    class="bg-gradient-to-br from-orange-500/10 to-transparent p-4 rounded-xl border border-orange-500/20">
                    <p class="text-sm text-slate-400">B1 (Delhi)</p>
                    <p class="text-2xl font-bold text-white">$48.2k</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-xs text-orange-500"><i class="fas fa-arrow-up"></i> +14%</span>
                        <span class="text-xs text-slate-400">vs last month</span>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-orange-500/10 to-transparent p-4 rounded-xl border border-orange-500/20">
                    <p class="text-sm text-slate-400">B2 (Mumbai)</p>
                    <p class="text-2xl font-bold text-white">$36.7k</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-xs text-orange-500"><i class="fas fa-arrow-up"></i> +8%</span>
                        <span class="text-xs text-slate-400">vs last month</span>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-orange-500/10 to-transparent p-4 rounded-xl border border-orange-500/20">
                    <p class="text-sm text-slate-400">B3 (Bangalore)</p>
                    <p class="text-2xl font-bold text-white">$24.1k</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-xs text-orange-500"><i class="fas fa-arrow-up"></i> +21%</span>
                        <span class="text-xs text-slate-400">vs last month</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-xs text-slate-400 border-t border-white/5 pt-3 flex items-center gap-2">
                <i class="fas fa-chart-line text-orange-500"></i>
                <span>Global accounts (B1, B2, B3) consolidated: $109k MTD</span>
            </div>
        </div>

        <div class="glass-panel p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">System Health</h3>
                <span class="text-xs text-slate-400">Technical metrics moved here</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Uptime</p>
                    <p class="text-xl font-semibold text-white mt-1">99.98%</p>
                    <p class="text-xs text-emerald-400 mt-1">30d avg</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">DB Load</p>
                    <p class="text-xl font-semibold text-white mt-1">23%</p>
                    <p class="text-xs text-slate-400 mt-1">~1.2k qps</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Socket Connections</p>
                    <p class="text-xl font-semibold text-white mt-1">1,422</p>
                    <p class="text-xs text-slate-400 mt-1">real-time billing</p>
                </div>
            </div>
        </div>

    </div>
@endsection
