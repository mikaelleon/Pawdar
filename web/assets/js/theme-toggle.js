(function () {
    var STORAGE_KEY = 'pawdar-theme';

    function getToggleButtons() {
        var buttons = document.querySelectorAll('[data-theme-toggle]');
        if (buttons.length) {
            return buttons;
        }

        var legacy = document.getElementById('darkModeToggle');
        return legacy ? [legacy] : [];
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        updateToggleIcons(theme);
    }

    function updateToggleIcons(theme) {
        getToggleButtons().forEach(function (btn) {
            var icon = btn.querySelector('[data-theme-icon]');
            if (icon) {
                icon.setAttribute('data-lucide', theme === 'dark' ? 'moon' : 'sun');
            }

            btn.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
            btn.setAttribute('title', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        });

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var savedTheme = localStorage.getItem(STORAGE_KEY)
            || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        applyTheme(savedTheme);

        getToggleButtons().forEach(function (btn) {
            btn.addEventListener('click', function () {
                var current = document.documentElement.getAttribute('data-theme') || 'light';
                var next = current === 'dark' ? 'light' : 'dark';
                applyTheme(next);
                localStorage.setItem(STORAGE_KEY, next);
            });
        });
    });
})();
