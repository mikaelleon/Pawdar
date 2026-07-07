document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('guide-search');
    var stepHintKey = 'pawdarGuideStepHintSeen';

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

    initGuideStepToggles();
    initGuideStepHint();

    function initGuideStepToggles() {
        document.querySelectorAll('[data-guide-step-toggle]').forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                var step = toggle.closest('[data-guide-step]');
                var detail = step ? step.querySelector('[data-guide-step-detail]') : null;
                if (!detail) {
                    return;
                }

                var isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                var nextExpanded = !isExpanded;
                toggle.setAttribute('aria-expanded', nextExpanded ? 'true' : 'false');
                detail.hidden = !nextExpanded;

                var label = toggle.querySelector('.guide-step-toggle-label');
                if (label) {
                    label.textContent = nextExpanded ? 'See less' : 'See more';
                }

                dismissGuideStepHint();

                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        });
    }

    function initGuideStepHint() {
        var hint = document.querySelector('[data-guide-step-hint]');
        if (!hint) {
            return;
        }

        var hasExpandable = document.querySelector('[data-guide-step-toggle]');
        if (!hasExpandable) {
            return;
        }

        try {
            if (sessionStorage.getItem(stepHintKey) === '1') {
                return;
            }
        } catch (err) {
            /* ignore */
        }

        hint.hidden = false;
    }

    function dismissGuideStepHint() {
        var hint = document.querySelector('[data-guide-step-hint]');
        if (hint) {
            hint.hidden = true;
        }

        try {
            sessionStorage.setItem(stepHintKey, '1');
        } catch (err) {
            /* ignore */
        }
    }
});
