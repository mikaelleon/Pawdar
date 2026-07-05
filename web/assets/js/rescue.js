document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-claim-stray]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-claim-stray');
            fetch('ajax/claim-stray.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': getCsrfToken() },
                body: JSON.stringify({ incident_id: parseInt(id, 10) })
            }).then(function (res) { return res.json(); }).then(function (data) {
                if (data.success) {
                    location.reload();
                } else if (window.PawdarUI && PawdarUI.showToast) {
                    PawdarUI.showToast(data.message || 'Could not claim case.', 'error');
                }
            }).catch(function () {
                if (window.PawdarUI && PawdarUI.showToast) {
                    PawdarUI.showToast('Network error. Please try again.', 'error');
                }
            });
        });
    });

    document.querySelectorAll('[data-rescue-status]').forEach(function (select) {
        select.addEventListener('change', function () {
            var caseId = select.getAttribute('data-rescue-status');
            var newStatus = select.value;
            var badge = document.querySelector('[data-rescue-badge="' + caseId + '"]');
            var previousStatus = badge ? badge.textContent : '';
            var previousValue = previousStatus;

            if (badge) {
                badge.textContent = newStatus;
            }

            fetch('ajax/update_rescue_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': getCsrfToken() },
                body: JSON.stringify({ rescue_case_id: parseInt(caseId, 10), status: newStatus })
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data.success) {
                        if (badge) {
                            badge.textContent = previousStatus;
                        }
                        select.value = previousValue;
                        if (window.PawdarUI && PawdarUI.showToast) {
                            PawdarUI.showToast(data.message || 'Failed to update status.', 'error');
                        }
                        return;
                    }
                    if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast('Status updated to ' + newStatus, 'success');
                    }
                })
                .catch(function () {
                    if (badge) {
                        badge.textContent = previousStatus;
                    }
                    select.value = previousValue;
                    if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast('Network error. Please try again.', 'error');
                    }
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
