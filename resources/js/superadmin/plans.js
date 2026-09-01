// Plan modal + table interactions
(() => {
    const modal = document.getElementById('planModal');
    const closeBtn = document.getElementById('closePlanModal');
    const cancelBtn = document.getElementById('cancelPlanModal');
    const backdrop = document.getElementById('planModalBackdrop');
    const form = document.getElementById('planForm');
    const methodInput = document.getElementById('planFormMethod');
    const modalTitle = document.getElementById('planModalTitle');
    const modalSubtitle = document.getElementById('planModalSubtitle');
    const modalSubmit = document.getElementById('planModalSubmit');
    const slugPreview = document.getElementById('planSlugPreview');

    const editButtons = Array.from(document.querySelectorAll('.openPlanEditModal'));
    const viewButtons = Array.from(document.querySelectorAll('.openPlanViewModal'));

    const searchInput = document.getElementById('planTableSearch');
    const resetBtn = document.getElementById('planSearchReset');
    const rows = Array.from(document.querySelectorAll('.plan-row'));
    const noResultRow = document.getElementById('planNoResultRow');
    const countBadge = document.getElementById('planCountBadge');

    if (!modal || !closeBtn || !cancelBtn || !backdrop || !form || !methodInput) return;

    const nameInput = document.getElementById('planName');
    const summaryInput = document.getElementById('planSummary');
    const maxBranchesInput = document.getElementById('planMaxBranches');
    const trialDaysInput = document.getElementById('planTrialDays');
    const statusInput = document.getElementById('planStatus');
    const recommendedInput = document.getElementById('planIsRecommended');
    const featureInputs = Array.from(document.querySelectorAll('[data-feature-checkbox]'));

    const monthlyInputs = Array.from(document.querySelectorAll('[data-price-monthly]'));
    const yearlyInputs = Array.from(document.querySelectorAll('[data-price-yearly]'));
    const primaryMonthlyInput = document.querySelector('[data-plan-price-primary="1"][data-price-monthly]');
    const primaryYearlyInput = document.querySelector('[data-plan-price-primary="1"][data-price-yearly]');
    const secondaryMonthlyInputs = Array.from(document.querySelectorAll('[data-plan-price-secondary="1"][data-price-monthly]'));
    const secondaryYearlyInputs = Array.from(document.querySelectorAll('[data-plan-price-secondary="1"][data-price-yearly]'));
    let isCreateMode = false;

    const priceMonthlyByCurrency = Object.fromEntries(
        monthlyInputs.map((input) => [input.dataset.priceMonthly, input])
    );
    const priceYearlyByCurrency = Object.fromEntries(
        yearlyInputs.map((input) => [input.dataset.priceYearly, input])
    );

    const openModal = () => {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };

    const slugify = (text) => (text || '')
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

    const updateSlugPreview = () => {
        if (!slugPreview || !nameInput) return;
        const slug = slugify(nameInput.value);
        slugPreview.textContent = `Slug preview: ${slug || '-'}`;
    };

    const setFieldsDisabled = (disabled) => {
        const allControls = Array.from(form.querySelectorAll('input, select, textarea'));
        allControls.forEach((control) => {
            if (control.name === '_token' || control.name === '_method') return;
            control.disabled = disabled;
        });

        if (methodInput) methodInput.disabled = disabled ? true : methodInput.disabled;

        if (modalSubmit) {
            modalSubmit.classList.toggle('hidden', disabled);
        }

        if (cancelBtn) {
            cancelBtn.textContent = disabled ? 'Close' : 'Cancel';
        }
    };

    const resetPricingInputs = () => {
        monthlyInputs.forEach((input) => {
            input.value = '';
        });

        yearlyInputs.forEach((input) => {
            input.value = '';
            input.dataset.manual = '0';
        });
    };

    const syncSecondaryPlanPrices = () => {
        if (!isCreateMode || !primaryMonthlyInput || !primaryYearlyInput) return;

        secondaryMonthlyInputs.forEach((input) => {
            input.value = primaryMonthlyInput.value;
        });

        secondaryYearlyInputs.forEach((input) => {
            input.value = primaryYearlyInput.value;
        });
    };

    const setCreateMode = () => {
        isCreateMode = true;
        form.reset();
        form.action = modal.dataset.storeUrl || form.action;
        methodInput.disabled = true;

        modalTitle.textContent = 'Add Plan';
        modalSubtitle.textContent = 'Create a new subscription plan for tenants';
        modalSubmit.textContent = 'Save Plan';

        if (statusInput) statusInput.value = 'Active';
        if (maxBranchesInput) maxBranchesInput.value = '1';
        if (trialDaysInput) trialDaysInput.value = '14';
        if (summaryInput) summaryInput.value = '';
        if (recommendedInput) recommendedInput.checked = false;

        featureInputs.forEach((checkbox) => {
            checkbox.checked = false;
        });

        resetPricingInputs();
        syncSecondaryPlanPrices();
        updateSlugPreview();
        setFieldsDisabled(false);
    };

    const parseJsonDataset = (raw) => {
        try {
            return raw ? JSON.parse(raw) : {};
        } catch {
            return {};
        }
    };

    const populateForm = (button) => {
        const data = button.dataset;

        if (nameInput) nameInput.value = data.name || '';
        if (summaryInput) summaryInput.value = data.summary || '';
        if (maxBranchesInput) maxBranchesInput.value = data.maxBranches || '1';
        if (trialDaysInput) trialDaysInput.value = data.trialDays || '0';
        if (statusInput) statusInput.value = data.status || 'Active';
        if (recommendedInput) recommendedInput.checked = data.isRecommended === '1';

        const features = parseJsonDataset(data.features);
        featureInputs.forEach((checkbox) => {
            checkbox.checked = String(features[checkbox.dataset.featureCheckbox] ?? '0') === '1' || features[checkbox.dataset.featureCheckbox] === true;
        });

        const prices = parseJsonDataset(data.prices);

        if (primaryMonthlyInput) {
            primaryMonthlyInput.value = data.defaultMonthlyPrice || '';
        }

        if (primaryYearlyInput) {
            primaryYearlyInput.value = data.defaultYearlyPrice || '';
        }

        Object.entries(priceMonthlyByCurrency).forEach(([currencyId, input]) => {
            input.value = prices[currencyId]?.monthly ?? '';
        });

        Object.entries(priceYearlyByCurrency).forEach(([currencyId, input]) => {
            input.value = prices[currencyId]?.yearly ?? '';
            input.dataset.manual = '1';
        });

        updateSlugPreview();
    };

    const setEditMode = (button) => {
        isCreateMode = false;
        const updateUrlTemplate = modal.dataset.updateUrlTemplate || '';
        form.action = updateUrlTemplate.replace('__ID__', button.dataset.id || '');
        methodInput.disabled = false;

        populateForm(button);

        modalTitle.textContent = 'Edit Plan';
        modalSubtitle.textContent = 'Update plan settings and prices';
        modalSubmit.textContent = 'Update Plan';

        setFieldsDisabled(false);
    };

    const setViewMode = (button) => {
        isCreateMode = false;
        form.action = modal.dataset.storeUrl || form.action;
        methodInput.disabled = true;

        populateForm(button);

        modalTitle.textContent = 'Plan Details';
        modalSubtitle.textContent = 'Read-only preview of this plan';

        setFieldsDisabled(true);
    };

    document.addEventListener('sa:modal:create-open', (event) => {
        if (event.detail?.modalId !== 'planModal') return;
        setCreateMode();
        openModal();
    });

    editButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setEditMode(button);
            openModal();
        });
    });

    viewButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setViewMode(button);
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

    if (nameInput) {
        nameInput.addEventListener('input', updateSlugPreview);
    }

    monthlyInputs.forEach((monthlyInput) => {
        monthlyInput.addEventListener('input', () => {
            const currencyId = monthlyInput.dataset.priceMonthly;
            const yearlyInput = priceYearlyByCurrency[currencyId];
            if (!yearlyInput) return;

            const monthlyValue = parseFloat(monthlyInput.value || '0');
            const suggested = Number.isFinite(monthlyValue) ? (monthlyValue * 10).toFixed(2) : '0.00';

            if (yearlyInput.dataset.manual !== '1') {
                yearlyInput.value = suggested;
            }

            if (monthlyInput === primaryMonthlyInput) {
                syncSecondaryPlanPrices();
            }
        });
    });

    yearlyInputs.forEach((yearlyInput) => {
        yearlyInput.addEventListener('input', () => {
            yearlyInput.dataset.manual = '1';

            if (yearlyInput === primaryYearlyInput) {
                syncSecondaryPlanPrices();
            }
        });
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
