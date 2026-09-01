@extends('core.layouts.menu-public')

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .menu-card {
        background: linear-gradient(145deg, #1e293b, #111827);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .hero-shell {
        background:
            radial-gradient(circle at top left, rgba(249, 115, 22, 0.16), transparent 32%),
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.05), transparent 28%),
            linear-gradient(180deg, #111827 0%, #0f172a 100%);
    }

    .progress-dot {
        position: relative;
    }

    .progress-dot::after {
        content: "";
        position: absolute;
        inset: -6px;
        border-radius: 999px;
        background: rgba(249, 115, 22, 0.14);
        animation: ringPulse 1.8s ease-out infinite;
        z-index: -1;
    }

    @keyframes ringPulse {
        0% {
            transform: scale(0.92);
            opacity: 0.6;
        }

        70% {
            transform: scale(1.3);
            opacity: 0;
        }

        100% {
            transform: scale(1.3);
            opacity: 0;
        }
    }

    .banner-pulse {
        animation: bannerPulse 1.8s ease-in-out infinite;
    }

    @keyframes bannerPulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.25);
        }

        50% {
            box-shadow: 0 0 0 14px rgba(249, 115, 22, 0);
        }
    }

    .toast-in {
        animation: toastIn 0.35s ease-out both;
    }

    @keyframes toastIn {
        from {
            opacity: 0;
            transform: translateY(-10px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .safe-bottom {
        padding-bottom: calc(6.5rem + env(safe-area-inset-bottom));
    }

    @media (min-width: 1024px) {
        .safe-bottom {
            padding-bottom: 2rem;
        }
    }

    .status-step {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
        color: rgba(209, 213, 219, 0.9);
    }

    .status-step.is-complete {
        background: rgba(16, 185, 129, 0.14);
        border-color: rgba(16, 185, 129, 0.28);
        color: #a7f3d0;
    }

    .status-step.is-active {
        background: rgba(249, 115, 22, 0.16);
        border-color: rgba(249, 115, 22, 0.34);
        color: #fdba74;
        animation: bannerPulse 1.8s ease-in-out infinite;
    }

    .status-connector {
        height: 2px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
    }

    .status-connector.is-active {
        background: linear-gradient(90deg, rgba(251, 146, 60, 0.55), rgba(251, 146, 60, 0.95));
        box-shadow: 0 0 0 1px rgba(251, 146, 60, 0.08);
    }

    .status-connector.is-complete {
        background: linear-gradient(90deg, rgba(34, 211, 238, 0.55), rgba(16, 185, 129, 0.85));
        box-shadow: 0 0 0 1px rgba(34, 211, 238, 0.08);
    }
</style>
@include('core.components.order-flow.partials.theme-overrides')

@section('content')
    @php
        $isLightTheme = strtolower((string) ($publicMenuTheme ?? 'dark')) === 'light';
    @endphp
    <div class="flex-1 h-screen lg:h-[calc(100vh-2rem)] flex overflow-hidden p-0 lg:p-4">
        @if ($showOrderPlaced)
            <div id="orderPlacedModal"
                class="fixed inset-0 z-[200] flex items-center justify-center bg-black/60 px-4 backdrop-blur-sm">
                <div class="relative w-full max-w-md rounded-[2rem] {{ $isLightTheme ? 'bg-white text-slate-900' : 'bg-white' }} p-6 text-center shadow-2xl sm:p-8">
                    <button id="closeOrderPlacedModal" type="button"
                        class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full {{ $isLightTheme ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-gray-900/80 text-white hover:bg-gray-800' }} transition"
                        aria-label="Close confirmation">
                        <i class="fas fa-times text-lg"></i>
                    </button>

                    <div
                        class="mx-auto mt-4 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fas fa-circle-check text-5xl"></i>
                    </div>

                    <h2 class="mt-6 text-3xl font-extrabold tracking-tight {{ $isLightTheme ? 'text-slate-900' : 'text-gray-900' }}">
                        Order Placed Successfully!
                    </h2>
                    <p class="mt-3 text-base leading-7 {{ $isLightTheme ? 'text-slate-600' : 'text-gray-500' }}">
                        Your order has been sent to the restaurant and is now being prepared.
                        You can track live progress on this screen.
                    </p>

                    <button id="continueOrderingBtn" type="button"
                        class="mt-7 inline-flex w-full items-center justify-center rounded-2xl bg-emerald-500 px-5 py-4 text-lg font-extrabold text-white transition hover:bg-emerald-600">
                        Continue Ordering
                    </button>
                </div>
            </div>
        @endif

        <aside class="hidden lg:flex flex-col w-72 {{ $isLightTheme ? 'bg-white border-slate-200' : 'bg-gray-800 border-gray-700' }} p-6 rounded-l-2xl border">
            <div class="flex items-start justify-between gap-3 mb-6">
                <div>
                    <p class="text-[10px] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-500' }} uppercase tracking-[0.35em] font-bold">Live Status</p>
                    <h2 class="mt-2 text-2xl font-extrabold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }} tracking-tight">Order Summary</h2>
                    <p class="mt-2 text-sm {{ $isLightTheme ? 'text-slate-600' : 'text-gray-400' }}">Track the active table order in real time.</p>
                </div>
                <div
                    class="w-12 h-12 rounded-2xl bg-orange-500/10 text-orange-500 flex items-center justify-center border border-orange-500/20">
                    <i class="fas fa-receipt text-lg"></i>
                </div>
            </div>

            <div class="space-y-3">
                <div class="rounded-2xl border {{ $isLightTheme ? 'border-slate-200 bg-slate-50' : 'border-gray-700 bg-gray-900/60' }} p-4">
                    <p class="text-xs uppercase tracking-[0.2em] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-500' }} font-semibold">Order Number</p>
                    <p class="mt-1 text-xl font-extrabold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">{{ $orderNumber }}</p>
                </div>
                <div class="rounded-2xl border {{ $isLightTheme ? 'border-slate-200 bg-slate-50' : 'border-gray-700 bg-gray-900/60' }} p-4">
                    <p class="text-xs uppercase tracking-[0.2em] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-500' }} font-semibold">Table</p>
                    <p class="mt-1 text-xl font-extrabold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">{{ $tableNumber }}</p>
                </div>
                <div class="rounded-2xl border {{ $isLightTheme ? 'border-slate-200 bg-slate-50' : 'border-gray-700 bg-gray-900/60' }} p-4">
                    <p class="text-xs uppercase tracking-[0.2em] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-500' }} font-semibold">Kitchen Status</p>
                    <p id="sidebarKitchenStatusPill"
                        class="mt-2 inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-bold {{ $statusPill[0] }}">
                        <i class="fas {{ $statusPill[1] }}"></i>
                        {{ $kitchenStage['label'] }}
                    </p>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                <div class="rounded-2xl border {{ $isLightTheme ? 'border-slate-200 bg-white' : 'border-white/5 bg-white/5' }} p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">Items</span>
                        <span id="orderLiveItemCount" class="text-sm font-bold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">{{ $liveItems }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">
                        <span>Running</span>
                        <span class="text-orange-400 font-semibold">{{ $runningCount }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">
                        <span>Ready</span>
                        <span class="text-emerald-400 font-semibold">{{ $readyCount }}</span>
                    </div>
                </div>

                <div class="rounded-2xl border {{ $isLightTheme ? 'border-slate-200 bg-white' : 'border-white/5 bg-white/5' }} p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">Grand Total</span>
                        <span id="sidebarGrandTotalAmount"
                            class="text-lg font-extrabold text-orange-400">₹{{ number_format($grandTotal, 2) }}</span>
                    </div>
                    <div class="mt-2 text-xs {{ $isLightTheme ? 'text-slate-500' : 'text-gray-500' }}">
                        Subtotal <span id="sidebarSubtotalAmount">₹{{ number_format($subtotal, 2) }}</span>
                        <span id="sidebarTaxSummary" class="{{ $showTaxAmount ? '' : 'hidden' }}"> ·
                            {{ $branchTaxLabelName }}
                            <span id="sidebarTaxAmount">₹{{ number_format($taxAmount, 2) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </aside>

        <main
                class="flex-1 min-h-0 flex flex-col relative {{ $isLightTheme ? 'bg-slate-50 border-slate-200' : 'bg-gray-900 border-gray-700' }} lg:rounded-r-2xl border-x lg:border-r overflow-hidden">
            <header
                class="sticky top-0 w-full z-20 flex justify-between items-center border-b {{ $isLightTheme ? 'border-slate-200 bg-white/90' : 'border-gray-700 bg-gray-900/80' }} px-4 py-2.5 lg:px-6 lg:py-4 backdrop-blur-sm">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('public.menu.scan', $qrToken) }}"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border {{ $isLightTheme ? 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100' : 'border-white/10 bg-white/5 text-white/90 hover:bg-white/10' }} transition flex-shrink-0">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div class="min-w-0">
                        <p class="text-[10px] uppercase tracking-[0.35em] text-orange-300/80 font-semibold">Live Order</p>
                        <h1 class="mt-1 text-[13px] lg:text-2xl font-bold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }} uppercase tracking-tight truncate">
                            Order Status
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-3 flex-shrink-0">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl border border-orange-500/20 bg-orange-500/10">
                        <span class="text-[10px] text-orange-400 font-bold uppercase tracking-wider">Table</span>
                        <span class="text-[10px] {{ $isLightTheme ? 'text-slate-900' : 'text-white' }} font-bold">{{ $tableNumber }}</span>
                    </div>
                    <div class="relative">
                        <button id="orderNotificationBellBtn" type="button"
                            class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border {{ $isLightTheme ? 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100' : 'border-white/10 bg-white/5 text-white/90 hover:bg-white/10' }} transition">
                            <i class="fas fa-bell text-base"></i>
                            <span id="orderNotificationBadge"
                                class="hidden absolute -top-1 -right-1 min-w-4 h-4 px-1 rounded-full bg-orange-500 text-[10px] font-bold text-white flex items-center justify-center border-2 {{ $isLightTheme ? 'border-white' : 'border-gray-900' }}">0</span>
                        </button>
                        <div id="orderNotificationPanel"
                            class="hidden absolute right-0 top-12 z-30 w-80 max-w-[90vw] overflow-hidden rounded-2xl border {{ $isLightTheme ? 'border-slate-200 bg-white/95 shadow-black/10' : 'border-white/10 bg-gray-900/95 shadow-black/40' }} shadow-2xl backdrop-blur-sm">
                            <div class="flex items-center justify-between border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }} px-4 py-3">
                                <div>
                                    <p class="text-[10px] uppercase tracking-[0.25em] text-orange-300 font-semibold">
                                        Notifications</p>
                                    <p class="text-xs {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">Rejected items and updates</p>
                                </div>
                                <button id="clearOrderNotificationsBtn" type="button"
                                    class="rounded-full border {{ $isLightTheme ? 'border-slate-200 bg-white px-2 py-1 text-[10px] font-bold text-slate-700 transition hover:bg-slate-100' : 'border-white/10 bg-white/5 px-2 py-1 text-[10px] font-bold text-gray-300 transition hover:bg-white/10 hover:text-white' }}">
                                    Clear
                                </button>
                            </div>
                            <div id="orderNotificationList" class="max-h-72 overflow-y-auto">
                                <div class="px-4 py-6 text-sm {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">No notifications yet.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div
                class="flex-1 min-h-0 overflow-y-auto no-scrollbar px-3 sm:px-4 mt-4  lg:p-6 lg:space-y-6 lg:mt-0 safe-bottom">
                <div class="menu-card rounded-lg p-3 sm:p-5">
                    <div class="grid grid-cols-2 gap-2 sm:gap-4">
                        <button id="callWaiterCardBtn" type="button" onclick="callWaiterOnStatus(this)"
                            class="flex flex-col items-center justify-center gap-2 rounded-2xl border {{ $isLightTheme ? 'border-orange-200 bg-orange-50 text-orange-500 hover:bg-orange-100' : 'border-orange-500/30 bg-orange-500/10 text-orange-400 hover:bg-orange-500/15' }} p-2.5 text-center transition">
                            <div
                                class="w-10 h-10 sm:w-18 sm:h-18 flex-shrink-0 rounded-full border {{ $isLightTheme ? 'border-orange-200 bg-white text-orange-500' : 'border-orange-500/30 bg-orange-500/15 text-orange-400' }} flex items-center justify-center">
                                <i class="fas fa-bell text-sm sm:text-2xl"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] sm:text-xs uppercase tracking-[0.15em] font-semibold text-orange-300">
                                    Call Waiter</p>
                                <p class="mt-0.5 sm:mt-1 text-[10px] sm:text-sm {{ $isLightTheme ? 'text-orange-700/90' : 'text-orange-100/90' }} truncate">
                                    Ask for help</p>
                            </div>
                        </button>

                        {{-- <div
                            class="flex flex-col items-center justify-center rounded-2xl border border-white/10 bg-black/10 px-2 py-3 text-center sm:px-4 sm:py-5">
                            <p class="text-[10px] sm:text-sm font-medium text-gray-300">Status</p>
                            <div id="liveStatusPill"
                                class="mt-2 sm:mt-3 inline-flex items-center gap-1.5 sm:gap-3 rounded-full border border-orange-500/20 bg-orange-500 px-2.5 py-2 sm:px-5 sm:py-3 text-white shadow-lg shadow-orange-500/10">
                                <span class="h-2 w-2 sm:h-3 sm:w-3 rounded-full bg-white/95"></span>
                                <span id="orderStatusPillText"
                                    class="text-[10px] sm:text-lg font-extrabold tracking-tight whitespace-nowrap">{{ $kitchenStage['label'] }}</span>
                            </div>
                            <p id="orderStatusMessage"
                                class="mt-2 sm:mt-3 text-[10px] sm:text-sm leading-4 sm:leading-6 text-gray-300 line-clamp-2">
                                {{ $kitchenStage['note'] }}
                            </p>
                        </div> --}}

                        <button id="requestBillCardBtn" type="button" onclick="requestBillOnStatus(this)"
                            class="flex flex-col items-center justify-center gap-2 rounded-2xl border {{ $isLightTheme ? 'border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/15' }} p-2.5 text-center transition">
                            <div
                                class="w-10 h-10 sm:w-18 sm:h-18 flex-shrink-0 rounded-full border {{ $isLightTheme ? 'border-emerald-200 bg-white text-emerald-600' : 'border-emerald-500/30 bg-emerald-500/15 text-emerald-400' }} flex items-center justify-center">
                                <i class="fas fa-file-invoice-dollar text-sm sm:text-2xl"></i>
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-[9px] sm:text-xs uppercase tracking-[0.15em] font-semibold {{ $isLightTheme ? 'text-emerald-700' : 'text-emerald-300' }}">
                                    Request Bill</p>
                                <p class="mt-0.5 sm:mt-1 text-[10px] sm:text-sm {{ $isLightTheme ? 'text-emerald-700/90' : 'text-emerald-100/90' }} truncate">
                                    Notify restaurant</p>
                            </div>
                        </button>
                    </div>
                </div>

                <div
                    class="rounded-lg border {{ $isLightTheme ? 'border-slate-200 bg-white text-slate-900 shadow-[0_8px_24px_rgba(15,23,42,0.06)]' : 'border-gray-600 bg-white/4 text-gray-900 shadow-[0_8px_24px_rgba(15,23,42,0.06)]' }} overflow-hidden mt-5">
                    <div class="px-5 pt-5 sm:px-6 sm:pt-6">
                        <h2 class="text-sm font-extrabold tracking-tight {{ $isLightTheme ? 'text-slate-900' : 'text-gray-100' }}">
                            Your Order (<span id="orderItemsHeaderCount">{{ $liveItems }}</span>)
                        </h2>
                    </div>

                    <div class="{{ $isLightTheme ? 'divide-y divide-slate-200' : 'divide-y divide-white/10' }}">
                        @forelse ($orderItems as $item)
                            <div data-order-item-id="{{ $item->id }}"
                                data-order-item-ids="{{ implode(',', $item->ids_group ?? [$item->id]) }}"
                                data-order-item-name="{{ $item->item_name }}"
                                data-order-item-status="{{ $item->status }}"
                                data-order-item-quantity="{{ $item->quantity }}"
                                data-order-item-total="{{ $item->total }}"
                                class="order-item-row relative flex items-center gap-4 px-5 py-2 sm:px-6 sm:py-5 {{ $item->is_rejected ? ($isLightTheme ? 'bg-red-50 pr-12' : 'bg-red-500/5 pr-12') : '' }}">
                                <div data-item-visual-section class="relative flex-shrink-0 pt-5 pl-px">
                                    @if ($item->is_rejected)
                                        <span data-item-rejection-badge
                                            class="absolute left-[-15px] top-0 inline-flex items-center gap-1 rounded-full border border-red-500/20 bg-red-500/90 px-1 text-[6px] font-bold text-white shadow-lg shadow-red-500/20">
                                            <i class="fas fa-circle-xmark text-[6px]"></i>
                                            Rejected
                                        </span>
                                    @endif
                                    <div data-item-image-wrapper
                                        class="h-11 w-11 overflow-hidden rounded-lg border {{ $isLightTheme ? 'border-slate-200 bg-slate-100' : 'border-white/10 bg-white/10' }}">
                                        <img src="{{ $item->image_url }}" alt="{{ $item->item_name }}"
                                            class="h-full w-full object-cover">
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1 pt-3">
                                    <h3 data-item-title
                                        class="truncate text-xs sm:text-xl font-extrabold tracking-tight {{ $item->is_rejected ? 'text-red-200 line-through' : ($isLightTheme ? 'text-slate-900' : 'text-white') }}">
                                        {{ $item->item_name }}
                                    </h3>
                                    <p data-item-qty
                                        class="text-[12px] font-semibold {{ $item->is_rejected ? 'text-red-300/80' : ($isLightTheme ? 'text-slate-500' : 'text-gray-400') }}">
                                        {{ (int) $item->quantity }} x Rs {{ number_format((float) $item->price, 0) }}
                                    </p>
                                    @if (!empty($item->meta_text))
                                        <div class="mt-1 flex w-full flex-wrap items-center gap-1.5">
                                            @foreach (array_filter(array_map('trim', explode(',', $item->meta_text))) as $metaPart)
                                                <span data-item-meta
                                            class="inline-flex items-center rounded-full border border-orange-500/20 bg-orange-500/10 px-2 py-0.5 text-[10px] font-semibold leading-4 text-orange-200 shadow-sm shadow-orange-500/5">
                                                    {{ $metaPart }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div data-item-rejection-box class="{{ $item->is_rejected ? '' : 'hidden' }} ">
                                        <p data-item-rejection-reason class="text-[10px] leading-5 text-red-200/90">
                                            {{ $item->rejection_reason }}
                                        </p>
                                    </div>
                                    @if ($item->is_rejected)
                                        <button type="button" data-hide-rejected-item
                                            class="absolute right-3 top-3 inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full border {{ $isLightTheme ? 'border-slate-200 bg-white/90 text-slate-600 hover:bg-slate-100 hover:text-slate-900' : 'border-white/10 bg-gray-900/80 text-gray-300 hover:bg-white/10 hover:text-white' }} transition"
                                            aria-label="Hide rejected item" title="Hide rejected item">
                                            <i class="fas fa-times text-[10px]"></i>
                                        </button>
                                    @endif
                                </div>

                                <div class="flex-shrink-0 text-right">
                                    <p data-item-total
                                        class="text-[14px] sm:text-xl font-extrabold tracking-tight {{ $item->is_rejected ? 'text-red-200' : ($isLightTheme ? 'text-slate-900' : 'text-white') }}">
                                        Rs {{ number_format((float) $item->display_total, 0) }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-8 sm:px-6 text-center {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }}">
                                No order items found for this order.
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }} px-5 py-4 sm:px-6 sm:py-5">
                        <div id="orderTotalDetails" class="hidden">
                            <div class="space-y-3 text-sm {{ $isLightTheme ? 'text-slate-600' : 'text-gray-300' }}">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="font-semibold {{ $isLightTheme ? 'text-slate-700' : 'text-gray-200' }}">Taxable Amount</span>
                                    <span id="orderSubtotalAmount" class="font-semibold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">Rs
                                        {{ number_format($subtotal, 0) }}</span>
                                </div>
                                <div id="orderTaxDetailRow"
                                    class="flex items-center justify-between gap-4 {{ $showTaxAmount ? '' : 'hidden' }}">
                                    <span id="orderTaxLabel" class="font-semibold {{ $isLightTheme ? 'text-slate-700' : 'text-gray-200' }}">
                                        {{ $branchTaxLabelName }}
                                    </span>
                                    <span id="orderTaxAmount" class="font-semibold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">Rs
                                        {{ number_format($taxAmount, 0) }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <span class="font-semibold {{ $isLightTheme ? 'text-slate-700' : 'text-gray-200' }}">Grand Total</span>
                                    <span id="orderGrandTotalAmount" class="font-semibold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">Rs
                                        {{ number_format($grandTotal, 0) }}</span>
                                </div>
                            </div>

                            <div class="my-3 border-t border-dashed {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }}"></div>
                        </div>

                        <button id="orderTotalToggle" type="button"
                            class="w-full flex items-center justify-between gap-4 text-left">
                            <div class="min-w-0">
                                <p class="text-lg font-extrabold tracking-tight {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">Total</p>
                            </div>

                            <div class="flex items-center gap-3 flex-shrink-0">
                                <p id="footerGrandTotalAmount"
                                    class="text-lg sm:text-2xl font-extrabold tracking-tight {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">
                                    Rs {{ number_format($grandTotal, 0) }}
                                </p>
                                <span id="orderTotalChevron"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-2xl border {{ $isLightTheme ? 'border-slate-200 bg-white text-slate-500' : 'border-white/10 bg-white/5 text-gray-300' }} shadow-sm transition-transform duration-300">
                                    <i class="fas fa-chevron-down text-[11px]"></i>
                                </span>
                            </div>
                        </button>
                    </div>
                </div>

                <div id="liveKitchenCard" data-stage="{{ $kitchenStage['step'] }}"
                    data-order-number="{{ $orderNumber }}" data-placed-at="{{ $orderPlacedAt }}"
                    data-kitchen-note="{{ $kitchenStage['note'] }}"
                    data-snapshot-url="{{ route('public.order.status', $qrToken) }}?snapshot=1"
                    class="w-full overflow-hidden rounded-2xl border {{ $isLightTheme ? 'border-cyan-200 bg-gradient-to-br from-white via-slate-50 to-slate-100 shadow-[0_12px_32px_rgba(15,23,42,0.08)]' : 'border-cyan-500/20 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 shadow-[0_12px_32px_rgba(2,6,23,0.35)]' }} p-3 sm:p-5 mt-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 pr-2">
                            <p class="text-[10px] uppercase tracking-[0.3em] {{ $isLightTheme ? 'text-cyan-700/80' : 'text-cyan-300/80' }} font-semibold">
                                Live Kitchen
                            </p>
                            <h3 class="mt-1 text-base sm:text-xl font-extrabold tracking-tight {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">
                                Order Progress
                            </h3>
                        </div>

                        <span id="liveKitchenStageBadge"
                            class="inline-flex flex-shrink-0 items-center gap-2 rounded-full border {{ $isLightTheme ? 'border-cyan-200 bg-cyan-50 px-2.5 py-1.5 text-[10px] sm:text-xs font-bold text-cyan-700' : 'border-cyan-500/20 bg-cyan-500/10 px-2.5 py-1.5 text-[10px] sm:text-xs font-bold text-cyan-300' }}">
                            <i id="liveKitchenStageIcon" class="fas fa-circle text-[9px]"></i>
                            <span id="liveKitchenStageText">{{ $kitchenStage['label'] }}</span>
                        </span>
                    </div>

                    <div id="liveKitchenAlert"
                        class="hidden mt-3 rounded-2xl border border-red-500/25 {{ $isLightTheme ? 'bg-red-50 text-red-700' : 'bg-red-500/10 text-red-100' }} p-3 sm:p-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-red-500/20 text-red-200">
                                <i class="fas fa-triangle-exclamation"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-[0.25em] font-semibold {{ $isLightTheme ? 'text-red-500' : 'text-red-300' }}">Item Rejected
                                </p>
                                <p id="liveKitchenAlertText"
                                    class="mt-1 text-sm sm:text-base font-semibold leading-6 {{ $isLightTheme ? 'text-red-700' : 'text-red-50' }}">
                                    The kitchen cancelled one item in your order.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pb-3 border-b {{ $isLightTheme ? 'border-slate-200' : 'border-white/10' }}">
                        <div class="flex items-center gap-2 min-w-0">
                            <p class="text-[10px] uppercase tracking-[0.2em] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }} font-semibold flex-shrink-0">
                                Order No</p>
                            <p id="liveKitchenOrderNumber"
                                class="text-sm sm:text-lg font-extrabold text-orange-400 truncate">
                                {{ $orderNumber }}
                            </p>
                        </div>
                        <p id="liveKitchenPlacedAt"
                            class="mt-1 text-[10px] sm:text-xs font-semibold {{ $isLightTheme ? 'text-slate-500' : 'text-gray-400' }} truncate">
                            {{ $orderPlacedAt }}
                        </p>

                    </div>

                    <div class="mt-3 flex items-center gap-2">
                        <div id="liveStepAccepted"
                            class="status-step min-w-0 flex-1 rounded-2xl border px-2.5 py-2 text-center text-[10px] sm:text-xs font-bold transition">
                            Accepted
                        </div>
                        <div id="liveStepLineAcceptedPreparing" class="status-connector flex-[0.55]"></div>
                        <div id="liveStepPreparing"
                            class="status-step min-w-0 flex-1 rounded-2xl border px-2.5 py-2 text-center text-[10px] sm:text-xs font-bold transition">
                            Preparing
                        </div>
                        <div id="liveStepLinePreparingServed" class="status-connector flex-[0.55]"></div>
                        <div id="liveStepServed"
                            class="status-step min-w-0 flex-1 rounded-2xl border px-2.5 py-2 text-center text-[10px] sm:text-xs font-bold transition">
                            Served
                        </div>
                    </div>

                    <p id="liveKitchenNote" class="mt-3 text-xs sm:text-sm leading-5 sm:leading-6 {{ $isLightTheme ? 'text-slate-600' : 'text-gray-300' }}">
                        {{ $kitchenStage['note'] }}
                    </p>
                </div>
            </div>

            <footer
                class="fixed lg:sticky bottom-0 left-0 w-full z-40 border-t border-orange-300/20 {{ $isLightTheme ? 'bg-white shadow-[0_-10px_30px_rgba(15,23,42,0.08)]' : 'bg-gradient-to-r from-[#0f172a] to-[#111827] shadow-[0_-10px_30px_rgba(15,23,42,0.4)]' }}">
                <div class="p-4 sm:px-6 py-3.5 flex items-center justify-between gap-3">
                    <a href="{{ route('public.menu.scan', $qrToken) }}"
                        class="flex-1 flex items-center justify-center gap-3 rounded-lg border border-orange-500/30 bg-orange-500/10 px-4 py-4 text-orange-400 font-bold transition hover:bg-orange-500/20">
                        <i class="fas fa-circle-plus text-xs"></i>
                        <span>Order More</span>
                    </a>

                    <button type="button" onclick="openBillSummaryModal()"
                        class="flex-1 flex items-center justify-center gap-3 rounded-lg bg-gradient-to-r from-orange-600 to-orange-500 px-4 py-4 {{ $isLightTheme ? 'text-white' : 'text-white' }} font-bold shadow-lg shadow-orange-600/20 transition hover:from-orange-500 hover:to-orange-400">
                        <i class="fas fa-file-invoice-dollar text-xs"></i>
                        <span>Generate Bill</span>
                    </button>
                </div>
            </footer>
        </main>
    </div>

    @php
        $billSummaryItems = $orderItems
            ->map(function (object $item) {
                return [
                    'name' => (string) $item->item_name,
                    'qty' => (int) $item->quantity,
                    'rate' => (float) $item->price,
                    'amount' => (float) ($item->display_total ?? ($item->total ?? 0)),
                    'status' => (string) ($item->is_rejected ? 'Rejected' : $item->status ?? 'new'),
                ];
            })
            ->all();

        $billSummaryData = [
            'table' => $tableNumber,
            'order_id' => $orderNumber,
            'invoice_number' => (string) ($order?->invoice?->invoice_number ?? $orderNumber),
            'subtotal' => $subtotal,
            'tax' => $taxAmount,
            'grand_total' => $grandTotal,
            'invoice_date' => optional($order?->created_at)->format('d M Y') ?? now()->format('d M Y'),
        ];

        $branch = $table?->branch;
        $tenant = $branch?->tenant;
        $invoiceBranding = [
            'restaurant_name' => (string) ($tenant?->company_name ?? 'Restaurant'),
            'branch_name' => (string) ($branch?->branch_name ?? ''),
            'branch_address' => trim((string) ($branch?->full_address ?: implode(', ', array_filter([
                $branch?->city,
                $branch?->state,
                $branch?->pincode,
            ])))),
            'branch_contact' => (string) ($branch?->contact_number ?? ''),
            'branch_email' => (string) ($branch?->branch_email ?? ''),
            'tax_registration' => (string) ($branch?->tax_registration ?? ''),
        ];
    @endphp

    @include('core.components.order-flow.partials.bill-summary-modal', [
        'summary' => $billSummaryData,
        'orderItems' => $billSummaryItems,
        'qrToken' => $qrToken,
        'paymentFlow' => $paymentFlow ?? [],
    ])
    @include('core.components.order-flow.partials.payment-options-modal', [
        'summary' => $billSummaryData,
        'orderItems' => $billSummaryItems,
        'paymentFlow' => $paymentFlow ?? [],
    ])
    @include('core.components.order-flow.partials.payment-success-modal', [
        'summary' => array_merge($billSummaryData, [
            'payment_mode' => $paymentResult['payment_mode'] ?? 'Online',
            'transaction_id' => $paymentResult['transaction_id'] ?? ('TXN-' . $orderNumber),
        ]),
    ])
    @include('core.components.order-flow.partials.tax-invoice-modal', [
        'summary' => $billSummaryData,
        'orderItems' => $billSummaryItems,
        'invoiceBranding' => $invoiceBranding,
        'invoiceData' => [
            'invoice_number' => $billSummaryData['invoice_number'],
            'invoice_date' => $billSummaryData['invoice_date'],
        ],
    ])

    <script>
        const billSummaryPdfData = @json($billSummaryData);
        const billSummaryPdfItems = @json($billSummaryItems);
        const paymentFlowData = @json($paymentFlow ?? []);
        const paymentResultData = @json($paymentResult ?? []);

        function openBillSummaryModal() {
            const modal = document.getElementById('billSummaryModal');
            if (!modal) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeBillSummaryModal() {
            const modal = document.getElementById('billSummaryModal');
            if (!modal) return;

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function openPaymentOptionsModal() {
            resetOnlinePaymentInlinePanel();
            const billModal = document.getElementById('billSummaryModal');
            const paymentModal = document.getElementById('paymentOptionsModal');
            const successModal = document.getElementById('paymentSuccessModal');
            const invoiceModal = document.getElementById('taxInvoiceModal');
            if (billModal) {
                billModal.classList.add('hidden');
                billModal.classList.remove('flex');
            }
            if (paymentModal) {
                paymentModal.classList.remove('hidden');
                paymentModal.classList.add('flex');
            }
            if (successModal) {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
            }
            if (invoiceModal) {
                invoiceModal.classList.add('hidden');
                invoiceModal.classList.remove('flex');
            }
            document.body.classList.add('overflow-hidden');
        }

        function closePaymentOptionsModal() {
            const paymentModal = document.getElementById('paymentOptionsModal');
            if (!paymentModal) return;

            paymentModal.classList.add('hidden');
            paymentModal.classList.remove('flex');
            resetOnlinePaymentInlinePanel();
            document.body.classList.remove('overflow-hidden');
        }

        function backToBillSummaryModal() {
            closePaymentOptionsModal();
            openBillSummaryModal();
        }

        function resetOnlinePaymentInlinePanel() {
            const panel = document.getElementById('onlinePaymentInlinePanel');
            const qrHolder = panel?.querySelector('[data-online-qr-holder]');
            const message = panel?.querySelector('[data-online-inline-message]');
            const successBtn = panel?.querySelector('[data-online-success-btn]');

            if (panel) {
                panel.classList.add('hidden');
            }
            if (qrHolder) {
                qrHolder.innerHTML = '';
            }
            if (message) {
                message.textContent = '';
            }
            if (successBtn) {
                successBtn.classList.add('hidden');
            }
        }

        function initiateOnlinePayment(buttonEl) {
            const endpoint = @json(route('public.order.payment.initiate', $qrToken));
            if (buttonEl) {
                buttonEl.disabled = true;
                buttonEl.dataset.originalText = buttonEl.innerHTML;
                buttonEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            }

            fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        table_id: @json($table->id ?? null),
                        tenant_id: @json($table->tenant_id ?? null),
                    })
                })
                .then((response) => response.json().then((data) => ({
                    ok: response.ok,
                    data
                })))
                .then(({ ok, data }) => {
                    if (!ok || !data.success) {
                        throw new Error(data.message || 'Unable to initiate payment.');
                    }

                    const payload = data.data || {};
                    if (payload.display_mode === 'redirect' && payload.redirect_url) {
                        window.location.href = payload.redirect_url;
                        return;
                    }

                    if (payload.display_mode === 'static_qr') {
                        const panel = document.getElementById('onlinePaymentInlinePanel');
                        if (panel) {
                            panel.classList.remove('hidden');
                            const qrHolder = panel.querySelector('[data-online-qr-holder]');
                            const message = panel.querySelector('[data-online-inline-message]');
                            const successBtn = panel.querySelector('[data-online-success-btn]');
                            if (qrHolder && payload.static_qr_svg) {
                                qrHolder.innerHTML = payload.static_qr_svg;
                            }
                            if (message) {
                                message.textContent = payload.static_qr_label
                                    ? `Scan ${payload.static_qr_label} and complete payment from your banking app.`
                                    : 'Scan the QR and complete payment from your banking app.';
                            }
                            if (successBtn) {
                                successBtn.classList.remove('hidden');
                            }
                        }
                        return;
                    }

                    throw new Error('Gateway response did not include a redirect URL.');
                })
                .catch((error) => {
                    alert(error.message || 'Unable to initiate payment.');
                })
                .finally(() => {
                    if (buttonEl) {
                        buttonEl.disabled = false;
                        buttonEl.innerHTML = buttonEl.dataset.originalText || 'Pay Now';
                    }
                });
        }

        function openPaymentSuccessModal() {
            const paymentModal = document.getElementById('paymentOptionsModal');
            const successModal = document.getElementById('paymentSuccessModal');
            if (paymentModal) {
                paymentModal.classList.add('hidden');
                paymentModal.classList.remove('flex');
            }
            if (successModal) {
                successModal.classList.remove('hidden');
                successModal.classList.add('flex');
            }
            document.body.classList.add('overflow-hidden');
        }

        function closePaymentSuccessModal() {
            const successModal = document.getElementById('paymentSuccessModal');
            if (!successModal) return;

            successModal.classList.add('hidden');
            successModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function backToPaymentOptionsModal() {
            closePaymentSuccessModal();
            openPaymentOptionsModal();
        }

        function requestBillFromBillSummary(buttonEl) {
            closeBillSummaryModal();
            requestBillFromPaymentOptions(buttonEl);
        }

        function openTaxInvoiceModal() {
            const successModal = document.getElementById('paymentSuccessModal');
            const invoiceModal = document.getElementById('taxInvoiceModal');
            if (successModal) {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
            }
            if (invoiceModal) {
                invoiceModal.classList.remove('hidden');
                invoiceModal.classList.add('flex');
            }
            document.body.classList.add('overflow-hidden');
        }

        function closeTaxInvoiceModal() {
            const invoiceModal = document.getElementById('taxInvoiceModal');
            if (!invoiceModal) return;

            invoiceModal.classList.add('hidden');
            invoiceModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function backToPaymentSuccessModal() {
            closeTaxInvoiceModal();
            openPaymentSuccessModal();
        }

        function requestBillFromPaymentOptions(buttonEl) {
            const tableId = @json($table->id ?? null);
            const tableNumber = @json($tableNumber);
            const tenantId = @json($table->tenant_id ?? null);

            if (!tableId && (!tableNumber || tableNumber === 'N/A')) {
                alert('Table info not found.');
                return;
            }

            if (buttonEl) buttonEl.disabled = true;

            fetch('{{ route('bill.request') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        table_id: tableId,
                        table_number: tableNumber,
                        tenant_id: tenantId
                    })
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        setOrderStatusUi('Request Bill', 'Cash payment selected. Your bill request has been sent to the restaurant.');
                        alert('Bill request has been sent');
                        closeBillSummaryModal();
                        closePaymentOptionsModal();
                    } else {
                        alert(data.message || 'Unable to request bill');
                    }
                })
                .catch(() => {
                    alert('Unable to request bill');
                })
                .finally(() => {
                    if (buttonEl) buttonEl.disabled = false;
                });
        }

        function callWaiterOnStatus(buttonEl) {
            const tableId = @json($table->id ?? null);
            const tableNumber = @json($tableNumber);
            const tenantId = @json($table->tenant_id ?? null);

            if (!tableId && (!tableNumber || tableNumber === 'N/A')) {
                alert('Table info not found.');
                return;
            }

            if (buttonEl) buttonEl.disabled = true;

            fetch('{{ route('waiter.call') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        table_id: tableId,
                        table_number: tableNumber,
                        tenant_id: tenantId
                    })
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        alert('Waiter has been notified');
                    } else {
                        alert(data.message || 'Unable to call waiter');
                    }
                })
                .catch(() => {
                    alert('Unable to call waiter');
                })
                .finally(() => {
                    if (buttonEl) buttonEl.disabled = false;
                });
        }

        function setOrderStatusUi(statusText, messageText) {
            const statusPillText = document.getElementById('orderStatusPillText');
            const statusMessage = document.getElementById('orderStatusMessage');
            const statusPill = statusPillText ? statusPillText.closest('div') : null;

            if (statusPillText && statusText) {
                statusPillText.textContent = statusText;
            }

            if (statusMessage && messageText) {
                statusMessage.textContent = messageText;
            }

            if (statusPill) {
                statusPill.className =
                    'mt-2 sm:mt-3 inline-flex items-center gap-1.5 sm:gap-3 rounded-full border px-2.5 py-2 sm:px-5 sm:py-3 text-white shadow-lg shadow-orange-500/10';
                statusPill.classList.add('bg-orange-500', 'border-orange-500/20');
            }
        }

        function requestBillOnStatus(buttonEl) {
            const tableId = @json($table->id ?? null);
            const tableNumber = @json($tableNumber);
            const tenantId = @json($table->tenant_id ?? null);

            if (!tableId && (!tableNumber || tableNumber === 'N/A')) {
                alert('Table info not found.');
                return;
            }

            if (buttonEl) buttonEl.disabled = true;

            fetch('{{ route('bill.request') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        table_id: tableId,
                        table_number: tableNumber,
                        tenant_id: tenantId
                    })
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        setOrderStatusUi('Request Bill', 'Your bill request has been sent to the restaurant.');
                        const footerBtn = document.querySelector('button[onclick="requestBillOnStatus(this)"]');
                        if (footerBtn) {
                            footerBtn.disabled = true;
                            footerBtn.innerHTML =
                                '<i class="fas fa-circle-check text-xs"></i><span>Request Sent</span>';
                        }
                        const cardBtn = document.getElementById('requestBillCardBtn');
                        if (cardBtn) {
                            cardBtn.disabled = true;
                            cardBtn.classList.add('opacity-80', 'cursor-not-allowed');
                        }
                        alert('Bill request has been sent');
                    } else {
                        alert(data.message || 'Unable to request bill');
                    }
                })
                .catch(() => {
                    alert('Unable to request bill');
                })
                .finally(() => {
                    if (buttonEl) buttonEl.disabled = false;
                });
        }

        function applyKitchenStage(stage, label, note) {
            const liveKitchenCard = document.getElementById('liveKitchenCard');
            if (!liveKitchenCard) return;

            const normalizedStage = String(stage || liveKitchenCard.dataset.stage || 'accepted').toLowerCase();
            const stageMap = {
                pending: 'accepted',
                confirmed: 'accepted',
                accepted: 'accepted',
                preparing: 'preparing',
                ready: 'served',
                served: 'served',
            };
            const finalStage = stageMap[normalizedStage] || 'accepted';
            const textMap = {
                accepted: 'Accepted',
                preparing: 'Preparing',
                served: 'Served',
            };
            const noteMap = {
                accepted: 'Kitchen has accepted your order.',
                preparing: 'Kitchen is preparing your order.',
                served: 'Your order has been served.',
            };
            const colorMap = {
                accepted: 'cyan',
                preparing: 'orange',
                served: 'emerald',
            };

            const stageText = document.getElementById('liveKitchenStageText');
            const noteText = document.getElementById('liveKitchenNote');
            const stageBadge = document.getElementById('liveKitchenStageBadge');
            const stageIcon = document.getElementById('liveKitchenStageIcon');
            const sidebarKitchenStatus = document.getElementById('sidebarKitchenStatusPill');
            const acceptedStep = document.getElementById('liveStepAccepted');
            const preparingStep = document.getElementById('liveStepPreparing');
            const servedStep = document.getElementById('liveStepServed');
            const lineAcceptedPreparing = document.getElementById('liveStepLineAcceptedPreparing');
            const linePreparingServed = document.getElementById('liveStepLinePreparingServed');

            liveKitchenCard.dataset.stage = finalStage;

            const resolvedLabel = label || textMap[finalStage];
            const resolvedNote = note || noteMap[finalStage];
            const tone = colorMap[finalStage];

            if (stageText) stageText.textContent = resolvedLabel;
            if (noteText) noteText.textContent = resolvedNote;

            if (stageBadge) {
                stageBadge.className =
                    'inline-flex flex-shrink-0 items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-bold';
                if (tone === 'cyan') {
                    stageBadge.classList.add('border-cyan-500/20', 'bg-cyan-500/10', 'text-cyan-300');
                } else if (tone === 'orange') {
                    stageBadge.classList.add('border-orange-500/20', 'bg-orange-500/10', 'text-orange-300');
                } else {
                    stageBadge.classList.add('border-emerald-500/20', 'bg-emerald-500/10', 'text-emerald-300');
                }
            }

            if (stageIcon) {
                stageIcon.className = 'fas fa-circle text-[9px]';
            }

            if (sidebarKitchenStatus) {
                sidebarKitchenStatus.className =
                    'mt-2 inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-bold';
                if (tone === 'cyan') {
                    sidebarKitchenStatus.classList.add('bg-cyan-500/15', 'text-cyan-300', 'border-cyan-500/20');
                } else if (tone === 'orange') {
                    sidebarKitchenStatus.classList.add('bg-orange-500/15', 'text-orange-300', 'border-orange-500/20');
                } else {
                    sidebarKitchenStatus.classList.add('bg-emerald-500/15', 'text-emerald-300', 'border-emerald-500/20');
                }
                sidebarKitchenStatus.innerHTML =
                    `<i class="fas fa-circle text-[8px]"></i>${resolvedLabel}`;
            }

            const resetStep = (el) => {
                if (!el) return;
                el.className =
                    'status-step min-w-0 flex-1 rounded-2xl border px-2.5 py-2 text-center text-[10px] sm:text-xs font-bold transition';
            };

            [acceptedStep, preparingStep, servedStep].forEach(resetStep);

            const resetConnector = (el) => {
                if (!el) return;
                el.className = 'status-connector flex-[0.55]';
            };

            [lineAcceptedPreparing, linePreparingServed].forEach(resetConnector);

            if (acceptedStep) {
                acceptedStep.classList.add(finalStage === 'accepted' ? 'is-active' : 'is-complete');
            }

            if (lineAcceptedPreparing) {
                if (finalStage === 'accepted') {
                    lineAcceptedPreparing.classList.add('is-active');
                } else {
                    lineAcceptedPreparing.classList.add('is-complete');
                }
            }

            if (preparingStep) {
                if (finalStage === 'preparing') {
                    preparingStep.classList.add('is-active');
                } else if (finalStage === 'served') {
                    preparingStep.classList.add('is-complete');
                }
            }

            if (linePreparingServed) {
                if (finalStage === 'served') {
                    linePreparingServed.classList.add('is-complete');
                } else if (finalStage === 'preparing') {
                    linePreparingServed.classList.add('is-active');
                }
            }

            if (servedStep && finalStage === 'served') {
                servedStep.classList.add('is-active');
            }
        }

        function getOrderNotificationKey() {
            const liveKitchenCard = document.getElementById('liveKitchenCard');
            const orderNumber = liveKitchenCard?.dataset.orderNumber || 'default';
            return `order-notifications:${orderNumber}`;
        }

        function getRejectedSeenKey() {
            const liveKitchenCard = document.getElementById('liveKitchenCard');
            const orderNumber = liveKitchenCard?.dataset.orderNumber || 'default';
            return `order-rejected-seen:${orderNumber}`;
        }

        function loadStoredJson(key, fallback) {
            try {
                const stored = localStorage.getItem(key);
                if (!stored) return fallback;
                const parsed = JSON.parse(stored);
                return parsed ?? fallback;
            } catch (error) {
                return fallback;
            }
        }

        function saveStoredJson(key, value) {
            try {
                localStorage.setItem(key, JSON.stringify(value));
            } catch (error) {
                console.error('Unable to persist order notification state', error);
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function getOrderNotifications() {
            const notifications = loadStoredJson(getOrderNotificationKey(), []);
            return Array.isArray(notifications) ? notifications : [];
        }

        function saveOrderNotifications(notifications) {
            saveStoredJson(getOrderNotificationKey(), notifications);
        }

        function getRejectedSeenIds() {
            const seenIds = loadStoredJson(getRejectedSeenKey(), []);
            return Array.isArray(seenIds) ? seenIds.map(String) : [];
        }

        function saveRejectedSeenIds(ids) {
            saveStoredJson(getRejectedSeenKey(), [...new Set(ids.map(String))]);
        }

        function playRejectedSound() {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;

            try {
                const ctx = new AudioCtx();
                const now = ctx.currentTime;

                const beep = (start, duration, freq) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.value = freq;
                    gain.gain.setValueAtTime(0.0001, start);
                    gain.gain.exponentialRampToValueAtTime(0.07, start + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, start + duration);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(start);
                    osc.stop(start + duration + 0.02);
                };

                beep(now, 0.12, 620);
                beep(now + 0.16, 0.14, 880);

                setTimeout(() => {
                    ctx.close().catch(() => {});
                }, 700);
            } catch (error) {
                console.warn('Rejected sound could not play', error);
            }
        }

        function renderOrderNotifications() {
            const list = document.getElementById('orderNotificationList');
            const badge = document.getElementById('orderNotificationBadge');
            const notifications = getOrderNotifications();

            if (badge) {
                if (notifications.length > 0) {
                    badge.textContent = String(notifications.length);
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            if (!list) return;

            if (notifications.length === 0) {
                list.innerHTML = '<div class="px-4 py-6 text-sm text-gray-400">No notifications yet.</div>';
                return;
            }

            list.innerHTML = notifications.map((notification) => {
                const safeTime = notification.time || '';
                const safeMessage = notification.message || '';
                const safeTitle = notification.title || 'Notification';
                return `
                    <div class="border-b border-white/5 px-4 py-3 last:border-0">
                        <p class="text-xs font-bold text-red-300">${escapeHtml(safeTitle)}</p>
                        <p class="mt-1 text-sm leading-5 text-gray-200">${escapeHtml(safeMessage)}</p>
                        <p class="mt-1 text-[10px] uppercase tracking-[0.2em] text-gray-500">${escapeHtml(safeTime)}</p>
                    </div>
                `;
            }).join('');
        }

        function openRejectedNotificationPanel() {
            const panel = document.getElementById('orderNotificationPanel');
            if (!panel) return;
            panel.classList.toggle('hidden');
        }

        function addRejectedNotification(itemId, itemName, reason) {
            const seenIds = getRejectedSeenIds();
            if (seenIds.includes(String(itemId))) {
                return false;
            }

            seenIds.push(String(itemId));
            saveRejectedSeenIds(seenIds);

            const notifications = getOrderNotifications();
            const now = new Date();
            const message = itemName ?
                `${itemName} was rejected by the kitchen.` :
                'An item was rejected by the kitchen.';
            const finalMessage = reason ? `${message} ${reason}` : message;

            notifications.unshift({
                id: `rejected-${itemId}-${Date.now()}`,
                type: 'error',
                title: 'Item Rejected',
                message: finalMessage,
                time: now.toLocaleString(),
            });

            saveOrderNotifications(notifications.slice(0, 20));
            renderOrderNotifications();

            if (window.showToast) {
                window.showToast({
                    type: 'error',
                    message: finalMessage,
                    duration: 5000,
                });
            }

            playRejectedSound();

            if (navigator.vibrate) {
                navigator.vibrate([120, 60, 120]);
            }

            return true;
        }

        function findOrderItemRow(itemId) {
            const targetId = String(itemId);
            return Array.from(document.querySelectorAll('.order-item-row[data-order-item-id]')).find((row) => {
                const ids = String(row.dataset.orderItemIds || row.dataset.orderItemId || '')
                    .split(',')
                    .map((id) => id.trim())
                    .filter(Boolean);
                return ids.includes(targetId);
            }) || null;
        }

        function notifyRejectedItem(itemId, itemName, reason) {
            const row = findOrderItemRow(itemId);
            const status = row ? String(row.dataset.orderItemStatus || '').toLowerCase() : 'rejected';
            if (status !== 'rejected') return;
            addRejectedNotification(itemId, itemName, reason);
        }

        function applyRejectedItemToRow(itemId, itemName, reason) {
            const row = findOrderItemRow(itemId);
            if (!row) return;

            row.classList.add('relative', 'pr-12');
            row.classList.add('bg-red-500/5');
            row.dataset.orderItemStatus = 'rejected';
            const title = row.querySelector('[data-item-title]');
            const qty = row.querySelector('[data-item-qty]');
            const total = row.querySelector('[data-item-total]');
            if (title) {
                title.classList.remove('text-white');
                title.classList.add('text-red-200', 'line-through');
            }
            if (qty) {
                qty.classList.remove('text-gray-400');
                qty.classList.add('text-red-300/80');
            }
            if (total) {
                total.classList.remove('text-white');
                total.classList.add('text-red-200');
                total.textContent = 'Rs 0';
            }

            const rejectionBox = row.querySelector('[data-item-rejection-box]');
            const rejectionReason = row.querySelector('[data-item-rejection-reason]');
            const visualSection = row.querySelector('[data-item-visual-section]');
            if (rejectionBox) {
                rejectionBox.classList.remove('hidden');
            }
            if (rejectionReason) {
                rejectionReason.textContent = reason || 'Item cancelled by kitchen.';
            }
            let badge = row.querySelector('[data-item-rejection-badge]');
            if (!badge && visualSection) {
                badge = document.createElement('span');
                badge.setAttribute('data-item-rejection-badge', '');
                badge.className =
                    'absolute left-0 top-0 inline-flex items-center gap-1 rounded-full border border-red-500/20 bg-red-500/90 px-1.5 py-0.5 text-[9px] font-bold text-white shadow-lg shadow-red-500/20';
                badge.innerHTML = '<i class="fas fa-circle-xmark text-[8px]"></i>Rejected';
                visualSection.appendChild(badge);
            }
            let hideBtn = row.querySelector('[data-hide-rejected-item]');
            if (!hideBtn) {
                hideBtn = document.createElement('button');
                hideBtn.type = 'button';
                hideBtn.setAttribute('data-hide-rejected-item', '');
                hideBtn.setAttribute('aria-label', 'Hide rejected item');
                hideBtn.setAttribute('title', 'Hide rejected item');
                hideBtn.className =
                    'absolute right-3 top-3 inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full border border-white/10 bg-gray-900/80 text-gray-300 transition hover:bg-white/10 hover:text-white';
                hideBtn.innerHTML = '<i class="fas fa-times text-[10px]"></i>';
                row.appendChild(hideBtn);
            }

        }

        function getHiddenRejectedItemIds() {
            const liveKitchenCard = document.getElementById('liveKitchenCard');
            const orderNumber = liveKitchenCard?.dataset.orderNumber || 'default';
            const storageKey = `hidden-rejected-items:${orderNumber}`;

            try {
                const stored = localStorage.getItem(storageKey);
                const parsed = stored ? JSON.parse(stored) : [];
                return Array.isArray(parsed) ? parsed.map(String) : [];
            } catch (error) {
                return [];
            }
        }

        function saveHiddenRejectedItemIds(ids) {
            const liveKitchenCard = document.getElementById('liveKitchenCard');
            const orderNumber = liveKitchenCard?.dataset.orderNumber || 'default';
            const storageKey = `hidden-rejected-items:${orderNumber}`;
            try {
                localStorage.setItem(storageKey, JSON.stringify([...new Set(ids.map(String))]));
            } catch (error) {
                console.error('Unable to save hidden rejected items', error);
            }
        }

        function hideRejectedItemRow(itemId) {
            const row = findOrderItemRow(itemId);
            if (!row) return;

            row.classList.add('hidden');

            const hiddenIds = getHiddenRejectedItemIds();
            if (!hiddenIds.includes(String(itemId))) {
                hiddenIds.push(String(itemId));
                saveHiddenRejectedItemIds(hiddenIds);
            }

            recalculateOrderTotals();
        }

        function applyHiddenRejectedItems() {
            const hiddenIds = new Set(getHiddenRejectedItemIds().map(String));
            document.querySelectorAll('.order-item-row[data-order-item-id]').forEach((row) => {
                const itemIds = String(row.dataset.orderItemIds || row.dataset.orderItemId || '')
                    .split(',')
                    .map((id) => id.trim())
                    .filter(Boolean);
                const isRejected = String(row.dataset.orderItemStatus || '').toLowerCase() === 'rejected';
                if (isRejected && itemIds.some((id) => hiddenIds.has(id))) {
                    row.classList.add('hidden');
                }
            });

            recalculateOrderTotals();
        }

        function recalculateOrderTotals() {
            const rows = document.querySelectorAll('.order-item-row[data-order-item-id]');
            let activeSubtotal = 0;
            let totalQuantity = 0;

            rows.forEach((row) => {
                const rowQuantity = Number(row.dataset.orderItemQuantity || 0);
                if (row.classList.contains('hidden')) return;

                if (Number.isFinite(rowQuantity)) {
                    totalQuantity += rowQuantity;
                }

                const status = String(row.dataset.orderItemStatus || '').toLowerCase();
                const rawTotal = Number(row.dataset.orderItemTotal || 0);
                if (status !== 'rejected') {
                    activeSubtotal += Number.isFinite(rawTotal) ? rawTotal : 0;
                }
            });

            const taxRate = Number(document.body.dataset.orderTaxRate || 0);
            const taxSetting = String(document.body.dataset.orderTaxSetting || 'exclusive').toLowerCase();
            let activeTax = 0;
            let activeGrandTotal = activeSubtotal;

            if (taxSetting === 'inclusive') {
                activeTax = Number.isFinite(taxRate) && taxRate > 0 ?
                    activeSubtotal - (activeSubtotal / (1 + taxRate)) :
                    0;
                activeGrandTotal = activeSubtotal;
            } else {
                activeTax = activeSubtotal * (Number.isFinite(taxRate) ? taxRate : 0);
                activeGrandTotal = activeSubtotal + activeTax;
            }

            const formatMoney = (value) => `₹${Number(value || 0).toFixed(2)}`;
            const formatPlainMoney = (value) => `Rs ${Math.round(Number(value || 0))}`;

            const sidebarSubtotal = document.getElementById('sidebarSubtotalAmount');
            const sidebarTax = document.getElementById('sidebarTaxAmount');
            const sidebarTaxSummary = document.getElementById('sidebarTaxSummary');
            const sidebarGrandTotal = document.getElementById('sidebarGrandTotalAmount');
            const orderSubtotal = document.getElementById('orderSubtotalAmount');
            const orderTaxRow = document.getElementById('orderTaxDetailRow');
            const orderTaxAmount = document.getElementById('orderTaxAmount');
            const orderTaxLabel = document.getElementById('orderTaxLabel');
            const orderGrandTotal = document.getElementById('orderGrandTotalAmount');
            const footerGrandTotal = document.getElementById('footerGrandTotalAmount');
            const liveItemCount = document.getElementById('orderLiveItemCount');
            const headerItemCount = document.getElementById('orderItemsHeaderCount');
            const branchTaxLabel = String(document.body.dataset.orderTaxLabel || 'Tax');

            if (sidebarSubtotal) sidebarSubtotal.textContent = formatMoney(activeSubtotal);
            if (sidebarTax) sidebarTax.textContent = formatMoney(activeTax);
            if (sidebarTaxSummary) {
                if (taxSetting === 'inclusive' || !(Number.isFinite(taxRate) && taxRate > 0)) {
                    sidebarTaxSummary.classList.add('hidden');
                } else {
                    sidebarTaxSummary.classList.remove('hidden');
                }
            }
            if (sidebarGrandTotal) sidebarGrandTotal.textContent = formatMoney(activeGrandTotal);
            if (orderSubtotal) orderSubtotal.textContent = formatPlainMoney(activeSubtotal);
            if (orderTaxAmount) orderTaxAmount.textContent = formatPlainMoney(activeTax);
            if (orderTaxLabel) orderTaxLabel.textContent = branchTaxLabel;
            if (orderTaxRow) {
                if (taxSetting === 'inclusive' || !(Number.isFinite(taxRate) && taxRate > 0)) {
                    orderTaxRow.classList.add('hidden');
                } else {
                    orderTaxRow.classList.remove('hidden');
                }
            }
            if (orderGrandTotal) orderGrandTotal.textContent = formatPlainMoney(activeGrandTotal);
            if (footerGrandTotal) footerGrandTotal.textContent = formatPlainMoney(activeGrandTotal);
            if (liveItemCount) liveItemCount.textContent = String(totalQuantity);
            if (headerItemCount) headerItemCount.textContent = String(totalQuantity);
        }

        function applyOrderStatusSnapshot(snapshot) {
            if (!snapshot) return;

            const stage = snapshot.kitchen_stage || {};
            applyKitchenStage(stage.step, stage.label, stage.note);

            const items = Array.isArray(snapshot.items) ? snapshot.items : [];
            items.forEach((item) => {
                if (String(item.status || '').toLowerCase() === 'rejected') {
                    applyRejectedItemToRow(item.id, item.item_name, item.rejection_reason);
                    notifyRejectedItem(item.id, item.item_name, item.rejection_reason);
                }
            });

            applyHiddenRejectedItems();
        }

        async function syncOrderStatusSnapshot() {
            const liveKitchenCard = document.getElementById('liveKitchenCard');
            const snapshotUrl = liveKitchenCard?.dataset.snapshotUrl;
            if (!snapshotUrl) return;

            try {
                const response = await fetch(snapshotUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });

                if (!response.ok) return;

                const snapshot = await response.json();
                applyOrderStatusSnapshot(snapshot);
            } catch (error) {
                console.error('Order status live sync failed', error);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const totalToggle = document.getElementById('orderTotalToggle');
            const totalDetails = document.getElementById('orderTotalDetails');
            const totalChevron = document.getElementById('orderTotalChevron');
            const orderPlacedModal = document.getElementById('orderPlacedModal');
            const closeOrderPlacedModal = document.getElementById('closeOrderPlacedModal');
            const continueOrderingBtn = document.getElementById('continueOrderingBtn');
            const notificationBellBtn = document.getElementById('orderNotificationBellBtn');
            const clearNotificationsBtn = document.getElementById('clearOrderNotificationsBtn');
            const notificationPanel = document.getElementById('orderNotificationPanel');
            const currentBranchId = Number(@json((int) ($table->branch_id ?? 0)));
            const currentTableNumber = String(@json($tableNumber));
            document.body.dataset.orderTaxSetting = @json($branchTaxSetting);
            document.body.dataset.orderTaxLabel = @json($branchTaxLabelName);
            document.body.dataset.orderTaxRate = @json($taxRate);

            applyKitchenStage(
                document.getElementById('liveKitchenCard')?.dataset.stage || @json($kitchenStage['step']),
                @json($kitchenStage['label']),
                @json($kitchenStage['note'])
            );
            applyHiddenRejectedItems();
            renderOrderNotifications();

            const billSummaryModal = document.getElementById('billSummaryModal');
            if (billSummaryModal) {
                billSummaryModal.classList.add('hidden');
            }

            resetOnlinePaymentInlinePanel();

            if (String(paymentResultData.status || '') === 'completed') {
                setTimeout(() => {
                    openPaymentSuccessModal();
                }, 150);
            } else if (String(paymentResultData.status || '') === 'cancelled') {
                alert(paymentResultData.message || 'Payment was cancelled.');
            }

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeBillSummaryModal();
                }
            });

            document.querySelectorAll('.order-item-row[data-order-item-id]').forEach((row) => {
                const isRejected = String(row.dataset.orderItemStatus || '').toLowerCase() === 'rejected';
                if (isRejected) {
                    notifyRejectedItem(row.dataset.orderItemId, row.dataset.orderItemName, row
                        .querySelector('[data-item-rejection-reason]')?.textContent?.trim() || '');
                }
            });

            if (notificationBellBtn) {
                notificationBellBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    openRejectedNotificationPanel();
                });
            }

            if (clearNotificationsBtn) {
                clearNotificationsBtn.addEventListener('click', () => {
                    saveOrderNotifications([]);
                    saveRejectedSeenIds([]);
                    renderOrderNotifications();
                    if (notificationPanel) {
                        notificationPanel.classList.add('hidden');
                    }
                });
            }

            document.addEventListener('click', (event) => {
                if (!notificationPanel || notificationPanel.classList.contains('hidden')) return;
                if (notificationPanel.contains(event.target) || notificationBellBtn?.contains(event.target))
                    return;
                notificationPanel.classList.add('hidden');
            });

            if (window.Echo && currentBranchId > 0) {
                window.Echo.private(`orders.branch.${currentBranchId}`)
                    .listen('KitchenStatusUpdated', (e) => {
                        const payload = e?.kitchenData || {};
                        const tableNum = String(payload.table_number ?? '');
                        if (tableNum && tableNum !== currentTableNumber) {
                            return;
                        }

                        const kitchenStatus = String(payload.kitchen_status ?? payload.item_status ?? '')
                            .toLowerCase();
                        const rejectionReason = String(payload.rejection_reason ?? '').trim();
                        const itemStatus = String(payload.item_status ?? '').toLowerCase();
                        const itemId = String(payload.item_id ?? '').trim();
                        const itemName = String(payload.item_name ?? '').trim();
                        const statusMap = {
                            pending: 'accepted',
                            confirmed: 'accepted',
                            accepted: 'accepted',
                            preparing: 'preparing',
                            ready: 'served',
                            served: 'served',
                        };
                        const stage = statusMap[kitchenStatus] || 'accepted';
                        const labelMap = {
                            accepted: 'Accepted',
                            preparing: 'Preparing',
                            served: 'Served',
                        };
                        const noteMap = {
                            accepted: 'Kitchen has accepted your order.',
                            preparing: 'Kitchen is preparing your order.',
                            served: 'Your order has been served.',
                        };

                        applyKitchenStage(stage, labelMap[stage], noteMap[stage]);

                        if (itemStatus === 'rejected' && itemId) {
                            applyRejectedItemToRow(itemId, itemName, rejectionReason);
                        }

                        applyHiddenRejectedItems();
                    });
            }

            syncOrderStatusSnapshot();
            setInterval(syncOrderStatusSnapshot, 5000);

            if (totalToggle && totalDetails && totalChevron) {
                totalToggle.addEventListener('click', () => {
                    const isHidden = totalDetails.classList.toggle('hidden');
                    const icon = totalChevron.querySelector('i');

                    if (icon) {
                        icon.classList.toggle('fa-chevron-down', isHidden);
                        icon.classList.toggle('fa-chevron-up', !isHidden);
                    }
                });
            }

            if (orderPlacedModal) {
                const clearPlacedQuery = () => {
                    const url = new URL(window.location.href);
                    if (url.searchParams.has('placed')) {
                        url.searchParams.delete('placed');
                        window.history.replaceState({}, '', url.toString());
                    }
                };

                const closePlacedModal = () => {
                    orderPlacedModal.remove();
                    clearPlacedQuery();
                };

                closeOrderPlacedModal && closeOrderPlacedModal.addEventListener('click', closePlacedModal);
                continueOrderingBtn && continueOrderingBtn.addEventListener('click', closePlacedModal);
            }

            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-hide-rejected-item]');
                if (!button) return;

                const row = button.closest('.order-item-row');
                const itemId = row?.dataset.orderItemId;
                if (!itemId) return;

                hideRejectedItemRow(itemId);
            });
        });
    </script>
@endsection
