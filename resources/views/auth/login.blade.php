<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restrotix Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gradient-to-br from-orange-100 via-orange-200 to-orange-300 min-h-screen flex flex-col font-sans">

    <x-toast-manager />

    <header class="p-6">
        <div class="flex items-center">
            <a href="{{ url('/') }}" class="mr-2 sm:mr-3 inline-flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Restrotix" class="h-8 w-auto sm:h-10">
            </a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4">
        <div class="max-w-6xl w-full flex flex-col md:flex-row items-center md:items-end justify-between gap-12">

            <div class="w-full md:w-1/2 text-center flex flex-col md:justify-end">
                <div class="relative mx-auto mb-2 w-[320px] h-[320px] md:w-[380px] md:h-[380px]">
                    <div class="absolute inset-0 bg-orange-200 rounded-full"></div>
                    <img src="{{ asset('images/login.png') }}" alt="img"
                        class="relative z-10 w-full h-full object-contain object-bottom">
                </div>
                <h1 class="text-4xl font-bold text-black-800 mb-2">Welcome!</h1>
                <p class="text-gray-600 max-w-sm mx-auto leading-relaxed">

                    All-in-One Restaurant Software – Simplify Operations, Boost Efficiency, Delight Customers with Our
                    Comprehensive Solution.
                </p>
            </div>

            <div class="w-full md:w-[450px]">
                <div class="bg-white rounded-xl shadow-2xl p-8 md:p-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Login</h2>
                    <p class="text-sm text-gray-500 mb-8">Please Enter your credentials to get started</p>

                    <form action="{{ route('login') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Email ID <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter Email Here"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition-all">
                            @error('email')
                                <p class="mt-2 text-xs font-medium text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input id="passwordField" type="password" name="password"
                                    placeholder="Enter Password Here"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition-all">
                                <button type="button" onclick="togglePass()"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500">
                                    <i id="eyeIcon" class="far fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-xs font-medium text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="remember"
                                    class="h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400"
                                    @checked(old('remember'))>
                                <span>Remember me</span>
                            </label>

                            <a href="#"
                                class="text-[#ff743c] font-medium hover:text-[#e65a2b] hover:underline">Forgot
                                password?</a>
                        </div>

                        <button type="submit"
                            class="w-full bg-[#ff743c] hover:bg-[#e65a2b] text-white font-semibold py-3 rounded-lg transition-all shadow-lg active:scale-[0.98]">
                            Login
                        </button>
                    </form>

                    <script>
                        function togglePass() {
                            const passInput = document.getElementById('passwordField');
                            const icon = document.getElementById('eyeIcon');
                            if (passInput.type === 'password') {
                                passInput.type = 'text';
                                icon.classList.replace('fa-eye', 'fa-eye-slash');
                            } else {
                                passInput.type = 'password';
                                icon.classList.replace('fa-eye-slash', 'fa-eye');
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </main>

    <footer class="p-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 gap-4">
        <div>© 2026 Restrotix </div>
        <div class="flex gap-6 uppercase tracking-wider">
            <a href="#" class="hover:text-gray-800">Terms of Use</a>
            <a href="#" class="hover:text-gray-800">Privacy Policy</a>
        </div>
    </footer>

</body>

</html>
