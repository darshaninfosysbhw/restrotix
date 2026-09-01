<div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-orange-500/20 flex items-center justify-center">
                <i class="fas fa-user text-orange-500 text-xl"></i>
            </div>

            <div>
                <p class="text-xs uppercase tracking-wide text-gray-400">My Profile</p>
                <h1 class="text-2xl font-bold text-white">{{ $profile['name'] }}</h1>
                <p class="text-sm text-gray-400">{{ $profile['email'] }}</p>
            </div>
        </div>

        <button type="submit" form="profileForm"
            class="bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-save mr-2"></i>Save Changes
        </button>

    </div>
</div>
