@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-wrap items-center gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-700 bg-gray-900 px-3 text-sm text-gray-500 opacity-50"
                aria-disabled="true" aria-label="@lang('pagination.previous')">
                <i class="fas fa-chevron-left text-[10px]"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-700 bg-gray-900 px-3 text-sm text-gray-300 transition hover:bg-gray-800"
                rel="prev" aria-label="@lang('pagination.previous')">
                <i class="fas fa-chevron-left text-[10px]"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-1 text-gray-500">...</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                            class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-orange-500/30 bg-orange-500/10 px-3 text-sm font-medium text-orange-500">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                            class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-gray-700 bg-gray-900 px-3 text-sm text-gray-300 transition hover:bg-gray-800"
                            aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-700 bg-gray-900 px-3 text-sm text-gray-300 transition hover:bg-gray-800"
                rel="next" aria-label="@lang('pagination.next')">
                <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        @else
            <span class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-700 bg-gray-900 px-3 text-sm text-gray-500 opacity-50"
                aria-disabled="true" aria-label="@lang('pagination.next')">
                <i class="fas fa-chevron-right text-[10px]"></i>
            </span>
        @endif
    </nav>
@endif
