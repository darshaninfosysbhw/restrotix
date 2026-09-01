<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-700/50 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Purchase Dashboard</h1>
            <p class="text-sm text-gray-400 font-medium">
                Branch: <span class="text-orange-500 font-bold">{{ auth()->user()->branch_name ?? 'Main Branch' }}</span>
            </p>
        </div>
        <button
            class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all">
            <i class="fas fa-plus-circle mr-2"></i> New Purchase Order
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-[#1e293b] p-6 rounded-2xl border border-gray-700/50">
            <div class="flex justify-between items-start">
                <p class="text-sm text-gray-400">Pending PR</p>
                <i class="fas fa-file-signature text-orange-500/40"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">12</h2>
            <p class="text-xs text-orange-500 mt-3 font-semibold">3 urgent approvals</p>
        </div>

        <div class="bg-[#1e293b] p-6 rounded-2xl border border-gray-700/50">
            <div class="flex justify-between items-start">
                <p class="text-sm text-gray-400">Open POs</p>
                <i class="fas fa-truck-loading text-blue-500/40"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">18</h2>
            <p class="text-xs text-blue-400 mt-3 font-semibold">7 expected today</p>
        </div>

        <div class="bg-[#1e293b] p-6 rounded-2xl border border-gray-700/50">
            <div class="flex justify-between items-start">
                <p class="text-sm text-gray-400">Monthly Spend</p>
                <i class="fas fa-rupee-sign text-green-500/40"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">₹3,42,800</h2>
            <p class="text-xs text-green-400 mt-3 font-semibold">+6.2% vs last month</p>
        </div>

        <div class="bg-[#1e293b] p-6 rounded-2xl border border-gray-700/50">
            <div class="flex justify-between items-start">
                <p class="text-sm text-gray-400">Low Stock Items</p>
                <i class="fas fa-exclamation-triangle text-red-500/40"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">9</h2>
            <p class="text-xs text-red-400 mt-3 font-semibold">2 critical items</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-[#1e293b] rounded-2xl border border-gray-700/50 p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold text-white">Recent Purchase Orders</h3>
                <span class="text-xs text-orange-500 font-bold cursor-pointer hover:underline">View All →</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="border-b border-gray-800 text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="pb-3">PO ID</th>
                            <th class="pb-3">Supplier</th>
                            <th class="pb-3">Expected Date</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-800/60">
                            <td class="py-4 font-semibold text-white">PO-1024</td>
                            <td>Metro Foods</td>
                            <td>24 Feb 2026</td>
                            <td><span
                                    class="px-2 py-1 rounded text-xs bg-yellow-500/10 text-yellow-400 border border-yellow-500/30">In
                                    Transit</span></td>
                            <td class="text-right font-semibold text-white">₹48,500</td>
                        </tr>
                        <tr class="border-b border-gray-800/60">
                            <td class="py-4 font-semibold text-white">PO-1023</td>
                            <td>Fresh Farm Co.</td>
                            <td>23 Feb 2026</td>
                            <td><span
                                    class="px-2 py-1 rounded text-xs bg-green-500/10 text-green-400 border border-green-500/30">Delivered</span>
                            </td>
                            <td class="text-right font-semibold text-white">₹27,800</td>
                        </tr>
                        <tr>
                            <td class="py-4 font-semibold text-white">PO-1022</td>
                            <td>Spice Hub</td>
                            <td>25 Feb 2026</td>
                            <td><span
                                    class="px-2 py-1 rounded text-xs bg-blue-500/10 text-blue-400 border border-blue-500/30">Approved</span>
                            </td>
                            <td class="text-right font-semibold text-white">₹19,200</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-[#1e293b] rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-bold text-white mb-4">Low Stock Alert</h3>
            <div class="space-y-3">
                <div class="p-3 rounded-lg bg-gray-900/50 border border-gray-700/40">
                    <p class="text-sm font-semibold text-white">Cooking Oil</p>
                    <p class="text-xs text-red-400">Only 5L left</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-900/50 border border-gray-700/40">
                    <p class="text-sm font-semibold text-white">Basmati Rice</p>
                    <p class="text-xs text-orange-400">Only 12kg left</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-900/50 border border-gray-700/40">
                    <p class="text-sm font-semibold text-white">Chicken</p>
                    <p class="text-xs text-red-400">Only 8kg left</p>
                </div>
            </div>
            <button
                class="w-full mt-5 py-2.5 rounded-lg border border-gray-700 hover:border-orange-500/40 text-sm text-gray-300 hover:text-orange-400 transition">
                Generate Reorder List
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-[#1e293b] rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-bold text-white mb-4">Supplier Comparison</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-gray-300 border-b border-gray-800 pb-2">
                    <span>Metro Foods</span><span class="text-green-400">On-time: 96%</span>
                </div>
                <div class="flex justify-between text-gray-300 border-b border-gray-800 pb-2">
                    <span>Fresh Farm Co.</span><span class="text-yellow-400">On-time: 89%</span>
                </div>
                <div class="flex justify-between text-gray-300">
                    <span>Spice Hub</span><span class="text-green-400">On-time: 94%</span>
                </div>
            </div>
        </div>

        <div class="bg-[#1e293b] rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-bold text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <button class="bg-gray-800 hover:bg-gray-700 text-white text-sm py-3 rounded-lg"><i
                        class="fas fa-plus mr-2 text-orange-500"></i>Create PR</button>
                <button class="bg-gray-800 hover:bg-gray-700 text-white text-sm py-3 rounded-lg"><i
                        class="fas fa-check-circle mr-2 text-orange-500"></i>Approve PO</button>
                <button class="bg-gray-800 hover:bg-gray-700 text-white text-sm py-3 rounded-lg"><i
                        class="fas fa-file-export mr-2 text-orange-500"></i>Export Report</button>
                <button class="bg-gray-800 hover:bg-gray-700 text-white text-sm py-3 rounded-lg"><i
                        class="fas fa-users mr-2 text-orange-500"></i>Suppliers</button>
            </div>
        </div>
    </div>

</div>
