<div class="bg-gray-800 border border-gray-700 rounded-xl p-5">

    {{-- Header / Search --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">

        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-400">
                All Areas / Floors
            </span>

            <span
                class="px-2.5 py-1 rounded-full text-xs bg-orange-500/10 text-orange-500 border border-orange-500/30">
                Total : {{ $stats['total'] }}
            </span>
        </div>

        <form
            id="areaSearchForm"
            method="GET"
            action="{{ route('admin.areas.index') }}"
            class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto"
        >

            {{-- Search --}}
            <div class="relative w-full sm:w-64">

                <i
                    class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">
                </i>

                <input
                    id="areaTableSearch"
                    name="search"
                    type="text"
                    value="{{ request('search') }}"
                    placeholder="Search area / floor..."
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                >

            </div>

            {{-- Reset --}}
            @if (request()->filled('search'))
                <a
                    href="{{ route('admin.areas.index') }}"
                    class="px-3 py-2 rounded-lg text-xs bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10 transition cursor-pointer"
                >
                    Reset
                </a>
            @endif

        </form>
    </div>


    {{-- Table --}}
    <div class="overflow-x-auto overflow-y-visible">

        <table class="w-full text-sm">

            <thead
                class="text-xs text-gray-400 border-b border-gray-700 uppercase tracking-wide">

                <tr>
                    <th class="text-left py-3 pr-4 font-medium">
                        #
                    </th>

                    <th class="text-left py-3 px-4 font-medium">
                        Area / Floor
                    </th>

                    <th class="text-left py-3 px-4 font-medium">
                        Tables
                    </th>

                    <th class="text-left py-3 px-4 font-medium">
                        Status
                    </th>

                    <th class="text-left py-3 px-4 font-medium">
                        Created
                    </th>

                    <th class="text-left py-3 pl-8 font-medium">
                        Action
                    </th>
                </tr>

            </thead>


            <tbody
                id="areaTableBody"
                class="divide-y divide-gray-700/80"
            >

                @forelse ($areas as $index => $area)

                    <tr class="area-row hover:bg-white/5 transition">

                        {{-- Number --}}
                        <td class="py-3 pr-4 text-gray-300">
                            {{ $index + 1 }}
                        </td>


                        {{-- Area Name --}}
                        <td class="py-3 px-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="w-9 h-9 rounded-lg bg-orange-500/10 text-orange-500 flex items-center justify-center flex-shrink-0">

                                    <i class="fas fa-layer-group text-sm"></i>

                                </div>

                                <div>
                                    <p class="font-medium text-white">
                                        {{ $area->name }}
                                    </p>

                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Area / Floor
                                    </p>
                                </div>

                            </div>

                        </td>


                        {{-- Tables Count --}}
                        <td class="py-3 px-4">

                            <span
                                class="inline-flex items-center gap-1.5 bg-orange-500/15 border border-orange-500/30 text-gray-200 px-2.5 py-1.5 rounded-full text-xs">

                                <i class="fas fa-chair text-orange-500"></i>

                                {{ $area->tables_count }}

                                {{ $area->tables_count == 1 ? 'Table' : 'Tables' }}

                            </span>

                        </td>


                        {{-- Status --}}
                        <td class="py-3 px-4">

                            @if ($area->is_active)

                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-400">

                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>

                                    Active
                                </span>

                            @else

                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs bg-slate-500/20 text-slate-300">

                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>

                                    Inactive
                                </span>

                            @endif

                        </td>


                        {{-- Created --}}
                        <td class="py-3 px-4 text-gray-300">
                            {{ $area->created_at?->format('d M Y') ?? '-' }}
                        </td>


                        {{-- Actions --}}
                        <td class="py-3 pl-8">

                            @include('core.components.actions', [

                                'editClass' => 'openAreaEditModal',

                                'editData' => [
                                    'update-url' => route('admin.areas.update', $area->id),
                                    'area-name' => $area->name,
                                    'area-code' => $area->code,
                                    'is-active' => $area->is_active ? '1' : '0',
                                ],

                                'deleteRoute' => route('admin.areas.destroy', $area->id),

                                'deleteConfirm' =>
                                    $area->tables_count > 0
                                        ? 'This area has assigned tables and cannot be deleted until those tables are moved or unassigned.'
                                        : 'Are you sure you want to delete this area?',
                            ])

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="6"
                            class="py-10 text-center text-sm text-gray-400"
                        >

                            <div
                                class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-700 flex items-center justify-center">

                                <i class="fas fa-layer-group text-gray-400"></i>

                            </div>

                            <p class="font-medium text-gray-300">
                                No areas found
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                Create your first area or floor to organize tables.
                            </p>

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>