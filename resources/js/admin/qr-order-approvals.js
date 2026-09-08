function initQrApprovals() {
    const root = document.querySelector('[data-qr-approvals]');
    if (!root || root.dataset.initialized) return;
    root.dataset.initialized = '1';
    const list = root.querySelector('[data-qr-list]');
    const error = root.querySelector('[data-qr-error]');
    const bell = document.getElementById('adminNotificationBellBtn') || document.getElementById('pickupAlertBellBtn');
    const badge = document.createElement('span');
    badge.className = 'hidden text-[10px] font-bold text-orange-500 ml-1';
    badge.setAttribute('aria-live', 'polite');
    bell?.append(badge);
    let fetching = false, busy = false, timer;
    const escape = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[c]));
    async function refresh() {
        if (fetching || busy) return;
        fetching = true;
        try {
            const response = await fetch(root.dataset.url, { headers: { Accept: 'application/json' }, signal: AbortSignal.timeout(15000) });
            if (!response.ok) throw new Error('Unable to load pending QR orders. Retrying automatically.');
            const rows = await response.json();
            if (busy) return;
            root.classList.toggle('hidden', rows.length === 0);
            badge.classList.toggle('hidden', rows.length === 0);
            badge.textContent = `QR ${rows.length}`;
            root.querySelector('[data-qr-count]').textContent = `(${rows.length})`;
            list.innerHTML = rows.map(row => `<article class="p-4 border-b border-gray-700" data-submission="${row.id}">
                <p class="text-sm font-semibold text-white">Table ${escape(row.table_number)} · ${escape(row.currency)} ${escape(row.total)} · ${row.quantity} items</p>
                <ul class="text-xs text-gray-400 mt-2 space-y-1">${row.items.map(item => `<li>${item.quantity} × ${escape(item.name)} ${escape(item.variant)}${item.addons?.length ? ` + ${item.addons.map(a => `${a.quantity} × ${escape(a.name)}`).join(', ')}` : ''}${item.notes ? ` — ${escape(item.notes)}` : ''}</li>`).join('')}</ul>
                ${row.notes ? `<p class="text-xs text-gray-400 mt-2">${escape(row.notes)}</p>` : ''}
                <div class="flex gap-2 mt-3"><button type="button" data-action="accept" data-url="${escape(row.accept_url)}" class="rounded-lg bg-orange-500 text-white text-xs px-3 py-2 disabled:opacity-50">Accept Order</button>
                <button type="button" data-action="reject" data-url="${escape(row.accept_url)}" class="rounded-lg border border-gray-600 text-gray-400 text-xs px-3 py-2 disabled:opacity-50">Reject</button></div>
                </article>`).join('');
            error.classList.add('hidden');
        } catch (exception) {
            root.classList.remove('hidden');
            error.textContent = exception.message;
            error.classList.remove('hidden');
        } finally { fetching = false; }
    }
    root.addEventListener('click', async event => {
        const button = event.target.closest('[data-action]');
        if (!button || busy) return;
        event.stopPropagation();
        let reason = null;
        if (button.dataset.action === 'reject') {
            reason = window.prompt('Reason for rejecting this order:', 'Item unavailable');
            if (reason === null) return;
        }
        busy = true;
        root.querySelectorAll('button').forEach(b => b.disabled = true);
        const originalText = button.textContent;
        button.textContent = 'Please wait…';
        try {
            const response = await fetch(button.dataset.url, {
                method: 'POST', signal: AbortSignal.timeout(20000),
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                body: JSON.stringify({ action: button.dataset.action, reason }),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(Object.values(data.errors || {}).flat()[0] || data.message || 'Please retry.');
            window.showToast?.({ type: 'success', message: data.message, duration: 3500 });
            error.classList.add('hidden');
        } catch (exception) {
            error.textContent = exception.name === 'TimeoutError' ? 'Timed out. Retrying is safe; an order can only be accepted once.' : exception.message;
            error.classList.remove('hidden');
            window.showToast?.({ type: 'error', message: error.textContent, duration: 4500 });
        } finally {
            busy = false;
            button.textContent = originalText;
            root.querySelectorAll('button').forEach(b => b.disabled = false);
            await refresh();
        }
    });
    const branchId = Number(root.dataset.branchId);
    window.Echo?.private(`qr-approvals.branch.${branchId}`).listen('QrOrderSubmissionUpdated', event => {
        refresh();
        if (event.submissionData?.status === 'pending') {
            window.showToast?.({ type: 'info', message: 'New QR order awaiting confirmation. Open notifications to accept.', duration: 5000 });
            let enabled = false;
            try { enabled = window.tableSoundEnabled?.() || localStorage.getItem(`kds_sound_enabled_v1:${branchId}`) === '1'; } catch (_) {}
            if (enabled) {
                const audio = new Audio('/sounds/forOrder.m4a');
                audio.play().catch(() => {});
            }
        }
    });
    refresh();
    timer = setInterval(() => { if (!document.hidden) refresh(); }, 10000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
    window.addEventListener('pagehide', () => clearInterval(timer), { once: true });
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initQrApprovals);
else initQrApprovals();
