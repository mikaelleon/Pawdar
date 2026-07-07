/**
 * Global corroborate (like) handler for feed cards and incident detail.
 */
(function () {
    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            return meta.getAttribute('content') || '';
        }

        var feed = document.querySelector('[data-feed-page]');
        return feed ? feed.getAttribute('data-csrf') || '' : '';
    }

    function showToast(message) {
        if (window.PawdarUI && PawdarUI.showToast) {
            PawdarUI.showToast(message, 4000);
        }
    }

    function handleCorroborate(btn) {
        if (!btn || btn.disabled) {
            return;
        }

        var incidentId = btn.getAttribute('data-corroborate');
        if (!incidentId) {
            return;
        }

        var csrfToken = getCsrfToken();
        if (!csrfToken) {
            showToast('Session expired. Please refresh the page.');
            return;
        }

        btn.disabled = true;

        fetch('ajax/corroborate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
                incident_id: parseInt(incidentId, 10),
                csrf_token: csrfToken
            })
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    btn.outerHTML = '<div class="chip chip-outline corroborated is-corroborated" title="You corroborated this report.">' +
                        '<i data-lucide="thumbs-up" style="width:14px;height:14px;color:var(--tea-green);"></i> ' +
                        (data.new_count || 0) + '</div>';
                    if (window.lucide) {
                        lucide.createIcons();
                    }
                    showToast('Thanks for corroborating');
                    return;
                }

                btn.disabled = false;
                showToast(data.message || 'Could not corroborate');
            })
            .catch(function () {
                btn.disabled = false;
                showToast('Network error. Please try again.');
            });
    }

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('[data-corroborate]');
        if (!btn || btn.disabled) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        handleCorroborate(btn);
    });

    window.PawdarCorroborate = {
        handle: handleCorroborate
    };
})();
