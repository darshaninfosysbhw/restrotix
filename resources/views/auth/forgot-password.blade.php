<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forgot Password - Restrotix</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#fdf2f2] font-sans text-slate-800">

    <x-toast-manager />

    <main class="flex min-h-screen items-center justify-center px-4 py-10">

        <section
            class="w-full max-w-md rounded-2xl border border-red-100 bg-white p-8 shadow-xl"
        >

            <div class="mb-8 text-center">

                <a href="{{ url('/') }}" class="inline-flex">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="Restrotix"
                        class="h-10 w-auto"
                    >
                </a>

                <h1
                    id="pageTitle"
                    class="mt-6 text-2xl font-bold text-slate-900"
                >
                    Forgot Password?
                </h1>

                <p
                    id="pageDescription"
                    class="mt-2 text-sm leading-6 text-slate-500"
                >
                    Enter your registered email or mobile number
                    to receive a verification code.
                </p>

            </div>

            {{-- STEP 1: SEND OTP --}}
            <form
                id="forgotPasswordForm"
                class="space-y-5"
            >
                @csrf

                <div>
                    <label
                        for="identifier"
                        class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                        Email or Mobile Number
                    </label>

                    <input
                        id="identifier"
                        type="text"
                        name="identifier"
                        required
                        autocomplete="username"
                        placeholder="Enter email or mobile number"
                        class="w-full rounded-lg border border-slate-200 bg-[#edf3fc]
                               px-4 py-3 text-base text-slate-800 outline-none
                               transition placeholder:text-slate-400
                               focus:border-[#DC0812]
                               focus:bg-white
                               focus:ring-4
                               focus:ring-[#DC0812]/15"
                    >
                </div>

                <button
                    type="submit"
                    id="sendOtpButton"
                    class="inline-flex w-full items-center justify-center
                           rounded-lg bg-[#DC0812] px-6 py-3
                           text-base font-semibold text-white
                           shadow-lg shadow-[#DC0812]/20
                           transition hover:bg-[#B8070F]
                           disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Send OTP
                </button>

            </form>

            {{-- STEP 2: VERIFY OTP --}}
            <form
                id="verifyOtpForm"
                class="hidden space-y-5"
            >
                @csrf

                <input
                    type="hidden"
                    id="verifyIdentifier"
                    name="identifier"
                >

                <div>
                    <label
                        for="otp"
                        class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                        Enter OTP
                    </label>

                    <input
                        id="otp"
                        type="text"
                        name="otp"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="Enter 6-digit OTP"
                        class="w-full rounded-lg border border-slate-200 bg-[#edf3fc]
                               px-4 py-3 text-center text-xl font-bold tracking-[0.35em]
                               text-slate-800 outline-none transition
                               placeholder:text-sm placeholder:font-normal
                               placeholder:tracking-normal placeholder:text-slate-400
                               focus:border-[#DC0812]
                               focus:bg-white
                               focus:ring-4
                               focus:ring-[#DC0812]/15"
                    >
                </div>

                <button
                    type="submit"
                    id="verifyOtpButton"
                    class="inline-flex w-full items-center justify-center
                           rounded-lg bg-[#DC0812] px-6 py-3
                           text-base font-semibold text-white
                           shadow-lg shadow-[#DC0812]/20
                           transition hover:bg-[#B8070F]
                           disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Verify OTP
                </button>

                <button
                    type="button"
                    id="changeIdentifierButton"
                    class="w-full text-sm font-semibold text-slate-500
                           transition hover:text-[#DC0812]"
                >
                    Change Email / Mobile Number
                </button>

            </form>

            {{-- STEP 3: RESET PASSWORD --}}
            <form
                id="resetPasswordForm"
                class="hidden space-y-5"
            >
                @csrf

                <div>
                    <label
                        for="password"
                        class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                        New Password
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Enter new password"
                        class="w-full rounded-lg border border-slate-200 bg-[#edf3fc]
                               px-4 py-3 text-base text-slate-800 outline-none
                               transition placeholder:text-slate-400
                               focus:border-[#DC0812]
                               focus:bg-white
                               focus:ring-4
                               focus:ring-[#DC0812]/15"
                    >
                </div>

                <div>
                    <label
                        for="password_confirmation"
                        class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                        Confirm Password
                    </label>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Confirm new password"
                        class="w-full rounded-lg border border-slate-200 bg-[#edf3fc]
                               px-4 py-3 text-base text-slate-800 outline-none
                               transition placeholder:text-slate-400
                               focus:border-[#DC0812]
                               focus:bg-white
                               focus:ring-4
                               focus:ring-[#DC0812]/15"
                    >
                </div>

                <p class="text-xs leading-5 text-slate-500">
                    Password must be at least 8 characters.
                </p>

                <button
                    type="submit"
                    id="resetPasswordButton"
                    class="inline-flex w-full items-center justify-center
                           rounded-lg bg-[#DC0812] px-6 py-3
                           text-base font-semibold text-white
                           shadow-lg shadow-[#DC0812]/20
                           transition hover:bg-[#B8070F]
                           disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Reset Password
                </button>

            </form>

            <div class="mt-6 text-center">

                <a
                    href="{{ route('login') }}"
                    class="text-sm font-semibold text-[#DC0812]
                           hover:text-[#B8070F]"
                >
                    ← Back to Login
                </a>

            </div>

        </section>

    </main>

    <script>
        (() => {

            const sendForm = document.getElementById('forgotPasswordForm');
            const verifyForm = document.getElementById('verifyOtpForm');
            const resetForm = document.getElementById('resetPasswordForm');

            const identifierInput = document.getElementById('identifier');
            const verifyIdentifier = document.getElementById('verifyIdentifier');
            const otpInput = document.getElementById('otp');

            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput =
                document.getElementById('password_confirmation');

            const sendButton = document.getElementById('sendOtpButton');
            const verifyButton = document.getElementById('verifyOtpButton');
            const resetButton = document.getElementById('resetPasswordButton');

            const changeIdentifierButton =
                document.getElementById('changeIdentifierButton');

            const pageTitle = document.getElementById('pageTitle');
            const pageDescription =
                document.getElementById('pageDescription');

            if (
                !sendForm ||
                !verifyForm ||
                !resetForm ||
                !identifierInput ||
                !verifyIdentifier ||
                !otpInput ||
                !passwordInput ||
                !passwordConfirmationInput ||
                !sendButton ||
                !verifyButton ||
                !resetButton
            ) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Step UI helpers
            |--------------------------------------------------------------------------
            */

            const showIdentifierStep = () => {

                verifyForm.classList.add('hidden');
                resetForm.classList.add('hidden');

                sendForm.classList.remove('hidden');

                verifyIdentifier.value = '';
                otpInput.value = '';

                passwordInput.value = '';
                passwordConfirmationInput.value = '';

                pageTitle.textContent = 'Forgot Password?';

                pageDescription.textContent =
                    'Enter your registered email or mobile number to receive a verification code.';

                identifierInput.focus();
            };


            const showVerifyStep = (identifier) => {

                sendForm.classList.add('hidden');
                resetForm.classList.add('hidden');

                verifyForm.classList.remove('hidden');

                verifyIdentifier.value = identifier;

                pageTitle.textContent = 'Verify OTP';

                pageDescription.textContent =
                    'Enter the 6-digit verification code sent to your registered account.';

                otpInput.focus();
            };


            const showResetStep = () => {

                sendForm.classList.add('hidden');
                verifyForm.classList.add('hidden');

                resetForm.classList.remove('hidden');

                pageTitle.textContent = 'Create New Password';

                pageDescription.textContent =
                    'Create a new secure password for your Restrotix account.';

                passwordInput.focus();
            };


            /*
            |--------------------------------------------------------------------------
            | STEP 1: SEND OTP
            |--------------------------------------------------------------------------
            */

            sendForm.addEventListener('submit', async (event) => {

                event.preventDefault();

                const formData = new FormData(sendForm);

                sendButton.disabled = true;
                sendButton.textContent = 'Sending OTP...';

                try {

                    const response = await fetch(
                        "{{ route('password.send-otp') }}",
                        {
                            method: 'POST',

                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },

                            body: formData,
                        }
                    );

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message || 'Unable to send OTP.'
                        );
                    }

                    window.showToast({
                        type: 'success',
                        message: data.message,
                        duration: 5000,
                    });

                    showVerifyStep(
                        identifierInput.value.trim()
                    );

                } catch (error) {

                    console.error(error);

                    window.showToast({
                        type: 'error',
                        message: error.message,
                        duration: 5000,
                    });

                } finally {

                    sendButton.disabled = false;
                    sendButton.textContent = 'Send OTP';
                }
            });


            /*
            |--------------------------------------------------------------------------
            | STEP 2: VERIFY OTP
            |--------------------------------------------------------------------------
            */

            verifyForm.addEventListener('submit', async (event) => {

                event.preventDefault();

                const formData = new FormData(verifyForm);

                verifyButton.disabled = true;
                verifyButton.textContent = 'Verifying...';

                try {

                    const response = await fetch(
                        "{{ route('password.verify-otp') }}",
                        {
                            method: 'POST',

                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },

                            body: formData,
                        }
                    );

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message || 'Unable to verify OTP.'
                        );
                    }

                    window.showToast({
                        type: 'success',
                        message: data.message,
                        duration: 2500,
                    });

                    showResetStep();

                } catch (error) {

                    console.error(error);

                    window.showToast({
                        type: 'error',
                        message: error.message,
                        duration: 5000,
                    });

                } finally {

                    verifyButton.disabled = false;
                    verifyButton.textContent = 'Verify OTP';
                }
            });


            /*
            |--------------------------------------------------------------------------
            | STEP 3: RESET PASSWORD
            |--------------------------------------------------------------------------
            */

            resetForm.addEventListener('submit', async (event) => {

                event.preventDefault();

                const formData = new FormData(resetForm);

                resetButton.disabled = true;
                resetButton.textContent = 'Resetting...';

                try {

                    const response = await fetch(
                        "{{ route('password.reset') }}",
                        {
                            method: 'POST',

                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },

                            body: formData,
                        }
                    );

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message || 'Unable to reset password.'
                        );
                    }

                    window.showToast({
                        type: 'success',
                        message: data.message,
                        duration: 2500,
                    });

                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1200);

                } catch (error) {

                    console.error(error);

                    window.showToast({
                        type: 'error',
                        message: error.message,
                        duration: 5000,
                    });

                } finally {

                    resetButton.disabled = false;
                    resetButton.textContent = 'Reset Password';
                }
            });


            /*
            |--------------------------------------------------------------------------
            | Change identifier
            |--------------------------------------------------------------------------
            */

            changeIdentifierButton?.addEventListener('click', () => {
                showIdentifierStep();
            });


            /*
            |--------------------------------------------------------------------------
            | OTP digits only
            |--------------------------------------------------------------------------
            */

            otpInput.addEventListener('input', () => {

                otpInput.value = otpInput.value
                    .replace(/\D/g, '')
                    .slice(0, 6);
            });

        })();
    </script>

</body>

</html>