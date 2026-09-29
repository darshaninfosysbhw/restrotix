<!-- Footer -->
<footer class="border-t-4 border-[#DC0812] bg-[#071d2d] text-white">
    <div class="container mx-auto px-4 py-8 sm:px-6 sm:py-9 lg:py-10">
        <div class="grid grid-cols-2 gap-x-5 gap-y-8 md:grid-cols-4 md:gap-x-7 lg:grid-cols-[1.55fr_0.8fr_0.8fr_0.95fr_1.35fr] lg:gap-8">
            <div class="col-span-2 border-b border-white/10 pb-7 md:col-span-4 lg:col-span-1 lg:border-b-0 lg:pb-0">
                <a href="{{ url('/') }}" class="inline-flex items-center" aria-label="Restrotix home">
                    <img src="{{ asset('images/header-logo.png') }}" alt="Restrotix" class="h-10 w-auto sm:h-12">
                </a>
                <p class="mt-3 max-w-md text-sm leading-6 text-slate-300 lg:max-w-xs">Centralized restaurant management for every outlet, team, and operation.</p>
                <div class="mt-4 flex items-center gap-2.5">
                    @foreach ([
                        ['icon' => 'fa-facebook-f', 'label' => 'Facebook', 'color' => 'hover:bg-[#1877f2]'],
                        ['icon' => 'fa-instagram', 'label' => 'Instagram', 'color' => 'hover:bg-[#e4405f]'],
                        ['icon' => 'fa-linkedin-in', 'label' => 'LinkedIn', 'color' => 'hover:bg-[#0a66c2]'],
                        ['icon' => 'fa-youtube', 'label' => 'YouTube', 'color' => 'hover:bg-[#ff0000]'],
                    ] as $social)
                        <a href="#" aria-label="{{ $social['label'] }}" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-xs text-white transition hover:-translate-y-0.5 {{ $social['color'] }}">
                            <i class="fa-brands {{ $social['icon'] }}" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="min-w-0">
                <h3 class="text-sm font-extrabold">Product</h3>
                <nav class="mt-3 flex flex-col gap-2 text-sm text-slate-300" aria-label="Product links">
                    <a href="{{ url('/') }}#features" class="transition hover:text-[#DC0812]">Features</a>
                    <a href="{{ url('/') }}#pricing" class="transition hover:text-[#DC0812]">Pricing</a>
                    <a href="{{ url('/') }}#solutions" class="transition hover:text-[#DC0812]">Solutions</a>
                    <a href="{{ url('/') }}#enquiry" class="transition hover:text-[#DC0812]">Integrations</a>
                </nav>
            </div>

            <div class="min-w-0">
                <h3 class="text-sm font-extrabold">Company</h3>
                <nav class="mt-3 flex flex-col gap-2 text-sm text-slate-300" aria-label="Company links">
                    <a href="{{ url('/') }}#about" class="transition hover:text-[#DC0812]">About Us</a>
                    <a href="#" class="transition hover:text-[#DC0812]">Blog</a>
                    <a href="#" class="transition hover:text-[#DC0812]">Careers</a>
                    <a href="{{ url('/') }}#enquiry" class="transition hover:text-[#DC0812]">Contact</a>
                </nav>
            </div>

            <div class="min-w-0">
                <h3 class="text-sm font-extrabold">Support</h3>
                <nav class="mt-3 flex flex-col gap-2 text-sm text-slate-300" aria-label="Support links">
                    <a href="{{ url('/') }}#enquiry" class="transition hover:text-[#DC0812]">Help Center</a>
                    <a href="#" class="transition hover:text-[#DC0812]">Documentation</a>
                    <a href="#" class="transition hover:text-[#DC0812]">Video Tutorials</a>
                    <a href="#" class="transition hover:text-[#DC0812]">System Status</a>
                </nav>
            </div>

            <div class="min-w-0">
                <h3 class="text-sm font-extrabold">Contact Us</h3>
                <div class="mt-3 space-y-3 text-sm text-slate-300">
                    <a href="tel:+9779851033146" class="flex items-start gap-2 transition hover:text-white sm:gap-3"><i class="fa-solid fa-phone mt-1 w-4 shrink-0 text-[#DC0812]" aria-hidden="true"></i><span class="min-w-0 break-words">+977-9851033146</span></a>
                    <a href="tel:+9779802892772" class="flex items-start gap-2 transition hover:text-white sm:gap-3"><i class="fa-solid fa-phone mt-1 w-4 shrink-0 text-[#DC0812]" aria-hidden="true"></i><span class="min-w-0 break-words">+977-9802892772</span></a>
                    <a href="mailto:support@restrotix.com" class="flex items-start gap-2 transition hover:text-white sm:gap-3"><i class="fa-solid fa-envelope mt-1 w-4 shrink-0 text-[#DC0812]" aria-hidden="true"></i><span class="min-w-0 break-all">support@restrotix.com</span></a>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot mt-1 w-4 shrink-0 text-[#DC0812]" aria-hidden="true"></i>
                        <span class="space-y-1">
                            <span class="block"><strong class="text-white">Head Office:</strong> Ward No. 10, Thapagaun, Kathmandu</span>
                            <span class="block"><strong class="text-white">Branch:</strong> Bhairahawa, Rupandehi, Nepal</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col items-center gap-4 border-t border-white/10 pb-10 pt-5 text-center text-xs text-slate-400 sm:pb-8 md:flex-row md:justify-between md:pb-0 md:pr-16 md:text-left lg:pr-14">
            <p>&copy; {{ date('Y') }} Restrotix. All rights reserved.</p>
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 md:justify-end">
                <a href="{{ route('policy.privacy') }}" class="transition hover:text-white">Privacy Policy</a>
                <a href="{{ route('policy.terms') }}" class="transition hover:text-white">Terms of Service</a>
                <span class="hidden h-4 w-px bg-white/20 md:block"></span>
                <span class="flex basis-full items-center justify-center gap-2 sm:basis-auto md:justify-end">
                    <span>Powered by</span>
                    <a href="https://darshaninfosys.com.np/" target="_blank" rel="noopener noreferrer" aria-label="Visit Darshan Infosys website" class="inline-flex items-center transition hover:opacity-80">
                        <img src="{{ asset('images/parrent-logo.png') }}" alt="Darshan Infosys" class="block h-3.5 w-auto object-contain">
                    </a>
                </span>
            </div>
        </div>
    </div>
</footer>
