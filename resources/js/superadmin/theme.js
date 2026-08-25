(() => {
    const themeToggleBtn = document.getElementById('sa-theme-toggle');
    const themeIcon = document.getElementById('sa-theme-icon');
    if (!themeToggleBtn || !themeIcon) return;

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

    const savedTheme = localStorage.getItem('restochain-theme');
    const isLightTheme = savedTheme !== 'dark';
    applyThemeState(isLightTheme);
    setIconBasedOnTheme();

    themeToggleBtn.addEventListener('click', () => {
        const isLightThemeNow = document.body.classList.toggle('light-theme');
        document.body.classList.toggle('dark', !isLightThemeNow);
        localStorage.setItem('restochain-theme', isLightThemeNow ? 'light' : 'dark');
        setIconBasedOnTheme();
    });
})();
