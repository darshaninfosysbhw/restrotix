@extends('core.layouts.admin')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Employee Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Staff Directory</h1>
                    <p class="text-sm text-gray-400 mt-2">All staff members are listed here. Use the modal form to add or
                        update employee details.</p>
                </div>
                <button id="openEmployeeModal" type="button"
                    class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-user-plus"></i>
                    Add New Employee
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Total Employees</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $employeeStats['total'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-orange-500/15 text-orange-500 flex items-center justify-center">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">On Duty</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $employeeStats['active'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-emerald-500/15 text-emerald-400 flex items-center justify-center">
                        <i class="fas fa-check-circle text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">On Leave</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $employeeStats['on_leave'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-amber-400/15 text-amber-400 flex items-center justify-center">
                        <i class="fas fa-calendar-minus text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Inactive</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $employeeStats['inactive'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-slate-500/20 text-slate-300 flex items-center justify-center">
                        <i class="fas fa-user-slash text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400">All Employees</span>
                    <span
                        class="px-2.5 py-1 rounded-full text-xs bg-orange-500/10 text-orange-500 border border-orange-500/30">
                        Total : {{ $employeeStats['total'] }}
                    </span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
                    <div class="relative w-full sm:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input id="employeeTableSearch" type="text" placeholder="Search name, role, branch..."
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button id="employeeSearchReset" type="button"
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
                                <td class="py-3 pr-4 text-gray-300">{{ $index + 1 }}</td>
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
                                <td class="py-3 px-4"><code
                                        class="text-xs bg-white/5 px-2.5 py-1 rounded-full text-gray-300">••••••••</code>
                                </td>
                                <td class="py-3 px-4 text-gray-300">{{ $employee['shift'] }}</td>
                                <td class="py-3 px-4">
                                    @if ($employee['status'] === 'Active')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-400">Active</span>
                                    @elseif($employee['status'] === 'Leave')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-amber-400/15 text-amber-400">Leave</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-slate-500/20 text-slate-300">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-300">{{ $employee['joined'] }}</td>
                                <td class="py-3 pl-8">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            class="px-2.5 py-1.5 rounded-md text-xs bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/20 transition">Edit</button>
                                        <button type="button"
                                            class="px-2.5 py-1.5 rounded-md text-xs bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 transition">Remove</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="employeeNoResultRow" class="{{ $employeeStats['total'] ? 'hidden' : '' }}">
                            <td colspan="10" class="py-6 text-center text-sm text-gray-400">No employee found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="employeeModal" class="fixed inset-0 z-[120] hidden overflow-y-auto">
        <div id="employeeModalBackdrop" class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 min-h-screen flex items-start md:items-center justify-center p-4">
            <div
                class="w-full max-w-3xl max-h-[calc(100dvh-2rem)] overflow-y-auto bg-gray-800 border border-gray-700 rounded-2xl">
                <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Add New Employee</h2>
                        <p class="text-xs text-gray-400 mt-1">Fill details to create a new employee entry</p>
                    </div>
                    <button id="closeEmployeeModal" type="button"
                        class="w-8 h-8 rounded-md bg-white/5 hover:bg-white/10 text-gray-300 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.employee.store') }}" method="POST"
                    class="p-5 space-y-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                    @csrf

                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-1 h-4 bg-orange-500 rounded-full"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Account & Security</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Employee Name</label>
                                <input type="text" name="name" required placeholder="e.g. Anuj Singh"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Email (Login ID)</label>
                                <input type="email" name="email" required placeholder="employee@example.com"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Phone</label>
                                <input type="text" name="phone_number" required placeholder="e.g. 9876543210"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1.5">POS Login PIN </label>
                                    <input type="text" name="pin_code" maxlength="6" placeholder="Ex: 123456"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-orange-500 font-mono placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1.5">Password</label>
                                    <input type="password" name="password" placeholder="••••••••"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-1 h-4 bg-blue-500 rounded-full"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Employment Details</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Role</label>
                                <select name="role"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    <option value="waiter">Waiter</option>
                                    <option value="chef">Chef</option>
                                    <option value="cashier">Cashier</option>
                                    <option value="manager">Branch Manager</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Branch</label>
                                <select name="branch_id"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Designation</label>
                                <input type="text" name="designation" placeholder="e.g. Senior Captain"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>

                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-1 h-4 bg-emerald-500 rounded-full"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">KYC & Verification</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1.5">ID Type</label>
                                    <select name="id_type"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                        <option>Citizenship</option>
                                        <option>Aadhar</option>
                                        <option>PAN</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1.5">ID Number</label>
                                    <input type="text" name="id_number" placeholder="Number"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Emergency Contact</label>
                                <input type="text" name="emergency_contact_number" placeholder="Phone Number"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Current Address</label>
                                <input type="text" name="current_address" placeholder="Street, City"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Permanent Address</label>
                                <input type="text" name="permanent_address" placeholder="As per ID proof"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-1 h-4 bg-purple-500 rounded-full"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Salary & Bank Details</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Base Salary</label>
                                <input type="number" name="base_salary" placeholder="0.00"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Bank Name</label>
                                <input type="text" name="bank_name" placeholder="e.g. NIC Asia"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Account Number</label>
                                <input type="text" name="account_number" placeholder="Account No."
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
                        <button id="cancelEmployeeModal" type="button"
                            class="px-5 py-2.5 rounded-lg text-sm bg-white/5 hover:bg-white/10 text-gray-300 transition duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-lg text-sm bg-orange-500 hover:bg-orange-600 text-white font-semibold shadow-lg shadow-orange-500/20 transition duration-200">
                            Save Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const modal = document.getElementById('employeeModal');
            const openBtn = document.getElementById('openEmployeeModal');
            const closeBtn = document.getElementById('closeEmployeeModal');
            const cancelBtn = document.getElementById('cancelEmployeeModal');
            const backdrop = document.getElementById('employeeModalBackdrop');
            const searchInput = document.getElementById('employeeTableSearch');
            const resetBtn = document.getElementById('employeeSearchReset');
            const rows = Array.from(document.querySelectorAll('.employee-row'));
            const emptyRow = document.getElementById('employeeNoResultRow');

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
