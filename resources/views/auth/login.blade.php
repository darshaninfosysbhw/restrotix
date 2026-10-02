<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restrotix Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .login-screen {
            min-height: 100dvh;
            background-position: center;
            background-size: cover;
        }

        /* Desktop specific styles */
        @media (min-width: 1024px) {
            .login-screen {
                min-height: max(100dvh, 680px);
            }
            .login-card {
                width: clamp(380px, 33vw, 452px);
                min-height: 484px;
                padding: clamp(32px, 3vw, 48px);
            }
            .login-character {
                left: 32%;
                width: 31%;
                height: 69%;
            }
        }

        /* Responsive for Tablet and Mobile: Centers card properly and prevents cut-off */
        @media (max-width: 1023px) {
            .login-screen {
                min-height: 100dvh;
                height: auto;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                padding: 20px 16px 16px;
                box-sizing: border-box;
            }
            .login-left-panel,
            .login-left,
            .login-character {
                display: none !important;
            }
            .login-logo {
                align-self: flex-start;
                margin-bottom: 12px;
            }
            .login-card {
                position: relative !important;
                top: auto !important;
                right: auto !important;
                transform: none !important;
                width: 100% !important;
                max-width: 440px !important;
                min-height: auto !important;
                padding: 28px 20px !important;
                margin: auto 0 !important;
                box-sizing: border-box !important;
            }
            .login-footer {
                position: static !important;
                width: 100% !important;
                margin-top: 20px !important;
                flex-direction: column !important;
                justify-content: center !important;
                gap: 10px !important;
                text-align: center !important;
                padding: 0 !important;
            }
        }

        @media (max-height: 760px) and (min-width: 1024px) {
            .login-card { padding: 24px 34px; }
            .login-card .form-stack { gap: 14px; }
            .login-left .feature-list { gap: 13px; margin-top: 22px; }
        }

        @media (max-width: 480px) {
            .login-screen { padding: 16px 12px 14px; }
            .login-card { padding: 24px 16px !important; border-radius: 12px; }
            .login-card .form-stack { margin-top: 22px; gap: 18px; }
        }

        @media (max-width: 360px) {
            .login-screen { padding: 12px 8px 12px; }
            .login-card { padding: 20px 14px !important; }
            .login-card .form-stack { gap: 14px; }
        }
    </style>
</head>

<body class="font-sans text-slate-800">
    <x-toast-manager />

    <main class="login-screen relative overflow-hidden bg-[#fdf2f2]"
        style="background-image: url('{{ asset('images/login-restaurant-bg.png') }}');">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-white/5 via-transparent to-red-100/10"></div>
        <svg class="login-left-panel pointer-events-none absolute inset-y-0 left-0 h-full w-[45%]" viewBox="0 0 600 600"
            preserveAspectRatio="none" aria-hidden="true">
            <defs>
                <linearGradient id="leftRedGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#fef0ed" />
                    <stop offset="40%" stop-color="#fde0da" />
                    <stop offset="100%" stop-color="#fbcbc2" />
                </linearGradient>
            </defs>
            <path d="M0 0 H570 C515 45 478 120 450 215 C420 320 410 425 438 505 C450 545 468 575 485 600 H0 Z"
                fill="url(#leftRedGradient)" />
        </svg>

        <a href="{{ url('/') }}" class="login-logo lg:absolute lg:left-[3.4%] lg:top-[3.8%] z-30 inline-flex" aria-label="Restrotix home">
            <img src="{{ asset('images/logo.png') }}" alt="Restrotix" class="h-8 w-auto sm:h-10">
        </a>

        <section class="login-left absolute left-[5%] top-[18.5%] z-20 w-[29%]" aria-labelledby="welcome-heading">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Restaurant Operations, Simplified</p>
            <h1 id="welcome-heading" class="mt-[2.2vh] text-4xl font-bold leading-tight text-slate-900">
                Welcome to<br><span class="text-[#DC0812]">Restrotix!</span>
            </h1>
            <p class="mt-[2.2vh] max-w-sm text-base leading-relaxed text-slate-600">All-in-One Restaurant Software – Simplify Operations, Boost Efficiency, Delight Customers with Our Comprehensive Solution.</p>

            <div class="feature-list mt-[4.5vh] flex flex-col gap-[2.5vh]">
                @foreach ([
                    ['icon' => 'fa-utensils', 'title' => 'Smarter Operations', 'text' => 'Manage orders, inventory, and staff with ease.', 'style' => 'bg-[#DC0812]/10 text-[#DC0812] group-hover:bg-[#DC0812]'],
                    ['icon' => 'fa-chart-column', 'title' => 'Greater Efficiency', 'text' => 'Save time and reduce manual work.', 'style' => 'bg-[#DC0812]/10 text-[#DC0812] group-hover:bg-[#DC0812]'],
                    ['icon' => 'fa-users', 'title' => 'Happier Customers', 'text' => 'Deliver seamless dining experiences.', 'style' => 'bg-[#DC0812]/10 text-[#DC0812] group-hover:bg-[#DC0812]'],
                ] as $feature)
                    <div class="group flex items-center gap-[1.5vw]">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full text-lg transition-colors duration-300 group-hover:text-white {{ $feature['style'] }}">
                            <i class="fa-solid {{ $feature['icon'] }}"></i>
                        </span>
                        <span>
                            <strong class="block text-base font-bold text-slate-900">{{ $feature['title'] }}</strong>
                            <span class="mt-1 block text-sm leading-5 text-slate-600">{{ $feature['text'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="login-character pointer-events-none absolute bottom-0 z-10 flex items-end justify-center" aria-hidden="true">
            <img src="{{ asset('images/login-boy.png') }}?v={{ filemtime(public_path('images/login-boy.png')) }}" alt="" class="max-h-full w-full object-contain object-bottom drop-shadow-2xl">
        </div>

        <section class="login-card lg:absolute lg:right-[4.2%] lg:top-1/2 lg:z-30 lg:-translate-y-1/2 rounded-[14px] border border-white/80 bg-white/95 shadow-[0_24px_60px_-28px_rgba(72,37,15,0.42)] backdrop-blur-xl">
            <header>
                <h2 class="text-2xl font-bold text-slate-900">Login</h2>
                <span class="mt-3 block h-1 w-12 rounded-full bg-[#DC0812]"></span>
                <p class="mt-2 max-w-sm text-sm leading-5 text-slate-500">Please Enter your credentials to get started</p>
            </header>

            <form action="{{ route('login') }}" method="POST" class="form-stack mt-8 flex flex-col gap-6">
                @csrf
                <div>
                    <label for="login-email" class="mb-2 block text-sm font-bold text-slate-800">Email ID <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input id="login-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter Email Here"
                            class="w-full rounded-lg border border-slate-200 bg-[#edf3fc] px-4 py-3 text-base text-slate-800 outline-none transition placeholder:text-slate-500 focus:border-[#DC0812] focus:bg-white focus:ring-4 focus:ring-[#DC0812]/15">
                    </div>
                    @error('email')<p class="mt-2 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="passwordField" class="mb-2 block text-sm font-bold text-slate-800">Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input id="passwordField" type="password" name="password" required autocomplete="current-password" placeholder="Enter Password Here"
                            class="w-full rounded-lg border border-slate-200 bg-[#edf3fc] py-3 pl-4 pr-12 text-base text-slate-800 outline-none transition placeholder:text-slate-500 focus:border-[#DC0812] focus:bg-white focus:ring-4 focus:ring-[#DC0812]/15">
                        <button type="button" onclick="togglePass()" aria-label="Show or hide password" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-[#DC0812]"><i id="eyeIcon" class="fa-regular fa-eye text-lg"></i></button>
                    </div>
                    @error('password')<p class="mt-2 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                    <label class="inline-flex cursor-pointer items-center gap-2 text-slate-500">
                        <input type="checkbox" name="remember" class="h-5 w-5 rounded border-slate-300 text-[#DC0812] focus:ring-[#DC0812]/30" @checked(old('remember'))>
                        <span>Remember me</span>
                    </label>
                    <a href="{{ route('password.forgot') }}" class="font-semibold text-[#DC0812] hover:text-[#B8070F] underline underline-offset-4">Forgot password?</a>
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-[#DC0812] px-6 py-3 text-base font-semibold text-white shadow-lg shadow-[#DC0812]/25 transition hover:-translate-y-0.5 hover:bg-[#B8070F] hover:shadow-xl active:scale-[0.99]">
                    <span>Login</span>
                </button>
            </form>

            <div class="compact-help">
                <div class="my-5 flex items-center gap-4 text-xs text-slate-400">
                    <span class="h-px flex-1 bg-slate-200"></span><span>or</span><span class="h-px flex-1 bg-slate-200"></span>
                </div>
                <a href="https://wa.me/9779802892772" target="_blank" rel="noopener noreferrer"
                    class="flex items-center justify-between rounded-lg transition hover:bg-emerald-50">
                    <span class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-lg text-emerald-700"><i class="fa-solid fa-headset"></i></span>
                        <span><strong class="block text-sm text-slate-800">Need help?</strong><span class="block text-xs text-slate-500">Contact our support team</span></span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
                </a>
            </div>
        </section>

        <footer class="login-footer lg:absolute lg:inset-x-0 lg:bottom-[3.8%] z-40 flex items-center justify-between px-[3.4%] text-xs font-medium uppercase tracking-wider text-slate-600">
            <p class="normal-case tracking-normal">&copy; {{ date('Y') }} Restrotix. All rights reserved.</p>
            <nav class="flex items-center gap-5"><a href="{{ route('policy.terms') }}" class="hover:text-[#DC0812]">Terms of Use</a><span class="h-4 w-px bg-slate-500"></span><a href="{{ route('policy.privacy') }}" class="hover:text-[#DC0812]">Privacy Policy</a></nav>
        </footer>
    </main>

    <script>
        function togglePass() {
            const input = document.getElementById('passwordField');
            const icon = document.getElementById('eyeIcon');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
        }
    </script>
</body>

</html>
