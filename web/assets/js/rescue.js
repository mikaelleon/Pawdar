var RESCUE_ACCENT_MAP = {
    'Spotted': 'is-spotted',
    'Rescued': 'is-rescued',
    'Under Vet Care': 'is-vet',
    'Ready for Adoption': 'is-adoption'
};

var RESCUE_BADGE_MAP = {
    'Spotted': 'badge-investigating',
    'Rescued': 'badge-resolved',
    'Under Vet Care': 'badge-received',
    'Ready for Adoption': 'badge-resolved'
};

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-claim-stray]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (window.PawdarUI && PawdarUI.setButtonLoading) {
                PawdarUI.setButtonLoading(btn, true);
            }
            var id = btn.getAttribute('data-claim-stray');
            fetch('ajax/claim-stray.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': getCsrfToken() },
                body: JSON.stringify({ incident_id: parseInt(id, 10) })
            }).then(function (res) { return res.json(); }).then(function (data) {
                if (data.success) {
                    location.reload();
                    return;
                }
                if (window.PawdarUI && PawdarUI.setButtonLoading) {
                    PawdarUI.setButtonLoading(btn, false);
                }
                if (window.PawdarUI && PawdarUI.showToast) {
                    PawdarUI.showToast(data.message || 'Could not claim case.', 'error');
                }
            }).catch(function () {
                if (window.PawdarUI && PawdarUI.setButtonLoading) {
                    PawdarUI.setButtonLoading(btn, false);
                }
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

            var accent = document.querySelector('[data-rescue-accent="' + caseId + '"]');
            var previousAccentClass = accent ? accent.className : '';

            if (badge) {
                badge.textContent = newStatus;
                badge.className = 'badge ' + (RESCUE_BADGE_MAP[newStatus] || 'badge-received') + ' rescue-status-badge';
            }

            if (accent) {
                accent.className = 'rescue-track-card-accent ' + (RESCUE_ACCENT_MAP[newStatus] || 'is-spotted');
            }

            var updatedEl = document.querySelector('[data-rescue-updated="' + caseId + '"]');
            var previousBadgeClass = badge ? badge.className : '';

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
                            badge.className = previousBadgeClass || ('badge ' + (RESCUE_BADGE_MAP[previousStatus] || 'badge-received') + ' rescue-status-badge');
                        }
                        select.value = previousValue;
                        if (accent) {
                            accent.className = previousAccentClass;
                        }
                        if (window.PawdarUI && PawdarUI.showToast) {
                            PawdarUI.showToast(data.message || 'Failed to update status.', 'error');
                        }
                        return;
                    }

                    if (updatedEl) {
                        updatedEl.textContent = 'Updated 1s ago';
                    }

                    if (window.PawdarUI && PawdarUI.showToast) {
                        if (newStatus === 'Ready for Adoption') {
                            PawdarUI.showToast('Dog published to Adoption listings.', 'success');
                            setTimeout(function () { location.reload(); }, 1200);
                        } else {
                            PawdarUI.showToast('Status updated to ' + newStatus, 'success');
                        }
                    }
                })
                .catch(function () {
                    if (badge) {
                        badge.textContent = previousStatus;
                        badge.className = previousBadgeClass || ('badge ' + (RESCUE_BADGE_MAP[previousStatus] || 'badge-received') + ' rescue-status-badge');
                    }
                    select.value = previousValue;
                    if (accent) {
                        accent.className = previousAccentClass;
                    }
                    if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast('Network error. Please try again.', 'error');
                    }
                });
        });
    });

    document.querySelectorAll('[data-adopt-contact]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var phone = btn.getAttribute('data-adopt-contact') || '';
            if (!phone) {
                if (window.PawdarUI && PawdarUI.showToast) {
                    PawdarUI.showToast('No contact number on file for this listing.', 'error');
                }
                return;
            }
            if (window.PawdarUI && PawdarUI.showToast) {
                PawdarUI.showToast('Contact rescue organization: ' + phone, 6000);
            }
        });
    });
});

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}
