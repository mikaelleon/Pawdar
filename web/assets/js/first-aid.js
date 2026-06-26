document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('guide-search');
    if (search) {
        search.addEventListener('input', function () {
            var q = search.value.trim().toLowerCase();
            document.querySelectorAll('[data-guide-item]').forEach(function (item) {
                var title = item.getAttribute('data-guide-title') || '';
                item.hidden = q !== '' && !title.includes(q);
            });
        });
    }

    document.querySelectorAll('[data-report-from-guide]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var type = btn.getAttribute('data-report-from-guide');
            var radio = document.querySelector('[data-report-form] input[name="incident_type"][value="' + type + '"]');
            if (radio) {
                radio.checked = true;
            }
            if (typeof openReportDrawer === 'function') {
                openReportDrawer();
            }
        });
    });
});
