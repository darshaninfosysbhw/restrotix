@extends('core.layouts.admin')
@section('content')
    <!--Dashboard-->
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900">

        @if (auth()->user()->role == 'admin' || auth()->user()->role == 'superadmin')
            @include('admin.roles._admin') <!--Owner panel-->
        @elseif(auth()->user()->role == 'manager')
            @include('admin.roles._branch_manager')<!--branch manager panel-->
        @elseif(auth()->user()->role == 'sales_manager')
            @include('admin.roles._sales_manager')<!--sales manager panel-->
        @elseif(auth()->user()->role == 'purchase_manager')
            @include('admin.roles._purchase_manager')<!--purchase manager panel-->
        @elseif(auth()->user()->role == 'account_manager')
            @include('admin.roles._account_manager')<!--account manager panel-->
            {{-- @elseif(auth()->user()->role == 'waiter')
            @include('admin.roles._waiter')<!--waiter panel--> --}}
        @elseif(auth()->user()->role == 'auditor')
            @include('admin.roles._auditor')<!--auditor panel-->
        @elseif(auth()->user()->role == 'store_keeper')
            @include('admin.roles._store_keeper')<!--store keeper panel-->
        @else
            <div class="text-white p-10 text-center">
                <h2 class="text-2xl">Welcome to RestoAdmin</h2>
                <p class="text-gray-400">Please contact admin for dashboard access.</p>
            </div>
        @endif
    </div>
@endsection
