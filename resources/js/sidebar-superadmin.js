function initSuperadminMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobileSidebarOverlay');
    const openBtn = document.getElementById('mobileSidebarOpenBtn');
    const closeBtn = document.getElementById('mobileSidebarCloseBtn');
    const links = sidebar ? Array.from(sidebar.querySelectorAll('a.sidebar-link')) : [];

    if (!sidebar || !overlay) return;

    function isMobile() {
        return window.innerWidth < 768;
    }

    function openSidebar() {
        if (!isMobile()) return;
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.classList.add('opacity-100');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.remove('opacity-100');
        overlay.classList.add('opacity-0', 'pointer-events-none');
    }

    function toggleSidebar() {
        if (!isMobile()) return;
        if (sidebar.classList.contains('-translate-x-full')) {
            openSidebar();
        } else {
            closeSidebar();
        }
    }

    if (openBtn) {
        openBtn.addEventListener('click', toggleSidebar);
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', toggleSidebar);
    }
    overlay.addEventListener('click', closeSidebar);
    links.forEach((link) => link.addEventListener('click', closeSidebar));

    // Close sidebar on desktop breakpoint.
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0', 'pointer-events-none');
        } else {
            closeSidebar();
        }
    });

    if (isMobile()) {
        closeSidebar();
    }

}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSuperadminMobileSidebar);
} else {
    initSuperadminMobileSidebar();
}
