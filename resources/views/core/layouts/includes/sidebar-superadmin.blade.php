<!-- mobile overlay for sidebar (hidden by default) -->
<div id="mobileSidebarOverlay"
    class="sa-overlay fixed inset-0 bg-black/60 z-30 opacity-0 pointer-events-none transition-opacity duration-350 md:hidden">
</div>

<!-- main app container -->
<div class="flex h-screen overflow-hidden relative">

    <!-- ========== SIDEBAR (collapsible) ========== -->
    <aside id="sidebar"
        class="sa-sidebar fixed md:static inset-y-0 left-0 z-[120] w-72 bg-[#0f172a] border-r border-white/5 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-out will-change-transform flex flex-col p-4">
        <!-- logo & collapse button -->
        <div class="flex items-center justify-between mb-8 px-2">
            <div class="flex items-center">
                <a href="{{ route('superadmin.dashboard') }}" class="mr-2 sm:mr-3 inline-flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Restrotix" class="h-8 w-auto sm:h-10">
                </a>
            </div>
            <button id="mobileSidebarCloseBtn" class="md:hidden text-slate-400 hover:text-white text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- search inside sidebar (quick filter) -->
        <div class="relative mb-6">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" placeholder="Search by branch/owner..."
                class="sa-sidebar-search w-full bg-[#1e293b] border border-white/5 rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        </div>

        <!-- navigation -->
        @php
            $isMasterSettingsActive =
                request()->routeIs('superadmin.services.*') || request()->routeIs('superadmin.currencies.*');
        @endphp
        <nav class="flex-1 space-y-5 overflow-y-auto pr-1">
            <div class="space-y-1">
                <p class="px-3 py-1 text-[11px] font-semibold tracking-[0.12em] uppercase text-slate-500">Overview</p>
                <a href="{{ route('superadmin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5"></i>Master Dashboard
                </a>
            </div>

            <div class="space-y-1">
                <p class="px-3 py-1 text-[11px] font-semibold tracking-[0.12em] uppercase text-slate-500">Operations</p>
                <a href="{{ route('superadmin.tenants.index') }}"
                    class="sidebar-link {{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}">
                    <i class="fas fa-building w-5"></i>Tenant Governance
                </a>
                {{-- <a href="#" class="sidebar-link"><i class="fas fa-server w-5"></i>Infrastructure</a> --}}
                <a href="#" class="sidebar-link"><i class="fas fa-globe w-5"></i>Marketplace Control</a>
            </div>

            <div class="space-y-1">
                <p class="px-3 py-1 text-[11px] font-semibold tracking-[0.12em] uppercase text-slate-500">Growth &
                    Revenue</p>
                <a href="#" class="sidebar-link"><i class="fas fa-credit-card w-5"></i>Plans & Revenue</a>
            </div>

            <div class="space-y-1">
                <p class="px-3 py-1 text-[11px] font-semibold tracking-[0.12em] uppercase text-slate-500">Configuration
                </p>

                <details class="sa-dropdown-container" {{ $isMasterSettingsActive ? 'open' : '' }}>
                    <summary class="sa-dropdown-trigger sidebar-link w-full">
                        <i class="fas fa-cogs w-5"></i>
                        <span class="sidebar-label whitespace-nowrap">Master Settings</span>
                        <i
                            class="sa-dropdown-arrow fas fa-chevron-right ml-auto text-[11px] transition-transform duration-200"></i>
                    </summary>

                    <div id="master-settings-menu"
                        class="sa-dropdown-menu pl-2 ml-3 border-l border-white/10 space-y-2 mb-9">
                        <a href="{{ route('superadmin.services.index') }}"
                            class="sidebar-link {{ request()->routeIs('superadmin.services.*') ? 'active' : '' }}">
                            <i class="fas fa-layer-group w-3"></i>Service Master
                        </a>
                        <a href="{{ route('superadmin.currencies.index') }}"
                            class="sidebar-link {{ request()->routeIs('superadmin.currencies.*') ? 'active' : '' }}">
                            <i class="fas fa-coins w-3"></i>Currency Management
                        </a>
                        <a href="{{ route('superadmin.plans.index') }}"
                            class="sidebar-link {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
                            <i class="fas fa-cubes w-3"></i>Plan Management
                        </a>
                        <a href="{{ route('superadmin.paymentGateway.index') }}"
                            class="sidebar-link {{ request()->routeIs('superadmin.paymentGateway.*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card w-3"></i>Payment Gateway
                        </a>
                        <a href="#" class="sidebar-link"><i class="fas fa-cog w-3"></i>System Settings</a>
                    </div>
                </details>
            </div>
        </nav>

        <!-- bottom user -->
        <div class="border-t border-white/5 pt-4 mt-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-orange-500/20 flex items-center justify-center">
                    <i class="fas fa-user-shield text-orange-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </aside>
