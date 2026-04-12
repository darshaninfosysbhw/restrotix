(() => {
    const themeToggleBtn = document.getElementById('sa-theme-toggle');
    const themeIcon = document.getElementById('sa-theme-icon');
    if (!themeToggleBtn || !themeIcon) return;

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
    if (savedTheme === 'light') {
        document.body.classList.add('light-theme');
    } else {
        document.body.classList.remove('light-theme');
    }
    setIconBasedOnTheme();

    themeToggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('light-theme');
        localStorage.setItem('restochain-theme', document.body.classList.contains('light-theme') ? 'light' : 'dark');
        setIconBasedOnTheme();
    });
})();
