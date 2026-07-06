document.addEventListener('DOMContentLoaded', function () {
    var bulkBar = document.querySelector('[data-cases-bulk]');
    var bulkCount = document.querySelector('[data-cases-bulk-count]');
    var bulkStatus = document.querySelector('[data-cases-bulk-status]');
    var bulkApply = document.querySelector('[data-cases-bulk-apply]');
    var selectAll = document.querySelector('[data-cases-select-all]');
    var rowChecks = document.querySelectorAll('[data-case-check]');

    function getSelectedIncidentIds() {
        return Array.prototype.slice.call(document.querySelectorAll('[data-case-check]:checked'))
            .map(function (el) { return parseInt(el.value, 10); })
            .filter(function (id) { return id > 0; });
    }

    function refreshBulkBar() {
        var ids = getSelectedIncidentIds();
        if (!bulkBar || !bulkCount) return;
        bulkBar.hidden = ids.length === 0;
        bulkCount.textContent = ids.length + ' selected';
        if (selectAll) {
            selectAll.checked = ids.length > 0 && ids.length === rowChecks.length;
            selectAll.indeterminate = ids.length > 0 && ids.length < rowChecks.length;
        }
    }

    rowChecks.forEach(function (check) {
        check.addEventListener('change', refreshBulkBar);
    });

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowChecks.forEach(function (check) {
                check.checked = selectAll.checked;
            });
            refreshBulkBar();
        });
    }

    function postCaseUpdate(incidentIds, status, onSuccess) {
        return fetch('ajax/update_case.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': getCsrfToken()
            },
            body: JSON.stringify({ incident_ids: incidentIds, status: status })
        }).then(function (res) { return res.json(); }).then(function (data) {
            if (data.success) {
                if (typeof onSuccess === 'function') {
                    onSuccess(data);
                }
                if (window.PawdarUI && PawdarUI.showToast) {
                    PawdarUI.showToast(data.message || 'Case status updated.', 4000);
                }
                window.setTimeout(function () { window.location.reload(); }, 600);
                return;
            }
            if (window.PawdarUI && PawdarUI.showToast) {
                PawdarUI.showToast(data.message || 'Update failed.', 4000);
            }
        }).catch(function () {
            if (window.PawdarUI && PawdarUI.showToast) {
                PawdarUI.showToast('Network error. Please try again.', 4000);
            }
        });
    }

    document.querySelectorAll('.case-status-select[data-case-status]').forEach(function (select) {
        select.addEventListener('focus', function () {
            select.dataset.previousValue = select.value;
        });

        select.addEventListener('change', function () {
            var incidentId = select.getAttribute('data-case-status');
            var status = select.value;
            var previousValue = select.dataset.previousValue || status;

            if (!incidentId) return;

            if ((status === 'Resolved' || status === 'Referred') && !window.confirm('Mark this case as ' + status + '?')) {
                select.value = previousValue;
                return;
            }

            select.disabled = true;
            postCaseUpdate([parseInt(incidentId, 10)], status).finally(function () {
                select.disabled = false;
            });
        });
    });

    if (bulkApply && bulkStatus) {
        bulkApply.addEventListener('click', function () {
            var ids = getSelectedIncidentIds();
            var status = bulkStatus.value;
            if (ids.length === 0) return;

            if ((status === 'Resolved' || status === 'Referred') &&
                !window.confirm('Mark ' + ids.length + ' case(s) as ' + status + '?')) {
                return;
            }

            bulkApply.disabled = true;
            postCaseUpdate(ids, status).finally(function () {
                bulkApply.disabled = false;
            });
        });
    }

    refreshBulkBar();
});

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}
