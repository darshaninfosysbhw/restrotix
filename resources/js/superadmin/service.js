// service modal-form js
(() => {
    const modal = document.getElementById('serviceModal');
    const openBtn = document.getElementById('openServiceModal');
    const closeBtn = document.getElementById('closeServiceModal');
    const cancelBtn = document.getElementById('cancelServiceModal');
    const backdrop = document.getElementById('serviceModalBackdrop');
    const form = document.getElementById('serviceForm');
    const methodInput = document.getElementById('serviceFormMethod');
    const modalTitle = document.getElementById('serviceModalTitle');
    const modalSubtitle = document.getElementById('serviceModalSubtitle');
    const modalSubmit = document.getElementById('serviceModalSubmit');
    const editButtons = Array.from(document.querySelectorAll('.openServiceEditModal'));
    const nameInput = document.getElementById('serviceName');
    const slugInput = document.getElementById('serviceSlug');
    const priceInput = document.getElementById('servicePrice');
    const statusInput = document.getElementById('serviceStatus');
    const descriptionInput = document.getElementById('serviceDescription');
    const searchInput = document.getElementById('serviceTableSearch');
    const resetBtn = document.getElementById('serviceSearchReset');
    const rows = Array.from(document.querySelectorAll('.service-row'));
    const noResultRow = document.getElementById('serviceNoResultRow');
    const countBadge = document.getElementById('serviceCountBadge');

    if (!modal || !openBtn || !closeBtn || !cancelBtn || !backdrop || !form || !methodInput) return;

    const openModal = () => {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };

    const setCreateMode = () => {
        form.reset();
        form.action = modal.dataset.storeUrl || form.action;
        methodInput.disabled = true;
        modalTitle.textContent = 'Add New Service';
        modalSubtitle.textContent = 'Define a new add-on service for tenants';
        modalSubmit.textContent = 'Save Service';
        if (statusInput) statusInput.value = 'Active';
        if (priceInput) priceInput.value = '';
        if (descriptionInput) descriptionInput.value = '';
    };

    const setEditMode = (button) => {
        const data = button.dataset;
        const updateUrlTemplate = modal.dataset.updateUrlTemplate || '';
        form.action = updateUrlTemplate.replace('__ID__', data.id || '');
        methodInput.disabled = false;

        if (nameInput) nameInput.value = data.name || '';
        if (slugInput) slugInput.value = data.slug || '';
        if (priceInput) priceInput.value = data.price || '';
        if (descriptionInput) descriptionInput.value = data.description || '';
        if (statusInput) statusInput.value = data.status || 'Active';

        modalTitle.textContent = 'Edit Service';
        modalSubtitle.textContent = 'Update service details';
        modalSubmit.textContent = 'Update Service';
    };

    openBtn.addEventListener('click', () => {
        setCreateMode();
        openModal();
    });

    editButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setEditMode(button);
            openModal();
        });
    });

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    if (searchInput && resetBtn && rows.length) {
        const updateVisibleCount = () => {
            const visibleRows = rows.filter((row) => !row.classList.contains('hidden')).length;
            if (countBadge) countBadge.textContent = `Total : ${visibleRows}`;
            if (noResultRow) noResultRow.classList.toggle('hidden', visibleRows !== 0);
        };

        const filterRows = () => {
            const keyword = searchInput.value.trim().toLowerCase();
            rows.forEach((row) => {
                const isMatch = row.textContent.toLowerCase().includes(keyword);
                row.classList.toggle('hidden', !isMatch);
            });
            updateVisibleCount();
        };

        searchInput.addEventListener('input', filterRows);
        resetBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterRows();
            searchInput.focus();
        });
    }
})();
