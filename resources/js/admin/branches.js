(() => {
    const modal = document.getElementById('branchModal');
    const openBtn = document.getElementById('openBranchModal');
    const closeBtn = document.getElementById('closeBranchModal');
    const cancelBtn = document.getElementById('cancelBranchModal');
    const backdrop = document.getElementById('branchModalBackdrop');
    const form = document.getElementById('branchForm');
    const formMethod = document.getElementById('branchFormMethod');
    const submitButton = document.getElementById('branchSubmitButton');
    const modalTitle = document.getElementById('branchModalTitle');
    const modalSubtitle = document.getElementById('branchModalSubtitle');
    const searchForm = document.getElementById('branchSearchForm');
    const searchInput = document.getElementById('branchTableSearch');

    if (!modal || !form) return;

    const storeUrl = form.dataset.storeUrl || form.action;

    const fields = {
        branchName: document.getElementById('branchName'),
        contactNumber: document.getElementById('contactNumber'),
        branchEmail: document.getElementById('branchEmail'),
        countryCode: document.getElementById('countryCode'),
        city: document.getElementById('city'),
        state: document.getElementById('state'),
        pincode: document.getElementById('pincode'),
        fullAddress: document.getElementById('fullAddress'),
        taxSetting: document.getElementById('taxSetting'),
        taxRate: document.getElementById('taxRate'),
        offlineBillingEnabled: document.getElementById('offlineBillingEnabled'),
    };

    const setModalVisible = (isOpen) => {
        modal.classList.toggle('hidden', !isOpen);
        document.body.classList.toggle('overflow-hidden', isOpen);
    };

    const setCreateMode = () => {
        form.reset();
        form.action = storeUrl;

        if (formMethod) {
            formMethod.disabled = true;
        }

        if (modalTitle) modalTitle.textContent = 'Add New Branch';
        if (modalSubtitle) modalSubtitle.textContent = 'Fill details to create a new branch entry';
        if (submitButton) submitButton.innerHTML = '<i class="fas fa-save mr-2"></i> Save Branch';

        if (fields.branchName) fields.branchName.value = '';
        if (fields.contactNumber) fields.contactNumber.value = '';
        if (fields.branchEmail) fields.branchEmail.value = '';
        if (fields.countryCode) fields.countryCode.value = 'Ind';
        if (fields.city) fields.city.value = '';
        if (fields.state) fields.state.value = '';
        if (fields.pincode) fields.pincode.value = '';
        if (fields.fullAddress) fields.fullAddress.value = '';
        if (fields.taxSetting) fields.taxSetting.value = 'exclusive';
        if (fields.taxRate) fields.taxRate.value = '5.0';
        if (fields.offlineBillingEnabled) fields.offlineBillingEnabled.checked = false;
    };

    const setEditMode = (trigger) => {
        const data = trigger.dataset || {};

        form.reset();
        form.action = data.updateUrl || storeUrl;

        if (formMethod) {
            formMethod.disabled = false;
        }

        if (modalTitle) modalTitle.textContent = 'Edit Branch';
        if (modalSubtitle) modalSubtitle.textContent = 'Update branch details';
        if (submitButton) submitButton.innerHTML = '<i class="fas fa-save mr-2"></i> Update Branch';

        if (fields.branchName) fields.branchName.value = data.branchName || '';
        if (fields.contactNumber) fields.contactNumber.value = data.contactNumber || '';
        if (fields.branchEmail) fields.branchEmail.value = data.branchEmail || '';
        if (fields.countryCode) fields.countryCode.value = data.countryCode || 'Ind';
        if (fields.city) fields.city.value = data.city || '';
        if (fields.state) fields.state.value = data.state || '';
        if (fields.pincode) fields.pincode.value = data.pincode || '';
        if (fields.fullAddress) fields.fullAddress.value = data.fullAddress || '';
        if (fields.taxSetting) fields.taxSetting.value = data.taxSetting || 'exclusive';
        if (fields.taxRate) fields.taxRate.value = data.taxRate ?? '5.0';
        if (fields.offlineBillingEnabled) {
            fields.offlineBillingEnabled.checked = String(data.offlineBillingEnabled || '0') === '1';
        }
    };

    const openCreateModal = () => {
        setCreateMode();
        setModalVisible(true);
    };

    const openEditModal = (trigger) => {
        setEditMode(trigger);
        setModalVisible(true);
    };

    const closeModal = () => {
        setModalVisible(false);
    };

    if (openBtn) {
        openBtn.addEventListener('click', openCreateModal);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeModal);
    }

    document.addEventListener('click', (event) => {
        const editBtn = event.target.closest('.openBranchEditModal');
        if (editBtn) {
            event.preventDefault();
            openEditModal(editBtn);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    if (searchInput && searchForm) {
        let searchTimer = null;

        const submitSearch = () => {
            if (searchTimer) {
                window.clearTimeout(searchTimer);
            }

            searchTimer = window.setTimeout(() => {
                searchForm.requestSubmit();
            }, 300);
        };

        searchInput.addEventListener('input', submitSearch);
    }
})();
