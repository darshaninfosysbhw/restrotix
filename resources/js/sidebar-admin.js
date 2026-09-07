
(function () {
    // Theme toggle (same as before)
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');

    function applyThemeState(isLightTheme) {
        document.body.classList.toggle('light-theme', isLightTheme);
        document.body.classList.toggle('dark', !isLightTheme);
    }

    function setIconBasedOnTheme() {
        if (document.body.classList.contains('light-theme')) {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        } else {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }
    }
    if (themeToggleBtn && themeIcon) {
        const savedTheme = localStorage.getItem('restochain-theme');
        const isLightTheme = savedTheme !== 'dark';
        applyThemeState(isLightTheme);
        setIconBasedOnTheme();
        themeToggleBtn.addEventListener('click', function () {
            const isLightThemeNow = document.body.classList.toggle('light-theme');
            document.body.classList.toggle('dark', !isLightThemeNow);
            localStorage.setItem('restochain-theme', isLightThemeNow ? 'light' : 'dark');
            setIconBasedOnTheme();
        });
    }

    // Mobile sidebar toggle
    const hamburger = document.getElementById('hamburgerBtn');
    const mobileSidebar = document.querySelector('.admin-navigation-sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const closeBtn = document.getElementById('closeSidebarBtn');
    const desktopViewport = window.matchMedia('(min-width: 48rem)');

    if (!mobileSidebar || !backdrop) return;

    function openSidebar() {
        if (desktopViewport.matches) return;
        mobileSidebar.classList.add('is-mobile-open');
        mobileSidebar.inert = false;
        backdrop.classList.remove('hidden');
        hamburger?.setAttribute('aria-expanded', 'true');
        closeBtn?.focus();
    }

    function closeSidebar() {
        const hadFocus = mobileSidebar.contains(document.activeElement);
        mobileSidebar.classList.remove('is-mobile-open');
        mobileSidebar.inert = !desktopViewport.matches;
        backdrop.classList.add('hidden');
        hamburger?.setAttribute('aria-expanded', 'false');
        if (hadFocus && !desktopViewport.matches) hamburger?.focus();
    }

    if (hamburger) {
        hamburger.addEventListener('click', openSidebar);
    }
    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mobileSidebar.classList.contains('is-mobile-open')) closeSidebar();
    });
    desktopViewport.addEventListener('change', closeSidebar);
    closeSidebar();
})();
//end here mobile sidebar script

// Desktop sidebar toggle script
const desktopToggleBtn = document.getElementById('desktopToggleBtn');
const sidebar = document.querySelector('.admin-navigation-sidebar');
const toggleIcon = document.getElementById('toggleIcon');

if (desktopToggleBtn && sidebar && toggleIcon) {
    desktopToggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-collapsed');
        const collapsed = sidebar.classList.contains('sidebar-collapsed');
        desktopToggleBtn.setAttribute('aria-expanded', String(!collapsed));
        desktopToggleBtn.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
        if (sidebar.classList.contains('sidebar-collapsed')) {
            toggleIcon.classList.replace('fa-angle-double-left', 'fa-angle-double-right');
        } else {
            toggleIcon.classList.replace('fa-angle-double-right', 'fa-angle-double-left');
        }
    });
}
// end Desktop Sidebar logic script

// Jitne bhi dropdown-trigger hain, sab par event listener lagao(sidebar ke andar ke dropdowns ke liye)
document.querySelectorAll('.dropdown-trigger').forEach(button => {
    button.addEventListener('click', function () {
        const menu = this.nextElementSibling;
        const arrow = this.querySelector('.trigger-arrow');
        closeOtherDropdowns(menu);
        menu.classList.toggle('hidden');
        if (arrow && !menu.classList.contains('hidden')) {
            arrow.style.transform = 'rotate(90deg)';
        } else if (arrow) {
            arrow.style.transform = 'rotate(0deg)';
        }
    });
});

// Sidebar collapsed ho toh dropdown band rahein (Optional logic)
function closeOtherDropdowns(currentMenu) {
    document.querySelectorAll('.dropdown-menu').forEach(m => {
        if (m !== currentMenu) {
            m.classList.add('hidden');
            const arrow = m.previousElementSibling?.querySelector('.trigger-arrow');
            if (arrow) {
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    });
}
