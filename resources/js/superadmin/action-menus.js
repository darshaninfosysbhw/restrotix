
(() => {
    const actionTriggers = Array.from(document.querySelectorAll('.actionTrigger'));
    const actionMenus = Array.from(document.querySelectorAll('.actionMenu'));
    if (!actionTriggers.length || !actionMenus.length) return;

    const actionMeta = new Map();

    const resetMenuPosition = (menu) => {
        menu.style.position = '';
        menu.style.top = '';
        menu.style.left = '';
        menu.style.right = '';
        menu.style.bottom = '';
        menu.style.visibility = '';
        menu.style.zIndex = '';
    };

    const positionMenu = (trigger, menu) => {
        const triggerRect = trigger.getBoundingClientRect();
        const viewportPadding = 8;
        const gap = 8;

        menu.style.position = 'fixed';
        menu.style.right = 'auto';
        menu.style.bottom = 'auto';
        menu.style.visibility = 'hidden';
        menu.style.zIndex = '3000';

        const menuWidth = menu.offsetWidth || 160;
        const menuHeight = menu.offsetHeight || 0;

        let left = triggerRect.right - menuWidth;
        if (left < viewportPadding) {
            left = triggerRect.left;
        }
        left = Math.min(left, window.innerWidth - menuWidth - viewportPadding);
        left = Math.max(viewportPadding, left);

        let top = triggerRect.bottom + gap;
        if (menuHeight && top + menuHeight > window.innerHeight - viewportPadding) {
            top = Math.max(viewportPadding, triggerRect.top - menuHeight - gap);
        }

        menu.style.left = `${Math.round(left)}px`;
        menu.style.top = `${Math.round(top)}px`;
        menu.style.visibility = 'visible';
    };

    const returnMenuToSource = (menu) => {
        const meta = actionMeta.get(menu);
        if (!meta?.group || menu.parentElement === meta.group) return;
        meta.group.appendChild(menu);
    };

    const closeMenu = (menu) => {
        const meta = actionMeta.get(menu);
        if (!meta) return;
        menu.classList.add('hidden');
        resetMenuPosition(menu);
        returnMenuToSource(menu);
        meta.trigger.setAttribute('aria-expanded', 'false');
    };

    const closeAllMenus = (exceptMenu = null) => {
        actionMenus.forEach((menu) => {
            if (menu !== exceptMenu) {
                closeMenu(menu);
            }
        });
    };

    actionTriggers.forEach((trigger) => {
        const group = trigger.closest('.actionGroup');
        const menu = group?.querySelector('.actionMenu');
        if (!menu) return;

        actionMeta.set(menu, {
            trigger,
            group,
        });

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const willOpen = menu.classList.contains('hidden');
            if (willOpen) {
                closeAllMenus(menu);
                if (menu.parentElement !== document.body) {
                    document.body.appendChild(menu);
                }
                menu.classList.remove('hidden');
                trigger.setAttribute('aria-expanded', 'true');
                requestAnimationFrame(() => positionMenu(trigger, menu));
            } else {
                closeMenu(menu);
            }
        });
    });

    document.addEventListener('click', () => closeAllMenus());
    window.addEventListener('resize', () => closeAllMenus());
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeAllMenus();
    });
})();
