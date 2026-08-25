@extends('core.layouts.admin')

@section('content')
    @php
        $filterState = $filters ?? [];
        $orderPaginator = $ordersPaginator ?? null;
        $orderTotalCount = $orderPaginator?->total() ?? ($orderCount ?? 0);
        $orderFirstItem = $orderPaginator?->firstItem() ?? 0;
        $orderLastItem = $orderPaginator?->lastItem() ?? 0;
    @endphp

    <div class="order-history-page flex-1 overflow-y-auto bg-gray-900 p-6 space-y-6"
        data-order-history-data='@json($orderHistoryData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'>
        <div class="space-y-6">
            <form id="orderHistoryFiltersForm" method="GET" action="{{ route('admin.orders.history') }}" class="space-y-6">
                <input type="hidden" name="status" id="orderHistoryStatusFilter" value="{{ $filterState['status'] ?? 'all' }}">

                <section class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Order Management</p>
                            <h1 class="mt-1 text-2xl font-bold text-white md:text-3xl">Order History</h1>
                            <p class="mt-2 text-sm text-gray-400">
                                Track every bill, payment, and table order in one place.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <div
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-700 bg-gray-900 px-4 py-2.5 text-sm font-medium text-gray-200">
                                <i class="fas fa-calendar-days text-xs text-gray-400"></i>
                                <input type="date" name="date_from" value="{{ $filterState['date_from'] ?? '' }}"
                                    class="bg-transparent text-sm text-gray-200 outline-none ring-0 border-0 p-0 focus:ring-0">
                                <span class="text-gray-500">to</span>
                                <input type="date" name="date_to" value="{{ $filterState['date_to'] ?? '' }}"
                                    class="bg-transparent text-sm text-gray-200 outline-none ring-0 border-0 p-0 focus:ring-0">
                            </div>

                            <div
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-700 bg-gray-900 px-4 py-2.5 text-sm font-medium text-gray-200">
                                <i class="fas fa-location-dot text-xs text-gray-400"></i>
                                <select name="branch_id"
                                    class="min-w-[11rem] bg-transparent text-sm text-gray-200 outline-none ring-0 border-0 p-0 focus:ring-0">
                                    <option value="">All Branches</option>
                                    @foreach ($branches ?? [] as $branch)
                                        <option value="{{ $branch['id'] }}"
                                            @selected((string) ($filterState['branch_id'] ?? '') === (string) $branch['id'])>
                                            {{ $branch['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down text-[10px] text-gray-500"></i>
                            </div>

                            <div class="relative" data-order-export-wrapper>
                                <button id="orderHistoryExportToggle" type="button"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-orange-500/30 bg-orange-500/10 px-4 py-2.5 text-sm font-medium text-orange-500 transition hover:bg-orange-500/20">
                                    <i class="fas fa-download text-xs"></i>
                                    Export
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </button>
                                <div id="orderHistoryExportMenu"
                                    class="absolute right-0 top-full z-30 mt-2 hidden w-48 overflow-hidden rounded-xl border border-gray-700 bg-gray-900 p-1 shadow-2xl">
                                    <button type="submit" name="format" value="csv"
                                        formaction="{{ route('admin.orders.history.export') }}" formmethod="GET"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-gray-200 transition hover:bg-orange-500/10 hover:text-orange-400">
                                        <i class="fas fa-file-csv text-xs text-orange-400"></i>
                                        CSV
                                    </button>
                                    <button type="submit" name="format" value="xls"
                                        formaction="{{ route('admin.orders.history.export') }}" formmethod="GET"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-gray-200 transition hover:bg-sky-500/10 hover:text-sky-400">
                                        <i class="fas fa-file-excel text-xs text-sky-400"></i>
                                        Excel (.xls)
                                    </button>
                                    <button type="submit" name="format" value="pdf"
                                        formaction="{{ route('admin.orders.history.export') }}" formmethod="GET"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-gray-200 transition hover:bg-rose-500/10 hover:text-rose-400">
                                        <i class="fas fa-file-pdf text-xs text-rose-400"></i>
                                        PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($summaryCards as $card)
                        <article class="rounded-xl border border-gray-700 bg-gray-800 p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-400">{{ $card['label'] }}</p>
                                    <p class="mt-1 text-2xl font-bold text-white">{{ $card['value'] }}</p>
                                    <p class="mt-2 text-xs {{ $card['deltaClass'] }}">{{ $card['delta'] }}</p>
                                </div>
                                <div class="flex h-9 w-9 items-center justify-center rounded-full {{ $card['iconClass'] }}">
                                    <i class="fas {{ $card['icon'] }} text-sm"></i>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>

                <section class="bg-gray-800 border border-gray-700 rounded-xl p-5">
                    <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach ($statusTabs as $tab)
                                @php
                                    $toneDot = [
                                        'orange' => 'bg-orange-500',
                                        'emerald' => 'bg-emerald-500',
                                        'amber' => 'bg-amber-500',
                                        'rose' => 'bg-rose-500',
                                    ][$tab['tone']] ?? 'bg-gray-500';
                                    $statusValue = match ($tab['label']) {
                                        'All' => 'all',
                                        'Completed' => 'completed',
                                        'Pending' => 'pending',
                                        'Cancelled' => 'cancelled',
                                        default => strtolower(str_replace(' ', '_', $tab['label'])),
                                    };
                                @endphp
                                <button type="button" data-order-status="{{ $statusValue }}"
                                    class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm transition {{ $tab['active'] ? 'border-orange-500/30 bg-orange-500/10 text-orange-500' : 'border-gray-700 bg-gray-900 text-gray-300 hover:bg-gray-800' }}">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $toneDot }}"></span>
                                    {{ $tab['label'] }}
                                </button>
                            @endforeach
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <select name="order_type"
                                class="h-11 rounded-lg border border-gray-700 bg-gray-900 px-4 text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="all" @selected(($filterState['order_type'] ?? 'all') === 'all')>All Order Types</option>
                                <option value="dine_in" @selected(($filterState['order_type'] ?? 'all') === 'dine_in')>Dine In</option>
                                <option value="takeaway" @selected(($filterState['order_type'] ?? 'all') === 'takeaway')>Take Away</option>
                                <option value="online" @selected(($filterState['order_type'] ?? 'all') === 'online')>Online</option>
                            </select>

                            <select name="payment_status"
                                class="h-11 rounded-lg border border-gray-700 bg-gray-900 px-4 text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="all" @selected(($filterState['payment_status'] ?? 'all') === 'all')>All Payment Status</option>
                                <option value="paid" @selected(($filterState['payment_status'] ?? 'all') === 'paid')>Paid</option>
                                <option value="pending" @selected(($filterState['payment_status'] ?? 'all') === 'pending')>Pending</option>
                                <option value="partially_paid" @selected(($filterState['payment_status'] ?? 'all') === 'partially_paid')>Partially Paid</option>
                                <option value="refunded" @selected(($filterState['payment_status'] ?? 'all') === 'refunded')>Refunded</option>
                            </select>

                            <button id="orderHistorySearchReset" type="button"
                                class="inline-flex h-11 items-center gap-2 rounded-lg border border-gray-700 bg-gray-900 px-4 text-sm text-gray-200 transition hover:bg-gray-800">
                                <i class="fas fa-rotate-left text-xs text-gray-400"></i>
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-700 bg-gray-800">
                    <div class="flex flex-col gap-3 border-b border-gray-700 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-base font-semibold text-white md:text-lg">Recent Orders</h2>
                                <span
                                    class="rounded-full border border-orange-500/30 bg-orange-500/10 px-2.5 py-1 text-xs text-orange-500">
                                    Total : {{ $orderTotalCount }}
                                </span>
                            </div>
                            <p id="orderHistoryResultsCount" class="mt-1 text-sm text-gray-400">
                                @if ($orderTotalCount > 0 && $orderFirstItem > 0)
                                    Showing {{ $orderFirstItem }} to {{ $orderLastItem }} of {{ $orderTotalCount }} orders
                                @else
                                    Showing 0 of {{ $orderTotalCount }} orders
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
                            <div class="relative w-full sm:w-80">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input id="orderHistoryTableSearch" name="search" type="text"
                                    value="{{ $filterState['search'] ?? '' }}"
                                    placeholder="Search order, table, customer..."
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                            <button type="submit"
                                class="px-3 py-2 rounded-lg text-xs bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/20 transition">
                                Search
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto overflow-y-visible">
                        <table class="w-full text-sm">
                            <thead class="text-xs text-gray-400 border-b border-gray-700 uppercase tracking-wide">
                                <tr>
                                    <th class="text-left py-3 pl-5 pr-4 font-medium">Order #</th>
                                    <th class="text-left py-3 px-4 font-medium">Table</th>
                                    <th class="text-left py-3 px-4 font-medium">Customer / Guest</th>
                                    <th class="text-left py-3 px-4 font-medium">Source</th>
                                    <th class="text-left py-3 px-4 font-medium">Items</th>
                                    <th class="text-left py-3 px-4 font-medium">Amount</th>
                                    <th class="text-left py-3 px-4 font-medium">Status</th>
                                    <th class="text-left py-3 px-4 font-medium">Paid</th>
                                    <th class="text-left py-3 px-4 font-medium">Time</th>
                                    <th class="text-left py-3 pl-8 pr-5 font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/80">
                                @foreach ($orders as $order)
                                    <tr data-order-history-row data-order-key="{{ $order['order_no'] }}"
                                        class="order-history-row cursor-pointer transition hover:bg-orange-500/5">
                                        <td class="py-3 pl-5 pr-4 text-gray-300">
                                            <div class="font-medium text-white">{{ $order['order_no'] }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center rounded-full border border-orange-500/30 bg-orange-500/10 px-2.5 py-1 text-xs text-gray-200">
                                                {{ $order['table'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-white">{{ $order['customer'] }}</div>
                                            @if ($order['subtext'] !== '')
                                                <div class="mt-1 text-xs text-gray-400">{{ $order['subtext'] }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="inline-flex items-center gap-2 text-gray-200">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-500/15 text-orange-500">
                                                    <i class="fas fa-bell-concierge text-[11px]"></i>
                                                </span>
                                                {{ $order['source'] }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-white">{{ $order['items'] }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-white">{{ $order['amount'] }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium {{ $order['statusClass'] }}">
                                                <span class="h-2 w-2 rounded-full bg-current"></span>
                                                {{ $order['status'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium {{ $order['paidClass'] }}">
                                                <span class="h-2 w-2 rounded-full bg-current"></span>
                                                {{ $order['paid'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="text-gray-300">{{ $order['time'] }}</div>
                                        </td>
                                        <td class="py-3 pl-8 pr-5 text-right">
                                            <button type="button" data-order-open data-order-key="{{ $order['order_no'] }}"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-orange-500/30 bg-orange-500/10 text-orange-500 transition hover:bg-orange-500/20">
                                                <i class="fas fa-eye text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="orderHistoryNoResultRow" class="{{ $displayedOrderCount > 0 ? 'hidden' : '' }}">
                                    <td colspan="10" class="py-6 text-center text-sm text-gray-400">
                                        No orders found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if ($orderPaginator && $orderPaginator->hasPages())
                        <div class="flex flex-col gap-3 border-t border-gray-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <p id="orderHistoryFooterCount" class="text-sm text-gray-400">
                                @if ($orderTotalCount > 0 && $orderFirstItem > 0)
                                    Showing {{ $orderFirstItem }} to {{ $orderLastItem }} of {{ $orderTotalCount }} orders
                                @else
                                    Showing 0 of {{ $orderTotalCount }} orders
                                @endif
                            </p>
                            <x-core::ui.pagination :paginator="$orderPaginator" :show-summary="false" class="ml-auto" />
                        </div>
                    @endif
                </section>
            </form>
        </div>

        @include('modules.orders.partials.history-drawer')
    </div>
@endsection
