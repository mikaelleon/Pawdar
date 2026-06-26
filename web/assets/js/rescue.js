document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-claim-stray]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-claim-stray');
            fetch('ajax/claim-stray.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': getCsrfToken() },
                body: JSON.stringify({ incident_id: parseInt(id, 10) })
            }).then(function (res) { return res.json(); }).then(function (data) {
                if (data.success) location.reload();
            });
        });
    });

    document.querySelectorAll('[data-adopt-contact]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var phone = btn.getAttribute('data-adopt-contact');
            alert('Contact rescue organization: ' + phone);
        });
    });
});

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}
