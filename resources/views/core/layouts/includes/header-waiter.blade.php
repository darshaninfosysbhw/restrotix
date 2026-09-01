<main class="flex h-screen flex-1 flex-col overflow-hidden">

    <header
        class="bg-gray-800 border-b border-gray-700 px-4 md:px-6 py-4 flex items-center justify-between sticky top-0 z-40">

        <div class="flex items-center min-w-0 gap-2">
            <button id="hamburgerBtn" class="lg:hidden text-gray-400 mr-3 focus:outline-none flex-shrink-0">
                <i class="fas fa-bars text-xl"></i>
            </button>
            @if (auth()->user()->role == 'waiter')
                <a href="{{ route('admin.waiter.index') }}" class="flex items-center gap-2 group transition-all">

                    <h1 class="text-sm md:text-xl font-semibold text-white truncate group-hover:text-orange-500">
                        Waiter <span>Panel</span>
                    </h1>
                </a>
            @else
                <span class="px-3 py-2 bg-orange-500 rounded-lg shadow-lg shadow-orange-500/20">
                    <i class="fas fa-utensils text-white text-xs md:text-sm"></i>
                </span>
                <h1 class="text-sm md:text-xl font-semibold text-white truncate">
                    @if (auth()->user()->role == 'admin' || auth()->user()->role == 'superadmin')
                        <span class="hidden sm:inline">Multi-Branch Dashboard</span>
                        <span class="sm:hidden text-orange-500">Admin</span>
                    @else
                        {{ ucfirst(auth()->user()->role) }} Panel
                    @endif
                </h1>
            @endif
        </div>

        <div class="hidden sm:flex items-center mx-4 flex-1 justify-center max-w-xs md:max-w-md">

            <div class="flex items-center space-x-2 px-3 py-1 bg-orange-500/10 border border-orange-500/20 rounded-lg">
                <i class="fas fa-store text-[10px] text-orange-500"></i>
                <span class="text-[10px] md:text-xs font-bold text-orange-500 uppercase tracking-wider truncate">
                    {{ auth()->user()->branch_name ?? 'Main Outlet' }}
                </span>
            </div>

        </div>
        @if (session()->has('impersonated_by'))
            <div
                class="hidden lg:flex hidden sm:flex items-center px-3 py-2 rounded-lg border border-orange-500/30 bg-orange-500/10 shadow-sm">
                <a href="{{ route('impersonate.leave') }}"
                    class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide bg-orange-500 text-white hover:bg-orange-400 transition-colors inline-flex items-center gap-1.5">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    Back to SuperAdmin
                </a>
            </div>
        @endif
        <div class="flex items-center space-x-2 md:space-x-4 flex-shrink-0">
            <button id="theme-toggle"
                class="hidden sm:block text-gray-400 hover:text-orange-500 text-lg transition-colors">
                <i id="theme-icon" class="fas fa-sun"></i>
            </button>

            <div class="relative">
                <i class="fas fa-bell text-gray-400 text-lg cursor-pointer hover:text-orange-500"></i>
                <span
                    class="absolute -top-1 -right-1 w-4 h-4 bg-orange-500 text-white text-[10px] rounded-full flex items-center justify-center border-2 border-gray-800">5</span>
            </div>

            <div class="relative border-l border-gray-700 pl-2 md:pl-4 flex items-center">
                <div id="profileBtn" class="flex items-center space-x-3 cursor-pointer group">

                    <div
                        class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center border-2 border-transparent group-hover:border-orange-500/50 transition-all shadow-lg overflow-hidden">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>

                    <div class="hidden md:flex flex-col items-start leading-none pointer-events-none">
                        <span class="text-sm font-semibold text-white group-hover:text-orange-500 transition-colors">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="text-[10px] text-gray-500 uppercase mt-1 font-bold tracking-tight">
                            {{ auth()->user()->role }}
                        </span>
                    </div>

                    <i
                        class="fas fa-chevron-down text-[10px] text-gray-400 group-hover:text-white transition-all transform group-hover:translate-y-0.5"></i>
                </div>

                <div id="profileMenu"
                    class="hidden absolute right-0 mt-3 w-48 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-[100] overflow-hidden top-full">
                    <div class="py-1">

                        <div class="px-4 py-3 border-b border-gray-700 md:hidden bg-gray-900/40">
                            <p class="text-xs text-white font-bold truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[9px] text-gray-500 uppercase mt-0.5">{{ auth()->user()->role }}</p>
                        </div>

                        <div class="mt-1">
                            <a href="{{ route('admin.profile') }}"
                                class="group flex items-center px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-orange-500 transition-all">
                                <div
                                    class="w-8 h-8 rounded-lg bg-gray-700 group-hover:bg-orange-500/10 flex items-center justify-center mr-3 transition-all">
                                    <i class="fas fa-user-circle text-gray-500 group-hover:text-orange-500"></i>
                                </div>
                                My Profile
                            </a>

                            <a href="#"
                                class="group flex items-center px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-orange-500 transition-all">
                                <div
                                    class="w-8 h-8 rounded-lg bg-gray-700 group-hover:bg-orange-500/10 flex items-center justify-center mr-3 transition-all">
                                    <i class="fas fa-cog text-gray-500 group-hover:text-orange-500"></i>
                                </div>
                                Settings
                            </a>
                        </div>

                        <div class="px-2 my-1">
                            <hr class="border-gray-700">
                        </div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 transition-colors font-bold">
                                <div class="w-8 h-8 rounded-lg bg-red-500/5 flex items-center justify-center mr-3">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
