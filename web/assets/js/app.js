document.addEventListener('DOMContentLoaded', function () {
    if (window.lucide) {
        lucide.createIcons();
    }

    var menuBtn = document.querySelector('[data-mobile-menu]');
    var mobileNav = document.querySelector('[data-mobile-nav]');

    if (menuBtn && mobileNav) {
        menuBtn.addEventListener('click', function () {
            mobileNav.classList.toggle('is-open');
        });
    }

    document.querySelectorAll('[data-role-chip]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            if (chip.classList.contains('is-disabled')) {
                return;
            }

            document.querySelectorAll('[data-role-chip]').forEach(function (c) {
                c.classList.remove('is-selected');
            });
            chip.classList.add('is-selected');
        });
    });
});
