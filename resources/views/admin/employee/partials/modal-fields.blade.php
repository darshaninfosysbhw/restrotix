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
            <input type="text" inputmode="decimal" name="base_salary" placeholder="0.00"
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
