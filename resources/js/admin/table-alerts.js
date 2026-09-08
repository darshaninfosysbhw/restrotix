document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-table-alert]');
    if (!button) return;
    event.preventDefault();
    event.stopPropagation();
    if (button.disabled) return;
    const card = button.closest('.table-card');
    if (!card) return;
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 15000);
    try {
        const response = await fetch(button.dataset.url, {
            method: 'POST', signal: controller.signal,
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        });
        if (!response.ok) throw new Error('Unable to clear request. Please try again.');
        const result = await response.json();
        const isCall = button.dataset.tableAlert === 'call';
        const flag = isCall ? 'is_calling_waiter' : 'is_bill_requested';
        if (result[flag] !== false) throw new Error('Request was not cleared. Please try again.');
        card.dataset[isCall ? 'isCallingWaiter' : 'isBillRequested'] = '0';
        card.classList.remove(isCall ? 'waiter-call-active' : 'request-bill-active');
        if (isCall) window.markWaiterCallSeen?.(card.dataset.tableNumber);
        window.syncTableStatsFromCards?.();
        const counter = button.querySelector(isCall ? '.waiter-call-count' : '.bill-request-count');
        if (counter) counter.textContent = '';
        if (isCall && button.animate && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            await button.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 160 }).finished;
        }
        // A fresh realtime call received while clearing must remain visible.
        if (card.dataset[isCall ? 'isCallingWaiter' : 'isBillRequested'] === '0') button.style.display = 'none';
        if (typeof window.showToast === 'function') {
            window.showToast({
                type: 'success',
                message: `Table ${card.dataset.tableNumber}: ${isCall ? 'Waiter call accepted.' : 'Bill request cleared.'}`,
                duration: 3500,
            });
        }
    } catch (error) {
        const message = error.name === 'AbortError' ? 'Request timed out. Please retry.' : error.message;
        if (typeof window.showToast === 'function') window.showToast({ type: 'error', message, duration: 4000 });
        else window.alert(message);
    } finally {
        clearTimeout(timeout);
        button.disabled = false;
        button.removeAttribute('aria-busy');
    }
}, true);
