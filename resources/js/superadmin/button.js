(() => {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-modal-open]');
        if (!button) return;

        const modalId = button.dataset.modalOpen;
        if (!modalId) return;

        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Let page modules prepare form state before the shared opener toggles the modal.
        document.dispatchEvent(new CustomEvent('sa:modal:create-open', {
            detail: {
                modalId,
                modal,
                trigger: button,
            },
        }));

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
})();
