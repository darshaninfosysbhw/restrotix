<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RestoChain ERP - Multi-Branch Restaurant Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="text-slate-200 antialiased sa-theme-dark">
    @include('core.layouts.includes.sidebar-superadmin')
    @include('core.layouts.includes.header-superadmin')
    @yield('content')

    </main>
    </div>
    <!-- Toast Manager -->
    <x-toast-manager />
</body>

</html>
