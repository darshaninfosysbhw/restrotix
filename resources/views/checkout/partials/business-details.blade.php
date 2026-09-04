<div x-show="step === 2" x-transition class="space-y-6">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 mb-0">Business Details</h2>
        <p class="text-[13px] text-gray-500 font-medium">Tell us more about your restaurant setup.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="space-y-2 md:col-span-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Restaurant
                Name <span class="text-red-500">*</span></label>
            <input type="text" placeholder="e.g. Grand Plaza"
                class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium"
                x-model="formData.restaurant_name" required>
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Business
                Type</label>
            <select class="w-full px-4 py-3 rounded-lg input-box outline-none bg-white font-medium"
                x-model="formData.business_type">
                <option value="">Select Type</option>
                <option>Fine Dining</option>
                <option>Quick Service (QSR)</option>
                <option>Cafe / Bakery</option>
                <option>Cloud Kitchen</option>
            </select>
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">No. of
                Outlets</label>
            <input type="number" placeholder="1" min="1"
                class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium"
                x-model="formData.outlets">
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Country</label>
            <select class="w-full px-4 py-3 rounded-lg input-box outline-none bg-white font-medium"
                x-model="formData.country"
                @change="formData.currency=(formData.country === 'Nepal' ? 'NPR' : 'INR' ); formData.timezone=(formData.country === 'Nepal' ? 'Asia/Kathmandu' : 'Asia/Kolkata' )">
                <option value="Nepal">Nepal</option>
            </select>
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">State/Province</label>
            <input type="text" placeholder="e.g. Lumbini"
                class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium"
                x-model="formData.state">
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">City</label>
            <input type="text" placeholder="e.g. Butwal"
                class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium"
                x-model="formData.city">
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Pincode/ZIP</label>
            <input type="text" placeholder="32907"
                class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium"
                x-model="formData.pincode">
        </div>

        <div class="space-y-2 hidden">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Currency</label>
            <select class="w-full px-4 py-3 rounded-lg input-box outline-none bg-white font-medium"
                x-model="formData.currency">
                <option value="NPR">NPR (रू)</option>
                <option value="INR">INR (₹)</option>
                <option value="USD">USD ($)</option>
            </select>
        </div>

        <div class="space-y-2 hidden">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Timezone</label>
            <select class="w-full px-4 py-3 rounded-lg input-box outline-none bg-white font-medium"
                x-model="formData.timezone">
                <option value="Asia/Kathmandu">(GMT+05:45) Kathmandu</option>
                <option value="Asia/Kolkata">(GMT+05:30) India</option>
            </select>
        </div>
    </div>

    <div class="flex gap-4 pt-4">
        <button @click="step = 1" class="flex-1 text-gray-500 font-bold hover:text-gray-800 transition">Back</button>
        <button
            @click="
            let step2Inputs = $el.closest('[x-show]').querySelectorAll('input[required], select[required]');
            let isValid = true;
            step2Inputs.forEach(input => {
                if(!input.value) { isValid = false; input.classList.add('border-red-500'); }
                else { input.classList.remove('border-red-500'); }
            });
            if(isValid) step = 3;
        "
            class="flex-[2] bg-orange-600 text-white py-3 rounded-lg font-bold shadow-lg hover:bg-orange-700 transition">
            Continue to Payment
        </button>
    </div>
</div>
