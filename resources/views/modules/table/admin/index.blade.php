@extends('core.layouts.admin')

@section('content')
    @php $isAdmin = true; @endphp

    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">

        <!-- HEADER -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Table Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Table Directory</h1>
                    <p class="text-sm text-gray-400 mt-2">Manage restaurant tables & QR access</p>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    @include('core.components.table.table-sound-toggle')

                    <button id="printAllQrBtn" type="button"
                        class="inline-flex justify-center items-center gap-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 border border-blue-500/30 px-4 py-2.5 rounded-lg text-sm">
                        <i class="fas fa-print"></i>
                        Print All Posters
                    </button>

                    <button id="openTableModal" type="button"
                        class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium cursor-pointer">
                        <i class="fas fa-plus"></i>
                        Add Tables
                    </button>
                </div>
            </div>
        </div>

        @include('modules.table.components.stats')

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
            @include('modules.table.components.table-card')
        </div>
    </div>

    @include('modules.table.components.qr-modal')
@endsection
