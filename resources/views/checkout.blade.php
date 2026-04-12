<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestroChain - Register</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .input-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease-in-out;
        }

        .input-box:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.1);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-orange-100 via-orange-200 to-orange-300 min-h-screen flex flex-col font-sans"
    x-data="{
        // 1. Step aur Form Data LocalStorage se (Refresh safe)
        step: localStorage.getItem('resto_step') ? parseInt(localStorage.getItem('resto_step')) : 1,
        formData: localStorage.getItem('resto_form') ? JSON.parse(localStorage.getItem('resto_form')) : {
            full_name: '',
            phone: '',
            email: '',
            restaurant_name: '',
            business_type: '',
            outlets: 1,
            country: 'Nepal',
            state: '',
            city: '',
            pincode: '',
            currency: 'NPR',
            timezone: 'Asia/Kathmandu',
            cuisine: ''
        },
    
        // 2. Plan ki details hamesha Database/URL se (Dynamic)
        plan: '{{ $planDetails->name }}',
        price: '{{ $planDetails->getPriceForCurrency(session('currency_id'))->monthly_price ?? 0 }}',
        symbol: '{{ session('currency_symbol') }}',
        plan_id: '{{ $planDetails->id }}',
        agreed: false,
        loading: false,
    
    
        async submitForm() {
            if (!this.agreed) {
                window.showToast({ type: 'error', message: 'Please agree to the terms first!' });
                return;
            }
    
            this.loading = true;
    
            try {
                const response = await fetch('{{ route('checkout.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ...this.formData,
                        plan_id: this.plan_id,
                        currency_id: '{{ session('currency_id') }}'
                    })
                });
    
                const data = await response.json();
    
                if (!response.ok) {
                    throw new Error(data.message || 'Validation failed. Check your details.');
                }
    
                if (data.success) {
                    window.showToast({
                        type: 'success',
                        message: data.message || 'Restaurant Launched Successfully!',
                        duration: 2000
                    });
    
                    // Cleanup & Redirect
                    setTimeout(() => {
                        localStorage.removeItem('resto_step');
                        localStorage.removeItem('resto_form');
                        window.location.href = data.redirect;
                    }, 1500);
                }
            } catch (err) {
                window.showToast({
                    type: 'error',
                    message: err.message || 'Server unreachable!',
                    duration: 5000
                });
            } finally {
                this.loading = false;
            }
        }
    }" x-init="$watch('step', value => localStorage.setItem('resto_step', value));
    $watch('formData', value => localStorage.setItem('resto_form', JSON.stringify(value)), { deep: true });">

    <header class="p-6">
        <div class="flex items-center gap-2">
            <div class="bg-orange-600 p-2 px-3 rounded-lg shadow-lg shadow-orange-200">
                <i class="fas fa-utensils text-white text-xl"></i>
            </div>
            <a href="{{ url('/') }}" class="text-2xl font-bold text-gray-800 tracking-tight transition">
                RestoChain <span class="text-orange-600">CHECKOUT</span>
            </a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 py-4">
        <div class="max-w-7xl w-full flex flex-col md:flex-row items-center justify-center gap-8 lg:gap-16">

            <div class="w-full md:w-[400px] lg:w-[450px] space-y-6">
                <div class="p-10 text-white relative overflow-hidden">
                    <div class="absolute -top-10 -left-20 w-48 h-48 bg-orange-600/20 blur-[80px] rounded-full"></div>
                    <div class="absolute -bottom-10 -right-20 w-48 h-48 bg-orange-600/20 blur-[80px] rounded-full">
                    </div>
                    <div class="mb-10">
                        <span
                            class="text-[10px] font-bold uppercase tracking-[0.3em] text-orange-500 mb-1 block">Premium
                            Plan</span>
                        <h3 class="text-2xl font-extrabold text-black" x-text="plan"></h3>
                        <p class="text-gray-400 mt-1 text-sm italic">Perfect for standalone restaurants</p>
                    </div>

                    <div class="space-y-4 mb-8">
                        @php
                            // Database se JSON features ko Array mein convert kar rahe hain
                            $features = is_array($planDetails->features)
                                ? $planDetails->features
                                : json_decode($planDetails->features, true);
                        @endphp

                        @if ($features)
                            @foreach ($features as $feature)
                                <div class="flex items-center gap-4 group">
                                    <div
                                        class="w-8 h-8 rounded-full bg-orange-600/10 flex items-center justify-center group-hover:bg-orange-600 transition-colors">
                                        <i class="fas fa-check text-[10px] text-orange-500 group-hover:text-white"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-500">{{ $feature }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="pt-8 border-t border-white/5 flex items-end justify-between">
                        <div>
                            <span class="text-5xl font-black text-black" x-text="symbol + price"></span>
                            <span class="text-gray-500 text-sm ml-1">/ month</span>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-green-500 uppercase tracking-widest mb-1">Trial Period
                            </p>
                            <p class="text-sm font-bold text-black">{{ $planDetails->trial_days }} Days Free</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center gap-8 px-4 opacity-70">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
                        <i class="fas fa-shield-check text-orange-600"></i> SSL SECURE
                    </div>
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
                        <i class="fas fa-clock text-orange-600"></i> CANCEL ANYTIME
                    </div>
                </div>
            </div>

            <div class="w-full md:w-[650px] lg:w-[750px]">
                <div class="bg-white rounded-lg shadow-xl p-6 lg:p-12 border border-white/40">

                    <div class="flex items-center justify-between mb-6 px-1">
                        <template x-for="i in [1,2,3]">
                            <div class="flex items-center flex-1">
                                <div :class="step >= i ? 'bg-orange-600 text-white shadow-lg shadow-orange-200' :
                                    'bg-gray-100 text-gray-400'"
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                                    x-text="i"></div>
                                <div x-show="i < 3" class="flex-1 h-[2px] mx-4"
                                    :class="step > i ? 'bg-orange-600' : 'bg-gray-100'"></div>
                            </div>
                        </template>
                    </div>

                    <div x-show="step === 1" x-transition class="space-y-6">
                        <div class="mb-6">
                            <h2 class="text-2xl font-extrabold text-gray-900 mb-0">Account Setup</h2>
                            <p class="text-[13px] text-gray-500 font-medium">Please enter your details to create your
                                restaurant account.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Full Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" placeholder="Enter Full Name"
                                    class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium"
                                    x-model="formData.full_name" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Phone
                                    Number <span class="text-red-500">*</span></label>
                                <input type="tel" placeholder="+91 00000 00000"
                                    class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium"
                                    x-model="formData.phone" required>
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Email
                                    Address <span class="text-red-500">*</span></label>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <input type="email" placeholder="name@company.com"
                                        class="flex-1 px-4 py-3 rounded-lg input-box outline-none font-medium w-full"
                                        x-model="formData.email" required>
                                    <button
                                        class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 sm:py-0 rounded-lg font-bold text-xs uppercase shadow-md transition-all active:scale-95 whitespace-nowrap w-full sm:w-auto">Send
                                        OTP</button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Verify
                                    OTP</label>
                                <input type="text" placeholder="0 0 0 0 0 0"
                                    class="w-full px-4 py-3 rounded-lg input-box outline-none text-center tracking-[0.5em] font-black"
                                    required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Create
                                    Password <span class="text-red-500">*</span></label>
                                <input type="password" placeholder="••••••••" x-model="formData.password"
                                    class="w-full px-4 py-3 rounded-lg input-box outline-none" required>
                            </div>
                        </div>

                        <button @click="step = 2"
                            class="w-full bg-[#ff743c] hover:bg-[#e65a2b] text-white font-bold py-3 rounded-lg transition-all shadow-xl shadow-orange-100 mt-2 flex items-center justify-center gap-3 group">
                            Continue to Business Details
                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

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
                                <label
                                    class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Country</label>
                                <select class="w-full px-4 py-3 rounded-lg input-box outline-none bg-white font-medium"
                                    x-model="formData.country"
                                    @change="formData.currency=(formData.country
                                    === 'Nepal' ? 'NPR' : 'INR' ); formData.timezone=(formData.country
                                    === 'Nepal' ? 'Asia/Kathmandu' : 'Asia/Kolkata' )">
                                    <option value="Nepal">Nepal</option>
                                    <option value="India">India</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">State/Province</label>
                                <input type="text" placeholder="e.g. Lumbini"
                                    class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium"
                                    x-model="formData.state">
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">City</label>
                                <input type="text" placeholder="e.g. Butwal"
                                    class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium"
                                    x-model="formData.city">
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Pincode/ZIP</label>
                                <input type="text" placeholder="32907"
                                    class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium"
                                    x-model="formData.pincode">
                            </div>

                            <div class="space-y-2 hidden">
                                <label
                                    class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Currency</label>
                                <select class="w-full px-4 py-3 rounded-lg input-box outline-none bg-white font-medium"
                                    x-model="formData.currency">
                                    <option value="NPR">NPR (रू)</option>
                                    <option value="INR">INR (₹)</option>
                                    <option value="USD">USD ($)</option>
                                </select>
                            </div>

                            <div class="space-y-2 hidden">
                                <label
                                    class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Timezone</label>
                                <select class="w-full px-4 py-3 rounded-lg input-box outline-none bg-white font-medium"
                                    x-model="formData.timezone">
                                    <option value="Asia/Kathmandu">(GMT+05:45) Kathmandu</option>
                                    <option value="Asia/Kolkata">(GMT+05:30) India</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button @click="step = 1"
                                class="flex-1 text-gray-500 font-bold hover:text-gray-800 transition">Back</button>
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

                    {{-- <div x-show="step === 3" x-transition class="space-y-5">
                        <div class="mb-6">
                            <h2 class="text-2xl font-extrabold text-gray-900 mb-0">Choose Payment Method</h2>
                            <p class="text-[13px] text-gray-500 font-medium">No charges will be made today. Your card
                                will
                                be debited only after the trial ends.</p>
                        </div>

                        <div class="space-y-4">
                            <label
                                class="flex items-center justify-between p-3 border-2 border-orange-500 bg-orange-50 rounded-lg cursor-pointer">
                                <div class="flex items-center gap-4">
                                    <input type="radio" name="pay" checked class="w-5 h-5 accent-orange-600">
                                    <div>
                                        <p class="font-bold text-gray-900">Secure Online Payment</p>
                                        <p class="text-xs text-orange-600 font-medium italic">Instant activation after
                                            payment</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 text-gray-400 text-xl">
                                    <i class="fab fa-cc-visa"></i>
                                    <i class="fab fa-cc-mastercard"></i>
                                </div>
                            </label>

                            <label
                                class="flex items-center gap-4 p-5 border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="pay" class="w-5 h-5 accent-orange-600">
                                <p class="font-bold text-gray-700">Direct Bank Transfer</p>
                            </label>
                        </div>

                        <div class="flex gap-4 pt-6">
                            <button @click="step = 2" class="flex-1 text-gray-500 font-bold">Back</button>
                            <button @click="submitForm()"
                                class="flex-[2] bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-bold shadow-lg shadow-green-100 transition">
                                Activate {{ $planDetails->trial_days }}-Day Free Trial
                            </button>
                        </div>
                    </div> --}}

                    <div x-show="step === 3" x-transition class="space-y-6">
                        <div class="mb-6 text-center">
                            <h2 class="text-2xl font-extrabold text-gray-900">Ready to Launch?</h2>
                            <p class="text-[13px] text-gray-500 font-medium">Review your plan and start your journey.
                            </p>
                        </div>

                        <div
                            class="bg-orange-50/50 border-2 border-dashed border-orange-200 rounded-2xl p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 font-medium text-sm">Selected Plan:</span>
                                <span class="font-bold text-gray-900" x-text="plan"></span>
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
                                <span class="font-black text-2xl text-orange-600" x-text="symbol + price"></span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 px-2">
                            <input type="checkbox" id="terms"
                                class="mt-1 w-4 h-4 accent-orange-600 cursor-pointer" x-model="agreed">
                            <label for="terms" class="text-xs text-gray-500 leading-relaxed cursor-pointer">
                                I agree to the <a href="#" class="text-orange-600 underline">Terms of Use</a>
                                and <a href="#" class="text-orange-600 underline">Privacy Policy</a>.
                                No credit card required for trial. Enjoy full access!
                            </label>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button @click="step = 2" class="flex-1 text-gray-500 font-bold py-3">Back</button>
                            <button @click="submitForm()" :disabled="!agreed || loading"
                                :class="(!agreed || loading) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-orange-700'"
                                class="flex-[2] bg-orange-600 text-white py-4 rounded-xl font-bold shadow-lg transition-all flex items-center justify-center gap-2">
                                <span x-show="!loading">Launch My Restaurant 🚀</span>
                                <span x-show="loading" class="flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i> Processing...
                                </span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <footer
        class="p-3 flex flex-col md:flex-row justify-between items-center text-[10px] font-bold text-gray-500 uppercase tracking-[0.1em] gap-4">
        <div>© 2026 Restrochain</div>
        <div class="flex gap-8">
            <a href="#" class="hover:text-orange-600 transition">Terms of Use</a>
            <a href="#" class="hover:text-orange-600 transition">Privacy Policy</a>
        </div>
    </footer>
    <x-toast-manager />
</body>

</html>
