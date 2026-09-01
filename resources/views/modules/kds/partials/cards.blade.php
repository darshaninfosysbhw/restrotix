@forelse ($orderCards as $order)
    <div data-order-id="{{ $order['id'] }}" data-batch-key="{{ $order['batch_key'] ?? '' }}"
        data-table-number="{{ $order['table_number'] ?? '' }}" data-kot-number="{{ $order['kot_number'] ?? '' }}"
        data-created-at="{{ $order['created_at_iso'] ?? '' }}"
        class="border rounded-xl p-4 {{ $order['card_class'] }} transition-all duration-300">

        <div class="flex justify-between items-center mb-3 gap-2">
            <span class="px-2 py-1 text-white text-[10px] font-bold rounded {{ $order['status_badge_class'] }}">
                {{ $order['is_urgent'] ? 'URGENT' : $order['status_label'] }}
            </span>
            <span class="kds-timer {{ $order['timer_class'] }} text-xs font-mono">
                Timer: {{ $order['timer_text'] }}
            </span>
        </div>

        <div class="mb-3 ">
            @if (!empty($order['kot_label']))
                <p class="text-[11px] font-bold text-white leading-none text-center">
                    {{ $order['kot_label'] }}
                </p>
            @endif
            <h4 class="mt-1 text-[12px] font-medium leading-tight text-white break-words">
                Order: <span class="text-orange-400 text-[12px] ">{{ $order['order_number'] }}</span>
            </h4>
            <p class="mt-1 text-[11px] text-gray-500">
                {{ $order['order_type'] }}
                @if (!empty($order['table_number']))
                    • Table {{ $order['table_number'] }}
                @endif
            </p>
        </div>

        <ul class="mt-3 space-y-2 text-sm text-gray-300">
            @forelse ($order['items'] as $item)
                <li class="flex flex-col border-b border-gray-700/30 pb-2 last:border-0 last:pb-0">
                    <div class="flex justify-between items-start gap-2">
                        <span
                            class="{{ in_array($item['status'], ['ready', 'served', 'rejected']) ? 'text-gray-500 line-through' : '' }}">
                            {{ $item['quantity'] }}x {{ $item['item_name'] }}
                        </span>

                        <div class="flex gap-1">
                            @if (in_array($item['status'] ?? 'new', ['new', 'pending']))
                                <button type="button" onclick="updateItemStatus({{ $item['id'] }}, 'preparing')"
                                    class="p-1 bg-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white rounded transition-colors shadow-sm"
                                    title="Start Item">
                                    <i class="fas fa-play text-[9px]"></i>
                                </button>
                                <button type="button" onclick="rejectItem({{ $item['id'] }})"
                                    class="p-1 bg-red-500/20 text-red-400 hover:bg-red-500 hover:text-white rounded transition-colors shadow-sm"
                                    title="Reject Item">
                                    <i class="fas fa-times text-[9px]"></i>
                                </button>
                            @elseif(($item['status'] ?? '') === 'preparing')
                                <button type="button" onclick="updateItemStatus({{ $item['id'] }}, 'ready')"
                                    class="p-1 bg-green-500/20 text-green-400 hover:bg-green-500 hover:text-white rounded transition-colors shadow-sm"
                                    title="Ready Item">
                                    <i class="fas fa-check text-[9px]"></i>
                                </button>
                                <button type="button" onclick="rejectItem({{ $item['id'] }})"
                                    class="p-1 bg-red-500/20 text-red-400 hover:bg-red-500 hover:text-white rounded transition-colors shadow-sm"
                                    title="Reject Item">
                                    <i class="fas fa-times text-[9px]"></i>
                                </button>
                            @elseif(($item['status'] ?? '') === 'rejected')
                                <i class="fas fa-ban text-red-500 text-[10px] mt-1" title="Rejected"></i>
                            @else
                                <i class="fas fa-check-double text-green-500 text-[10px] mt-1"></i>
                            @endif
                        </div>
                    </div>

                    @if (($item['status'] ?? '') === 'rejected')
                        <span class="text-[10px] text-red-400 font-bold mt-1">Rejected:
                            {{ $item['rejection_reason'] ?? 'N/A' }}</span>
                    @endif

                    @if (!empty($item['addons']))
                        <div
                            class="mt-2 rounded-lg border border-orange-500/15 bg-orange-500/5 px-2.5 py-2 space-y-1.5">
                            @foreach ($item['addons'] as $addon)
                                @php
                                    $isDone = in_array($item['status'] ?? 'new', ['ready', 'served', 'rejected']);
                                @endphp
                                <div
                                    class="flex items-start gap-2 text-[11px] font-medium {{ $isDone ? 'text-gray-500 line-through' : 'text-orange-700 dark:text-orange-300' }}">
                                    <span
                                        class="mt-1 h-1.5 w-1.5 rounded-full {{ $isDone ? 'bg-gray-400' : 'bg-orange-500' }} shrink-0"></span>
                                    <div class="flex-1 min-w-0 flex items-center justify-between gap-2">
                                        <span class="truncate leading-4">{{ $addon['addon_name'] }}</span>
                                        @if (($addon['quantity'] ?? 1) > 1)
                                            <span
                                                class="shrink-0 rounded-full border {{ $isDone ? 'border-gray-400/30 bg-gray-500/10 text-gray-500' : 'border-orange-500/30 bg-orange-500/10 text-orange-700 dark:text-orange-200' }} px-1.5 py-0.5 text-[10px] font-semibold">
                                                x{{ $addon['quantity'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($item['notes']))
                        <span
                            class="text-left text-orange-400 text-[10px] italic mt-0.5 bg-orange-500/10 px-1.5 py-0.5 rounded w-max">
                            Note: {{ $item['notes'] }}
                        </span>
                    @endif
                </li>
            @empty
                <li class="text-xs text-gray-500">No items found</li>
            @endforelse
        </ul>

        @if (!empty($order['special_notes']))
            <div class="mt-3 p-2 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-[10px] uppercase text-red-400 font-bold tracking-wider">Order Note:</p>
                <p class="text-xs text-gray-200 italic font-medium">"{{ $order['special_notes'] }}"</p>
            </div>
        @endif

        @if (!empty($order['action']))
            <form action="{{ route('admin.kds.update-status', $order['id']) }}" method="POST">
                @csrf
                <input type="hidden" name="kot_number" value="{{ $order['kot_number'] ?? '' }}">
                <input type="hidden" name="status" value="{{ $order['action']['next_status'] }}">
                <button type="submit" @if (!empty($order['action']['disabled'])) disabled @endif
                    class="w-full mt-4 py-2 text-xs font-bold rounded-lg transition-all {{ $order['action']['button_class'] }} {{ !empty($order['action']['disabled']) ? 'cursor-not-allowed opacity-75' : '' }}">
                    {{ $order['action']['label'] }}
                </button>
            </form>
        @else
            <button
                class="w-full mt-4 py-2 border border-green-500/40 text-green-400 text-xs font-bold rounded-lg cursor-not-allowed opacity-80"
                disabled>
                READY
            </button>
        @endif
    </div>
@empty
    <div class="col-span-1 sm:col-span-2 lg:col-span-4 2xl:col-span-5">
        <div
            class="bg-gray-800/80 border border-dashed border-gray-600 rounded-2xl p-8 md:p-12 text-center flex flex-col items-center">
            <div
                class="w-16 h-16 rounded-2xl bg-orange-500/15 border border-orange-500/30 text-orange-400 flex items-center justify-center mb-4">
                <i class="fas fa-utensils text-2xl"></i>
            </div>
            <h3 class="text-xl md:text-2xl font-bold text-white">No Active Kitchen Orders</h3>
            <p class="text-sm text-gray-400 mt-2 max-w-md">
                New orders will appear here automatically.
            </p>
        </div>
    </div>
@endforelse

<script>
    function updateItemStatus(itemId, status) {
        axios.post(`/admin/kds/item/${itemId}/status`, {
            status: status
        })
            .then(res => {
                if (res.data.success) {
                    location.reload();
                }
            });
    }

    function rejectItem(itemId) {
        const reason = prompt("Enter rejection reason:");
        if (reason === null) return;
        if (reason.trim() === "") {
            alert("Reason required!");
            return;
        }

        axios.post(`/admin/kds/item/${itemId}/status`, {
            status: 'rejected',
            reason: reason
        }).then(res => {
            if (res.data.success) {
                location.reload();
            }
        });
    }
</script>
