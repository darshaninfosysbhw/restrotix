@extends('core.layouts.admin')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">

        @include('profile.partials.header')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
            @include('profile.partials.basic-info')

            <div class="space-y-5">
                @include('profile.partials.password')
                @include('profile.partials.activity')
            </div>
        </div>

    </div>
@endsection
