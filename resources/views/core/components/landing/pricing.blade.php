<!-- Pricing Section -->
@props(['plans'])

<section id="pricing" class="relative overflow-hidden bg-[#fbfcfb] py-16 sm:py-20">
    <div class="pointer-events-none absolute -left-20 top-8 h-52 w-52 rounded-full bg-orange-100/60 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-20 bottom-8 h-56 w-56 rounded-full bg-emerald-100/60 blur-3xl"></div>

    <div class="container relative mx-auto px-4 sm:px-6">
        <div class="mx-auto mb-9 max-w-3xl text-center">
            <p class="mb-2 text-[11px] font-extrabold uppercase tracking-[0.2em] text-[#a52a28]">Plans for Every Restaurant</p>
            <h2 class="text-3xl font-black tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">
                <span class="text-[#e43d20]">Simple &amp; Transparent</span><span class="text-emerald-700"> Pricing</span>
            </h2>
            <p class="mx-auto mt-3 max-w-xl text-sm text-slate-600 sm:text-base">Flexible plans built for restaurants of every size, from a single outlet to a growing multi-branch brand.</p>
        </div>

        <div class="pricing-toggle-wrap mb-10 mt-14 flex justify-center sm:mt-16">
            <div class="pricing-toggle relative flex rounded-full border border-slate-200 bg-white p-1 shadow-md">
                <div class="pricing-toggle-callout absolute -top-14 right-1 z-20 -rotate-3 whitespace-nowrap text-center text-sm font-extrabold italic leading-tight text-emerald-600 drop-shadow-[0_1px_1px_rgba(255,255,255,0.9)] sm:text-base">
                    Save 20%<br>Here!
                    <svg class="absolute -bottom-12 -right-10 h-20 w-16 overflow-visible" viewBox="0 0 64 80" fill="none" aria-hidden="true">
                        <path d="M28 4C50 10 57 30 49 50C45 61 38 67 27 70" stroke="#059669" stroke-width="3" stroke-linecap="butt" />
                        <path d="M35 61L25 71L39 74" stroke="#059669" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div id="toggle-slider" class="absolute bottom-1 left-1 top-1 w-1/2 rounded-full bg-[#a52a28] shadow-sm transition-transform duration-300"></div>
                <button id="monthly-btn" type="button" class="relative z-10 w-24 rounded-full py-2 text-center text-sm font-bold text-white transition sm:w-28">Monthly</button>
                <button id="yearly-btn" type="button" class="relative z-10 w-24 rounded-full py-2 text-center text-sm font-bold text-slate-700 transition sm:w-28">Yearly</button>
            </div>
        </div>

        <div class="pricing-cards mx-auto grid max-w-7xl grid-cols-1 items-stretch gap-6 lg:grid-cols-3 lg:gap-5">
            @foreach ($plans as $plan)
                @php
                    $features = $plan->getDisplayFeatures();
                    $isPopular = $plan->slug === 'plus';
                    $isEnterprise = $plan->slug === 'enterprise';
                    $currencyId = session('currency_id');
                    $priceData = $currencyId ? $plan->prices->firstWhere('currency_id', $currencyId) : null;
                    $priceData = $priceData ?? $plan->prices->first();
                    $currencySymbol = trim((string) ($priceData?->currency?->symbol ?? session('currency_symbol', 'Rs.')));
                    $monthlyPrice = is_numeric($priceData?->monthly_price ?? null) ? (float) $priceData->monthly_price : null;
                    $yearlyPrice = is_numeric($priceData?->yearly_price ?? null) ? (float) $priceData->yearly_price : ($monthlyPrice !== null ? $monthlyPrice * 10 : null);
                    $price = $monthlyPrice !== null ? $currencySymbol.' '.number_format($monthlyPrice, 2) : 'N/A';
                    $priceSuffix = $monthlyPrice !== null ? '/month' : 'Pricing';
                    $planKey = strtolower(trim((string) $plan->slug.' '.(string) $plan->name));
                    $icon = str_contains($planKey, 'enterprise')
                        ? 'fa-building'
                        : (str_contains($planKey, 'multi') || str_contains($planKey, 'branch')
                            ? 'fa-code-branch'
                            : 'fa-store');
                    $iconClasses = 'bg-[#a52a28]/10 text-[#a52a28]';
                @endphp

                <article data-pricing-card
                    style="background: linear-gradient(180deg, rgba(165,42,40,0.065) 0%, rgba(165,42,40,0.025) 18%, #ffffff 36%, #ffffff 72%, rgba(165,42,40,0.04) 100%);"
                    class="pricing-card group relative flex h-full flex-col overflow-visible rounded-2xl border px-5 pb-6 pt-5 transition duration-300 hover:-translate-y-1 hover:shadow-xl sm:px-6 {{ $isPopular ? 'border-2 border-[#f05a28] shadow-[0_18px_45px_-22px_rgba(240,90,40,0.55)]' : 'border-slate-200 shadow-[0_14px_35px_-25px_rgba(15,23,42,0.35)]' }}">
                    @if ($isPopular)
                        <span class="absolute -top-4 left-1/2 -translate-x-1/2 rounded-full bg-[#f05a28] px-5 py-1.5 text-[10px] font-extrabold uppercase tracking-[0.16em] text-white shadow-md">Most Popular</span>
                    @endif

                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $iconClasses }}"><i class="fas {{ $icon }} text-lg"></i></span>
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900">{{ $plan->name }}</h3>
                            <p class="mt-0.5 text-xs leading-5 text-slate-500">{{ $plan->marketing_summary }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex items-end border-b border-slate-100 pb-4">
                        <span data-plan-price data-currency-symbol="{{ $currencySymbol }}"
                            data-monthly-price="{{ $monthlyPrice !== null ? number_format($monthlyPrice, 2, '.', '') : '' }}"
                            data-yearly-price="{{ $yearlyPrice !== null ? number_format($yearlyPrice, 2, '.', '') : '' }}"
                            class="min-w-0 whitespace-nowrap text-3xl font-black tracking-tight text-slate-950 sm:text-4xl lg:text-[clamp(1.8rem,2.7vw,2.25rem)]">{{ $price }}</span>
                        <span class="mb-1 ml-2 text-sm font-medium text-slate-500" data-plan-price-suffix>{{ $priceSuffix }}</span>
                    </div>

                    <a href="{{ $isEnterprise ? '#enquiry' : route('checkout', ['plan' => $plan->slug, 'billing_cycle' => 'monthly']) }}"
                        @if (!$isEnterprise) data-checkout-link="1" data-checkout-base-url="{{ route('checkout', ['plan' => $plan->slug]) }}" @endif
                        class="mt-4 inline-flex w-full items-center justify-center rounded-lg border-2 px-4 py-2.5 text-sm font-bold transition {{ $isPopular ? 'border-[#a52a28] bg-[#a52a28] text-white shadow-md hover:bg-[#851817] hover:shadow-lg' : 'border-[#a52a28] text-[#a52a28] hover:bg-[#a52a28] hover:text-white' }}">
                        {{ $isEnterprise ? 'Contact Sales' : 'Start Free Trial' }}
                        @if ($isPopular)<i class="fas fa-arrow-right ml-2 text-xs"></i>@endif
                    </a>

                    <div class="mt-5 flex-1 space-y-2.5">
                        @foreach (($features ?? []) as $feature)
                            @php
                                $featureName = is_array($feature) ? ($feature['name'] ?? '') : $feature;
                                $isAvailable = is_array($feature) ? ($feature['available'] ?? true) : true;
                                $isBold = is_array($feature) ? ($feature['bold'] ?? false) : false;
                            @endphp
                            <div class="flex items-start gap-2.5 {{ !$isAvailable ? 'opacity-45' : '' }}">
                                <i class="fas {{ $isAvailable ? 'fa-check text-emerald-500' : 'fa-times text-slate-300' }} mt-0.5 text-xs"></i>
                                <span class="text-xs leading-5 text-slate-600 sm:text-sm {{ $isBold ? 'font-bold text-slate-800' : '' }}">{{ $featureName }}</span>
                            </div>
                        @endforeach
                    </div>

                    <span class="pointer-events-none absolute bottom-0 right-0 h-14 w-20 overflow-hidden rounded-br-2xl" aria-hidden="true">
                        <span class="absolute -bottom-10 -right-8 h-24 w-24 rounded-full bg-[#a52a28]/[0.07]"></span>
                    </span>
                </article>
            @endforeach
        </div>

        <div class="mt-9 text-center text-sm text-slate-500">
            <p><i class="fas fa-circle-check mr-1.5 text-emerald-500"></i>All plans include a 14-day free trial. No credit card required.</p>
            <a href="#enquiry" class="mt-4 inline-flex items-center font-bold text-[#a52a28] transition hover:text-[#851817]">Compare All Features <i class="fas fa-arrow-right ml-2 text-xs"></i></a>
        </div>
    </div>
</section>

<style>
    #pricing #monthly-btn[aria-pressed="true"], #pricing #yearly-btn[aria-pressed="true"] { color: #fff !important; }
    #pricing #monthly-btn[aria-pressed="false"], #pricing #yearly-btn[aria-pressed="false"] { color: #334155 !important; }

    @media (max-width: 639px) {
        #pricing .pricing-toggle-wrap {
            margin-top: 4.5rem;
        }

        #pricing .pricing-toggle {
            width: min(100%, 19rem);
        }

        #pricing .pricing-toggle > button {
            flex: 1 1 50%;
            width: auto;
        }

        #pricing .pricing-toggle-callout {
            right: 0.75rem;
            font-size: 0.78rem;
        }

        #pricing .pricing-toggle-callout svg {
            right: -1.25rem;
            width: 3.25rem;
        }

        #pricing [data-plan-price] {
            font-size: clamp(1.75rem, 9vw, 2.25rem);
        }
    }

    @media (min-width: 640px) and (max-width: 1023px) {
        #pricing .pricing-cards {
            max-width: 42rem;
        }
    }
</style>
