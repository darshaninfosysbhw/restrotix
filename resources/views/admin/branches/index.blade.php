@extends('core.layouts.admin')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Branch Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Restaurant Branches</h1>
                    <p class="text-sm text-gray-400 mt-2">All branches are listed here. Use the form modal to add or update
                        branch details.</p>
                </div>
                <button id="openBranchModal" type="button"
                    class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-plus"></i>
                    Add New Branch
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Total Branches</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-orange-500/15 text-orange-500 flex items-center justify-center">
                        <i class="fas fa-code-branch text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Active</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-emerald-500/15 text-emerald-400 flex items-center justify-center">
                        <i class="fas fa-check-circle text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Under Setup</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['setup'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-amber-400/15 text-amber-400 flex items-center justify-center">
                        <i class="fas fa-tools text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Inactive</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-slate-500/20 text-slate-300 flex items-center justify-center">
                        <i class="fas fa-pause-circle text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400">All Branches</span>
                    <span
                        class="px-2.5 py-1 rounded-full text-xs bg-orange-500/10 text-orange-500 border border-orange-500/30">
                        Total : {{ $stats['total'] }}
                    </span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
                    <div class="relative w-full sm:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input id="branchTableSearch" type="text" placeholder="Search branch, manager, city..."
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button id="branchSearchReset" type="button"
                            class="px-3 py-2 rounded-lg text-xs bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10 transition">
                            Reset
                        </button>
                        <button type="button"
                            class="px-3 py-2 rounded-lg text-xs bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-400 border border-emerald-500/25 transition inline-flex items-center gap-1.5">
                            <i class="fas fa-file-import"></i>
                            Import
                        </button>
                        <button type="button"
                            class="px-3 py-2 rounded-lg text-xs bg-sky-500/15 hover:bg-sky-500/25 text-sky-400 border border-sky-500/25 transition inline-flex items-center gap-1.5">
                            <i class="fas fa-file-export"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto overflow-y-visible">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-400 border-b border-gray-700 uppercase tracking-wide">
                        <tr>
                            <th class="text-left py-3 pr-4 font-medium">#</th>
                            <th class="text-left py-3 px-4 font-medium">Branch</th>
                            <th class="text-left py-3 px-4 font-medium">Manager</th>
                            <th class="text-left py-3 px-4 font-medium">Contact</th>
                            <th class="text-left py-3 px-4 font-medium">City</th>
                            <th class="text-left py-3 px-4 font-medium">Employees</th>
                            <th class="text-left py-3 px-4 font-medium">Status</th>
                            <th class="text-left py-3 px-4 font-medium">Created</th>
                            <th class="text-left py-3 pl-8 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody id="branchTableBody" class="divide-y divide-gray-700/80">
                        @foreach ($branches as $index => $branch)
                            <tr class="branch-row hover:bg-white/5 transition">
                                <td class="py-3 pr-4 text-gray-300">{{ $index + 1 }}</td>
                                <td class="py-3 px-4">
                                    <div>
                                        <p class="font-medium text-white">{{ $branch['name'] }}</p>
                                        <p class="text-xs text-gray-400">Code: {{ $branch['code'] }}</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-white">{{ $branch['manager_name'] }}</td>
                                <td class="py-3 px-4">
                                    <p class="text-gray-300">{{ $branch['manager_email'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $branch['contact_number'] }}</p>
                                </td>
                                <td class="py-3 px-4 text-gray-300">{{ $branch['city'] }}</td>
                                <td class="py-3 px-4"><span
                                        class="bg-white/5 px-2.5 py-1 rounded-full text-xs">{{ $branch['employees'] }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($branch['status'] === 'Active')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-400">Active</span>
                                    @elseif($branch['status'] === 'Setup')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-amber-400/15 text-amber-400">Setup</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-slate-500/20 text-slate-300">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-300">{{ $branch['created'] }}</td>
                                <td class="py-3 pl-8">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            class="px-2.5 py-1.5 rounded-md text-xs bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/20 transition">Edit</button>
                                        <button type="button"
                                            class="px-2.5 py-1.5 rounded-md text-xs bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 transition">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="branchNoResultRow" class="{{ $stats['total'] ? 'hidden' : '' }}">
                            <td colspan="9" class="py-6 text-center text-sm text-gray-400">No branch found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="branchModal" class="fixed inset-0 z-[120] hidden overflow-y-auto">
        <div id="branchModalBackdrop" class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 min-h-screen flex items-start md:items-center justify-center p-4">
            <div
                class="w-full max-w-3xl max-h-[calc(100dvh-2rem)] overflow-y-auto bg-gray-800 border border-gray-700 rounded-2xl">
                <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Add New Branch</h2>
                        <p class="text-xs text-gray-400 mt-1">Fill details to create a new branch entry</p>
                    </div>
                    <button id="closeBranchModal" type="button"
                        class="w-8 h-8 rounded-md bg-white/5 hover:bg-white/10 text-gray-300 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.branches.store') }}" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Branch Name <span
                                    class="text-orange-500">*</span></label>
                            <input type="text" name="branch_name" required placeholder="e.g. Thamel Outlet"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Contact Number <span
                                    class="text-orange-500">*</span></label>
                            <input type="text" name="contact_number" required placeholder="+977-98XXXXXXXX"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Branch Email</label>
                            <input type="email" name="branch_email" placeholder="e.g. branch@resto.com"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Country</label>
                            <select name="country_code"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
                                <option value="Ind">India</option>
                                <option value="Nep">Nepal</option>
                                <option value="UAE">UAE</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5 font-medium">City <span
                                        class="text-orange-500">*</span></label>
                                <input type="text" name="city" required placeholder="e.g. Kathmandu"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
                            </div>

                            <div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">State</label>
                                    <input type="text" name="state" placeholder="State"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
                                </div>

                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Pincode</label>
                            <input type="text" name="pincode" placeholder="Pincode"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5 font-medium">Full Address</label>
                        <textarea name="full_address" rows="2" placeholder="Street, Landmark, etc."
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition"></textarea>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-700">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded bg-orange-500/10 text-orange-500 flex items-center justify-center text-xs">
                                <i class="fas fa-wifi"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-white">Offline Billing</p>
                                <p class="text-[10px] text-gray-500">Enable POS even without internet</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="offline_billing_enabled" value="1" class="sr-only peer">
                            <div
                                class="w-9 h-5 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-orange-500">
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button id="cancelBranchModal" type="button"
                            class="px-4 py-2.5 rounded-lg text-sm bg-white/5 hover:bg-white/10 text-gray-300 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 rounded-lg text-sm bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 transition">
                            <i class="fas fa-save mr-2"></i> Save Branch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .offline-toggle-input:checked+.offline-toggle-track {
            background-color: rgb(249 115 22);
        }

        .offline-toggle-input:checked+.offline-toggle-track+.offline-toggle-knob {
            transform: translateX(1rem);
        }
    </style>

    <script>
        (function() {
            const modal = document.getElementById('branchModal');
            const openBtn = document.getElementById('openBranchModal');
            const closeBtn = document.getElementById('closeBranchModal');
            const cancelBtn = document.getElementById('cancelBranchModal');
            const backdrop = document.getElementById('branchModalBackdrop');
            const searchInput = document.getElementById('branchTableSearch');
            const resetBtn = document.getElementById('branchSearchReset');
            const rows = Array.from(document.querySelectorAll('.branch-row'));
            const emptyRow = document.getElementById('branchNoResultRow');

            function openModal() {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            function filterRows(query) {
                const needle = query.trim().toLowerCase();
                let visible = 0;

                rows.forEach((row) => {
                    const text = row.textContent.toLowerCase();
                    const matched = !needle || text.includes(needle);
                    row.style.display = matched ? '' : 'none';
                    if (matched) visible += 1;
                });

                if (emptyRow) {
                    emptyRow.classList.toggle('hidden', visible !== 0);
                }
            }

            openBtn && openBtn.addEventListener('click', openModal);
            closeBtn && closeBtn.addEventListener('click', closeModal);
            cancelBtn && cancelBtn.addEventListener('click', closeModal);
            backdrop && backdrop.addEventListener('click', closeModal);

            searchInput && searchInput.addEventListener('input', (e) => {
                filterRows(e.target.value);
            });

            resetBtn && resetBtn.addEventListener('click', () => {
                if (searchInput) searchInput.value = '';
                filterRows('');
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>
@endsection
