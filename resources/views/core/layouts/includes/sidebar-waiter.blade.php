<!-- Mobile Sidebar (hidden by default) -->
<div id="mobileSidebar"
    class="fixed inset-y-0 left-0 w-64 bg-gray-800 border-r border-gray-700 z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden flex flex-col">

    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-700">
        <div class="flex items-center">
            <div
                class="w-8 h-8 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center mr-3">
                <i class="fas fa-utensils text-white text-sm"></i>
            </div>
            <span class="text-xl font-bold text-white">Waiter<span class="text-orange-500">Panel</span></span>
        </div>
        <button id="closeSidebarBtn" class="text-gray-400 hover:text-orange-500">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        @php
            $services = $activeServiceSlugs ?? [];
            $userRole = auth()->user()->role;
        @endphp

        <a href="{{ route('admin.waiter.index') }}"
            class="sidebar-item {{ request()->routeIs('admin.waiter.index') ? 'active text-orange-500 bg-gray-700/50' : 'text-gray-300 hover:text-orange-500' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg">
            <i class="fas fa-tachometer-alt w-5 mr-3"></i>
            <span class="sidebar-label-text">Dashboard</span>
        </a>

        @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'waiter')
            <a href="{{ route('waiter.tables.index') }}"
                class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                <i class="fas fa-chair w-5 mr-3"></i>
                <span class="sidebar-label-text">Table</span>
            </a>
        @endif

        @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'waiter')
            <a href="{{ route('order.index') }}"
                class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                <i class="fas fa-chair w-5 mr-3"></i>
                <span class="sidebar-label-text">Order</span>
            </a>
        @endif

        <a href="#"
            class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
            <i class="fas fa-cog w-5 mr-3"></i>
            <span class="sidebar-label-text">Settings</span>
        </a>
    </nav>

    <div class="px-4 py-4 border-t border-gray-700">
        <div class="flex items-center">
            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                <i class="fas fa-user text-white text-sm"></i>
            </div>
            <div class="ml-3 sidebar-label-text">
                <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 uppercase">{{ str_replace('_', ' ', $userRole) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Backdrop for mobile sidebar -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-transparent bg-opacity-50 z-40 hidden transition-opacity md:hidden">
</div>

<!-- Main Container -->
<div class="flex h-screen overflow-hidden bg-gray-900">

    <aside id="sidebar" class="hidden md:flex md:flex-col w-64 bg-gray-800 border-r border-gray-700">
        <button id="desktopToggleBtn">
            <i class="fas fa-angle-double-left text-xs" id="toggleIcon"></i>
        </button>

        <div class="flex items-center px-6 py-5 border-b border-gray-700">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center mr-3 flex-shrink-0">
                    <i class="fas fa-utensils text-white text-sm"></i>
                </div>
                <span class="sidebar-label text-xl font-bold text-white whitespace-nowrap">
                    Waiter<span class="text-orange-500">Panel</span>
                </span>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            @php
                $services = $activeServiceSlugs ?? [];
                $userRole = auth()->user()->role;
            @endphp

            <a href="{{ route('admin.waiter.index') }}"
                class="sidebar-item {{ request()->routeIs('admin.waiter.index') ? 'active text-orange-500 bg-gray-700/50' : 'text-gray-300 hover:text-orange-500' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                <span class="sidebar-label-text">Dashboard</span>
            </a>

            {{-- ===========================This Step taken for fast work ======================================== --}}
            {{-- // tabel (is not final) --}}

            @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'waiter')
                <a href="{{ route('waiter.tables.index') }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                    <i class="fas fa-chair w-5 mr-3"></i>
                    <span class="sidebar-label-text">Table</span>
                </a>
            @endif

            @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'waiter')
                <a href="{{ route('order.index') }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                    <i class="fas fa-chair w-5 mr-3"></i>
                    <span class="sidebar-label-text">Order</span>
                </a>
            @endif

            {{-- @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'waiter')
                <div class="dropdown-container">
                    <button
                        class="dropdown-trigger sidebar-item w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg focus:outline-none transition-all">
                        <div class="flex items-center">
                            <i class="fas fa-chart-pie w-5 mr-3"></i>
                            <span class="sidebar-label-text">Menu Mangement</span>
                        </div>
                        <div class="flex items-center sidebar-label-text">
                            <i
                                class="fas fa-chevron-right text-[10px] transition-transform duration-200 trigger-arrow"></i>
                        </div>
                    </button>
                    <div class="dropdown-menu hidden pl-12 space-y-1">

                        <a href="{{ route('admin.menu.categories.index') }}"
                            class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors">Categories</a>

                        <a href="{{ route('menu.items') }}"
                            class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors">Menu
                            Items</a>

                        <a href="{{ route('menu.preview') }}"
                            class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors">Menu
                            Preview</a>
                    </div>
                </div>
            @endif --}}

            {{-- ===========================Working Area===================== --}}

            {{-- @if (in_array($userRole, ['admin', 'manager', 'superadmin']))
                @php $isInventoryActive = in_array('table', $services); @endphp
                <a href="{{ $isInventoryActive ? route('admin.tables.index') : 'javascript:void(0)' }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isInventoryActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                    onclick="{{ !$isInventoryActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                    <i class="fas fa-chair w-5 mr-3"></i>
                    <span class="sidebar-label-text">Table</span>
                    @if (!$isInventoryActive)
                        <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px]"></i>
                    @endif
                </a>
            @endif --}}


            @if ($userRole == 'admin' || $userRole == 'superadmin')
                <div class="dropdown-container">
                    <button
                        class="dropdown-trigger sidebar-item w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg focus:outline-none transition-all">
                        <div class="flex items-center">
                            <i class="fas fa-cog w-5 mr-3"></i>
                            <span class="sidebar-label-text">Settings</span>
                        </div>
                        <div class="flex items-center sidebar-label-text">
                            <i class="fas fa-chevron-right text-[10px] transition-transform duration-200 trigger-arrow"></i>
                        </div>
                    </button>
                    <div class="dropdown-menu hidden pl-12 space-y-1">
                        <a href="{{ route('admin.branches.payment-gateways') }}"
                            class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors {{ request()->routeIs('admin.branches.payment-gateways*') ? 'text-orange-500' : '' }}">
                            Payment Settings
                        </a>
                    </div>
                </div>
            @endif
        </nav>

        <div class="px-4 py-4 border-t border-gray-700">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
                <div class="ml-3 sidebar-label-text">
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 uppercase">{{ str_replace('_', ' ', $userRole) }}</p>
                </div>
            </div>
        </div>
    </aside>
