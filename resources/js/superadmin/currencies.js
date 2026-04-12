// Currency modal-form js
(() => {
    const modal = document.getElementById('currencyModal');
    const closeBtn = document.getElementById('closeCurrencyModal');
    const cancelBtn = document.getElementById('cancelCurrencyModal');
    const backdrop = document.getElementById('currencyModalBackdrop');
    const form = document.getElementById('currencyForm');
    const methodInput = document.getElementById('currencyFormMethod');
    const modalTitle = document.getElementById('currencyModalTitle');
    const modalSubtitle = document.getElementById('currencyModalSubtitle');
    const modalSubmit = document.getElementById('currencyModalSubmit');
    const editButtons = Array.from(document.querySelectorAll('.openCurrencyEditModal'));
    const searchInput = document.getElementById('currencyTableSearch');
    const resetBtn = document.getElementById('currencySearchReset');
    const rows = Array.from(document.querySelectorAll('.currency-row'));
    const noResultRow = document.getElementById('currencyNoResultRow');
    const countBadge = document.getElementById('currencyCountBadge');

    if (!modal || !closeBtn || !cancelBtn || !backdrop || !form || !methodInput) return;

    const nameInput = document.getElementById('currencyName');
    const codeInput = document.getElementById('currencyCode');
    const symbolInput = document.getElementById('currencySymbol');
    const countryIdInput = document.getElementById('currencyCountry');
    const rateInput = document.getElementById('currencyRate');
    const decimalsInput = document.getElementById('currencyDecimals');
    const positionInput = document.getElementById('currencyPosition');
    const statusInput = document.getElementById('currencyStatus');
    const defaultInput = document.getElementById('currencyDefault');

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
        modalTitle.textContent = 'Add Currency';
        modalSubtitle.textContent = 'Configure a new currency for billing and reporting';
        modalSubmit.textContent = 'Save Currency';
        if (statusInput) statusInput.value = 'Active';
        if (positionInput) positionInput.value = 'Prefix';
        if (decimalsInput) decimalsInput.value = '2';
        if (rateInput) rateInput.value = '';
        if (defaultInput) defaultInput.checked = false;
    };

    const setEditMode = (button) => {
        const data = button.dataset;
        const updateUrlTemplate = modal.dataset.updateUrlTemplate || '';
        form.action = updateUrlTemplate.replace('__ID__', data.id || '');
        methodInput.disabled = false;

        if (nameInput) nameInput.value = data.name || '';
        if (codeInput) codeInput.value = data.code || '';
        if (symbolInput) symbolInput.value = data.symbol || '';
        if (countryIdInput) countryIdInput.value = data.countryId || '';
        if (rateInput) rateInput.value = data.rate || '';
        if (decimalsInput) decimalsInput.value = data.decimals || '2';
        if (positionInput) positionInput.value = data.position || 'Prefix';
        if (statusInput) statusInput.value = data.status || 'Active';
        if (defaultInput) defaultInput.checked = data.default === '1';

        modalTitle.textContent = 'Edit Currency';
        modalSubtitle.textContent = 'Update currency details and conversion settings';
        modalSubmit.textContent = 'Update Currency';
    };

    document.addEventListener('sa:modal:create-open', (event) => {
        if (event.detail?.modalId !== 'currencyModal') return;
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



