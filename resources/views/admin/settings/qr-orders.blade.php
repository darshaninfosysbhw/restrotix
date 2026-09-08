@extends('core.layouts.admin')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6 max-w-2xl">
            <h1 class="text-2xl font-bold text-white mt-1">QR Order Settings</h1>
            <p class="text-sm text-gray-400 mt-2 break-words">Branch: {{ $branch->branch_name }}</p>
            <form data-qr-order-settings method="POST" action="{{ route('admin.settings.qr-orders.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                <input type="hidden" name="auto_accept_qr_orders" value="0">
                <label class="flex items-start justify-between gap-4 border border-gray-700 rounded-lg p-4 cursor-pointer">
                    <span>
                        <span id="qrAutoAcceptLabel" class="block font-medium text-white">Auto-Accept QR Orders</span> 
                        <span class="block text-sm text-gray-400 mt-2">On: new QR orders go directly to the kitchen. </span> 
                        <span class="block text-sm text-gray-400">Off: new QR orders require confirmation before going to the kitchen. </span>
                    </span>
                    <span class="relative inline-flex min-h-11 shrink-0 items-center">
                        <input type="checkbox" role="switch" aria-labelledby="qrAutoAcceptLabel" name="auto_accept_qr_orders" value="1" @checked(old('auto_accept_qr_orders', $branch->auto_accept_qr_orders)) class="peer sr-only">
                        <span aria-hidden="true" class="relative h-7 w-12 rounded-full bg-gray-500 transition-colors peer-checked:!bg-orange-500 peer-focus-visible:ring-2 peer-focus-visible:ring-orange-500 peer-focus-visible:ring-offset-2 after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition-transform peer-checked:after:translate-x-5"></span>
                        <span aria-hidden="true" class="ml-2 text-xs font-semibold text-gray-400 peer-checked:hidden">OFF</span>
                        <span aria-hidden="true" class="ml-2 hidden text-xs font-semibold text-orange-500 peer-checked:inline">ON</span>
                    </span>
                </label>
                @foreach ($errors->all() as $error)
                    <p role="alert" class="text-sm text-red-500">{{ $error }}</p>
                @endforeach
            </form>
        </div>
    </div>
@endsection
