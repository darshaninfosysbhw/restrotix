<div class="space-y-6 p-1">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Store Keeper Dashboard</h1>
            <p class="text-sm text-gray-400">Inventory monitoring for <span
                    class="text-orange-500 font-semibold">{{ auth()->user()->branch_name ?? 'Central Store' }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-green-900/30 text-green-400 rounded-full text-xs font-bold animate-pulse">● Stock
                Live</span>
            <button class="p-2 bg-gray-800 border border-gray-700 rounded-lg">
                <i class="fas fa-sync-alt text-gray-400"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Total SKUs</p>
                <div class="p-2 bg-blue-900/20 rounded-lg text-blue-500"><i class="fas fa-boxes"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">428</h2>
            <p class="text-xs text-gray-400 mt-2 font-semibold">Across all categories</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Low Stock Items</p>
                <div class="p-2 bg-yellow-900/20 rounded-lg text-yellow-500"><i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">16</h2>
            <p class="text-xs text-yellow-400 mt-2 font-semibold">5 critical today</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Inward Today</p>
                <div class="p-2 bg-green-900/20 rounded-lg text-green-500"><i class="fas fa-arrow-down"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">29</h2>
            <p class="text-xs text-green-500 mt-2 font-semibold">Delivered batches</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Outward Today</p>
                <div class="p-2 bg-orange-900/20 rounded-lg text-orange-500"><i class="fas fa-arrow-up"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">41</h2>
            <p class="text-xs text-orange-500 mt-2 font-semibold">Issued to kitchen/bar</p>
        </div>
    </div>

    <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-700 flex justify-between items-center">
            <h3 class="font-bold text-white text-lg">Critical Reorder List</h3>
            <button class="text-xs font-bold text-orange-500 hover:underline">View Full Stock Report</button>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="border-2 border-red-500/40 rounded-xl p-4 bg-red-500/5 card-hover">
                <div class="flex justify-between items-center mb-3">
                    <span class="px-2 py-1 bg-red-500 text-white text-[10px] font-bold rounded">CRITICAL</span>
                    <span class="text-gray-400 text-xs font-mono">SKU-0192</span>
                </div>
                <h4 class="font-bold text-white">Cooking Oil (15L)</h4>
                <p class="mt-2 text-sm text-gray-300">Current stock: 2 can only. Daily usage is high.</p>
                <button
                    class="w-full mt-4 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition-all">RAISE
                    PO</button>
            </div>

            <div class="border border-gray-700 rounded-xl p-4 bg-gray-700/30 card-hover">
                <div class="flex justify-between items-center mb-3">
                    <span class="px-2 py-1 bg-yellow-500 text-white text-[10px] font-bold rounded">LOW</span>
                    <span class="text-yellow-500 text-xs font-mono">SKU-0410</span>
                </div>
                <h4 class="font-bold text-white">Chicken Breast</h4>
                <p class="mt-2 text-sm text-gray-300">Current stock: 6 kg. Reorder threshold is 10 kg.</p>
                <button
                    class="w-full mt-4 py-2 border border-yellow-500 text-yellow-500 hover:bg-yellow-500 hover:text-white text-xs font-bold rounded-lg transition-all">CREATE
                    REQUEST</button>
            </div>

            <div class="border border-gray-700 rounded-xl p-4 bg-gray-700/30 card-hover">
                <div class="flex justify-between items-center mb-3">
                    <span class="px-2 py-1 bg-blue-500 text-white text-[10px] font-bold rounded">WATCH</span>
                    <span class="text-blue-500 text-xs font-mono">SKU-0074</span>
                </div>
                <h4 class="font-bold text-white">Basmati Rice</h4>
                <p class="mt-2 text-sm text-gray-300">Current stock: 18 kg. Trend indicates 2-day balance.</p>
                <button
                    class="w-full mt-4 py-2 border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white text-xs font-bold rounded-lg transition-all">SET
                    ALERT</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
            <h3 class="font-bold text-white mb-4">Recent Stock Movements</h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-xl">
                    <div>
                        <p class="text-white font-bold">Tomato - 30 kg</p>
                        <p class="text-[10px] text-green-400 uppercase font-black">Inward | Metro Foods</p>
                    </div>
                    <span class="text-xs text-gray-300">09:20 AM</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-xl">
                    <div>
                        <p class="text-white font-bold">Flour - 20 kg</p>
                        <p class="text-[10px] text-orange-400 uppercase font-black">Outward | Kitchen</p>
                    </div>
                    <span class="text-xs text-gray-300">10:05 AM</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-xl">
                    <div>
                        <p class="text-white font-bold">Soft Drinks - 4 Crates</p>
                        <p class="text-[10px] text-green-400 uppercase font-black">Inward | Beverage Hub</p>
                    </div>
                    <span class="text-xs text-gray-300">11:15 AM</span>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
            <h3 class="font-bold text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <button class="py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all text-xs">
                    <i class="fas fa-plus-circle mr-2 text-orange-500"></i>ADD STOCK
                </button>
                <button class="py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all text-xs">
                    <i class="fas fa-dolly mr-2 text-orange-500"></i>ISSUE ITEM
                </button>
                <button class="py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all text-xs">
                    <i class="fas fa-clipboard-check mr-2 text-orange-500"></i>VERIFY BATCH
                </button>
                <button class="py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all text-xs">
                    <i class="fas fa-file-export mr-2 text-orange-500"></i>EXPORT LOG
                </button>
            </div>
        </div>
    </div>
</div>
