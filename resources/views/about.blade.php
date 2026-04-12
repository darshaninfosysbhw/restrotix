@extends('core.layouts.front')

@section('content')
    <section class="relative overflow-hidden bg-gradient-to-br from-[#071733] via-[#0a1f46] to-[#0f2d63] py-18 sm:py-24">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_8%_20%,rgba(249,115,22,0.25),transparent_35%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_85%_80%,rgba(14,165,233,0.14),transparent_35%)]"></div>
        <div class="relative container mx-auto px-4 sm:px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">
                <div>
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-orange-300/50 bg-orange-400/10 px-3.5 py-1.5 text-sm font-medium text-orange-100">
                        <i class="fas fa-sparkles text-xs"></i>RestoChain SaaS
                    </span>
                    <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight">
                        Empowering the Culinary World
                    </h1>
                    <p class="mt-5 text-base sm:text-lg text-blue-100/90 max-w-xl">
                        We help restaurant brands operate faster, smarter, and in sync by connecting every outlet,
                        supplier, and decision point through one intelligent platform.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ url('/#enquiry') }}"
                            class="inline-flex items-center rounded-lg bg-orange-500 px-6 py-3 font-semibold text-white hover:bg-orange-600 transition">
                            Talk to Sales
                        </a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center rounded-lg border border-white/30 px-6 py-3 font-semibold text-white hover:bg-white/10 transition">
                            Explore Platform
                        </a>
                    </div>
                </div>

                <div
                    class="relative min-h-[360px] sm:min-h-[420px] rounded-3xl border border-white/15 bg-white/5 backdrop-blur p-6 sm:p-8">
                    <div
                        class="absolute inset-0 rounded-3xl bg-[linear-gradient(130deg,rgba(59,130,246,0.14),rgba(249,115,22,0.12))]">
                    </div>
                    <div class="relative h-full w-full">
                        <div
                            class="absolute left-1/2 top-1/2 h-18 w-18 -translate-x-1/2 -translate-y-1/2 rounded-2xl bg-orange-500 shadow-[0_0_40px_rgba(249,115,22,0.55)] flex items-center justify-center text-white">
                            <i class="fas fa-network-wired text-xl"></i>
                        </div>

                        <div
                            class="absolute left-[12%] top-[14%] h-14 w-14 rounded-xl bg-[#123468] border border-blue-300/25 text-blue-100 flex items-center justify-center">
                            <i class="fas fa-store"></i>
                        </div>
                        <div
                            class="absolute right-[10%] top-[18%] h-14 w-14 rounded-xl bg-[#123468] border border-blue-300/25 text-blue-100 flex items-center justify-center">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <div
                            class="absolute left-[16%] bottom-[16%] h-14 w-14 rounded-xl bg-[#123468] border border-blue-300/25 text-blue-100 flex items-center justify-center">
                            <i class="fas fa-truck-ramp-box"></i>
                        </div>
                        <div
                            class="absolute right-[14%] bottom-[13%] h-14 w-14 rounded-xl bg-[#123468] border border-blue-300/25 text-blue-100 flex items-center justify-center">
                            <i class="fas fa-chart-line"></i>
                        </div>

                        <svg class="absolute inset-0 h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <line x1="50" y1="50" x2="19" y2="20" stroke="rgba(249,115,22,0.6)"
                                stroke-width="0.8" stroke-dasharray="2 2" />
                            <line x1="50" y1="50" x2="81" y2="24" stroke="rgba(249,115,22,0.6)"
                                stroke-width="0.8" stroke-dasharray="2 2" />
                            <line x1="50" y1="50" x2="22" y2="80" stroke="rgba(249,115,22,0.6)"
                                stroke-width="0.8" stroke-dasharray="2 2" />
                            <line x1="50" y1="50" x2="80" y2="78" stroke="rgba(249,115,22,0.6)"
                                stroke-width="0.8" stroke-dasharray="2 2" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16 sm:py-20">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">
                <div>
                    <p class="text-sm font-semibold tracking-[0.18em] text-orange-500 uppercase">Our Story</p>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-bold text-[#0b1f44] leading-tight">
                        From One Operational Pain Point To A Connected Marketplace Ecosystem
                    </h2>
                    <p class="mt-5 text-gray-600 leading-relaxed">
                        RestoChain started with one goal: remove the operational friction between branch teams, management,
                        and suppliers. What began as a control layer for multi-branch execution has evolved into a complete
                        ecosystem where procurement, billing, performance, and decisions flow in one direction.
                    </p>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        We build with discipline, measure outcomes closely, and keep the product practical for real
                        kitchens, real teams, and real service pressure.
                    </p>
                </div>

                <div class="relative">
                    <div class="absolute -inset-3 rounded-3xl bg-gradient-to-tr from-orange-500/25 to-sky-500/25 blur-xl">
                    </div>
                    <img src="https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&q=80&w=1000"
                        alt="Professional restaurant kitchen in operation"
                        class="relative h-[360px] sm:h-[440px] w-full rounded-3xl object-cover shadow-2xl border border-slate-200">
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#f6f9ff] py-16 sm:py-20">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-10 sm:mb-12">
                    <p class="text-sm font-semibold tracking-[0.18em] text-orange-500 uppercase">Our Values</p>
                    <h2 class="mt-2 text-3xl sm:text-4xl font-bold text-[#0b1f44]">The Principles Behind Every Release</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <article
                        class="rounded-2xl border border-[#d7e3ff] bg-white p-7 shadow-[0_15px_40px_-30px_rgba(2,6,23,0.8)]">
                        <div
                            class="h-14 w-14 rounded-xl bg-[#0b1f44] text-orange-400 flex items-center justify-center shadow-[0_0_35px_rgba(249,115,22,0.65)]">
                            <i class="fas fa-lightbulb text-lg"></i>
                        </div>
                        <h3 class="mt-5 text-xl font-semibold text-[#0b1f44]">Innovation</h3>
                        <p class="mt-3 text-gray-600 leading-relaxed">
                            We solve deeply operational problems with thoughtful product design, not vanity features.
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-[#d7e3ff] bg-white p-7 shadow-[0_15px_40px_-30px_rgba(2,6,23,0.8)]">
                        <div
                            class="h-14 w-14 rounded-xl bg-[#0b1f44] text-orange-400 flex items-center justify-center shadow-[0_0_35px_rgba(249,115,22,0.65)]">
                            <i class="fas fa-eye text-lg"></i>
                        </div>
                        <h3 class="mt-5 text-xl font-semibold text-[#0b1f44]">Transparency</h3>
                        <p class="mt-3 text-gray-600 leading-relaxed">
                            Clear reporting, clear ownership, and clear visibility across branches and stakeholders.
                        </p>
                    </article>

                    <article
                        class="rounded-2xl border border-[#d7e3ff] bg-white p-7 shadow-[0_15px_40px_-30px_rgba(2,6,23,0.8)]">
                        <div
                            class="h-14 w-14 rounded-xl bg-[#0b1f44] text-orange-400 flex items-center justify-center shadow-[0_0_35px_rgba(249,115,22,0.65)]">
                            <i class="fas fa-headset text-lg"></i>
                        </div>
                        <h3 class="mt-5 text-xl font-semibold text-[#0b1f44]">Support</h3>
                        <p class="mt-3 text-gray-600 leading-relaxed">
                            We stand with operations teams during peak hours, scale phases, and every critical transition.
                        </p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16 sm:py-20">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <p class="text-sm font-semibold tracking-[0.18em] text-orange-500 uppercase">Journey</p>
                    <h2 class="mt-2 text-3xl sm:text-4xl font-bold text-[#0b1f44]">From Idea To Marketplace Ecosystem</h2>
                </div>

                <div class="relative">
                    <div class="absolute left-3 top-0 h-full w-0.5 bg-gradient-to-b from-orange-400 to-[#0b1f44] md:hidden">
                    </div>
                    <div
                        class="hidden md:block absolute left-0 right-0 top-7 h-0.5 bg-gradient-to-r from-orange-400 via-orange-300 to-[#0b1f44]">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-4">
                        <div class="relative rounded-2xl border border-slate-200 bg-slate-50 p-6 md:pt-12">
                            <div
                                class="hidden md:flex absolute -top-3 left-1/2 -translate-x-1/2 h-6 w-6 rounded-full bg-orange-500 border-4 border-white">
                            </div>
                            <h3 class="text-lg font-semibold text-[#0b1f44]">Concept</h3>
                            <p class="mt-2 text-sm text-gray-600">Mapped branch pain points and workflow complexity.</p>
                        </div>
                        <div class="relative rounded-2xl border border-slate-200 bg-slate-50 p-6 md:pt-12">
                            <div
                                class="hidden md:flex absolute -top-3 left-1/2 -translate-x-1/2 h-6 w-6 rounded-full bg-orange-500 border-4 border-white">
                            </div>
                            <h3 class="text-lg font-semibold text-[#0b1f44]">MVP Launch</h3>
                            <p class="mt-2 text-sm text-gray-600">Introduced centralized control and multi-branch billing.
                            </p>
                        </div>
                        <div class="relative rounded-2xl border border-slate-200 bg-slate-50 p-6 md:pt-12">
                            <div
                                class="hidden md:flex absolute -top-3 left-1/2 -translate-x-1/2 h-6 w-6 rounded-full bg-orange-500 border-4 border-white">
                            </div>
                            <h3 class="text-lg font-semibold text-[#0b1f44]">Scale Engine</h3>
                            <p class="mt-2 text-sm text-gray-600">Added role governance, visibility, and performance tools.
                            </p>
                        </div>
                        <div class="relative rounded-2xl border border-[#0b1f44]/20 bg-[#0b1f44] p-6 md:pt-12">
                            <div
                                class="hidden md:flex absolute -top-3 left-1/2 -translate-x-1/2 h-6 w-6 rounded-full bg-orange-500 border-4 border-white">
                            </div>
                            <h3 class="text-lg font-semibold text-white">Marketplace Ecosystem</h3>
                            <p class="mt-2 text-sm text-blue-100">Connected suppliers, procurement, and operations in one
                                platform.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
