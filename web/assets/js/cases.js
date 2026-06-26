document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.case-status-select').forEach(function (select) {
        select.addEventListener('change', function () {
            var incidentId = select.getAttribute('data-case-status');
            var status = select.value;
            if (!incidentId) return;

            if ((status === 'Resolved' || status === 'Referred') && !window.confirm('Mark this case as ' + status + '?')) {
                return;
            }

            fetch('ajax/update_case.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify({ incident_id: parseInt(incidentId, 10), status: status })
            }).then(function (res) { return res.json(); }).then(function (data) {
                if (data.success && window.PawdarUI && window.PawdarUI.toast) {
                    window.PawdarUI.toast('Case updated to ' + data.status_label, 'success');
                }
            });
        });
    });
});

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}
