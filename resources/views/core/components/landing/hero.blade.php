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
                        Unified Management for <span class="text-[#851817]">Multi-Branch</span> Restaurant Chains
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
                            <i class="fas fa-play-circle mr-3 text-[#851817]"></i>
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
                                    class="text-[#851817]">500+</span> restaurant owners</p>
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
                                <div
                                    class="w-12 h-12 sm:w-16 sm:h-16 bg-purple-100 border-2 border-purple-300 relative">
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
                                <div
                                    class="w-12 h-12 sm:w-16 sm:h-16 bg-yellow-100 border-2 border-yellow-300 relative">
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
