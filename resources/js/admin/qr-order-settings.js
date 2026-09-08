function initQrOrderSettings() {
    const form = document.querySelector('[data-qr-order-settings]');
    if (!form) return;
    const toggle = form.querySelector('[role="switch"]');
    let saved = toggle.checked;
    form.addEventListener('submit', event => event.preventDefault());
    toggle.addEventListener('change', async () => {
        if (toggle.disabled) return;
        const enabled = toggle.checked;
        toggle.disabled = true;
        form.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(form.action, {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                },
                body: JSON.stringify({
                    branch_id: form.querySelector('[name="branch_id"]').value,
                    auto_accept_qr_orders: enabled,
                }),
                signal: AbortSignal.timeout(15000),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Unable to save settings.');
            saved = data.auto_accept_qr_orders;
            toggle.checked = saved;
            window.showToast({ type: 'success', message: `Auto-accept QR orders turned ${saved ? 'ON' : 'OFF'}.`, duration: 3500 });
        } catch (error) {
            toggle.checked = saved;
            const message = error.name === 'TimeoutError' || error instanceof TypeError
                ? 'Could not confirm the save. Please refresh to check the current setting.'
                : `Could not save. ${error.message}`;
            window.showToast({ type: 'error', message, duration: 5000 });
        } finally {
            toggle.disabled = false;
            form.removeAttribute('aria-busy');
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQrOrderSettings);
} else {
    initQrOrderSettings();
}
