@extends('core.layouts.menu-public')

@section('content')
    @php
        $isLightTheme = strtolower((string) ($publicMenuTheme ?? 'dark')) === 'light';
        $restaurantName = trim((string) ($tenant?->company_name ?? 'Restaurant'));
        $restaurantName = $restaurantName !== '' ? $restaurantName : 'Restaurant';
        // $tableNumber = trim((string) ($tableNumber ??
        $message = trim((string) data_get($paymentResult ?? [], 'message', 'Thank you for visiting.'));
        $invoiceNumber = trim((string) ($order?->invoice?->invoice_number ?? ($order?->invoice?->invoice_no ?? '')));
        $invoiceDownloadUrl = route('public.order.status.pdf', ['qr_token' => $qrToken]);
    @endphp

    <div
        class="min-h-screen flex items-center justify-center px-4 py-8 {{ $isLightTheme ? 'bg-slate-50 text-slate-900' : 'bg-gray-900 text-white' }}">
        <div
            class="w-full max-w-2xl rounded-[2rem] border shadow-2xl overflow-hidden {{ $isLightTheme ? 'border-slate-200 bg-white' : 'border-white/10 bg-[#0f172a]' }}">
            <div
                class="px-6 py-8 sm:px-8 sm:py-10 text-center bg-gradient-to-br {{ $isLightTheme ? 'from-emerald-50 via-white to-orange-50' : 'from-emerald-500/10 via-transparent to-orange-500/10' }}">
                <div
                    class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-500 ring-8 ring-emerald-500/10">
                    <i class="fas fa-circle-check text-4xl"></i>
                </div>

                <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight">

                    <span class="block text-orange-500">{{ $restaurantName }}</span>
                </h1>
                <p
                    class="mt-4 mx-auto max-w-xl text-sm sm:text-base leading-7 {{ $isLightTheme ? 'text-slate-600' : 'text-gray-300' }}">
                    {{ $message }}
                </p>
            </div>

            <div class="grid gap-4 px-2 pb-6 sm:px-8 sm:pb-8">
                <div class="grid gap-3 sm:grid-cols-3">

                    <div
                        class="rounded-lg border p-4 {{ $isLightTheme ? 'border-slate-200 bg-slate-50' : 'border-white/10 bg-white/5' }}">
                        <p
                            class="text-[11px] uppercase tracking-[0.2em] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">
                            Invoice</p>
                        <p class="mt-1 text-lg font-black">{{ $invoiceNumber !== '' ? $invoiceNumber : 'Ready' }}</p>
                        <a href="{{ $invoiceDownloadUrl }}"
                            class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-orange-500 px-4 py-3 text-sm font-black text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600">
                            <i class="fas fa-download mr-2"></i>
                            Download Invoice
                        </a>
                    </div>
                </div>

                <div
                    class="rounded-lg border px-5 py-5 sm:px-6 sm:py-6 text-center {{ $isLightTheme ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-emerald-500/20 bg-emerald-500/10 text-emerald-50' }}">
                    <p class="text-sm font-semibold leading-7">
                        Your bill has been settled successfully.
                        <span class="block mt-1">You may leave the menu now.</span>
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">

                    <a href="{{ route('public.menu.scan', ['qr_token' => $qrToken]) }}"
                        class="inline-flex items-center justify-center rounded-2xl border px-5 py-4 text-sm font-black transition {{ $isLightTheme ? 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' : 'border-white/10 bg-white/5 text-white hover:bg-white/10' }}">
                        Back to Menu
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
