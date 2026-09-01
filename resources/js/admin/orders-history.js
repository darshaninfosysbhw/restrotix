document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.order-history-page');

    if (!page || !page.dataset.orderHistoryData) {
        return;
    }

    let config = {};

    try {
        config = JSON.parse(page.dataset.orderHistoryData);
    } catch (error) {
        config = {};
    }

    const orderDetails = config.orderDetails || {};
    const defaultOrderKey = config.defaultOrderKey || Object.keys(orderDetails)[0] || '';
    const filterForm = document.getElementById('orderHistoryFiltersForm');
    const statusFilterField = document.getElementById('orderHistoryStatusFilter');
    const exportToggle = document.getElementById('orderHistoryExportToggle');
    const exportMenu = document.getElementById('orderHistoryExportMenu');
    const drawer = document.getElementById('orderHistoryDrawer');
    const overlay = document.getElementById('orderHistoryDrawerOverlay');
    const closeButton = document.getElementById('orderHistoryDrawerClose');
    const searchInput = document.getElementById('orderHistoryTableSearch');
    const resetButton = document.getElementById('orderHistorySearchReset');
    const resultsCount = document.getElementById('orderHistoryResultsCount');
    const footerCount = document.getElementById('orderHistoryFooterCount');

    if (!drawer || !overlay || !closeButton) {
        return;
    }

    const drawerFields = {
        orderNo: document.getElementById('orderDrawerOrderNo'),
        date: document.getElementById('orderDrawerDate'),
        time: document.getElementById('orderDrawerTime'),
        type: document.getElementById('orderDrawerType'),
        table: document.getElementById('orderDrawerTable'),
        waiter: document.getElementById('orderDrawerWaiter'),
        timeline: document.getElementById('orderDrawerTimeline'),
        items: document.getElementById('orderDrawerItems'),
        subtotal: document.getElementById('orderDrawerSubtotal'),
        discount: document.getElementById('orderDrawerDiscount'),
        service: document.getElementById('orderDrawerService'),
        tax: document.getElementById('orderDrawerTax'),
        total: document.getElementById('orderDrawerTotal'),
        paymentMethod: document.getElementById('orderDrawerPaymentMethod'),
        amountPaid: document.getElementById('orderDrawerAmountPaid'),
        paidAt: document.getElementById('orderDrawerPaidAt'),
        transactionId: document.getElementById('orderDrawerTransactionId'),
        note: document.getElementById('orderDrawerNote'),
    };

    const rows = Array.from(document.querySelectorAll('[data-order-history-row]'));
    const openButtons = Array.from(document.querySelectorAll('[data-order-open]'));
    const filterSubmitDelayMs = 350;
    let searchSubmitTimer = null;

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    })[char]);

    const setText = (element, value) => {
        if (element) {
            element.textContent = value ?? '';
        }
    };

    const setActiveRow = (orderKey) => {
        rows.forEach((row) => {
            const isActive = row.dataset.orderKey === orderKey;

            row.classList.toggle('order-history-selected-row', isActive);
            row.classList.toggle('bg-orange-500/5', isActive);
            row.classList.toggle('border-orange-500/30', isActive);
            row.classList.toggle('ring-1', isActive);
            row.classList.toggle('ring-orange-500/20', isActive);
            row.classList.toggle('hover:bg-orange-500/5', !isActive);
            row.classList.toggle('hover:border-orange-500/20', !isActive);
        });
    };

    const renderTimeline = (timeline = []) => {
        if (!drawerFields.timeline) return;

                drawerFields.timeline.innerHTML = timeline.map((step) => `
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">
                                <i class="fas fa-check text-[10px]"></i>
                            </span>
                            <span class="text-gray-200">${escapeHtml(step.label)}</span>
                        </div>
                        <span class="text-gray-400">${escapeHtml(step.time)}</span>
                    </div>
                `).join('');
            };

    const renderItems = (items = []) => {
        if (!drawerFields.items) return;

                drawerFields.items.innerHTML = items.map((item) => `
                    <div class="flex items-start justify-between gap-3 text-sm">
                        <div class="flex items-start gap-3">
                            <span class="w-4 text-gray-400">${escapeHtml(item.qty)}</span>
                            <span class="text-gray-200">${escapeHtml(item.name)}</span>
                        </div>
                        <span class="text-gray-400">${escapeHtml(item.price)}</span>
                    </div>
                `).join('');
            };

    const submitFilters = () => {
        if (!filterForm) {
            return;
        }

        if (typeof filterForm.requestSubmit === 'function') {
            filterForm.requestSubmit();
            return;
        }

        filterForm.submit();
    };

    const bindFilterControls = () => {
        if (!filterForm) {
            return;
        }

        const filterControls = filterForm.querySelectorAll('select[name="branch_id"], select[name="order_type"], select[name="payment_status"], input[name="date_from"], input[name="date_to"]');
        const statusTabs = filterForm.querySelectorAll('[data-order-status]');

        filterControls.forEach((control) => {
            control.addEventListener('change', submitFilters);
        });

        statusTabs.forEach((button) => {
            button.addEventListener('click', () => {
                if (statusFilterField) {
                    statusFilterField.value = button.dataset.orderStatus || 'all';
                }

                submitFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                window.clearTimeout(searchSubmitTimer);
                searchSubmitTimer = window.setTimeout(submitFilters, filterSubmitDelayMs);
            });
        }

        if (resetButton) {
            resetButton.addEventListener('click', () => {
                const searchField = filterForm.querySelector('input[name="search"]');
                const branchField = filterForm.querySelector('select[name="branch_id"]');
                const orderTypeField = filterForm.querySelector('select[name="order_type"]');
                const paymentField = filterForm.querySelector('select[name="payment_status"]');
                const dateFromField = filterForm.querySelector('input[name="date_from"]');
                const dateToField = filterForm.querySelector('input[name="date_to"]');

                if (searchField) searchField.value = '';
                if (branchField) branchField.value = '';
                if (orderTypeField) orderTypeField.value = 'all';
                if (paymentField) paymentField.value = 'all';
                if (dateFromField) dateFromField.value = '';
                if (dateToField) dateToField.value = '';
                if (statusFilterField) statusFilterField.value = 'all';

                submitFilters();
            });
        }
    };

    const bindExportDropdown = () => {
        if (!exportToggle || !exportMenu) {
            return;
        }

        exportToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            exportMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (!exportMenu.contains(event.target) && !exportToggle.contains(event.target)) {
                exportMenu.classList.add('hidden');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                exportMenu.classList.add('hidden');
            }
        });
    };

    const applyOrder = (orderKey) => {
        const order = orderDetails[orderKey];

        if (!order) {
            return;
        }

        setText(drawerFields.orderNo, order.order_no);
        setText(drawerFields.date, order.date);
        setText(drawerFields.time, order.time);
        setText(drawerFields.type, order.type);
        setText(drawerFields.table, order.table);
        setText(drawerFields.waiter, order.waiter);
        setText(drawerFields.subtotal, order.subtotal);
        setText(drawerFields.discount, order.discount);
        setText(drawerFields.service, order.service);
        setText(drawerFields.tax, order.tax);
        setText(drawerFields.total, order.total);
        setText(drawerFields.paymentMethod, order.payment_method);
        setText(drawerFields.amountPaid, order.amount_paid);
        setText(drawerFields.paidAt, order.paid_at);
        setText(drawerFields.transactionId, order.transaction_id);
        setText(drawerFields.note, order.note);

        renderTimeline(order.timeline);
        renderItems(order.items);
        setActiveRow(orderKey);
    };

    const openDrawer = (orderKey) => {
        applyOrder(orderKey);
        drawer.classList.remove('translate-x-full');
        drawer.setAttribute('aria-hidden', 'false');
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeDrawer = () => {
        drawer.classList.add('translate-x-full');
        drawer.setAttribute('aria-hidden', 'true');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    rows.forEach((row) => {
        row.addEventListener('click', () => openDrawer(row.dataset.orderKey));
    });

    openButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            openDrawer(button.dataset.orderKey);
        });
    });

    closeButton.addEventListener('click', closeDrawer);
    overlay.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeDrawer();
        }
    });

    bindFilterControls();
    bindExportDropdown();

    if (defaultOrderKey && orderDetails[defaultOrderKey]) {
        applyOrder(defaultOrderKey);
    }
});
