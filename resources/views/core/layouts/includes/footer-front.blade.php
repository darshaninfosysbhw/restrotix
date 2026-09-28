<!-- Footer -->
<footer class="border-t-4 border-[#a52a28] bg-[#071d2d] text-white">
    <div class="container mx-auto px-5 py-9 sm:px-6 lg:py-10">
        <div class="grid gap-9 sm:grid-cols-2 lg:grid-cols-[1.55fr_0.8fr_0.8fr_0.95fr_1.35fr] lg:gap-8">
            <div>
                <a href="{{ url('/') }}" class="inline-flex items-center" aria-label="Restrotix home">
                    <img src="{{ asset('images/header-logo.png') }}" alt="Restrotix" class="h-10 w-auto sm:h-12">
                </a>
                <p class="mt-3 max-w-xs text-sm leading-6 text-slate-300">Centralized restaurant management for every outlet, team, and operation.</p>
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

            <div>
                <h3 class="text-sm font-extrabold">Product</h3>
                <nav class="mt-3 flex flex-col gap-2 text-sm text-slate-300" aria-label="Product links">
                    <a href="{{ url('/') }}#features" class="transition hover:text-[#f05a28]">Features</a>
                    <a href="{{ url('/') }}#pricing" class="transition hover:text-[#f05a28]">Pricing</a>
                    <a href="{{ url('/') }}#solutions" class="transition hover:text-[#f05a28]">Solutions</a>
                    <a href="{{ url('/') }}#enquiry" class="transition hover:text-[#f05a28]">Integrations</a>
                </nav>
            </div>

            <div>
                <h3 class="text-sm font-extrabold">Company</h3>
                <nav class="mt-3 flex flex-col gap-2 text-sm text-slate-300" aria-label="Company links">
                    <a href="{{ url('/') }}#about" class="transition hover:text-[#f05a28]">About Us</a>
                    <a href="#" class="transition hover:text-[#f05a28]">Blog</a>
                    <a href="#" class="transition hover:text-[#f05a28]">Careers</a>
                    <a href="{{ url('/') }}#enquiry" class="transition hover:text-[#f05a28]">Contact</a>
                </nav>
            </div>

            <div>
                <h3 class="text-sm font-extrabold">Support</h3>
                <nav class="mt-3 flex flex-col gap-2 text-sm text-slate-300" aria-label="Support links">
                    <a href="{{ url('/') }}#enquiry" class="transition hover:text-[#f05a28]">Help Center</a>
                    <a href="#" class="transition hover:text-[#f05a28]">Documentation</a>
                    <a href="#" class="transition hover:text-[#f05a28]">Video Tutorials</a>
                    <a href="#" class="transition hover:text-[#f05a28]">System Status</a>
                </nav>
            </div>

            <div>
                <h3 class="text-sm font-extrabold">Contact Us</h3>
                <div class="mt-3 space-y-3 text-sm text-slate-300">
                    <a href="tel:+9779800000000" class="flex items-start gap-3 transition hover:text-white"><i class="fa-solid fa-phone mt-1 w-4 text-[#f05a28]" aria-hidden="true"></i><span>+977-9800000000</span></a>
                    <a href="mailto:info@restrotix.com" class="flex items-start gap-3 transition hover:text-white"><i class="fa-solid fa-envelope mt-1 w-4 text-[#f05a28]" aria-hidden="true"></i><span>info@restrotix.com</span></a>
                    <p class="flex items-start gap-3"><i class="fa-solid fa-location-dot mt-1 w-4 text-[#f05a28]" aria-hidden="true"></i><span>Kathmandu, Nepal</span></p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-4 border-t border-white/10 pt-5 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} Restrotix. All rights reserved.</p>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                <a href="{{ route('policy.privacy') }}" class="transition hover:text-white">Privacy Policy</a>
                <a href="{{ route('policy.terms') }}" class="transition hover:text-white">Terms of Service</a>
                <span class="hidden h-4 w-px bg-white/20 sm:block"></span>
                <span>Powered by <strong class="ml-1 tracking-wide"><span class="text-[#f05a28]">DARSHAN</span><span class="text-sky-400">INFOSYS</span></strong></span>
            </div>
        </div>
    </div>
</footer>
