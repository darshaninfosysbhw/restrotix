(() => {
    const modal = document.getElementById('employeeModal');
    const openBtn = document.getElementById('openEmployeeModal');
    const closeBtn = document.getElementById('closeEmployeeModal');
    const cancelBtn = document.getElementById('cancelEmployeeModal');
    const backdrop = document.getElementById('employeeModalBackdrop');
    const form = document.getElementById('employeeForm');
    const formMethod = document.getElementById('employeeFormMethod');
    const submitButton = document.getElementById('employeeSubmitButton');
    const modalTitle = document.getElementById('employeeModalTitle');
    const modalSubtitle = document.getElementById('employeeModalSubtitle');
    const searchForm = document.getElementById('employeeSearchForm');
    const searchInput = document.getElementById('employeeTableSearch');

    if (!modal || !form) return;

    const storeUrl = form.dataset.storeUrl || form.action;
    const passwordField = form.elements.namedItem('password');

    const setModalVisible = (isOpen) => {
        modal.classList.toggle('hidden', !isOpen);
        document.body.classList.toggle('overflow-hidden', isOpen);
    };

    const setFieldValue = (name, value) => {
        const field = form.elements.namedItem(name);
        if (!field || !('value' in field)) return;

        field.value = value ?? '';
    };

    const setPasswordState = (isEditMode) => {
        if (!passwordField || !('value' in passwordField)) return;

        passwordField.value = '';

        if ('required' in passwordField) {
            passwordField.required = !isEditMode;
        }

        if ('placeholder' in passwordField) {
            passwordField.placeholder = isEditMode ? 'Leave blank to keep current password' : '••••••••';
        }
    };

    const setCreateMode = () => {
        form.reset();
        form.action = storeUrl;

        if (formMethod) {
            formMethod.disabled = true;
        }

        if (modalTitle) modalTitle.textContent = 'Add New Employee';
        if (modalSubtitle) modalSubtitle.textContent = 'Fill details to create a new employee entry';
        if (submitButton) submitButton.innerHTML = 'Save Employee';

        setPasswordState(false);
    };

    const setEditMode = (trigger) => {
        const data = trigger.dataset || {};

        form.reset();
        form.action = data.updateUrl || storeUrl;

        if (formMethod) {
            formMethod.disabled = false;
            formMethod.value = 'PUT';
        }

        if (modalTitle) modalTitle.textContent = 'Edit Employee';
        if (modalSubtitle) modalSubtitle.textContent = 'Update employee details';
        if (submitButton) submitButton.innerHTML = 'Update Employee';

        setFieldValue('name', data.name || '');
        setFieldValue('email', data.email || '');
        setFieldValue('phone_number', data.phoneNumber || '');
        setFieldValue('pin_code', data.pinCode || '');
        setFieldValue('role', data.role || 'waiter');
        setFieldValue('branch_id', data.branchId || '');
        setFieldValue('designation', data.designation || '');
        setFieldValue('id_type', data.idType || '');
        setFieldValue('id_number', data.idNumber || '');
        setFieldValue('emergency_contact_number', data.emergencyContactNumber || '');
        setFieldValue('current_address', data.currentAddress || '');
        setFieldValue('permanent_address', data.permanentAddress || '');
        setFieldValue('base_salary', data.baseSalary ?? '');
        setFieldValue('bank_name', data.bankName || '');
        setFieldValue('account_number', data.accountNumber || '');

        setPasswordState(true);
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
        const editBtn = event.target.closest('.openEmployeeEditModal');

        if (!editBtn) {
            return;
        }

        event.preventDefault();
        openEditModal(editBtn);
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
