<div
    class="flex flex-col gap-4 border-b border-slate-200 py-2 lg:flex-row lg:items-center lg:justify-between">
    <div class="flex items-center gap-3">
        <div class="flex h-7.5 w-7 items-center justify-center rounded-2xl bg-orange-500 text-white shadow-md shadow-orange-500/20">
            <i class="fas fa-receipt text-xs"></i>
        </div>
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xs font-extrabold tracking-tight text-slate-950 sm:text-xs lg:text-xs">
                    CHECKOUT
                </h1>
                <span class="text-slate-400">—</span>
                <span class="text-sm font-semibold text-slate-600">Table {{ $tableNo }}</span>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
        <span class="inline-flex items-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-2 py-1.5 text-[10px] font-bold text-orange-600">
            <span class="uppercase tracking-[0.18em]">Token:</span>
            {{ $token }}
        </span>
        <button type="button"
            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1.5 text-[10px] font-semibold text-slate-700 transition hover:bg-slate-100 cursor-pointer">
            <i class="fas fa-bolt text-xs"></i>
            Quick Mode
        </button>
        <button type="button"
            id="billingDownloadEstimateBtn"
            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-[10px] font-semibold text-slate-700 transition hover:bg-slate-50 cursor-pointer">
            <i class="fas fa-download text-xs"></i>
            Download
        </button>
        <button type="button"
            id="billingPrintEstimateBtn"
            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-[10px] font-semibold text-slate-700 transition hover:bg-slate-50 cursor-pointer">
            <i class="fas fa-print text-xs"></i>
            Print Estimate
        </button>
    </div>
</div>
