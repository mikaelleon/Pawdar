document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-gallery-thumb]').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            var src = thumb.getAttribute('data-src');
            var hero = document.querySelector('.breed-detail-hero-img');
            if (!src || !hero) return;
            hero.src = src;
            document.querySelectorAll('[data-gallery-thumb]').forEach(function (t) {
                t.classList.toggle('is-active', t === thumb);
            });
        });
    });
});
