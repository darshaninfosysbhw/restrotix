<!-- Hero Section -->
<section class="restrotix-hero relative overflow-x-clip overflow-y-visible bg-white lg:-mt-16 lg:pb-24">
    <div class="hero-backdrop absolute inset-x-0 top-0 bottom-24 hidden lg:block"
        style="background-image:url('{{ asset('images/restrotix-hero-seamless-v2.png') }}'); background-repeat:no-repeat; background-position:center; background-size:100% 100%;">
        <div class="absolute left-[41%] top-20 z-30 -rotate-6 text-center text-sm font-bold italic leading-tight text-white drop-shadow-[0_2px_3px_rgba(0,0,0,0.9)] xl:text-base">
            Your restaurant<br>grows here!
            <svg class="absolute -right-12 -top-1 h-11 w-14 overflow-visible" viewBox="0 0 56 44"
                fill="none" aria-hidden="true">
                <path d="M3 7C21 1 42 5 44 19C45 25 41 30 35 33"
                    stroke="white" stroke-width="3" stroke-linecap="round"
                    filter="drop-shadow(0 2px 2px rgba(0,0,0,.65))" />
                <path d="M39 25L34 34L44 32" stroke="white" stroke-width="3"
                    stroke-linecap="round" stroke-linejoin="round"
                    filter="drop-shadow(0 2px 2px rgba(0,0,0,.65))" />
            </svg>
            <svg class="absolute -right-5 top-10 h-12 w-20 overflow-visible" viewBox="0 0 80 48"
                fill="none" aria-hidden="true">
                <path d="M3 4C11 25 35 34 66 27" stroke="white" stroke-width="3"
                    stroke-linecap="round" filter="drop-shadow(0 2px 2px rgba(0,0,0,.65))" />
                <path d="M57 22L68 27L59 34" stroke="white" stroke-width="3"
                    stroke-linecap="round" stroke-linejoin="round"
                    filter="drop-shadow(0 2px 2px rgba(0,0,0,.65))" />
            </svg>
        </div>
    </div>

    <div class="hero-shell container relative mx-auto px-4 pb-6 pt-8 sm:px-6 lg:min-h-[690px] lg:pb-32 lg:pt-0">
        <div class="hero-copy max-w-xl lg:w-[43%] lg:pt-32">
            <div class="mb-5 inline-flex items-center rounded-full border border-[#a52a28]/25 bg-[#a52a28]/5 px-3 py-1.5 text-xs font-semibold text-[#851817]">
                <span class="mr-2 flex h-5 w-5 items-center justify-center rounded-full bg-[#a52a28] text-[10px] text-white">
                    <i class="fas fa-utensils"></i>
                </span>
                All the tools your restaurant needs in one powerful system
            </div>

            <h1 class="hero-title text-[42px] font-black leading-[0.98] tracking-[-0.04em] text-slate-950 sm:text-5xl lg:text-[64px]">
                Your Restaurant.<br>
                Now <span class="text-[#a52a28]">Smarter!</span>
            </h1>

            <p class="mt-5 max-w-lg text-sm font-semibold leading-6 tracking-[0.01em] text-slate-700 sm:text-[17px] sm:leading-7">
                Centralize Orders, Tables, Menus, Billing, Inventory, Staff, and Every Branch in One Powerful, Reliable Platform.
            </p>

            <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                <a href="#pricing"
                    class="inline-flex items-center justify-center rounded-lg bg-[#a52a28] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-[#a52a28]/20 transition hover:-translate-y-0.5 hover:bg-[#851817] hover:shadow-xl">
                    Start Free Trial <i class="fas fa-arrow-right ml-2"></i>
                </a>
                <button
                    class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white/95 px-6 py-3.5 text-sm font-bold text-slate-800 shadow-sm transition hover:border-emerald-300">
                    <span class="mr-2 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-[8px] text-white">
                        <i class="fas fa-play"></i>
                    </span>
                    Watch Demo
                </button>
            </div>

            <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-[10px] font-semibold text-slate-500 sm:text-xs">
                <span><i class="fas fa-circle-check mr-1 text-emerald-500"></i>No credit card required</span>
                <span><i class="fas fa-circle-check mr-1 text-emerald-500"></i>Setup in minutes</span>
                <span class="lg:basis-full 2xl:basis-auto"><i class="fas fa-circle-check mr-1 text-emerald-500"></i>Trusted by 1,000+ restaurants</span>
            </div>
        </div>

        <div class="relative mt-8 lg:hidden">
            <img src="{{ asset('images/restrotix-hero-seamless-v2.png') }}"
                alt="Restrotix restaurant management dashboard across desktop, tablet and mobile devices"
                class="h-auto w-full rounded-2xl object-contain shadow-2xl" width="1536" height="1024" fetchpriority="high">
        </div>

        <div class="hero-features relative z-20 mt-7 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_20px_50px_-20px_rgba(15,23,42,0.3)] lg:absolute lg:inset-x-6 lg:bottom-0 lg:mt-0 lg:translate-y-[70px]">
            @php
                $heroFeatures = [
                    ['fa-cash-register', 'Smart POS', 'Fast & easy billing', 'orange'],
                    ['fa-qrcode', 'QR Ordering', 'Contactless dining', 'emerald'],
                    ['fa-chair', 'Table Management', 'Better table control', 'orange'],
                    ['fa-boxes', 'Inventory Management', 'Track stock in real time', 'emerald'],
                    ['fa-store', 'Multi-Branch Support', 'Manage multiple outlets', 'orange'],
                    ['fa-users-cog', 'Staff Management', 'Roles & permissions', 'emerald'],
                    ['fa-chart-pie', 'Reports & Analytics', 'Make data-driven decisions', 'orange'],
                    ['fa-th', 'More Features', 'And much more...', 'emerald'],
                ];
            @endphp

            <div class="grid grid-cols-2 divide-x divide-y divide-slate-100 sm:grid-cols-4 lg:grid-cols-8 lg:divide-y-0">
                @foreach ($heroFeatures as [$icon, $title, $subtitle, $color])
                    <div class="hero-feature px-2 py-4 text-center sm:px-3">
                        <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full text-white {{ $color === 'orange' ? 'bg-[#a52a28]' : 'bg-emerald-500' }}">
                            <i class="fas {{ $icon }} text-sm"></i>
                        </span>
                        <p class="text-[11px] font-extrabold leading-tight text-slate-800">{{ $title }}</p>
                        <p class="mt-1 text-[9px] leading-tight text-slate-500">{{ $subtitle }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<style>
    @media (min-width: 1024px) {
        .restrotix-hero .hero-shell {
            min-height: clamp(590px, calc(100vh - 88px), 690px);
            max-width: none;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .restrotix-hero .hero-title {
            font-size: clamp(3.15rem, 4.35vw, 4rem);
        }
    }

    @media (min-width: 1024px) and (max-width: 1279px) {
        .restrotix-hero {
            margin-top: 0;
        }

        .restrotix-hero .hero-shell {
            min-height: 590px;
            padding-bottom: 7rem;
        }

        .restrotix-hero .hero-copy {
            width: 45%;
            padding-top: 6.5rem;
        }

        .restrotix-hero .hero-title {
            font-size: 3.15rem;
        }

        .restrotix-hero .hero-backdrop {
            background-size: 100% 100% !important;
            background-position: center !important;
        }

        .restrotix-hero .hero-feature {
            padding: 0.7rem 0.3rem;
        }

        .restrotix-hero .hero-feature > span {
            width: 2.1rem;
            height: 2.1rem;
            margin-bottom: 0.35rem;
        }
    }

    @media (min-width: 1280px) and (max-height: 800px) {
        .restrotix-hero .hero-shell {
            min-height: 620px;
            padding-bottom: 7rem;
        }

        .restrotix-hero .hero-copy {
            padding-top: 6.5rem;
        }

        .restrotix-hero .hero-title {
            font-size: 3.4rem;
        }

        .restrotix-hero .hero-backdrop {
            background-size: 100% 100% !important;
            background-position: center !important;
        }

        .restrotix-hero .hero-feature {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    }

    @media (max-width: 1023px) {
        .restrotix-hero .hero-copy {
            margin-inline: auto;
            text-align: center;
        }

        .restrotix-hero .hero-copy > div {
            justify-content: center;
        }

        .restrotix-hero .hero-copy p {
            margin-inline: auto;
        }
    }

    @media (max-width: 639px) {
        .restrotix-hero .hero-title {
            font-size: clamp(2.35rem, 11vw, 2.75rem);
        }

        .restrotix-hero .hero-feature {
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
        }
    }
</style>
