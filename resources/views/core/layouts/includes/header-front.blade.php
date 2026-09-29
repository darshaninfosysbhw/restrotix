<!-- Navigation -->
<header class="sticky top-0 z-50 bg-white shadow-sm">
    <div class="container mx-auto px-4 py-4 sm:px-6">
        <div class="flex items-center justify-between">
            <a href="{{ url('/') }}" class="inline-flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Restrotix" class="h-8 w-auto sm:h-10">
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('login') }}"
                    class="hidden rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-[#DC0812] hover:text-[#DC0812] sm:px-5 sm:py-2.5 md:inline-flex">
                    Login
                </a>
                <a href="{{ url('/') }}#pricing"
                    class="hidden rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-emerald-700 sm:px-6 sm:py-2.5 md:inline-flex">
                    Get Started
                </a>
                <a href="{{ url('/') }}#enquiry"
                    class="hidden rounded-lg border border-[#DC0812] bg-white px-4 py-2 text-sm font-semibold text-[#DC0812] transition hover:bg-[#DC0812] hover:text-white sm:px-6 sm:py-2.5 md:inline-flex">
                    Become Supplier
                </a>

                <button id="mobile-menu-button" type="button" aria-controls="mobile-menu" aria-expanded="false"
                    class="text-gray-700 md:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed inset-y-0 right-0 z-50 w-72 bg-white p-6 shadow-2xl md:hidden">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ url('/') }}" class="inline-flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Restrotix" class="h-9 w-auto">
            </a>
            <button id="close-menu" type="button" class="text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="flex flex-col gap-3">
            <a href="{{ route('login') }}"
                class="rounded-lg border border-gray-200 px-4 py-2.5 text-center font-semibold text-gray-700 transition hover:border-[#DC0812] hover:text-[#DC0812]">
                Login
            </a>
            <a href="{{ url('/') }}#pricing"
                class="rounded-lg bg-emerald-600 px-4 py-2.5 text-center font-semibold text-white shadow-md transition hover:bg-emerald-700">
                Get Started
            </a>
            <a href="{{ url('/') }}#enquiry"
                class="rounded-lg border border-[#DC0812] px-4 py-2.5 text-center font-semibold text-[#DC0812] transition hover:bg-[#DC0812] hover:text-white">
                Become Supplier
            </a>
        </div>
    </div>
</header>

<a href="https://wa.me/9779802892772" target="_blank" rel="noopener noreferrer"
    aria-label="Chat with Restrotix on WhatsApp"
    class="group fixed bottom-4 right-4 z-50 flex h-11 w-11 items-center justify-center rounded-full bg-[#25D366] text-white shadow-[0_8px_24px_rgba(37,211,102,0.35)] transition duration-200 hover:-translate-y-1 hover:bg-[#1fbd5a] hover:shadow-[0_12px_28px_rgba(37,211,102,0.45)] sm:bottom-5 sm:right-5 sm:h-12 sm:w-12">
    <i class="fab fa-whatsapp text-2xl sm:text-[28px]" aria-hidden="true"></i>
    <span
        class="pointer-events-none absolute right-full mr-3 hidden whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white opacity-0 shadow-lg transition group-hover:opacity-100 sm:block">
        Chat on WhatsApp
    </span>
</a>
