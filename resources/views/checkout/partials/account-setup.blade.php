<div x-show="step === 1" x-transition class="space-y-6">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-900 mb-0">Account Setup</h2>
        <p class="text-[13px] text-gray-500 font-medium">Please enter your details to create your restaurant account.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="space-y-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Full Name
                <span class="text-red-500">*</span></label>
            <input type="text" placeholder="Enter Full Name"
                class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium" x-model="formData.full_name"
                required>
        </div>
        <div class="space-y-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Phone
                Number <span class="text-red-500">*</span></label>
            <input type="tel" placeholder="+977 00000 00000"
                class="w-full px-4 py-3 rounded-lg input-box outline-none font-medium" x-model="formData.phone"
                required>
        </div>
        <div class="space-y-2 md:col-span-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Email
                Address <span class="text-red-500">*</span></label>
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="email" placeholder="name@company.com"
                    class="flex-1 px-4 py-3 rounded-lg input-box outline-none font-medium w-full" x-model="formData.email"
                    required>
                <button
                    type="button" @click="sendOtp()" :disabled="otpSending || otpResendCooldown > 0 || !formData.email"
                    :class="(otpSending || otpResendCooldown > 0 || !formData.email) ? 'opacity-60 cursor-not-allowed' : ''"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 sm:py-0 rounded-lg font-bold text-xs uppercase shadow-md transition-all active:scale-95 whitespace-nowrap w-full sm:w-auto">
                    <span x-show="!otpSending && otpResendCooldown === 0">Send OTP</span>
                    <span x-show="!otpSending && otpResendCooldown > 0">
                        Resend in <span x-text="formatOtpCountdown(otpResendCooldown)"></span>
                    </span>
                    <span x-show="otpSending">Sending...</span>
                </button>
            </div>
            <div class="text-xs font-medium mt-1" x-show="otpRequested || otpVerified"
                :class="otpVerified ? 'text-green-600' : 'text-orange-600'">
                <span x-show="otpVerified">Email verified successfully.</span>
                <span x-show="!otpVerified">OTP sent to <span x-text="otpEmail"></span></span>
            </div>
        </div>
        <div class="space-y-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Verify
                OTP</label>
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" placeholder="0 0 0 0 0 0" maxlength="6" inputmode="numeric" autocomplete="one-time-code" x-model="otpCode"
                    @paste.prevent="otpCode = $event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6)"
                    @input="otpCode = otpCode.replace(/\D/g, '').slice(0, 6)"
                    class="flex-1 w-full px-4 py-3 rounded-lg input-box outline-none text-center tracking-[0.5em] font-black"
                    required>
                <button x-cloak x-show="otpRequested" type="button" @click="verifyOtp()"
                    :disabled="otpVerifying || otpVerified || !otpCode || otpCode.length !== 6"
                    :class="otpVerified
                        ? 'bg-green-100 text-black border border-green-100 cursor-not-allowed shadow-none normal-case'
                        : (otpVerifying || !otpCode || otpCode.length !== 6)
                            ? 'bg-green-600 text-white opacity-60 cursor-not-allowed shadow-md uppercase'
                            : 'bg-green-600 hover:bg-green-700 text-white shadow-md uppercase'"
                    class="px-8 py-3 sm:py-0 rounded-lg font-bold text-xs transition-all active:scale-95 whitespace-nowrap w-full sm:w-auto">
                    <span x-show="!otpVerifying && !otpVerified">Verify</span>
                    <span x-show="otpVerifying">Verifying...</span>
                    <span x-show="otpVerified" class="flex items-center gap-2 text-black">
                        <i class="fas fa-check text-black"></i> Verified
                    </span>
                </button>
            </div>
            <p x-show="otpVerified" class="text-xs font-medium text-green-600">
                OTP verified. You can continue to the next step.
            </p>
            <p x-show="!otpVerified && otpRequested" class="text-xs font-medium text-orange-600">
                Please verify the OTP to continue.
            </p>
        </div>
        <div class="space-y-2">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider ml-1">Create
                Password <span class="text-red-500">*</span></label>
            <input type="password" placeholder="••••••••" x-model="formData.password"
                class="w-full px-4 py-3 rounded-lg input-box outline-none" required>
        </div>
    </div>

    <button type="button" @click="continueToBusinessDetails()" :disabled="otpVerifying"
        :class="otpVerifying ? 'opacity-60 cursor-not-allowed' : ''"
        class="w-full bg-[#ff743c] hover:bg-[#e65a2b] text-white font-bold py-3 rounded-lg transition-all shadow-xl shadow-orange-100 mt-2 flex items-center justify-center gap-3 group">
        <span x-show="!otpVerifying">Continue to Business Details</span>
        <span x-show="otpVerifying" class="flex items-center gap-2">
            <i class="fas fa-spinner fa-spin"></i> Verifying...
        </span>
        <i x-show="!otpVerifying" class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
    </button>
</div>
