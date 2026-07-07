/**
 * Shared LGU case status updates with optional remarks prompt.
 */
(function () {
    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function updateStatusBadge(incidentId, data) {
        var badge = document.querySelector('[data-status-badge="' + incidentId + '"]');
        if (!badge || !data.success) return false;

        var dotOrCheck = data.status_label === 'Resolved'
            ? '<i data-lucide="check" style="width:12px;height:12px;"></i>'
            : '<span class="badge-dot" aria-hidden="true"></span>';
        badge.className = 'badge badge-with-dot ' + data.status_class;
        badge.innerHTML = dotOrCheck + data.status_label;
        if (window.lucide) {
            lucide.createIcons();
        }
        return true;
    }

    function promptRemarks(status) {
        if (!window.PawdarUI || typeof PawdarUI.showRemarksModal !== 'function') {
            return Promise.resolve({ cancelled: false, remarks: '' });
        }

        return PawdarUI.showRemarksModal({
            title: 'Update case status',
            body: 'Changing status to "' + status + '". Add optional remarks for the audit trail.',
            confirmLabel: 'Save status',
            placeholder: 'e.g. Dispatched animal control, awaiting site visit…'
        }).then(function (result) {
            if (!result.confirmed) {
                return { cancelled: true };
            }
            return { cancelled: false, remarks: result.remarks || '' };
        });
    }

    function postCaseUpdate(incidentIds, status, remarks, options) {
        options = options || {};

        return fetch('ajax/update_case.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': getCsrfToken()
            },
            body: JSON.stringify({
                incident_ids: incidentIds,
                status: status,
                remarks: remarks || ''
            })
        }).then(function (res) { return res.json(); }).then(function (data) {
            if (data.success) {
                if (typeof options.onSuccess === 'function') {
                    options.onSuccess(data);
                }
                if (window.PawdarUI && PawdarUI.showToast) {
                    PawdarUI.showToast(data.message || 'Case status updated.', 4000);
                }
                if (options.reload !== false && !options.skipReload) {
                    window.setTimeout(function () { window.location.reload(); }, 600);
                }
                return data;
            }
            if (window.PawdarUI && PawdarUI.showToast) {
                PawdarUI.showToast(data.message || 'Update failed.', 4000);
            }
            return data;
        }).catch(function () {
            if (window.PawdarUI && PawdarUI.showToast) {
                PawdarUI.showToast('Network error. Please try again.', 4000);
            }
        });
    }

    function handleStatusSelectChange(select) {
        var incidentId = select.getAttribute('data-case-status');
        var status = select.value;
        var previousValue = select.dataset.previousValue || status;

        if (!incidentId) return;

        var needsConfirm = status === 'Resolved' || status === 'Referred';

        function proceed(remarks) {
            select.disabled = true;
            var badgeUpdated = false;
            postCaseUpdate([parseInt(incidentId, 10)], status, remarks, {
                skipReload: true,
                onSuccess: function (data) {
                    badgeUpdated = updateStatusBadge(incidentId, data);
                }
            }).then(function (data) {
                select.disabled = false;
                if (data && data.success) {
                    select.dataset.previousValue = status;
                    if (!badgeUpdated || document.querySelector('[data-incident-detail]')) {
                        window.setTimeout(function () { window.location.reload(); }, 600);
                    }
                } else {
                    select.value = previousValue;
                }
            });
        }

        function runPrompt() {
            promptRemarks(status).then(function (result) {
                if (result.cancelled) {
                    select.value = previousValue;
                    return;
                }
                proceed(result.remarks);
            });
        }

        if (needsConfirm && !window.confirm('Mark this case as ' + status + '?')) {
            select.value = previousValue;
            return;
        }

        runPrompt();
    }

    window.PawdarCaseStatus = {
        postCaseUpdate: postCaseUpdate,
        promptRemarks: promptRemarks,
        handleStatusSelectChange: handleStatusSelectChange,
        bindSelects: function (root) {
            (root || document).querySelectorAll('.case-status-select[data-case-status]').forEach(function (select) {
                if (select.dataset.remarksBound === '1') return;
                select.dataset.remarksBound = '1';
                select.addEventListener('focus', function () {
                    select.dataset.previousValue = select.value;
                });
                select.addEventListener('change', function () {
                    handleStatusSelectChange(select);
                });
            });
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        window.PawdarCaseStatus.bindSelects(document);
    });
})();
