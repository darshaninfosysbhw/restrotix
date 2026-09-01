(() => {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    const desktopMQ = window.matchMedia('(min-width: 768px)');
    const links = Array.from(sidebar.querySelectorAll('.sidebar-link'));
    if (!links.length) return;

    const topRow = sidebar.querySelector('div.flex.items-center.justify-between.mb-8.px-2');
    const brandGroup = topRow ? topRow.querySelector('div.flex.items-center.gap-2') : null;
    const brandText = brandGroup ? brandGroup.querySelector('span.text-xl') : null;
    const searchWrap = sidebar.querySelector('div.relative.mb-6');
    const sectionHeadings = Array.from(sidebar.querySelectorAll('nav p'));
    const dropdownArrows = Array.from(sidebar.querySelectorAll('.sa-dropdown-arrow'));
    const dropdownMenus = Array.from(sidebar.querySelectorAll('.sa-dropdown-menu'));
    const dropdownDetails = Array.from(sidebar.querySelectorAll('.sa-dropdown-container'));

    const userSection = sidebar.querySelector('div.border-t');
    const userRow = userSection ? userSection.querySelector('div.flex.items-center.gap-3') : null;
    const userText = userRow && userRow.children.length > 1 ? userRow.children[1] : null;

    sidebar.classList.add('md:relative', 'transition-all', 'duration-200', 'ease-in-out');
    sidebar.style.overflowX = 'visible';

    links.forEach((link) => {
        let labelEl = link.querySelector('.sidebar-label');

        if (!labelEl) {
            const textNodes = Array.from(link.childNodes).filter(
                (n) => n.nodeType === Node.TEXT_NODE && n.textContent.trim().length
            );

            if (textNodes.length) {
                labelEl = document.createElement('span');
                labelEl.className = 'sidebar-label whitespace-nowrap';
                labelEl.textContent = textNodes
                    .map((n) => n.textContent.replace(/\s+/g, ' ').trim())
                    .join(' ');
                textNodes.forEach((n) => n.remove());
                link.appendChild(labelEl);
            }
        }

        const labelText = (labelEl?.textContent || '').trim();
        if (labelText) link.dataset.label = labelText;
    });

    let toggleBtn = document.getElementById('desktopSidebarToggle');
    if (!toggleBtn) {
        toggleBtn = document.createElement('button');
        toggleBtn.id = 'desktopSidebarToggle';
        toggleBtn.type = 'button';
        toggleBtn.className = [
            'hidden', 'md:flex', 'absolute', 'top-4', 'right-0', 'translate-x-1/2',
            'h-8', 'w-8', 'items-center', 'justify-center', 'rounded-full',
            'border', 'border-white/20', 'bg-orange-500', 'text-white',
            'hover:bg-orange-400', 'z-[9999]', 'shadow-lg'
        ].join(' ');
        sidebar.appendChild(toggleBtn);
    }

    let collapsed = false;

    function applyState() {
        const isDesktop = desktopMQ.matches;

        if (!isDesktop) {
            collapsed = false;
            toggleBtn.classList.add('hidden');
        } else {
            toggleBtn.classList.remove('hidden');
        }

        sidebar.classList.toggle('w-20', isDesktop && collapsed);
        sidebar.classList.toggle('w-72', !collapsed || !isDesktop);

        if (topRow) {
            topRow.classList.toggle('justify-center', isDesktop && collapsed);
            topRow.classList.toggle('justify-between', !(isDesktop && collapsed));
        }
        if (brandGroup) {
            brandGroup.classList.toggle('justify-center', isDesktop && collapsed);
        }
        if (brandText) {
            brandText.style.display = (isDesktop && collapsed) ? 'none' : '';
        }
        if (searchWrap) {
            searchWrap.style.display = (isDesktop && collapsed) ? 'none' : '';
        }

        if (userRow) {
            userRow.classList.toggle('justify-center', isDesktop && collapsed);
        }
        if (userText) {
            userText.style.display = (isDesktop && collapsed) ? 'none' : '';
        }

        sectionHeadings.forEach((heading) => {
            heading.style.display = (isDesktop && collapsed) ? 'none' : '';
        });

        dropdownArrows.forEach((arrow) => {
            arrow.style.display = (isDesktop && collapsed) ? 'none' : '';
        });

        dropdownMenus.forEach((menu) => {
            menu.style.display = (isDesktop && collapsed) ? 'none' : '';
        });

        if (isDesktop && collapsed) {
            dropdownDetails.forEach((details) => {
                details.removeAttribute('open');
            });
        }

        links.forEach((link) => {
            const labelEl = link.querySelector('.sidebar-label');
            if (labelEl) labelEl.style.display = (isDesktop && collapsed) ? 'none' : '';

            link.classList.toggle('justify-center', isDesktop && collapsed);
            link.classList.toggle('gap-0', isDesktop && collapsed);
            link.title = (isDesktop && collapsed) ? (link.dataset.label || '') : '';
        });

        toggleBtn.innerHTML = collapsed
            ? '<i class="fas fa-angle-double-right text-sm"></i>'
            : '<i class="fas fa-angle-double-left text-sm"></i>';
        toggleBtn.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
        toggleBtn.style.zIndex = '9999';
    }

    toggleBtn.addEventListener('click', () => {
        if (!desktopMQ.matches) return;
        collapsed = !collapsed;
        applyState();
    });

    if (desktopMQ.addEventListener) {
        desktopMQ.addEventListener('change', applyState);
    } else {
        desktopMQ.addListener(applyState);
    }

    applyState();
})();
