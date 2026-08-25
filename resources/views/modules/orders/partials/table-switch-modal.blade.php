@php
    $switchTableCardClasses = [
        'available' => 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm',
        'occupied' => 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm',
        'reserved' => 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm',
        'calling_waiter' => 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm',
        'request_bill' => 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm',
        'out_of_service' => 'border-slate-200 bg-slate-50 opacity-50',
    ];

    $switchTableStatusDotClasses = [
        'available' => 'text-emerald-500',
        'occupied' => 'text-red-500',
        'reserved' => 'text-amber-500',
        'calling_waiter' => 'text-sky-500',
        'request_bill' => 'text-orange-500',
        'out_of_service' => 'text-slate-400',
    ];
@endphp

<div id="tableSwitchModal" class="fixed inset-0 z-[120] hidden">
    <div id="tableSwitchBackdrop" class="absolute inset-0 bg-black/60"></div>

    <div class="relative z-10 flex min-h-full items-end justify-center p-3 sm:items-center sm:p-4">
        <div
            class="w-full max-w-3xl overflow-hidden rounded-lg border border-slate-200 bg-white text-slate-900 shadow-2xl shadow-slate-950/10">
            <div class="flex items-start justify-between gap-3 border-b border-slate-200 bg-slate-50 px-5 py-4">
                <div>
                    <h3 class="mt-1 text-xl font-bold text-slate-950">Switch Table</h3>
                </div>

                <button id="closeTableSwitchModal" type="button"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-500 shadow-sm hover:bg-slate-50 hover:text-slate-900 cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="px-4 py-4 sm:px-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input id="tableSwitchSearch" type="text" placeholder="Search table number or status..."
                            class="h-10 w-full rounded-lg border border-slate-200 bg-white pl-10 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="button" data-switch-filter="all"
                            class="switch-filter-btn rounded-lg border border-orange-500/30 bg-orange-500/10 px-3 py-2 text-xs font-semibold text-orange-600 cursor-pointer">
                            All
                        </button>
                        <button type="button" data-switch-filter="available"
                            class="switch-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50 cursor-pointer">
                            Available
                        </button>
                        <button type="button" data-switch-filter="occupied"
                            class="switch-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50 cursor-pointer">
                            Occupied
                        </button>
                        <button type="button" data-switch-filter="reserved"
                            class="switch-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50 cursor-pointer">
                            Reserved
                        </button>
                    </div>
                </div>

                <div id="tableSwitchGrid"
                    class="mt-4 grid max-h-[56vh] grid-cols-2 gap-2.5 overflow-y-auto pr-1 sm:grid-cols-3 xl:grid-cols-4">
                    @forelse ($switchableTables ?? [] as $switchTable)
                        @php
                            $status = (string) ($switchTable['status'] ?? 'available');
                            $statusCardClass = $switchTableCardClasses[$status] ?? $switchTableCardClasses['available'];
                            $statusDotClass = $switchTableStatusDotClasses[$status] ?? null;
                            $statusLabelText = $switchTable['status_label'] ?? ucfirst(str_replace('_', ' ', $status));
                            $isCurrent = !empty($switchTable['is_current']);
                            $isDisabled = !empty($switchTable['is_disabled']);
                            $searchText = strtolower(
                                trim(
                                    implode(
                                        ' ',
                                        array_filter([
                                            'table ' . (string) ($switchTable['table_number'] ?? ''),
                                            (string) ($switchTable['status_label'] ?? ''),
                                        ]),
                                    ),
                                ),
                            );
                        @endphp

                        <button type="button" data-switch-table-card data-switch-table-id="{{ $switchTable['id'] }}"
                            data-switch-table-number="{{ $switchTable['table_number'] }}"
                            data-switch-table-status="{{ $status }}"
                            data-switch-table-search="{{ $searchText }}"
                            data-switch-table-disabled="{{ $isDisabled ? '1' : '0' }}"
                            @if ($isDisabled) disabled @endif
                            class="group table-card flex min-h-[118px] flex-col justify-between rounded-lg border p-3 text-left transition
                            {{ $isDisabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer card-hover' }}
                            {{ $statusCardClass }}
                            {{ $isCurrent ? 'border-orange-500/70 ring-1 ring-orange-500/30' : '' }}">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <div class="min-w-0 flex flex-wrap items-center gap-1.5">
                                    <h3 class="font-semibold text-sm leading-tight text-slate-950">
                                        Table {{ $switchTable['table_number'] }}
                                    </h3>
                                    @if ($statusDotClass)
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-semibold {{ $statusDotClass }}">
                                            <i class="fas fa-circle text-[8px]"></i>
                                            <span>{{ $statusLabelText }}</span>
                                        </span>
                                    @endif
                                    @if ($isCurrent)
                                        <span
                                            class="rounded-full border border-orange-500/30 bg-orange-500/10 px-2 py-0.5 text-[10px] font-semibold text-orange-600">
                                            Current
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <hr class="mb-3 border-slate-200">

                            <div class="flex items-center justify-between gap-2 text-[11px] text-slate-500">
                                <span>Tap to select and switch</span>
                            </div>
                        </button>
                    @empty
                        <div
                            class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center">
                            <h3 class="text-lg font-bold text-slate-950">No Tables Found</h3>
                            <p class="mt-2 text-sm text-slate-500">
                                This branch has no tables to switch to.
                            </p>
                        </div>
                    @endforelse
                </div>

                <div id="tableSwitchEmptyState"
                    class="hidden rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                    <h3 class="text-lg font-bold text-slate-950">No Matching Tables</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Try a different search term or filter.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                <p class="text-xs text-slate-500">
                    Draft cart stays saved per table automatically.
                </p>

                <button id="closeTableSwitchFooter" type="button"
                    class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm hover:bg-slate-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
