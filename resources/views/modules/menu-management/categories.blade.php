@extends('core.layouts.admin')

<style>
    .tree-line {
        position: relative;
    }

    .tree-line::before {
        content: "";
        position: absolute;
        top: 0;
        left: 115px;
        width: 1px;
        height: 100%;
        background: #374151;
    }

    .tree-item::before {
        content: "";
        position: absolute;
        top: 18px;
        left: 72px;
        width: 18px;
        height: 18px;
        border-left: 1px solid #374151;
        border-bottom: 1px solid #374151;
        border-bottom-left-radius: 4px;
    }

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
        background: white;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: 0.3s;
    }

    .toggle:checked::before {
        transform: translateX(18px);
    }

    @media (max-width: 1024px) {
        .tree-line::before {
            left: 88px;
        }

        .tree-item::before {
            left: 56px;
        }
    }

    @media (max-width: 768px) {
        .tree-line::before {
            left: 74px;
        }

        .tree-item::before {
            left: 42px;
            width: 20px;
        }

        .text-sm {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 640px) {
        .tree-line::before {
            left: 67px;
        }

        .tree-item::before {
            left: 35px;
        }
    }
</style>

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Menu Category Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Category Directory</h1>
                    <p class="text-sm text-gray-400 mt-2">Manage your category tree by branch</p>
                </div>

                <button id="openTableModal" type="button"
                    class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium cursor-pointer">
                    <i class="fas fa-plus"></i>
                    Add Category
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total Categories</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-orange-500/15 text-orange-500 flex items-center justify-center">
                    <i class="fas fa-layer-group text-sm"></i>
                </div>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Active</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['active'] ?? 0 }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-green-500/15 flex items-center justify-center text-green-400">
                    <i class="fas fa-check-circle text-sm"></i>
                </div>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Inactive</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['inactive'] ?? 0 }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-red-500/15 flex items-center justify-center text-red-400">
                    <i class="fas fa-times-circle text-sm"></i>
                </div>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Global (All Branch)</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['global'] ?? 0 }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-sky-500/15 flex items-center justify-center text-sky-400">
                    <i class="fas fa-code-branch text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-sm text-gray-400">All Branches</span>
                <span class="px-2.5 py-1 rounded-full text-xs bg-orange-500/10 text-orange-500 border border-orange-500/30">
                    Total : {{ $stats['total'] ?? 0 }}
                </span>
            </div>

            <div class="overflow-x-auto overflow-y-visible">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-400 border-b border-gray-700 uppercase tracking-wide">
                        <tr>
                            <th class="text-left py-3 pr-4 font-medium">#</th>
                            <th class="text-center py-3 px-4 font-medium">Name</th>
                            <th class="text-center py-3 px-4 font-medium">Type</th>
                            <th class="text-center py-3 px-4 font-medium">Visible In Branch</th>
                            <th class="text-center py-3 px-4 font-medium">Status</th>
                            <th class="text-center py-3 pl-8 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody id="branchTableBody" class="divide-y divide-gray-700/80">
                        @forelse ($categories as $parent)
                            @php
                                $parentChildren = $parent['sub_categories'] ?? [];
                                $parentHasChildren = !empty($parentChildren);
                            @endphp
                            <tr class="hover:bg-white/5 transition group">
                                <td class="py-4 px-4">{{ $loop->iteration }}</td>

                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        @if ($parentHasChildren)
                                            <button type="button"
                                                onclick="toggleRow('children-{{ $parent['id'] }}', this)">
                                                <i
                                                    class="fas fa-chevron-right text-gray-500 text-xs transition-transform cursor-pointer"></i>
                                            </button>
                                        @else
                                            <span class="w-[14px]"></span>
                                        @endif

                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400">
                                            <i class="fas fa-list text-xs"></i>
                                        </div>

                                        <span class="font-semibold text-gray-200">{{ $parent['name'] }}
                                        </span><span
                                            class="px-2.5 py-1 rounded-full text-[10px] bg-orange-500/10 text-orange-500 border border-orange-500/30">{{ $parent['code_label'] ?? 'N/A' }}</span>
                                    </div>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-[11px] bg-gray-700/50 text-gray-400 border border-gray-600">
                                        Category
                                    </span>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-[11px] bg-gray-700/50 text-gray-300 border border-gray-600">
                                        {{ $parent['branch_name'] ?? 'All Branches (Global)' }}
                                    </span>
                                </td>

                                <td class="py-4 px-4">
                                    <form method="POST"
                                        action="{{ route('admin.menu.categories.toggle-status', $parent['id']) }}"
                                        class="flex items-center justify-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $parent['is_active'] ? 0 : 1 }}">
                                        <input type="checkbox" class="toggle" {{ $parent['is_active'] ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <span
                                            class="text-xs {{ $parent['is_active'] ? 'text-green-400' : 'text-red-400' }}">
                                            {{ $parent['is_active'] ? 'Active' : 'Inactive' }}
                                        </span>
                                    </form>
                                </td>

                                <td class="py-4 px-6 text-right">
                                    <div class="flex justify-end gap-2  transition">
                                        <button type="button"
                                            class="edit-category-btn px-2.5 py-1.5 rounded-md text-xs bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/20 transition"
                                            data-id="{{ $parent['id'] }}" data-name="{{ $parent['name'] }}"
                                            data-code="{{ $parent['code'] ?? '' }}"
                                            data-parent-id="{{ $parent['parent_id'] }}"
                                            data-branch-id="{{ $parent['branch_id'] }}"
                                            data-sort-order="{{ $parent['sort_order'] }}"
                                            data-is-active="{{ $parent['is_active'] ? '1' : '0' }}"
                                            data-image-url="{{ $parent['image_url'] ?? '' }}">
                                            Edit
                                        </button>
                                        <form method="POST"
                                            action="{{ route('admin.menu.categories.destroy', $parent['id']) }}"
                                            onsubmit="return confirm('Delete this category and all sub-categories?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2.5 py-1.5 rounded-md text-xs bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            @if ($parentHasChildren)
                                <tr id="children-{{ $parent['id'] }}" class="hidden">
                                    <td colspan="7" class="p-0">
                                        <table class="w-full">
                                            <tbody class="tree-line">
                                                @foreach ($parentChildren as $child)
                                                    <tr class="hover:bg-white/5 transition">
                                                        <td class="py-4 px-4"></td>
                                                        <td class="py-4 px-6 pl-21 relative tree-item rounded-full">
                                                            <div class="flex items-center gap-3 ml-2">
                                                                <div
                                                                    class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400">
                                                                    <i class="fas fa-circle text-[8px]"></i>
                                                                </div>
                                                                <span
                                                                    class="text-gray-300">{{ $child['name'] }}</span><span
                                                                    class="px-2.5 py-1 rounded-full text-[10px] bg-orange-500/10 text-orange-500 border border-orange-500/30">{{ $child['code_label'] ?? 'N/A' }}</span>
                                                            </div>
                                                        </td>



                                                        <td class="py-4 px-4 text-center">
                                                            <span
                                                                class="px-3 py-1 rounded-full text-[11px] bg-gray-700/50 text-gray-400 border border-gray-600">
                                                                Sub-category
                                                            </span>
                                                        </td>

                                                        <td class="py-4 px-4 text-center">
                                                            <span
                                                                class="px-3 py-1 rounded-full text-[11px] bg-gray-700/50 text-gray-300 border border-gray-600">
                                                                {{ $child['branch_name'] ?? 'All Branches (Global)' }}
                                                            </span>
                                                        </td>

                                                        <td class="py-4 px-4">
                                                            <form method="POST"
                                                                action="{{ route('admin.menu.categories.toggle-status', $child['id']) }}"
                                                                class="flex items-center justify-center gap-2">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="is_active"
                                                                    value="{{ $child['is_active'] ? 0 : 1 }}">
                                                                <input type="checkbox" class="toggle"
                                                                    {{ $child['is_active'] ? 'checked' : '' }}
                                                                    onchange="this.form.submit()">
                                                                <span
                                                                    class="text-xs {{ $child['is_active'] ? 'text-green-400' : 'text-red-400' }}">
                                                                    {{ $child['is_active'] ? 'Active' : 'Inactive' }}
                                                                </span>
                                                            </form>
                                                        </td>

                                                        <td class="py-4 px-6 text-right">
                                                            <div class="flex justify-end gap-2">
                                                                <button type="button"
                                                                    class="edit-category-btn px-2.5 py-1.5 rounded-md text-xs bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/20 transition"
                                                                    data-id="{{ $child['id'] }}"
                                                                    data-name="{{ $child['name'] }}"
                                                                    data-code="{{ $child['code'] ?? '' }}"
                                                                    data-parent-id="{{ $child['parent_id'] }}"
                                                                    data-branch-id="{{ $child['branch_id'] }}"
                                                                    data-sort-order="{{ $child['sort_order'] }}"
                                                                    data-is-active="{{ $child['is_active'] ? '1' : '0' }}"
                                                                    data-image-url="{{ $child['image_url'] ?? '' }}">
                                                                    {{-- <i class="fas fa-edit"></i> --}}Edit
                                                                </button>
                                                                <form method="POST"
                                                                    action="{{ route('admin.menu.categories.destroy', $child['id']) }}"
                                                                    onsubmit="return confirm('Delete this category?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="px-2.5 py-1.5 rounded-md text-xs bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 transition">
                                                                        {{-- <i class="fas fa-trash"></i> --}}Delete
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-gray-400">No categories found. Add your
                                    first category.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="tableModal" class="fixed inset-0 z-[120] hidden overflow-y-auto">
        <div id="tableModalBackdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-xl bg-gray-800 border border-gray-700 rounded-lg shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center bg-gray-800">
                    <h2 class="text-xl font-bold text-white" id="modalTitle">Add New Category</h2>
                    <button id="closeTableModal" class="text-gray-400 hover:text-white transition cursor-pointer"
                        type="button">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.menu.categories.store') }}" class="p-6 space-y-6"
                    id="tableForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="categoryIdInput" name="id" value="">

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-orange-500/90 uppercase tracking-wider">Category
                            Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="ex. Drinks"
                            class="w-full bg-gray-900 text-sm border border-gray-700 rounded-lg px-3 py-2.5 text-white focus:outline-none focus:ring-1 focus:ring-orange-500/50 transition-all">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-400 uppercase tracking-wider">Parent
                            Category</label>
                        <div class="relative">
                            <select name="parent_id"
                                class="w-full bg-gray-900 text-sm border border-gray-700 rounded-lg px-3 py-2.5 text-white appearance-none focus:outline-none focus:ring-1 focus:ring-orange-500/50 cursor-pointer">
                                <option value="">None (Main Category)</option>
                                @foreach ($parentCategories as $parentCategory)
                                    <option value="{{ $parentCategory->id }}"
                                        {{ old('parent_id') == $parentCategory->id ? 'selected' : '' }}>
                                        {{ $parentCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i
                                class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-400 uppercase tracking-wider">Select
                                    Branch</label>
                                <div class="relative">
                                    <select name="branch_id"
                                        class="w-full bg-gray-900 text-sm border border-gray-700 rounded-lg px-3 py-2.5 text-white appearance-none focus:outline-none focus:ring-1 focus:ring-orange-500/50 cursor-pointer">
                                        <option value="">All Branches (Global)</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i
                                        class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-400 uppercase tracking-wider">Sort
                                    Order</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                                    class="w-full bg-gray-900 text-sm border border-gray-700 rounded-lg px-3 py-2.5 text-white focus:outline-none focus:ring-1 focus:ring-orange-500/50">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-400 uppercase tracking-wider">Code
                                    (Optional)</label>
                                <input type="text" id="codeInputField" name="code" value="{{ old('code') }}"
                                    placeholder="Auto-generated if empty"
                                    class="w-full bg-gray-900 text-sm border border-gray-700 rounded-lg px-3 py-2.5 text-white focus:outline-none focus:ring-1 focus:ring-orange-500/50">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label
                                class="block text-sm font-medium text-gray-400 uppercase tracking-wider text-center md:text-left">Image
                                Upload</label>
                            <div
                                class="relative border-2 border-dashed border-gray-700 rounded-xl p-4 flex flex-col items-center justify-center bg-gray-900 hover:border-orange-500/50 transition-colors group h-[135px]">
                                <input type="file" name="image"
                                    class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                <img id="imagePreview" src="" alt="Category image preview"
                                    class="hidden w-16 h-16 object-cover rounded-full border border-gray-600 mb-2">
                                <div id="imagePlaceholder"
                                    class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-utensils text-2xl text-gray-500"></i>
                                </div>
                                <span class="text-xs text-gray-500">Click to upload image</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 py-2">
                        <input type="hidden" name="is_active" value="0">
                        <input id="isActiveToggle" type="checkbox" name="is_active" value="1" class="toggle"
                            {{ old('is_active', 1) ? 'checked' : '' }}>
                        <span id="statusLabel" class="text-sm font-medium text-gray-300"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-700">
                        <button type="button" id="closeTableModalBtn"
                            class="px-6 py-2.5 rounded-lg text-sm font-semibold bg-transparent hover:bg-white/5 text-gray-400 border border-gray-700 transition flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" id="submitCategoryBtn"
                            class="px-6 py-2.5 rounded-lg text-sm font-semibold bg-orange-600 hover:bg-orange-500 text-white shadow-lg shadow-orange-900/20 transition flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const modal = document.getElementById('tableModal');
            const openBtn = document.getElementById('openTableModal');
            const backdrop = document.getElementById('tableModalBackdrop');
            const closeBtns = document.querySelectorAll('#closeTableModal, #closeTableModalBtn');
            const activeToggle = document.getElementById('isActiveToggle');
            const statusLabel = document.getElementById('statusLabel');
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('tableForm');
            const submitBtn = document.getElementById('submitCategoryBtn');
            const editButtons = document.querySelectorAll('.edit-category-btn');
            const categoryIdInput = document.getElementById('categoryIdInput');
            const nameInput = form ? form.querySelector('input[name=\"name\"]') : null;
            const parentSelect = form ? form.querySelector('select[name=\"parent_id\"]') : null;
            const branchSelect = form ? form.querySelector('select[name=\"branch_id\"]') : null;
            const sortOrderInput = form ? form.querySelector('input[name=\"sort_order\"]') : null;
            const codeInput = document.getElementById('codeInputField');
            const imageInput = form ? form.querySelector('input[name=\"image\"]') : null;
            const imagePreview = document.getElementById('imagePreview');
            const imagePlaceholder = document.getElementById('imagePlaceholder');
            const updateUrlTemplate = @json(route('admin.menu.categories.update', ['id' => '__ID__']));
            const shouldOpenModal =
                {{ $errors->any() || old('name') || old('parent_id') || old('branch_id') || old('sort_order') || old('code') ? 'true' : 'false' }};
            const editCategoryId = @json(session('edit_category_id'));
            let codeManuallyEdited = {{ old('code') ? 'true' : 'false' }};
            let codeSeed = Math.floor(1000 + (Math.random() * 9000));

            function getCodePrefix(name) {
                const cleaned = String(name || '').replace(/[^a-zA-Z0-9 ]/g, '').trim();
                if (!cleaned) {
                    return '';
                }
                return cleaned.substring(0, 3).toUpperCase().padEnd(3, 'X');
            }

            function getAutoCode(name) {
                const prefix = getCodePrefix(name);
                if (!prefix) {
                    return '';
                }
                return prefix + '-' + codeSeed;
            }

            function syncStatusLabel() {
                if (!activeToggle || !statusLabel) {
                    return;
                }

                statusLabel.textContent = activeToggle.checked ? 'Visibility: Active' : 'Visibility: Inactive';
                statusLabel.classList.toggle('text-green-400', activeToggle.checked);
                statusLabel.classList.toggle('text-red-400', !activeToggle.checked);
                statusLabel.classList.toggle('text-gray-300', false);
            }

            function setImagePreview(url) {
                if (!imagePreview || !imagePlaceholder) {
                    return;
                }

                if (url) {
                    imagePreview.src = url;
                    imagePreview.onerror = function() {
                        imagePreview.src = '';
                        imagePreview.classList.add('hidden');
                        imagePlaceholder.classList.remove('hidden');
                    };
                    imagePreview.classList.remove('hidden');
                    imagePlaceholder.classList.add('hidden');
                    return;
                }

                imagePreview.src = '';
                imagePreview.classList.add('hidden');
                imagePlaceholder.classList.remove('hidden');
            }

            function setFormMode(mode, payload) {
                if (!form || !modalTitle || !submitBtn) {
                    return;
                }

                if (mode === 'edit' && payload) {
                    form.action = updateUrlTemplate.replace('__ID__', payload.id);
                    modalTitle.textContent = 'Edit Category';
                    submitBtn.innerHTML = '<i class=\"fas fa-save\"></i> Update Category';
                    if (categoryIdInput) categoryIdInput.value = payload.id || '';
                    if (nameInput) nameInput.value = payload.name || '';
                    if (parentSelect) parentSelect.value = payload.parent_id || '';
                    if (branchSelect) branchSelect.value = payload.branch_id || '';
                    if (sortOrderInput) sortOrderInput.value = payload.sort_order !== '' ? payload.sort_order : 0;
                    if (codeInput) codeInput.value = payload.code || '';
                    codeManuallyEdited = true;
                    if (activeToggle) {
                        activeToggle.checked = payload.is_active === '1';
                        syncStatusLabel();
                    }
                    setImagePreview(payload.image_url || '');
                    return;
                }

                form.action = @json(route('admin.menu.categories.store'));
                modalTitle.textContent = 'Add New Category';
                submitBtn.innerHTML = '<i class=\"fas fa-save\"></i> Save Category';
                if (categoryIdInput) categoryIdInput.value = '';
                if (nameInput) nameInput.value = '';
                if (parentSelect) parentSelect.value = '';
                if (branchSelect) branchSelect.value = '';
                if (sortOrderInput) sortOrderInput.value = 0;
                codeSeed = Math.floor(1000 + (Math.random() * 9000));
                if (codeInput) codeInput.value = nameInput ? getAutoCode(nameInput.value) : '';
                codeManuallyEdited = false;
                setImagePreview('');
                if (activeToggle) {
                    activeToggle.checked = true;
                    syncStatusLabel();
                }
            }

            function openModal() {
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
            }

            function closeModal() {
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            }

            if (openBtn) {
                openBtn.addEventListener('click', function() {
                    setFormMode('create');
                    openModal();
                });
            }

            closeBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeModal();
                });
            });

            if (backdrop) {
                backdrop.addEventListener('click', closeModal);
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });

            if (activeToggle) {
                syncStatusLabel();
                activeToggle.addEventListener('change', syncStatusLabel);
            }

            if (nameInput && codeInput) {
                nameInput.addEventListener('input', function() {
                    if (codeManuallyEdited) {
                        return;
                    }
                    codeInput.value = getAutoCode(nameInput.value);
                });

                codeInput.addEventListener('input', function() {
                    const currentValue = codeInput.value.trim();
                    if (!currentValue) {
                        codeManuallyEdited = false;
                        codeInput.value = getAutoCode(nameInput.value);
                        return;
                    }
                    codeManuallyEdited = true;
                });
            }

            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    const file = imageInput.files && imageInput.files[0] ? imageInput.files[0] : null;
                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        setImagePreview(e.target && e.target.result ? e.target.result : '');
                    };
                    reader.readAsDataURL(file);
                });
            }

            editButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    setFormMode('edit', {
                        id: btn.getAttribute('data-id') || '',
                        name: btn.getAttribute('data-name') || '',
                        code: btn.getAttribute('data-code') || '',
                        parent_id: btn.getAttribute('data-parent-id') || '',
                        branch_id: btn.getAttribute('data-branch-id') || '',
                        sort_order: btn.getAttribute('data-sort-order') || '',
                        is_active: btn.getAttribute('data-is-active') || '0',
                        image_url: btn.getAttribute('data-image-url') || ''
                    });
                    openModal();
                });
            });

            if (shouldOpenModal) {
                if (editCategoryId) {
                    const editBtn = document.querySelector('.edit-category-btn[data-id=\"' + editCategoryId + '\"]');
                    if (editBtn) {
                        setFormMode('edit', {
                            id: editBtn.getAttribute('data-id') || '',
                            name: editBtn.getAttribute('data-name') || '',
                            code: editBtn.getAttribute('data-code') || '',
                            parent_id: editBtn.getAttribute('data-parent-id') || '',
                            branch_id: editBtn.getAttribute('data-branch-id') || '',
                            sort_order: editBtn.getAttribute('data-sort-order') || '',
                            is_active: editBtn.getAttribute('data-is-active') || '0',
                            image_url: editBtn.getAttribute('data-image-url') || ''
                        });
                    }
                } else if (nameInput && codeInput && !codeInput.value.trim()) {
                    codeInput.value = getAutoCode(nameInput.value);
                }
                openModal();
            } else if (nameInput && codeInput && !codeInput.value.trim()) {
                codeInput.value = getAutoCode(nameInput.value);
            }
        })();

        function toggleRow(id, btn) {
            const el = document.getElementById(id);
            if (!el) {
                return;
            }

            el.classList.toggle('hidden');

            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.toggle('rotate-90');
            }
        }
    </script>
@endsection
