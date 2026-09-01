<div class="space-y-6 p-1">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Sales Command Center</h1>
            <p class="text-sm text-gray-400">Performance snapshot for <span
                    class="text-orange-500 font-semibold">{{ auth()->user()->branch_name ?? 'Main Outlet' }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-green-900/30 text-green-400 rounded-full text-xs font-bold animate-pulse">● Sales
                Live</span>
            <button class="p-2 bg-gray-800 border border-gray-700 rounded-lg">
                <i class="fas fa-sync-alt text-gray-400"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Today's Revenue</p>
                <div class="p-2 bg-green-900/20 rounded-lg text-green-500"><i class="fas fa-rupee-sign"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">₹84,320</h2>
            <p class="text-xs text-green-500 mt-2 font-semibold">+11.2% vs yesterday</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Orders Processed</p>
                <div class="p-2 bg-blue-900/20 rounded-lg text-blue-500"><i class="fas fa-receipt"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">246</h2>
            <p class="text-xs text-blue-400 mt-2 font-semibold">31 orders/hour avg</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Avg Bill Value</p>
                <div class="p-2 bg-purple-900/20 rounded-lg text-purple-500"><i class="fas fa-chart-line"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">₹343</h2>
            <p class="text-xs text-purple-400 mt-2 font-semibold">Peak 7PM - 9PM</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Pending Payments</p>
                <div class="p-2 bg-red-900/20 rounded-lg text-red-500"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">07</h2>
            <p class="text-xs text-red-400 mt-2 font-semibold">₹6,450 outstanding</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden">
            <div class="p-5 border-b border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-white text-lg">Live Billing Queue</h3>
                <button class="text-xs font-bold text-orange-500 hover:underline">View Full Log</button>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border-2 border-orange-500/40 rounded-xl p-4 bg-orange-500/5 card-hover">
                    <div class="flex justify-between items-center mb-3">
                        <span class="px-2 py-1 bg-orange-500 text-white text-[10px] font-bold rounded">HIGH VALUE</span>
                        <span class="text-gray-400 text-xs font-mono">Bill #BL-8912</span>
                    </div>
                    <h4 class="font-bold text-white">Table 14 Settlement</h4>
                    <p class="mt-2 text-sm text-gray-300">Card payment pending authorization from POS terminal.</p>
                    <button
                        class="w-full mt-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-lg transition-all">PROCESS
                        NOW</button>
                </div>

                <div class="border border-gray-700 rounded-xl p-4 bg-gray-700/30 card-hover">
                    <div class="flex justify-between items-center mb-3">
                        <span class="px-2 py-1 bg-green-500 text-white text-[10px] font-bold rounded">READY</span>
                        <span class="text-green-500 text-xs font-mono">Bill #BL-8910</span>
                    </div>
                    <h4 class="font-bold text-white">Takeaway Counter</h4>
                    <p class="mt-2 text-sm text-gray-300">UPI confirmed. Invoice waiting for print and handover.</p>
                    <button
                        class="w-full mt-4 py-2 border border-green-500 text-green-500 hover:bg-green-500 hover:text-white text-xs font-bold rounded-lg transition-all">PRINT
                        INVOICE</button>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
            <h3 class="font-bold text-white mb-5">Payment Mix</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs mb-1"><span class="text-gray-400">UPI</span><span
                            class="text-white font-bold">42%</span></div>
                    <div class="w-full bg-gray-700 h-2 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500" style="width: 42%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1"><span class="text-gray-400">Card</span><span
                            class="text-white font-bold">28%</span></div>
                    <div class="w-full bg-gray-700 h-2 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500" style="width: 28%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1"><span class="text-gray-400">Cash</span><span
                            class="text-white font-bold">20%</span></div>
                    <div class="w-full bg-gray-700 h-2 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-500" style="width: 20%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1"><span class="text-gray-400">Wallet</span><span
                            class="text-white font-bold">10%</span></div>
                    <div class="w-full bg-gray-700 h-2 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500" style="width: 10%"></div>
                    </div>
                </div>
            </div>
            <button
                class="w-full mt-6 py-3 border border-gray-700 hover:border-orange-500/50 hover:bg-orange-500/5 text-gray-400 hover:text-orange-500 text-xs font-bold rounded-xl transition-all uppercase tracking-widest">
                Detailed Payment Report
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
            <h3 class="font-bold text-white mb-4">Top Selling Items</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-xl">
                    <div>
                        <p class="text-sm font-bold text-gray-200">Chicken Momo</p>
                        <p class="text-[10px] text-green-400 uppercase font-black">128 sold today</p>
                    </div>
                    <span class="text-xs text-white font-bold">₹22,400</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-xl">
                    <div>
                        <p class="text-sm font-bold text-gray-200">Paneer Butter Masala</p>
                        <p class="text-[10px] text-green-400 uppercase font-black">96 sold today</p>
                    </div>
                    <span class="text-xs text-white font-bold">₹31,200</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-xl">
                    <div>
                        <p class="text-sm font-bold text-gray-200">Veg Burger</p>
                        <p class="text-[10px] text-green-400 uppercase font-black">84 sold today</p>
                    </div>
                    <span class="text-xs text-white font-bold">₹16,800</span>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
            <h3 class="font-bold text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <button class="py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all text-xs"><i
                        class="fas fa-cash-register mr-2 text-orange-500"></i>OPEN POS</button>
                <button class="py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all text-xs"><i
                        class="fas fa-undo mr-2 text-orange-500"></i>REFUND</button>
                <button class="py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all text-xs"><i
                        class="fas fa-percent mr-2 text-orange-500"></i>DISCOUNT</button>
                <button class="py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all text-xs"><i
                        class="fas fa-chart-bar mr-2 text-orange-500"></i>SUMMARY</button>
            </div>
        </div>
    </div>
</div>
