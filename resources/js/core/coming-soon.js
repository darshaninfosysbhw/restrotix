(() => {
    const DEFAULT_MESSAGE = 'This feature is under development and will be available soon.';
    const MODAL_ID = 'comingSoonModal';
    const OPEN_CLASSES = ['opacity-100', 'translate-y-0', 'scale-100'];
    const CLOSED_CLASSES = ['opacity-0', 'translate-y-4', 'scale-95'];

    const init = () => {
        const modal = document.getElementById(MODAL_ID);
        if (!modal) return;

        const overlay = modal.querySelector('[data-coming-soon-overlay]');
        const panel = modal.querySelector('[data-coming-soon-panel]');
        const titleNode = modal.querySelector('[data-coming-soon-title]');
        const messageNode = modal.querySelector('[data-coming-soon-message]');
        const chipNode = modal.querySelector('[data-coming-soon-chip]');
        const iconShell = modal.querySelector('[data-coming-soon-icon-shell]');
        const iconNode = modal.querySelector('[data-coming-soon-icon]');
        const closeButtons = modal.querySelectorAll('[data-coming-soon-close]');

        if (!overlay || !panel || !titleNode || !messageNode || !chipNode) {
            return;
        }

        let lastTrigger = null;
        let closeTimer = null;

        const clearCloseTimer = () => {
            if (closeTimer) {
                window.clearTimeout(closeTimer);
                closeTimer = null;
            }
        };

        const setOpenState = (isOpen) => {
            clearCloseTimer();

            if (isOpen) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                requestAnimationFrame(() => {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');

                    panel.classList.remove(...CLOSED_CLASSES);
                    panel.classList.add(...OPEN_CLASSES);
                });

                document.body.classList.add('overflow-hidden');
                return;
            }

            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');

            panel.classList.remove(...OPEN_CLASSES);
            panel.classList.add(...CLOSED_CLASSES);

            document.body.classList.remove('overflow-hidden');

            closeTimer = window.setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 220);
        };

        const openModal = (trigger) => {
            if (!trigger) return;

            lastTrigger = trigger;

            const feature = String(trigger.dataset.comingSoonFeature || '').trim();
            const title = String(trigger.dataset.comingSoonTitle || 'Coming Soon').trim();
            const message = String(trigger.dataset.comingSoonMessage || '').trim() ||
                (feature ? `${feature} is under development and will be available soon.` : DEFAULT_MESSAGE);
            const iconClass = String(trigger.dataset.comingSoonIcon || 'fas fa-bullhorn').trim() || 'fas fa-bullhorn';

            titleNode.textContent = title;
            messageNode.textContent = message;

            if (feature) {
                chipNode.textContent = feature;
                chipNode.classList.remove('hidden');
            } else {
                chipNode.textContent = '';
                chipNode.classList.add('hidden');
            }

            if (iconShell && iconNode) {
                if (iconClass) {
                    iconNode.className = `${iconClass} text-2xl`;
                    iconShell.classList.remove('hidden');
                } else {
                    iconShell.classList.add('hidden');
                }
            }

            setOpenState(true);

            window.setTimeout(() => {
                const focusTarget = modal.querySelector('[data-coming-soon-close]');
                focusTarget?.focus?.({ preventScroll: true });
            }, 0);
        };

        const closeModal = () => {
            if (modal.classList.contains('hidden')) return;

            setOpenState(false);

            if (lastTrigger?.focus) {
                window.setTimeout(() => {
                    lastTrigger.focus({ preventScroll: true });
                }, 220);
            }
        };

        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-coming-soon-trigger="1"]');
            if (trigger) {
                event.preventDefault();
                openModal(trigger);
                return;
            }

            if (event.target.closest('[data-coming-soon-close]')) {
                event.preventDefault();
                closeModal();
                return;
            }

            if (event.target.closest('[data-coming-soon-overlay]')) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                closeModal();
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
