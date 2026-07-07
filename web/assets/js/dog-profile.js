document.addEventListener('DOMContentLoaded', function () {
    var profile = document.querySelector('[data-dog-profile]');

    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            navigator.clipboard.writeText(btn.getAttribute('data-copy') || '');
            if (window.PawdarUI && PawdarUI.showToast) {
                PawdarUI.showToast('Copied!');
            }
        });
    });

    var idCardModal = document.querySelector('[data-id-card-modal]');

    if (idCardModal && idCardModal.parentElement !== document.body) {
        document.body.appendChild(idCardModal);
    }

    function openIdCardModal() {
        if (!idCardModal) {
            return;
        }
        idCardModal.hidden = false;
        document.body.classList.add('modal-open', 'id-card-modal-open');
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
        window.requestAnimationFrame(function () {
            idCardModal.scrollTop = 0;
            var card = idCardModal.querySelector('[data-id-card-print-target]');
            if (card && typeof card.scrollIntoView === 'function') {
                card.scrollIntoView({ block: 'nearest', behavior: 'auto' });
            }
        });
    }

    function closeIdCardModal() {
        if (!idCardModal) {
            return;
        }
        idCardModal.hidden = true;
        document.body.classList.remove('modal-open', 'id-card-modal-open', 'id-card-printing');
    }

    document.querySelectorAll('[data-print-id-card]').forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            event.preventDefault();
            if (idCardModal) {
                openIdCardModal();
                return;
            }

            var dogId = profile ? profile.getAttribute('data-dog-id') : '';
            if (dogId) {
                window.open('dog-id-card.php?id=' + encodeURIComponent(dogId), '_blank', 'noopener');
            }
        });
    });

    if (idCardModal) {
        idCardModal.querySelectorAll('[data-id-card-close], [data-id-card-close-inline]').forEach(function (btn) {
            btn.addEventListener('click', closeIdCardModal);
        });

        idCardModal.addEventListener('click', function (event) {
            if (event.target === idCardModal) {
                closeIdCardModal();
            }
        });

        var idCardPrintBtn = idCardModal.querySelector('[data-id-card-print]');
        if (idCardPrintBtn) {
            idCardPrintBtn.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
                document.body.classList.add('id-card-printing');
                window.requestAnimationFrame(function () {
                    window.requestAnimationFrame(function () {
                        window.print();
                    });
                });
            });
        }
    }

    window.addEventListener('beforeprint', function () {
        if (idCardModal && !idCardModal.hidden) {
            document.body.classList.add('id-card-printing');
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }
    });

    window.addEventListener('afterprint', function () {
        document.body.classList.remove('id-card-printing');
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && idCardModal && !idCardModal.hidden) {
            closeIdCardModal();
        }
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

    document.querySelectorAll('[data-report-dog-incident]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!profile) return;
            var dogId = profile.getAttribute('data-dog-id');
            var dogName = profile.getAttribute('data-dog-name') || '';
            var registryId = profile.getAttribute('data-registry-id') || '';
            if (typeof window.openReportDrawerPrefill === 'function') {
                window.openReportDrawerPrefill({
                    dogId: dogId,
                    dogName: dogName,
                    registryId: registryId
                });
            } else if (typeof window.openReportDrawer === 'function') {
                window.openReportDrawer();
            }
        });
    });

    var editModal = document.querySelector('[data-dog-edit-modal]');
    var editForm = editModal ? editModal.querySelector('[data-dog-edit-form]') : null;
    var coatSelect = editModal ? editModal.querySelector('[data-coat-select]') : null;
    var coatOtherWrap = editModal ? editModal.querySelector('[data-coat-other-wrap]') : null;
    var appMain = document.querySelector('.app-main');

    if (editModal && editModal.parentElement !== document.body) {
        document.body.appendChild(editModal);
    }

    function openEditModal() {
        if (!editModal) {
            return;
        }
        editModal.hidden = false;
        document.body.classList.add('modal-open', 'dog-edit-modal-open');
        if (appMain) {
            appMain.setAttribute('aria-hidden', 'true');
        }
        var firstField = editForm ? editForm.querySelector('input, select, textarea') : null;
        if (firstField) {
            window.setTimeout(function () { firstField.focus(); }, 0);
        }
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    document.querySelectorAll('[data-open-edit-dog]').forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            event.preventDefault();
            openEditModal();
        });
    });

    function closeEditModal() {
        if (!editModal) {
            return;
        }
        editModal.hidden = true;
        document.body.classList.remove('modal-open', 'dog-edit-modal-open');
        if (appMain) {
            appMain.removeAttribute('aria-hidden');
        }
    }

    document.querySelectorAll('[data-close-dog-edit]').forEach(function (btn) {
        btn.addEventListener('click', closeEditModal);
    });

    if (editModal) {
        editModal.addEventListener('click', function (event) {
            if (event.target === editModal) {
                closeEditModal();
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && editModal && !editModal.hidden) {
            closeEditModal();
        }
    });

    if (coatSelect && coatOtherWrap) {
        coatSelect.addEventListener('change', function () {
            coatOtherWrap.hidden = coatSelect.value !== 'Other';
        });
    }

    if (editForm && profile) {
        editForm.addEventListener('submit', function (event) {
            event.preventDefault();
            var submitBtn = editForm.querySelector('[data-dog-edit-submit]');
            if (window.PawdarUI) PawdarUI.setButtonLoading(submitBtn, true);

            var payload = {
                dog_id: parseInt(profile.getAttribute('data-dog-id'), 10),
                dog_name: editForm.querySelector('[name="dog_name"]').value.trim(),
                gender: editForm.querySelector('[name="gender"]').value,
                age: parseInt(editForm.querySelector('[name="age"]').value, 10) || 0,
                coat_color: editForm.querySelector('[name="coat_color"]').value,
                coat_color_other: editForm.querySelector('[name="coat_color_other"]').value.trim(),
                weight_kg: editForm.querySelector('[name="weight_kg"]').value,
                distinguishing_marks: editForm.querySelector('[name="distinguishing_marks"]').value.trim(),
                temperament_notes: editForm.querySelector('[name="temperament_notes"]').value.trim(),
                health_notes: editForm.querySelector('[name="health_notes"]').value.trim()
            };

            fetch('ajax/update_dog.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify(payload)
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (window.PawdarUI) PawdarUI.setButtonLoading(submitBtn, false);
                    if (data.success) {
                        if (window.PawdarUI && PawdarUI.showToast) {
                            PawdarUI.showToast('Profile updated', 'success');
                        }
                        window.setTimeout(function () { window.location.reload(); }, 500);
                        return;
                    }
                    if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast(data.message || 'Update failed', 'error');
                    }
                })
                .catch(function () {
                    if (window.PawdarUI) PawdarUI.setButtonLoading(submitBtn, false);
                    if (window.PawdarUI && PawdarUI.showToast) {
                        PawdarUI.showToast('Network error.', 'error');
                    }
                });
        });
    }

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
