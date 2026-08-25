<div x-show="step === 3" x-transition class="space-y-6">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-extrabold text-gray-900">Ready to Launch?</h2>
        <p class="text-[13px] text-gray-500 font-medium">Review your plan and start your journey.
        </p>
    </div>

    <div class="bg-orange-50/50 border-2 border-dashed border-orange-200 rounded-2xl p-6 space-y-4">
        <div class="flex justify-between items-center">
            <span class="text-gray-600 font-medium text-sm">Selected Plan:</span>
            <span class="font-bold text-gray-900" x-text="plan"></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600 font-medium text-sm">Billing Cycle:</span>
            <span class="font-bold text-gray-900" x-text="billingCycle === 'yearly' ? 'Yearly' : 'Monthly'"></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600 font-medium text-sm">Trial Duration:</span>
            <span class="font-bold text-green-600">14 Days (Free)</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600 font-medium text-sm">First Billing Date:</span>
            <span class="font-bold text-gray-800">
                {{ now()->addDays(14)->format('d M, Y') }}
            </span>
        </div>
        <hr class="border-orange-100">
        <div class="flex justify-between items-center">
            <span class="text-gray-800 font-bold text-lg">Payable Today:</span>
            <span class="font-black text-2xl text-orange-600" x-text="symbol + ' ' + price"></span>
            <span class="text-gray-500 text-sm ml-2" x-text="billingCycle === 'yearly' ? '/ year' : '/ month'"></span>
        </div>
    </div>

    <div class="flex items-start gap-3 px-2">
        <input type="checkbox" id="terms" class="mt-1 w-4 h-4 accent-orange-600 cursor-pointer" x-model="agreed">
        <label for="terms" class="text-xs text-gray-500 leading-relaxed cursor-pointer">
            I agree to the <a href="#" class="text-orange-600 underline">Terms of Use</a>
            and <a href="#" class="text-orange-600 underline">Privacy Policy</a>.
            No credit card required for trial. Enjoy full access!
        </label>
    </div>

    <div class="flex gap-4 pt-4">
        <button @click="step = 2" class="flex-1 text-gray-500 font-bold py-3">Back</button>
        <button @click="submitForm()" :disabled="!agreed || loading || !otpVerified"
            :class="(!agreed || loading || !otpVerified) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-orange-700'"
            class="flex-[2] bg-orange-600 text-white py-4 rounded-xl font-bold shadow-lg transition-all flex items-center justify-center gap-2">
            <span x-show="!loading">Launch My Restaurant 🚀</span>
            <span x-show="loading" class="flex items-center gap-2">
                <i class="fas fa-spinner fa-spin"></i> Processing...
            </span>
        </button>
    </div>
</div>
