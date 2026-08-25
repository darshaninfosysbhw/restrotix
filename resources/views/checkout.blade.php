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
        [x-cloak] {
            display: none !important;
        }

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
        billingCycle: '{{ $billingCycle }}',
        price: '{{ number_format((float) ($selectedPrice ?? 0), 2, '.', '') }}',
        symbol: '{{ trim((string) ($planDetails->currency_symbol ?? session('currency_symbol', '₹'))) }}',
        plan_id: '{{ $planDetails->id }}',
        agreed: false,
        loading: false,
        otpCode: '',
        otpRequested: false,
        otpSending: false,
        otpVerifying: false,
        otpEmail: localStorage.getItem('resto_otp_email') || '',
        otpVerified: false,
        otpVerifiedEmail: localStorage.getItem('resto_otp_verified_email') || '',
        otpResendAvailableAt: parseInt(localStorage.getItem('resto_otp_resend_available_at') || '0'),
        otpResendCooldown: 0,
        otpResendTimer: null,

        normalizeEmail(email) {
            return (email || '').trim().toLowerCase();
        },

        formatOtpCountdown(totalSeconds) {
            const minutes = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
            const seconds = (totalSeconds % 60).toString().padStart(2, '0');
            return `${minutes}:${seconds}`;
        },

        updateOtpResendCooldown() {
            if (!this.otpResendAvailableAt) {
                this.otpResendCooldown = 0;
                return 0;
            }

            const remaining = Math.max(0, Math.ceil((this.otpResendAvailableAt - Date.now()) / 1000));
            this.otpResendCooldown = remaining;

            if (remaining === 0) {
                this.otpResendAvailableAt = 0;
                localStorage.removeItem('resto_otp_resend_available_at');

                if (this.otpResendTimer) {
                    clearInterval(this.otpResendTimer);
                    this.otpResendTimer = null;
                }
            }

            return remaining;
        },

        startOtpResendCooldown(seconds = 30) {
            if (this.otpResendTimer) {
                clearInterval(this.otpResendTimer);
                this.otpResendTimer = null;
            }

            this.otpResendAvailableAt = Date.now() + (seconds * 1000);
            this.otpResendCooldown = seconds;
            localStorage.setItem('resto_otp_resend_available_at', String(this.otpResendAvailableAt));
            this.otpResendTimer = setInterval(() => this.updateOtpResendCooldown(), 1000);
        },

        resetOtpState(keepRequestedEmail = false) {
            this.otpCode = '';
            this.otpRequested = false;
            this.otpVerified = false;
            this.otpSending = false;
            this.otpVerifying = false;

            if (!keepRequestedEmail) {
                this.otpEmail = '';
                this.otpVerifiedEmail = '';
                localStorage.removeItem('resto_otp_email');
                localStorage.removeItem('resto_otp_verified_email');
            } else {
                localStorage.removeItem('resto_otp_verified_email');
            }
        },

        syncOtpStateWithEmail() {
            const currentEmail = this.normalizeEmail(this.formData.email);
            const requestedEmail = this.normalizeEmail(this.otpEmail);
            const verifiedEmail = this.normalizeEmail(this.otpVerifiedEmail);

            if (!currentEmail) {
                this.resetOtpState();
                return;
            }

            if (requestedEmail && requestedEmail !== currentEmail) {
                this.resetOtpState();
                return;
            }

            if (verifiedEmail && verifiedEmail !== currentEmail) {
                this.resetOtpState();
                return;
            }

            if (requestedEmail && requestedEmail === currentEmail) {
                this.otpRequested = true;
            }

            if (verifiedEmail && verifiedEmail === currentEmail) {
                this.otpRequested = true;
                this.otpVerified = true;
            }
        },

        async sendOtp() {
            const email = this.normalizeEmail(this.formData.email);

            if (!email) {
                window.showToast({ type: 'error', message: 'Please enter a valid email first!' });
                return;
            }

            this.otpSending = true;

            try {
                const response = await fetch('{{ route('checkout.otp.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email: this.formData.email
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Unable to send OTP.');
                }

                this.otpRequested = true;
                this.otpVerified = false;
                this.otpCode = '';
                this.otpEmail = email;
                this.otpVerifiedEmail = '';
                this.startOtpResendCooldown(30);
                localStorage.setItem('resto_otp_email', email);
                localStorage.removeItem('resto_otp_verified_email');

                window.showToast({
                    type: 'success',
                    message: data.message || 'OTP sent successfully.',
                    duration: 2500
                });
            } catch (err) {
                window.showToast({
                    type: 'error',
                    message: err.message || 'Unable to send OTP.',
                    duration: 5000
                });
            } finally {
                this.otpSending = false;
            }
        },

        async verifyOtp({ advance = false } = {}) {
            const email = this.normalizeEmail(this.formData.email);

            if (!email) {
                window.showToast({ type: 'error', message: 'Please enter your email first!' });
                return false;
            }

            if (!this.otpRequested || this.normalizeEmail(this.otpEmail) !== email) {
                window.showToast({
                    type: 'error',
                    message: 'Please send a fresh OTP to the current email first!'
                });
                return false;
            }

            if (!this.otpCode || this.otpCode.trim().length !== 6) {
                window.showToast({
                    type: 'error',
                    message: 'Please enter the 6-digit OTP.'
                });
                return false;
            }

            this.otpVerifying = true;

            try {
                const response = await fetch('{{ route('checkout.otp.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email: this.formData.email,
                        otp: this.otpCode
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'OTP verification failed.');
                }

                this.otpVerified = true;
                this.otpVerifiedEmail = email;
                localStorage.setItem('resto_otp_verified_email', email);

                window.showToast({
                    type: 'success',
                    message: data.message || 'Email verified successfully.',
                    duration: 2500
                });

                if (advance) {
                    this.step = 2;
                }

                return true;
            } catch (err) {
                this.otpVerified = false;
                localStorage.removeItem('resto_otp_verified_email');

                window.showToast({
                    type: 'error',
                    message: err.message || 'OTP verification failed.',
                    duration: 5000
                });

                return false;
            } finally {
                this.otpVerifying = false;
            }
        },

        async continueToBusinessDetails() {
            const currentEmail = this.normalizeEmail(this.formData.email);
            const verifiedEmail = this.normalizeEmail(this.otpVerifiedEmail);

            if (this.otpVerified && verifiedEmail === currentEmail) {
                this.step = 2;
                return;
            }

            window.showToast({
                type: 'error',
                message: 'Please verify your email first'
            });
        },
    
    
        async submitForm() {
            if (!this.agreed) {
                window.showToast({ type: 'error', message: 'Please agree to the terms first!' });
                return;
            }

            const currentEmail = this.normalizeEmail(this.formData.email);
            if (!this.otpVerified || this.normalizeEmail(this.otpVerifiedEmail) !== currentEmail) {
                window.showToast({
                    type: 'error',
                    message: 'Please verify your email OTP before launching!'
                });
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
                        billing_cycle: this.billingCycle,
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
    $watch('formData', value => localStorage.setItem('resto_form', JSON.stringify(value)), { deep: true });
    this.otpEmail = localStorage.getItem('resto_otp_email') || '';
    this.otpVerifiedEmail = localStorage.getItem('resto_otp_verified_email') || '';
    this.otpRequested = !!this.otpEmail;
    this.otpVerified = this.normalizeEmail(this.otpVerifiedEmail) === this.normalizeEmail(this.formData.email) && this.otpVerifiedEmail !== '';
    this.syncOtpStateWithEmail();
    this.updateOtpResendCooldown();
    if (this.otpResendCooldown > 0 && !this.otpResendTimer) {
        this.otpResendTimer = setInterval(() => this.updateOtpResendCooldown(), 1000);
    }
    $watch('formData.email', () => this.syncOtpStateWithEmail());">

    @include('checkout.partials.header')

    <main class="flex-grow flex items-center justify-center px-4 py-4">
        <div class="max-w-7xl w-full flex flex-col md:flex-row items-center justify-center gap-8 lg:gap-16">

            @include('checkout.partials.plan-summary')

            <div class="w-full md:w-[650px] lg:w-[750px]">
                <div class="bg-white rounded-lg shadow-xl p-6 lg:p-12 border border-white/40">

                    @include('checkout.partials.step-indicator')

                    @include('checkout.partials.account-setup')
                    @include('checkout.partials.business-details')
                    @include('checkout.partials.review-launch')

                </div>
            </div>
        </div>
    </main>

    @include('checkout.partials.footer')
    <x-toast-manager />
</body>

</html>
