<script data-theme-bootstrap>
    (() => {
        const themes = { light: true, dark: true };
        const root = document.documentElement;
        let storedTheme = null;

        try {
            storedTheme = localStorage.getItem('aegoryx.theme');
        } catch (error) {
            storedTheme = null;
        }

        const currentTheme = root.dataset.theme;
        const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const nextTheme = themes[currentTheme] ? currentTheme : (themes[storedTheme] ? storedTheme : preferredTheme);

        root.dataset.theme = nextTheme;
    })();
</script>
