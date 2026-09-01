@extends('core.layouts.chef')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6 ">
        <div class="flex items-center gap-4 bg-gray-800 border border-gray-700 rounded-lg p-2 md:p-2 max-w-[130px] ">
            <a href="{{ route('admin.kds.index') }}" class="text-sm text-orange-400 hover:underline"> <- Back To KDS</a>
        </div>

        @include('profile.partials.header')
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
            @include('profile.partials.basic-info')

            @include('profile.partials.password')
        </div>

    </div>
@endsection
