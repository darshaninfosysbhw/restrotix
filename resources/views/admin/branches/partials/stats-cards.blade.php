<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Total Branches</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-orange-500/15 text-orange-500 flex items-center justify-center">
                <i class="fas fa-code-branch text-sm"></i>
            </div>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Active</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $stats['active'] }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-emerald-500/15 text-emerald-400 flex items-center justify-center">
                <i class="fas fa-check-circle text-sm"></i>
            </div>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Under Setup</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $stats['setup'] }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-amber-400/15 text-amber-400 flex items-center justify-center">
                <i class="fas fa-tools text-sm"></i>
            </div>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Inactive</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $stats['inactive'] }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-slate-500/20 text-slate-300 flex items-center justify-center">
                <i class="fas fa-pause-circle text-sm"></i>
            </div>
        </div>
    </div>
</div>
