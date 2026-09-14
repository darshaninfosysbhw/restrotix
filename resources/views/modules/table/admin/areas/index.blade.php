@extends('core.layouts.admin')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">
        @include('modules.table.admin.areas.partials.header')
        @include('modules.table.admin.areas.partials.stats-card')
        @include('modules.table.admin.areas.partials.table')
        @include('modules.table.admin.areas.partials.modal')
    </div>

    <style>
        .offline-toggle-input:checked+.offline-toggle-track {
            background-color: rgb(249 115 22);
        }

        .offline-toggle-input:checked+.offline-toggle-track+.offline-toggle-knob {
            transform: translateX(1rem);
        }
    </style>
@endsection
