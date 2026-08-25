@extends('core.layouts.admin')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">
        @include('admin.branches.partials.header')
        @include('admin.branches.partials.stats-cards')
        @include('admin.branches.partials.table')
        @include('admin.branches.partials.modal')
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
