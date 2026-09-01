<!-- Pricing Section -->
@props(['plans'])
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
                    id="monthly-btn" type="button">
                    Monthly
                </button>
                <button
                    class="relative z-10 w-28 sm:w-32 py-0 text-center font-medium rounded-full transition text-sm sm:text-base flex items-center justify-center gap-1 m-2"
                    id="yearly-btn" type="button">

                    <span>Yearly<sup class="text-green-600 bg-green-100 rounded-full p-1 text-[10px]">20%
                            Save</sup></span>

                </button>
            </div>
        </div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-7 items-stretch pricing-cards">
            @foreach ($plans as $plan)
                @php
                    $features = $plan->getDisplayFeatures();

                    $isPopular = $plan->slug === 'plus';

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
                @endphp

                <div class="pricing-card {{ $cardClasses }}" data-pricing-card>
                    @php
                        $currencyId = session('currency_id');
                        $priceData = $currencyId ? $plan->prices->firstWhere('currency_id', $currencyId) : null;
                        $priceData = $priceData ?? $plan->prices->first();
                        $currencySymbol = trim((string) ($priceData?->currency?->symbol ?? session('currency_symbol', '₹')));
                        $monthlyPrice = is_numeric($priceData?->monthly_price ?? null)
                            ? (float) $priceData->monthly_price
                            : null;
                        $yearlyPrice = is_numeric($priceData?->yearly_price ?? null)
                            ? (float) $priceData->yearly_price
                            : ($monthlyPrice !== null ? $monthlyPrice * 10 : null);

                        $price = $monthlyPrice !== null
                            ? $currencySymbol . ' ' . number_format($monthlyPrice, 2)
                            : 'N/A';

                        $priceSuffix = $monthlyPrice !== null ? '/month' : 'Pricing';
                    @endphp

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
                            <span class="text-3xl sm:text-4xl font-bold whitespace-nowrap"
                                data-plan-price
                                data-currency-symbol="{{ $currencySymbol }}"
                                data-monthly-price="{{ $monthlyPrice !== null ? number_format($monthlyPrice, 2, '.', '') : '' }}"
                                data-yearly-price="{{ $yearlyPrice !== null ? number_format($yearlyPrice, 2, '.', '') : '' }}">
                                {{ $price }}
                            </span>
                            <span
                                class="ml-1 sm:ml-2 mb-1 {{ $isPopular ? 'opacity-90' : ($plan->slug === 'enterprise' ? 'text-gray-400' : 'text-gray-600') }} text-sm sm:text-base">
                                <span data-plan-price-suffix>{{ $priceSuffix }}</span>
                            </span>
                        </div>

                        <p
                            class="{{ $isPopular ? 'mt-1 sm:mt-2 opacity-90 text-sm sm:text-base' : ($plan->slug === 'enterprise' ? 'text-gray-400 mt-1 sm:mt-2 text-sm sm:text-base' : 'text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base') }}">
                            {{ $plan->marketing_summary }}
                        </p>
                    </div>

                    {{-- Bottom Content --}}
                    <div
                        class="p-6 sm:p-8 flex flex-col h-full {{ $plan->slug === 'enterprise' ? 'text-white' : 'text-gray-800' }}">


                        {{-- Button --}}
                        <a href="{{ $plan->slug === 'enterprise' ? 'javascript:void(0)' : route('checkout', ['plan' => $plan->slug, 'billing_cycle' => 'monthly']) }}"
                            @if ($plan->slug !== 'enterprise')
                                data-checkout-link="1"
                                data-checkout-base-url="{{ route('checkout', ['plan' => $plan->slug]) }}"
                            @endif
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
                                            $featureName = $feature;
                                            $isAvailable = true;
                                            $isBold = false;
                                        }
                                    @endphp

                                    <div class="flex items-start {{ !$isAvailable ? 'opacity-50' : '' }}">
                                        <i
                                            class="fas {{ $isAvailable ? 'fa-check text-green-500' : 'fa-times text-gray-400' }} mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                        <span class="text-sm sm:text-base">
                                            @if ($isBold)
                                                <strong>{{ $featureName }}</strong>
                                            @else
                                                {{ $featureName }}
                                            @endif
                                        </span>
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
            <a href="#pricing"
                class="inline-flex items-center justify-center mt-5 bg-[#a52a28] hover:bg-[#851817] text-white font-semibold py-2.5 sm:py-3 px-6 sm:px-8 rounded-lg transition shadow-md">
                View Full Features
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>


    </div>
</section>
