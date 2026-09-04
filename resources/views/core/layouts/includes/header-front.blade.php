<!-- Navigation -->
<header class="sticky top-0 z-50 bg-white shadow-sm">
    <div class="container mx-auto px-4 sm:px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="mr-2 sm:mr-3 inline-flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Restrotix" class="h-8 w-auto sm:h-10">
                </a>
            </div>

            <nav class="hidden md:flex space-x-6 lg:space-x-6">

                <div class="relative group/menu pb-2">
                    <button
                        class="font-medium text-gray-700 hover:text-orange-500 transition inline-flex items-center gap-2">
                        Features
                        <span
                            class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-orange-50 text-orange-500 transition-transform duration-200 group-hover/menu:rotate-180 group-focus-within/menu:rotate-180">
                            <i class="fas fa-angle-down text-[10px]"></i>
                        </span>
                    </button>
                    <div
                        class="absolute left-0 top-full mt-1 w-64 bg-white border border-gray-100 rounded-xl shadow-xl p-2 opacity-0 invisible group-hover/menu:opacity-100 group-hover/menu:visible group-focus-within/menu:opacity-100 group-focus-within/menu:visible transition duration-200 z-50">
                        <a href="#option-1"
                            class="group/item relative flex items-center gap-3 rounded-md px-3 py-2.5 pr-10 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">
                            <span class="inline-flex items-center gap-2 leading-snug">
                                <i class="fas fa-cash-register text-orange-500"></i>
                                Smart Billing (POS)
                            </span>
                            <i
                                class="fas fa-arrow-right absolute right-3 top-1/2 -translate-y-1/2 text-xs opacity-0 translate-x-1 group-hover/item:opacity-100 group-hover/item:translate-x-0 transition duration-200"></i>
                        </a>
                        <a href="#option-2"
                            class="group/item relative flex items-center gap-3 rounded-md px-3 py-2.5 pr-10 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">
                            <span class="inline-flex items-center gap-2 leading-snug">
                                <i class="fas fa-store text-orange-500"></i>
                                Marketplace Connect
                            </span>
                            <i
                                class="fas fa-arrow-right absolute right-3 top-1/2 -translate-y-1/2 text-xs opacity-0 translate-x-1 group-hover/item:opacity-100 group-hover/item:translate-x-0 transition duration-200"></i>
                        </a>
                        <a href="#option-3"
                            class="group/item relative flex items-center gap-3 rounded-md px-3 py-2.5 pr-10 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">
                            <span class="inline-flex items-center gap-2 leading-snug">
                                <i class="fas fa-code-branch text-orange-500"></i>
                                Multi-Branch Hub
                            </span>
                            <i
                                class="fas fa-arrow-right absolute right-3 top-1/2 -translate-y-1/2 text-xs opacity-0 translate-x-1 group-hover/item:opacity-100 group-hover/item:translate-x-0 transition duration-200"></i>
                        </a>

                    </div>
                </div>
                <a href="#pricing" class="font-medium text-gray-700 hover:text-orange-500 transition">Pricing</a>

                <a href="{{ route('about') }}" class="font-medium text-gray-700 hover:text-orange-500 transition">About
                    Us</a>

                <div class="relative group/menu pb-2">
                    <button
                        class="font-medium text-gray-700 hover:text-orange-500 transition inline-flex items-center gap-2">
                        More
                        <span
                            class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-orange-50 text-orange-500 transition-transform duration-200 group-hover/menu:rotate-180 group-focus-within/menu:rotate-180">
                            <i class="fas fa-angle-down text-[10px]"></i>
                        </span>
                    </button>
                    <div
                        class="absolute left-0 top-full mt-1 w-64 bg-white border border-gray-100 rounded-xl shadow-xl p-2 opacity-0 invisible group-hover/menu:opacity-100 group-hover/menu:visible group-focus-within/menu:opacity-100 group-focus-within/menu:visible transition duration-200 z-50">
                        <a href="#option-1"
                            class="group/item relative flex items-center gap-3 rounded-md px-3 py-2.5 pr-10 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">
                            <span class="inline-flex items-center gap-2 leading-snug">

                                Career
                            </span>
                            <i
                                class="fas fa-arrow-right absolute right-3 top-1/2 -translate-y-1/2 text-xs opacity-0 translate-x-1 group-hover/item:opacity-100 group-hover/item:translate-x-0 transition duration-200"></i>
                        </a>
                        <a href="#option-2"
                            class="group/item relative flex items-center gap-3 rounded-md px-3 py-2.5 pr-10 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">
                            <span class="inline-flex items-center gap-2 leading-snug">

                                Blogs
                            </span>
                            <i
                                class="fas fa-arrow-right absolute right-3 top-1/2 -translate-y-1/2 text-xs opacity-0 translate-x-1 group-hover/item:opacity-100 group-hover/item:translate-x-0 transition duration-200"></i>
                        </a>
                        <a href="#option-3"
                            class="group/item relative flex items-center gap-3 rounded-md px-3 py-2.5 pr-10 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">
                            <span class="inline-flex items-center gap-2 leading-snug">

                                Support
                            </span>
                            <i
                                class="fas fa-arrow-right absolute right-3 top-1/2 -translate-y-1/2 text-xs opacity-0 translate-x-1 group-hover/item:opacity-100 group-hover/item:translate-x-0 transition duration-200"></i>
                        </a>

                    </div>
                </div>
            </nav>

            <div class="flex items-center space-x-3 sm:space-x-4">
                <a href="{{ route('login') }}"
                    class="font-medium text-gray-700 hover:text-orange-500 transition hidden md:block">Login</a>
                <a href="{{ url('/') }}#pricing"
                    class="bg-[#a52a28] hover:bg-[#851817] text-white font-medium py-2 px-4 sm:py-2.5 sm:px-6 rounded-lg transition shadow-md text-sm sm:text-base hidden md:block">
                    Get Started
                </a>
                
                <button id="mobile-menu-button" type="button" aria-controls="mobile-menu" aria-expanded="false"
                    class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed inset-y-0 right-0 w-64 bg-white shadow-2xl z-50 p-6 md:hidden">
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center">
               <a href="{{ url('/') }}" class="mr-2 sm:mr-3 inline-flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Restrotix" class="h-8 w-auto sm:h-10">
                </a>
            </div>
            <button id="close-menu" type="button" class="text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex flex-col space-y-6 mb-5">

            <a href="#pricing"
                class="font-medium text-gray-700 hover:text-orange-500 transition py-2 border-b border-gray-100">Pricing</a>
            <a href="{{ route('about') }}"
                class="font-medium text-gray-700 hover:text-orange-500 transition py-2 border-b border-gray-100">About
                Us</a>
            <details class="border-b border-gray-100 pb-2">
                <summary
                    class="font-medium text-gray-700 hover:text-orange-500 transition py-2 cursor-pointer list-none flex items-center justify-between">
                    Features
                    <span
                        class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-orange-50 text-orange-500">
                        <i class="fas fa-angle-down text-[10px]"></i>
                    </span>
                </summary>
                <div class="mt-1 flex flex-col space-y-1">
                    <a href="#option-1"
                        class="inline-flex items-center gap-2 font-medium text-gray-600 hover:text-orange-500 transition py-1 pl-4">
                        <i class="fas fa-cash-register text-orange-500 text-xs"></i>
                        Smart Billing (POS)</a>
                    <a href="#option-2"
                        class="inline-flex items-center gap-2 font-medium text-gray-600 hover:text-orange-500 transition py-1 pl-4">
                        <i class="fas fa-store text-orange-500 text-xs"></i>
                        Marketplace Connect</a>
                    <a href="#option-3"
                        class="inline-flex items-center gap-2 font-medium text-gray-600 hover:text-orange-500 transition py-1 pl-4">
                        <i class="fas fa-code-branch text-orange-500 text-xs"></i>
                        Multi-Branch Hub</a>
                </div>
            </details>
            <details class="border-b border-gray-100 pb-2">
                <summary
                    class="font-medium text-gray-700 hover:text-orange-500 transition py-2 cursor-pointer list-none flex items-center justify-between">
                    More
                    <span
                        class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-orange-50 text-orange-500">
                        <i class="fas fa-angle-down text-[10px]"></i>
                    </span>
                </summary>
                <div class="mt-1 flex flex-col space-y-1">
                    <a href="#option-1"
                        class="font-medium text-gray-600 hover:text-orange-500 transition py-1 pl-4">Career</a>
                    <a href="#option-2"
                        class="font-medium text-gray-600 hover:text-orange-500 transition py-1 pl-4">Blogs</a>
                    <a href="#option-3"
                        class="font-medium text-gray-600 hover:text-orange-500 transition py-1 pl-4">Support</a>
                </div>
            </details>
            <a href="#"
                class="font-medium text-gray-700 hover:text-orange-500 transition py-2 border-b border-gray-100">Case
                Studies</a>
            <a href="{{ route('login') }}"
                class="font-medium text-gray-700 hover:text-orange-500 transition py-2  border-b border-gray-100">Login</a>
        </nav>

        <a href="{{ url('/') }}#pricing"
            class="w-full bg-[#a52a28] hover:bg-[#851817] text-white font-medium py-2 rounded-lg transition shadow-md  px-4">
            Get Started
        </a>
    </div>
</header>
