import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.feedmartTheme = {
    isDark() {
        return document.documentElement.classList.contains('dark');
    },
    toggle() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('feedmart_theme', isDark ? 'dark' : 'light');
        window.dispatchEvent(new CustomEvent('feedmart-theme-changed', { detail: { dark: isDark } }));
        return isDark;
    },
};

Alpine.data('themeSwitcher', () => ({
    dark: document.documentElement.classList.contains('dark'),
    init() {
        window.addEventListener('feedmart-theme-changed', (e) => {
            this.dark = e.detail.dark;
        });
    },
    toggle() {
        this.dark = window.feedmartTheme.toggle();
    },
}));

Alpine.start();
