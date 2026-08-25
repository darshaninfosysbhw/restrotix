<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs text-gray-400 mb-1.5 font-medium">Branch Name <span
                class="text-orange-500">*</span></label>
        <input id="branchName" type="text" name="branch_name" required placeholder="e.g. Thamel Outlet"
            value="{{ old('branch_name') }}"
            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1.5 font-medium">Contact Number <span
                class="text-orange-500">*</span></label>
        <input id="contactNumber" type="text" name="contact_number" required placeholder="+977-98XXXXXXXX"
            value="{{ old('contact_number') }}"
            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1.5 font-medium">Branch Email</label>
        <input id="branchEmail" type="email" name="branch_email" placeholder="e.g. branch@resto.com"
            value="{{ old('branch_email') }}"
            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1.5 font-medium">Country</label>
        <select id="countryCode" name="country_code"
            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
            <option value="Ind" {{ old('country_code', 'Ind') === 'Ind' ? 'selected' : '' }}>India</option>
            <option value="Nep" {{ old('country_code') === 'Nep' ? 'selected' : '' }}>Nepal</option>
            <option value="UAE" {{ old('country_code') === 'UAE' ? 'selected' : '' }}>UAE</option>
        </select>
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1.5 font-medium">State</label>
        <input id="state" type="text" name="state" placeholder="State" value="{{ old('state') }}"
            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
    </div>



    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs text-gray-400 mb-1.5 font-medium">City <span
                    class="text-orange-500">*</span></label>
            <input id="city" type="text" name="city" required placeholder="e.g. Kathmandu"
                value="{{ old('city') }}"
                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
        </div>

        <div>
            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Pincode</label>
            <input id="pincode" type="text" name="pincode" placeholder="Pincode" value="{{ old('pincode') }}"
                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
        </div>
    </div>


</div>

<div class="mt-4">
    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Full Address</label>
    <textarea id="fullAddress" name="full_address" rows="2" placeholder="Street, Landmark, etc."
        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">{{ old('full_address') }}</textarea>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 border-t border-gray-700 pt-4">
    <div>
        <label class="text-xs font-bold text-orange-400 uppercase tracking-wider block mb-1.5">Tax Calculation
            Mode</label>
        <select id="taxSetting" name="tax_setting"
            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-sm text-white outline-none focus:border-orange-500 transition">
            <option value="exclusive" {{ old('tax_setting', 'exclusive') === 'exclusive' ? 'selected' : '' }}>
                Exclusive (Tax added on Top of Menu Price)
            </option>
            <option value="inclusive" {{ old('tax_setting') === 'inclusive' ? 'selected' : '' }}>
                Inclusive (Tax Included inside Menu Price)
            </option>
        </select>
    </div>
    <div>
        <label class="text-xs font-bold text-orange-400 uppercase tracking-wider block mb-1.5">Tax Percentage Rate
            (%)</label>
        <input id="taxRate" type="text" inputmode="decimal" name="tax_rate" value="{{ old('tax_rate', 5.0) }}"
            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-sm text-white outline-none focus:border-orange-500 transition"
            placeholder="e.g. 5.00 or 13.00">
    </div>
</div>
<p class="text-[11px] text-gray-500 mt-1 md:col-span-2">Configure local compliance records globally (e.g., 5% GST for
    India, 13% VAT for Nepal).</p>

<div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-700 mt-4">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded bg-orange-500/10 text-orange-500 flex items-center justify-center text-xs">
            <i class="fas fa-wifi"></i>
        </div>
        <div>
            <p class="text-xs font-medium text-white">Offline Billing</p>
            <p class="text-[10px] text-gray-500">Enable POS even without internet</p>
        </div>
    </div>
    <label class="relative inline-flex items-center cursor-pointer">
        <input id="offlineBillingEnabled" type="checkbox" name="offline_billing_enabled" value="1"
            class="sr-only peer" {{ old('offline_billing_enabled') ? 'checked' : '' }}>
        <div
            class="w-9 h-5 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:!bg-orange-500">
        </div>
    </label>
</div>
