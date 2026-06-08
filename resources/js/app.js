const storedTheme = localStorage.getItem('aegoryx.theme');
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

document.documentElement.dataset.theme = storedTheme ?? (systemPrefersDark ? 'dark' : 'light');

window.aegoryxTheme = {
    set(theme) {
        document.documentElement.dataset.theme = theme;
        localStorage.setItem('aegoryx.theme', theme);
    },
    clear() {
        localStorage.removeItem('aegoryx.theme');
        document.documentElement.dataset.theme = systemPrefersDark ? 'dark' : 'light';
    },
};
