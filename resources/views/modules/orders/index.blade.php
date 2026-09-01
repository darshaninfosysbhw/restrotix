@extends($layout)
@section('content')
    <script>
        window.storeTaxSetting = "{{ $branch->tax_setting ?? 'exclusive' }}";
        window.storeTaxRate = parseFloat("{{ $branch->tax_rate ?? 5.0 }}") / 100;
        window.storeTaxLabelName = parseFloat("{{ $branch->tax_rate ?? 5.0 }}") === 13.00 ? "VAT" : "Tax";
    </script>
    <div class="flex-1 overflow-hidden bg-gray-900" x-data="{
        customizeOpen: false,
        customItem: { id: '', name: '', variants: [], addons: [] },
        selectedVariant: null,
        selectedAddons: [],
        itemNotes: '',
        modalQty: 1,
        editingCartIndex: null,
        openCustomizeSheet(itemData, isEditIndex = null) {
            this.customItem = itemData;
            this.selectedVariant = itemData.variants && itemData.variants.length ? itemData.variants[0] : null;
            this.selectedAddons = [];
            this.itemNotes = '';
            this.modalQty = 1;
            this.editingCartIndex = isEditIndex;
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
        submitModalConfiguration() {
            let itemBasePrice = (this.selectedVariant ? this.selectedVariant.price : this.customItem.display_price);
            let addonIds = this.selectedAddons.map(a => a.id + '-' + a.quantity).sort().join('-');
            let variantId = this.selectedVariant ? this.selectedVariant.id : '0';
            let serializedNotes = this.itemNotes.trim().toLowerCase().replace(/[^a-z0-9]/g, '');
            let uniqueKey = this.customItem.id + '_' + variantId + '_' + (addonIds || '0') + '_' + (serializedNotes || 'none');
            let configPayload = {
                id: this.customItem.id,
                unique_key: uniqueKey,
                name: this.customItem.name,
                price: itemBasePrice,
                variant_name: this.selectedVariant ? this.selectedVariant.name : '',
                addons: JSON.parse(JSON.stringify(this.selectedAddons)),
                qty: this.modalQty,
                note: this.itemNotes.trim()
            };
            window.addConfiguredItemToCart(configPayload, this.editingCartIndex);
            this.customizeOpen = false;
        }
    }">

        <div class="hidden md:flex h-full flex-col p-2 lg:p-2 gap-3">
            <div class="grid flex-1 min-h-0 grid-cols-12 gap-3">
                <section
                    class="col-span-12 md:col-span-7 xl:col-span-8 min-h-0 h-full rounded-2xl border border-gray-700 bg-gray-800 p-4 shadow-sm flex flex-col">
                    <div class="mb-3 border-b border-gray-700 pb-3 space-y-3">
                        <div class="grid gap-3 md:grid-cols-[auto_minmax(0,1fr)_auto] md:items-center">
                            <div class="flex items-center gap-3">
                                <button id="backToTablesBtn" type="button"
                                    class="inline-flex items-center gap-2 rounded-xl border border-gray-600 bg-gray-700 px-3 py-1 text-sm text-gray-200 hover:bg-gray-700/80 cursor-pointer whitespace-nowrap">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                    Back to Tables
                                </button>
                            </div>

                            <div class="flex flex-col items-center gap-1 text-center">
                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    <h2 id="desktopTableHeading" class="text-lg font-bold text-white">
                                        Table {{ $selectedTableNumber ?? request()->query('table', '5') }}
                                    </h2>
                                    <span
                                        class="rounded-md border px-2 py-1 text-xs font-semibold {{ $tableStatusBadgeClass ?? 'border-gray-500/60 bg-gray-500/15 text-gray-300' }}">{{ $tableStatusLabel ?? 'Inactive' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-start md:justify-end">
                                <button id="switchTableBtn" type="button"
                                    class="inline-flex items-center gap-2 rounded-xl border border-orange-500/40 bg-orange-500/10 px-3 py-1 text-sm font-medium text-orange-300 hover:bg-orange-500/15 cursor-pointer whitespace-nowrap">
                                    <i class="fas fa-exchange-alt text-xs"></i>
                                    Switch Table
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-2 text-sm text-gray-400">
                                <span>{{ $selectedTableCapacityLabel ?? 'Seater' }}</span>
                                <span class="text-gray-500">|</span>
                                <span>Dine In</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                <div
                                    class="inline-flex items-center gap-1 rounded-xl bg-gray-700 p-2 text-center min-w-[80px]">
                                    <p class="text-[11px] text-gray-400">Orders :</p>
                                    <p class="text-[11px] font-bold text-blue-400">
                                        {{ number_format((int) ($tableOrdersCount ?? 0)) }}
                                    </p>
                                </div>

                                @include('modules.orders.partials.session-timer', [
                                    'sessionOrderId' => $sessionOrderId,
                                    'sessionStartedAt' => $sessionStartedAt,
                                    'sessionEndedAt' => $sessionEndedAt,
                                    'selectedTableId' => $selectedTableId,
                                    'selectedTableNumber' => $selectedTableNumber,
                                ])
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 flex gap-2">
                        <div class="relative flex-1">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input id="menuSearchInput" type="text" placeholder="Search items..."
                                class="h-10 w-full rounded-lg border border-gray-600 bg-gray-700 pl-10 pr-3 text-sm text-white placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <button
                            class="h-10 rounded-lg border border-gray-600 bg-gray-700 px-4 text-sm font-medium text-gray-200 hover:bg-gray-700/80 cursor-pointer">
                            <i class="fas fa-sliders-h mr-1.5"></i> Filters
                        </button>
                    </div>

                    <div id="desktopCategoryTabs" class="no-scrollbar mb-3 flex gap-2 overflow-x-auto pb-1 ">
                        @foreach ($dynamicCategories ?? [] as $idx => $category)
                            <button type="button" data-category-name="{{ $category }}"
                                class="whitespace-nowrap rounded-lg px-4 py-1.5 cursor-pointer text-sm font-medium  {{ $idx === 0 ? 'bg-orange-500 text-white' : 'bg-gray-700 border border-gray-600 text-gray-300 hover:text-white' }}">
                                {{ $category }}
                            </button>
                        @endforeach
                    </div>

                    <div class="no-scrollbar flex-1 min-h-0 overflow-y-auto pr-1" id="desktopMenuGridWrapper">
                        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                            @foreach ($dynamicMenuItems ?? [] as $menuIndex => $item)
                                @php
                                    $variants = $item['variants'] ?? ($item['variants_json'] ?? []);
                                    $hasVariants = !empty($variants) && count($variants) > 0;
                                    $displayPrice = $hasVariants
                                        ? collect($variants)->min('price')
                                        : $item['price'] ?? 0;
                                @endphp
                                <article
                                    class="rounded-xl border border-gray-700 bg-gray-800 overflow-hidden cursor-pointer"
                                    data-menu-card-index="{{ $menuIndex }}">
                                    <img src="{{ $item['image'] ?? asset('images/default-food.png') }}"
                                        alt="{{ $item['name'] ?? 'Item' }}" class="h-20 w-full object-cover">
                                    <div class="p-2.5">
                                        <h3 class="truncate text-sm font-semibold text-white">{{ $item['name'] ?? '' }}
                                        </h3>
                                        <p class="mt-0.5 text-[12px] font-medium text-gray-200">Rs
                                            {{ number_format((float) $displayPrice) }} @if ($hasVariants)
                                                <span class="text-[9px] text-gray-500 lowercase font-light">onwards</span>
                                            @endif
                                        </p>
                                        <button data-add-menu-item="1" data-menu-index="{{ $menuIndex }}"
                                            data-item-name="{{ $item['name'] ?? '' }}"
                                            data-item-price="{{ $displayPrice }}"
                                            data-item-category="{{ $item['category'] ?? 'All Items' }}"
                                            data-variants='@json($variants)'
                                            data-addons='@json($item['addons'] ?? ($item['addons_json'] ?? []))'
                                            class="mt-1.5 w-full rounded-lg border border-gray-600 bg-gray-700 px-2.5 py-1.5 text-sm text-gray-200 hover:bg-gray-700/80 flex items-center justify-between cursor-pointer">
                                            <span><i class="fas fa-plus text-[10px] mr-1"></i> Add</span>
                                            <i class="fas fa-plus text-[10px] text-gray-400"></i>
                                        </button>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-2 grid grid-cols-2 xl:grid-cols-3 gap-2 border-t border-gray-700 pt-2">
                        <x-core::ui.coming-soon
                            label="Show Orders"
                            feature="Show Orders"
                            tone="blue"
                            class="rounded-xl border border-blue-500/40 bg-blue-500/10 px-3 py-1.5 text-sm text-blue-300 cursor-pointer"
                        />
                        <x-core::ui.coming-soon
                            label="Custom Item"
                            feature="Custom Item"
                            tone="indigo"
                            class="rounded-xl border border-indigo-500/40 bg-indigo-500/10 px-3 py-1.5 text-sm text-indigo-300 cursor-pointer"
                        />
                        {{-- <button
                            class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-1.5 text-sm text-rose-300 cursor-pointer">Discount</button> --}}
                        <button id="clearCartQuickBtn"
                            class="rounded-xl border border-orange-500/40 bg-orange-500/10 px-3 py-1.5 text-sm text-orange-300 cursor-pointer">Clear
                            Cart</button>
                    </div>
                </section>

                <aside
                    class="col-span-12 md:col-span-5 xl:col-span-4 min-h-0 h-full rounded-2xl border border-gray-700 bg-gray-800 shadow-sm flex flex-col">
                    <div class="px-4 py-3 border-b border-gray-700 flex items-center justify-between">
                        <h3 id="desktopCartHeading" class="text-lg font-bold text-white">Cart (0 Items)</h3>
                        <button id="clearCartBtn"
                            class="rounded-lg bg-red-500/10 border border-red-500/30 px-3 py-1.5 text-sm text-red-300 cursor-pointer">Clear</button>
                    </div>

                    <div id="desktopCartList" class="no-scrollbar flex-1 min-h-0 overflow-y-auto">
                        <div class="flex h-full min-h-[220px] items-center justify-center px-4 py-8">
                            <div class="text-center">
                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-orange-500/15 text-orange-400">
                                    <i class="fas fa-cart-plus text-xl"></i>
                                </div>
                                <h4 class="mt-4 text-base font-semibold text-white">No items selected</h4>
                                <p class="mt-1 text-sm text-gray-400">Use Add buttons to build order.</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-3 border-t border-gray-700">
                        <div class="relative mt-2">
                            <label for="orderNotes"
                                class="absolute -top-2.5 left-3 bg-gray-800 px-2 text-xs font-semibold text-orange-400">
                                Special Note
                            </label>

                            <textarea id="orderNotes" rows="2"
                                class="w-full rounded-xl border border-gray-600 bg-transparent p-3 text-sm text-white placeholder:text-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                                placeholder="Customer wants less spicy food."></textarea>
                        </div>
                    </div>

                    <div class="px-4 py-2 border-t border-gray-700">
                        <div class="space-y-1 text-sm text-gray-300">
                            <div class="flex justify-between"><span>Subtotal</span><span id="desktopSubtotal"
                                    class="font-semibold text-white">Rs 0</span></div>

                            <div class="flex justify-between" id="desktopTaxRow">
                                <span id="desktopTaxLabel">Tax (13%)</span>
                                <span class="font-semibold text-white" id="desktopTax">Rs 0</span>
                            </div>
                        </div>
                        <div class="mt-2 border-t border-gray-700 pt-2 flex items-end justify-between">
                            <p class="text-lg font-bold text-white">Total</p>
                            <p id="desktopTotal" class="text-lg font-bold text-white">Rs 0</p>
                        </div>

                        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <button id="printSendKitchenDesktopBtn" type="button"
                                class="rounded-xl border border-orange-500/40 bg-orange-500/10 py-2.5 text-sm font-bold text-orange-300 hover:bg-orange-500/15 cursor-pointer">
                                <i class="fas fa-print mr-1.5"></i> Confirm & Print
                            </button>
                            <button id="sendKitchenDesktopBtn" type="button"
                                class="rounded-xl bg-orange-500 py-2.5 text-sm font-bold text-white hover:bg-orange-600 cursor-pointer">
                                <i class="far fa-paper-plane mr-1.5"></i>Confirm Order
                            </button>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <div class="md:hidden relative h-full bg-gray-900 text-gray-200 overflow-hidden">
            <div class="h-full overflow-y-auto px-3 pb-24 pt-3">
                <div class="rounded-2xl border border-gray-700 bg-gray-800 p-3 mb-3 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <button id="backToTablesMobileBtn" type="button"
                            class="inline-flex items-center gap-2 rounded-xl border border-gray-600 bg-gray-700 px-3 py-1.5 text-xs text-gray-200 hover:bg-gray-700/80 cursor-pointer whitespace-nowrap">
                            <i class="fas fa-chevron-left text-[10px]"></i>
                            Back to Tables
                        </button>

                        <button id="mobileSwitchTableBtn" type="button"
                            class="inline-flex items-center gap-1 rounded-lg border border-orange-500/40 bg-orange-500/10 px-3 py-1.5 text-xs font-medium text-orange-300 whitespace-nowrap">
                            <i class="fas fa-exchange-alt text-[10px]"></i>
                            Switch Table
                        </button>
                    </div>

                    <div class="flex flex-col items-center gap-1 text-center">
                        <h2 id="mobileTableHeading" class="text-base font-semibold text-white">
                            Table {{ $selectedTableNumber ?? request()->query('table', '5') }}
                        </h2>
                        <span
                            class="inline-flex items-center rounded-md border px-2 py-0.5 text-[8px] font-semibold {{ $tableStatusBadgeClass ?? 'border-gray-500/60 bg-gray-500/15 text-gray-300' }}">
                            {{ $tableStatusLabel ?? 'Inactive' }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <span>{{ $selectedTableCapacityLabel ?? 'Seater' }}</span>
                            <span class="text-gray-500">|</span>
                            <span>Dine In</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                            <div
                                class="inline-flex items-center gap-1 rounded-xl bg-gray-700 p-2 text-center min-w-[80px]">
                                <p class="text-[11px] text-gray-400">Orders :</p>
                                <p class="text-sm font-bold text-blue-400">
                                    {{ number_format((int) ($tableOrdersCount ?? 0)) }}
                                </p>
                            </div>

                            @include('modules.orders.partials.session-timer', [
                                'sessionOrderId' => $sessionOrderId,
                                'sessionStartedAt' => $sessionStartedAt,
                                'sessionEndedAt' => $sessionEndedAt,
                                'selectedTableId' => $selectedTableId,
                                'selectedTableNumber' => $selectedTableNumber,
                            ])
                        </div>
                    </div>

                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input id="mobileSearchInput" type="text" placeholder="Search"
                            class="h-10 w-full rounded-xl border border-gray-600 bg-gray-700 pl-9 pr-3 text-sm text-white placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>
                </div>

                <div id="mobileCategoryTabs" class="no-scrollbar mb-3 flex gap-2 overflow-x-auto py-2 px-2">
                    @foreach ($dynamicCategories ?? [] as $idx => $category)
                        <button data-mobile-category-name="{{ $category }}"
                            class="{{ $idx === 0
                                ? 'flex-shrink-0 px-5 py-2.5 rounded-full text-xs uppercase bg-orange-600 text-white font-black whitespace-nowrap'
                                : 'flex-shrink-0 px-5 py-2.5 rounded-full text-xs uppercase bg-gray-800 border border-gray-700 text-gray-400 font-bold whitespace-nowrap' }}">
                            {{ $category }}
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    @foreach ($dynamicMenuItems ?? [] as $mobileIndex => $item)
                        @php
                            $variants = $item['variants'] ?? ($item['variants_json'] ?? []);
                            $hasVariants = !empty($variants) && count($variants) > 0;
                            $displayPrice = $hasVariants ? collect($variants)->min('price') : $item['price'] ?? 0;
                        @endphp
                        <article class="rounded-xl border border-gray-700 bg-gray-800 overflow-hidden cursor-pointer"
                            data-menu-card-index="{{ $mobileIndex }}">
                            <img src="{{ $item['image'] ?? asset('images/default-food.png') }}"
                                alt="{{ $item['name'] ?? 'Item' }}" class="h-24 w-full object-cover">
                            <div class="p-2">
                                <h3 class="truncate text-sm font-semibold text-white">{{ $item['name'] ?? '' }}</h3>
                                <p class="text-xs text-gray-400 truncate">Ready to serve quickly</p>
                                <div class="mt-1.5 flex items-center justify-between">
                                    <p class="text-sm font-bold text-white">Rs
                                        {{ number_format((float) $displayPrice) }}</p>
                                    <button data-add-menu-item="1" data-menu-index="{{ $mobileIndex }}"
                                        data-item-name="{{ $item['name'] ?? '' }}" data-item-price="{{ $displayPrice }}"
                                        data-item-category="{{ $item['category'] ?? 'All Items' }}"
                                        data-variants='@json($variants)'
                                        data-addons='@json($item['addons'] ?? ($item['addons_json'] ?? []))'
                                        class="rounded-md border border-orange-500/60 px-2.5 py-1 text-xs text-orange-300">+
                                        Add</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <button id="openMobileCart"
                class="fixed bottom-3 left-3 right-3 z-30 rounded-xl bg-orange-500 px-4 py-3 text-white flex items-center justify-between shadow-lg shadow-orange-500/25">
                <span id="mobileCartBarText" class="text-sm font-semibold">0 Items | Rs 0</span>
                <i class="fas fa-shopping-cart"></i>
            </button>

            <div id="mobileCartSheet"
                class="mobile-sheet fixed inset-x-0 bottom-0 z-40 hidden rounded-t-3xl border border-gray-700 bg-gray-800 p-4">
                <div class="mx-auto mb-3 h-1.5 w-14 rounded-full bg-gray-600"></div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Cart Review</h3>
                    <button id="closeMobileCart" class="text-gray-400"><i class="fas fa-times"></i></button>
                </div>

                <div id="mobileCartItemsContainer" class="max-h-[45vh] overflow-y-auto space-y-2.5">
                    <div class="flex min-h-[180px] items-center justify-center px-2 py-4">
                        <div class="text-center">
                            <div
                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-orange-500/15 text-orange-400">
                                <i class="fas fa-cart-plus text-lg"></i>
                            </div>
                            <h4 class="mt-3 text-sm font-semibold text-white">No items selected</h4>
                            <p class="mt-1 text-xs text-gray-400">Tap Add to build your order.</p>
                        </div>
                    </div>
                </div>

                <button id="openMobileReview"
                    class="mt-3 w-full rounded-xl bg-orange-500 py-2.5 text-sm font-semibold text-white">Continue to
                    KOT</button>
            </div>

            <div id="mobileReviewSheet"
                class="mobile-sheet fixed inset-x-0 bottom-0 z-50 hidden rounded-t-3xl border border-gray-700 bg-gray-800 p-4">
                <div class="mx-auto mb-3 h-1.5 w-14 rounded-full bg-gray-600"></div>
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Cart Review and KOT Punch</h3>
                    <button id="closeMobileReview" class="text-gray-400"><i class="fas fa-times"></i></button>
                </div>

                <div id="mobileReviewItemsContainer" class="max-h-[40vh] overflow-y-auto space-y-2.5">
                    <div class="flex min-h-[180px] items-center justify-center px-2 py-4">
                        <div class="text-center">
                            <div
                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-orange-500/15 text-orange-400">
                                <i class="fas fa-cart-plus text-lg"></i>
                            </div>
                            <h4 class="mt-3 text-sm font-semibold text-white">No items selected</h4>
                            <p class="mt-1 text-xs text-gray-400">Tap Add to build your order.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-3 rounded-xl border border-gray-700 bg-gray-700/60 p-3 text-sm text-gray-300">
                    <div class="mb-1 flex justify-between"><span>Subtotal:</span><span id="mobileSubtotal">Rs 0</span>
                    </div>
                    <div class="mb-1 flex justify-between" id="mobileTaxRow">
                        <span id="mobileTaxLabel">Tax (5%):</span>
                        <span id="mobileTax">Rs 0</span>
                    </div>
                    <div class="flex justify-between font-bold text-white"><span>Total Amount:</span><span
                            id="mobileTotal">Rs 0</span>
                    </div>
                </div>

                <div class="mt-3">
                    <p class="text-sm font-semibold text-white mb-1.5">Special Note</p>
                    <textarea id="mobileOrderNotes" rows="3"
                        class="w-full rounded-xl border border-gray-600 bg-gray-700 p-2.5 text-sm text-white placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        placeholder="Customer wants less spicy food."></textarea>
                </div>

                <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <button id="sendKitchenMobileBtn" type="button"
                        class="rounded-xl bg-orange-500 py-2.5 text-sm font-semibold text-white">
                        Send to Kitchen (KOT)
                    </button>
                    <button id="printSendKitchenMobileBtn" type="button"
                        class="rounded-xl border border-orange-500/40 bg-orange-500/10 py-2.5 text-sm font-semibold text-orange-300">
                        <i class="fas fa-print mr-1.5"></i>Print & Send
                    </button>
                </div>

                <button id="backToMobileCart"
                    class="mt-2 w-full rounded-xl bg-gray-700 border border-gray-600 py-2.5 text-sm text-gray-200">Cancel</button>
            </div>
        </div>
    </div>

    <div id="customizeModal"
        class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 p-4">
        <div
            class="w-full max-w-lg bg-gray-800 border border-gray-700 rounded-t-2xl sm:rounded-2xl p-5 space-y-4 shadow-2xl max-h-[85vh] overflow-y-auto no-scrollbar text-left text-white">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3">
                <h3 id="customizeModalTitle" class="text-lg font-bold text-white">Customize Item</h3>
                <button id="closeCustomizeModal"
                    class="text-gray-400 hover:text-white p-1 text-sm bg-gray-700 px-3 py-1 rounded-xl">Close</button>
            </div>
            <div id="customizeModalBody" class="mt-3 text-sm text-gray-200 space-y-4">
                <div class="space-y-2">
                    <span id="variantLabelBlock"
                        class="text-xs font-bold text-orange-400 uppercase tracking-wide block">Select Portion Size</span>
                    <div id="variantOptions" class="grid grid-cols-1 gap-2"></div>
                </div>
                <div class="space-y-2">
                    <span id="addonLabelBlock"
                        class="text-xs font-bold text-orange-400 uppercase tracking-wide block">Add-ons / Extra
                        Toppings</span>
                    <div id="addonOptions" class="grid grid-cols-1 gap-2"></div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-700 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-1.5 bg-gray-900 px-2 py-1 rounded-xl border border-gray-700">
                        <button id="customQtyDec"
                            class="px-3 py-1 rounded bg-gray-700 text-white font-bold hover:bg-gray-600">-</button>
                        <span id="customQty" class="px-3 py-1 font-bold text-white">1</span>
                        <button id="customQtyInc"
                            class="px-3 py-1 rounded bg-gray-700 text-white font-bold hover:bg-gray-600">+</button>
                    </div>
                    <input id="customNote" placeholder="Note (optional)"
                        class="flex-1 min-w-[150px] p-2.5 rounded-xl bg-gray-900 border border-gray-700 text-white text-xs outline-none focus:border-orange-500/50" />
                </div>

                <div class="mt-4 pt-2 border-t border-gray-700 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 block font-medium uppercase">Total Price</span>
                        <span id="modalCalculatedPriceDisplay" class="text-base font-bold text-orange-400">Rs 0</span>
                    </div>
                    <button id="confirmAddCustomized"
                        class="bg-orange-500 hover:bg-orange-600 px-5 py-2.5 rounded-xl font-bold text-white text-xs uppercase tracking-wide shadow-md transition active:scale-95">Add
                        to Cart</button>
                </div>
            </div>
        </div>
    </div>

    @include('modules.orders.partials.table-switch-modal')

    <script>
        (function() {
            const openMobileCartBtn = document.getElementById('openMobileCart');
            const closeMobileCartBtn = document.getElementById('closeMobileCart');
            const openMobileReviewBtn = document.getElementById('openMobileReview');
            const closeMobileReviewBtn = document.getElementById('closeMobileReview');
            const backToMobileCartBtn = document.getElementById('backToMobileCart');
            const mobileCartSheet = document.getElementById('mobileCartSheet');
            const mobileReviewSheet = document.getElementById('mobileReviewSheet');

            if (!openMobileCartBtn || !mobileCartSheet || !mobileReviewSheet) return;

            const openSheet = (sheet) => sheet.classList.remove('hidden');
            const closeSheet = (sheet) => sheet.classList.add('hidden');

            openMobileCartBtn.addEventListener('click', () => {
                openSheet(mobileCartSheet);
                closeSheet(mobileReviewSheet);
            });

            if (closeMobileCartBtn) closeMobileCartBtn.addEventListener('click', () => closeSheet(mobileCartSheet));
            if (openMobileReviewBtn) openMobileReviewBtn.addEventListener('click', () => {
                closeSheet(mobileCartSheet);
                openSheet(mobileReviewSheet);
            });
            if (closeMobileReviewBtn) closeMobileReviewBtn.addEventListener('click', () => closeSheet(
                mobileReviewSheet));
            if (backToMobileCartBtn) backToMobileCartBtn.addEventListener('click', () => {
                closeSheet(mobileReviewSheet);
                openSheet(mobileCartSheet);
            });
        })();
    </script>

    <script>
        window.allItems = @json($dynamicMenuItems ?? []);

        (function() {
            const tableNumber = @json($selectedTableNumber ?? request()->query('table', '5'));
            const tableId = @json($selectedTableId ?? (request()->query('table_id') ?? 0));
            const csrfToken = '{{ csrf_token() }}';
            const storageKey = `waiter_manual_order_${tableId || tableNumber || 'default'}`;
            const categories = @json($dynamicCategories ?? []);
            const menuItems = window.allItems;

            const state = {
                cart: [],
                search: '',
                selectedCategory: categories[0] || 'All Items',
            };

            let currentCustomize = null;

            const byId = (id) => document.getElementById(id);
            const formatPrice = (val) => `Rs ${Number(val || 0).toLocaleString()}`;
            const normalizeCategory = (value) => String(value || '').trim().toLowerCase();
            const escapeHtml = (value) => String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g,
                '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            const renderEmptyCartState = (compact = false) => `
                <div class="flex h-full min-h-[220px] items-center justify-center px-4 py-8">
                    <div class="text-center">
                        <div class="mx-auto flex ${compact ? 'h-12 w-12' : 'h-14 w-14'} items-center justify-center rounded-full bg-orange-500/15 text-orange-400">
                            <i class="fas fa-cart-plus ${compact ? 'text-lg' : 'text-xl'}"></i>
                        </div>
                        <h4 class="mt-4 text-base font-semibold text-white">No items selected</h4>
                        <p class="mt-1 text-sm text-gray-400">Use Add buttons to build order.</p>
                    </div>
                </div>`;

            const backToTablesUrl = @json($backUrl);
            const kotPdfRouteTemplate = @json(route('admin.tables.kot.pdf', ['table_number' => '__TABLE__'], false));
            const tableSwitchModal = byId('tableSwitchModal');
            const openTableSwitchBtn = byId('switchTableBtn');
            const openMobileTableSwitchBtn = byId('mobileSwitchTableBtn');
            const closeTableSwitchBtn = byId('closeTableSwitchModal');
            const closeTableSwitchFooterBtn = byId('closeTableSwitchFooter');
            const tableSwitchBackdrop = byId('tableSwitchBackdrop');
            const tableSwitchSearch = byId('tableSwitchSearch');
            const tableSwitchEmptyState = byId('tableSwitchEmptyState');
            const tableSwitchFilterBtns = Array.from(document.querySelectorAll('[data-switch-filter]'));
            const tableSwitchCards = Array.from(document.querySelectorAll('[data-switch-table-card]'));
            const mobileCartSheet = byId('mobileCartSheet');
            const mobileReviewSheet = byId('mobileReviewSheet');
            const customizeModal = byId('customizeModal');

            let activeTableSwitchFilter = 'all';
            let kotPrintFrame = null;

            function openTableSwitchModal() {
                if (!tableSwitchModal) return;

                if (customizeModal) customizeModal.classList.add('hidden');
                if (mobileCartSheet) mobileCartSheet.classList.add('hidden');
                if (mobileReviewSheet) mobileReviewSheet.classList.add('hidden');

                activeTableSwitchFilter = 'all';
                if (tableSwitchSearch) tableSwitchSearch.value = '';
                tableSwitchModal.classList.remove('hidden');
                updateTableSwitchFilterButtons();
                applyTableSwitchFilters();

                window.setTimeout(() => tableSwitchSearch?.focus(), 0);
            }

            function closeTableSwitchModal() {
                if (!tableSwitchModal) return;
                tableSwitchModal.classList.add('hidden');
            }

            function updateTableSwitchFilterButtons() {
                tableSwitchFilterBtns.forEach((btn) => {
                    const isActive = String(btn.dataset.switchFilter || 'all') === activeTableSwitchFilter;
                    btn.classList.toggle('bg-orange-500/10', isActive);
                    btn.classList.toggle('text-orange-400', isActive);
                    btn.classList.toggle('border-orange-500/30', isActive);
                    btn.classList.toggle('bg-gray-700', !isActive);
                    btn.classList.toggle('text-gray-300', !isActive);
                    btn.classList.toggle('border-gray-600', !isActive);
                });
            }

            function applyTableSwitchFilters() {
                if (!tableSwitchCards.length) {
                    if (tableSwitchEmptyState) tableSwitchEmptyState.classList.add('hidden');
                    return;
                }

                const searchTerm = String(tableSwitchSearch?.value || '').trim().toLowerCase();
                let visibleCount = 0;

                tableSwitchCards.forEach((card) => {
                    const cardStatus = String(card.dataset.switchTableStatus || '').toLowerCase();
                    const cardText = String(card.dataset.switchTableSearch || '').toLowerCase();
                    const matchesFilter = activeTableSwitchFilter === 'all' || cardStatus ===
                        activeTableSwitchFilter;
                    const matchesSearch = !searchTerm || cardText.includes(searchTerm);
                    const isVisible = matchesFilter && matchesSearch;

                    card.classList.toggle('hidden', !isVisible);
                    if (isVisible) visibleCount++;
                });

                if (tableSwitchEmptyState) {
                    tableSwitchEmptyState.classList.toggle('hidden', visibleCount > 0);
                }
            }

            function switchToTable(tableIdValue, tableNumberValue) {
                const nextUrl = new URL(window.location.href);
                const nextTableId = String(tableIdValue || '').trim();
                const nextTableNumber = String(tableNumberValue || '').trim();

                if (nextTableId) {
                    nextUrl.searchParams.set('table_id', nextTableId);
                } else {
                    nextUrl.searchParams.delete('table_id');
                }

                if (nextTableNumber) {
                    nextUrl.searchParams.set('table', nextTableNumber);
                    nextUrl.searchParams.set('table_number', nextTableNumber);
                } else {
                    nextUrl.searchParams.delete('table');
                    nextUrl.searchParams.delete('table_number');
                }

                window.location.href = nextUrl.toString();
            }

            function getKotPdfUrl(tableNumberValue, orderIdValue = null, kotNumberValue = null) {
                const safeTableNumber = String(tableNumberValue ?? '').trim();
                if (!safeTableNumber) return '';

                const baseUrl = kotPdfRouteTemplate.replace('__TABLE__', encodeURIComponent(safeTableNumber));
                const params = new URLSearchParams({
                    print: '1'
                });
                const safeOrderId = String(orderIdValue ?? '').trim();
                const safeKotNumber = String(kotNumberValue ?? '').trim();

                if (safeOrderId) {
                    params.set('order_id', safeOrderId);
                }

                if (safeKotNumber) {
                    params.set('kot_number', safeKotNumber);
                }

                return `${baseUrl}?${params.toString()}`;
            }

            function printKotPdf(pdfUrl) {
                if (!pdfUrl) return false;

                try {
                    if (kotPrintFrame) {
                        kotPrintFrame.remove();
                        kotPrintFrame = null;
                    }

                    const frame = document.createElement('iframe');
                    frame.title = 'KOT Print';
                    frame.setAttribute('aria-hidden', 'true');
                    frame.className = 'fixed left-0 top-0 h-0 w-0 border-0 opacity-0 pointer-events-none';
                    frame.src = pdfUrl;
                    kotPrintFrame = frame;
                    document.body.appendChild(frame);

                    let handled = false;
                    const finishPrintFlow = () => {
                        if (handled) return;
                        handled = true;

                        if (kotPrintFrame === frame) {
                            kotPrintFrame.remove();
                            kotPrintFrame = null;
                        }

                        window.setTimeout(() => {
                            window.location.reload();
                        }, 150);
                    };

                    frame.addEventListener('load', () => {
                        window.setTimeout(() => {
                            try {
                                const frameWindow = frame.contentWindow;
                                if (!frameWindow) {
                                    throw new Error('Missing print window');
                                }

                                const afterPrintHandler = () => {
                                    finishPrintFlow();
                                };

                                try {
                                    frameWindow.onafterprint = afterPrintHandler;
                                } catch (afterPrintError) {
                                    console.warn('Unable to bind afterprint on KOT frame',
                                        afterPrintError);
                                }

                                frameWindow.addEventListener?.('afterprint', afterPrintHandler, {
                                    once: true
                                });
                                window.addEventListener('afterprint', afterPrintHandler, {
                                    once: true
                                });
                                frameWindow.focus();
                                frameWindow.print();
                            } catch (error) {
                                console.error('KOT PDF print failed', error);
                                finishPrintFlow();
                            }
                        }, 250);
                    }, {
                        once: true
                    });

                    return true;
                } catch (error) {
                    console.error('KOT PDF print failed', error);
                    return false;
                }
            }

            openTableSwitchBtn?.addEventListener('click', openTableSwitchModal);
            openMobileTableSwitchBtn?.addEventListener('click', openTableSwitchModal);
            closeTableSwitchBtn?.addEventListener('click', closeTableSwitchModal);
            closeTableSwitchFooterBtn?.addEventListener('click', closeTableSwitchModal);
            tableSwitchBackdrop?.addEventListener('click', closeTableSwitchModal);

            tableSwitchFilterBtns.forEach((btn) => {
                btn.addEventListener('click', () => {
                    activeTableSwitchFilter = String(btn.dataset.switchFilter || 'all');
                    updateTableSwitchFilterButtons();
                    applyTableSwitchFilters();
                });
            });

            tableSwitchSearch?.addEventListener('input', applyTableSwitchFilters);

            tableSwitchCards.forEach((card) => {
                card.addEventListener('click', () => {
                    if (String(card.dataset.switchTableDisabled || '0') === '1') return;
                    switchToTable(card.dataset.switchTableId, card.dataset.switchTableNumber);
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && tableSwitchModal && !tableSwitchModal.classList.contains(
                        'hidden')) {
                    closeTableSwitchModal();
                }
            });

            function loadCart() {
                try {
                    const parsed = JSON.parse(localStorage.getItem(storageKey) || '[]');
                    state.cart = Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    state.cart = [];
                }
            }

            function saveCart() {
                localStorage.setItem(storageKey, JSON.stringify(state.cart));
            }

            function syncUi() {
                saveCart();
                renderCartDesktop();
                renderCartMobile();
                updateCategoryActiveState();
                applySearchFilter();
                updateSummary();
            }

            function addItemByIndex(menuIndex) {
                const item = menuItems[Number(menuIndex)];
                if (!item) return;

                let variants = item.variants || item.variants_json || [];
                let addons = item.addons || item.addons_json || [];

                if ((variants && variants.length > 0) || (addons && addons.length > 0)) {
                    openCustomizeModal(menuIndex);
                    return;
                }

                let uniqueKey = item.id + '_0_0_none';
                let existing = state.cart.find(i => i.unique_key === uniqueKey);
                if (existing) {
                    existing.qty++;
                } else {
                    state.cart.push({
                        id: item.id,
                        unique_key: uniqueKey,
                        name: item.name,
                        price: parseFloat(item.price || 0),
                        qty: 1,
                        variant_name: '',
                        addons: [],
                        note: ''
                    });
                }
                syncUi();
            }

            function openCustomizeModal(menuIndex) {
                const item = menuItems[Number(menuIndex)];
                if (!item) return;

                currentCustomize = {
                    menuIndex: Number(menuIndex),
                    item: item,
                    variants: item.variants || item.variants_json || [],
                    addons: item.addons || item.addons_json || [],
                    selectedVariantIdx: 0,
                    selectedAddons: {},
                    qty: 1,
                    note: ''
                };

                renderCustomizeModal();
                byId('customizeModal').classList.remove('hidden');
            }

            function closeCustomizeModal() {
                if (byId('customizeModal')) byId('customizeModal').classList.add('hidden');
                currentCustomize = null;
            }

            function renderCustomizeModal() {
                if (!currentCustomize) return;
                const title = byId('customizeModalTitle');
                const variantHolder = byId('variantOptions');
                const addonHolder = byId('addonOptions');

                title.textContent = currentCustomize.item.name || 'Customize Item';

                if (currentCustomize.variants.length) {
                    byId('variantLabelBlock').style.display = 'block';
                    variantHolder.innerHTML = currentCustomize.variants.map((v, idx) => {
                        let variantPrice = parseFloat(v.price ?? v.sale_price ?? v.base_price ?? v
                            .display_price ?? 0);
                        let checked = idx === currentCustomize.selectedVariantIdx ? 'checked' : '';
                        let activeStyle = idx === currentCustomize.selectedVariantIdx ?
                            'border-orange-500 bg-orange-500/5' : 'border-gray-700 bg-gray-900/40';
                        return `
                        <label class="flex items-center justify-between p-2.5 rounded-xl border ${activeStyle} cursor-pointer text-xs transition" onclick="window.updateModalVariantSelection(${idx})">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="customVariant" value="${idx}" ${checked} class="text-orange-500 focus:ring-orange-500 h-3.5 w-3.5 bg-gray-700 border-gray-600" onchange="window.updateModalVariantSelection(${idx})" />
                                <span class="font-bold text-gray-200">${escapeHtml(v.name)}</span>
                            </div>
                            <span class="font-bold text-orange-400">Rs ${variantPrice.toFixed(2)}</span>
                        </label>`;
                    }).join('');
                } else {
                    byId('variantLabelBlock').style.display = 'none';
                    variantHolder.innerHTML = '';
                }

                if (currentCustomize.addons.length) {
                    byId('addonLabelBlock').style.display = 'block';
                    addonHolder.innerHTML = currentCustomize.addons.map((a) => {
                        let price = parseFloat(a.price || 0);
                        let currentQty = currentCustomize.selectedAddons[a.id] || 0;
                        let activeStyle = currentQty > 0 ? 'border-orange-500 bg-orange-500/5' :
                            'border-gray-700 bg-gray-900/40';

                        let controls = currentQty === 0 ?
                            `<button type="button" data-addon-add-id="${a.id}" class="border border-gray-600 text-orange-400 px-2.5 py-1 font-bold rounded-lg bg-gray-700 hover:bg-gray-600 transition">Add +</button>` :
                            `<div class="flex items-center gap-2 bg-gray-700 border border-orange-500 rounded-lg px-2 py-0.5 font-bold">
                                    <button type="button" data-addon-action="minus" data-aid="${a.id}" class="text-orange-400 font-bold text-sm px-1">-</button>
                                    <span class="w-3 text-center text-xs text-orange-400">${currentQty}</span>
                                    <button type="button" data-addon-action="plus" data-aid="${a.id}" class="text-orange-400 font-bold text-sm px-1">+</button>
                               </div>`;

                        return `
                        <div class="flex items-center justify-between p-2.5 rounded-xl border ${activeStyle} text-xs transition">
                            <div>
                                <span class="font-bold text-gray-200 block">${escapeHtml(a.name)}</span>
                                <span class="text-[10px] text-gray-400 font-medium">+ Rs ${price.toFixed(2)}</span>
                            </div>
                            <div>${controls}</div>
                        </div>`;
                    }).join('');
                } else {
                    byId('addonLabelBlock').style.display = 'none';
                    addonHolder.innerHTML = '';
                }

                byId('customQty').textContent = String(currentCustomize.qty || 1);
                byId('customNote').value = currentCustomize.note || '';
                evaluateLiveModalPrice();
            }

            window.updateModalVariantSelection = function(idx) {
                if (currentCustomize) {
                    currentCustomize.selectedVariantIdx = Number(idx);
                    const variantHolder = byId('variantOptions');
                    if (variantHolder) {
                        const labels = variantHolder.querySelectorAll('label');
                        labels.forEach((label, lidx) => {
                            const radioInput = label.querySelector('input[type="radio"]');
                            if (lidx === idx) {
                                label.className =
                                    "flex items-center justify-between p-2.5 rounded-xl border border-orange-500 bg-orange-500/5 cursor-pointer text-xs transition";
                                if (radioInput) radioInput.checked = true;
                            } else {
                                label.className =
                                    "flex items-center justify-between p-2.5 rounded-xl border border-gray-700 bg-gray-900/40 cursor-pointer text-xs transition";
                                if (radioInput) radioInput.checked = false;
                            }
                        });
                    }
                    evaluateLiveModalPrice();
                }
            };

            function evaluateLiveModalPrice() {
                if (!currentCustomize) return;
                let v = currentCustomize.variants[currentCustomize.selectedVariantIdx];
                let itemPrice = v ? parseFloat(v.price ?? v.sale_price ?? v.base_price ?? v.display_price ?? 0) :
                    parseFloat(currentCustomize.item.price || currentCustomize.item.display_price || 0);
                let addonsSum = currentCustomize.addons.reduce((sum, a) => sum + (parseFloat(a.price || 0) * (
                    currentCustomize.selectedAddons[a.id] || 0)), 0);

                let grandLiveTotal = (itemPrice * currentCustomize.qty) + addonsSum;
                byId('modalCalculatedPriceDisplay').textContent = formatPrice(grandLiveTotal);
            }

            function confirmAddCustomized() {
                if (!currentCustomize) return;
                const item = currentCustomize.item;
                const variant = currentCustomize.variants[currentCustomize.selectedVariantIdx] || null;
                const variantId = variant ? (variant.id || '0') : '0';

                const addonTokens = Object.keys(currentCustomize.selectedAddons).map(id => id + '-' + currentCustomize
                    .selectedAddons[id]).sort().join('-');
                const serializedNote = String(currentCustomize.note).trim().toLowerCase().replace(/[^a-z0-9]/g, '') ||
                    'none';

                const uniqueKey = `${item.id}_${variantId}_${addonTokens || '0'}_${serializedNote}`;
                const basePrice = variant ? parseFloat(variant.price ?? variant.sale_price ?? variant.base_price ??
                    variant.display_price ?? 0) : parseFloat(item.price || item.display_price || 0);

                let compiledAddonsList = [];
                currentCustomize.addons.forEach(a => {
                    let count = currentCustomize.selectedAddons[a.id] || 0;
                    if (count > 0) {
                        compiledAddonsList.push({
                            id: a.id,
                            name: a.name,
                            price: parseFloat(a.price || 0),
                            quantity: count
                        });
                    }
                });

                state.cart.push({
                    id: item.id,
                    unique_key: uniqueKey,
                    name: item.name,
                    price: basePrice,
                    variant_name: variant ? variant.name : '',
                    addons: compiledAddonsList,
                    note: currentCustomize.note.trim(),
                    qty: Number(currentCustomize.qty || 1)
                });

                syncUi();
                closeCustomizeModal();
            }

            window.triggerCartRowEdit = function(index) {
                const cartItem = state.cart[Number(index)];
                if (!cartItem) return;

                const masterItem = menuItems.find(i => i.id === cartItem.id);
                if (!masterItem) return;

                currentCustomize = {
                    menuIndex: menuItems.findIndex(i => i.id === cartItem.id),
                    item: masterItem,
                    variants: masterItem.variants || masterItem.variants_json || [],
                    addons: masterItem.addons || masterItem.addons_json || [],
                    selectedVariantIdx: Math.max(0, (masterItem.variants || masterItem.variants_json || [])
                        .findIndex(v => v.name === cartItem.variant_name)),
                    selectedAddons: {},
                    qty: cartItem.qty || 1,
                    note: cartItem.note || ''
                };

                if (cartItem.addons) {
                    cartItem.addons.forEach(a => {
                        currentCustomize.selectedAddons[a.id] = a.quantity;
                    });
                }

                state.cart.splice(Number(index), 1);

                renderCustomizeModal();
                byId('customizeModal').classList.remove('hidden');
            };

            function updateQty(index, delta) {
                if (state.cart[index]) {
                    state.cart[index].qty += delta;
                    if (state.cart[index].qty <= 0) {
                        state.cart.splice(index, 1);
                    }
                    syncUi();
                }
            }

            function removeItem(index) {
                state.cart.splice(index, 1);
                syncUi();
            }

            function renderCartDesktop() {
                const holder = byId('desktopCartList');
                if (!holder) return;
                if (!state.cart.length) {
                    holder.innerHTML = renderEmptyCartState(false);
                    return;
                }

                holder.innerHTML = state.cart.map((item, index) => {
                    let addonsTotal = item.addons ? item.addons.reduce((sum, a) => sum + (a.price * a.quantity),
                        0) : 0;
                    let calculatedRowPrice = (item.price * item.qty) + addonsTotal;

                    let variantTextStr = item.variant_name ?
                        ` <span class="text-orange-500 font-bold text-xs">[${item.variant_name}]</span>` : '';
                    let addonsTextStr = '';
                    if (item.addons && item.addons.length > 0) {
                        addonsTextStr = `<div class="text-[11px] text-orange-400/90 font-bold pl-1">Addons: ` +
                            item.addons.map(a => `${a.name}(x${a.quantity})`).join(', ') + `</div>`;
                    }

                    return `
                    <div class="px-4 py-2.5 border-b border-gray-700/60 flex items-start gap-2.5">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1">
                                <h4 class="truncate text-sm font-semibold text-white">${item.name}${variantTextStr}</h4>
                                ${(item.variant_name || (item.addons && item.addons.length > 0)) ? `<button type="button" onclick="window.triggerCartRowEdit(${index})" class="text-orange-400 hover:text-orange-500 text-xs font-bold">Edit</button>` : ''}
                            </div>
                            ${addonsTextStr}
                            <input type="text" data-cart-index="${index}" value="${String(item.note || '').replace(/"/g, '&quot;')}"
                                placeholder="Note..." class="cart-note-input mt-1 h-7 w-full rounded-md border border-gray-600 bg-transparent px-2 text-xs text-white focus:outline-none focus:border-orange-500/50" />
                        </div>
                        <div class="text-right flex flex-col items-end flex-shrink-0 min-w-[70px]">
                            <p class="text-sm font-semibold text-white mt-0.5">${formatPrice(calculatedRowPrice)}</p>
                            <div class="mt-1 inline-flex items-center rounded-md border border-gray-600 bg-gray-700 px-1 py-0.5">
                                <button data-action="dec" data-index="${index}" class="h-6 w-6 text-gray-300 font-bold">-</button>
                                <span class="w-5 text-center text-xs font-semibold text-white">${item.qty}</span>
                                <button data-action="inc" data-index="${index}" class="h-6 w-6 text-gray-300 font-bold">+</button>
                            </div>
                        </div>
                        <button data-action="remove" data-index="${index}" class="ml-1 text-rose-500 hover:text-rose-400 p-1 mt-5">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>`;
                }).join('');
            }

            function renderCartMobile() {
                const cartHolder = byId('mobileCartItemsContainer');
                const reviewHolder = byId('mobileReviewItemsContainer');
                if (!cartHolder || !reviewHolder) return;

                if (!state.cart.length) {
                    cartHolder.innerHTML = renderEmptyCartState(true);
                    reviewHolder.innerHTML = renderEmptyCartState(true);
                    return;
                }

                cartHolder.innerHTML = state.cart.map((item, index) => {
                    let addonsTotal = item.addons ? item.addons.reduce((sum, a) => sum + (a.price * a.quantity),
                        0) : 0;
                    let calculatedRowPrice = (item.price * item.qty) + addonsTotal;
                    let variantTextStr = item.variant_name ? ` [${item.variant_name}]` : '';

                    return `
                    <div class="rounded-xl border border-gray-700 bg-gray-700/50 p-2.5 mb-2">
                        <div class="flex items-center gap-2.5">
                            <div class="flex-1 min-w-0 text-left">
                                <p class="text-sm font-semibold text-white truncate">${item.qty}x ${item.name}${variantTextStr}</p>
                                <p class="text-sm font-bold text-gray-200">${formatPrice(calculatedRowPrice)}</p>
                            </div>
                            <div class="flex gap-1 flex-shrink-0">
                                ${(item.variant_name || (item.addons && item.addons.length > 0)) ? `<button type="button" onclick="window.triggerCartRowEdit(${index})" class="border border-orange-500/40 text-orange-400 text-xs px-2 py-1 rounded-lg mr-1 bg-transparent font-bold">Edit</button>` : ''}
                                <button data-action="inc" data-index="${index}" class="h-8 w-8 rounded-full border border-orange-500 text-orange-300">+</button>
                                <button data-action="dec" data-index="${index}" class="h-8 w-8 rounded-full border border-orange-500 text-orange-300">-</button>
                                <button data-action="remove" data-index="${index}" class="text-rose-500 text-lg h-8 w-8 flex items-center justify-center"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                        <input type="text" data-cart-index="${index}" value="${String(item.note || '').replace(/"/g, '&quot;')}"
                            placeholder="Instructions..." class="cart-note-input mt-2 h-8 w-full rounded-md border border-orange-500/50 bg-transparent px-2 text-xs text-white focus:outline-none" />
                    </div>`;
                }).join('');

                reviewHolder.innerHTML = state.cart.map(item => {
                    let addonsTotal = item.addons ? item.addons.reduce((sum, a) => sum + (a.price * a.quantity),
                        0) : 0;
                    let calculatedRowPrice = (item.price * item.qty) + addonsTotal;
                    let metaLabel = item.variant_name ? ` (${item.variant_name})` : '';

                    let reviewAddonStr = '';
                    if (item.addons && item.addons.length > 0) {
                        reviewAddonStr =
                            `<span class="text-[11px] text-orange-400 block font-medium pl-2">+ Extra: ` + item
                            .addons.map(a => `${a.name}(x${a.quantity})`).join(', ') + `</span>`;
                    }

                    return `
                    <div class="border-b border-gray-700 py-2 text-xs">
                        <div class="flex justify-between">
                            <div class="min-w-0 flex-1 text-left">
                                <span class="text-sm text-white block truncate">${item.qty}x ${item.name}${metaLabel}</span>
                            </div>
                            <span class="text-sm font-semibold text-white ml-2 flex-shrink-0">${formatPrice(calculatedRowPrice)}</span>
                        </div>
                        ${reviewAddonStr}
                        ${item.note ? `<span class="text-[11px] text-emerald-400 italic block pl-2 mt-0.5">Note: ${item.note}</span>` : ''}
                    </div>`;
                }).join('');
            }

            function applySearchFilter() {
                const keyword = (state.search || '').trim().toLowerCase();
                const isSearching = keyword.length > 0;
                document.querySelectorAll('[data-add-menu-item="1"]').forEach(btn => {
                    const article = btn.closest('article');
                    if (!article) return;

                    const name = String(btn.dataset.itemName || '').toLowerCase();
                    const cat = String(btn.dataset.itemCategory || '');
                    const catMatch = isSearching ?
                        true :
                        normalizeCategory(state.selectedCategory) === normalizeCategory('All Items') ||
                        normalizeCategory(cat) === normalizeCategory(state.selectedCategory);
                    const searchMatch = isSearching ? name.includes(keyword) : true;

                    article.classList.toggle('hidden', !(catMatch && searchMatch));
                });
            }

            function updateCategoryActiveState() {
                document.querySelectorAll('#desktopCategoryTabs [data-category-name]').forEach((btn) => {
                    const isActive = normalizeCategory(btn.dataset.categoryName || '') === normalizeCategory(
                        state.selectedCategory);
                    btn.className = isActive ?
                        'whitespace-nowrap rounded-lg px-4 py-1.5 cursor-pointer text-sm font-medium bg-orange-500 text-white' :
                        'whitespace-nowrap rounded-lg px-4 py-1.5 cursor-pointer text-sm font-medium bg-gray-700 border border-gray-600 text-gray-300 hover:text-white';
                });

                document.querySelectorAll('#mobileCategoryTabs [data-mobile-category-name]').forEach((btn) => {
                    const isActive = normalizeCategory(btn.dataset.mobileCategoryName || '') ===
                        normalizeCategory(state.selectedCategory);
                    btn.className = isActive ?
                        'flex-shrink-0 px-5 py-2.5 rounded-full text-xs uppercase bg-orange-600 text-white font-black whitespace-nowrap cursor-pointer' :
                        'flex-shrink-0 px-5 py-2.5 rounded-full text-xs uppercase bg-gray-800 border border-gray-700 text-gray-400 font-bold whitespace-nowrap cursor-pointer';
                });
            }

            function updateSummary() {
                const count = state.cart.reduce((s, i) => s + i.qty, 0);

                const subtotal = state.cart.reduce((sum, i) => {
                    let addonsSum = i.addons ? i.addons.reduce((s, a) => s + (a.price * a.quantity), 0) : 0;
                    return sum + (i.qty * i.price) + addonsSum;
                }, 0);

                const taxRate = window.storeTaxRate; // 🌟 DYNAMIC TAX MULTIPLIER DETECTED
                let taxAmount = 0;
                let grandTotal = 0;

                if (window.storeTaxSetting === 'inclusive') {
                    taxAmount = subtotal - (subtotal / (1 + taxRate));
                    grandTotal = subtotal;

                    if (byId('desktopTaxRow')) byId('desktopTaxRow').style.display = 'none';
                    if (byId('mobileTaxRow')) byId('mobileTaxRow').style.display = 'none';
                } else {
                    taxAmount = subtotal * taxRate;
                    grandTotal = subtotal + taxAmount;

                    if (byId('desktopTaxRow')) byId('desktopTaxRow').style.display = 'flex';
                    if (byId('mobileTaxRow')) byId('mobileTaxRow').style.display = 'flex';

                    if (byId('desktopTax')) {
                        byId('desktopTaxLabel').textContent =
                            `${window.storeTaxLabelName} (${(taxRate * 100).toFixed(0)}%)`;
                        byId('desktopTax').textContent = formatPrice(taxAmount);
                    }
                    if (byId('mobileTax')) {
                        byId('mobileTaxLabel').textContent =
                            `${window.storeTaxLabelName} (${(taxRate * 100).toFixed(0)}%):`;
                        byId('mobileTax').textContent = formatPrice(taxAmount);
                    }
                }

                if (byId('desktopCartHeading')) {
                    byId('desktopCartHeading').textContent = `Cart (${count} Items)`;
                }
                if (byId('desktopSubtotal')) byId('desktopSubtotal').textContent = formatPrice(subtotal);
                if (byId('desktopTotal')) byId('desktopTotal').textContent = formatPrice(grandTotal);

                if (byId('mobileCartBarText')) byId('mobileCartBarText').textContent =
                    `${count} Items | ${formatPrice(grandTotal)}`;
                if (byId('mobileSubtotal')) byId('mobileSubtotal').textContent = formatPrice(subtotal);
                if (byId('mobileTotal')) byId('mobileTotal').textContent = formatPrice(grandTotal);

                const sendBtns = [byId('sendKitchenDesktopBtn'), byId('sendKitchenMobileBtn')];
                sendBtns.forEach(btn => {
                    if (btn) btn.disabled = count === 0;
                });
            }

            document.addEventListener('click', (e) => {
                const card = e.target.closest('[data-menu-card-index]');
                if (card && !e.target.closest('[data-add-menu-item="1"]')) {
                    return addItemByIndex(card.dataset.menuCardIndex);
                }

                const addBtn = e.target.closest('[data-add-menu-item="1"]');
                if (addBtn) return addItemByIndex(addBtn.dataset.menuIndex);

                const actionBtn = e.target.closest('[data-action]');
                if (actionBtn) {
                    const index = Number(actionBtn.dataset.index);
                    const action = actionBtn.dataset.action;
                    if (action === 'remove') removeItem(index);
                    else updateQty(index, action === 'inc' ? 1 : -1);
                    return;
                }

                const catBtn = e.target.closest('[data-category-name], [data-mobile-category-name]');
                if (catBtn) {
                    state.selectedCategory = String(catBtn.dataset.categoryName || catBtn.dataset
                        .mobileCategoryName).trim();
                    syncUi();
                    return;
                }

                if (!currentCustomize) return;
                const addonAddBtn = e.target.closest('[data-addon-add-id]');
                if (addonAddBtn) {
                    currentCustomize.selectedAddons[addonAddBtn.dataset.addonAddId] = 1;
                    renderCustomizeModal();
                    return;
                }
                const addonAction = e.target.closest('[data-addon-action]');
                if (addonAction) {
                    let aid = addonAction.dataset.aid;
                    if (addonAction.dataset.addonAction === 'plus') {
                        currentCustomize.selectedAddons[aid]++;
                    } else {
                        currentCustomize.selectedAddons[aid]--;
                        if (currentCustomize.selectedAddons[aid] <= 0) delete currentCustomize.selectedAddons[
                            aid];
                    }
                    renderCustomizeModal();
                    return;
                }
                if (e.target.id === 'customQtyDec') {
                    currentCustomize.qty = Math.max(1, currentCustomize.qty - 1);
                    byId('customQty').textContent = String(currentCustomize.qty);
                    evaluateLiveModalPrice();
                    return;
                }
                if (e.target.id === 'customQtyInc') {
                    currentCustomize.qty++;
                    byId('customQty').textContent = String(currentCustomize.qty);
                    evaluateLiveModalPrice();
                    return;
                }
                if (e.target.id === 'confirmAddCustomized') {
                    confirmAddCustomized();
                    return;
                }
                if (e.target.id === 'closeCustomizeModal') {
                    closeCustomizeModal();
                    return;
                }
            });

            document.addEventListener('input', (e) => {
                if (e.target.classList.contains('cart-note-input')) {
                    const index = Number(e.target.dataset.cartIndex);
                    if (state.cart[index]) {
                        state.cart[index].note = e.target.value;
                        saveCart();
                    }
                    return;
                }
                if (currentCustomize && e.target.id === 'customNote') {
                    currentCustomize.note = e.target.value;
                    return;
                }
                if (e.target.id === 'menuSearchInput' || e.target.id === 'mobileSearchInput') {
                    state.search = e.target.value;
                    applySearchFilter();
                }
            });

            async function placeOrder(options = {}) {
                const shouldPrintKot = Boolean(options.printKot);

                if (!state.cart.length) {
                    alert('Please add at least one item.');
                    return;
                }
                const desktopNote = (byId('orderNotes')?.value || '').trim();
                const mobileNote = (byId('mobileOrderNotes')?.value || '').trim();
                const specialNote = desktopNote || mobileNote || null;

                const payload = {
                    items: state.cart.map(i => ({
                        id: i.id,
                        unique_key: i.unique_key || (i.id + '_0_0_none'),
                        name: i.name,
                        price: Number(i.price || 0),
                        quantity: i.qty,
                        variant_name: i.variant_name || '',
                        notes: i.note || '',
                        addons: i.addons || []
                    })),
                    table_id: tableId,
                    table_number: tableNumber,
                    order_type: 'dine_in',
                    overall_instructions: specialNote,
                    source: 'waiter'
                };

                const res = await fetch('/place-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const orderId = data.order_id || null;
                    const kotNumber = data.kot_number || null;
                    state.cart = [];
                    syncUi();
                    if (byId('orderNotes')) byId('orderNotes').value = '';
                    if (byId('mobileOrderNotes')) byId('mobileOrderNotes').value = '';
                    if (shouldPrintKot) {
                        const kotPdfUrl = getKotPdfUrl(tableNumber, orderId, kotNumber);
                        if (kotPdfUrl) {
                            const started = printKotPdf(kotPdfUrl);
                            if (!started) {
                                alert('Order sent, but the KOT print could not open.');
                            }
                        }
                    } else {
                        alert('Order Placed!');
                        window.location.reload();
                    }
                } else {
                    alert(data.message || 'Unable to place order');
                }
            }

            byId('sendKitchenDesktopBtn')?.addEventListener('click', () => placeOrder({
                printKot: false
            }));
            byId('printSendKitchenDesktopBtn')?.addEventListener('click', () => placeOrder({
                printKot: true
            }));
            byId('sendKitchenMobileBtn')?.addEventListener('click', () => placeOrder({
                printKot: false
            }));
            byId('printSendKitchenMobileBtn')?.addEventListener('click', () => placeOrder({
                printKot: true
            }));
            byId('clearCartBtn')?.addEventListener('click', () => {
                state.cart = [];
                syncUi();
            });
            byId('clearCartQuickBtn')?.addEventListener('click', () => {
                state.cart = [];
                syncUi();
            });
            byId('backToTablesBtn')?.addEventListener('click', () => {
                window.location.href = backToTablesUrl;
            });
            byId('backToTablesMobileBtn')?.addEventListener('click', () => {
                window.location.href = backToTablesUrl;
            });

            loadCart();
            syncUi();
        })();
    </script>
@endsection
