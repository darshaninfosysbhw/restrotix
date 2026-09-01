<div class="actionGroup">
    <button type="button" aria-haspopup="menu" aria-expanded="false" aria-label="Open row actions"
        class="actionTrigger cursor-pointer">
        <i class="fas fa-ellipsis-v text-[11px]"></i>
    </button>

    <div class="actionMenu hidden">

        {{-- View --}}
        @if (isset($viewData))
            <button type="button" class="actionMenuItem actionMenuItem--view {{ $viewClass ?? 'openViewModal' }}"
                @foreach ($viewData as $key => $value)
                    data-{{ $key }}="{{ $value }}" @endforeach>
                {{ $viewLabel ?? 'View' }}
            </button>
        @endif

        {{-- Edit --}}
        @if (isset($editData))
            <button type="button"
                class="actionMenuItem actionMenuItem--edit {{ $editClass ?? 'openEditModal' }} cursor-pointer"
                @foreach ($editData as $key => $value)
                    data-{{ $key }}="{{ $value }}" @endforeach>
                Edit
            </button>
        @endif

        {{-- Delete --}}
        @if (isset($deleteRoute))
            <form class="actionMenuForm" action="{{ $deleteRoute }}" method="POST"
                onsubmit="return confirm('{{ $deleteConfirm ?? 'Are you sure you want to delete this item?' }}')">
                @csrf
                @method('DELETE')

                <button type="submit" class="actionMenuItem actionMenuItem--delete cursor-pointer">
                    Delete
                </button>
            </form>
        @endif

    </div>
</div>
