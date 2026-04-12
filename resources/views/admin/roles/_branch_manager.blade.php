<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-700/50 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Branch Overview</h1>
            <p class="text-sm text-gray-400 font-medium">Managing: <span
                    class="text-orange-500 font-bold">{{ auth()->user()->branch_name ?? 'Downtown Outlet' }}</span></p>
        </div>
        <div class="flex gap-3">
            <div class="bg-gray-800 p-3 rounded-xl border border-gray-700">
                <p class="text-[10px] text-gray-500 uppercase font-bold">Today's Sales</p>
                <p class="text-green-500 font-bold">$1,240.00</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700/50 shadow-sm relative overflow-hidden">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Today's Transactions</p>
                <i class="fas fa-receipt text-blue-500/30"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">142</h2>
            <div class="mt-4 flex items-center text-xs text-green-500 font-bold">
                <i class="fas fa-arrow-up mr-1"></i> 8.5% <span class="text-gray-500 font-normal ml-2 text-[10px]">than
                    yesterday</span>
            </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700/50 shadow-sm">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Staff Present</p>
                <i class="fas fa-users text-orange-500/30"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">08 / 12</h2>
            <p class="text-[10px] text-gray-500 mt-4 uppercase font-bold tracking-widest">4 Shift pending</p>
        </div>

        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700/50 shadow-sm">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Live Tables</p>
                <i class="fas fa-chair text-purple-500/30"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">15 / 24</h2>
            <div class="w-full bg-gray-700 h-1.5 mt-4 rounded-full overflow-hidden">
                <div class="bg-purple-500 h-full" style="width: 62%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-gray-800 rounded-2xl border border-gray-700/50 p-6 shadow-xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-white font-bold text-lg">Current Branch Orders</h3>
                <span class="text-xs text-orange-500 font-bold cursor-pointer hover:underline">View All Orders →</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead>
                        <tr class="border-b border-gray-800 text-[10px] uppercase font-bold tracking-widest">
                            <th class="pb-3">Order ID</th>
                            <th class="pb-3 text-center">Table</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (range(1, 4) as $index)
                            <tr class="border-b border-gray-800/50 hover:bg-gray-700/50 transition-colors">
                                <td class="py-4 font-bold text-white">#ORD-50{{ $index }}</td>
                                <td class="py-4 text-center">T-0{{ $index + 2 }}</td>
                                <td class="py-4 text-center">
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-bold {{ $index % 2 == 0 ? 'bg-orange-500/10 text-orange-500 border border-orange-500/20' : 'bg-green-500/10 text-green-500 border border-green-500/20' }}">
                                        {{ $index % 2 == 0 ? 'COOKING' : 'SERVED' }}
                                    </span>
                                </td>
                                <td class="py-4 text-right font-bold text-white">$45.00</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-gray-800 rounded-2xl border border-gray-700/50 p-6 shadow-xl">
            <h3 class="text-white font-bold text-lg mb-6 flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-500"></i> Stock To Reorder
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-xl border border-gray-700/30">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-gray-800 rounded-lg flex items-center justify-center text-gray-500">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div>
                            <p class="text-white font-bold text-sm">Refined Oil</p>
                            <p class="text-[10px] text-red-500 font-bold uppercase tracking-tighter italic">Critical: 2L
                                Left</p>
                        </div>
                    </div>
                    <button
                        class="bg-gray-800 hover:bg-orange-600 text-white text-[10px] font-black px-4 py-2 rounded-lg transition-all border border-gray-700">ACTION</button>
                </div>
            </div>

            <button
                class="w-full mt-6 py-3 border border-gray-700 hover:border-orange-500/50 hover:bg-orange-500/5 text-gray-500 hover:text-orange-500 text-xs font-bold rounded-xl transition-all uppercase tracking-widest">
                Full Inventory Report
            </button>
        </div>

    </div>
</div>
