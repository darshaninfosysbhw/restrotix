<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Total Employees</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $employeeStats['total'] }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-orange-500/15 text-orange-500 flex items-center justify-center">
                <i class="fas fa-users text-sm"></i>
            </div>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">On Duty</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $employeeStats['active'] }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-emerald-500/15 text-emerald-400 flex items-center justify-center">
                <i class="fas fa-check-circle text-sm"></i>
            </div>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">On Leave</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $employeeStats['on_leave'] }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-amber-400/15 text-amber-400 flex items-center justify-center">
                <i class="fas fa-calendar-minus text-sm"></i>
            </div>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Inactive</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $employeeStats['inactive'] }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-slate-500/20 text-slate-300 flex items-center justify-center">
                <i class="fas fa-user-slash text-sm"></i>
            </div>
        </div>
    </div>
</div>
