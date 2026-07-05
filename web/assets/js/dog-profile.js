document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            navigator.clipboard.writeText(btn.getAttribute('data-copy') || '');
            if (window.PawdarUI && PawdarUI.showToast) {
                PawdarUI.showToast('Copied!');
            }
        });
    });

    document.querySelectorAll('[data-flag-dog]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!window.PawdarUI || !PawdarUI.showConfirmModal) {
                return;
            }
            PawdarUI.showConfirmModal({
                title: 'Flag this dog?',
                body: 'Are you sure you want to flag this dog? This will notify the admin for review.',
                confirmLabel: 'Confirm'
            }).then(function (ok) {
                if (ok && PawdarUI.showToast) {
                    PawdarUI.showToast('Dog flagged for review');
                }
            });
        });
    });

    document.querySelectorAll('[data-cosign-vaccine]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var vaccineId = btn.getAttribute('data-vaccine-id');
            btn.disabled = true;
            btn.textContent = 'Signing…';

            fetch('ajax/cosign_vaccine.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify({ vaccine_id: parseInt(vaccineId, 10) })
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        if (window.PawdarUI && PawdarUI.showToast) {
                            PawdarUI.showToast('Vaccination record co-signed.', 'success');
                        }
                        setTimeout(function () { location.reload(); }, 800);
                        return;
                    }
                    if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast(data.message || 'Failed to co-sign.', 'error');
                    }
                    btn.disabled = false;
                    btn.textContent = 'Co-sign Vaccination';
                })
                .catch(function () {
                    if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast('Network error.', 'error');
                    }
                    btn.disabled = false;
                    btn.textContent = 'Co-sign Vaccination';
                });
        });
    });

    document.querySelectorAll('.btn-call-owner').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var contact = (btn.getAttribute('data-owner-contact') || '').trim();
            var name = btn.getAttribute('data-owner-name') || 'Owner';
            var contactLine = document.querySelector('[data-owner-contact-line]');

            if (!contact) {
                if (window.PawdarUI && PawdarUI.showToast) {
                    PawdarUI.showToast('No contact number on file for this owner.', 'error');
                }
                return;
            }

            if (contactLine) {
                contactLine.hidden = false;
                contactLine.textContent = contact;
            }

            var existing = btn.parentNode.querySelector('.call-tooltip');
            if (existing) {
                existing.remove();
            }

            var tooltip = document.createElement('div');
            tooltip.className = 'call-tooltip';
            tooltip.innerHTML = '<p>' + escapeHtml(name) + '</p><a href="tel:' + escapeHtml(contact.replace(/\s+/g, '')) + '">' + escapeHtml(contact) + '</a>';
            btn.parentNode.appendChild(tooltip);

            if (/Mobi|Android/i.test(navigator.userAgent)) {
                window.location.href = 'tel:' + contact.replace(/\s+/g, '');
            }

            setTimeout(function () {
                tooltip.remove();
            }, 5000);
        });
    });
});

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function escapeHtml(value) {
    var node = document.createElement('div');
    node.textContent = value || '';
    return node.innerHTML;
}
