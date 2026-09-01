<main class="sa-main flex-1 flex flex-col overflow-hidden bg-[#0f172a]">

    <!-- header with mobile toggle and search -->
    <header
        class="sa-header relative z-40 bg-[#1e293b]/80 backdrop-blur-sm border-b border-white/5 px-4 md:px-6 py-3 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4 flex-1">
            <button id="mobileSidebarOpenBtn" class="text-slate-300 text-xl md:hidden">
                <i class="fas fa-bars"></i>
            </button>
            <!-- search with filter (by branch/owner) -->
            <div class="relative flex-1 max-w-md hidden sm:block">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" placeholder="Search by branch or owner..."
                    class="sa-search-input w-full bg-[#0f172a] border border-white/5 rounded-full py-2.5 pl-11 pr-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>
        </div>
        <!-- Country Switcher -->
        <form action="{{ route('superadmin.switch.country') }}" method="POST" class="hidden md:block">
            @csrf

            <div class="relative">
                <select name="country_id" onchange="this.form.submit()"
                    class="sa-country-switch appearance-none bg-[#0f172a] border border-white/10 text-white text-sm
            rounded-xl px-4 py-2.5 pr-10 focus:outline-none focus:ring-1 focus:ring-orange-500
            hover:border-orange-500 transition-all cursor-pointer">

                    <option value="0">🌍 Global View</option>
                    @foreach (\App\Models\Country::where('is_active', 1)->get() as $country)
                        <option value="{{ $country->id }}"
                            {{ session('active_country_id') == $country->id ? 'selected' : '' }}>

                            {{-- अगर flag में emoji है तो वो, वरना iso_code दिखाओ --}}
                            {{ $country->flag ?? $country->iso_code }} {{ $country->name }}

                        </option>
                    @endforeach
                </select>

                <!-- dropdown arrow -->
                <div class="sa-country-arrow pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
        </form>
        <div class="flex items-center gap-5 ">
            <span class="text-sm text-slate-300 hidden md:inline"><i
                    class="fas fa-circle text-orange-500 text-xs mr-1"></i> 99.98% uptime</span>
            <button id="sa-theme-toggle" class="text-slate-300 hover:text-orange-500 text-xl transition-colors">
                <i id="sa-theme-icon" class="fas fa-sun"></i>
            </button>
            <div class="relative">
                <i class="fas fa-bell text-slate-300 text-xl hover:text-orange-500 cursor-pointer"></i>
                <span
                    class="absolute -top-1 -right-1 w-4 h-4 bg-orange-500 text-white text-xs rounded-full flex items-center justify-center">4</span>
            </div>
            <details class="relative group">
                <summary
                    class="list-none [&::-webkit-details-marker]:hidden flex items-center gap-2 cursor-pointer select-none">
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                        <i class="fas fa-crown text-white text-xs"></i>
                    </div>
                    <span class="hidden lg:inline text-sm font-medium text-white">{{ auth()->user()->name }}</span>
                    <i
                        class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-150 group-open:rotate-180"></i>
                </summary>
                <div
                    class="sa-profile-menu absolute right-0 top-full mt-2 w-44 overflow-hidden rounded-xl border border-white/10 bg-[#0f172a] shadow-2xl z-50">
                    <a href="{{ route('superadmin.profile') }}"
                        class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-white/5 transition-colors">Profile</a>
                    <a href="#"
                        class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-white/5 transition-colors">Settings</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="block px-4 py-2.5 text-sm text-rose-300 hover:bg-rose-500/10 transition-colors w-full text-left">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </details>
        </div>
    </header>
