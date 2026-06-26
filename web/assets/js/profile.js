document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-notify-pref]').forEach(function (input) {
        input.addEventListener('change', function () {
            var field = input.getAttribute('data-notify-pref');
            fetch('ajax/profile_prefs.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': getCsrfToken() },
                body: JSON.stringify({ field: field, value: input.checked ? 1 : 0 })
            });
        });
    });

    setInterval(function () {
        fetch('ajax/notif_count.php')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.success) return;
                var badge = document.querySelector('[data-notification-count]');
                if (!badge) return;
                badge.textContent = data.count;
                badge.classList.toggle('is-hidden', data.count <= 0);
            });
    }, 30000);
});

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}
