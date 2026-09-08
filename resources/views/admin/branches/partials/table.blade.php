<div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-400">All Branches</span>
            <span class="px-2.5 py-1 rounded-full text-xs bg-orange-500/10 text-orange-500 border border-orange-500/30">
                Total : {{ $stats['total'] }}
            </span>
        </div>
        <form id="branchSearchForm" method="GET" action="{{ route('admin.branches.index') }}"
            class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
            <div class="relative w-full sm:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input id="branchTableSearch" name="search" type="text" value="{{ request('search') }}"
                    placeholder="Search branch, manager, city..."
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.branches.index') }}"
                    class="px-3 py-2 rounded-lg text-xs bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10 transition cursor-pointer">
                    Reset
                </a>
                <button type="button"
                    class="px-3 py-2 rounded-lg text-xs bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-400 border border-emerald-500/25 transition inline-flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-file-import"></i>
                    Import
                </button>
                <button type="button"
                    class="px-3 py-2 rounded-lg text-xs bg-sky-500/15 hover:bg-sky-500/25 text-sky-400 border border-sky-500/25 transition inline-flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-file-export"></i>
                    Export
                </button>
            </div>
        </form>
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
                        <td class="py-3 pr-4 text-gray-300">
                            {{ !empty($branchesPaginator) && $branchesPaginator->firstItem() ? $branchesPaginator->firstItem() + $index : $index + 1 }}
                        </td>
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
                        <td class="py-3 px-4 text-center">
                            <span
                                class="bg-orange-500/15 border border-orange-500/30 text-gray-200 px-2.5 py-1.5 rounded-full text-xs">
                                {{ $branch['employees'] }}
                            </span>
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
                        <td class="py-3 pl-8 ">
                            @include('core.components.actions', [
                                'editClass' => 'openBranchEditModal',
                                'editData' => [
                                    'update-url' => route('admin.branches.update', $branch['id']),
                                    'branch-name' => $branch['name'],
                                    'contact-number' => $branch['contact_number'],
                                    'branch-email' => $branch['branch_email'] ?? '',
                                    'country-code' => $branch['country_code'] ?? 'Ind',
                                    'city' => $branch['city'] ?? '',
                                    'state' => $branch['state'] ?? '',
                                    'pincode' => $branch['pincode'] ?? '',
                                    'full-address' => $branch['full_address'] ?? '',
                                    'tax-setting' => $branch['tax_setting'] ?? 'exclusive',
                                    'tax-rate' => $branch['tax_rate'] ?? 5.0,
                                    'offline-billing-enabled' => !empty($branch['offline_billing_enabled'])
                                        ? '1'
                                        : '0',
                                ],
                                'deleteRoute' => route('admin.branches.destroy', $branch['id']),
                                'deleteConfirm' => 'Are you sure you want to delete this branch?',
                            ])
                        </td>
                    </tr>
                @endforeach
                <tr id="branchNoResultRow" class="{{ count($branches) ? 'hidden' : '' }}">
                    <td colspan="9" class="py-6 text-center text-sm text-gray-400">No branch found.</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if (!empty($branchesPaginator))
        <div class="mt-4 flex flex-col gap-3 border-t border-gray-700 pt-4 lg:flex-row lg:items-center lg:justify-between">
            <x-core::ui.pagination :paginator="$branchesPaginator" label="branches" />
        </div>
    @endif
</div>
