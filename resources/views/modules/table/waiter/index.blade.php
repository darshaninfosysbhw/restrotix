@extends('core.layouts.waiter')

@section('content')
    @php $isAdmin = false; @endphp

    <div class="flex-1 overflow-y-auto p-5 bg-gray-900 space-y-3">

        <div class ="pb-3">
            @include('modules.table.components.stats')
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @include('modules.table.components.table-card')
        </div>
    </div>

    @include('modules.table.components.qr-modal')
@endsection
