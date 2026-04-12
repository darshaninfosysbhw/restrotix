@extends('core.layouts.waiter')

@section('content')
    @php $isAdmin = false; @endphp

    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">

        <div class="bg-gray-800 p-5 rounded-xl">
            <h1 class="text-white text-xl font-bold">Tables</h1>
            <p class="text-sm text-gray-400 mt-1">Manage your tables efficiently</p>
        </div>

        @include('modules.table.components.stats')

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @include('modules.table.components.table-card')
        </div>

    </div>

    @include('modules.table.components.qr-modal')
@endsection
