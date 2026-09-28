<div aria-label="Platform benefits" class="mx-auto mt-5 max-w-6xl">
        <div class="grid grid-cols-1 overflow-hidden rounded-xl border border-[#a52a28]/15 bg-white shadow-[0_12px_30px_-20px_rgba(15,23,42,0.4)] sm:grid-cols-2 lg:grid-cols-4">
            @php
                $benefits = [
                    ['icon' => 'fa-gear', 'title' => 'Quick Setup', 'description' => 'Get started in minutes', 'colors' => 'bg-orange-100 text-[#f05a28]'],
                    ['icon' => 'fa-headset', 'title' => 'Nepal-based Support', 'description' => 'Friendly local team', 'colors' => 'bg-emerald-100 text-emerald-700'],
                    ['icon' => 'fa-cloud', 'title' => 'Cloud Based', 'description' => 'Access from anywhere', 'colors' => 'bg-orange-100 text-[#f05a28]'],
                    ['icon' => 'fa-shield-halved', 'title' => 'Secure & Reliable', 'description' => 'Your data is always safe', 'colors' => 'bg-emerald-100 text-emerald-700'],
                ];
            @endphp

            @foreach ($benefits as $benefit)
                <div class="relative flex items-center gap-3 border-b border-[#a52a28]/10 px-4 py-3.5 odd:bg-[#fffaf9] even:bg-[#fffdf8] last:border-b-0 sm:[&:nth-child(odd)]:border-r lg:border-b-0 lg:border-r lg:last:border-r-0">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $benefit['colors'] }}">
                        <i class="fa-solid {{ $benefit['icon'] }} text-base" aria-hidden="true"></i>
                    </span>
                    <span class="min-w-0">
                        <strong class="block text-sm font-extrabold leading-tight text-slate-900">{{ $benefit['title'] }}</strong>
                        <span class="mt-1 block text-xs leading-tight text-slate-500">{{ $benefit['description'] }}</span>
                    </span>
                </div>
            @endforeach
        </div>
</div>
