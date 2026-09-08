@if (in_array(auth()->user()->role, ['admin', 'manager', 'waiter'], true))
    <section data-qr-approvals data-url="{{ route('qr-submissions.index') }}"
        data-branch-id="{{ auth()->user()->role === 'admin' ? (int) session('active_branch_id', auth()->user()->branch_id) : (int) auth()->user()->branch_id }}"
        class="hidden border-b border-orange-500/30 bg-orange-500/5">
        <h4 class="px-4 pt-3 text-sm font-semibold text-orange-500">QR orders awaiting confirmation <span data-qr-count></span></h4>
        <div data-qr-list class="max-h-72 overflow-y-auto"></div>
        <p data-qr-error class="hidden px-4 py-2 text-xs text-red-500" role="alert"></p>
    </section>
@endif
