<script>
    (function() {
        try {
            var savedTheme = localStorage.getItem('restochain-theme');
            var isLightTheme = savedTheme !== 'dark';
            document.body.classList.toggle('light-theme', isLightTheme);
            document.body.classList.toggle('dark', !isLightTheme);
        } catch (error) {
            document.body.classList.add('light-theme');
            document.body.classList.remove('dark');
        }
    })();
</script>
