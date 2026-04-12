@props([
    'title', // Card title
    'value', // Card value
    'icon', // FontAwesome icon class
    'color' => 'orange', // Tailwind color: orange, emerald, sky, etc.
])

<div class="glass-panel p-4">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs text-slate-400 uppercase tracking-wide">{{ $title }}</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $value }}</p>
        </div>
        <div
            class="w-9 h-9 rounded-full bg-{{ $color }}-500/15 text-{{ $color }}-400 flex items-center justify-center">
            <i class="{{ $icon }} text-sm"></i>
        </div>
    </div>
</div>
