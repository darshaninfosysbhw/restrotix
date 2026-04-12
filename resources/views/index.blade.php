@extends('core.layouts.front')
@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-white to-gray-50 pt-12 pb-16 md:pt-20">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="flex flex-col lg:flex-row items-center">
                <div class="lg:w-1/2 mb-10 lg:mb-0">
                    <div
                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-orange-100 text-orange-800 text-sm font-medium mb-4 sm:mb-6">
                        <i class="fas fa-chart-line mr-2"></i> Average 27% Cost Reduction
                    </div>

                    <h1
                        class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-4 sm:mb-6">
                        Unified Management for <span class="text-orange-500">Multi-Branch</span> Restaurant Chains
                    </h1>

                    <p class="text-base sm:text-lg md:text-xl text-gray-600 mb-8 sm:mb-10 max-w-2xl">
                        Centralized ERP solution that synchronizes inventory, staff, and finances across all your
                        restaurant locations in real-time.
                    </p>

                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 lg:space-x-6">
                        <a href="{{ route('checkout') }}"
                            class="gradient-orange text-white font-semibold py-3 px-6 sm:py-4 sm:px-8 rounded-lg shadow-lg hover:shadow-xl transition flex items-center justify-center text-sm sm:text-base">
                            Start Free Trial
                            <i class="fas fa-arrow-right ml-3"></i>
                        </a>
                        <button
                            class="border-2 border-gray-300 text-gray-800 font-semibold py-3 px-6 sm:py-4 sm:px-8 rounded-lg hover:border-orange-300 transition flex items-center justify-center text-sm sm:text-base">
                            <i class="fas fa-play-circle mr-3 text-orange-500"></i>
                            Watch Demo
                        </button>
                    </div>

                    <div class="mt-8 sm:mt-12 flex items-center">
                        <div class="flex -space-x-2 sm:-space-x-3 mr-3 sm:mr-4">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-500 border-2 border-white"></div>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-green-500 border-2 border-white"></div>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-purple-500 border-2 border-white"></div>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-yellow-500 border-2 border-white"></div>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 text-sm sm:text-base">Trusted by <span
                                    class="text-orange-500">500+</span> restaurant owners</p>
                            <div class="flex items-center">
                                <div class="flex text-yellow-400 mr-1 sm:mr-2 text-sm sm:text-base">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="text-gray-600 text-xs sm:text-sm">4.8/5 (287 reviews)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/2 relative w-full">
                    <!-- Multi-branch connectivity visualization -->
                    <div class="hero-visual relative w-full h-80 sm:h-96 lg:h-[500px]">
                        <!-- Central Hub -->
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10">
                            <div class="relative">
                                <div
                                    class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 gradient-orange rounded-xl lg:rounded-2xl flex items-center justify-center shadow-2xl">
                                    <i class="fas fa-server text-white text-xl sm:text-2xl lg:text-3xl"></i>
                                </div>
                                <div
                                    class="absolute -top-2 -right-2 bg-green-500 text-white text-xs font-bold py-1 px-2 rounded-full">
                                    LIVE
                                </div>
                            </div>
                            <p class="text-center font-semibold mt-2 text-sm sm:text-base">RestoChain Hub</p>
                        </div>

                        <!-- Connecting Lines -->
                        <div class="absolute top-0 left-0 w-full h-full">
                            <div
                                class="absolute top-1/4 left-1/4 w-1/3 h-1/3 border-t-2 border-l-2 border-dashed border-orange-300 pulse-line">
                            </div>
                            <div
                                class="absolute top-1/4 right-1/4 w-1/3 h-1/3 border-t-2 border-r-2 border-dashed border-orange-300 pulse-line">
                            </div>
                            <div
                                class="absolute bottom-1/4 left-1/4 w-1/3 h-1/3 border-b-2 border-l-2 border-dashed border-orange-300 pulse-line">
                            </div>
                            <div
                                class="absolute bottom-1/4 right-1/4 w-1/3 h-1/3 border-b-2 border-r-2 border-dashed border-orange-300 pulse-line">
                            </div>
                        </div>

                        <!-- Branch 1 (Top Left) -->
                        <div class="branch-node absolute top-4 left-4 sm:top-10 sm:left-10 md:left-20">
                            <div class="isometric-icon">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 border-2 border-blue-300 relative">
                                    <div class="absolute -top-2 -left-2 w-3 h-3 sm:w-4 sm:h-4 bg-blue-400"></div>
                                    <div class="absolute -top-2 -right-2 w-3 h-3 sm:w-4 sm:h-4 bg-blue-400"></div>
                                </div>
                            </div>
                            <p class="text-xs sm:text-sm font-medium mt-1 sm:mt-2">Downtown</p>
                            <p class="text-xs text-green-600">+14% revenue</p>
                        </div>

                        <!-- Branch 2 (Top Right) -->
                        <div class="branch-node absolute top-4 right-4 sm:top-10 sm:right-10 md:right-20">
                            <div class="isometric-icon">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-100 border-2 border-green-300 relative">
                                    <div class="absolute -top-2 -left-2 w-3 h-3 sm:w-4 sm:h-4 bg-green-400"></div>
                                    <div class="absolute -top-2 -right-2 w-3 h-3 sm:w-4 sm:h-4 bg-green-400"></div>
                                </div>
                            </div>
                            <p class="text-xs sm:text-sm font-medium mt-1 sm:mt-2">Westside</p>
                            <p class="text-xs text-green-600">+8% revenue</p>
                        </div>

                        <!-- Branch 3 (Bottom Left) -->
                        <div class="branch-node absolute bottom-4 left-4 sm:bottom-10 sm:left-10 md:left-20">
                            <div class="isometric-icon">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-purple-100 border-2 border-purple-300 relative">
                                    <div class="absolute -top-2 -left-2 w-3 h-3 sm:w-4 sm:h-4 bg-purple-400"></div>
                                    <div class="absolute -top-2 -right-2 w-3 h-3 sm:w-4 sm:h-4 bg-purple-400"></div>
                                </div>
                            </div>
                            <p class="text-xs sm:text-sm font-medium mt-1 sm:mt-2">East End</p>
                            <p class="text-xs text-orange-600">-2% revenue</p>
                        </div>

                        <!-- Branch 4 (Bottom Right) -->
                        <div class="branch-node absolute bottom-4 right-4 sm:bottom-10 sm:right-10 md:right-20">
                            <div class="isometric-icon">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-yellow-100 border-2 border-yellow-300 relative">
                                    <div class="absolute -top-2 -left-2 w-3 h-3 sm:w-4 sm:h-4 bg-yellow-400"></div>
                                    <div class="absolute -top-2 -right-2 w-3 h-3 sm:w-4 sm:h-4 bg-yellow-400"></div>
                                </div>
                            </div>
                            <p class="text-xs sm:text-sm font-medium mt-1 sm:mt-2">Uptown</p>
                            <p class="text-xs text-green-600">+5% revenue</p>
                        </div>

                        <!-- Stats Overlay -->
                        <div
                            class="absolute bottom-2 sm:bottom-4 left-1/2 transform -translate-x-1/2 bg-white/90 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4 shadow-lg max-w-xs sm:max-w-none">
                            <div class="flex justify-between space-x-4 sm:space-x-6">
                                <div class="text-center">
                                    <p class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">99.9%</p>
                                    <p class="text-xs text-gray-600">Uptime</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">Real-time</p>
                                    <p class="text-xs text-gray-600">Reporting</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">24/7</p>
                                    <p class="text-xs text-gray-600">Support</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- What We Do Section -->
    <section id="what-we-do" class="py-16 sm:py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-10 sm:mb-12">
                <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-wide leading-none mb-4">WHAT WE DO
                </h2>
                <p class="text-2xl sm:text-3xl text-orange-500 font-medium">Solutions Designed for Modern Restaurants</p>
            </div>

            <div class="max-w-5xl mx-auto relative px-3 sm:px-0 py-4 px-5 sm:py-6 sm:px-8 lg:px-10">
                <div class="pointer-events-none absolute inset-0 border border-orange-300/70"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-7">
                    <div
                        class="rounded-3xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-6 sm:p-7 transition duration-300 ease-out hover:-translate-y-1.5 hover:scale-[1.01] hover:border-green-300 hover:shadow-[0_12px_28px_rgba(22,163,74,0.16)]">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-11 h-11 rounded-xl bg-green-50 text-green-600 border border-green-100 flex items-center justify-center shrink-0 shadow-sm">
                                <i class="fas fa-sitemap text-lg"></i>
                            </div>
                            <div class="pt-0.5">
                                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 leading-tight mb-2">Unified
                                    Management</h3>
                                <p class="text-gray-600 text-sm sm:text-base leading-relaxed">Control and manage all your
                                    branches from one dashboard.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-3xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-6 sm:p-7 transition duration-300 ease-out hover:-translate-y-1.5 hover:scale-[1.01] hover:border-amber-300 hover:shadow-[0_12px_28px_rgba(245,158,11,0.16)]">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0 shadow-sm">
                                <i class="fas fa-tags text-lg"></i>
                            </div>
                            <div class="pt-0.5">
                                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 leading-tight mb-2">Smart Price
                                    Comparison</h3>
                                <p class="text-gray-600 text-sm sm:text-base leading-relaxed">Automatically compare
                                    supplier
                                    rates and reduce your costs.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-3xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-6 sm:p-7 transition duration-300 ease-out hover:-translate-y-1.5 hover:scale-[1.01] hover:border-sky-300 hover:shadow-[0_12px_28px_rgba(14,165,233,0.16)]">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0 shadow-sm">
                                <i class="fas fa-boxes-stacked text-lg"></i>
                            </div>
                            <div class="pt-0.5">
                                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 leading-tight mb-2">Automated
                                    Inventory</h3>
                                <p class="text-gray-600 text-sm sm:text-base leading-relaxed">Never worry about stock-outs
                                    with auto alerts and inventory tracking.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-3xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-6 sm:p-7 transition duration-300 ease-out hover:-translate-y-1.5 hover:scale-[1.01] hover:border-violet-300 hover:shadow-[0_12px_28px_rgba(139,92,246,0.16)]">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-11 h-11 rounded-xl bg-violet-50 text-violet-600 border border-violet-100 flex items-center justify-center shrink-0 shadow-sm">
                                <i class="fas fa-handshake-simple text-lg"></i>
                            </div>
                            <div class="pt-0.5">
                                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 leading-tight mb-2">Direct
                                    Marketplace Access</h3>
                                <p class="text-gray-600 text-sm sm:text-base leading-relaxed">Connect directly with top
                                    suppliers and secure the best deals.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 sm:py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-10 sm:mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">How It Works</h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    Start quickly and manage your restaurant chain with a simple 3-step flow.
                </p>
            </div>
            <div class="max-w-6xl mx-auto relative">
                <div class="hidden md:block absolute top-10 left-0 right-0 h-0.5 bg-orange-200"></div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                    <div class="relative">
                        <div
                            class="hidden md:flex absolute top-5 left-1/2 -translate-x-1/2 w-10 h-10 rounded-full bg-white border-2 border-orange-500 text-orange-500 items-center justify-center font-bold z-10">
                            1
                        </div>
                        <div
                            class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-7 pt-8 md:pt-16 transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <div
                                class="w-11 h-11 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center mb-4">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Setup Your Account</h3>
                            <p class="text-gray-600 text-sm sm:text-base">Create your workspace, add business details, and
                                invite your core team.</p>
                        </div>
                    </div>

                    <div class="relative">
                        <div
                            class="hidden md:flex absolute top-5 left-1/2 -translate-x-1/2 w-10 h-10 rounded-full bg-white border-2 border-orange-500 text-orange-500 items-center justify-center font-bold z-10">
                            2
                        </div>
                        <div
                            class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-7 pt-8 md:pt-16 transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <div
                                class="w-11 h-11 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center mb-4">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Configure Branch Operations</h3>
                            <p class="text-gray-600 text-sm sm:text-base">Set menus, inventory rules, and staff workflows
                                for each location.</p>
                        </div>
                    </div>

                    <div class="relative">
                        <div
                            class="hidden md:flex absolute top-5 left-1/2 -translate-x-1/2 w-10 h-10 rounded-full bg-white border-2 border-orange-500 text-orange-500 items-center justify-center font-bold z-10">
                            3
                        </div>
                        <div
                            class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-7 pt-8 md:pt-16 transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <div
                                class="w-11 h-11 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center mb-4">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Monitor and Scale</h3>
                            <p class="text-gray-600 text-sm sm:text-base">Track performance in real time and optimize costs
                                across all branches.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-16 sm:py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">Simple, Transparent
                    Pricing</h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">Choose the perfect plan for your
                    restaurant business. Scale up as you grow.</p>
            </div>

            <!-- Billing Toggle -->
            <div class="flex justify-center mb-8 sm:mb-12 ">
                <div class="relative bg-gray-200 rounded-full p-1 flex ">
                    <div class="absolute top-1 left-1 w-1/2 h-8 sm:h-10 bg-white rounded-full shadow-md transition-transform duration-300"
                        id="toggle-slider"></div>
                    <button
                        class="relative z-10 w-28 sm:w-32 py-0 text-center font-medium rounded-full transition text-sm sm:text-base"
                        id="monthly-btn">
                        Monthly
                    </button>
                    <button
                        class="relative z-10 w-28 sm:w-32 py-0 text-center font-medium rounded-full transition text-sm sm:text-base flex items-center justify-center gap-1 m-2"
                        id="yearly-btn">

                        <span>Yearly<sup class="text-green-600 bg-green-100 rounded-full p-1 text-[10px]">20%
                                Save</sup></span>

                    </button>
                </div>
            </div>

            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-7 items-stretch pricing-cards">
                @foreach ($plans as $plan)
                    @php
                        // Decode features if stored as JSON
                        $features = is_array($plan->features) ? $plan->features : json_decode($plan->features, true);

                        $isPopular = $plan->slug === 'multi-branch';

                        // Card styling
                        $cardClasses = $isPopular
                            ? 'bg-white rounded-xl sm:rounded-2xl shadow-2xl border-2 border-orange-500 overflow-hidden relative'
                            : ($plan->slug === 'enterprise'
                                ? 'bg-gray-900 rounded-xl sm:rounded-2xl shadow-lg border border-gray-800 overflow-hidden'
                                : 'bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-200 overflow-hidden');

                        // Top content background
                        $contentBgClasses = $isPopular
                            ? 'gradient-orange p-6 sm:p-8 text-white'
                            : ($plan->slug === 'enterprise'
                                ? 'p-6 sm:p-8 text-white'
                                : 'p-6 sm:p-8');

                        // Button styling
                        $btnClasses = $isPopular
                            ? 'w-full gradient-orange text-white font-semibold py-2.5 sm:py-3 rounded-lg shadow-md hover:shadow-lg transition mb-6 sm:mb-8 text-sm sm:text-base'
                            : ($plan->slug === 'enterprise'
                                ? 'w-full bg-gray-800 hover:bg-gray-700 text-white font-semibold py-2.5 sm:py-3 rounded-lg transition mb-6 sm:mb-8 border border-gray-700 text-sm sm:text-base'
                                : 'w-full border-2 border-gray-300 text-gray-800 font-semibold py-2.5 sm:py-3 rounded-lg hover:border-orange-300 transition mb-6 sm:mb-8 text-sm sm:text-base');

                        // currency price
                        $priceData = $plan->getPriceForCurrency(1); // 1 = INR

                        $monthlyPrice = $priceData ? $priceData->monthly_price : null;

                        // Price formatting
                        $price = is_numeric($monthlyPrice)
                            ? session('currency_symbol') . number_format($monthlyPrice, 2)
                            : 'N/A';

                        $priceSuffix = is_numeric($monthlyPrice) ? '/month' : 'Pricing';
                    @endphp

                    <div class="pricing-card {{ $cardClasses }}">

                        {{-- Popular Badge --}}
                        @if ($isPopular)
                            <div
                                class="absolute top-0 right-4 sm:right-6 bg-orange-500 text-white text-xs font-bold py-1 sm:py-1.5 px-3 sm:px-4 rounded-b-lg">
                                MOST POPULAR
                            </div>
                        @endif

                        {{-- Top Content --}}
                        <div class="{{ $contentBgClasses }}">
                            <h3 class="text-xl sm:text-2xl font-bold mb-1 sm:mb-2">{{ $plan->name }}</h3>
                            <div class="flex items-end">
                                <span class="text-3xl sm:text-4xl font-bold">{{ $price }}</span>
                                <span
                                    class="ml-1 sm:ml-2 mb-1 {{ $isPopular ? 'opacity-90' : ($plan->slug === 'enterprise' ? 'text-gray-400' : 'text-gray-600') }} text-sm sm:text-base">
                                    {{ $priceSuffix }}
                                </span>
                            </div>

                            <p
                                class="{{ $isPopular ? 'mt-1 sm:mt-2 opacity-90 text-sm sm:text-base' : ($plan->slug === 'enterprise' ? 'text-gray-400 mt-1 sm:mt-2 text-sm sm:text-base' : 'text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base') }}">
                                @if ($plan->slug === 'single-outlet')
                                    Perfect for standalone restaurants
                                @elseif($isPopular)
                                    Up to {{ $plan->max_branches }} branches
                                @else
                                    Unlimited branches + white-label options
                                @endif
                            </p>
                        </div>

                        {{-- Bottom Content --}}
                        <div
                            class="p-6 sm:p-8 flex flex-col h-full {{ $plan->slug === 'enterprise' ? 'text-white' : 'text-gray-800' }}">


                            {{-- Button --}}
                            <a href="{{ $plan->slug === 'enterprise' ? 'javascript:void(0)' : route('checkout', ['plan' => $plan->slug]) }}"
                                class="{{ $btnClasses }} inline-block text-center transition-all duration-200">
                                @if ($plan->slug === 'enterprise')
                                    Contact Sales
                                @elseif($isPopular)
                                    Get Started
                                @else
                                    Start Free Trial
                                @endif
                            </a>

                            {{-- Features --}}
                            <div class="space-y-3 sm:space-y-4 ">
                                @if ($features)
                                    @foreach ($features as $feature)
                                        @php
                                            if (is_array($feature)) {
                                                $featureName = $feature['name'] ?? '';
                                                $isAvailable = $feature['available'] ?? true;
                                                $isBold = $feature['bold'] ?? false;
                                            } else {
                                                $featureName = $feature; // plain string
                                                $isAvailable = true;
                                                $isBold = false;
                                            }
                                        @endphp

                                        <div class="flex items-start {{ !$isAvailable ? 'opacity-50' : '' }}">
                                            <i
                                                class="fas {{ $isAvailable ? 'fa-check text-green-500' : 'fa-times text-gray-400' }} mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                            <span class="text-sm sm:text-base">{!! $isBold ? '<strong>' . $featureName . '</strong>' : $featureName !!}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-8 sm:mt-12 text-gray-600 text-sm sm:text-base">
                <p>All plans include a 14-day free trial. No credit card required.</p>
                <a href="{{ url('/pricing') }}"
                    class="inline-flex items-center justify-center mt-5 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 sm:py-3 px-6 sm:px-8 rounded-lg transition shadow-md">
                    View Full Features
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>


        </div>
    </section>

    <!-- Price Comparison Dashboard -->
    <section id="comparison" class="py-16 sm:py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">Smart Price
                    Comparison Dashboard</h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">Automatically compare suppliers and
                    save up to 25% on ingredients across all your branches.</p>
            </div>

            <div
                class="max-w-6xl mx-auto bg-white rounded-xl sm:rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                <div class="comparison-container flex flex-col lg:flex-row">
                    <!-- Left Panel - Ingredients -->
                    <div class="ingredients-panel lg:w-1/3 border-r border-gray-200">
                        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Ingredient Dashboard</h3>
                            <div class="mt-3 sm:mt-4 relative">
                                <input type="text" placeholder="Search ingredients..."
                                    class="w-full p-2.5 sm:p-3 pl-9 sm:pl-10 border border-gray-300 rounded-lg text-sm sm:text-base">
                                <i class="fas fa-search absolute left-3 top-2.5 sm:top-3.5 text-gray-400"></i>
                            </div>
                        </div>

                        <div class="p-4 sm:p-6">
                            <div class="mb-4 sm:mb-6 bg-white p-3 sm:p-4 rounded-lg border border-gray-200 shadow-sm">
                                <p class="text-xs sm:text-sm text-gray-600">Monthly Ingredient Spend</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">$12,450</p>
                                <div class="flex items-center mt-1 sm:mt-2">
                                    <span class="text-green-600 text-xs sm:text-sm font-medium">Potential Savings:
                                        18%</span>
                                    <span
                                        class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-1.5 sm:px-2 py-0.5 rounded">$2,241</span>
                                </div>
                            </div>

                            <h4 class="font-medium text-gray-700 mb-3 sm:mb-4 text-sm sm:text-base">Frequently
                                Purchased</h4>
                            <div class="space-y-2 sm:space-y-3">
                                <div class="p-3 rounded-lg border-l-4 border-orange-500 bg-orange-50 cursor-pointer">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-sm sm:text-base">Onion</span>
                                        <span class="text-xs sm:text-sm text-gray-600">$2.49/kg</span>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <span class="text-xs text-gray-500">Last ordered: 3 days ago</span>
                                        <span
                                            class="ml-2 text-xs bg-blue-100 text-blue-800 px-1.5 sm:px-2 py-0.5 rounded">3
                                            suppliers</span>
                                    </div>
                                </div>

                                <div class="p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-sm sm:text-base">Tomato</span>
                                        <span class="text-xs sm:text-sm text-gray-600">$3.29/kg</span>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <span class="text-xs text-gray-500">Last ordered: 1 day ago</span>
                                    </div>
                                </div>

                                <div class="p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-sm sm:text-base">Flour</span>
                                        <span class="text-xs sm:text-sm text-gray-600">$1.89/kg</span>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <span class="text-xs text-gray-500">Last ordered: 5 days ago</span>
                                    </div>
                                </div>

                                <div class="p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-sm sm:text-base">Chicken Breast</span>
                                        <span class="text-xs sm:text-sm text-gray-600">$8.99/kg</span>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <span class="text-xs text-gray-500">Last ordered: Today</span>
                                    </div>
                                </div>

                                <div class="p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-sm sm:text-base">Cooking Oil</span>
                                        <span class="text-xs sm:text-sm text-gray-600">$4.49/L</span>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <span class="text-xs text-gray-500">Last ordered: 2 days ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel - Supplier Comparison -->
                    <div class="suppliers-panel lg:w-2/3">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                <div class="mb-3 sm:mb-0">
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">Supplier Comparison: <span
                                            class="text-orange-500">Onion</span> (per kg)</h3>
                                    <p class="text-gray-600 text-sm sm:text-base">Prices updated 2 hours ago</p>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-xs sm:text-sm text-gray-600 mr-2 sm:mr-3">Auto-order when price
                                        drops 10%</span>
                                    <div class="relative inline-block w-10 sm:w-12 mr-2 align-middle select-none">
                                        <input type="checkbox" name="toggle" id="auto-order"
                                            class="toggle-checkbox absolute block w-4 h-4 sm:w-6 sm:h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                            checked />
                                        <label for="auto-order"
                                            class="toggle-label block overflow-hidden h-5 sm:h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 sm:p-7">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7 items-stretch">
                                <!-- Supplier 1 -->
                                <div class="h-full bg-white rounded-2xl border-2 border-green-500 shadow-md">
                                    <div class="p-5 sm:p-6 h-full flex flex-col">
                                        <div class="flex justify-between items-start gap-2 flex-wrap mb-4 sm:mb-5">
                                            <div>
                                                <h4 class="font-bold text-base sm:text-lg text-gray-900">Metro Foods
                                                </h4>
                                                <div class="flex items-center mt-1">
                                                    <div class="flex text-yellow-400 text-sm sm:text-base">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star-half-alt"></i>
                                                    </div>
                                                    <span
                                                        class="text-xs sm:text-sm text-gray-600 ml-1 sm:ml-2">4.2/5</span>
                                                </div>
                                            </div>
                                            <div
                                                class="inline-flex items-center justify-center shrink-0 bg-green-100 text-green-800 text-[10px] sm:text-xs font-bold leading-tight py-1 px-2 sm:px-3 rounded-lg whitespace-nowrap">
                                                LOWEST PRICE
                                            </div>
                                        </div>

                                        <div class="text-center py-4 sm:py-5">
                                            <p class="text-2xl sm:text-3xl font-bold text-gray-900">$2.49</p>
                                            <p class="text-xs sm:text-sm text-gray-600 mt-1">per kg</p>
                                        </div>

                                        <div class="space-y-2.5 sm:space-y-3 mb-6 sm:mb-7">
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Delivery</span>
                                                <span class="font-medium text-sm">2-4 hours</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Minimum Order</span>
                                                <span class="font-medium text-sm">$100</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Last Order</span>
                                                <span class="font-medium text-sm">3 days ago</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Quality Score</span>
                                                <span class="font-medium text-sm">9.2/10</span>
                                            </div>
                                        </div>

                                        <button
                                            class="mt-auto w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 sm:py-3 rounded-lg transition text-sm sm:text-base">
                                            Order Now
                                        </button>
                                    </div>
                                </div>

                                <!-- Supplier 2 -->
                                <div class="h-full bg-white rounded-2xl border border-gray-200 shadow-sm">
                                    <div class="p-5 sm:p-6 h-full flex flex-col">
                                        <div class="flex justify-between items-start gap-2 flex-wrap mb-4 sm:mb-5">
                                            <div>
                                                <h4 class="font-bold text-base sm:text-lg text-gray-900">Sysco</h4>
                                                <div class="flex items-center mt-1">
                                                    <div class="flex text-yellow-400 text-sm sm:text-base">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                    </div>
                                                    <span
                                                        class="text-xs sm:text-sm text-gray-600 ml-1 sm:ml-2">4.8/5</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center py-4 sm:py-5">
                                            <p class="text-2xl sm:text-3xl font-bold text-gray-900">$2.89</p>
                                            <p class="text-xs sm:text-sm text-gray-600 mt-1">per kg</p>
                                        </div>

                                        <div class="space-y-2.5 sm:space-y-3 mb-6 sm:mb-7">
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Delivery</span>
                                                <span class="font-medium text-sm">Next day</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Minimum Order</span>
                                                <span class="font-medium text-sm">$200</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Last Order</span>
                                                <span class="font-medium text-sm">1 week ago</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Quality Score</span>
                                                <span class="font-medium text-sm">9.5/10</span>
                                            </div>
                                        </div>

                                        <button
                                            class="mt-auto w-full border-2 border-gray-300 text-gray-800 font-semibold py-2.5 sm:py-3 rounded-lg hover:border-orange-300 transition text-sm sm:text-base">
                                            Compare
                                        </button>
                                    </div>
                                </div>

                                <!-- Supplier 3 -->
                                <div class="h-full bg-white rounded-2xl border border-gray-200 shadow-sm">
                                    <div class="p-5 sm:p-6 h-full flex flex-col">
                                        <div class="flex justify-between items-start gap-2 flex-wrap mb-4 sm:mb-5">
                                            <div>
                                                <h4 class="font-bold text-base sm:text-lg text-gray-900">Local Farm
                                                    Co-op</h4>
                                                <div class="flex items-center mt-1">
                                                    <div class="flex text-yellow-400 text-sm sm:text-base">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                    </div>
                                                    <span
                                                        class="text-xs sm:text-sm text-gray-600 ml-1 sm:ml-2">4.5/5</span>
                                                </div>
                                            </div>
                                            <div
                                                class="inline-flex items-center justify-center shrink-0 bg-blue-100 text-blue-800 text-[10px] sm:text-xs font-bold leading-tight py-1 px-2 sm:px-3 rounded-lg whitespace-nowrap">
                                                LOCAL
                                            </div>
                                        </div>

                                        <div class="text-center py-4 sm:py-5">
                                            <p class="text-2xl sm:text-3xl font-bold text-gray-900">$2.69</p>
                                            <p class="text-xs sm:text-sm text-gray-600 mt-1">per kg</p>
                                        </div>

                                        <div class="space-y-2.5 sm:space-y-3 mb-6 sm:mb-7">
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Delivery</span>
                                                <span class="font-medium text-sm">6-8 hours</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Minimum Order</span>
                                                <span class="font-medium text-sm">$50</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Last Order</span>
                                                <span class="font-medium text-sm">New supplier</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-600 text-sm">Quality Score</span>
                                                <span class="font-medium text-sm">9.8/10</span>
                                            </div>
                                        </div>

                                        <button
                                            class="mt-auto w-full border-2 border-gray-300 text-gray-800 font-semibold py-2.5 sm:py-3 rounded-lg hover:border-orange-300 transition text-sm sm:text-base">
                                            Compare
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                                <p class="text-gray-600 text-xs sm:text-sm"><i
                                        class="fas fa-info-circle text-blue-500 mr-2"></i> Based on your order history,
                                    Metro Foods offers the best value for onions across all 5 branches.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </section>

    <!-- RAP Admin Panel Preview -->
    <section id="dashboard" class="py-16 sm:py-20 bg-gray-900">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4">RestoChain RAP: Super
                    Admin Dashboard</h2>
                <p class="text-base sm:text-lg text-gray-300 max-w-2xl mx-auto">Centralized control for multi-branch
                    restaurant chains. Everything you need in one place.</p>
            </div>

            <div class="max-w-6xl mx-auto bg-gray-800 rounded-xl sm:rounded-2xl shadow-2xl overflow-hidden">
                <div class="admin-panel flex flex-col lg:flex-row">
                    <!-- Sidebar -->
                    <div class="admin-sidebar w-full lg:w-1/5 bg-gray-900 border-r border-gray-700">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center mb-6 sm:mb-8">
                                <div
                                    class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg gradient-orange flex items-center justify-center mr-2 sm:mr-3">
                                    <i class="fas fa-user-shield text-white text-xs sm:text-sm"></i>
                                </div>
                                <span class="text-lg sm:text-xl font-bold text-white">RAP<span
                                        class="text-orange-500">Admin</span></span>
                            </div>

                            <nav class="space-y-1 sm:space-y-2">
                                <a href="#"
                                    class="flex items-center p-2 sm:p-3 rounded-lg bg-gray-800 text-white text-sm sm:text-base">
                                    <i class="fas fa-tachometer-alt mr-2 sm:mr-3 text-orange-500 text-sm sm:text-base"></i>
                                    Dashboard
                                </a>
                                <a href="#"
                                    class="flex items-center p-2 sm:p-3 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm sm:text-base">
                                    <i class="fas fa-building mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    All Branches
                                </a>
                                <a href="#"
                                    class="flex items-center p-2 sm:p-3 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm sm:text-base">
                                    <i class="fas fa-boxes mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    Global Inventory
                                </a>
                                <a href="#"
                                    class="flex items-center p-2 sm:p-3 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm sm:text-base">
                                    <i class="fas fa-chart-bar mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    Financial Analytics
                                </a>
                                <a href="#"
                                    class="flex items-center p-2 sm:p-3 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm sm:text-base">
                                    <i class="fas fa-users mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    Staff Management
                                </a>
                                <a href="#"
                                    class="flex items-center p-2 sm:p-3 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm sm:text-base">
                                    <i class="fas fa-bell mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    Alerts & Notifications
                                </a>
                                <a href="#"
                                    class="flex items-center p-2 sm:p-3 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm sm:text-base">
                                    <i class="fas fa-cog mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    System Settings
                                </a>
                            </nav>
                        </div>

                        <div class="p-4 sm:p-6 border-t border-gray-700">
                            <div class="text-center">
                                <div
                                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-700 flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                    <i class="fas fa-user text-gray-300 text-sm sm:text-base"></i>
                                </div>
                                <p class="text-white font-medium text-sm sm:text-base">Super Admin</p>
                                <p class="text-xs text-gray-400">restochain@example.com</p>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="admin-main w-full lg:w-4/5">
                        <!-- Top Bar -->
                        <div
                            class="p-4 sm:p-6 border-b border-gray-700 flex flex-col sm:flex-row sm:justify-between sm:items-center">
                            <div class="mb-3 sm:mb-0">
                                <h3 class="text-lg sm:text-xl font-bold text-white">Good evening, Super Admin</h3>
                                <p class="text-gray-400 text-sm sm:text-base">Today: <span
                                        class="text-green-400 font-medium">$42,850</span> | Week: <span
                                        class="text-green-400 font-medium">$298,400</span></p>
                            </div>
                            <div class="flex items-center space-x-3 sm:space-x-4">
                                <div class="relative">
                                    <i class="fas fa-bell text-gray-300 text-lg sm:text-xl cursor-pointer"></i>
                                    <span
                                        class="absolute -top-1 -right-1 w-4 h-4 sm:w-5 sm:h-5 bg-orange-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                                </div>
                                <div
                                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-700 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-300"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Main Dashboard -->
                        <div class="p-4 sm:p-6">
                            <div class="flex flex-col lg:flex-row h-auto lg:h-96 mb-6 lg:mb-0">
                                <!-- Map -->
                                <div class="lg:w-1/2 mb-6 lg:mb-0 lg:pr-3">
                                    <div
                                        class="bg-gray-900 rounded-lg sm:rounded-xl p-3 sm:p-4 h-full border border-gray-700">
                                        <div
                                            class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 sm:mb-4">
                                            <h4 class="text-base sm:text-lg font-bold text-white mb-2 sm:mb-0">Branch
                                                Locations</h4>
                                            <div class="flex space-x-2">
                                                <button
                                                    class="text-xs bg-gray-700 text-gray-300 px-2 sm:px-3 py-1 rounded-lg">All</button>
                                                <button
                                                    class="text-xs text-gray-400 px-2 sm:px-3 py-1 rounded-lg">Active</button>
                                                <button
                                                    class="text-xs text-gray-400 px-2 sm:px-3 py-1 rounded-lg">Inactive</button>
                                            </div>
                                        </div>

                                        <!-- Simplified Map Visualization -->
                                        <div class="relative bg-gray-800 rounded-lg h-64 sm:h-full overflow-hidden">
                                            <!-- Map Background -->
                                            <div class="absolute inset-0 opacity-20">
                                                <div class="w-full h-full bg-gradient-to-br from-gray-700 to-gray-900">
                                                </div>
                                            </div>

                                            <!-- Map Points -->
                                            <div class="absolute top-1/4 left-1/4">
                                                <div class="relative">
                                                    <div
                                                        class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 rounded-full bg-green-500 border-2 sm:border-3 md:border-4 border-gray-900 flex items-center justify-center">
                                                        <i class="fas fa-utensils text-white text-xs"></i>
                                                    </div>
                                                    <div
                                                        class="absolute -top-5 sm:-top-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs py-0.5 sm:py-1 px-1 sm:px-2 rounded whitespace-nowrap">
                                                        Downtown (+14%)
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="absolute top-1/3 right-1/4">
                                                <div class="relative">
                                                    <div
                                                        class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 rounded-full bg-green-500 border-2 sm:border-3 md:border-4 border-gray-900 flex items-center justify-center">
                                                        <i class="fas fa-utensils text-white text-xs"></i>
                                                    </div>
                                                    <div
                                                        class="absolute -top-5 sm:-top-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs py-0.5 sm:py-1 px-1 sm:px-2 rounded whitespace-nowrap">
                                                        Westside (+8%)
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="absolute bottom-1/3 left-1/3">
                                                <div class="relative">
                                                    <div
                                                        class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 rounded-full bg-orange-500 border-2 sm:border-3 md:border-4 border-gray-900 flex items-center justify-center">
                                                        <i class="fas fa-utensils text-white text-xs"></i>
                                                    </div>
                                                    <div
                                                        class="absolute -top-5 sm:-top-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs py-0.5 sm:py-1 px-1 sm:px-2 rounded whitespace-nowrap">
                                                        East End (-2%)
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="absolute bottom-1/4 right-1/3">
                                                <div class="relative">
                                                    <div
                                                        class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 rounded-full bg-green-500 border-2 sm:border-3 md:border-4 border-gray-900 flex items-center justify-center">
                                                        <i class="fas fa-utensils text-white text-xs"></i>
                                                    </div>
                                                    <div
                                                        class="absolute -top-5 sm:-top-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs py-0.5 sm:py-1 px-1 sm:px-2 rounded whitespace-nowrap">
                                                        Uptown (+5%)
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Connection Lines -->
                                            <div
                                                class="absolute top-1/2 left-1/2 w-0.5 sm:w-1 h-12 sm:h-16 md:h-20 bg-gray-600 transform -translate-x-1/2 -rotate-45">
                                            </div>
                                            <div
                                                class="absolute top-1/2 left-1/2 w-0.5 sm:w-1 h-14 sm:h-18 md:h-24 bg-gray-600 transform -translate-x-1/2 rotate-45">
                                            </div>
                                            <div
                                                class="absolute top-1/2 left-1/2 w-0.5 sm:w-1 h-12 sm:h-16 md:h-20 bg-gray-600 transform -translate-x-1/2">
                                            </div>
                                            <div
                                                class="absolute top-1/2 left-1/2 w-0.5 sm:w-1 h-14 sm:h-18 md:h-24 bg-gray-600 transform -translate-x-1/2 -rotate-90">
                                            </div>

                                            <!-- Central Hub -->
                                            <div
                                                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                                <div
                                                    class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 rounded-full gradient-orange border-2 sm:border-3 md:border-4 border-gray-900 flex items-center justify-center">
                                                    <i class="fas fa-server text-white text-sm sm:text-base"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sales Graph -->
                                <div class="lg:w-1/2 lg:pl-3">
                                    <div
                                        class="bg-gray-900 rounded-lg sm:rounded-xl p-3 sm:p-4 h-full border border-gray-700">
                                        <div
                                            class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 sm:mb-4">
                                            <h4 class="text-base sm:text-lg font-bold text-white mb-2 sm:mb-0">
                                                Consolidated Sales (Last 30 Days)</h4>
                                            <div class="flex space-x-2">
                                                <button
                                                    class="text-xs bg-gray-700 text-gray-300 px-2 sm:px-3 py-1 rounded-lg">30D</button>
                                                <button
                                                    class="text-xs text-gray-400 px-2 sm:px-3 py-1 rounded-lg">90D</button>
                                                <button
                                                    class="text-xs text-gray-400 px-2 sm:px-3 py-1 rounded-lg">1Y</button>
                                            </div>
                                        </div>

                                        <!-- Simplified Graph -->
                                        <div class="h-64 sm:h-full flex flex-col">
                                            <!-- Graph Legend -->
                                            <div class="flex space-x-2 sm:space-x-4 mb-3 sm:mb-4">
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-orange-500 mr-1 sm:mr-2">
                                                    </div>
                                                    <span class="text-xs sm:text-sm text-gray-300">Revenue</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-gray-500 mr-1 sm:mr-2">
                                                    </div>
                                                    <span class="text-xs sm:text-sm text-gray-300">Costs</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-green-500 mr-1 sm:mr-2">
                                                    </div>
                                                    <span class="text-xs sm:text-sm text-gray-300">Net Profit</span>
                                                </div>
                                            </div>

                                            <!-- Graph Area -->
                                            <div class="relative flex-1">
                                                <!-- Y-axis labels -->
                                                <div
                                                    class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-gray-500 py-3 sm:py-4">
                                                    <span class="text-xs">$60k</span>
                                                    <span class="text-xs">$45k</span>
                                                    <span class="text-xs">$30k</span>
                                                    <span class="text-xs">$15k</span>
                                                    <span class="text-xs">$0</span>
                                                </div>

                                                <!-- Graph lines -->
                                                <div class="absolute left-8 sm:left-10 right-0 top-0 h-full">
                                                    <!-- Grid lines -->
                                                    <div class="h-full flex flex-col justify-between">
                                                        <div class="border-t border-gray-700"></div>
                                                        <div class="border-t border-gray-700"></div>
                                                        <div class="border-t border-gray-700"></div>
                                                        <div class="border-t border-gray-700"></div>
                                                        <div class="border-t border-gray-700"></div>
                                                    </div>

                                                    <!-- Revenue line (orange) -->
                                                    <div class="absolute top-1/4 left-0 w-full">
                                                        <div class="h-0.5 sm:h-1 bg-orange-500 rounded-full"></div>
                                                        <div class="flex justify-between mt-3 sm:mt-6">
                                                            <div
                                                                class="w-4 h-4 sm:w-6 sm:h-6 md:w-8 md:h-8 rounded-full bg-gray-900 border border-orange-500 flex items-center justify-center">
                                                                <div
                                                                    class="w-1 h-1 sm:w-1.5 sm:h-1.5 md:w-2 md:h-2 rounded-full bg-orange-500">
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="w-4 h-4 sm:w-6 sm:h-6 md:w-8 md:h-8 rounded-full bg-gray-900 border border-orange-500 flex items-center justify-center">
                                                                <div
                                                                    class="w-1 h-1 sm:w-1.5 sm:h-1.5 md:w-2 md:h-2 rounded-full bg-orange-500">
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="w-4 h-4 sm:w-6 sm:h-6 md:w-8 md:h-8 rounded-full bg-gray-900 border border-orange-500 flex items-center justify-center">
                                                                <div
                                                                    class="w-1 h-1 sm:w-1.5 sm:h-1.5 md:w-2 md:h-2 rounded-full bg-orange-500">
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="w-4 h-4 sm:w-6 sm:h-6 md:w-8 md:h-8 rounded-full bg-gray-900 border border-orange-500 flex items-center justify-center">
                                                                <div
                                                                    class="w-1 h-1 sm:w-1.5 sm:h-1.5 md:w-2 md:h-2 rounded-full bg-orange-500">
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="w-4 h-4 sm:w-6 sm:h-6 md:w-8 md:h-8 rounded-full bg-gray-900 border border-orange-500 flex items-center justify-center">
                                                                <div
                                                                    class="w-1 h-1 sm:w-1.5 sm:h-1.5 md:w-2 md:h-2 rounded-full bg-orange-500">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Profit line (green) -->
                                                    <div class="absolute top-1/2 left-0 w-full">
                                                        <div class="h-0.5 sm:h-1 bg-green-500 rounded-full"></div>
                                                    </div>

                                                    <!-- Cost line (gray) -->
                                                    <div class="absolute top-3/4 left-0 w-full">
                                                        <div class="h-0.5 sm:h-1 bg-gray-500 rounded-full"></div>
                                                    </div>

                                                    <!-- X-axis labels -->
                                                    <div
                                                        class="absolute -bottom-6 sm:-bottom-8 left-0 right-0 flex justify-between text-xs text-gray-500">
                                                        <span class="text-xs">Week 1</span>
                                                        <span class="text-xs">Week 2</span>
                                                        <span class="text-xs">Week 3</span>
                                                        <span class="text-xs">Week 4</span>
                                                        <span class="text-xs">Today</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Widgets -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-6">
                                <div class="bg-gray-900 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-700">
                                    <div class="flex items-center justify-between mb-2 sm:mb-3">
                                        <div
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-red-900/30 flex items-center justify-center">
                                            <i class="fas fa-exclamation-triangle text-red-500 text-sm sm:text-base"></i>
                                        </div>
                                        <span class="text-xs bg-red-900/50 text-red-300 px-1.5 sm:px-2 py-0.5 rounded">3
                                            Alerts</span>
                                    </div>
                                    <h5 class="text-white font-bold mb-1 text-sm sm:text-base">Inventory Alert</h5>
                                    <p class="text-xs sm:text-sm text-gray-400">3 items below reorder point</p>
                                </div>

                                <div class="bg-gray-900 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-700">
                                    <div class="flex items-center justify-between mb-2 sm:mb-3">
                                        <div
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-yellow-900/30 flex items-center justify-center">
                                            <i class="fas fa-users text-yellow-500 text-sm sm:text-base"></i>
                                        </div>
                                        <span
                                            class="text-xs bg-yellow-900/50 text-yellow-300 px-1.5 sm:px-2 py-0.5 rounded">Action
                                            Needed</span>
                                    </div>
                                    <h5 class="text-white font-bold mb-1 text-sm sm:text-base">Staff Scheduling</h5>
                                    <p class="text-xs sm:text-sm text-gray-400">4 shifts need coverage tomorrow</p>
                                </div>

                                <div class="bg-gray-900 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-700">
                                    <div class="flex items-center justify-between mb-2 sm:mb-3">
                                        <div
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-green-900/30 flex items-center justify-center">
                                            <i class="fas fa-chart-line text-green-500 text-sm sm:text-base"></i>
                                        </div>
                                        <span
                                            class="text-xs bg-green-900/50 text-green-300 px-1.5 sm:px-2 py-0.5 rounded">+14%</span>
                                    </div>
                                    <h5 class="text-white font-bold mb-1 text-sm sm:text-base">Financial Summary</h5>
                                    <p class="text-xs sm:text-sm text-gray-400">Top branch: Downtown (+14%)</p>
                                </div>

                                <div class="bg-gray-900 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-700">
                                    <div class="flex items-center justify-between mb-2 sm:mb-3">
                                        <div
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-blue-900/30 flex items-center justify-center">
                                            <i class="fas fa-heartbeat text-blue-500 text-sm sm:text-base"></i>
                                        </div>
                                        <span
                                            class="text-xs bg-blue-900/50 text-blue-300 px-1.5 sm:px-2 py-0.5 rounded">All
                                            Systems Go</span>
                                    </div>
                                    <h5 class="text-white font-bold mb-1 text-sm sm:text-base">System Health</h5>
                                    <p class="text-xs sm:text-sm text-gray-400">All systems operational</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-8 sm:mt-12">
                <button
                    class="gradient-orange text-white font-semibold py-2.5 sm:py-3 px-6 sm:px-8 rounded-lg shadow-lg hover:shadow-xl transition text-sm sm:text-base">
                    Request a Demo of RAP Admin
                </button>
            </div>
        </div>
    </section>

    <!-- Enquiry Form Section -->
    <section id="enquiry" class="py-16 sm:py-20 bg-white border-t border-gray-200">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-6xl mx-auto bg-gray-50 border border-gray-200 rounded-2xl p-6 sm:p-8 lg:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10 items-stretch">
                    <div class="h-full flex flex-col">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">Need Support or Have an Enquiry?</h2>
                        <p class="text-gray-600 text-sm sm:text-base mb-6">
                            Share your requirement with us. Our support and onboarding team will connect with you quickly.
                        </p>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center gap-3 text-sm sm:text-base text-gray-700">
                                <i class="fas fa-headset text-orange-500"></i>
                                <span>Dedicated implementation support</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm sm:text-base text-gray-700">
                                <i class="fas fa-clock text-orange-500"></i>
                                <span>Fast response from our technical team</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm sm:text-base text-gray-700">
                                <i class="fas fa-shield-alt text-orange-500"></i>
                                <span>Secure and reliable assistance</span>
                            </div>
                        </div>

                        <div
                            class="relative w-[280px] h-[280px] sm:w-[340px] sm:h-[340px] mt-auto mx-auto lg:mx-0 flex items-end justify-center">
                            <div class="absolute inset-0 rounded-full border border-orange-200"></div>
                            <div class="absolute inset-4 rounded-full bg-orange-100"></div>
                            <img src="{{ asset('images/support.png') }}" alt="Support Enquiry"
                                class="relative z-10 w-full h-full object-contain object-bottom p-3">
                        </div>
                    </div>

                    <form class="space-y-5 bg-white border border-gray-200 rounded-xl p-5 sm:p-6 h-full flex flex-col">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="enquiry-name" class="block text-sm font-medium text-gray-700 mb-2">Full
                                    Name</label>
                                <input id="enquiry-name" type="text"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                    placeholder="Enter your name">
                            </div>
                            <div>
                                <label for="enquiry-phone" class="block text-sm font-medium text-gray-700 mb-2">Phone
                                    Number</label>
                                <input id="enquiry-phone" type="tel"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                    placeholder="Enter your phone number">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="enquiry-email"
                                    class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input id="enquiry-email" type="email"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                    placeholder="Enter your email">
                            </div>
                            <div>
                                <label for="enquiry-subject"
                                    class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                <input id="enquiry-subject" type="text"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                    placeholder="Enter enquiry subject">
                            </div>
                        </div>

                        <div class="flex-1">
                            <label for="enquiry-message"
                                class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea id="enquiry-message" rows="6"
                                class="w-full min-h-[180px] rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                placeholder="Write your requirement..."></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="gradient-orange text-white font-semibold px-8 py-3 rounded-lg shadow-md hover:shadow-lg transition">
                                Submit Enquiry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>


    <!-- JavaScript -->
    <script>
        // Pricing Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const monthlyBtn = document.getElementById('monthly-btn');
            const yearlyBtn = document.getElementById('yearly-btn');
            const toggleSlider = document.getElementById('toggle-slider');

            monthlyBtn.addEventListener('click', function() {
                toggleSlider.style.transform = 'translateX(0)';
                monthlyBtn.classList.add('text-orange-600');
                yearlyBtn.classList.remove('text-orange-600');
            });

            yearlyBtn.addEventListener('click', function() {
                toggleSlider.style.transform = 'translateX(100%)';
                yearlyBtn.classList.add('text-orange-600');
                monthlyBtn.classList.remove('text-orange-600');
            });



            // Auto-order toggle
            const autoOrderToggle = document.getElementById('auto-order');
            autoOrderToggle.addEventListener('change', function() {
                const label = document.querySelector('.toggle-label');
                if (this.checked) {
                    label.classList.remove('bg-gray-300');
                    label.classList.add('toggle-checked');
                } else {
                    label.classList.remove('toggle-checked');
                    label.classList.add('bg-gray-300');
                }
            });

            // Mobile Menu Toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeMenuButton = document.getElementById('close-menu');
            const mobileMenu = document.getElementById('mobile-menu');

            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            closeMenuButton.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = 'auto';
            });

            // Close menu when clicking on a link
            const mobileLinks = document.querySelectorAll('#mobile-menu a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.remove('active');
                    document.body.style.overflow = 'auto';
                });
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;

                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Add hover effect to pricing cards on desktop only
            if (window.innerWidth > 768) {
                const pricingCards = document.querySelectorAll('.card-hover');
                pricingCards.forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-8px)';
                        this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
                    });

                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '';
                    });
                });
            }

            // Simulate real-time updates for the admin dashboard
            function updateDashboardStats() {
                // Randomly update the stats
                const todayStat = document.querySelector('.text-green-400:nth-child(1)');
                const weekStat = document.querySelector('.text-green-400:nth-child(2)');

                if (todayStat && weekStat) {
                    // Simulate small fluctuations
                    const todayChange = (Math.random() * 2000 - 1000).toFixed(0);
                    const weekChange = (Math.random() * 5000 - 2500).toFixed(0);

                    const todayValue = 42850 + parseInt(todayChange);
                    const weekValue = 298400 + parseInt(weekChange);

                    todayStat.textContent = `$${todayValue.toLocaleString()}`;
                    weekStat.textContent = `$${weekValue.toLocaleString()}`;
                }
            }

            // Update stats every 10 seconds
            setInterval(updateDashboardStats, 10000);

            // Handle window resize
            window.addEventListener('resize', function() {
                // Adjust mobile menu if window is resized to desktop
                if (window.innerWidth > 768) {
                    mobileMenu.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });
    </script>
@endsection
