@extends(auth()->check() ? 'core.layouts.admin' : 'core.layouts.menu-public')

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

    .ribbon {
        position: absolute;
        top: 0;
        right: 25px;
        background: #f97316;
        color: white;
        font-size: 8px;
        font-weight: 800;
        padding: 4px 12px;
        clip-path: polygon(10% 0, 100% 0, 100% 100%, 0 100%);
        transform: translateY(-2px);
        z-index: 10;
    }

    .food-type-icon {
        width: 14px;
        height: 14px;
        border: 1px solid currentColor;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 2px;
        padding: 2px;
    }

    .food-type-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: currentColor;
    }

    body.light-theme .menu-card {
        background: #ffffff;
        border-color: #e5e7eb;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }

    body.light-theme .preview-footer .text-white,
    body.light-theme .preview-footer .text-white\/80,
    body.light-theme .preview-footer .text-white\/60 {
        color: #ffffff !important;
    }
</style>
@include('core.components.order-flow.partials.theme-overrides')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

@section('content')
    <script>
        window.storeTaxSetting = "{{ $branch->tax_setting ?? 'exclusive' }}";
        window.storeTaxRate = parseFloat("{{ $branch->tax_rate ?? 5.0 }}") / 100;
        window.storeTaxLabelName = parseFloat("{{ $branch->tax_rate ?? 5.0 }}") === 13.00 ? "VAT" : "Tax";
    </script>
    @php
        $isLightTheme = strtolower((string) ($publicMenuTheme ?? 'dark')) === 'light';
        $menuThemeClass = $isLightTheme ? 'menu-theme-light' : '';
        $categoryList = $menuCategories ?? collect();
        $activeCategory = $selectedCategory ?? $categoryList->first();
        $activeItems = collect($activeCategory['items'] ?? []);
        $isAdminPreview = auth()->check() && request()->routeIs('menu.preview');
        $qrToken = request()->route('qr_token');
        $hasActiveOrder = (bool) ($hasActiveOrder ?? false);
        $orderStatusUrl = $qrToken && $hasActiveOrder ? route('public.order.status', $qrToken) : null;
        $tableAccessSession = $tableAccessSession ?? null;
        $tableSessionToken = (string) ($tableSessionToken ?? $tableAccessSession?->session_token ?? '');
        $tableSessionStartedAt = $tableSessionStartedAt ?? $tableAccessSession?->started_at?->toIso8601String();
        $tableSessionExpiresAt = $tableSessionExpiresAt ?? $tableAccessSession?->expires_at?->toIso8601String();
        $tableAccessSessionPayload = [
            'token' => $tableSessionToken,
            'status' => (string) ($tableAccessSession?->status ?? 'active'),
            'started_at' => $tableSessionStartedAt,
            'expires_at' => $tableSessionExpiresAt,
        ];

        $searchItems = $categoryList
            ->flatMap(function ($category) {
                return collect($category['items'] ?? [])->map(function ($item) use ($category) {
                    $displayPrice =
                        $item->sale_price && $item->sale_price > 0
                            ? (float) $item->sale_price
                            : (float) $item->base_price;

                    $hasVariants = (bool) $item->has_variants;
                    if ($hasVariants && $item->variants->isNotEmpty()) {
                        $displayPrice = (float) $item->variants->min(function ($v) {
                            return $v->sale_price > 0 ? $v->sale_price : $v->base_price;
                        });
                    }

                    return [
                        'id' => (int) $item->id,
                        'name' => (string) $item->name,
                        'description' => (string) ($item->description ?? ''),
                        'type' => (string) ($item->type ?? 'non_veg'),
                        'base_price' => (float) $item->base_price,
                        'sale_price' => (float) ($item->sale_price ?? 0),
                        'display_price' => $displayPrice,
                        'has_variants' => $hasVariants,
                        'is_recommended' => (bool) $item->is_recommended,
                        'image' => $item->image
                            ? asset('storage/' . $item->image)
                            : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=400',
                        'category_id' => (int) $category['id'],
                        'category_name' => (string) $category['name'],
                        'category_url' => request()->fullUrlWithQuery(['category' => $category['id']]),
                        'variants_json' => $item->variants
                            ->map(function ($v) {
                                return [
                                    'id' => $v->id,
                                    'name' => $v->name,
                                    'price' => $v->sale_price > 0 ? (float) $v->sale_price : (float) $v->base_price,
                                ];
                            })
                            ->toArray(),
                        'addons_json' => $item->addons
                            ->map(function ($a) {
                                return ['id' => $a->id, 'name' => $a->name, 'price' => (float) $a->price];
                            })
                            ->toArray(),
                    ];
                });
            })
            ->values();

        $tenantName = $tenant->company_name ?? 'Menu';
        $tableLabel = $tableNumber && $tableNumber !== 'N/A' ? '#' . $tableNumber : 'N/A';
        $resolvedTableId = isset($tableId) && $tableId ? (int) $tableId : null;
        $headerClass = $isLightTheme ? 'bg-white/95 border-slate-200' : 'bg-gray-900/80 border-gray-700';
        $callWaiterClass = $isLightTheme
            ? 'bg-white border-slate-200 text-orange-600 hover:bg-orange-50 shadow-sm'
            : 'bg-transparent border-orange-500/40 text-orange-500 hover:bg-orange-600';
        $viewCartClass = $isLightTheme
            ? 'bg-white text-orange-600 border border-orange-200 shadow-sm'
            : 'bg-white/20 text-white';
        $quickMenuItemClass = $isLightTheme
            ? 'bg-white border-slate-200 text-slate-700 shadow-sm hover:bg-slate-50'
            : 'bg-gray-900/95 border-orange-500/30 text-white shadow-2xl shadow-black/30 hover:bg-gray-800';
        $quickMenuIconClasses = [
            'track' => $isLightTheme ? 'bg-orange-100 text-orange-600' : 'bg-orange-500 text-white',
            'bill' => $isLightTheme ? 'bg-emerald-100 text-emerald-600' : 'bg-emerald-500 text-white',
            'waiter' => $isLightTheme ? 'bg-blue-100 text-blue-600' : 'bg-blue-500 text-white',
        ];
    @endphp

    <div class="{{ $menuThemeClass }}" data-table-access-session-token="{{ $tableSessionToken }}"
        data-table-access-session-started-at="{{ $tableSessionStartedAt }}"
        data-table-access-session-expires-at="{{ $tableSessionExpiresAt }}">
        <div class="flex-1 h-screen lg:h-[calc(100vh-2rem)] flex overflow-hidden p-0 lg:p-4" x-data="{
            customizeOpen: false,
            cartOpen: false,
            quickMenuOpen: false,
            customItem: { id: '', name: '', variants: [], addons: [] },
            selectedVariant: null,
            selectedAddons: [],
            itemNotes: '',
            modalQty: 1,
            overallNotes: '',
            editingUniqueKey: '',
            openCustomizeSheet(itemData) {
                this.customItem = itemData;
                this.selectedVariant = itemData.variants.length ? itemData.variants[0] : null;
                this.selectedAddons = [];
                this.itemNotes = '';
                this.modalQty = 1;
                this.editingUniqueKey = '';
                this.customizeOpen = true;
            },
            toggleAddonQty(addon, action) {
                let existing = this.selectedAddons.find(a => a.id === addon.id);
                if (existing) {
                    if (action === 'plus') {
                        existing.quantity++;
                    } else if (action === 'minus') {
                        existing.quantity--;
                        if (existing.quantity <= 0) {
                            this.selectedAddons = this.selectedAddons.filter(a => a.id !== addon.id);
                        }
                    }
                } else if (action === 'plus') {
                    this.selectedAddons.push({ id: addon.id, name: addon.name, price: addon.price, quantity: 1 });
                }
            },
            getAddonQty(addonId) {
                let match = this.selectedAddons.find(a => a.id === addonId);
                return match ? match.quantity : 0;
            },
            editCartItem(cartItem) {
                let masterItem = window.allItems ? window.allItems.find(i => i.id === cartItem.id) : null;
                this.customItem = {
                    id: cartItem.id,
                    name: cartItem.name,
                    display_price: masterItem ? masterItem.display_price : cartItem.price,
                    variants: masterItem ? masterItem.variants_json : [],
                    addons: masterItem ? masterItem.addons_json : []
                };
                this.selectedVariant = this.customItem.variants.find(v => v.name === cartItem.variant_name) || null;
                this.selectedAddons = JSON.parse(JSON.stringify(cartItem.addons || []));
                this.itemNotes = cartItem.notes || '';
                this.modalQty = cartItem.quantity || 1;
                this.editingUniqueKey = cartItem.unique_key;
                this.cartOpen = false;
                this.customizeOpen = true;
            },
            toggleQuickMenu() {
                this.quickMenuOpen = !this.quickMenuOpen;
            },
            addCustomizedToCart() {
                if (this.editingUniqueKey) {
                    $store.cart.removeItemByKey(this.editingUniqueKey);
                    this.editingUniqueKey = '';
                }
        
                let itemBasePrice = (this.selectedVariant ? this.selectedVariant.price : this.customItem.display_price);
                let addonIds = this.selectedAddons.map(a => a.id + '-' + a.quantity).sort().join('-');
                let variantId = this.selectedVariant ? this.selectedVariant.id : '0';
        
                let serializedNotes = this.itemNotes.trim().toLowerCase().replace(/[^a-z0-9]/g, '');
                let uniqueKey = this.customItem.id + '_' + variantId + '_' + (addonIds || '0') + '_' + (serializedNotes || 'none');
        
                $store.cart.add({
                    id: this.customItem.id,
                    unique_key: uniqueKey,
                    name: this.customItem.name,
                    price: itemBasePrice,
                    variant_name: this.selectedVariant ? this.selectedVariant.name : '',
                    addons: JSON.parse(JSON.stringify(this.selectedAddons)),
                    notes: this.itemNotes.trim(),
                    quantity: this.modalQty
                });
                this.customizeOpen = false;
            }
        }"
            @open-custom-sheet.window="openCustomizeSheet($event.detail)">

            <aside class="hidden lg:flex flex-col w-72 bg-gray-800 p-6 rounded-l-lg border border-gray-700">
                <h2 class="text-lg font-bold text-gray-500 uppercase tracking-[2px] mb-8">Categories</h2>
                <div class="space-y-2 overflow-y-auto no-scrollbar">
                    @forelse ($categoryList as $category)
                        <a href="{{ request()->fullUrlWithQuery(['category' => $category['id']]) }}"
                            class="w-full flex items-center justify-between p-4 rounded-lg border transition {{ $activeCategory && $activeCategory['id'] === $category['id'] ? 'bg-orange-500/10 text-white font-bold border-orange-500/30' : 'text-gray-400 hover:bg-white/5 border-gray-700' }}">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-utensils text-sm"></i>
                                <span>{{ $category['name'] }}</span>
                            </div>
                            <span
                                class="text-[10px] px-2 py-1.5 rounded-full {{ $activeCategory && $activeCategory['id'] === $category['id'] ? 'bg-black/20' : 'text-gray-500' }}">
                                {{ $category['items_count'] }}
                            </span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">No active categories with items found.</p>
                    @endforelse
                </div>
            </aside>

            <main
                class="flex-1 flex flex-col relative bg-gray-900 lg:rounded-r-lg border-x lg:border-r border-gray-700 overflow-hidden">
                <header
                    class="sticky lg:sticky top-0 w-full z-20 flex justify-between items-center border-b {{ $headerClass }} px-4 py-2.5 lg:px-6 lg:py-4 backdrop-blur-sm lg:mb-0">
                    <div class="flex items-center gap-3">
                        <h1
                            class="text-[13px] lg:text-2xl font-bold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }} uppercase tracking-tight">
                            {{ $tenantName }}
                        </h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex item-center text-right gap-2">
                            <p
                                class="text-[10px] {{ $isLightTheme ? 'text-slate-500' : 'text-gray-500' }} font-bold leading-none hidden lg:block pt-0.5">
                                TABLE</p>
                            <span class="text-[10px] text-orange-500/80 font-bold">{{ $tableLabel }}</span>
                        </div>
                        <button id="callWaiterBtn" type="button" onclick="callWaiter(this)"
                            class="{{ $callWaiterClass }} px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition group">
                            <i class="fas fa-bell text-[10px] group-hover:animate-bounce"></i> Call Waiter
                        </button>
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto no-scrollbar px-2.5 mt-2 lg:p-8 lg:space-y-6 lg:mt-0">
                    <div class="swiper mySwiper w-full h-20 lg:h-48 overflow-hidden relative group px-2">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide relative"><img src="{{ asset('images/menu-banner.png') }}"
                                    class="w-full h-full object-cover"></div>
                            <div class="swiper-slide relative"><img src="{{ asset('images/offer-banner.png') }}"
                                    class="w-full h-full object-cover"></div>
                            <div class="swiper-slide relative"><img src="{{ asset('images/menu-banner.png') }}"
                                    class="w-full h-full object-cover"></div>
                        </div>
                        <div class="absolute inset-y-0 left-2 z-20 flex items-center">
                            <button
                                class="swiper-prev-custom w-6 h-6 lg:w-8 lg:h-8 flex items-center justify-center rounded-full bg-black/40 text-white"><i
                                    class="fas fa-chevron-left text-[10px]"></i></button>
                        </div>
                        <div class="absolute inset-y-0 right-2 z-20 flex items-center">
                            <button
                                class="swiper-next-custom w-6 h-6 lg:w-8 lg:h-8 flex items-center justify-center rounded-full bg-black/40 text-white"><i
                                    class="fas fa-chevron-right text-[10px]"></i></button>
                        </div>
                    </div>

                    <div class="relative flex items-center gap-2 mt-0 mb-3">
                        <div class="relative flex-1">
                            <input type="text" id="menuSearch" placeholder="Search dish..."
                                class="w-full bg-gray-800 border border-gray-700 rounded-xl py-3 pl-4 pr-10 text-sm text-white placeholder-gray-500 outline-none focus:border-orange-500/50 transition">
                        </div>
                        <button id="menuSearchBtn"
                            class="bg-orange-600 h-11 w-11 rounded-xl flex items-center justify-center shadow-lg shadow-orange-600/30">
                            <i id="menuSearchIcon" class="fas fa-search text-white"></i>
                            <i id="menuClearIcon" class="fas fa-times text-white" style="display:none;"></i>
                        </button>
                    </div>

                    @if ($orderStatusUrl)
                        <a href="{{ $orderStatusUrl }}"
                            class="mb-2 flex items-center justify-between gap-3 rounded-xl border border-orange-500/30 bg-orange-500/10 px-4 py-3 text-orange-100 shadow-sm transition hover:bg-orange-500/15">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-orange-300/80">Your Active
                                    Order</p>
                                <p class="mt-1 text-sm font-extrabold text-white truncate">
                                    View live status
                                </p>
                            </div>
                            <span
                                class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-orange-500 text-white">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </span>
                        </a>
                    @endif

                      <div id="mobileCategoryTabs"
                        class="sticky top-0 z-15 bg-gray-900 lg:hidden flex gap-2 overflow-x-auto no-scrollbar py-2">
                        @foreach ($categoryList as $category)
                            <a href="{{ request()->fullUrlWithQuery(['category' => $category['id']]) }}"
                                data-category-id="{{ $category['id'] }}"
                                class="flex-shrink-0 px-5 py-2.5 rounded-full text-xs uppercase {{ $activeCategory && $activeCategory['id'] === $category['id'] ? 'bg-orange-600 font-bold' : 'bg-gray-800 border border-gray-700 text-gray-400 font-bold' }}">
                                {{ $category['name'] }}
                            </a>
                        @endforeach
                    </div>

                    <div id="defaultMenuView">
                        @if ($activeCategory)
                            <div class="flex items-center gap-2 py-3">
                                <h3 class="font-medium text-lg text-orange-500/90 uppercase tracking-[0.5px]">
                                    {{ $activeCategory['name'] }}</h3>
                                <span class="text-gray-500 text-sm font-bold">({{ $activeCategory['items_count'] }})</span>
                            </div>

                            @if ($activeItems->isNotEmpty())
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5 pb-28">
                                    @foreach ($activeItems as $item)
                                        @php
                                            $typeColor = match ($item->type) {
                                                'veg' => 'text-green-500',
                                                'egg' => 'text-yellow-400',
                                                default => 'text-red-500',
                                            };
                                            $hasVariants = (bool) $item->has_variants;

                                            $displayPrice =
                                                $item->sale_price && $item->sale_price > 0
                                                    ? (float) $item->sale_price
                                                    : (float) $item->base_price;
                                            if ($hasVariants && $item->variants->isNotEmpty()) {
                                                $displayPrice = (float) $item->variants->min(function ($v) {
                                                    return $v->sale_price > 0 ? $v->sale_price : $v->base_price;
                                                });
                                            }
                                            $basePrice = (float) $item->base_price;
                                            $image = $item->image
                                                ? asset('storage/' . $item->image)
                                                : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=400';
                                        @endphp

                                        <div class="menu-card relative group rounded-lg p-4 flex gap-3 sm:gap-4 border-2 border-orange-500/60 transition-all hover:scale-[1.01] cursor-pointer"
                                            @click="openCustomizeSheet({
                                             id: {{ $item->id }},
                                             name: '{{ e($item->name) }}',
                                             display_price: {{ $displayPrice }},
                                             variants: {{ $hasVariants? json_encode($item->variants->map(function ($v) {return ['id' => $v->id, 'name' => $v->name, 'price' => $v->sale_price > 0 ? (float) $v->sale_price : (float) $v->base_price];})): '[]' }},
                                             addons: {{ json_encode($item->addons->map(function ($a) {return ['id' => $a->id, 'name' => $a->name, 'price' => (float) $a->price];})) }}
                                         })">

                                            @if ($item->is_recommended)
                                                <div class="ribbon uppercase">Bestseller</div>
                                            @endif

                                            <div class="relative w-24 h-24 sm:w-28 sm:h-28 lg:w-32 lg:h-32 flex-shrink-0">
                                                <img src="{{ $image }}"
                                                    class="w-full h-full object-cover rounded-2xl"
                                                    alt="{{ $item->name }}">
                                                <div
                                                    class="absolute top-2 right-2 bg-black/40 backdrop-blur-md p-1 rounded-md {{ $typeColor }}">
                                                    <div class="food-type-icon">
                                                        <div class="food-type-dot"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex flex-col justify-between flex-1 py-1 min-w-0">
                                                <div>
                                                    <h4
                                                        class="font-bold text-white text-sm sm:text-base lg:text-lg leading-tight break-words">
                                                        {{ $item->name }}</h4>
                                                    <p
                                                        class="text-[11px] text-gray-500 mt-1 leading-relaxed line-clamp-2 italic">
                                                        {{ $item->description ?: 'Freshly prepared and served with care.' }}
                                                    </p>
                                                </div>
                                                <div class="flex justify-between items-center mt-2 sm:mt-3 gap-2">
                                                    <div class="flex items-center gap-1 min-w-0">
                                                        <span
                                                            class="text-lg sm:text-xl font-bold text-orange-500">₹{{ number_format($displayPrice, 2) }}</span>
                                                        @if ($hasVariants)
                                                            <span
                                                                class="text-[9px] text-gray-400 lowercase font-medium pt-1">onwards</span>
                                                        @elseif ($item->sale_price && $displayPrice < $basePrice)
                                                            <span
                                                                class="text-xs text-gray-500 line-through">₹{{ number_format($basePrice, 2) }}</span>
                                                        @endif
                                                    </div>

                                                    <div @click.stop>
                                                        @if ($hasVariants)
                                                            <button type="button"
                                                                @click="openCustomizeSheet({
                                                                id: {{ $item->id }},
                                                                name: '{{ e($item->name) }}',
                                                                display_price: {{ $displayPrice }},
                                                                variants: {{ json_encode($item->variants->map(function ($v) {return ['id' => $v->id, 'name' => $v->name, 'price' => $v->sale_price > 0 ? (float) $v->sale_price : (float) $v->base_price];})) }},
                                                                addons: {{ json_encode($item->addons->map(function ($a) {return ['id' => $a->id, 'name' => $a->name, 'price' => (float) $a->price];})) }}
                                                            })"
                                                                class="bg-gray-800 hover:bg-gray-700 border border-gray-700 text-orange-500 px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-md">
                                                                Add <span
                                                                    class="text-[10px] text-orange-400 font-light">+</span>
                                                            </button>
                                                        @else
                                                            <template
                                                                x-if="!$store.cart.hasSimpleItem({{ $item->id }})">
                                                                <button type="button"
                                                                    @click="$store.cart.add({ id: {{ $item->id }}, unique_key: '{{ $item->id }}_0_0_none', name: '{{ e($item->name) }}', price: {{ $displayPrice }}, variant_name: '', addons: [], notes: '', quantity: 1 })"
                                                                    class="bg-gray-800 border border-gray-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition hover:border-orange-500">
                                                                    Add
                                                                </button>
                                                            </template>
                                                            <template
                                                                x-if="$store.cart.hasSimpleItem({{ $item->id }})">
                                                                <div
                                                                    class="flex items-center gap-3 bg-gray-800 border border-gray-700 rounded-xl px-3 py-1.5 text-white text-xs font-bold">
                                                                    <button type="button"
                                                                        @click="$store.cart.decrementSimple({{ $item->id }})">-</button>
                                                                    <span
                                                                        x-text="$store.cart.getSimpleQty({{ $item->id }})"></span>
                                                                    <button type="button"
                                                                        @click="$store.cart.incrementSimple({{ $item->id }})">+</button>
                                                                </div>
                                                            </template>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="rounded-xl border border-dashed border-gray-700 p-8 text-center text-gray-400">
                                    Sorry, items not found in this category.</div>
                            @endif
                        @else
                            <div class="rounded-xl border border-dashed border-gray-700 p-8 text-center text-gray-400">No
                                menu
                                items available for preview yet.</div>
                        @endif
                    </div>

                    <div id="searchResultsView" class="hidden pb-28">
                        <div class="flex items-center justify-between gap-2 py-3">
                            <h3 id="searchTitle"
                                class="font-medium text-lg text-orange-500/90 uppercase tracking-[0.5px]">
                                Search Results</h3>
                            <span id="searchCount" class="text-gray-500 text-sm font-bold">(0)</span>
                        </div>
                        <div id="searchResultsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5"></div>
                        <div id="searchNoResults"
                            class="hidden rounded-xl border border-dashed border-gray-700 p-8 text-center text-gray-400 mt-2">
                            No matching items found.</div>
                    </div>
                </div>

                <footer
                    class="preview-footer fixed lg:sticky bottom-0 sm:bottom-6 left-0 w-full z-40 border-t border-orange-300/30 bg-gradient-to-r from-[#f97316] to-[#ea580c] shadow-[0_-10px_30px_rgba(249,115,22,0.35)] cursor-pointer transition active:scale-[0.99] "
                    x-show="$store.cart.items.length > 0" x-transition @click="cartOpen = true">
                    <div class="p-4 sm:px-6 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="relative w-9 h-9 bg-black/20 rounded-xl flex items-center justify-center border border-white/10">
                                <i class="fas fa-shopping-basket text-white text-base"></i>
                                <span x-text="$store.cart.totalItems()"
                                    class="absolute -top-1.5 -right-1.5 bg-white text-orange-600 text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow-md">0</span>
                            </div>
                            <div>
                                <span class="block text-white font-bold text-[11px]"
                                    x-text="$store.cart.totalItems() + ' Items Added'">0 Items Added</span>
                                {{-- <span class="block text-[11px] text-white/80 font-medium">Click to review configurations</span> --}}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- <span class="text-white font-bold text-lg mr-2"
                            x-text="'₹' + $store.cart.totalPrice().toFixed(2)">₹0</span> --}}
                            <span
                                class="{{ $viewCartClass }} font-bold text-xs uppercase tracking-wider px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                                View Cart <i class="fas fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>
                    </div>
                </footer>

                @if ($orderStatusUrl)
                    <div class="fixed right-2 bottom-18 z-50 flex flex-col items-end gap-3"
                        @click.away="quickMenuOpen = false">
                        <div x-show="quickMenuOpen" x-transition.opacity.scale.origin.bottom.right
                            class="flex flex-col gap-2">
                            <a href="{{ $orderStatusUrl }}"
                                class="inline-flex items-center gap-3 rounded-full border px-4 py-3 backdrop-blur-md transition {{ $quickMenuItemClass }}">
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-full {{ $quickMenuIconClasses['track'] }}">
                                    <i class="fas fa-clock text-sm"></i>
                                </span>
                                <span
                                    class="text-sm font-bold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">Track
                                    Order</span>
                            </a>

                            <a href="{{ route('checkout') }}"
                                class="inline-flex items-center gap-3 rounded-full border px-4 py-3 backdrop-blur-md transition {{ $quickMenuItemClass }}">
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-full {{ $quickMenuIconClasses['bill'] }}">
                                    <i class="fas fa-file-invoice-dollar text-sm"></i>
                                </span>
                                <span
                                    class="text-sm font-bold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">Request
                                    Bill</span>
                            </a>

                            <button type="button" @click="callWaiter($event.currentTarget); quickMenuOpen = false"
                                class="inline-flex items-center gap-3 rounded-full border px-4 py-3 backdrop-blur-md transition {{ $quickMenuItemClass }}">
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-full {{ $quickMenuIconClasses['waiter'] }}">
                                    <i class="fas fa-bell-concierge text-sm"></i>
                                </span>
                                <span class="text-sm font-bold {{ $isLightTheme ? 'text-slate-900' : 'text-white' }}">Call
                                    Waiter</span>
                            </button>
                        </div>

                        <button type="button" @click="toggleQuickMenu()"
                            class="touch-none select-none inline-flex h-14 w-14 items-center justify-center rounded-full bg-orange-500 text-white shadow-2xl shadow-orange-600/30 border border-orange-400/30 transition hover:bg-orange-400">
                            <i class="fas" :class="quickMenuOpen ? 'fa-xmark' : 'fa-plus'"></i>
                        </button>
                    </div>
                @endif
            </main>

            <div x-show="customizeOpen"
                class="fixed inset-0 z-50 flex items-end justify-center bg-black/60 backdrop-blur-xs" x-transition
                style="display: none;">
                <div @click.away="customizeOpen = false"
                    class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-t-2xl p-6 space-y-6 shadow-2xl max-h-[85vh] overflow-y-auto no-scrollbar border-t border-gray-200 dark:border-gray-700">

                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="customItem.name">Dish Name
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Customize your portions & extra modifiers.</p>
                        </div>
                        <button type="button" @click="customizeOpen = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><i
                                class="fas fa-times text-lg"></i></button>
                    </div>

                    <template x-if="customItem.variants && customItem.variants.length">
                        <div class="space-y-3">
                            <span class="text-xs font-bold text-orange-500 uppercase tracking-wider block">Select Portion
                                Size</span>
                            <div class="grid grid-cols-1 gap-2">
                                <template x-for="v in customItem.variants" :key="v.id">
                                    <label
                                        class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 cursor-pointer transition hover:border-orange-500/40"
                                        :class="selectedVariant && selectedVariant.id === v.id ?
                                            'border-orange-500 dark:border-orange-500 bg-orange-500/5' : ''">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="selected_portion" :value="v"
                                                :checked="selectedVariant && selectedVariant.id === v.id"
                                                @change="selectedVariant = v"
                                                class="text-orange-500 focus:ring-orange-500">
                                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200"
                                                x-text="v.name">Portion Name</span>
                                        </div>
                                        <span class="text-sm font-bold text-orange-500"
                                            x-text="'₹' + v.price.toFixed(2)">Price</span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="customItem.addons && customItem.addons.length">
                        <div class="space-y-3">
                            <span class="text-xs font-bold text-orange-500 uppercase tracking-wider block">Add-ons / Extra
                                Modifiers</span>
                            <div class="grid grid-cols-1 gap-2">
                                <template x-for="a in customItem.addons" :key="a.id">
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 transition"
                                        :class="getAddonQty(a.id) > 0 ?
                                            'border-orange-500 dark:border-orange-500 bg-orange-500/5' :
                                            ''">

                                        <div>
                                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200 block"
                                                x-text="a.name">Addon Name</span>
                                            <span class="text-xs text-gray-400 font-medium"
                                                x-text="'+ ₹' + a.price.toFixed(2) + ' / portion'">Price</span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <template x-if="getAddonQty(a.id) === 0">
                                                <button type="button" @click="toggleAddonQty(a, 'plus')"
                                                    class="border border-orange-500/40 hover:border-orange-500 text-orange-500 px-3 py-1 text-xs font-bold rounded-lg bg-white dark:bg-gray-900 transition">
                                                    Add +
                                                </button>
                                            </template>
                                            <template x-if="getAddonQty(a.id) > 0">
                                                <div
                                                    class="flex items-center gap-2.5 bg-white dark:bg-gray-900 border border-orange-500 rounded-lg px-2.5 py-1 text-gray-900 dark:text-white text-xs font-bold shadow-xs">
                                                    <button type="button" @click="toggleAddonQty(a, 'minus')"
                                                        class="text-orange-500 font-bold transition text-sm">-</button>
                                                    <span x-text="getAddonQty(a.id)"
                                                        class="w-3 text-center text-xs text-orange-500">1</span>
                                                    <button type="button" @click="toggleAddonQty(a, 'plus')"
                                                        class="text-orange-500 font-bold transition text-sm">+</button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div class="space-y-2 pt-2">
                        <span class="text-xs font-bold text-orange-500 uppercase tracking-wider block">Special Instructions
                            (Optional)</span>
                        <input type="text" x-model="itemNotes"
                            placeholder="ex. Jyada mirch mat dalna, less oil, extra mayo..."
                            class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-3 text-sm text-gray-900 dark:text-white outline-none focus:border-orange-500/50 transition">
                    </div>

                    <div
                        class="pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center justify-between w-full sm:w-auto gap-6">
                            <div>
                                <span class="text-[10px] text-gray-400 uppercase tracking-wider block font-bold">Total
                                    Price</span>
                                <span class="text-xl font-bold text-orange-500"
                                    x-text="'₹' + (((selectedVariant ? selectedVariant.price : customItem.display_price) * modalQty) + selectedAddons.reduce((sum, a) => sum + (a.price * a.quantity), 0)).toFixed(2)">₹0</span>
                            </div>

                            <div
                                class="flex items-center gap-3 bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-gray-900 dark:text-white text-sm font-bold shadow-inner">
                                <button type="button" @click="if(modalQty > 1) modalQty--"
                                    class="px-1 text-gray-500 hover:text-orange-500 font-bold transition text-base">-</button>
                                <span x-text="modalQty" class="w-4 text-center">1</span>
                                <button type="button" @click="modalQty++"
                                    class="px-1 text-gray-500 hover:text-orange-500 font-bold transition text-base">+</button>
                            </div>
                        </div>

                        <button type="button" @click="addCustomizedToCart()"
                            class="w-full sm:flex-1 bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 rounded-xl text-xs uppercase tracking-wider transition shadow-lg shadow-orange-600/20">
                            Add item configuration
                        </button>
                    </div>

                </div>
            </div>

            <div x-show="cartOpen" class="fixed inset-0 z-50 flex items-end justify-center bg-black/70 backdrop-blur-xs"
                x-transition style="display: none;">
                <div @click.away="cartOpen = false"
                    class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-t-2xl p-5 space-y-5 shadow-2xl max-h-[90vh] flex flex-col border-t border-gray-200 dark:border-gray-700">

                    <div
                        class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-3 flex-shrink-0">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-shopping-cart text-orange-500 text-lg"></i>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Review Your Cart Items</h3>
                        </div>
                        <button type="button" @click="cartOpen = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1"><i
                                class="fas fa-times text-base"></i></button>
                    </div>

                    <div class="flex-1 overflow-y-auto no-scrollbar space-y-4 pr-1">
                        <template x-for="item in $store.cart.items" :key="item.unique_key">
                            <div
                                class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-200 dark:border-gray-700/60 flex items-start justify-between gap-3">
                                <div class="space-y-2 min-w-0 flex-1">

                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-bold text-gray-900 dark:text-white text-sm break-words"
                                                x-text="item.name">Item Name</span>
                                            <template x-if="item.variant_name">
                                                <span
                                                    class="text-[10px] bg-orange-500/10 text-orange-600 dark:text-orange-400 font-bold px-1.5 py-0.5 rounded"
                                                    x-text="item.variant_name">Variant</span>
                                            </template>
                                        </div>

                                        <button type="button" @click="editCartItem(item)"
                                            class="text-orange-500 hover:text-orange-600 font-bold text-xs flex items-center gap-1 bg-orange-500/10 px-2 py-1 rounded-md transition active:scale-95">
                                            <i class="fas fa-pencil-alt text-[9px]"></i> Edit
                                        </button>
                                    </div>

                                    <div class="flex flex-wrap gap-1.5 pt-0.5">
                                        <template x-if="item.addons && item.addons.length">
                                            <template x-for="addon in item.addons" :key="addon.id">
                                                <span
                                                    class="inline-flex items-center gap-1 bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400 text-[10px] font-bold px-2 py-0.5 rounded-md border border-orange-200/30">
                                                    <i class="fas fa-plus text-[8px]"></i>
                                                    <span x-text="addon.name"></span>
                                                    <span
                                                        class="bg-orange-600 text-white text-[8px] font-bold px-1 rounded-sm ml-0.5"
                                                        x-show="addon.quantity > 1" x-text="'x' + addon.quantity"></span>
                                                </span>
                                            </template>
                                        </template>

                                        <template x-if="item.notes && item.notes.trim() !== ''">
                                            <span
                                                class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-[10px] font-bold px-2 py-0.5 rounded-md border border-gray-200/50 dark:border-gray-700/50">
                                                <i class="far fa-comment-dots text-[9px] text-orange-500"></i>
                                                <span x-text="'Note: ' + item.notes"></span>
                                            </span>
                                        </template>
                                    </div>

                                    <span class="block text-xs font-bold text-orange-500 pt-0.5"
                                        x-text="'₹' + ((item.price * item.quantity) + (item.addons ? item.addons.reduce((sum, a) => sum + (a.price * a.quantity), 0) : 0)).toFixed(2)">Price</span>
                                </div>

                                <div
                                    class="flex items-center gap-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg px-2.5 py-1 text-gray-900 dark:text-white text-xs font-bold shadow-xs flex-shrink-0">
                                    <button type="button" @click="$store.cart.decrementByKey(item.unique_key)"
                                        class="text-gray-400 hover:text-red-500 font-bold transition text-sm">-</button>
                                    <span x-text="item.quantity" class="w-3 text-center text-xs">1</span>
                                    <button type="button" @click="$store.cart.incrementByKey(item.unique_key)"
                                        class="text-gray-400 hover:text-orange-500 font-bold transition text-sm">+</button>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="cartOpen = false"
                            class="w-full py-2.5 border border-dashed border-orange-500/40 hover:border-orange-500 text-orange-500 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 bg-orange-500/5">
                            <i class="fas fa-plus text-[10px]"></i> Add More Items / Cooking Choices
                        </button>
                    </div>

                    <div class="space-y-1.5 flex-shrink-0 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <label
                            class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide flex items-center gap-1.5">
                            <i class="fas fa-utensils text-orange-500 text-[11px]"></i> Overall Cooking Instructions /
                            Notes
                        </label>
                        <textarea x-model="overallNotes" rows="1" placeholder="ex. Serve everything together, bring extra tissues..."
                            class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-2.5 text-xs text-gray-900 dark:text-white outline-none focus:border-orange-500/50 transition resize-none"></textarea>
                    </div>

                    <div
                        class="rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-3.5 text-xs text-gray-600 dark:text-gray-300 space-y-1.5 flex-shrink-0">
                        <div class="flex justify-between">
                            <span>Items Subtotal:</span>
                            <span class="font-bold text-gray-900 dark:text-white"
                                x-text="'₹' + $store.cart.getRawSubtotal().toFixed(2)">₹0.00</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-200/50 dark:border-gray-700/50 pb-1.5"
                            x-show="window.storeTaxSetting !== 'inclusive'">
                            <span
                                x-text="window.storeTaxLabelName + ' (' + (window.storeTaxRate * 100).toFixed(0) + '%):'">Tax:</span>
                            <span class="font-bold text-gray-900 dark:text-white"
                                x-text="'₹' + $store.cart.getCalculatedTax().toFixed(2)">₹0.00</span>
                        </div>

                        <div class="flex justify-between items-end font-bold text-gray-900 dark:text-white text-sm pt-0.5">
                            <span class="text-orange-500">Grand Total Payable:</span>
                            <span class="text-orange-500 text-base"
                                x-text="'₹' + $store.cart.totalPrice().toFixed(2)">₹0.00</span>
                        </div>
                    </div>

                    @if ($isAdminPreview)
                        <div id="previewOnlyNotice"
                            class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-amber-500/15 text-amber-700">
                                    <i class="fas fa-circle-info"></i>
                                </div>
                                <div>
                                    <p class="font-bold">Preview only</p>
                                    <p class="mt-1 leading-6">
                                        This is only a preview page. You cannot place a real order from here.
                                        Please use the actual table order page for checkout.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="pt-1 flex items-center justify-between gap-4 flex-shrink-0">
                        @if ($isAdminPreview)
                            <button type="button" disabled
                                class="w-full bg-gray-300 text-gray-600 font-bold py-3.5 rounded-xl text-xs uppercase tracking-wider cursor-not-allowed flex items-center justify-center gap-2 border border-gray-200">
                                <i class="fas fa-lock text-sm"></i> Preview Only
                            </button>
                        @else
                            <button type="button" @click="placeOrder(overallNotes)"
                                class="w-full bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-400 text-white font-bold py-3.5 rounded-xl text-xs uppercase tracking-wider transition shadow-lg shadow-orange-600/20 flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle text-sm"></i> Confirm & Place Order
                            </button>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                items: JSON.parse(localStorage.getItem('cart')) || [],

                add(item) {
                    let existing = this.items.find(i => i.unique_key === item.unique_key);
                    if (existing) {
                        existing.quantity += item.quantity;
                    } else {
                        this.items.push({
                            ...item
                        });
                    }
                    this.sync();
                },

                incrementByKey(uniqueKey) {
                    let item = this.items.find(i => i.unique_key === uniqueKey);
                    if (item) {
                        item.quantity++;
                        this.sync();
                    }
                },

                decrementByKey(uniqueKey) {
                    let index = this.items.findIndex(i => i.unique_key === uniqueKey);
                    if (index !== -1) {
                        if (this.items[index].quantity > 1) {
                            this.items[index].quantity--;
                        } else {
                            this.items.splice(index, 1);
                        }
                        this.sync();
                    }
                },

                removeItemByKey(uniqueKey) {
                    this.items = this.items.filter(i => i.unique_key !== uniqueKey);
                    this.sync();
                },

                hasSimpleItem(id) {
                    return this.items.some(i => i.id === id && i.unique_key === id + '_0_0_none');
                },
                getSimpleQty(id) {
                    let item = this.items.find(i => i.id === id && i.unique_key === id + '_0_0_none');
                    return item ? item.quantity : 0;
                },
                incrementSimple(id) {
                    this.incrementByKey(id + '_0_0_none');
                },
                decrementSimple(id) {
                    this.decrementByKey(id + '_0_0_none');
                },

                totalItems() {
                    return this.items.reduce((sum, i) => sum + i.quantity, 0);
                },

                totalPrice() {
                    let sub = this.getRawSubtotal();
                    let taxRate = window.storeTaxRate;
                    if (window.storeTaxSetting === 'inclusive') {
                        return sub;
                    } else {
                        return sub + (sub * taxRate);
                    }
                },

                getCalculatedTax() {
                    let sub = this.getRawSubtotal();
                    let taxRate = window.storeTaxRate;
                    if (window.storeTaxSetting === 'inclusive') {
                        return sub - (sub / (1 + taxRate));
                    } else {
                        return sub * taxRate;
                    }
                },

                getRawSubtotal() {
                    return this.items.reduce((sum, i) => {
                        let addonsTotal = i.addons ? i.addons.reduce((s, a) => s + (a.price * a
                            .quantity), 0) : 0;
                        return sum + (i.price * i.quantity) + addonsTotal;
                    }, 0);
                },

                sync() {
                    localStorage.setItem('cart', JSON.stringify(this.items));
                }
            });
        });
    </script>

    <script>
        window.allItems = @json($searchItems);
        window.selectedMenuCategoryId = @json((int) ($activeCategory['id'] ?? 0));
        window.currentTableAccessSessionToken = @json($tableSessionToken);
        window.tableAccessSession = @json($tableAccessSessionPayload);
        window.tableGeofencePolicy = {
            enabled: @json(!$isAdminPreview && $resolvedTableId && $branch?->latitude !== null && $branch?->longitude !== null),
            radiusMeters: 50,
            branchLatitude: @json($branch?->latitude !== null ? (float) $branch->latitude : null),
            branchLongitude: @json($branch?->longitude !== null ? (float) $branch->longitude : null),
        };
        window.tableGeoState = {
            status: 'idle',
            latitude: null,
            longitude: null,
            accuracy: null,
            error: null,
            promise: null,
        };
        
        
         window.scrollActiveMenuCategoryIntoView = function() {
            const tabs = document.getElementById('mobileCategoryTabs');
            if (!tabs || !window.selectedMenuCategoryId) return;

            const activeTab = tabs.querySelector(`[data-category-id="${window.selectedMenuCategoryId}"]`);
            if (!activeTab) return;

            activeTab.scrollIntoView({
                behavior: 'auto',
                block: 'nearest',
                inline: 'center'
            });
        };

        window.addEventListener('load', () => {
            window.requestAnimationFrame(() => {
                window.scrollActiveMenuCategoryIntoView();
            });
        });


        window.ensureTableGeoLocation = function() {
            if (!window.tableGeofencePolicy.enabled) {
                return Promise.resolve(null);
            }

            if (window.tableGeoState.latitude !== null && window.tableGeoState.longitude !== null) {
                return Promise.resolve(window.tableGeoState);
            }

            if (window.tableGeoState.promise) {
                return window.tableGeoState.promise;
            }

            if (!navigator.geolocation) {
                window.tableGeoState.status = 'error';
                window.tableGeoState.error = 'Location access is not supported by this browser.';
                window.tableGeoState.promise = Promise.reject(new Error(window.tableGeoState.error));
                return window.tableGeoState.promise;
            }

            window.tableGeoState.status = 'requesting';
            window.tableGeoState.promise = new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        window.tableGeoState.status = 'ready';
                        window.tableGeoState.latitude = position.coords.latitude;
                        window.tableGeoState.longitude = position.coords.longitude;
                        window.tableGeoState.accuracy = position.coords.accuracy || null;
                        window.tableGeoState.error = null;
                        resolve(window.tableGeoState);
                    },
                    (error) => {
                        const message = error?.message || 'Unable to read location.';
                        window.tableGeoState.status = 'error';
                        window.tableGeoState.error = message;
                        reject(new Error(message));
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }).catch((error) => {
                window.tableGeoState.status = 'error';
                window.tableGeoState.error = error?.message || 'Unable to read location.';
                throw error;
            });

            return window.tableGeoState.promise;
        };

        if (window.tableGeofencePolicy.enabled) {
            window.ensureTableGeoLocation().catch(() => {});
        }

        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 3000
            },
            navigation: {
                nextEl: ".swiper-next-custom",
                prevEl: ".swiper-prev-custom"
            },
        });

        (function() {
            const menuSearchInput = document.getElementById('menuSearch');
            const menuSearchBtn = document.getElementById('menuSearchBtn');
            const menuSearchIcon = document.getElementById('menuSearchIcon');
            const menuClearIcon = document.getElementById('menuClearIcon');
            const defaultMenuView = document.getElementById('defaultMenuView');
            const searchResultsView = document.getElementById('searchResultsView');
            const searchResultsGrid = document.getElementById('searchResultsGrid');
            const searchNoResults = document.getElementById('searchNoResults');
            const searchCount = document.getElementById('searchCount');
            const searchTitle = document.getElementById('searchTitle');
            const allItems = window.allItems;

            const escapeHtml = (value) => {
                return String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(
                    /"/g, '&quot;').replace(/'/g, '&#039;');
            };

            const updateSearchButtonIcon = () => {
                const hasValue = menuSearchInput.value.trim().length > 0;
                if (hasValue) {
                    menuSearchIcon.style.display = 'none';
                    menuClearIcon.style.display = 'inline-block';
                } else {
                    menuClearIcon.style.display = 'none';
                    menuSearchIcon.style.display = 'inline-block';
                }
            };

            window.triggerSearchCardClick = function(index) {
                const item = allItems[index];
                window.dispatchEvent(new CustomEvent('open-custom-sheet', {
                    detail: {
                        id: item.id,
                        name: item.name,
                        display_price: item.display_price,
                        variants: item.variants_json || [],
                        addons: item.addons_json || []
                    }
                }));
            };

            window.triggerSearchAdd = function(index) {
                const item = allItems[index];
                if (item.has_variants) {
                    window.triggerSearchCardClick(index);
                } else {
                    Alpine.store('cart').add({
                        id: item.id,
                        unique_key: item.id + '_0_0_none',
                        name: item.name,
                        price: item.display_price,
                        variant_name: '',
                        addons: [],
                        notes: '',
                        quantity: 1
                    });
                }
            };

            const renderSearchResults = (items, term) => {
                const keyword = term.trim();
                if (!keyword) {
                    defaultMenuView.classList.remove('hidden');
                    searchResultsView.classList.add('hidden');
                    searchResultsGrid.innerHTML = '';
                    searchNoResults.classList.add('hidden');
                    return;
                }

                defaultMenuView.classList.add('hidden');
                searchResultsView.classList.remove('hidden');
                searchTitle.textContent = `Search Results for "${keyword}"`;
                searchCount.textContent = `(${items.length})`;

                if (!items.length) {
                    searchResultsGrid.innerHTML = '';
                    searchNoResults.classList.remove('hidden');
                    return;
                }

                searchNoResults.classList.add('hidden');

                searchResultsGrid.innerHTML = items.map((item) => {
                    const originalIndex = allItems.findIndex(i => i.id === item.id);
                    const description = item.description || 'Freshly prepared and served with care.';
                    const displayPrice = Number(item.display_price || 0);

                    return `
                    <div onclick="triggerSearchCardClick(${originalIndex})" class="menu-card relative group rounded-lg p-4 flex gap-3 sm:gap-4 border-2 border-orange-500/60 transition-all cursor-pointer">
                        ${item.is_recommended ? '<div class="ribbon uppercase">Bestseller</div>' : ''}
                        <div class="relative w-24 h-24 sm:w-28 sm:h-28 lg:w-32 lg:h-32 flex-shrink-0">
                            <img src="${escapeHtml(item.image)}" class="w-full h-full object-cover rounded-2xl" alt="${escapeHtml(item.name)}">
                        </div>
                        <div class="flex flex-col justify-between flex-1 py-1 min-w-0">
                            <div>
                                <h4 class="font-bold text-white text-sm sm:text-base lg:text-lg">${escapeHtml(item.name)}</h4>
                                <p class="text-[11px] text-gray-500 line-clamp-2 mt-1">${escapeHtml(description)}</p>
                            </div>
                            <div class="flex justify-between items-center mt-2" onclick="event.stopPropagation()">
                                <span class="text-lg font-bold text-orange-500">₹${displayPrice.toFixed(2)} ${item.has_variants ? '<span class="text-[9px] text-gray-400 lowercase">onwards</span>' : ''}</span>
                                <button type="button" onclick="triggerSearchAdd(${originalIndex})" class="bg-gray-800 border border-gray-700 text-white px-4 py-1.5 rounded-xl text-xs font-bold">
                                    Add ${item.has_variants ? '+' : ''}
                                </button>
                            </div>
                        </div>
                    </div>`;
                }).join('');
            };

            const runSearch = () => {
                const keyword = menuSearchInput.value.trim().toLowerCase();
                updateSearchButtonIcon();
                if (!keyword) {
                    renderSearchResults([], '');
                    return;
                }

                const matches = allItems.filter((item) => {
                    return [item.name, item.description, item.category_name].join(' ').toLowerCase()
                        .includes(keyword);
                });
                renderSearchResults(matches, menuSearchInput.value);
            };

            let searchDebounceTimer = null;
            menuSearchInput.addEventListener('input', () => {
                updateSearchButtonIcon();
                if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(runSearch, 120);
            });

            menuSearchBtn.addEventListener('click', () => {
                if (menuSearchInput.value.trim()) {
                    menuSearchInput.value = '';
                    updateSearchButtonIcon();
                    renderSearchResults([], '');
                    return;
                }
                runSearch();
            });
        })();

        function callWaiter(buttonEl) {
            const tableId = @json($resolvedTableId);
            const tableNumber = @json($tableNumber);
            if (!tableId && (!tableNumber || tableNumber === 'N/A')) {
                alert('Table info not found.');
                return;
            }
            if (buttonEl) buttonEl.disabled = true;

            fetch('/call-waiter', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        table_id: tableId,
                        table_number: tableNumber,
                        tenant_id: {{ (int) $tenant->id }}
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert('Waiter has been notified');
                })
                .finally(() => {
                    if (buttonEl) buttonEl.disabled = false;
                });
        }

        async function placeOrder(overallNotesValue = '') {
            @if ($isAdminPreview)
                const previewNotice = document.getElementById('previewOnlyNotice');
                if (previewNotice) {
                    previewNotice.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    previewNotice.classList.add('ring-2', 'ring-amber-300');
                    setTimeout(() => {
                        previewNotice.classList.remove('ring-2', 'ring-amber-300');
                    }, 2500);
                }
                return;
            @endif

            if (Alpine.store('cart').items.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            let geoState = null;
            if (window.tableGeofencePolicy.enabled) {
                try {
                    geoState = await window.ensureTableGeoLocation();
                } catch (error) {
                    alert(error?.message || 'Please allow location access to place this order.');
                    return;
                }

                if (!geoState || geoState.latitude === null || geoState.longitude === null) {
                    alert('Please allow location access to place this order.');
                    return;
                }
            }

            fetch('/place-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: Alpine.store('cart').items,
                        overall_instructions: overallNotesValue,
                        table_number: '{{ $tableNumber }}',
                        table_id: @json($resolvedTableId),
                        session_token: window.currentTableAccessSessionToken || '',
                        client_latitude: geoState?.latitude ?? null,
                        client_longitude: geoState?.longitude ?? null,
                        order_type: @json($resolvedTableId) ? 'dine_in' : 'self_order',
                        source: 'qr',
                        qr_token: '{{ request()->route('qr_token') ?? ($table->qr_token ?? '') }}'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // 1. Cart Clear Karo
                        Alpine.store('cart').items = [];
                        Alpine.store('cart').sync();

                        // 2. Direct Live Tracking Page par Seamless Jump Karo
                        if (data.redirect_url) {
                            const redirectUrl = new URL(data.redirect_url, window.location.origin);
                            redirectUrl.searchParams.set('placed', '1');
                            window.location.href = redirectUrl.toString();
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert(data.message || 'Something went wrong!');
                    }
                })
                .catch(err => {
                    console.error('Order Error:', err);
                    alert('Order submit failed. Please try again.');
                });
        }
    </script>
@endsection
