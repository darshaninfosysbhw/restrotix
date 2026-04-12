{{-- <div class="space-y-6 p-1">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Waiter Service Panel</h1>
            <p class="text-sm text-gray-400">Floor activity for <span
                    class="text-orange-500 font-semibold">{{ auth()->user()->branch_name ?? 'Main Dining Area' }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-green-900/30 text-green-400 rounded-full text-xs font-bold animate-pulse">●
                Service Live</span>
            <button class="p-2 bg-gray-800 border border-gray-700 rounded-lg">
                <i class="fas fa-sync-alt text-gray-400"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Active Tables</p>
                <div class="p-2 bg-blue-900/20 rounded-lg text-blue-500"><i class="fas fa-chair"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">15 / 24</h2>
            <p class="text-xs text-gray-400 mt-2 font-semibold">9 tables currently free</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Running Orders</p>
                <div class="p-2 bg-orange-900/20 rounded-lg text-orange-500"><i class="fas fa-receipt"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">28</h2>
            <p class="text-xs text-orange-500 mt-2 font-semibold">6 delayed orders</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Ready to Serve</p>
                <div class="p-2 bg-green-900/20 rounded-lg text-green-500"><i class="fas fa-bell"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">11</h2>
            <p class="text-xs text-green-500 mt-2 font-semibold">Pickup from kitchen now</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Pending Bills</p>
                <div class="p-2 bg-red-900/20 rounded-lg text-red-500"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">05</h2>
            <p class="text-xs text-red-500 mt-2 font-semibold">2 tables waiting checkout</p>
        </div>
    </div>

    <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-700 flex justify-between items-center">
            <h3 class="font-bold text-white text-lg">Live Table Queue</h3>
            <button class="text-xs font-bold text-orange-500 hover:underline">View All Tables</button>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="border-2 border-orange-500/40 rounded-xl p-4 bg-orange-500/5 card-hover">
                <div class="flex justify-between items-center mb-3">
                    <span class="px-2 py-1 bg-orange-500 text-white text-[10px] font-bold rounded">PRIORITY</span>
                    <span class="text-gray-400 text-xs font-mono">Table T-07</span>
                </div>
                <h4 class="font-bold text-white">Order #W-3102</h4>
                <ul class="mt-3 space-y-2 text-sm text-gray-300">
                    <li>1x Chicken Burger</li>
                    <li>2x Lemon Soda</li>
                    <li class="text-red-400 font-bold">Special Note: No onion</li>
                </ul>
                <button
                    class="w-full mt-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-lg transition-all">SERVE
                    NOW</button>
            </div>

            <div class="border border-gray-700 rounded-xl p-4 bg-gray-700/30 card-hover">
                <div class="flex justify-between items-center mb-3">
                    <span class="px-2 py-1 bg-green-500 text-white text-[10px] font-bold rounded">READY</span>
                    <span class="text-green-500 text-xs font-mono">Table T-03</span>
                </div>
                <h4 class="font-bold text-white">Order #W-3105</h4>
                <ul class="mt-3 space-y-2 text-sm text-gray-300">
                    <li>2x Veg Momo</li>
                    <li>1x Cold Coffee</li>
                </ul>
                <button
                    class="w-full mt-4 py-2 border border-green-500 text-green-500 hover:bg-green-500 hover:text-white text-xs font-bold rounded-lg transition-all">MARK
                    SERVED</button>
            </div>

            <div class="border border-gray-700 rounded-xl p-4 bg-gray-700/30 card-hover">
                <div class="flex justify-between items-center mb-3">
                    <span class="px-2 py-1 bg-blue-500 text-white text-[10px] font-bold rounded">NEW</span>
                    <span class="text-blue-500 text-xs font-mono">Table T-12</span>
                </div>
                <h4 class="font-bold text-white">Order #W-3110</h4>
                <ul class="mt-3 space-y-2 text-sm text-gray-300">
                    <li>1x Paneer Pizza</li>
                    <li>1x Mojito</li>
                </ul>
                <button
                    class="w-full mt-4 py-2 border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white text-xs font-bold rounded-lg transition-all">SEND
                    TO KITCHEN</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
            <h3 class="font-bold text-white mb-4">Table Status Board</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs font-bold">
                <div class="p-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-400 text-center">T-01
                    Occupied</div>
                <div class="p-3 rounded-lg bg-blue-500/10 border border-blue-500/30 text-blue-400 text-center">T-02
                    Cleaning</div>
                <div class="p-3 rounded-lg bg-gray-700/60 border border-gray-600 text-gray-300 text-center">T-03 Free
                </div>
                <div class="p-3 rounded-lg bg-orange-500/10 border border-orange-500/30 text-orange-400 text-center">
                    T-04
                    Billing</div>
                <div class="p-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-400 text-center">T-05
                    Occupied</div>
                <div class="p-3 rounded-lg bg-gray-700/60 border border-gray-600 text-gray-300 text-center">T-06 Free
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
            <h3 class="font-bold text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <button
                    class="py-3 bg-gray-700 hover:bg-gray-600 text-white text-xs font-bold rounded-lg transition-all">
                    <i class="fas fa-plus-circle mr-2 text-orange-500"></i>New Order
                </button>
                <button
                    class="py-3 bg-gray-700 hover:bg-gray-600 text-white text-xs font-bold rounded-lg transition-all">
                    <i class="fas fa-print mr-2 text-orange-500"></i>Print Bill
                </button>
                <button
                    class="py-3 bg-gray-700 hover:bg-gray-600 text-white text-xs font-bold rounded-lg transition-all">
                    <i class="fas fa-exchange-alt mr-2 text-orange-500"></i>Shift Table
                </button>
                <button
                    class="py-3 bg-gray-700 hover:bg-gray-600 text-white text-xs font-bold rounded-lg transition-all">
                    <i class="fas fa-comment-dots mr-2 text-orange-500"></i>Customer Note
                </button>
            </div>
        </div>
    </div>
</div> --}}
