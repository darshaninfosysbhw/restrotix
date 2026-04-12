@extends($layout)
@section('content')
    <div class="flex-1 overflow-hidden bg-gray-900">
        <div class="hidden md:flex h-full flex-col p-2 lg:p-2 gap-3">
            <div class="grid flex-1 min-h-0 grid-cols-12 gap-3">
                <section
                    class="col-span-12 md:col-span-7 xl:col-span-8 min-h-0 h-full rounded-2xl border border-gray-700 bg-gray-800 p-4 shadow-sm flex flex-col">
                    <div class="mb-3 border-b border-gray-700 pb-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <button id="backToTablesBtn"
                                class="inline-flex items-center gap-2 rounded-xl border border-gray-600 bg-gray-700 px-3.5 py-1.5 text-sm text-gray-200 hover:bg-gray-700/80">
                                <i class="fas fa-chevron-left text-xs"></i>
                                Back to Tables
                            </button>

                            <h2 id="desktopTableHeading" class="text-xl font-bold text-white">Table
                                {{ $selectedTableNumber ?? request()->query('table', '5') }}</h2>
                            <span
                                class="rounded-md border border-green-400/60 bg-green-500/15 px-2.5 py-1 text-xs font-semibold text-green-400">Active</span>
                            <span class="text-sm text-gray-400">4 Seater</span>
                            <span class="text-sm text-gray-400">Dine In</span>

                            <div class="ml-auto grid grid-cols-3 gap-2 w-full sm:w-auto">
                                <div
                                    class="inline-flex items-center gap-1 rounded-xl bg-gray-700 p-2 text-center min-w-[80px]">
                                    <p class="text-[11px] text-gray-400">Orders :</p>
                                    <p class="text-sm font-bold text-blue-400 ">2</p>
                                </div>
                                <div
                                    class="inline-flex items-center gap-1 rounded-xl bg-gray-700 p-2 text-center min-w-[80px]">
                                    <p class="text-[11px] text-gray-400 ">Guests :</p>
                                    <p class="text-sm font-bold text-blue-400">4</p>
                                </div>
                                <div
                                    class="inline-flex items-center gap-1 rounded-xl bg-gray-700 p-2 text-center min-w-[80px]">
                                    <p class="text-[11px] text-gray-400 ">Session :</p>
                                    <p class="text-sm font-bold text-white">32m</p>
                                </div>
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
                            class="h-10 rounded-lg border border-gray-600 bg-gray-700 px-4 text-sm font-medium text-gray-200 hover:bg-gray-700/80">
                            <i class="fas fa-sliders-h mr-1.5"></i> Filters
                        </button>
                    </div>

                    <div id="desktopCategoryTabs" class="no-scrollbar mb-3 flex gap-2 overflow-x-auto pb-1">
                        @foreach ($dynamicCategories ?? [] as $idx => $category)
                            <button data-category-name="{{ $category }}"
                                class="whitespace-nowrap rounded-lg px-4 py-1.5 text-sm font-medium {{ $idx === 0 ? 'bg-orange-500 text-white' : 'bg-gray-700 border border-gray-600 text-gray-300 hover:text-white' }}">
                                {{ $category }}
                            </button>
                        @endforeach
                    </div>

                    <div class="no-scrollbar flex-1 min-h-0 overflow-y-auto pr-1" id="desktopMenuGridWrapper">
                        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                            @foreach ($dynamicMenuItems ?? [] as $menuIndex => $item)
                                <article class="rounded-xl border border-gray-700 bg-gray-800 overflow-hidden">
                                    <img src="{{ $item['image'] ?? asset('images/default-food.png') }}"
                                        alt="{{ $item['name'] ?? 'Item' }}" class="h-20 w-full object-cover">
                                    <div class="p-2.5">
                                        <h3 class="truncate text-sm font-semibold text-white">{{ $item['name'] ?? '' }}</h3>
                                        <p class="mt-0.5 text-[12px] font-medium text-gray-200">Rs
                                            {{ number_format((float) ($item['price'] ?? 0)) }}</p>
                                        <button data-add-menu-item="1" data-menu-index="{{ $menuIndex }}"
                                            data-item-name="{{ $item['name'] ?? '' }}"
                                            data-item-price="{{ $item['price'] ?? 0 }}"
                                            data-item-category="{{ $item['category'] ?? 'All Items' }}"
                                            class="mt-1.5 w-full rounded-lg border border-gray-600 bg-gray-700 px-2.5 py-1.5 text-sm text-gray-200 hover:bg-gray-700/80 flex items-center justify-between">
                                            <span><i class="fas fa-plus text-[10px] mr-1"></i> Add</span>
                                            <i class="fas fa-plus text-[10px] text-gray-400"></i>
                                        </button>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-2 grid grid-cols-2 xl:grid-cols-4 gap-2 border-t border-gray-700 pt-2">
                        <button
                            class="rounded-xl border border-blue-500/40 bg-blue-500/10 px-3 py-1.5 text-sm text-blue-300">Note</button>
                        <button
                            class="rounded-xl border border-indigo-500/40 bg-indigo-500/10 px-3 py-1.5 text-sm text-indigo-300">Custom
                            Item</button>
                        <button
                            class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-1.5 text-sm text-rose-300">Discount</button>
                        <button id="clearCartQuickBtn"
                            class="rounded-xl border border-orange-500/40 bg-orange-500/10 px-3 py-1.5 text-sm text-orange-300">Clear
                            Cart</button>
                    </div>
                </section>

                <aside
                    class="col-span-12 md:col-span-5 xl:col-span-4 min-h-0 h-full rounded-2xl border border-gray-700 bg-gray-800 shadow-sm flex flex-col">
                    <div class="px-4 py-3 border-b border-gray-700 flex items-center justify-between">
                        <h3 id="desktopCartHeading" class="text-lg font-bold text-white">Cart (0 Items)</h3>
                        <button id="clearCartBtn"
                            class="rounded-lg bg-red-500/10 border border-red-500/30 px-3 py-1.5 text-sm text-red-300">Clear</button>
                    </div>

                    <div id="desktopCartList" class="no-scrollbar flex-1 min-h-0 overflow-y-auto">
                        <div class="px-4 py-6 text-sm text-gray-400">No items selected yet. Use Add buttons to build order.
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
                                    class="font-semibold text-white">Rs
                                    0</span></div>

                            <div class="flex justify-between"><span>Tax (5%)</span><span class="font-semibold text-white"
                                    id="desktopTax">Rs
                                    0</span></div>
                        </div>
                        <div class="mt-2 border-t border-gray-700 pt-2 flex items-end justify-between">
                            <p class="text-lg font-bold text-white">Total</p>
                            <p id="desktopTotal" class="text-lg font-bold text-white">Rs 0</p>
                        </div>

                        <button id="sendKitchenDesktopBtn"
                            class="mt-3 w-full rounded-xl bg-orange-500 py-2.5 text-sm font-bold text-white hover:bg-orange-600">
                            <i class="far fa-paper-plane mr-1.5"></i> Send to Kitchen
                        </button>

                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <button class="rounded-xl bg-gray-700 border border-gray-600 py-2 text-sm text-gray-200">Hold
                                Order</button>
                            <button class="rounded-xl bg-gray-700 border border-gray-600 py-2 text-sm text-gray-200">Save
                                Draft</button>
                        </div>
                    </div>
                </aside>
            </div>

        </div>

        <div class="md:hidden relative h-full bg-gray-900 text-gray-200 overflow-hidden">
            <div class="h-full overflow-y-auto px-3 pb-24 pt-3">
                <div class="rounded-2xl border border-gray-700 bg-gray-800 p-3 mb-3">
                    <div class="flex items-center justify-between mb-2.5">
                        <h2 id="mobileTableHeading" class="text-base font-semibold text-white">Order - Table
                            {{ $selectedTableNumber ?? request()->query('table', '5') }} <span
                                class="text-red-400">(Occupied)</span></h2>
                        <button class="text-gray-400"><i class="fas fa-times"></i></button>
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
                        <article class="rounded-xl border border-gray-700 bg-gray-800 overflow-hidden">
                            <img src="{{ $item['image'] ?? asset('images/default-food.png') }}"
                                alt="{{ $item['name'] ?? 'Item' }}" class="h-24 w-full object-cover">
                            <div class="p-2">
                                <h3 class="truncate text-sm font-semibold text-white">{{ $item['name'] ?? '' }}</h3>
                                <p class="text-xs text-gray-400 truncate">Ready to serve quickly</p>
                                <div class="mt-1.5 flex items-center justify-between">
                                    <p class="text-sm font-bold text-white">Rs
                                        {{ number_format((float) ($item['price'] ?? 0)) }}</p>
                                    <button data-add-menu-item="1" data-menu-index="{{ $mobileIndex }}"
                                        data-item-name="{{ $item['name'] ?? '' }}"
                                        data-item-price="{{ $item['price'] ?? 0 }}"
                                        data-item-category="{{ $item['category'] ?? 'All Items' }}"
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
                    <div class="text-center py-6 text-gray-400 text-sm">No items selected yet.</div>
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
                    <div class="text-center py-6 text-gray-400 text-sm">No items selected yet.</div>
                </div>

                <div class="mt-3 rounded-xl border border-gray-700 bg-gray-700/60 p-3 text-sm text-gray-300">
                    <div class="mb-1 flex justify-between"><span>Subtotal:</span><span id="mobileSubtotal">Rs 0</span>
                    </div>
                    <div class="mb-1 flex justify-between"><span>Tax (5%):</span><span id="mobileTax">Rs 0</span>
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

                <div class="mt-3 grid grid-cols-2 gap-2">
                    <button id="backToMobileCart"
                        class="rounded-xl bg-gray-700 border border-gray-600 py-2.5 text-sm text-gray-200">Cancel</button>
                    <button id="sendKitchenMobileBtn"
                        class="rounded-xl bg-orange-500 py-2.5 text-sm font-semibold text-white">Send to Kitchen
                        (KOT)</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .mobile-sheet {
            animation: sheetUp .18s ease-out;
        }

        @keyframes sheetUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>

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
        (function() {
            // 1. Data & State Initialization
            const tableNumber = @json($selectedTableNumber ?? request()->query('table', '5'));
            const tableId = @json($selectedTableId ?? request()->query('table_id') ? (int) ($selectedTableId ?? request()->query('table_id')) : null);
            const csrfToken = '{{ csrf_token() }}';
            const storageKey = `waiter_manual_order_${tableId || tableNumber || 'default'}`;
            const categories = @json($dynamicCategories ?? []);
            const menuItems = @json($dynamicMenuItems ?? []);

            const state = {
                cart: [],
                search: '',
                selectedCategory: categories[0] || 'All Items',
            };

            const byId = (id) => document.getElementById(id);
            const formatPrice = (val) => `Rs ${Number(val || 0).toLocaleString()}`;
            const normalizeCategory = (value) => String(value || '').trim().toLowerCase();

            // 2. Core Functions
            function loadCart() {
                try {
                    const parsed = JSON.parse(localStorage.getItem(storageKey) || '[]');
                    state.cart = Array.isArray(parsed) ? parsed.map((item) => ({
                        ...item,
                        note: item?.note ?? '',
                    })) : [];
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

            // 3. Cart Logic (Using Index for uniqueness)
            function addItemByIndex(menuIndex) {
                const item = menuItems[Number(menuIndex)];
                if (!item) return;

                // Naya line item add karo (Allows multiple entries for same item with different notes)
                state.cart.push({
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    qty: 1,
                    note: '',
                });
                syncUi();
            }

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
                state.cart.splice(index, 1); // Poora item array se uda do
                syncUi();
            }

            // 4. Rendering Logic
            function renderCartDesktop() {
                const holder = byId('desktopCartList');
                if (!holder) return;
                if (!state.cart.length) {
                    holder.innerHTML = '<div class="px-4 py-6 text-sm text-gray-400">No items selected.</div>';
                    return;
                }

                holder.innerHTML = state.cart.map((item, index) => `
            <div class="px-4 py-2.5 border-b border-gray-700/60 flex items-start gap-2.5">
                <div class="min-w-0 flex-1">
                    <h4 class="truncate text-sm font-semibold text-white">${item.name}</h4>
                    <input type="text" data-cart-index="${index}" value="${String(item.note || '').replace(/"/g, '&quot;')}"
                        placeholder="Note..." class="cart-note-input mt-1 h-7 w-full rounded-md border border-gray-600 bg-transparent px-2 text-xs text-white focus:outline-none" />
                </div>
                <div class="text-right mt-4.5">
                    <div class="mt-1 inline-flex items-center rounded-md border border-gray-600 bg-gray-700 px-1 py-0.5">
                        <button data-action="dec" data-index="${index}" class="h-6 w-6 text-gray-300">-</button>
                        <span class="w-5 text-center text-xs font-semibold text-white">${item.qty}</span>
                        <button data-action="inc" data-index="${index}" class="h-6 w-6 text-gray-300">+</button>
                    </div>
                </div>
                <p class="text-sm font-semibold text-white mt-0.5">${formatPrice(item.qty * item.price)}</p>
                <button data-action="remove" data-index="${index}" class="ml-2 text-rose-500 hover:text-rose-400 p-1 mt-3">
                  <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `).join('');
            }

            function renderCartMobile() {
                const cartHolder = byId('mobileCartItemsContainer');
                const reviewHolder = byId('mobileReviewItemsContainer');
                if (!cartHolder || !reviewHolder) return;

                cartHolder.innerHTML = state.cart.map((item, index) => `
            <div class="rounded-xl border border-gray-700 bg-gray-700/50 p-2.5 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-white">${item.qty}x ${item.name}</p>
                        <p class="text-sm font-bold text-gray-200">${formatPrice(item.qty * item.price)}</p>
                    </div>
                    <div class="flex gap-1">
                        <button data-action="inc" data-index="${index}" class="h-8 w-8 rounded-full border border-orange-500 text-orange-300">+</button>
                        <button data-action="dec" data-index="${index}" class="h-8 w-8 rounded-full border border-orange-500 text-orange-300">-</button>
                        <button data-action="remove" data-index="${index}" class="text-rose-500 text-lg h-8 w-8"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>
                <input type="text" data-cart-index="${index}" value="${String(item.note || '').replace(/"/g, '&quot;')}"
                    placeholder="Instructions..." class="cart-note-input mt-2 h-8 w-full rounded-md border border-orange-500/50 bg-transparent px-2 text-xs text-white" />
            </div>
        `).join('');

                reviewHolder.innerHTML = state.cart.map(item => `
            <div class="flex justify-between border-b border-gray-700 py-2">
                <span class="text-sm text-white">${item.qty}x ${item.name}</span>
                <span class="text-sm font-semibold text-white">${formatPrice(item.qty * item.price)}</span>
            </div>
        `).join('');
            }

            // 5. Search & Filters
            function applySearchFilter() {
                const keyword = (state.search || '').trim().toLowerCase();
                document.querySelectorAll('[data-add-menu-item="1"]').forEach(btn => {
                    const article = btn.closest('article');
                    const name = btn.dataset.itemName.toLowerCase();
                    const cat = btn.dataset.itemCategory;
                    const catMatch = normalizeCategory(state.selectedCategory) === normalizeCategory(
                            'All Items') ||
                        normalizeCategory(cat) === normalizeCategory(state.selectedCategory);
                    article.classList.toggle('hidden', !(catMatch && name.includes(keyword)));
                });
            }

            function updateCategoryActiveState() {
                document.querySelectorAll('#desktopCategoryTabs [data-category-name]').forEach((btn) => {
                    const isActive = normalizeCategory(btn.dataset.categoryName || '') === normalizeCategory(
                        state.selectedCategory);
                    btn.className = isActive ?
                        'whitespace-nowrap rounded-lg px-4 py-1.5 text-sm font-medium bg-orange-500 text-white' :
                        'whitespace-nowrap rounded-lg px-4 py-1.5 text-sm font-medium bg-gray-700 border border-gray-600 text-gray-300 hover:text-white';
                });

                document.querySelectorAll('#mobileCategoryTabs [data-mobile-category-name]').forEach((btn) => {
                    const isActive = normalizeCategory(btn.dataset.mobileCategoryName || '') ===
                        normalizeCategory(state.selectedCategory);
                    btn.className = isActive ?
                        'flex-shrink-0 px-5 py-2.5 rounded-full text-xs uppercase bg-orange-600 text-white font-black whitespace-nowrap' :
                        'flex-shrink-0 px-5 py-2.5 rounded-full text-xs uppercase bg-gray-800 border border-gray-700 text-gray-400 font-bold whitespace-nowrap';
                });
            }

            function setActiveCategory(categoryName) {
                state.selectedCategory = String(categoryName || 'All Items').trim();
                syncUi();
            }



            function updateSummary() {
                const count = state.cart.reduce((s, i) => s + i.qty, 0);
                const subtotal = state.cart.reduce((s, i) => s + (i.qty * i.price), 0);

                // 🔥 Dynamic Calculations (Abhi ke liye logic yahan rakho)
                const discount = subtotal > 0 ? 0 : 0; // Yahan future mein discount logic aayega
                const taxRate = 0.05; // 5% Tax
                const taxAmount = subtotal * taxRate;
                const grandTotal = subtotal - discount + taxAmount;

                // --- Desktop Updates ---
                if (byId('desktopCartHeading')) byId('desktopCartHeading').textContent = `Cart (${count} Items)`;
                if (byId('desktopSubtotal')) byId('desktopSubtotal').textContent = formatPrice(subtotal);
                if (byId('desktopTax')) byId('desktopTax').textContent = formatPrice(taxAmount); // Id add karni hogi
                if (byId('desktopTotal')) byId('desktopTotal').textContent = formatPrice(grandTotal);

                // --- Mobile Updates ---
                if (byId('mobileCartBarText')) byId('mobileCartBarText').textContent =
                    `${count} Items | ${formatPrice(grandTotal)}`;
                if (byId('mobileSubtotal')) byId('mobileSubtotal').textContent = formatPrice(subtotal);
                if (byId('mobileTax')) byId('mobileTax').textContent = formatPrice(taxAmount); // Correct Target
                if (byId('mobileTotal')) byId('mobileTotal').textContent = formatPrice(grandTotal);

                // Disable button if cart empty
                const sendBtns = [byId('sendKitchenDesktopBtn'), byId('sendKitchenMobileBtn')];
                sendBtns.forEach(btn => {
                    if (btn) btn.disabled = count === 0;
                });
            }


            // 6. Event Listeners (Delegation used for dynamic elements)
            document.addEventListener('click', (e) => {
                // Add Item
                const addBtn = e.target.closest('[data-add-menu-item="1"]');
                if (addBtn) return addItemByIndex(addBtn.dataset.menuIndex);

                // Qty Actions
                const actionBtn = e.target.closest('[data-action]');
                // if (actionBtn) {
                //     const index = Number(actionBtn.dataset.index);
                //     const delta = actionBtn.dataset.action === 'inc' ? 1 : -1;
                //     updateQty(index, delta);
                // }

                if (actionBtn) {
                    const index = Number(actionBtn.dataset.index);
                    const action = actionBtn.dataset.action;

                    if (action === 'remove') {
                        removeItem(index);
                    } else {
                        const delta = action === 'inc' ? 1 : -1;
                        updateQty(index, delta);
                    }
                    return;
                }

                // Category Switch
                const catBtn = e.target.closest('[data-category-name], [data-mobile-category-name]');
                if (catBtn) {
                    setActiveCategory(catBtn.dataset.categoryName || catBtn.dataset.mobileCategoryName);
                }
            });

            document.addEventListener('input', (e) => {
                // Note Input
                if (e.target.classList.contains('cart-note-input')) {
                    const index = Number(e.target.dataset.cartIndex);
                    if (state.cart[index]) {
                        state.cart[index].note = e.target.value;
                        saveCart(); // Sirf save karo, syncUi nahi (taaki focus lose na ho)
                    }
                }
                // Search
                if (e.target.id === 'menuSearchInput' || e.target.id === 'mobileSearchInput') {
                    state.search = e.target.value;
                    applySearchFilter();
                }
            });

            // 7. Order Submission
            async function placeOrder() {
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
                        name: i.name,
                        price: Number(i.price || 0),
                        quantity: i.qty,
                        note: i.note
                    })),
                    table_id: tableId,
                    table_number: tableNumber,
                    order_type: 'dine_in',
                    notes: specialNote,
                    source: 'pos',
                    // source: window.authRole || 'pos',

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
                    state.cart = [];
                    syncUi();
                    if (byId('orderNotes')) byId('orderNotes').value = '';
                    if (byId('mobileOrderNotes')) byId('mobileOrderNotes').value = '';
                    alert('Order Placed!');
                } else {
                    const firstValidationError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
                    alert(firstValidationError || data.message || 'Unable to place order');
                }
            }

            byId('sendKitchenDesktopBtn')?.addEventListener('click', placeOrder);
            byId('sendKitchenMobileBtn')
                ?.addEventListener('click', placeOrder);
            byId('clearCartBtn')?.addEventListener('click', () => {
                state.cart = [];
                syncUi();
            });


            byId('backToTablesBtn')?.addEventListener('click', () => {
                // window.location.href = @json(route('waiter.tables.index'));
                window.location.href = @json($backUrl);
            });

            // Init
            loadCart();
            syncUi();
        })();
    </script>
@endsection
