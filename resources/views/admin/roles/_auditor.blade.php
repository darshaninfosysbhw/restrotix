<div class="space-y-6 p-1">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Audit Control Center</h1>
            <p class="text-sm text-gray-400">Compliance overview for <span
                    class="text-orange-500 font-semibold">{{ auth()->user()->branch_name ?? 'All Active Branches' }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-green-900/30 text-green-400 rounded-full text-xs font-bold animate-pulse">●
                Audit Live</span>
            <button class="p-2 bg-gray-800 border border-gray-700 rounded-lg">
                <i class="fas fa-sync-alt text-gray-400"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Open Findings</p>
                <div class="p-2 bg-red-900/20 rounded-lg text-red-500"><i class="fas fa-exclamation-circle"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">14</h2>
            <p class="text-xs text-red-500 mt-2 font-semibold">5 High Priority</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Checks Completed</p>
                <div class="p-2 bg-blue-900/20 rounded-lg text-blue-500"><i class="fas fa-clipboard-check"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">128</h2>
            <p class="text-xs text-green-500 mt-2 font-semibold">+18 this week</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Compliance Score</p>
                <div class="p-2 bg-green-900/20 rounded-lg text-green-500"><i class="fas fa-shield-alt"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">91%</h2>
            <p class="text-xs text-gray-400 mt-2 font-semibold italic">Target: 95%</p>
        </div>

        <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
            <div class="flex justify-between">
                <p class="text-gray-400 text-sm font-medium">Pending Approvals</p>
                <div class="p-2 bg-orange-900/20 rounded-lg text-orange-500"><i class="fas fa-user-check"></i></div>
            </div>
            <h2 class="text-3xl font-bold text-white mt-2">09</h2>
            <p class="text-xs text-orange-500 mt-2 font-semibold">2 overdue approvals</p>
        </div>
    </div>

    <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-700 flex justify-between items-center">
            <h3 class="font-bold text-white text-lg">Recent Audit Findings</h3>
            <div class="flex gap-2">
                <button class="text-xs font-bold text-orange-500 hover:underline">View Full Report</button>
            </div>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="border-2 border-red-500/30 rounded-xl p-4 bg-red-500/5 card-hover">
                <div class="flex justify-between items-center mb-3">
                    <span class="px-2 py-1 bg-red-500 text-white text-[10px] font-bold rounded">HIGH</span>
                    <span class="text-gray-400 text-xs font-mono">23 Feb 2026</span>
                </div>
                <h4 class="font-bold text-white">Cash Register Mismatch</h4>
                <p class="mt-2 text-sm text-gray-300">Counter-2 cash closing differs by ₹2,450 from POS summary.</p>
                <button
                    class="w-full mt-4 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition-all">ASSIGN
                    INVESTIGATION</button>
            </div>

            <div class="border border-gray-700 rounded-xl p-4 bg-gray-700/30 card-hover">
                <div class="flex justify-between items-center mb-3">
                    <span class="px-2 py-1 bg-yellow-500 text-white text-[10px] font-bold rounded">MEDIUM</span>
                    <span class="text-yellow-500 text-xs font-mono">22 Feb 2026</span>
                </div>
                <h4 class="font-bold text-white">Stock Reconciliation Delay</h4>
                <p class="mt-2 text-sm text-gray-300">Daily stock adjustment pending for last 2 days in kitchen store.</p>
                <button
                    class="w-full mt-4 py-2 border border-yellow-500 text-yellow-500 hover:bg-yellow-500 hover:text-white text-xs font-bold rounded-lg transition-all">FOLLOW
                    UP</button>
            </div>

            <div class="border border-gray-700 rounded-xl p-4 bg-gray-700/30 card-hover">
                <div class="flex justify-between items-center mb-3">
                    <span class="px-2 py-1 bg-green-500 text-white text-[10px] font-bold rounded">CLOSED</span>
                    <span class="text-green-500 text-xs font-mono">21 Feb 2026</span>
                </div>
                <h4 class="font-bold text-white">Invoice Approval Trail</h4>
                <p class="mt-2 text-sm text-gray-300">Approval hierarchy corrected and verified for supplier invoices.</p>
                <button
                    class="w-full mt-4 py-2 border border-green-500 text-green-500 hover:bg-green-500 hover:text-white text-xs font-bold rounded-lg transition-all">VIEW
                    CLOSURE NOTE</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
            <h3 class="font-bold text-white mb-4">Policy Exception Alerts</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-xl">
                    <div>
                        <p class="text-sm font-bold text-gray-200">Unapproved Discount Applied</p>
                        <p class="text-[10px] text-red-500 uppercase font-black">Billing desk | ₹1,200 variance</p>
                    </div>
                    <button
                        class="px-4 py-1.5 bg-orange-500 text-white text-xs font-bold rounded-lg shadow-md shadow-orange-500/20">REVIEW</button>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-xl">
                    <div>
                        <p class="text-sm font-bold text-gray-200">Late Stock Entry</p>
                        <p class="text-[10px] text-orange-500 uppercase font-black">Store ledger updated after cut-off</p>
                    </div>
                    <button class="px-4 py-1.5 border border-orange-500 text-orange-500 text-xs font-bold rounded-lg">OPEN
                        CASE</button>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
            <h3 class="font-bold text-white mb-4">Audit Task Queue</h3>
            <div class="grid grid-cols-2 gap-3 mb-3">
                <input type="text" placeholder="Task Title"
                    class="col-span-2 bg-gray-700 border border-gray-600 rounded-xl p-3 text-sm text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <select
                    class="bg-gray-700 border border-gray-600 rounded-xl p-3 text-sm text-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <option>Priority</option>
                    <option>High</option>
                    <option>Medium</option>
                    <option>Low</option>
                </select>
                <input type="date"
                    class="bg-gray-700 border border-gray-600 rounded-xl p-3 text-sm text-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <button
                class="w-full py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all">CREATE
                TASK</button>
        </div>
    </div>
</div>
