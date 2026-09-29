<section id="testimonials" class="relative overflow-hidden bg-gradient-to-r from-[#fffaf7] via-white to-[#f7fffb] py-7 sm:py-9">
    <div class="container mx-auto px-4 sm:px-6">
        <header class="mb-5 text-center">
            <p class="text-[9px] font-black uppercase tracking-[0.22em] text-[#DC0812]">What Our Customers Say</p>
            <h2 class="mt-1.5 text-lg font-black text-slate-900 sm:text-xl">Trusted by restaurant owners across Nepal</h2>
        </header>

        @php
            $testimonials = [
                ['name' => 'Ravi Sharma', 'role' => 'Owner, Thamel House Restaurant', 'quote' => 'Restrotix has made our daily operations much easier. The POS is fast, reliable, and the support team is always helpful.', 'avatar' => 'https://i.pravatar.cc/120?img=12'],
                ['name' => 'Sita Gurung', 'role' => 'Managing Director, Himalayan Bites', 'quote' => 'Very easy to use and perfect for managing multiple branches. Everything stays connected and simple for our team.', 'avatar' => 'https://i.pravatar.cc/120?img=47'],
                ['name' => 'Anil Karki', 'role' => 'Owner, Aroma Cafe & Restaurant', 'quote' => 'Great features and professional support - a centralized solution that grows smoothly with our business.', 'avatar' => 'https://i.pravatar.cc/120?img=11'],
                ['name' => 'Maya Thapa', 'role' => 'Founder, Lakeside Kitchen', 'quote' => 'Billing, inventory, and reports now work together beautifully. Our team saves hours every week with Restrotix.', 'avatar' => 'https://i.pravatar.cc/120?img=44'],
                ['name' => 'Bikash Rai', 'role' => 'Director, Everest Food Hub', 'quote' => 'The multi-branch dashboard gives us a clear view of every outlet and helps us make faster business decisions.', 'avatar' => 'https://i.pravatar.cc/120?img=13'],
            ];
        @endphp

        <div id="testimonial-carousel" class="relative mx-auto max-w-7xl px-4">
            <button type="button" data-testimonial-prev aria-label="Previous testimonial" class="absolute left-0 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white text-[#DC0812] shadow-md transition hover:bg-[#DC0812] hover:text-white">
                <i class="fa-solid fa-chevron-left text-[10px]"></i>
            </button>

            <div data-testimonial-track class="flex snap-x snap-mandatory gap-4 overflow-x-auto px-2 py-1 scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach ($testimonials as $testimonial)
                    <article data-testimonial-card class="testimonial-card snap-start rounded-xl border border-slate-200/90 bg-white p-4 shadow-[0_10px_25px_-20px_rgba(15,23,42,0.55)]">
                        <div class="flex gap-3.5">
                            <img src="{{ $testimonial['avatar'] }}" alt="{{ $testimonial['name'] }}" loading="lazy"
                                class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-white shadow-md">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start gap-2">
                                    <i class="fa-solid fa-quote-left mt-0.5 text-sm text-[#DC0812]/35"></i>
                                    <p class="text-xs leading-5 text-slate-600 sm:text-[13px]">{{ $testimonial['quote'] }}</p>
                                </div>
                                <div class="mt-3 flex items-end justify-between gap-3 border-t border-slate-100 pt-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate text-xs font-extrabold text-slate-900 sm:text-sm">{{ $testimonial['name'] }}</h3>
                                        <p class="mt-0.5 truncate text-[9px] text-slate-500 sm:text-[10px]">{{ $testimonial['role'] }}</p>
                                    </div>
                                    <div class="flex shrink-0 gap-0.5 text-[9px] text-amber-400" aria-label="5 out of 5 stars">
                                        @for ($star = 0; $star < 5; $star++)
                                            <i class="fa-solid fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <button type="button" data-testimonial-next aria-label="Next testimonial" class="absolute right-0 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white text-[#DC0812] shadow-md transition hover:bg-[#DC0812] hover:text-white">
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </button>
        </div>
    </div>
</section>

<style>
    #testimonial-carousel .testimonial-card { flex: 0 0 88%; }
    @media (min-width: 640px) {
        #testimonial-carousel .testimonial-card { flex-basis: calc((100% - 1rem) / 2); }
    }
    @media (min-width: 1024px) {
        #testimonial-carousel .testimonial-card { flex-basis: calc((100% - 2rem) / 3); }
    }
</style>

<script>
    (() => {
        const carousel = document.getElementById('testimonial-carousel');
        if (!carousel || carousel.dataset.ready) return;
        carousel.dataset.ready = 'true';

        const track = carousel.querySelector('[data-testimonial-track]');
        const previous = carousel.querySelector('[data-testimonial-prev]');
        const next = carousel.querySelector('[data-testimonial-next]');
        let timer;

        const step = () => {
            const card = track.querySelector('[data-testimonial-card]');
            return card ? card.getBoundingClientRect().width + 16 : track.clientWidth;
        };
        const move = (direction) => {
            const maximum = track.scrollWidth - track.clientWidth;
            if (direction > 0 && track.scrollLeft >= maximum - 8) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else if (direction < 0 && track.scrollLeft <= 8) {
                track.scrollTo({ left: maximum, behavior: 'smooth' });
            } else {
                track.scrollBy({ left: direction * step(), behavior: 'smooth' });
            }
        };
        const stop = () => window.clearInterval(timer);
        const start = () => {
            stop();
            timer = window.setInterval(() => move(1), 4000);
        };

        previous.addEventListener('click', () => { move(-1); start(); });
        next.addEventListener('click', () => { move(1); start(); });
        carousel.addEventListener('mouseenter', stop);
        carousel.addEventListener('mouseleave', start);
        carousel.addEventListener('pointerdown', stop);
        carousel.addEventListener('pointerup', start);
        carousel.addEventListener('touchend', start, { passive: true });
        document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());
        start();
    })();
</script>
