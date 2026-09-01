@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, ghost
])

@php
    $base = 'inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition';

    $variants = [
        'primary' => 'bg-orange-500/15 hover:bg-orange-500/25 text-orange-500 border border-orange-500/30',
        'secondary' => 'bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10',
    ];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $base . ' ' . $variants[$variant]]) }}>
    {{ $slot }}
</button>
