@props([
    'id',
    'backdropId',
    'titleId',
    'subtitleId',
    'closeId',
    'formId',
    'storeUrl',
    'methodId',
    'cancelId',
    'submitId',
    'title',
    'subtitle',
    'action',
    'submitLabel' => 'Save',
    'submitIcon' => null,
    'submitIconClass' => 'mr-2',
    'maxWidthClass' => 'max-w-3xl',
    'bodyClass' => 'no-scrollbar flex-1 min-h-0 overflow-y-auto p-5 space-y-6',
    'footerClass' => 'flex-none flex justify-end gap-3 px-5 py-4 border-t border-gray-800 bg-gray-800',
    'cancelButtonClass' =>
        'px-5 py-2.5 rounded-lg text-sm bg-white/5 hover:bg-white/10 text-gray-300 transition duration-200 cursor-pointer',
    'submitButtonClass' =>
        'px-5 py-2.5 rounded-lg text-sm bg-orange-500 hover:bg-orange-600 text-white font-semibold shadow-lg shadow-orange-500/20 transition duration-200 cursor-pointer',
    'overlayClass' => 'absolute inset-0 bg-black/50',
    'cardExtraClass' => '',
])

@php
    $cardClass = trim(
        'w-full ' .
            $maxWidthClass .
            ' max-h-[calc(100dvh-2rem)] overflow-hidden bg-gray-800 border border-gray-700 rounded-lg flex flex-col ' .
            $cardExtraClass,
    );
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-[120] hidden overflow-y-auto">
    <div id="{{ $backdropId }}" class="{{ $overlayClass }}"></div>
    <div class="relative z-10 min-h-screen flex items-start md:items-center justify-center p-4">
        <div {{ $attributes->merge(['class' => $cardClass]) }}>
            <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
                <div>
                    <h2 id="{{ $titleId }}" class="text-lg font-semibold text-white">{{ $title }}</h2>
                    <p id="{{ $subtitleId }}" class="text-xs text-gray-400 mt-1">{{ $subtitle }}</p>
                </div>
                <button id="{{ $closeId }}" type="button"
                    class="w-8 h-8 rounded-md bg-white/5 hover:bg-white/10 text-gray-300 transition cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="{{ $formId }}" action="{{ $action }}" method="POST"
                data-store-url="{{ $storeUrl }}" class="flex min-h-0 flex-1 flex-col">
                @csrf
                <input id="{{ $methodId }}" type="hidden" name="_method" value="PUT" disabled>

                <div class="{{ $bodyClass }}">
                    {{ $slot }}
                </div>

                <div class="{{ $footerClass }}">
                    <button id="{{ $cancelId }}" type="button" class="{{ $cancelButtonClass }}">
                        Cancel
                    </button>
                    <button id="{{ $submitId }}" type="submit" class="{{ $submitButtonClass }}">
                        @if ($submitIcon)
                            <i class="{{ $submitIcon }} {{ $submitIconClass }}"></i>
                        @endif
                        {{ $submitLabel }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
