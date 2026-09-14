<div class="flex flex-wrap items-center gap-2 " id="areaFilterBar">

    {{-- ALL --}}
    <button type="button"
        class="area-filter-btn active px-3 py-2 rounded-md text-xs font-medium
               border border-orange-500/30 bg-orange-500/10 text-orange-400 transition cursor-pointer"
        data-area-filter="all">
        All Tables
        <span class="ml-1 text-[10px] opacity-70">
            {{ $tables->count() }}
        </span>
    </button>

    {{-- GENERAL --}}
    @php
        $generalCount = $tables->filter(
            fn ($table) => empty($table['area_id'])
        )->count();
    @endphp

    <button type="button"
        class="area-filter-btn px-3 py-2 rounded-md text-xs font-medium
               border border-gray-700 text-gray-400 hover:text-orange-400 hover:border-orange-500/30 transition cursor-pointer"
        data-area-filter="general">
        General
        <span class="ml-1 text-[10px] opacity-70">
            {{ $generalCount }}
        </span>
    </button>

    {{-- AREAS --}}
    @foreach ($areas as $area)
        @php
            $areaCount = $tables->filter(
                fn ($table) => (int) ($table['area_id'] ?? 0) === (int) $area->id
            )->count();
        @endphp

        <button type="button"
            class="area-filter-btn px-3 py-2 rounded-md text-xs font-medium
                   border border-gray-700 text-gray-400 hover:text-orange-400 hover:border-orange-500/30 transition cursor-pointer"
            data-area-filter="{{ $area->id }}">
            {{ $area->name }}

            <span class="ml-1 text-[10px] opacity-70">
                {{ $areaCount }}
            </span>
        </button>
    @endforeach


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButtons = document.querySelectorAll('.area-filter-btn');
        const tableCards = document.querySelectorAll('.table-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                const selectedArea = this.dataset.areaFilter;

                // Active button UI
                filterButtons.forEach(btn => {
                    btn.classList.remove(
                        'active',
                        'border-orange-500/30',
                        'bg-orange-500/10',
                        'text-orange-400'
                    );

                    btn.classList.add(
                        'border-gray-700',
                        'text-gray-400'
                    );
                });

                this.classList.remove(
                    'border-gray-700',
                    'text-gray-400'
                );

                this.classList.add(
                    'active',
                    'border-orange-500/30',
                    'bg-orange-500/10',
                    'text-orange-400'
                );

                // Filter table cards
                tableCards.forEach(card => {
                    const cardAreaId = card.dataset.areaId || '';

                    let shouldShow = false;

                    if (selectedArea === 'all') {
                        shouldShow = true;
                    } else if (selectedArea === 'general') {
                        shouldShow = cardAreaId === '';
                    } else {
                        shouldShow = cardAreaId === selectedArea;
                    }

                    card.classList.toggle('hidden', !shouldShow);
                });
            });
        });
    });
</script>

</div>