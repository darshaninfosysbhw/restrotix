@extends('core.layouts.admin')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">
        @include('admin.employee.partials.header')
        @include('admin.employee.partials.stats-cards')
        @include('admin.employee.partials.table')
        @include('admin.employee.partials.modal')
    </div>
@endsection
