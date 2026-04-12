/**
 * Payment Gateway UI Logic (Final - Global Modal Compatible)
 * Works with: data-modal-open + sa:modal:create-open
 */

(() => {

    // Modal Pre-Open Setup (GLOBAL SYSTEM HOOK)
    document.addEventListener('sa:modal:create-open', (event) => {
        if (event.detail?.modalId !== 'gatewayModal') return;

        const modal = event.detail.modal;
        const trigger = event.detail.trigger;

        if (!modal) return;

        // Extract Data (safe defaults for Add button)
        const name = trigger?.dataset?.name || 'New Gateway';
        const slug = trigger?.dataset?.slug || 'stripe';
        const mode = trigger?.dataset?.mode || 'sandbox';

        // Header Update
        const nameDisplay = modal.querySelector('#modalGatewayName');
        if (nameDisplay) nameDisplay.innerText = name;

        // Mode Select (Sandbox / Live)
        modal.querySelectorAll('input[name="mode"]').forEach(input => {
            input.checked = (input.value === mode);
        });

        // Reset Fields Visibility
        modal.querySelectorAll('.gateway-fields').forEach(el => {
            el.classList.add('hidden');
        });

        // Show Active Gateway Fields
        const activeFields = modal.querySelector(`#fields-${slug}`);
        if (activeFields) {
            activeFields.classList.remove('hidden');
        }

        // Reset Form (important for fresh state)
        const form = modal.querySelector('form');
        if (form) form.reset();
    });


    // Close Modal (Reusable)
    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };

    // Close Button Click
    document.addEventListener('click', (e) => {
        const closeBtn = e.target.closest('.closeModal');
        if (!closeBtn) return;

        const modal = closeBtn.closest('#gatewayModal');
        closeModal(modal);
    });

    // Backdrop Click Close
    document.addEventListener('click', (e) => {
        if (!e.target.classList.contains('sa-overlay-close')) return;

        const modal = document.getElementById('gatewayModal');
        closeModal(modal);
    });

    // ESC Key Close
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;

        const modal = document.getElementById('gatewayModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeModal(modal);
        }
    });

    // Password Toggle (Eye Icon)
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.togglePassword');
        if (!btn) return;

        const wrapper = btn.closest('div');
        const input = wrapper?.querySelector('input');
        const icon = btn.querySelector('i');

        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            if (icon) icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

})();
