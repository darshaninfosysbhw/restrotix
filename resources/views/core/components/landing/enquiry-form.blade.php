<!-- Enquiry Form Section -->
<section id="enquiry" class="relative overflow-hidden border-t border-[#DC0812]/10 bg-[#fffdf9] py-10 sm:py-12">
    <div class="pointer-events-none absolute -left-12 bottom-0 h-44 w-44 rounded-full bg-emerald-100/60 blur-2xl"></div>
    <div class="pointer-events-none absolute -right-12 top-0 h-48 w-48 rounded-full bg-[#DC0812]/10 blur-2xl"></div>

    <div class="container relative mx-auto px-4 sm:px-6">
        <div class="mx-auto max-w-7xl overflow-hidden rounded-2xl border border-[#DC0812]/15 bg-gradient-to-br from-white via-[#fffaf6] to-[#fff4eb] p-4 shadow-[0_18px_45px_-28px_rgba(220,8,18,0.30)] sm:p-6">
            <div class="grid items-stretch gap-5 lg:grid-cols-[1.05fr_1.35fr_0.8fr]">
                <div class="relative min-h-[310px] overflow-hidden rounded-xl px-2 pt-2 sm:min-h-[330px] sm:px-4">
                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase tracking-[0.15em] text-emerald-700">Get in touch</span>
                    <h2 class="mt-3 text-3xl font-black leading-[1.05] text-slate-900 sm:text-4xl">Have an <span class="text-[#DC0812]">Enquiry?</span></h2>
                    <p class="mt-2 max-w-sm text-sm leading-5 text-slate-600">Tell us what you need, and our friendly team will get back to you shortly.</p>

                    <span class="absolute right-16 top-[132px] z-20 flex h-11 w-16 items-center justify-center rounded-xl bg-emerald-600 shadow-md sm:right-20" aria-hidden="true">
                        <span class="flex items-center gap-1">
                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                        </span>
                        <span class="absolute -bottom-2 left-3 h-4 w-4 rotate-45 rounded-sm bg-emerald-600"></span>
                    </span>
                    <span class="absolute right-2 top-[112px] z-20 -rotate-12 text-4xl text-[#DC0812] drop-shadow-sm sm:right-4" aria-hidden="true">
                        <i class="fa-solid fa-paper-plane"></i>
                    </span>
                    <span class="absolute right-9 top-[155px] z-10 h-14 w-14 rounded-full border-b-2 border-r-2 border-dashed border-emerald-500/80 sm:right-12" aria-hidden="true"></span>

                    <div class="absolute bottom-0 left-0 right-0 flex h-[190px] items-end justify-center sm:h-[215px]">
                        <div class="absolute bottom-3 left-6 h-24 w-24 rounded-full bg-emerald-100/80"></div>
                        <div class="absolute bottom-8 right-5 h-20 w-20 rounded-full bg-[#DC0812]/10"></div>
                        <img src="{{ asset('images/enquiry-support-girl-red.png') }}" alt="Restrotix customer support representative" class="relative z-10 h-[110%] w-full object-contain object-bottom" loading="lazy">
                    </div>
                </div>

                <form class="flex h-full flex-col rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label for="enquiry-name" class="mb-1 block text-[11px] font-bold text-slate-700">Full Name <span class="text-[#DC0812]">*</span></label>
                            <input id="enquiry-name" name="name" type="text" required placeholder="Your full name" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#DC0812] focus:ring-2 focus:ring-[#DC0812]/10">
                        </div>
                        <div>
                            <label for="enquiry-phone" class="mb-1 block text-[11px] font-bold text-slate-700">Phone Number <span class="text-[#DC0812]">*</span></label>
                            <input id="enquiry-phone" name="phone" type="tel" required placeholder="+977 98XXXXXXXX" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#DC0812] focus:ring-2 focus:ring-[#DC0812]/10">
                        </div>
                        <div>
                            <label for="enquiry-restaurant" class="mb-1 block text-[11px] font-bold text-slate-700">Restaurant Name <span class="text-[#DC0812]">*</span></label>
                            <input id="enquiry-restaurant" name="restaurant" type="text" required placeholder="Your restaurant name" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#DC0812] focus:ring-2 focus:ring-[#DC0812]/10">
                        </div>
                        <div>
                            <label for="enquiry-email" class="mb-1 block text-[11px] font-bold text-slate-700">Email Address <span class="text-[#DC0812]">*</span></label>
                            <input id="enquiry-email" name="email" type="email" required placeholder="you@example.com" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#DC0812] focus:ring-2 focus:ring-[#DC0812]/10">
                        </div>
                    </div>

                    <div class="mt-3 flex-1">
                        <label for="enquiry-message" class="mb-1 block text-[11px] font-bold text-slate-700">Message <span class="text-[#DC0812]">*</span></label>
                        <textarea id="enquiry-message" name="message" rows="4" required placeholder="Tell us about your requirements..." class="min-h-[95px] w-full resize-none rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#DC0812] focus:ring-2 focus:ring-[#DC0812]/10"></textarea>
                    </div>

                    <button type="submit" class="mt-3 inline-flex w-full items-center justify-center rounded-lg bg-gradient-to-r from-[#DC0812] to-[#B8070F] px-5 py-3 text-sm font-extrabold text-white shadow-md shadow-[#DC0812]/20 transition hover:-translate-y-0.5 hover:shadow-lg">
                        Send Enquiry <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </button>
                </form>

                <aside class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1" aria-label="Contact information">
                    <div class="group flex items-center gap-3 rounded-xl border border-[#DC0812]/10 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#DC0812] to-[#B8070F] text-lg text-white shadow-md"><i class="fa-solid fa-phone"></i></span>
                        <span class="min-w-0">
                            <strong class="block text-sm text-slate-900">Call Us</strong>
                            <a href="tel:+9779851033146" class="block break-words text-xs font-bold text-slate-700 hover:text-[#DC0812]">+977-9851033146</a>
                            <a href="tel:+9779802892772" class="block break-words text-xs font-bold text-slate-700 hover:text-[#DC0812]">+977-9802892772</a>
                            <small class="mt-1 block text-[10px] text-slate-500">Sun - Fri, 9:00 AM - 6:00 PM</small>
                        </span>
                    </div>
                    <a href="mailto:support@restrotix.com" class="group flex items-center gap-3 rounded-xl border border-[#DC0812]/10 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#DC0812] to-[#B8070F] text-lg text-white shadow-md"><i class="fa-solid fa-envelope"></i></span>
                        <span class="min-w-0"><strong class="block text-sm text-slate-900">Email Us</strong><span class="block break-all text-xs font-bold text-slate-700">support@restrotix.com</span><small class="mt-1 block text-[10px] text-slate-500">We reply within 24 hours</small></span>
                    </a>
                    <div class="flex items-center gap-3 rounded-xl border border-[#DC0812]/10 bg-white p-4 shadow-sm">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#DC0812] to-[#B8070F] text-lg text-white shadow-md"><i class="fa-solid fa-location-dot"></i></span>
                        <span class="min-w-0">
                            <strong class="block text-sm text-slate-900">Visit Our Offices</strong>
                            <span class="block text-[10px] font-bold leading-4 text-slate-700">Ward No. 10, Thapagaun, Kathmandu</span>
                            <span class="block text-[10px] font-bold leading-4 text-slate-700">Bhairahawa, Rupandehi</span>
                        </span>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
