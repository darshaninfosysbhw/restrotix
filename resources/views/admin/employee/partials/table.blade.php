<div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-400">All Employees</span>
            <span class="px-2.5 py-1 rounded-full text-xs bg-orange-500/10 text-orange-500 border border-orange-500/30">
                Total : {{ $employeeStats['total'] }}
            </span>
        </div>
        <form id="employeeSearchForm" method="GET" action="{{ route('admin.employee.index') }}"
            class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
            <div class="relative w-full sm:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input id="employeeTableSearch" name="search" type="text" value="{{ request('search') }}"
                    placeholder="Search name, role, branch..."
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.employee.index') }}"
                    class="px-3 py-2 rounded-lg text-xs bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10 transition">
                    Reset
                </a>
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
        </form>
    </div>
    <div class="overflow-x-auto overflow-y-visible">
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-400 border-b border-gray-700 uppercase tracking-wide">
                <tr>
                    <th class="text-left py-3 pr-4 font-medium">#</th>
                    <th class="text-left py-3 px-4 font-medium">Employee</th>
                    <th class="text-left py-3 px-4 font-medium">Role</th>
                    <th class="text-left py-3 px-4 font-medium">Branch</th>
                    <th class="text-left py-3 px-4 font-medium">Contact</th>
                    <th class="text-left py-3 px-4 font-medium">Password</th>
                    <th class="text-left py-3 px-4 font-medium">Shift</th>
                    <th class="text-left py-3 px-4 font-medium">Status</th>
                    <th class="text-left py-3 px-4 font-medium">Joined</th>
                    <th class="text-left py-3 pl-8 font-medium">Action</th>
                </tr>
            </thead>
            <tbody id="employeeTableBody" class="divide-y divide-gray-700/80">
                @foreach ($employees as $index => $employee)
                    <tr class="employee-row hover:bg-white/5 transition">
                        <td class="py-3 pr-4 text-gray-300">
                            {{ !empty($employeesPaginator) && $employeesPaginator->firstItem() ? $employeesPaginator->firstItem() + $index : $index + 1 }}
                        </td>
                        <td class="py-3 px-4">
                            <div>
                                <p class="font-medium text-white">{{ $employee['name'] }}</p>
                                <p class="text-xs text-gray-400">{{ $employee['employee_code'] }}</p>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-white">{{ $employee['role_label'] }}</td>
                        <td class="py-3 px-4 text-gray-300">{{ $employee['branch_name'] }}</td>
                        <td class="py-3 px-4">
                            <p class="text-gray-300">{{ $employee['email'] }}</p>
                            <p class="text-xs text-gray-400">{{ $employee['phone_number'] }}</p>
                        </td>
                        <td class="py-3 px-4">
                            <code class="text-xs bg-white/5 px-2.5 py-1 rounded-full text-gray-300">••••••••</code>
                        </td>
                        <td class="py-3 px-4 text-gray-300">{{ $employee['shift'] }}</td>
                        <td class="py-3 px-4">
                            @if ($employee['status'] === 'Active')
                                <span class="px-2.5 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-400">Active</span>
                            @elseif($employee['status'] === 'Leave')
                                <span class="px-2.5 py-1 rounded-full text-xs bg-amber-400/15 text-amber-400">Leave</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs bg-slate-500/20 text-slate-300">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-gray-300">{{ $employee['joined'] }}</td>
                        <td class="py-3 pl-8">
                            @include('core.components.actions', [
                                'editClass' => 'openEmployeeEditModal',
                                'editData' => [
                                    'update-url' => route('admin.employee.update', $employee['id']),
                                    'name' => $employee['name'],
                                    'email' => $employee['email'],
                                    'phone-number' => $employee['phone_number'],
                                    'pin-code' => $employee['pin_code'],
                                    'role' => $employee['role'],
                                    'branch-id' => $employee['branch_id'],
                                    'designation' => $employee['designation'],
                                    'id-type' => $employee['id_type'],
                                    'id-number' => $employee['id_number'],
                                    'emergency-contact-number' => $employee['emergency_contact_number'],
                                    'current-address' => $employee['current_address'],
                                    'permanent-address' => $employee['permanent_address'],
                                    'base-salary' => $employee['base_salary'],
                                    'bank-name' => $employee['bank_name'],
                                    'account-number' => $employee['account_number'],
                                ],
                                'deleteRoute' => route('admin.employee.destroy', $employee['id']),
                                'deleteConfirm' => 'Are you sure you want to delete this employee?',
                            ])
                        </td>
                    </tr>
                @endforeach
                <tr id="employeeNoResultRow" class="{{ count($employees) ? 'hidden' : '' }}">
                    <td colspan="10" class="py-6 text-center text-sm text-gray-400">No employee found.</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if (!empty($employeesPaginator))
        <div class="mt-4 flex flex-col gap-3 border-t border-gray-700 pt-4 lg:flex-row lg:items-center lg:justify-between">
            <x-core::ui.pagination :paginator="$employeesPaginator" label="employees" />
        </div>
    @endif
</div>
