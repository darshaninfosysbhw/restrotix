<section class="relative isolate overflow-hidden border-t border-white/20 bg-[#16100d] text-white"
    style="background-image: url('{{ asset('images/restrotix-hero-seamless-v2.png') }}'); background-position: center 70%; background-size: cover;">
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/85 via-black/70 to-black/40"></div>
    <div class="absolute inset-0 -z-10 bg-[#30120a]/20"></div>

    <div class="container mx-auto grid min-h-[150px] items-center gap-6 px-5 py-6 sm:px-6 lg:grid-cols-[1.35fr_auto_0.8fr] lg:gap-10 lg:py-5">
        <div>
            <h2 class="text-xl font-black leading-tight sm:text-2xl">Make Your Restaurant Smarter Today!</h2>
            <p class="mt-1 text-sm text-white/80">Join hundreds of restaurants already growing with Restrotix.</p>

            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-[11px] font-semibold text-white/90 sm:text-xs">
                <span><i class="fa-solid fa-circle-check mr-1.5 text-emerald-400"></i>Easy setup</span>
                <span><i class="fa-solid fa-circle-check mr-1.5 text-emerald-400"></i>Nepal-based support</span>
                <span><i class="fa-solid fa-circle-check mr-1.5 text-emerald-400"></i>Trusted by 1,000+ restaurants</span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 lg:justify-center">
            <a href="{{ url('/') }}#pricing" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-gradient-to-r from-[#DC0812] to-[#B8070F] px-6 text-sm font-extrabold text-white shadow-lg shadow-red-950/30 transition hover:-translate-y-0.5 hover:brightness-110">
                Start Free Trial <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
            <a href="{{ url('/') }}#enquiry" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-white/70 bg-black/25 px-6 text-sm font-extrabold text-white backdrop-blur-sm transition hover:-translate-y-0.5 hover:bg-white hover:text-slate-900">
                <i class="fa-regular fa-calendar-check mr-2"></i>Book a Demo
            </a>
        </div>

        <div class="hidden justify-self-end text-center lg:block">
            <p class="-rotate-6 text-xl font-black italic leading-tight text-amber-300 drop-shadow-md">
                Better Food.<br>Better Business!
            </p>
        </div>
    </div>
</section>
