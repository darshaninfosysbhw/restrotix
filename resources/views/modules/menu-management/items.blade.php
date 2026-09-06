@extends('core.layouts.admin')

<style>
    .toggle {
        appearance: none;
        width: 36px;
        height: 18px;
        background: #374151;
        border-radius: 999px;
        position: relative;
        cursor: pointer;
        transition: 0.3s;
    }

    .toggle:checked {
        background: #f97316;
    }

    .toggle::before {
        content: "";
        position: absolute;
        width: 14px;
        height: 14px;
        background: #ffffff;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: 0.3s;
    }

    .toggle:checked::before {
        transform: translateX(18px);
    }
</style>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@section('content')
    @php
        $menuItems = collect($items ?? []);
        $categoryOptions = collect($categories ?? []);
        $branchOptions = collect($branches ?? []);
        $itemsPaginator = $itemsPaginator ?? null;
        $isManager = strtolower((string) auth()->user()->role) === 'manager';

        $itemStats = $itemStats ?? [
            'total' => $menuItems->count(),
            'active' => $menuItems->where('is_active', true)->count(),
            'out_of_stock' => $menuItems->where('is_available', false)->count(),
            'inactive' => $menuItems->where('is_active', false)->count(),
        ];

        $selectedCategoryId = (string) old('category_id', optional($categoryOptions->first())->id);
        $selectedCategory = $categoryOptions->firstWhere('id', (int) $selectedCategoryId) ?? $categoryOptions->first();
        $selectedCategoryName = $selectedCategory->name ?? 'Select Category';

        $selectedBranchId = (string) old('branch_id', $isManager ? optional($branchOptions->first())->id : '');
        $selectedBranch = $branchOptions->firstWhere('id', (int) $selectedBranchId);
        $selectedBranchName = $selectedBranch ? $selectedBranch->branch_name : 'Global Specific';

        $selectedType = old('type', 'veg');
        $editItemIdFromSession = session('edit_item_id');
        $editItemFromSession = $editItemIdFromSession
            ? $menuItems->firstWhere('id', (int) $editItemIdFromSession)
            : null;
        $initialImagePreviewUrl = old('form_mode', 'add') === 'edit' ? $editItemFromSession['image_url'] ?? '' : '';
    @endphp

    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Menu Item Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Item Directory</h1>
                    <p class="text-sm text-gray-400 mt-2">Manage item listing, stock visibility, and pricing from one place.
                    </p>
                </div>
                @if (!$isManager)
                    <button id="openItemModal" type="button"
                        class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium transition cursor-pointer">
                        <i class="fas fa-plus"></i>
                        Add New Item
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Total Items</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $itemStats['total'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-orange-500/15 text-orange-500 flex items-center justify-center">
                        <i class="fas fa-utensils text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Active</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $itemStats['active'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-emerald-500/15 text-emerald-400 flex items-center justify-center">
                        <i class="fas fa-check-circle text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Out Of Stock</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $itemStats['out_of_stock'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-amber-400/15 text-amber-400 flex items-center justify-center">
                        <i class="fas fa-box-open text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Inactive</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $itemStats['inactive'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-slate-500/20 text-slate-300 flex items-center justify-center">
                        <i class="fas fa-eye-slash text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400">All Items</span>
                    <span
                        class="px-2.5 py-1 rounded-full text-xs bg-orange-500/10 text-orange-500 border border-orange-500/30">
                        Total : {{ $itemStats['total'] }}
                    </span>
                </div>
                <form id="itemSearchForm" method="GET" action="{{ route('admin.menu.items.index') }}"
                    class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
                    <div class="relative w-full sm:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input id="itemTableSearch" name="search" type="text" value="{{ request('search') }}"
                            placeholder="Search item, category, code..."
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.menu.items.index') }}"
                            class="px-3 py-2 rounded-lg text-xs bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto overflow-y-visible">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-400 border-b border-gray-700 uppercase tracking-wide">
                        <tr>
                            <th class="text-left py-3 pr-4 font-medium">#</th>
                            <th class="text-left py-3 px-4 font-medium">Item</th>
                            <th class="text-left py-3 px-4 font-medium">Category</th>
                            <th class="text-left py-3 px-4 font-medium">Price</th>
                            <th class="text-left py-3 px-4 font-medium">Visibility</th>
                            <th class="text-left py-3 px-4 font-medium">Availability</th>
                            <th class="text-left py-3 px-4 font-medium">Status</th>
                            <th class="text-left py-3 px-4 font-medium">Updated</th>
                            @if (auth()->user()->role !== 'manager')
                                <th class="text-left py-3 pl-8 font-medium">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="itemTableBody" class="divide-y divide-gray-700/80">
                        @foreach ($menuItems as $index => $item)
                            <tr class="item-row hover:bg-white/5 transition">
                                <td class="py-3 pr-4 text-gray-300">
                                    {{ !empty($itemsPaginator) && $itemsPaginator->firstItem() ? $itemsPaginator->firstItem() + $index : $index + 1 }}
                                </td>
                                <td class="py-3 px-4">
                                    <div>
                                        <p class="font-medium text-white">{{ $item['name'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $item['code'] ?: 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-white">{{ $item['category_name'] }}</td>
                                <td class="py-3 px-4 text-gray-300">
                                    Rs. {{ $item['sale_price'] ?? $item['base_price'] }}
                                    @if (!empty($item['sale_price']))
                                        <span class="text-xs text-gray-500 line-through ml-1">Rs.
                                            {{ $item['base_price'] }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-300">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs bg-white/5 text-gray-300 border border-white/10">
                                        {{ $item['branch_name'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($item['is_available'])
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-400">Available</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs bg-red-500/15 text-red-400">Out Of
                                            Stock</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <form method="POST"
                                        action="{{ route('admin.menu.items.toggle-status', $item['id']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex items-center gap-2">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" name="is_active" value="1" class="toggle"
                                                {{ !empty($item['is_active']) ? 'checked' : '' }}
                                                onchange="this.form.submit()">
                                            <span
                                                class="text-xs {{ !empty($item['is_active']) ? 'text-gray-300' : 'text-gray-400' }}">
                                                {{ !empty($item['is_active']) ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </form>
                                </td>
                                <td class="py-3 px-4 text-gray-300">{{ $item['updated_at'] ?? '-' }}</td>
                                @if (auth()->user()->role !== 'manager')
                                <td class="py-3 pl-8">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            class="edit-item-btn px-2.5 py-1.5 rounded-md text-xs bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/20 transition cursor-pointer"
                                            data-item='@json($item)'
                                            data-update-url="{{ route('admin.menu.items.update', $item['id']) }}">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('admin.menu.items.destroy', $item['id']) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2.5 py-1.5 rounded-md text-xs bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 transition cursor-pointer">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                        <tr id="itemNoResultRow" class="{{ count($menuItems) ? 'hidden' : '' }}">
                            <td colspan="9" class="py-6 text-center text-sm text-gray-400">No items found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (!empty($itemsPaginator))
                <div class="mt-4 border-t border-gray-700 pt-4">
                    <x-core::ui.pagination :paginator="$itemsPaginator" label="items" />
                </div>
            @endif
        </div>
    </div>

    <div id="itemModal" class="fixed inset-0 z-[120] hidden overflow-y-auto">
        <div id="itemModalBackdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div class="relative z-10 min-h-screen flex items-start md:items-center justify-center p-4">
            <div
                class="w-full max-w-xl max-h-[calc(100dvh-2rem)] overflow-y-auto bg-gray-800 rounded-lg shadow-2xl border border-gray-700">
                <div class="px-6 py-5 flex justify-between items-center border-b border-gray-700">
                    <h2 id="itemModalTitle" class="text-xl font-bold text-orange-500">Add New Menu Item</h2>
                    <button id="closeItemModal" type="button"
                        class="text-gray-400 hover:text-gray-200 transition cursor-pointer">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="itemModalForm" method="POST" action="{{ route('admin.menu.items.store') }}"
                    class="px-8 py-6 space-y-6 max-h-[80vh] overflow-y-auto custom-scrollbar"
                    enctype="multipart/form-data" x-data="{ hasVariants: false, variants: [], addons: [] }"
                    @edit-item.window="hasVariants = $event.detail.has_variants; variants = $event.detail.variants || []; addons = $event.detail.addons || [];"
                    @reset-form.window="hasVariants = false; variants = []; addons = [];">

                    @csrf
                    <input type="hidden" id="item_form_mode" name="form_mode" value="{{ old('form_mode', 'add') }}">
                    <input type="hidden" id="edit_item_id" name="edit_item_id"
                        value="{{ old('edit_item_id', session('edit_item_id')) }}">
                    <input type="hidden" name="category_id" id="selected_category_id"
                        value="{{ $selectedCategoryId }}">
                    <input type="hidden" name="type" id="selected_item_type" value="{{ $selectedType }}">
                    <input type="hidden" name="tax_percent" value="{{ old('tax_percent', 0) }}">

                    @if ($errors->any())
                        <div class="rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2 text-xs text-red-300">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Item Name</label>
                                <input id="item_name" type="text" name="name" value="{{ old('name') }}"
                                    placeholder="ex. Cola" required
                                    class="w-full border border-gray-700 bg-gray-900 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-orange-500/50 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Item Image</label>
                                <label
                                    class="border-2 border-dashed border-gray-700 rounded-xl p-6 flex flex-col items-center justify-center bg-gray-900 hover:bg-gray-900/70 transition cursor-pointer group">
                                    <img id="item_image_preview" src="" alt="Item Preview"
                                        class="hidden w-24 h-24 rounded-lg object-cover border border-gray-700 mb-3">
                                    <div id="item_image_placeholder"
                                        class="w-10 h-10 mb-2 text-gray-500 group-hover:text-orange-500 transition">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </div>
                                    <span id="item_image_label" class="text-xs font-medium text-gray-400">Upload
                                        Image</span>
                                    <input id="item_image_input" type="file" class="hidden" name="image"
                                        accept="image/*">
                                </label>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                                <div onclick="document.getElementById('cat_list').classList.toggle('hidden')"
                                    class="w-full border border-gray-700 rounded-lg px-4 py-2.5 text-sm flex justify-between items-center bg-gray-900 cursor-pointer">
                                    <div class="flex items-center gap-2 text-gray-200">
                                        <i class="fas fa-search text-gray-400"></i>
                                        <span id="category_display_text">{{ $selectedCategoryName }}</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </div>

                                <div id="cat_list"
                                    class="hidden absolute mt-1 w-full bg-gray-900 border border-gray-700 rounded-lg shadow-xl z-20 overflow-hidden">
                                    @forelse ($categoryOptions as $category)
                                        @php $isSelectedCategory = (string) $category->id === $selectedCategoryId; @endphp
                                        <div onclick="selectCategory('{{ $category->id }}', '{{ e($category->name) }}')"
                                            class="px-4 py-2 text-sm cursor-pointer border-l-4 transition {{ $isSelectedCategory ? 'bg-orange-500/10 text-orange-400 font-medium border-orange-500' : 'text-gray-300 border-transparent hover:bg-white/5' }}">
                                            {{ $category->name }}
                                        </div>
                                    @empty
                                        <div class="px-4 py-2 text-sm text-gray-400">No categories available</div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="space-y-2 relative">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <span class="text-sm font-medium text-gray-300">Visible in Branch</span>
                                    <i class="fas fa-info-circle text-[10px] text-gray-500 cursor-help"></i>
                                </div>

                                <input type="hidden" name="branch_id" id="selected_branch_id"
                                    value="{{ $selectedBranchId }}">

                                <div onclick="document.getElementById('branch_list').classList.toggle('hidden')"
                                    class="w-full border border-gray-700 rounded-lg px-4 py-2.5 text-sm flex justify-between items-center bg-gray-900 cursor-pointer hover:border-gray-600 transition">
                                    <div class="flex items-center gap-2 text-gray-200">
                                        <i class="fas fa-store text-gray-400 text-xs"></i>
                                        <span id="branch_display_text">{{ $selectedBranchName }}</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                                </div>

                                <div id="branch_list"
                                    class="hidden absolute mt-1 w-full bg-gray-900 border border-gray-700 rounded-lg shadow-2xl z-30 overflow-hidden">
                                    @if (!$isManager)
                                        <div onclick="selectBranch('', 'Global Specific')"
                                            class="px-4 py-2.5 text-sm text-gray-300 hover:bg-orange-500/10 hover:text-orange-400 cursor-pointer transition">
                                            Global Specific
                                        </div>
                                    @endif
                                    @foreach ($branchOptions as $branch)
                                        <div onclick="selectBranch('{{ $branch->id }}', '{{ e($branch->branch_name) }}')"
                                            class="px-4 py-2.5 text-sm text-gray-300 hover:bg-orange-500/10 hover:text-orange-400 cursor-pointer border-t border-gray-800 transition">
                                            {{ $branch->branch_name }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Food Type</label>
                        <div class="flex gap-2">
                            <button type="button" onclick="setType('veg')" id="btn-veg"
                                class="flex-1 py-2 px-3 rounded-lg border text-xs font-bold flex items-center justify-center gap-2 transition-all"><i
                                    class="fas fa-leaf"></i> VEG</button>
                            <button type="button" onclick="setType('non-veg')" id="btn-nonveg"
                                class="flex-1 py-2 px-3 rounded-lg border text-xs font-bold flex items-center justify-center gap-2 transition-all"><i
                                    class="fas fa-drumstick-bite"></i> NON-VEG</button>
                            <button type="button" onclick="setType('egg')" id="btn-egg"
                                class="flex-1 py-2 px-3 rounded-lg border text-xs font-bold flex items-center justify-center gap-2 transition-all"><i
                                    class="fas fa-egg"></i> EGG</button>
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2 space-y-6 pt-4 border-t border-gray-700">

                        <div class="bg-gray-900/50 p-4 rounded-xl border border-gray-700/60">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-sm font-bold text-white">Does this item have multiple
                                        variants?</label>
                                    <span class="text-xs text-gray-400 block">Enable this for different portions like
                                        Half/Full, Regular/Large.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="hasVariants" name="has_variants" value="1"
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:!bg-orange-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div x-show="!hasVariants" x-transition
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-900 p-4 rounded-xl border border-gray-700/40">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-2">Base Price <span
                                        class="text-red-500">*</span></label>
                                <input id="item_base_price" type="number" step="0.01" name="base_price"
                                    placeholder="ex. 150.00" value="{{ old('base_price') }}"
                                    class="w-full border border-gray-700 bg-gray-900 rounded-lg px-3 py-2.5 text-sm text-white outline-none focus:border-orange-500/50">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-2">Sale Price (Discounted)</label>
                                <input id="item_sale_price" type="number" step="0.01" name="sale_price"
                                    placeholder="ex. 120.00" value="{{ old('sale_price') }}"
                                    class="w-full border border-gray-700 bg-gray-900 rounded-lg px-3 py-2.5 text-sm text-white outline-none focus:border-orange-500/50">
                            </div>
                        </div>

                        <div x-show="hasVariants" x-transition
                            class="space-y-3 bg-gray-900 p-4 rounded-xl border border-gray-700/40">
                            <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                <span class="text-xs text-gray-300 font-black uppercase tracking-wider">Configure
                                    Portions / Sizes</span>
                                <button type="button"
                                    @click="variants.push({ id: '', name: '', base_price: '', sale_price: '' })"
                                    class="text-xs bg-orange-600/20 hover:bg-orange-600/25 border border-orange-500/40 text-orange-400 px-3 py-1.5 rounded-lg font-bold transition cursor-pointer">
                                    + Add Variant Row
                                </button>
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto no-scrollbar">
                                <template x-for="(variant, index) in variants" :key="index">
                                    <div
                                        class="flex gap-2 items-center bg-gray-900 p-2 rounded-lg border border-gray-700/50">
                                        <input type="hidden" :name="'variants[' + index + '][id]'" x-model="variant.id">
                                        <input type="text" :name="'variants[' + index + '][name]'"
                                            x-model="variant.name" placeholder="ex. Half, Full" required
                                            class="w-1/3 bg-gray-800 border border-gray-700 text-white rounded-lg p-2 text-xs outline-none focus:border-orange-500/50">
                                        <input type="number" step="0.01" :name="'variants[' + index + '][base_price]'"
                                            x-model="variant.base_price" placeholder="Base Price" required
                                            class="w-1/3 bg-gray-800 border border-gray-700 text-white rounded-lg p-2 text-xs outline-none focus:border-orange-500/50">
                                        <input type="number" step="0.01" :name="'variants[' + index + '][sale_price]'"
                                            x-model="variant.sale_price" placeholder="Sale Price"
                                            class="w-1/3 bg-gray-800 border border-gray-700 text-white rounded-lg p-2 text-xs outline-none focus:border-orange-500/50">
                                        <button type="button" @click="variants.splice(index, 1)"
                                            class="text-red-500 hover:text-red-400 font-bold px-2 text-sm cursor-pointer">✕</button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="space-y-3 bg-gray-900 p-4 rounded-xl border border-gray-700/40">
                            <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                <div>
                                    <span class="text-xs text-gray-300 font-black uppercase tracking-wider">Item Add-ons
                                        / Modifiers</span>
                                </div>
                                <button type="button" @click="addons.push({ id: '', name: '', price: '' })"
                                    class="text-xs bg-emerald-600/20 hover:bg-emerald-600/25 border border-emerald-500/40 text-emerald-400 px-3 py-1.5 rounded-lg font-bold transition cursor-pointer">
                                    + Add Addon Row
                                </button>
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto no-scrollbar">
                                <template x-for="(addon, index) in addons" :key="index">
                                    <div
                                        class="flex gap-2 items-center bg-gray-900 p-2 rounded-lg border border-gray-700/50">
                                        <input type="hidden" :name="'addons[' + index + '][id]'" x-model="addon.id">
                                        <input type="text" :name="'addons[' + index + '][name]'" x-model="addon.name"
                                            placeholder="ex. Extra Cheese" required
                                            class="w-1/2 bg-gray-800 border border-gray-700 text-white rounded-lg p-2 text-xs outline-none focus:border-orange-500/50">
                                        <input type="number" step="0.01" :name="'addons[' + index + '][price]'"
                                            x-model="addon.price" placeholder="Price" required
                                            class="w-1/2 bg-gray-800 border border-gray-700 text-white rounded-lg p-2 text-xs outline-none focus:border-orange-500/50">
                                        <button type="button" @click="addons.splice(index, 1)"
                                            class="text-red-500 hover:text-red-400 font-bold px-2 text-sm cursor-pointer">✕</button>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    <div class="flex flex-col gap-4 pt-4 border-t border-gray-700">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Description (Optional)</label>
                            <textarea id="item_description" name="description" rows="2" placeholder="Tell something about this dish..."
                                class="w-full border border-gray-700 bg-gray-900 rounded-lg px-3 py-2 text-sm text-white outline-none focus:border-orange-500/50">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="flex flex-wrap items-center gap-6">

                                <!-- 🟢 1. ACTIVE STATUS (DUMMY REMOVED - NOW FIXED) -->
                                <div class="flex items-center gap-3">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="is_active" value="0">
                                        <input id="item_is_active" type="checkbox" name="is_active" value="1"
                                            {{ old('is_active', 1) ? 'checked' : '' }} class="sr-only peer">
                                        <div
                                            class="w-10 h-5 bg-gray-700 rounded-full peer peer-checked:!bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5">
                                        </div>
                                    </label>
                                    <span class="text-[10px] font-bold text-gray-400">ACTIVE STATUS</span>
                                </div>

                                <!-- 🟠 2. AVAILABLE -->
                                <div class="flex items-center gap-3 border-l border-gray-700/60 pl-6">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="is_available" value="0">
                                        <input id="item_is_available" type="checkbox" name="is_available" value="1"
                                            {{ old('is_available', 1) ? 'checked' : '' }} class="sr-only peer">
                                        <div
                                            class="w-10 h-5 bg-gray-700 rounded-full peer peer-checked:!bg-orange-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5">
                                        </div>
                                    </label>
                                    <span class="text-[10px] font-bold text-gray-400">AVAILABLE</span>
                                </div>

                                <!-- 🟡 3. RECOMMENDED -->
                                <div class="flex items-center gap-3 border-l border-gray-700/60 pl-6">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="is_recommended" value="0">
                                        <input id="item_is_recommended" type="checkbox" name="is_recommended"
                                            value="1" {{ old('is_recommended') ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-10 h-5 bg-gray-700 rounded-full peer peer-checked:!bg-yellow-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5">
                                        </div>
                                    </label>
                                    <span class="text-[10px] font-bold text-gray-400">RECOMMENDED</span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-700">
                        <button type="button" id="closeTableModalBtn"
                            class="px-6 py-2.5 rounded-lg text-sm font-semibold bg-transparent hover:bg-white/5 text-gray-400 border border-gray-700 transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" id="submitCategoryBtn"
                            class="px-6 py-2.5 rounded-lg text-sm font-semibold bg-orange-600 hover:bg-orange-500 text-white shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fas fa-save"></i> Save Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const modal = document.getElementById('itemModal');
            const openBtn = document.getElementById('openItemModal');
            const closeBtn = document.getElementById('closeItemModal');
            const cancelBtn = document.getElementById('closeTableModalBtn');
            const backdrop = document.getElementById('itemModalBackdrop');
            const searchForm = document.getElementById('itemSearchForm');
            const searchInput = document.getElementById('itemTableSearch');
            const itemForm = document.getElementById('itemModalForm');
            const modalTitle = document.getElementById('itemModalTitle');
            const submitBtn = document.getElementById('submitCategoryBtn');
            const formModeInput = document.getElementById('item_form_mode');
            const editItemIdInput = document.getElementById('edit_item_id');
            const nameInput = document.getElementById('item_name');
            const basePriceInput = document.getElementById('item_base_price');
            const salePriceInput = document.getElementById('item_sale_price');
            const descriptionInput = document.getElementById('item_description');
            const isAvailableInput = document.getElementById('item_is_available');
            const isRecommendedInput = document.getElementById('item_is_recommended');
            const isActiveInput = document.getElementById('item_is_active');
            const editButtons = Array.from(document.querySelectorAll('.edit-item-btn'));
            const storeItemUrl = @json(route('admin.menu.items.store'));
            const updateItemUrlTemplate = @json(url('admin/menu/items/update/__ID__'));
            const oldFormMode = @json(old('form_mode', 'add'));
            const oldEditItemId = @json(old('edit_item_id', session('edit_item_id')));
            const initialImagePreviewUrl = @json($initialImagePreviewUrl);
            const defaultCategoryId = @json($selectedCategoryId);
            const defaultCategoryName = @json($selectedCategoryName);
            const defaultBranchId = @json($selectedBranchId);
            const defaultBranchName = @json($selectedBranchName);
            const imageInput = document.getElementById('item_image_input');
            const imagePreview = document.getElementById('item_image_preview');
            const imagePlaceholder = document.getElementById('item_image_placeholder');
            const imageLabel = document.getElementById('item_image_label');

            function openModal() {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            function setModalHeader(isEdit) {
                if (modalTitle) modalTitle.textContent = isEdit ? 'Edit Menu Item' : 'Add New Menu Item';
                if (submitBtn) {
                    submitBtn.innerHTML = isEdit ? '<i class="fas fa-save"></i> Update Item' :
                        '<i class="fas fa-save"></i> Save Item';
                }
            }

            function resetToAddMode(resetFields = false) {
                if (itemForm) itemForm.action = storeItemUrl;
                if (formModeInput) formModeInput.value = 'add';
                if (editItemIdInput) editItemIdInput.value = '';
                setModalHeader(false);

                if (resetFields) {
                    itemForm && itemForm.reset();
                    setType('veg');
                    selectCategory(defaultCategoryId, defaultCategoryName);
                    selectBranch(defaultBranchId, defaultBranchName);
                    if (isAvailableInput) isAvailableInput.checked = true;
                    if (isRecommendedInput) isRecommendedInput.checked = false;
                    if (isActiveInput) isActiveInput.checked = true;
                    setImagePreview('');

                    // 🌟 Reset Alpine Fields
                    window.dispatchEvent(new CustomEvent('reset-form'));
                }
            }

            const typeButtons = {
                veg: document.getElementById('btn-veg'),
                'non-veg': document.getElementById('btn-nonveg'),
                egg: document.getElementById('btn-egg'),
            };
            const selectedTypeInput = document.getElementById('selected_item_type');
            const selectedCategoryInput = document.getElementById('selected_category_id');
            const categoryText = document.getElementById('category_display_text');
            const categoryList = document.getElementById('cat_list');
            const selectedBranchInput = document.getElementById('selected_branch_id');
            const branchText = document.getElementById('branch_display_text');
            const branchList = document.getElementById('branch_list');

            function applyTypeStyle(activeType) {
                Object.entries(typeButtons).forEach(([type, button]) => {
                    if (!button) return;
                    if (type === activeType) {
                        button.classList.remove('border-gray-700', 'bg-gray-900', 'text-gray-400');
                        if (type === 'veg') {
                            button.classList.add('border-green-600/30', 'bg-green-600/10', 'text-green-500',
                                'ring-2', 'ring-green-600/50');
                        } else if (type === 'non-veg') {
                            button.classList.add('border-red-600/30', 'bg-red-600/10', 'text-red-400', 'ring-2',
                                'ring-red-600/50');
                        } else {
                            button.classList.add('border-amber-600/30', 'bg-amber-600/10', 'text-amber-400',
                                'ring-2', 'ring-amber-600/50');
                        }
                    } else {
                        button.classList.remove('border-green-600/30', 'bg-green-600/10', 'text-green-500',
                            'ring-green-600/50', 'border-red-600/30', 'bg-red-600/10', 'text-red-400',
                            'ring-red-600/50', 'border-amber-600/30', 'bg-amber-600/10', 'text-amber-400',
                            'ring-amber-600/50', 'ring-2');
                        button.classList.add('border-gray-700', 'bg-gray-900', 'text-gray-400');
                    }
                });
            }

            function setImagePreview(url) {
                if (!imagePreview || !imagePlaceholder || !imageLabel) return;
                if (url) {
                    imagePreview.src = url;
                    imagePreview.classList.remove('hidden');
                    imagePlaceholder.classList.add('hidden');
                    imageLabel.textContent = 'Change Image';
                } else {
                    imagePreview.src = '';
                    imagePreview.classList.add('hidden');
                    imagePlaceholder.classList.remove('hidden');
                    imageLabel.textContent = 'Upload Image';
                }
            }

            if (searchInput && searchForm) {
                let searchTimer = null;

                searchInput.addEventListener('input', () => {
                    if (searchTimer) {
                        window.clearTimeout(searchTimer);
                    }

                    searchTimer = window.setTimeout(() => {
                        searchForm.requestSubmit();
                    }, 300);
                });
            }

            window.setType = function(type) {
                if (selectedTypeInput) selectedTypeInput.value = type;
                applyTypeStyle(type);
            };

            window.selectCategory = function(id, name) {
                if (selectedCategoryInput) selectedCategoryInput.value = id;
                if (categoryText) categoryText.textContent = name;
                if (categoryList) categoryList.classList.add('hidden');
            };

            window.selectBranch = function(id, name) {
                if (selectedBranchInput) selectedBranchInput.value = id;
                if (branchText) branchText.textContent = name;
                if (branchList) branchList.classList.add('hidden');
            };

            applyTypeStyle((selectedTypeInput && selectedTypeInput.value) || 'veg');

            editButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const item = JSON.parse(button.dataset.item || '{}');
                    const updateUrl = button.dataset.updateUrl;

                    if (itemForm && updateUrl) {
                        itemForm.action = updateUrl;
                    }

                    if (formModeInput) formModeInput.value = 'edit';
                    if (editItemIdInput) editItemIdInput.value = item.id || '';
                    if (nameInput) nameInput.value = item.name || '';
                    if (basePriceInput) basePriceInput.value = item.base_price ?? '';
                    if (salePriceInput) salePriceInput.value = item.sale_price ?? '';
                    if (descriptionInput) descriptionInput.value = item.description || '';
                    if (isAvailableInput) isAvailableInput.checked = !!item.is_available;
                    if (isRecommendedInput) isRecommendedInput.checked = !!item.is_recommended;
                    if (isActiveInput) isActiveInput.checked = !!item.is_active;
                    if (imageInput) imageInput.value = '';

                    setType(item.type_value || 'veg');
                    selectCategory(item.category_id ?? '', item.category_name || 'Select Category');
                    selectBranch(item.branch_id ?? '', item.branch_name || 'Global Specific');
                    setImagePreview(item.image_url || '');

                    // 🌟 Dispatch Data to Alpine to populate arrays in Edit Mode
                    window.dispatchEvent(new CustomEvent('edit-item', {
                        detail: {
                            has_variants: !!item.has_variants,
                            variants: item.variants && item.variants.length ? item
                                .variants : [],
                            addons: item.addons && item.addons.length ? item.addons : []
                        }
                    }));

                    setModalHeader(true);
                    openModal();
                });
            });

            imageInput && imageInput.addEventListener('change', (e) => {
                const [file] = e.target.files || [];
                if (!file) {
                    if (formModeInput && formModeInput.value === 'edit') return;
                    setImagePreview('');
                    return;
                }
                const reader = new FileReader();
                reader.onload = (ev) => setImagePreview(ev.target?.result || '');
                reader.readAsDataURL(file);
            });

            if (oldFormMode === 'edit' && oldEditItemId && itemForm) {
                itemForm.action = updateItemUrlTemplate.replace('__ID__', oldEditItemId);
                if (formModeInput) formModeInput.value = 'edit';
                if (editItemIdInput) editItemIdInput.value = oldEditItemId;
                setModalHeader(true);
                setImagePreview(initialImagePreviewUrl || '');
            }

            @if ($errors->any())
                openModal();
            @endif

            openBtn && openBtn.addEventListener('click', () => {
                resetToAddMode(true);
                openModal();
            });
            closeBtn && closeBtn.addEventListener('click', closeModal);
            cancelBtn && cancelBtn.addEventListener('click', closeModal);
            backdrop && backdrop.addEventListener('click', closeModal);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>
@endsection
