@php
    $selectedFilter = $statusFilter ?? 'all';
    $filterConfig = [
        'all' => ['label' => 'All Orders', 'count' => $stats['all_orders'] ?? 0, 'badge' => 'bg-white/20 text-white'],
        'new' => ['label' => 'New', 'count' => $stats['new_orders'] ?? 0, 'badge' => 'bg-red-500 text-white'],
        'preparing' => [
            'label' => 'Preparing',
            'count' => $stats['preparing_orders'] ?? 0,
            'badge' => 'bg-blue-500 text-white',
        ],
        'ready' => ['label' => 'Ready', 'count' => $stats['ready_orders'] ?? 0, 'badge' => 'bg-green-500 text-white'],
    ];
@endphp

@foreach ($filterConfig as $key => $tab)
    @php
        $isActive = $selectedFilter === $key;
        $tabBaseClass = $isActive
            ? 'bg-orange-600 text-white shadow-lg shadow-orange-600/20'
            : 'bg-gray-800 text-gray-400 hover:bg-gray-700 border border-gray-700';
    @endphp
    <a href="{{ route('admin.kds.index', ['status' => $key]) }}"
        class="shrink-0 flex items-center gap-3 px-3 sm:px-4 py-2 rounded-lg transition-all {{ $tabBaseClass }}">
        <span class="text-xs font-bold uppercase tracking-wider">{{ $tab['label'] }}</span>
        <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $tab['badge'] }}">
            {{ $tab['count'] }}
        </span>
    </a>
@endforeach
