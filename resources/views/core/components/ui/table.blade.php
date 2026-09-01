@props([
    'columns', // [['label' => 'Name', 'key' => 'name'], ['label' => 'Code', 'key' => 'code']]
    'rows', // array of objects/arrays
    'id' => 'dynamicTable', // optional, JS or search mapping
])

<div class="overflow-x-auto overflow-y-visible">
    <table id="{{ $id }}" class="w-full text-sm">
        <thead class="text-xs text-slate-400 border-b border-white/10 uppercase tracking-wide">
            <tr>
                @foreach ($columns as $col)
                    <th class="text-left py-3 px-4 font-medium">
                        {{ $col['label'] }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($rows as $row)
                <tr class="hover:bg-white/5 transition">
                    @foreach ($columns as $col)
                        <td class="py-3 px-4 text-slate-300">
                            @php
                                $value = data_get($row, $col['key'], '—');
                            @endphp
                            {{ $value }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" class="py-6 text-center text-sm text-slate-400">
                        No records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
