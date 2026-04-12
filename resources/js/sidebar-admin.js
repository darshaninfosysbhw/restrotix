
(function () {
    // Theme toggle (same as before)
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');

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
        if (savedTheme === 'light') {
            document.body.classList.add('light-theme');
        } else {
            document.body.classList.remove('light-theme');
        }
        setIconBasedOnTheme();
        themeToggleBtn.addEventListener('click', function () {
            document.body.classList.toggle('light-theme');
            localStorage.setItem('restochain-theme', document.body.classList.contains('light-theme') ?
                'light' : 'dark');
            setIconBasedOnTheme();
        });
    }

    // Mobile sidebar toggle
    const hamburger = document.getElementById('hamburgerBtn');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const closeBtn = document.getElementById('closeSidebarBtn');

    function openSidebar() {
        mobileSidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
    }

    function closeSidebar() {
        mobileSidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
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
})();
//end here mobile sidebar script

// Desktop sidebar toggle script
const desktopToggleBtn = document.getElementById('desktopToggleBtn');
const sidebar = document.getElementById('sidebar');
const toggleIcon = document.getElementById('toggleIcon');

if (desktopToggleBtn && sidebar && toggleIcon) {
    desktopToggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-collapsed');
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
