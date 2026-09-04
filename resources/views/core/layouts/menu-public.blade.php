<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu Preview | Restrotix</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $publicMenuTheme = $publicMenuTheme ?? 'dark';
    $bodyThemeClass = $publicMenuTheme === 'light'
        ? 'light-theme bg-slate-50 text-slate-900'
        : 'dark bg-gray-900 text-gray-200';
@endphp

<body class="{{ $bodyThemeClass }} min-h-screen">
    <x-toast-manager />
    @yield('content')
</body>

</html>
