<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-700/50 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Accounts Dashboard</h1>
            <p class="text-sm text-gray-400 font-medium">
                Branch: <span class="text-orange-500 font-bold">{{ auth()->user()->branch_name ?? 'Main Outlet' }}</span>
            </p>
        </div>
        <div class="flex gap-3">
            <button
                class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all">
                <i class="fas fa-file-invoice mr-2"></i> Add Expense
            </button>
            <button
                class="bg-gray-800 hover:bg-gray-700 text-white text-sm font-semibold px-4 py-2 rounded-lg border border-gray-700 transition-all">
                <i class="fas fa-download mr-2 text-orange-500"></i> Export Report
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700/50 shadow-sm">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Today's Revenue</p>
                <i class="fas fa-rupee-sign text-green-500/30"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">₹1,24,500</h2>
            <p class="text-xs text-green-500 mt-3 font-bold"><i class="fas fa-arrow-up mr-1"></i> 9.1% vs yesterday</p>
        </div>

        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700/50 shadow-sm">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Today's Expenses</p>
                <i class="fas fa-wallet text-red-500/30"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">₹42,300</h2>
            <p class="text-xs text-red-400 mt-3 font-bold">Utilities + Vendor Payments</p>
        </div>

        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700/50 shadow-sm">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Net Profit</p>
                <i class="fas fa-chart-line text-blue-500/30"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">₹82,200</h2>
            <p class="text-xs text-blue-400 mt-3 font-bold">Margin: 66.0%</p>
        </div>

        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700/50 shadow-sm">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Pending Dues</p>
                <i class="fas fa-exclamation-triangle text-yellow-500/30"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">₹18,750</h2>
            <p class="text-xs text-yellow-400 mt-3 font-bold">6 unpaid invoices</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-gray-800 rounded-2xl border border-gray-700/50 p-6 shadow-xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-white font-bold text-lg">Recent Financial Entries</h3>
                <span class="text-xs text-orange-500 font-bold cursor-pointer hover:underline">View Ledger →</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead>
                        <tr class="border-b border-gray-800 text-[10px] uppercase font-bold tracking-widest">
                            <th class="pb-3">Date</th>
                            <th class="pb-3">Category</th>
                            <th class="pb-3">Description</th>
                            <th class="pb-3 text-center">Type</th>
                            <th class="pb-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-800/50 hover:bg-gray-700/50 transition-colors">
                            <td class="py-4 text-white font-semibold">23 Feb</td>
                            <td>Sales</td>
                            <td>Dine-in settlement</td>
                            <td class="text-center"><span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-500/10 text-green-500 border border-green-500/20">Credit</span>
                            </td>
                            <td class="py-4 text-right text-white font-bold">₹22,450</td>
                        </tr>
                        <tr class="border-b border-gray-800/50 hover:bg-gray-700/50 transition-colors">
                            <td class="py-4 text-white font-semibold">23 Feb</td>
                            <td>Purchase</td>
                            <td>Vegetable supplier payment</td>
                            <td class="text-center"><span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">Debit</span>
                            </td>
                            <td class="py-4 text-right text-white font-bold">₹8,100</td>
                        </tr>
                        <tr class="border-b border-gray-800/50 hover:bg-gray-700/50 transition-colors">
                            <td class="py-4 text-white font-semibold">22 Feb</td>
                            <td>Expense</td>
                            <td>Electricity bill</td>
                            <td class="text-center"><span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">Debit</span>
                            </td>
                            <td class="py-4 text-right text-white font-bold">₹5,400</td>
                        </tr>
                        <tr class="hover:bg-gray-700/50 transition-colors">
                            <td class="py-4 text-white font-semibold">22 Feb</td>
                            <td>Sales</td>
                            <td>Online order payouts</td>
                            <td class="text-center"><span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-500/10 text-green-500 border border-green-500/20">Credit</span>
                            </td>
                            <td class="py-4 text-right text-white font-bold">₹13,760</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-gray-800 rounded-2xl border border-gray-700/50 p-6 shadow-xl">
            <h3 class="text-white font-bold text-lg mb-5">Expense Breakdown</h3>
            <div class="space-y-4 text-sm">
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="text-gray-400">Raw Materials</span>
                    <span class="text-white font-bold">₹21,000</span>
                </div>
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="text-gray-400">Utilities</span>
                    <span class="text-white font-bold">₹9,800</span>
                </div>
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="text-gray-400">Staff Expense</span>
                    <span class="text-white font-bold">₹7,500</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Miscellaneous</span>
                    <span class="text-white font-bold">₹4,000</span>
                </div>
            </div>
            <button
                class="w-full mt-6 py-3 border border-gray-700 hover:border-orange-500/50 hover:bg-orange-500/5 text-gray-400 hover:text-orange-500 text-xs font-bold rounded-xl transition-all uppercase tracking-widest">
                Full Expense Report
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-800 rounded-2xl border border-gray-700/50 p-6 shadow-xl">
            <h3 class="text-white font-bold text-lg mb-5">Pending Invoices</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-700/40 rounded-lg border border-gray-700/40">
                    <div>
                        <p class="text-sm text-white font-semibold">INV-3021</p>
                        <p class="text-[11px] text-gray-400">Vendor: Metro Foods</p>
                    </div>
                    <p class="text-sm text-yellow-400 font-bold">₹6,200</p>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-700/40 rounded-lg border border-gray-700/40">
                    <div>
                        <p class="text-sm text-white font-semibold">INV-3014</p>
                        <p class="text-[11px] text-gray-400">Vendor: Fresh Dairy</p>
                    </div>
                    <p class="text-sm text-yellow-400 font-bold">₹4,850</p>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-700/40 rounded-lg border border-gray-700/40">
                    <div>
                        <p class="text-sm text-white font-semibold">INV-2998</p>
                        <p class="text-[11px] text-gray-400">Vendor: Spice Hub</p>
                    </div>
                    <p class="text-sm text-yellow-400 font-bold">₹7,700</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-2xl border border-gray-700/50 p-6 shadow-xl">
            <h3 class="text-white font-bold text-lg mb-5">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <button class="bg-gray-700 hover:bg-gray-600 text-white text-sm py-3 rounded-lg">
                    <i class="fas fa-plus-circle mr-2 text-orange-500"></i> Add Entry
                </button>
                <button class="bg-gray-700 hover:bg-gray-600 text-white text-sm py-3 rounded-lg">
                    <i class="fas fa-file-invoice mr-2 text-orange-500"></i> New Invoice
                </button>
                <button class="bg-gray-700 hover:bg-gray-600 text-white text-sm py-3 rounded-lg">
                    <i class="fas fa-money-check-alt mr-2 text-orange-500"></i> Reconcile
                </button>
                <button class="bg-gray-700 hover:bg-gray-600 text-white text-sm py-3 rounded-lg">
                    <i class="fas fa-chart-pie mr-2 text-orange-500"></i> P&L Report
                </button>
            </div>
        </div>
    </div>

</div>
