(function () {
    var STORAGE_KEY = 'pawdar-theme';

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        updateToggleIcon(theme);
    }

    function updateToggleIcon(theme) {
        var btn = document.getElementById('darkModeToggle');
        if (!btn) {
            return;
        }

        var icon = btn.querySelector('[data-theme-icon]');
        if (icon) {
            icon.setAttribute('data-lucide', theme === 'dark' ? 'moon' : 'sun');
            if (window.lucide) {
                lucide.createIcons();
            }
        }

        btn.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        btn.setAttribute('title', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var savedTheme = localStorage.getItem(STORAGE_KEY)
            || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        applyTheme(savedTheme);

        var btn = document.getElementById('darkModeToggle');
        if (!btn) {
            return;
        }

        btn.addEventListener('click', function () {
            var current = document.documentElement.getAttribute('data-theme') || 'light';
            var next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem(STORAGE_KEY, next);
        });
    });
})();
