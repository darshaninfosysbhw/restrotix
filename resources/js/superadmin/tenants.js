//tenant restaurant modal-form  js
(() => {
    const modal = document.getElementById('restaurantModal');
    const openBtn = document.getElementById('openRestaurantModal');
    const closeBtn = document.getElementById('closeRestaurantModal');
    const cancelBtn = document.getElementById('cancelRestaurantModal');
    const backdrop = document.getElementById('restaurantModalBackdrop');
    const form = document.getElementById('restaurantForm');
    const methodInput = document.getElementById('restaurantFormMethod');
    const modalTitle = document.getElementById('restaurantModalTitle');
    const modalSubtitle = document.getElementById('restaurantModalSubtitle');
    const modalSubmit = document.getElementById('restaurantModalSubmit');
    const editButtons = Array.from(document.querySelectorAll('.openRestaurantEditModal'));
    const companyNameInput = document.getElementById('restaurantCompanyName');
    const slugInput = document.getElementById('restaurantSlug');
    const ownerNameInput = document.getElementById('restaurantOwnerName');
    const emailInput = document.getElementById('restaurantEmail');
    const phoneInput = document.getElementById('restaurantPhone');
    const countryInput = document.getElementById('restaurantCountry');
    const cityInput = document.getElementById('restaurantCity');
    const planInput = document.getElementById('restaurantPlan');
    const billingCycleInput = document.getElementById('restaurantBillingCycle');
    const branchLimitInput = document.getElementById('restaurantBranchLimit');
    const addressInput = document.getElementById('restaurantAddress');
    const statusInput = document.getElementById('restaurantStatus');
    const searchInput = document.getElementById('restaurantTableSearch');
    const resetBtn = document.getElementById('restaurantSearchReset');
    const rows = Array.from(document.querySelectorAll('.restaurant-row'));
    const noResultRow = document.getElementById('restaurantNoResultRow');
    const countBadge = document.getElementById('restaurantCountBadge');

    if (!modal || !openBtn || !closeBtn || !cancelBtn || !backdrop || !form || !methodInput) return;

    const normalizeStatusValue = (value) => {
        const raw = String(value || '').trim().toLowerCase();

        if (raw === 'cancelled') return 'canceled';
        if (raw === 'pending') return 'expired';
        if (['trial', 'active', 'expired', 'canceled'].includes(raw)) return raw;

        return '';
    };

    const normalizeBillingCycleValue = (value) => {
        const raw = String(value || '').trim().toLowerCase();

        if (['monthly', 'yearly'].includes(raw)) return raw;

        return 'monthly';
    };

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
        modalTitle.textContent = 'Add New Restaurant';
        modalSubtitle.textContent = 'Fill details to register a new restaurant tenant';
        modalSubmit.textContent = 'Register Restaurant';
        if (planInput) planInput.value = '';
        if (billingCycleInput) billingCycleInput.value = 'monthly';
        if (statusInput) statusInput.value = 'trial';
        if (branchLimitInput) branchLimitInput.value = '';
        if (addressInput) addressInput.value = '';
        if (slugInput) slugInput.value = '';
    };

    const setEditMode = (button) => {
        const data = button.dataset;
        const updateUrlTemplate = modal.dataset.updateUrlTemplate || '';
        form.action = updateUrlTemplate.replace('__ID__', data.id || '');
        methodInput.disabled = false;

        if (companyNameInput) companyNameInput.value = data.name || '';
        if (slugInput) slugInput.value = data.slug || '';
        if (ownerNameInput) ownerNameInput.value = data.owner || '';
        if (emailInput) emailInput.value = data.email || '';
        if (phoneInput) phoneInput.value = data.phone || '';
        if (countryInput) countryInput.value = data.countryId || '';
        if (cityInput) cityInput.value = data.city || '';
        if (planInput) planInput.value = data.planId || '';
        if (billingCycleInput) billingCycleInput.value = normalizeBillingCycleValue(data.billingCycle || 'monthly');
        if (statusInput) statusInput.value = normalizeStatusValue(data.statusKey || data.status || 'trial');
        if (branchLimitInput) branchLimitInput.value = '1';
        if (addressInput) addressInput.value = '';

        modalTitle.textContent = 'Edit Restaurant';
        modalSubtitle.textContent = 'Update tenant details';
        modalSubmit.textContent = 'Update Restaurant';
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
