(() => {
    const modal = document.getElementById("areaModal");
    const openBtn = document.getElementById("openAreaModal");
    const shortcutBtn = document.getElementById("openAreaShortcut");
    const closeBtn = document.getElementById("closeAreaModal");
    const cancelBtn = document.getElementById("cancelAreaModal");
    const backdrop = document.getElementById("areaModalBackdrop");

    const form = document.getElementById("areaForm");
    const formMethod = document.getElementById("areaFormMethod");
    const submitButton = document.getElementById("areaSubmitButton");

    const modalTitle = document.getElementById("areaModalTitle");
    const modalSubtitle = document.getElementById("areaModalSubtitle");

    const searchForm = document.getElementById("areaSearchForm");
    const searchInput = document.getElementById("areaTableSearch");
    const tableModal = document.getElementById("tableModal");
    const areaSelect = document.getElementById("areaSelect");

    if (!modal || !form) return;

    const storeUrl = form.dataset.storeUrl || form.action;
    let openedFromTableShortcut = false;

    const fields = {
        areaName: document.getElementById("areaName"),
         areaCode: document.getElementById("areaCode"),
        isActive: document.getElementById("areaIsActive"),
    };

    /**
     * Show / hide modal
     */
    const setModalVisible = (isOpen) => {
        modal.classList.toggle("hidden", !isOpen);
        document.body.classList.toggle("overflow-hidden", isOpen);
    };

    /**
     * CREATE MODE
     */
    const setCreateMode = () => {
        form.reset();

        // Store route
        form.action = storeUrl;

        // Disable _method so Laravel handles it as POST
        if (formMethod) {
            formMethod.disabled = true;
        }

        // Modal heading
        if (modalTitle) {
            modalTitle.textContent = "Add Area / Floor";
        }

        if (modalSubtitle) {
            modalSubtitle.textContent =
                "Create an area or floor to organize restaurant tables";
        }

        // Submit button
        if (submitButton) {
            submitButton.innerHTML =
                '<i class="fas fa-save mr-2"></i> Save Area';
        }

        // Fields
        if (fields.areaName) {
            fields.areaName.value = "";
        }

        if (fields.areaCode) {
            fields.areaCode.value = "";
        }

        // New area default = active
        if (fields.isActive) {
            fields.isActive.checked = true;
        }
    };

    /**
     * EDIT MODE
     */
    const setEditMode = (trigger) => {
        const data = trigger.dataset || {};

        form.reset();

        // Update route from data-update-url
        form.action = data.updateUrl || storeUrl;

        // Enable hidden PUT/PATCH method
        if (formMethod) {
            formMethod.disabled = false;
        }

        // Modal heading
        if (modalTitle) {
            modalTitle.textContent = "Edit Area / Floor";
        }

        if (modalSubtitle) {
            modalSubtitle.textContent = "Update area or floor details";
        }

        // Submit button
        if (submitButton) {
            submitButton.innerHTML =
                '<i class="fas fa-save mr-2"></i> Update Area';
        }

        // Area name
        if (fields.areaName) {
            fields.areaName.value = data.areaName || "";
        }

         if (fields.areaCode) {
            fields.areaCode.value = data.areaCode || "";
        }

        // Active status
        if (fields.isActive) {
            fields.isActive.checked =
                String(data.isActive || "0") === "1";
        }
    };

    /**
     * Open create modal
     */
    const openCreateModal = () => {
        openedFromTableShortcut = false;
        setCreateMode();
        setModalVisible(true);

        // Focus field
        window.setTimeout(() => {
            fields.areaName?.focus();
        }, 100);
    };

    const openShortcutModal = () => {
        openedFromTableShortcut = true;
        setCreateMode();
        setModalVisible(true);

        window.setTimeout(() => {
            fields.areaName?.focus();
        }, 100);
    };

    /**
     * Open edit modal
     */
    const openEditModal = (trigger) => {
        setEditMode(trigger);
        setModalVisible(true);

        window.setTimeout(() => {
            fields.areaName?.focus();
        }, 100);
    };

    /**
     * Close modal
     */
    const closeModal = () => {
        setModalVisible(false);

        if (openedFromTableShortcut && tableModal) {
            tableModal.classList.remove("hidden");
            document.body.classList.add("overflow-hidden");
        }

        openedFromTableShortcut = false;
    };

    /**
     * Add Area button
     */
    openBtn?.addEventListener("click", openCreateModal);
   
    /**
      * Table page shortcut + button
    */
    shortcutBtn?.addEventListener("click", openShortcutModal);

    form.addEventListener("submit", async (event) => {
        if (!openedFromTableShortcut || !formMethod?.disabled) return;

        event.preventDefault();

        const originalContent = submitButton?.innerHTML;

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        }

        try {
            const response = await fetch(form.action, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: new FormData(form),
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || "Unable to create area / floor.");
            }

            if (areaSelect && data.area) {
                const option = new Option(data.area.name, data.area.id, true, true);
                areaSelect.add(option);
                areaSelect.dispatchEvent(new Event("change", { bubbles: true }));
            }

            setModalVisible(false);
            openedFromTableShortcut = false;

            if (tableModal) {
                tableModal.classList.remove("hidden");
                document.body.classList.add("overflow-hidden");
            }

            window.showToast?.({
                type: "success",
                message: data.message || "Area / floor created successfully.",
                duration: 4000,
            });
        } catch (error) {
            window.showToast?.({
                type: "error",
                message: error.message || "Unable to create area / floor.",
                duration: 5000,
            });
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalContent;
            }
        }
    });

    /**
     * Close button
     */
    closeBtn?.addEventListener("click", closeModal);

    /**
     * Cancel button
     */
    cancelBtn?.addEventListener("click", closeModal);

    /**
     * Backdrop click
     */
    backdrop?.addEventListener("click", closeModal);

    /**
     * Edit Area
     *
     * Event delegation used because edit buttons
     * can exist inside table rows.
     */
    document.addEventListener("click", (event) => {
        const editBtn = event.target.closest(".openAreaEditModal");

        if (!editBtn) return;

        event.preventDefault();

        openEditModal(editBtn);
    });

    /**
     * ESC closes modal
     */
    document.addEventListener("keydown", (event) => {
        if (
            event.key === "Escape" &&
            !modal.classList.contains("hidden")
        ) {
            closeModal();
        }
    });

    /**
     * Area Search
     * Auto submit after 300ms
     */
    if (searchInput && searchForm) {
        let searchTimer = null;

        searchInput.addEventListener("input", () => {
            if (searchTimer) {
                window.clearTimeout(searchTimer);
            }

            searchTimer = window.setTimeout(() => {
                searchForm.requestSubmit();
            }, 300);
        });
    }
})();
