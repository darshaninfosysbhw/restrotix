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
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@section('content')
    @php
        $categoryList = $menuCategories ?? collect();
        $activeCategory = $selectedCategory ?? $categoryList->first();
        $activeItems = collect($activeCategory['items'] ?? []);
        $searchItems = $categoryList
            ->flatMap(function ($category) {
                return collect($category['items'] ?? [])->map(function ($item) use ($category) {
                    $displayPrice =
                        $item->sale_price && $item->sale_price > 0
                            ? (float) $item->sale_price
                            : (float) $item->base_price;

                    return [
                        'id' => (int) $item->id,
                        'name' => (string) $item->name,
                        'description' => (string) ($item->description ?? ''),
                        'type' => (string) ($item->type ?? 'non_veg'),
                        'base_price' => (float) $item->base_price,
                        'sale_price' => (float) ($item->sale_price ?? 0),
                        'display_price' => $displayPrice,
                        'is_recommended' => (bool) $item->is_recommended,
                        'image' => $item->image
                            ? asset('storage/' . $item->image)
                            : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=400',
                        'category_id' => (int) $category['id'],
                        'category_name' => (string) $category['name'],
                        'category_url' => request()->fullUrlWithQuery(['category' => $category['id']]),
                    ];
                });
            })
            ->values();
        $tenantName = $tenant->company_name ?? 'Menu';
        $tableLabel = $tableNumber && $tableNumber !== 'N/A' ? '#' . $tableNumber : 'N/A';
        $resolvedTableId = isset($tableId) && $tableId ? (int) $tableId : null;
    @endphp

    <div class="flex-1 h-screen lg:h-[calc(100vh-2rem)] flex overflow-hidden p-0 lg:p-4 ">

        <aside class="hidden lg:flex flex-col w-72 bg-gray-800 p-6 rounded-l-lg border border-gray-700">
            <h2 class="text-lg font-bold text-gray-500 uppercase tracking-[2px] mb-8">Categories</h2>
            <div class="space-y-2 overflow-y-auto no-scrollbar">
                @forelse ($categoryList as $category)
                    <a href="{{ request()->fullUrlWithQuery(['category' => $category['id']]) }}"
                        class="w-full flex items-center justify-between p-4 rounded-lg border transition {{ $activeCategory && $activeCategory['id'] === $category['id']
                            ? 'bg-orange-500/10 text-white font-bold border-orange-500/30'
                            : 'text-gray-400 hover:bg-white/5 border-gray-700' }}">
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
                class="sticky lg:sticky top-0 w-full z-20 flex justify-between items-center border-b border-gray-700 px-4 py-2.5 lg:px-6 lg:py-4 backdrop-blur-sm bg-gray-900/80 lg:bg-gray-900/10 lg:mb-0">
                <div class="flex items-center gap-3">
                    <button class="lg:hidden text-orange-500 text-xl hidden"><i class="fas fa-bars"></i></button>
                    <h1 class="text-[13px] lg:text-2xl font-black text-white uppercase tracking-tight">{{ $tenantName }}
                    </h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex item-center text-right gap-2">
                        <p class="text-[10px] text-gray-500 font-bold leading-none hidden lg:block pt-0.5">TABLE</p>
                        <span class="text-[10px] text-orange-500/80 font-black">{{ $tableLabel }}</span>
                    </div>
                    <button id="callWaiterBtn" type="button" onclick="callWaiter(this)"
                        class="bg-transparent border border-orange-500/40 text-orange-500 px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 hover:bg-orange-600 transition group">
                        <i class="fas fa-bell text-[10px] group-hover:animate-bounce"></i> Call Waiter
                    </button>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto no-scrollbar px-2.5 mt-2 lg:p-8 lg:space-y-6 lg:mt-0">
                <div class="swiper mySwiper w-full h-20 lg:h-48 overflow-hidden relative group px-2">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide relative">
                            <img src="{{ asset('images/menu-banner.png') }}" class="w-full h-full object-cover">
                        </div>
                        <div class="swiper-slide relative">
                            <img src="{{ asset('images/offer-banner.png') }}" class="w-full h-full object-cover">
                        </div>
                        <div class="swiper-slide relative">
                            <img src="{{ asset('images/menu-banner.png') }}" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <div class="absolute inset-y-0 left-2 z-20 flex items-center">
                        <button
                            class="swiper-prev-custom w-6 h-6 lg:w-8 lg:h-8 flex items-center justify-center rounded-full bg-black/40 text-white pointer-events-auto">
                            <i class="fas fa-chevron-left text-[10px]"></i>
                        </button>
                    </div>

                    <div class="absolute inset-y-0 right-2 z-20 flex items-center">
                        <button
                            class="swiper-next-custom w-6 h-6 lg:w-8 lg:h-8 flex items-center justify-center rounded-full bg-black/40 text-white pointer-events-auto">
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                </div>

                {{-- search --}}
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

                {{-- =========================== --}}

                <div class="sticky top-0 z-50 bg-gray-900 lg:hidden flex gap-2 overflow-x-auto no-scrollbar py-2">
                    @foreach ($categoryList as $category)
                        <a href="{{ request()->fullUrlWithQuery(['category' => $category['id']]) }}"
                            class="flex-shrink-0 px-5 py-2.5 rounded-full text-xs uppercase {{ $activeCategory && $activeCategory['id'] === $category['id']
                                ? 'bg-orange-600 font-black'
                                : 'bg-gray-800 border border-gray-700 text-gray-400 font-bold' }}">
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

                                        $displayPrice =
                                            $item->sale_price && $item->sale_price > 0
                                                ? (float) $item->sale_price
                                                : (float) $item->base_price;
                                        $basePrice = (float) $item->base_price;
                                        $image = $item->image
                                            ? asset('storage/' . $item->image)
                                            : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=400';
                                    @endphp

                                    <div
                                        class="relative group rounded-lg p-4 flex gap-3 sm:gap-4 border-2 border-orange-500/60 transition-all hover:scale-[1.01]">
                                        @if ($item->is_recommended)
                                            <div class="ribbon uppercase">Bestseller</div>
                                        @endif

                                        <div class="relative w-24 h-24 sm:w-28 sm:h-28 lg:w-32 lg:h-32 flex-shrink-0">
                                            <img src="{{ $image }}" class="w-full h-full object-cover rounded-2xl"
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
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span
                                                        class="text-lg sm:text-xl font-black text-orange-500">₹{{ number_format($displayPrice, 2) }}</span>
                                                    @if ($item->sale_price && $displayPrice < $basePrice)
                                                        <span
                                                            class="text-xs text-gray-500 line-through">₹{{ number_format($basePrice, 2) }}</span>
                                                    @endif
                                                </div>


                                                <div x-data>
                                                    <!-- ADD BUTTON -->
                                                    <template x-if="!$store.cart.getItem({{ $item->id }})">
                                                        <button
                                                            @click="$store.cart.add({
                                                                id: {{ $item->id }},
                                                                name: '{{ $item->name }}',
                                                                price: {{ $displayPrice }}
                                                            })"
                                                            class="bg-gray-800 border border-gray-700 text-white px-4 py-2 rounded-xl text-xs font-bold">
                                                            Add
                                                        </button>
                                                    </template>

                                                    <!-- QUANTITY SELECTOR -->
                                                    <template x-if="$store.cart.getItem({{ $item->id }})">
                                                        <div
                                                            class="flex items-center gap-2 bg-gray-800 rounded-xl px-3 py-1">
                                                            <button
                                                                @click="$store.cart.decrement({{ $item->id }})">-</button>

                                                            <span
                                                                x-text="$store.cart.getItem({{ $item->id }}).quantity"></span>

                                                            <button
                                                                @click="$store.cart.increment({{ $item->id }})">+</button>
                                                        </div>
                                                    </template>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-gray-700 p-8 text-center text-gray-400">
                                Sorry, items not found in this category.
                            </div>
                        @endif
                    @else
                        <div class="rounded-xl border border-dashed border-gray-700 p-8 text-center text-gray-400">
                            No menu items available for preview yet.
                        </div>
                    @endif
                </div>

                <div id="searchResultsView" class="hidden pb-28">
                    <div class="flex items-center justify-between gap-2 py-3">
                        <h3 id="searchTitle" class="font-medium text-lg text-orange-500/90 uppercase tracking-[0.5px]">
                            Search Results
                        </h3>
                        <span id="searchCount" class="text-gray-500 text-sm font-bold">(0)</span>
                    </div>
                    <div id="searchResultsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5"></div>
                    <div id="searchNoResults"
                        class="hidden rounded-xl border border-dashed border-gray-700 p-8 text-center text-gray-400 mt-2">
                        No matching items found.
                    </div>
                </div>
            </div>
            <div x-data>
                <footer
                    class="preview-footer fixed lg:sticky bottom-0 left-0 w-full z-40 border-t border-orange-300/30 bg-gradient-to-r from-[#f97316] to-[#ea580c] shadow-[0_-10px_30px_rgba(249,115,22,0.35)]"
                    x-show="$store.cart.items.length > 0" x-transition>
                    <div class="p-3 sm:px-4 sm:py-3">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <button type="button"
                                class="w-1/2 min-w-0 inline-flex items-center justify-between rounded-xl bg-black/20 hover:bg-black/30 border border-white/20 px-2.5 sm:px-4 py-2.5 transition">
                                <span class="inline-flex items-center gap-2 min-w-0 flex-1">
                                    <span
                                        class="relative flex-shrink-0 w-6 h-6 lg:w-8 lg:h-8 bg-orange-500 rounded-md flex items-center justify-center">
                                        <i class="fas fa-shopping-bag text-white text-sm sm:text-base"></i>
                                        <span x-text="$store.cart.totalItems()"
                                            class="absolute -top-1.5 -right-1.5 bg-white text-orange-600 text-[8px] font-black min-w-3 h-3 lg:min-w-4 lg:h-4 lg:px-1 lg:text-[10px] lg:-top-2 lg:-right-2 rounded-full flex items-center justify-center">0</span>
                                    </span>
                                    <span class="text-left leading-tight min-w-0">
                                        <span
                                            class="block text-[8px] sm:text-[9px] uppercase tracking-wider font-black text-white/80 whitespace-nowrap">View
                                            Cart</span>
                                        <span
                                            class="block text-[8px] sm:text-[13px] font-bold text-white whitespace-nowrap"
                                            x-text="$store.cart.totalItems() + ' Items'">0
                                            Items</span>
                                    </span>
                                </span>
                                <span class="h-7 w-px bg-white/25 mx-2 sm:mx-3 flex-shrink-0"></span>
                                <span
                                    class="inline-flex items-center gap-1 text-white font-black text-[12px] sm:text-[22px] leading-none flex-shrink-0"
                                    x-text="'₹' + $store.cart.totalPrice().toFixed(2)">
                                    <i class="fas fa-wallet text-white/80 text-[10px] sm:text-xs"></i>
                                    ₹0
                                </span>
                            </button>

                            <button type="button" @click="placeOrder()"
                                class="w-1/2 min-w-0 inline-flex items-center justify-center gap-1.5 sm:gap-2 rounded-xl bg-white text-orange-600 px-2.5 sm:px-5 py-2.5 font-black text-[11px] sm:text-xs uppercase tracking-wide sm:tracking-wider hover:bg-orange-50 transition shadow-lg shadow-black/20 whitespace-nowrap">
                                <i class="fas fa-utensils text-[10px] sm:text-xs"></i>
                                Place Order
                            </button>
                        </div>
                    </div>
                </footer>
            </div>
        </main>
    </div>

    <script>
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 3000
            },
            navigation: {
                nextEl: ".swiper-next-custom",
                prevEl: ".swiper-prev-custom",
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
            const allItems = @json($searchItems);

            const escapeHtml = (value) => {
                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            const getTypeColor = (type) => {
                if (type === 'veg') return 'text-green-500';
                if (type === 'egg') return 'text-yellow-400';
                return 'text-red-500';
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
                    const description = item.description || 'Freshly prepared and served with care.';
                    const typeColor = getTypeColor(item.type);
                    const displayPrice = Number(item.display_price || item.base_price || 0);
                    const basePrice = Number(item.base_price || 0);
                    const showBasePrice = Number(item.sale_price || 0) > 0 && displayPrice < basePrice;

                    return `
                    <div class="relative group rounded-lg p-4 flex gap-3 sm:gap-4 border-2 border-orange-500/60 transition-all hover:scale-[1.01]">
                        ${item.is_recommended ? '<div class="ribbon uppercase">Bestseller</div>' : ''}
                        <div class="relative w-24 h-24 sm:w-28 sm:h-28 lg:w-32 lg:h-32 flex-shrink-0">
                            <img src="${escapeHtml(item.image)}" class="w-full h-full object-cover rounded-2xl" alt="${escapeHtml(item.name)}">
                            <div class="absolute top-2 right-2 bg-black/40 backdrop-blur-md p-1 rounded-md ${typeColor}">
                                <div class="food-type-icon">
                                    <div class="food-type-dot"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col justify-between flex-1 py-1 min-w-0">
                            <div>
                                <h4 class="font-bold text-white text-sm sm:text-base lg:text-lg leading-tight break-words">${escapeHtml(item.name)}</h4>
                                <p class="text-[11px] text-gray-500 mt-1 leading-relaxed line-clamp-2 italic">${escapeHtml(description)}</p>
                                <p class="text-[10px] text-orange-400 mt-1 uppercase tracking-wide">
                                    <a href="${escapeHtml(item.category_url)}" class="hover:underline">${escapeHtml(item.category_name)}</a>
                                </p>
                            </div>
                            <div class="flex justify-between items-center mt-2 sm:mt-3 gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-lg sm:text-xl font-black text-orange-500">₹${displayPrice.toFixed(2)}</span>
                                    ${showBasePrice ? `<span class="text-xs text-gray-500 line-through">₹${basePrice.toFixed(2)}</span>` : ''}
                                </div>
                                <button class="bg-gray-800 border border-gray-700 text-white hover:border-orange-500 px-4 sm:px-6 py-2 rounded-xl text-[10px] sm:text-[11px] font-black uppercase transition active:scale-95 shadow-lg whitespace-nowrap">
                                    Add
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
                    const haystack = [
                        item.name,
                        item.description,
                        item.category_name,
                    ].join(' ').toLowerCase();

                    return haystack.includes(keyword);
                });

                renderSearchResults(matches, menuSearchInput.value);
            };

            let searchDebounceTimer = null;

            menuSearchInput.addEventListener('input', () => {
                updateSearchButtonIcon();
                if (searchDebounceTimer) {
                    clearTimeout(searchDebounceTimer);
                }

                searchDebounceTimer = setTimeout(runSearch, 120);
            });

            menuSearchInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    runSearch();
                }
            });

            menuSearchBtn.addEventListener('click', () => {
                if (menuSearchInput.value.trim()) {
                    menuSearchInput.value = '';
                    updateSearchButtonIcon();
                    renderSearchResults([], '');
                    menuSearchInput.focus();
                    return;
                }

                runSearch();
            });

            updateSearchButtonIcon();
        })();
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                items: JSON.parse(localStorage.getItem('cart')) || [],

                add(item) {
                    let existing = this.items.find(i => i.id === item.id);

                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.items.push({
                            ...item,
                            quantity: 1
                        });
                    }

                    this.sync();
                },

                increment(id) {
                    let item = this.items.find(i => i.id === id);
                    if (item) {
                        item.quantity++;
                        this.sync();
                    }
                },

                decrement(id) {
                    let index = this.items.findIndex(i => i.id === id);

                    if (index === -1) return;

                    if (this.items[index].quantity > 1) {
                        this.items[index].quantity--;
                    } else {
                        this.items.splice(index, 1); // ✅ IMPORTANT (no filter)
                    }

                    this.sync();
                },

                getItem(id) {
                    return this.items.find(i => i.id === id);
                },

                totalItems() {
                    return this.items.reduce((sum, i) => sum + i.quantity, 0);
                },

                totalPrice() {
                    return this.items.reduce((sum, i) => sum + (i.price * i.quantity), 0);
                },

                sync() {
                    localStorage.setItem('cart', JSON.stringify(this.items));
                }
            });

            // 🔥 auto sync with localStorage
            Alpine.effect(() => {
                localStorage.setItem('cart', JSON.stringify(Alpine.store('cart').items));
            });
        });
    </script>

    <script>
        function callWaiter(buttonEl) {
            const tableId = @json($resolvedTableId);
            const tableNumber = @json($tableNumber);

            if (!tableId && (!tableNumber || tableNumber === 'N/A')) {
                alert('Table info not found. Please scan table QR again.');
                return;
            }

            if (buttonEl) {
                buttonEl.disabled = true;
                buttonEl.classList.add('opacity-60', 'cursor-not-allowed');
            }

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
                .then(async (res) => {
                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        throw new Error(data.message || 'Failed to call waiter');
                    }
                    alert('Waiter has been notified');
                })
                .catch((err) => {
                    alert(err.message || 'Unable to call waiter');
                })
                .finally(() => {
                    if (buttonEl) {
                        buttonEl.disabled = false;
                        buttonEl.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                });
        }

        function placeOrder() {
            fetch('/place-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: Alpine.store('cart').items,
                        table_number: '{{ $tableNumber }}',
                        table_id: @json($resolvedTableId),
                        order_type: @json($resolvedTableId) ? 'dine_in' : 'self_order',
                        source: 'qr'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Order placed successfully');

                        // clear cart
                        Alpine.store('cart').items = [];
                    }
                });
        }
    </script>
@endsection
