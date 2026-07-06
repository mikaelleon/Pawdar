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

    if (window.PawdarCaseStatus) {
        PawdarCaseStatus.bindSelects(document);
    }

    if (bulkApply && bulkStatus && window.PawdarCaseStatus) {
        bulkApply.addEventListener('click', function () {
            var ids = getSelectedIncidentIds();
            var status = bulkStatus.value;
            if (ids.length === 0) return;

            if ((status === 'Resolved' || status === 'Referred') &&
                !window.confirm('Mark ' + ids.length + ' case(s) as ' + status + '?')) {
                return;
            }

            PawdarCaseStatus.promptRemarks(status, bulkStatus.value).then(function (result) {
                if (result.cancelled) return;
                bulkApply.disabled = true;
                PawdarCaseStatus.postCaseUpdate(ids, status, result.remarks).finally(function () {
                    bulkApply.disabled = false;
                });
            });
        });
    }

    refreshBulkBar();
});
