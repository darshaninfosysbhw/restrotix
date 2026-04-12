
(() => {
    const actionTriggers = Array.from(document.querySelectorAll('.actionTrigger'));
    const actionMenus = Array.from(document.querySelectorAll('.actionMenu'));
    if (!actionTriggers.length || !actionMenus.length) return;

    const closeAllMenus = (exceptMenu = null) => {
        actionMenus.forEach((menu) => {
            if (menu !== exceptMenu) menu.classList.add('hidden');
        });
    };

    actionTriggers.forEach((trigger) => {
        const menu = trigger.parentElement?.querySelector('.actionMenu');
        if (!menu) return;

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const willOpen = menu.classList.contains('hidden');
            closeAllMenus(menu);
            if (willOpen) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        });
    });

    document.addEventListener('click', () => closeAllMenus());
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeAllMenus();
    });
})();
