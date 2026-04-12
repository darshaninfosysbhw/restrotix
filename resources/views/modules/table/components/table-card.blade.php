@forelse ($tables as $table)
    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 card-hover table-card cursor-pointer"
        data-id="{{ $table['id'] }}" data-name="{{ $table['display_name'] }}"
        data-table-number="{{ $table['table_number'] }}" data-orders='@json($table['active_orders'] ?? [])'>

        <div class="flex justify-between items-center mb-1">
            <h3 class="text-white font-semibold">
                {{ $table['display_name'] }}
            </h3>
            <div class="flex items-center gap-2">
                <span
                    class="waiter-call-bell hidden items-center gap-1 text-[10px] px-2 py-1 rounded-full border border-blue-500/60 bg-blue-500/20 text-blue-300 font-semibold">
                    <i class="fas fa-bell animate-bounce"></i>
                    <span class="waiter-call-count"></span>
                </span>
                <span
                    class="table-status-pill text-xs px-2 py-1 rounded-full
                bg-{{ $table['status_color'] }}-500/20 text-{{ $table['status_color'] }}-400">
                    {{ $table['status_label'] }}
                </span>
                <span
                    class="kitchen-status-badge hidden text-[10px] px-2 py-1 rounded-full border border-gray-500/50 bg-gray-500/10 text-gray-300 font-semibold">
                    Kitchen
                </span>
                <span
                    class="new-order-badge hidden text-[10px] px-2 py-1 rounded-full border border-orange-400 bg-orange-100 text-orange-700 dark:border-orange-500/60 dark:bg-orange-500/20 dark:text-orange-300 font-semibold">
                    New
                </span>
            </div>
        </div>

        <p class="text-xs text-gray-400 mb-3">
            Token: {{ $table['qr_token'] ?: 'N/A' }}
        </p>
        <p class="last-order-activity text-[11px] text-orange-700 dark:text-orange-300/80 mb-3 hidden"></p>

        <hr class="mb-3 text-gray-600">

        <div class="flex items-center justify-between">

            <img class="qrPreview cursor-pointer border border-gray-600 rounded-lg p-2 card-hover"
                src="{{ $table['qr_code_inline'] }}" data-name="Table {{ $table['table_number'] }}"
                data-qr="{{ $table['qr_code_inline'] }}" />

            <div class="flex gap-2">

                <button class="viewQrBtn text-xs px-2 py-1 border border-gray-600 rounded-lg text-gray-300"
                    data-name="Table {{ $table['table_number'] }}" data-qr="{{ $table['qr_code_inline'] }}">
                    View
                </button>

                @if ($isAdmin)
                    <button class="editBtn text-xs px-2 py-1 border border-orange-500/40 text-orange-400 rounded-lg"
                        data-id="{{ $table['id'] }}" data-table-number="{{ $table['table_number'] }}"
                        data-branch="{{ $table['branch_id'] }}" data-capacity="{{ $table['capacity'] }}"
                        data-status="{{ $table['status'] }}"
                        data-update-url="{{ route('admin.tables.update', $table['id']) }}">
                        Edit
                    </button>
                @endif

            </div>
        </div>
    </div>
@empty
    <div class="col-span-1 sm:col-span-2 md:col-span-3 xl:col-span-4">
        <div
            class="bg-gray-800/80 border border-dashed border-gray-600 rounded-2xl p-8 md:p-12 text-center flex flex-col items-center">
            <div
                class="w-20 h-20 rounded-2xl bg-orange-500/15 border border-orange-500/30 text-orange-400 flex items-center justify-center mb-5">
                <i class="fas fa-chair text-3xl"></i>
            </div>

            <h3 class="text-2xl font-bold text-white">No Tables Found</h3>
            <p class="text-sm text-gray-400 mt-2 max-w-md">
                Your table directory is empty right now. Add your first table set to start generating QR access.
            </p>

            @if ($isAdmin ?? false)
                <button type="button" onclick="document.getElementById('openTableModal').click()"
                    class="mt-6 inline-flex items-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-400 border border-orange-500/30 px-5 py-2.5 rounded-lg text-sm font-medium">
                    <i class="fas fa-plus"></i>
                    Add Your First Table
                </button>
            @endif
        </div>
    </div>
@endforelse
