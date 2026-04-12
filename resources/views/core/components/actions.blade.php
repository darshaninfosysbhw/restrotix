<div class="flex items-center gap-2">
    <div class="relative group">

        <button type="button"
            class="actionTrigger w-8 h-8 rounded-md text-slate-300 bg-white/5 hover:bg-white/10 border border-white/10 transition inline-flex items-center justify-center">
            <i class="fas fa-ellipsis-v text-xs"></i>
        </button>

        <div
            class="actionMenu hidden absolute right-0 top-full mt-2 w-36 z-[200] rounded-lg border border-white/10 bg-[#111827] shadow-xl p-1.5 space-y-1">

            {{-- View --}}
            @if (isset($viewData))
                <button type="button"
                    class="{{ $viewClass ?? 'openViewModal' }} w-full text-left px-2.5 py-1.5 rounded-md text-xs bg-orange-500/15 hover:bg-orange-500/25 text-orange-500 border border-orange-500/30 transition"
                    @foreach ($viewData as $key => $value)
                        data-{{ $key }}="{{ $value }}" @endforeach>
                    {{ $viewLabel ?? 'View' }}
                </button>
            @endif

            {{-- Edit --}}
            @if (isset($editData))
                <button type="button"
                    class="{{ $editClass ?? 'openEditModal' }} w-full text-left px-2.5 py-1.5 rounded-md text-xs bg-orange-500/15 hover:bg-orange-500/25 text-orange-500 border border-orange-500/30 transition"
                    @foreach ($editData as $key => $value)
                        data-{{ $key }}="{{ $value }}" @endforeach>
                    Edit
                </button>
            @endif

            {{-- Delete --}}
            @if (isset($deleteRoute))
                <form action="{{ $deleteRoute }}" method="POST"
                    onsubmit="return confirm('{{ $deleteConfirm ?? 'Are you sure you want to delete this item?' }}')">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="w-full text-left px-2.5 py-1.5 rounded-md text-xs bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 transition">
                        Delete
                    </button>
                </form>
            @endif

        </div>
    </div>
</div>
