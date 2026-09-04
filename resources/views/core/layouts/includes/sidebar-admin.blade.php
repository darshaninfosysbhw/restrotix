<!-- Mobile Sidebar (hidden by default) -->
@php
    $user = auth()->user();
    $tenant = $user?->tenant;

    $restaurantName = trim((string) ($tenant?->company_name ?? 'RestoAdmin'));
    $restaurantName = $restaurantName !== '' ? $restaurantName : 'RestoAdmin';

    $branchName = trim((string) ($user?->branch?->branch_name ?? 'Main Outlet'));
    $branchName = $branchName !== '' ? $branchName : 'Main Outlet';

    $restaurantLogoPath = trim((string) ($tenant?->logo ?? ''));
    $restaurantLogoUrl = null;

    if ($restaurantLogoPath !== '') {
        if (preg_match('/^https?:\/\//i', $restaurantLogoPath)) {
            $restaurantLogoUrl = $restaurantLogoPath;
        } elseif (str_starts_with($restaurantLogoPath, 'storage/')) {
            $restaurantLogoUrl = asset($restaurantLogoPath);
        } elseif (str_starts_with($restaurantLogoPath, 'public/')) {
            $restaurantLogoUrl = asset(str_replace('public/', 'storage/', $restaurantLogoPath));
        } elseif (str_starts_with($restaurantLogoPath, '/')) {
            $restaurantLogoUrl = asset(ltrim($restaurantLogoPath, '/'));
        } else {
            $restaurantLogoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($restaurantLogoPath);
        }
    }
@endphp

<div id="mobileSidebar"
    class="fixed inset-y-0 left-0 w-64 bg-gray-800 border-r border-gray-700 z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden flex flex-col">

    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-700">
        <div class="flex items-center">
            <div
                class="w-9 h-9 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center mr-3 flex-shrink-0 overflow-hidden shadow-lg shadow-orange-500/20">
                @if ($restaurantLogoUrl)
                    <img src="{{ $restaurantLogoUrl }}" alt="{{ $restaurantName }}"
                        class="h-full w-full object-contain p-1" />
                @else
                    <i class="fas fa-utensils text-sm text-white"></i>
                @endif
            </div>
            <div class="min-w-0">
                <span class="block text-xl font-bold text-white truncate max-w-[13rem]" title="{{ $restaurantName }}">
                    {{ $restaurantName }}
                </span>
                <p class="text-[10px] uppercase tracking-[0.22em] text-gray-400 truncate max-w-[13rem]"
                    title="{{ $branchName }}">
                    {{ $branchName }}
                </p>
            </div>
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

        <a href="{{ route('admin.dashboard') }}"
            class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active text-orange-500 bg-gray-700/50' : 'text-gray-300 hover:text-orange-500' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg">
            <i class="fas fa-tachometer-alt w-5 mr-3"></i>
            <span class="sidebar-label-text">Dashboard</span>
        </a>

        @if ($userRole == 'admin' || $userRole == 'superadmin')
            <a href="{{ route('admin.branches.index') }}"
                class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                <i class="fas fa-store w-5 mr-3"></i>
                <span class="sidebar-label-text">All Branches</span>
            </a>
        @endif

        @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'manager')
            <a href="{{ route('admin.employee.index') }}"
                class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                <i class="fas fa-store w-5 mr-3"></i>
                <span class="sidebar-label-text">Employee</span>
            </a>
        @endif
        
         @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'waiter' || $userRole == 'manager')
                <a href="{{ route('admin.tables.index') }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                    <i class="fas fa-chair w-5 mr-3"></i>
                    <span class="sidebar-label-text">Table</span>
                </a>
            @endif

            @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'waiter')
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
            @endif
            
            
            @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'chef')
                <a href="{{ route('admin.kds.index') }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                    <i class="fas fa-utensils w-5 mr-3"></i>
                    <span class="sidebar-label-text">Kitchen Orders (KDS)</span>
                </a>
            @endif
            
              @if (in_array($userRole, ['admin', 'manager', 'superadmin']))
            <a href="{{ route('admin.orders.history') }}"
                class="sidebar-item {{ request()->routeIs('admin.orders.history') ? 'active text-orange-500 bg-gray-700/50' : 'text-gray-300 hover:text-orange-500' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <i class="fas fa-clock-rotate-left w-5 mr-3"></i>
                <span class="sidebar-label-text">Order History</span>
            </a>
        @endif


        @if (in_array($userRole, ['admin', 'manager', 'sales_manager', 'superadmin']))
            @php $isBillingActive = in_array('billing', $services); @endphp
            <a href="{{ $isBillingActive ? route('billing.index') : 'javascript:void(0)' }}"
                class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isBillingActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                onclick="{{ !$isBillingActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                <i class="fas fa-file-invoice-dollar w-5 mr-3"></i>
                <span class="sidebar-label-text">Billing System</span>
                @if (!$isBillingActive)
                    <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px] text-gray-600"></i>
                @endif
            </a>
        @endif

        @if (in_array($userRole, ['admin', 'manager', 'sales_manager', 'superadmin']))
            @php $isBillingActive = in_array('membership-card', $services); @endphp
            <a href="{{ $isBillingActive ? route('membership-card.index') : 'javascript:void(0)' }}"
                class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isBillingActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                onclick="{{ !$isBillingActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                <i class="fas fa-file-invoice-dollar w-5 mr-3"></i>
                <span class="sidebar-label-text">Membership Card</span>
                @if (!$isBillingActive)
                    <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px] text-gray-600"></i>
                @endif
            </a>
        @endif

        @if (in_array($userRole, ['admin', 'manager', 'sales_manager', 'superadmin']))
            @php $isBillingActive = in_array('membership-card', $services); @endphp
            <a href="{{ $isBillingActive ? route('membership-card.index') : 'javascript:void(0)' }}"
                class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isBillingActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                onclick="{{ !$isBillingActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                <i class="fas fa-file-invoice-dollar w-5 mr-3"></i>
                <span class="sidebar-label-text">RestroTix Promotion</span>
                @if (!$isBillingActive)
                    <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px] text-gray-600"></i>
                @endif
            </a>
        @endif

        @if (in_array($userRole, ['admin', 'manager', 'purchase_manager', 'superadmin']))
            @php $isInventoryActive = in_array('inventory', $services); @endphp
            <a href="{{ $isInventoryActive ? route('marketplace.index') : 'javascript:void(0)' }}"
                class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isInventoryActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                onclick="{{ !$isInventoryActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                <i class="fas fa-boxes w-5 mr-3"></i>
                <span class="sidebar-label-text">Inventory Management</span>
                @if (!$isInventoryActive)
                    <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px] text-gray-600"></i>
                @endif
            </a>
        @endif

        @if (in_array($userRole, ['admin', 'manager', 'purchase_manager', 'superadmin']))
            @php $isMarketActive = in_array('marketplace', $services); @endphp
            <a href="{{ $isMarketActive ? route('marketplace.index') : 'javascript:void(0)' }}"
                class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isMarketActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                onclick="{{ !$isMarketActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                <i class="fas fa-shopping-cart w-5 mr-3"></i>
                <span class="sidebar-label-text">Marketplace</span>
                @if (!$isMarketActive)
                    <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px] text-gray-600"></i>
                @endif
            </a>
        @endif

        @if (in_array($userRole, ['admin', 'manager', 'chef', 'superadmin']))
            @php $isInventoryActive = in_array('inventory', $services); @endphp
            <a href="#"
                class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isInventoryActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                onclick="{{ !$isInventoryActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                <i class="fas fa-utensils w-5 mr-3"></i>
                <span class="sidebar-label-text">Kitchen Orders (KDS)</span>
                @if (!$isInventoryActive)
                    <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px]"></i>
                @endif
            </a>
        @endif

      

        @if (in_array($userRole, ['admin', 'manager', 'account_manager', 'superadmin']))
            @php $isAccountActive = in_array('accounts', $services); @endphp
            <div class="dropdown-container">
                <button
                    class="dropdown-trigger sidebar-item w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg {{ $isAccountActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }} focus:outline-none transition-all">
                    <div class="flex items-center">
                        <i class="fas fa-chart-pie w-5 mr-3"></i>
                        <span class="sidebar-label-text">Financial Reports</span>
                    </div>
                    <div class="flex items-center">
                        @if (!$isAccountActive)
                            <i class="fas fa-lock sidebar-lock-icon mr-2 text-[10px] text-gray-600"></i>
                        @endif
                        <i class="fas fa-chevron-right text-[10px] transition-transform duration-200 trigger-arrow"></i>
                    </div>
                </button>
                <div class="dropdown-menu hidden pl-12 space-y-1">
                    <a href="{{ $isAccountActive ? '#' : route('admin.dashboard') }}"
                        class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors">Revenue
                        Reports</a>
                    <a href="{{ $isAccountActive ? '#' : route('admin.dashboard') }}"
                        class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors">Expense
                        Tracker</a>
                </div>
            </div>
        @endif

        <div class="dropdown-container">
            <button
                class="dropdown-trigger sidebar-item w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg focus:outline-none transition-all {{ request()->routeIs('admin.settings.menu.*', 'admin.branches.payment-gateways*') ? 'text-orange-500 bg-gray-700/50' : 'text-gray-300 hover:text-orange-500' }}">
                <div class="flex items-center">
                    <i class="fas fa-cog w-5 mr-3"></i>
                    <span class="sidebar-label-text">Settings</span>
                </div>
                <div class="flex items-center sidebar-label-text">
                    <i class="fas fa-chevron-right text-[10px] transition-transform duration-200 trigger-arrow"></i>
                </div>
            </button>
            <div class="dropdown-menu hidden pl-12 space-y-1">
                <a href="{{ route('admin.settings.menu.index') }}"
                    class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors {{ request()->routeIs('admin.settings.menu.*') ? 'text-orange-500' : '' }}">
                    Menu Settings
                </a>
                
                 <a href="{{ route('admin.branches.payment-gateways') }}"
                            class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors {{ request()->routeIs('admin.branches.payment-gateways*') ? 'text-orange-500' : '' }}">
                            Payment Settings
                        </a>
            </div>
        </div>
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
                <div
                    class="w-9 h-9 rounded-lg bg-gradient-to-r from-orange-500 to-orange-500 flex items-center justify-center mr-3 flex-shrink-0 overflow-hidden shadow-lg shadow-orange-500/20">
                    @if ($restaurantLogoUrl)
                        <img src="{{ $restaurantLogoUrl }}" alt="{{ $restaurantName }}"
                            class="h-full w-full object-contain p-1" />
                    @else
                        <i class="fas fa-utensils text-[19px] text-white"></i>
                    @endif
                </div>
                <div class="min-w-0">
                    <span
                        class="sidebar-label block text-xl font-bold text-white whitespace-nowrap truncate max-w-[11rem]"
                        title="{{ $restaurantName }}">
                        {{ $restaurantName }}
                    </span>
                    <p class="text-[10px] uppercase tracking-[0.22em] text-gray-400 truncate max-w-[11rem]"
                        title="{{ $branchName }}">
                        {{ $branchName }}
                    </p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            @php
                $services = $activeServiceSlugs ?? [];
                $userRole = auth()->user()->role;
            @endphp

            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active text-orange-500 bg-gray-700/50' : 'text-gray-300 hover:text-orange-500' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                <span class="sidebar-label-text">Dashboard</span>
            </a>

            @if ($userRole == 'admin' || $userRole == 'superadmin')
                <a href="{{ route('admin.branches.index') }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                    <i class="fas fa-store w-5 mr-3"></i>
                    <span class="sidebar-label-text">All Branches</span>
                </a>
            @endif



            @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'manager')
                <a href="{{ route('admin.employee.index') }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                    <i class="fas fa-store w-5 mr-3"></i>
                    <span class="sidebar-label-text">Employee</span>
                </a>
            @endif

            {{-- ===========================This Step taken for fast work ======================================== --}}
            {{-- // tabel (is not final) --}}

            @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'waiter' || $userRole == 'manager')
                <a href="{{ route('admin.tables.index') }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                    <i class="fas fa-chair w-5 mr-3"></i>
                    <span class="sidebar-label-text">Table</span>
                </a>
            @endif

            @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'waiter')
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
            @endif

            @if ($userRole == 'admin' || $userRole == 'superadmin' || $userRole == 'chef')
                <a href="{{ route('admin.kds.index') }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-300 hover:text-orange-500">
                    <i class="fas fa-utensils w-5 mr-3"></i>
                    <span class="sidebar-label-text">Kitchen Orders (KDS)</span>
                </a>
            @endif

            @if (in_array($userRole, ['admin', 'manager', 'superadmin']))
                <a href="{{ route('admin.orders.history') }}"
                    class="sidebar-item {{ request()->routeIs('admin.orders.history') ? 'active text-orange-500 bg-gray-700/50' : 'text-gray-300 hover:text-orange-500' }} flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                    <i class="fas fa-clock-rotate-left w-5 mr-3"></i>
                    <span class="sidebar-label-text">Order History</span>
                </a>
            @endif


            {{-- ===========================Working Area===================== --}}


            {{-- @php
                // $isTableVisible = in_array('table', $planServiceSlugs);
                $isTableActive = in_array('table', $activeServiceSlugs);
            @endphp

            @if ($isTableActive)
                <a href="{{ route('table.index') }}" class="sidebar-item text-gray-300 hover:text-orange-500">
                    Table
                </a>
            @else
                <a href="javascript:void(0)" class="sidebar-item text-gray-500 opacity-60 italic"
                    onclick="alert('यह Add-on है, कृपया खरीदें')">
                    Table 🔒
                </a>
            @endif --}}



            <!--@if (in_array($userRole, ['admin', 'manager', 'superadmin']))-->
            <!--    @php $isInventoryActive = in_array('table', $services); @endphp-->
            <!--    <a href="{{ $isBillingActive ? route('table.index') : 'javascript:void(0)' }}"-->
            <!--        class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isInventoryActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"-->
            <!--        onclick="{{ !$isInventoryActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">-->
            <!--        <i class="fas fa-chair w-5 mr-3"></i>-->
            <!--        <span class="sidebar-label-text">Table</span>-->
            <!--        @if (!$isInventoryActive)-->
            <!--            <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px]"></i>-->
            <!--        @endif-->
            <!--    </a>-->
            <!--@endif-->




            @if (in_array($userRole, ['admin', 'manager', 'sales_manager', 'superadmin']))
                @php $isBillingActive = in_array('billing', $services); @endphp
                <a href="{{ $isBillingActive ? route('billing.index') : 'javascript:void(0)' }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isBillingActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                    onclick="{{ !$isBillingActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                    <i class="fas fa-file-invoice-dollar w-5 mr-3"></i>
                    <span class="sidebar-label-text">Billing System</span>
                    @if (!$isBillingActive)
                        <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px] text-gray-600"></i>
                    @endif
                </a>
            @endif

            @if (in_array($userRole, ['admin', 'manager', 'sales_manager', 'superadmin']))
                @php $isBillingActive = in_array('membership-card', $services); @endphp
                <a href="{{ $isBillingActive ? route('membership-card.index') : 'javascript:void(0)' }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isBillingActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                    onclick="{{ !$isBillingActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                    <i class="fas fa-file-invoice-dollar w-5 mr-3"></i>
                    <span class="sidebar-label-text">Membership Card</span>
                    @if (!$isBillingActive)
                        <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px] text-gray-600"></i>
                    @endif
                </a>
            @endif

            @if (in_array($userRole, ['admin', 'manager', 'sales_manager', 'superadmin']))
                @php $isBillingActive = in_array('membership-card', $services); @endphp
                <a href="{{ $isBillingActive ? route('membership-card.index') : 'javascript:void(0)' }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isBillingActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                    onclick="{{ !$isBillingActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                    <i class="fas fa-file-invoice-dollar w-5 mr-3"></i>
                    <span class="sidebar-label-text">RestroTix Promotion</span>
                    @if (!$isBillingActive)
                        <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px] text-gray-600"></i>
                    @endif
                </a>
            @endif


            {{-- @if (in_array($userRole, ['admin', 'manager', 'account_manager', 'superadmin']))
                @php $isAccountActive = in_array('accounts', $services); @endphp
                <div class="dropdown-container">
                    <button
                        class="dropdown-trigger sidebar-item w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg {{ $isAccountActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }} focus:outline-none transition-all">
                        <div class="flex items-center">
                            <i class="fas fa-chart-pie w-5 "></i>
                            <span class="sidebar-label-text">RestroTix Promotion</span>
                        </div>
                        <div class="flex items-center">
                            @if (!$isAccountActive)
                                <i class="fas fa-lock sidebar-lock-icon mr-2 text-[10px] text-gray-600"></i>
                            @endif
                            <i
                                class="fas fa-chevron-right text-[10px] transition-transform duration-200 trigger-arrow"></i>
                        </div>
                    </button>
                    <div class="dropdown-menu hidden pl-12 space-y-1">
                        <a href="{{ $isAccountActive ? '#' : route('admin.dashboard') }}"
                            class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors">Social
                            media</a>
                        <a href="{{ $isAccountActive ? '#' : route('admin.dashboard') }}"
                            class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors">Influencer
                            hiring
                        </a>
                    </div>
                </div>
            @endif --}}


            @if (in_array($userRole, ['admin', 'manager', 'purchase_manager', 'superadmin']))
                @php $isInventoryActive = in_array('inventory', $services); @endphp
                <a href="{{ $isInventoryActive ? route('marketplace.index') : 'javascript:void(0)' }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isInventoryActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                    onclick="{{ !$isInventoryActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                    <i class="fas fa-boxes w-5 mr-3"></i>
                    <span class="sidebar-label-text">Inventory Management</span>
                    @if (!$isInventoryActive)
                        <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px] text-gray-600"></i>
                    @endif
                </a>
            @endif

            @if (in_array($userRole, ['admin', 'manager', 'purchase_manager', 'superadmin']))
                @php $isMarketActive = in_array('marketplace', $services); @endphp
                <a href="{{ $isMarketActive ? route('marketplace.index') : 'javascript:void(0)' }}"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isMarketActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                    onclick="{{ !$isMarketActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                    <i class="fas fa-shopping-cart w-5 mr-3"></i>
                    <span class="sidebar-label-text">Marketplace</span>
                    @if (!$isMarketActive)
                        <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px] text-gray-600"></i>
                    @endif
                </a>
            @endif

            @if (in_array($userRole, ['admin', 'manager', 'chef', 'superadmin']))
                @php $isInventoryActive = in_array('inventory', $services); @endphp
                <a href="#"
                    class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isInventoryActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }}"
                    onclick="{{ !$isInventoryActive ? "alert('यह सर्विस आपके प्लान में नहीं है। कृपया एडन (Add-on) खरीदें।')" : '' }}">
                    <i class="fas fa-utensils w-5 mr-3"></i>
                    <span class="sidebar-label-text">Kitchen Orders (KDS)</span>
                    @if (!$isInventoryActive)
                        <i class="fas fa-lock sidebar-lock-icon ml-auto text-[10px]"></i>
                    @endif
                </a>
            @endif



            @if (in_array($userRole, ['admin', 'manager', 'account_manager', 'superadmin']))
                @php $isAccountActive = in_array('accounts', $services); @endphp
                <div class="dropdown-container">
                    <button
                        class="dropdown-trigger sidebar-item w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg {{ $isAccountActive ? 'text-gray-300 hover:text-orange-500' : 'text-gray-500 opacity-60 italic' }} focus:outline-none transition-all">
                        <div class="flex items-center">
                            <i class="fas fa-chart-pie w-5 mr-3"></i>
                            <span class="sidebar-label-text">Financial Reports</span>
                        </div>
                        <div class="flex items-center">
                            @if (!$isAccountActive)
                                <i class="fas fa-lock sidebar-lock-icon mr-2 text-[10px] text-gray-600"></i>
                            @endif
                            <i
                                class="fas fa-chevron-right text-[10px] transition-transform duration-200 trigger-arrow"></i>
                        </div>
                    </button>
                    <div class="dropdown-menu hidden pl-12 space-y-1">
                        <a href="{{ $isAccountActive ? '#' : route('admin.dashboard') }}"
                            class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors">Revenue
                            Reports</a>
                        <a href="{{ $isAccountActive ? '#' : route('admin.dashboard') }}"
                            class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors">Expense
                            Tracker</a>
                    </div>
                </div>
            @endif

            @if ($userRole == 'admin' || $userRole == 'superadmin')
                <div class="dropdown-container">
                    <button
                        class="dropdown-trigger sidebar-item w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg focus:outline-none transition-all {{ request()->routeIs('admin.settings.menu.*', 'admin.branches.payment-gateways*') ? 'text-orange-500 bg-gray-700/50' : 'text-gray-300 hover:text-orange-500' }}">
                        <div class="flex items-center">
                            <i class="fas fa-cog w-5 mr-3"></i>
                            <span class="sidebar-label-text">Settings</span>
                        </div>
                        <div class="flex items-center sidebar-label-text">
                            <i
                                class="fas fa-chevron-right text-[10px] transition-transform duration-200 trigger-arrow"></i>
                        </div>
                    </button>
                    <div class="dropdown-menu hidden pl-12 space-y-1">
                        <a href="{{ route('admin.settings.menu.index') }}"
                            class="block py-2 text-sm text-gray-400 hover:text-orange-500 transition-colors {{ request()->routeIs('admin.settings.menu.*') ? 'text-orange-500' : '' }}">
                            Menu Settings
                        </a>
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
