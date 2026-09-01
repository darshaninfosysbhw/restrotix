<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Tables</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats->total }}</p>
        </div>
        <div class="w-9 h-9 rounded-full bg-orange-500/15 text-orange-500 flex items-center justify-center ">
            <i class="fas fa-chair text-sm"></i>
        </div>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Available</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats->available }}</p>
        </div>
        <div class="w-9 h-9 rounded-full bg-green-500/15 flex items-center justify-center text-green-400">
            <i class="fas fa-check-circle text-sm"></i>
        </div>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Reserved</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats->reserved }}</p>
        </div>
        <div class="w-9 h-9 rounded-full bg-yellow-500/15 flex items-center justify-center text-yellow-400">
            <i class="fas fa-clock text-sm"></i>
        </div>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Booked</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats->occupied }}</p>
        </div>
        <div class="w-9 h-9 rounded-full bg-red-500/15 flex items-center justify-center text-red-400">
            <i class="fas fa-times-circle text-sm"></i>
        </div>
    </div>

</div>
