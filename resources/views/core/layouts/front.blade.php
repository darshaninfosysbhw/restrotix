<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Restrotix - Restaurant Software</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
 <style>
        html {
    scroll-behavior: smooth;
}
    </style>
</head>

<body class="text-gray-800">
    <x-toast-manager />
    @include('core.layouts.includes.header-front')
    @yield('content')
    @include('core.layouts.includes.footer-front')
</body>

</html>
