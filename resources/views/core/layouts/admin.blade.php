<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restrotix - Restaurant Software</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="light-theme text-gray-200 bg-gray-900">
    @include('core.layouts.includes.theme-bootstrap')
    @include('core.layouts.includes.sidebar-admin')
    @include('core.layouts.includes.header-admin')
    @yield('content')
    <x-toast-manager />
    </main>
    </div>
</body>

</html>
