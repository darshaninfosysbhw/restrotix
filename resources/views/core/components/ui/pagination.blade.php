@props([
    'paginator' => null,
    'label' => 'items',
    'showSummary' => true,
    'summaryClass' => 'text-sm text-gray-400',
    'navClass' => 'flex flex-wrap items-center gap-2',
])

@php
    $total = $paginator?->total() ?? 0;
    $firstItem = $paginator?->firstItem() ?? 0;
    $lastItem = $paginator?->lastItem() ?? 0;
    $hasPages = $paginator?->hasPages() ?? false;
@endphp

@if ($paginator)
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        @if ($showSummary)
            <p class="{{ $summaryClass }}">
                @if ($total > 0 && $firstItem > 0)
                    Showing {{ $firstItem }} to {{ $lastItem }} of {{ $total }} {{ $label }}
                @else
                    Showing 0 of {{ $total }} {{ $label }}
                @endif
            </p>
        @endif

        @if ($hasPages)
            <div {{ $attributes->merge(['class' => $navClass]) }}>
                {{ $paginator->links() }}
            </div>
        @endif
    </div>
@endif
