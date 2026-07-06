document.addEventListener('DOMContentLoaded', function () {
    var feedPage = document.querySelector('[data-feed-page]');
    if (!feedPage) {
        return;
    }

    var csrfToken = feedPage.getAttribute('data-csrf') || getCsrfToken();
    var currentFilter = feedPage.getAttribute('data-filter') || 'all';
    var nextOffset = document.querySelectorAll('[data-incident-list] .incident-card:not(.incident-skeleton)').length;
    var incidentList = document.querySelector('[data-incident-list]');
    var loadMoreWrap = document.querySelector('[data-load-more-wrap]');
    var loadMoreBtn = document.querySelector('[data-load-more]');

    document.querySelectorAll('[data-filter-chips] .filter-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var filter = chip.getAttribute('data-filter');
            if (filter === currentFilter) {
                return;
            }

            currentFilter = filter;
            nextOffset = 0;
            updateFilterChips(filter);
            updateUrlFilter(filter);
            fetchFeed(filter, 0, false);
        });
    });

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            var offset = parseInt(feedPage.getAttribute('data-next-offset') || String(nextOffset), 10);
            fetchFeed(currentFilter, offset, true);
        });
    }

    incidentList.addEventListener('click', function (event) {
        var corroborateBtn = event.target.closest('[data-corroborate]');
        if (corroborateBtn) {
            handleCorroborate(corroborateBtn, csrfToken);
            return;
        }

        var detailsToggle = event.target.closest('[data-details-toggle]');
        if (detailsToggle) {
            toggleDetails(detailsToggle);
            return;
        }

        var claimBtn = event.target.closest('[data-claim-stray]');
        if (claimBtn) {
            handleClaimStray(claimBtn, csrfToken);
        }
    });

    incidentList.addEventListener('change', function (event) {
        var select = event.target.closest('[data-case-status]');
        if (select) {
            handleCaseStatusUpdate(select, csrfToken);
        }
    });

    if (typeof window.initReportDrawer === 'function') {
        window.initReportDrawer(csrfToken, function () {
            nextOffset = 0;
            fetchFeed(currentFilter, 0, false);
            showToast('Incident reported successfully');
        });
    }
});

function updateFilterChips(activeFilter) {
    document.querySelectorAll('[data-filter-chips] .filter-chip').forEach(function (chip) {
        var isActive = chip.getAttribute('data-filter') === activeFilter;
        chip.classList.toggle('chip-active', isActive);
        chip.classList.toggle('chip-outline', !isActive);
        chip.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
}

function updateUrlFilter(filter) {
    var url = new URL(window.location.href);
    if (filter === 'all') {
        url.searchParams.delete('filter');
    } else {
        url.searchParams.set('filter', filter);
    }
    window.history.replaceState({}, '', url.toString());
}

function fetchFeed(filter, offset, append) {
    var incidentList = document.querySelector('[data-incident-list]');
    var loadMoreWrap = document.querySelector('[data-load-more-wrap]');
    var loadMoreBtn = document.querySelector('[data-load-more]');

    if (!append) {
        incidentList.innerHTML = getSkeletonHtml(3);
    } else if (loadMoreBtn) {
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Loading…';
    }

    fetch('ajax/feed-filter.php?filter=' + encodeURIComponent(filter) + '&offset=' + offset + '&limit=10')
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.success) {
                if (!append) {
                    incidentList.innerHTML = getFeedErrorHtml('Could not load incidents. Please try again.');
                }
                return;
            }

            if (append) {
                incidentList.insertAdjacentHTML('beforeend', data.html);
            } else {
                incidentList.innerHTML = data.html;
            }

            if (window.lucide) {
                lucide.createIcons();
            }

            updateMapPreview(data.counts, data.pins);

            if (typeof data.next_offset === 'number') {
                window.feedNextOffset = data.next_offset;
            }

            var nextOffset = data.next_offset || 0;
            if (loadMoreWrap) {
                loadMoreWrap.hidden = !data.has_more;
                if (!data.has_more && !append) {
                    loadMoreWrap.innerHTML = '';
                } else if (!data.has_more && append) {
                    loadMoreWrap.innerHTML = '<div class="feed-end-message">You\'ve seen all nearby incidents</div>';
                }
            }
            if (loadMoreBtn) {
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Load more';
            }

            var feedPage = document.querySelector('[data-feed-page]');
            if (feedPage) {
                feedPage.setAttribute('data-next-offset', String(data.next_offset || 0));
                feedPage.setAttribute('data-filter', filter);
            }
        })
        .catch(function () {
            if (!append) {
                incidentList.innerHTML = getFeedErrorHtml('Network error. Check your connection and retry.');
            }
            if (loadMoreBtn) {
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Load more';
            }
        });
}

function getFeedErrorHtml(message) {
    return '<div class="page-error-state">' +
        '<svg class="state-illustration" viewBox="0 0 200 120" aria-hidden="true">' +
        '<circle cx="100" cy="60" r="36" fill="none" stroke="#E0765E" stroke-width="4"/>' +
        '<path d="M88 48 L112 72 M112 48 L88 72" stroke="#E0765E" stroke-width="4" stroke-linecap="round"/>' +
        '</svg>' +
        '<p class="state-title">Something went wrong</p>' +
        '<p class="text-sm text-muted">' + escapeHtml(message) + '</p>' +
        '<button type="button" class="btn-primary btn-sm" onclick="location.reload()">Retry</button>' +
        '</div>';
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

function getSkeletonHtml(count) {
    var html = '';
    for (var i = 0; i < count; i++) {
        html += '<article class="incident-card incident-skeleton card-bordered" aria-hidden="true">' +
            '<div class="accent skeleton-shimmer"></div>' +
            '<div class="card-body" style="flex:1;">' +
            '<div class="skeleton-line skeleton-shimmer" style="width:30%;height:20px;margin-bottom:12px;"></div>' +
            '<div class="skeleton-line skeleton-shimmer" style="width:85%;height:18px;margin-bottom:8px;"></div>' +
            '<div class="skeleton-line skeleton-shimmer" style="width:60%;height:14px;"></div>' +
            '</div></article>';
    }
    return html;
}

function updateMapPreview(counts, pins) {
    if (counts) {
        var map = {
            bites: '[data-count-bites]',
            strays: '[data-count-strays]',
            aggressive: '[data-count-aggressive]',
            vehicular: '[data-count-vehicular]'
        };
        Object.keys(map).forEach(function (key) {
            var el = document.querySelector(map[key]);
            if (el) {
                el.textContent = counts[key] || 0;
            }
        });
    }

    var preview = document.querySelector('[data-map-preview]');
    if (!preview || !pins) {
        return;
    }

    var baseHtml = '<div style="position:absolute;top:70px;left:-10px;right:-10px;height:12px;background:#fff;transform:rotate(-7deg);"></div>' +
        '<div style="position:absolute;inset:0;background:var(--tea-green);opacity:.15;"></div>';

    preview.innerHTML = baseHtml + pins.map(function (pin) {
        return '<div class="map-pin map-pin-drop ' + pin.accent + '" style="left:' + pin.left + 'px;top:' + pin.top + 'px;width:26px;height:26px;"></div>';
    }).join('');
}

function handleCorroborate(btn, csrfToken) {
    var incidentId = btn.getAttribute('data-corroborate');
    btn.disabled = true;

    fetch('ajax/corroborate.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({ incident_id: parseInt(incidentId, 10), csrf_token: csrfToken })
    })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                btn.outerHTML = '<div class="chip chip-outline corroborated is-corroborated">' +
                    '<i data-lucide="thumbs-up" style="width:14px;height:14px;color:var(--air-force);"></i> ' +
                    'Corroborate · ' + data.new_count + '</div>';
                if (window.lucide) {
                    lucide.createIcons();
                }
            } else {
                btn.disabled = false;
                showToast(data.message || 'Could not corroborate');
            }
        })
        .catch(function () {
            btn.disabled = false;
        });
}

function handleCaseStatusUpdate(select, csrfToken) {
    if (window.PawdarCaseStatus) {
        PawdarCaseStatus.handleStatusSelectChange(select);
        return;
    }

    var incidentId = select.getAttribute('data-case-status');
    var status = select.value;

    fetch('ajax/update_case.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({
            incident_id: parseInt(incidentId, 10),
            status: status,
            csrf_token: csrfToken
        })
    })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.success) {
                showToast(data.message || 'Update failed');
                return;
            }

            var badge = document.querySelector('[data-status-badge="' + incidentId + '"]');
            if (badge) {
                var dotOrCheck = data.status_label === 'Resolved'
                    ? '<i data-lucide="check" style="width:12px;height:12px;"></i>'
                    : '<span class="badge-dot" aria-hidden="true"></span>';
                badge.className = 'badge badge-with-dot ' + data.status_class;
                badge.innerHTML = dotOrCheck + data.status_label;
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
            showToast('Case status updated');
        });
}

function handleClaimStray(btn, csrfToken) {
    var incidentId = btn.getAttribute('data-claim-stray');
    btn.disabled = true;

    fetch('ajax/claim-stray.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({ incident_id: parseInt(incidentId, 10), csrf_token: csrfToken })
    })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                btn.textContent = 'Claimed';
                showToast('Stray case claimed');
            } else {
                btn.disabled = false;
                showToast(data.message || 'Could not claim case');
            }
        });
}

function toggleDetails(toggle) {
    var panel = toggle.nextElementSibling;
    if (!panel) {
        return;
    }

    var expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
    var label = toggle.querySelector('span');
    if (label) label.textContent = expanded ? 'More details' : 'Less details';
    panel.hidden = expanded;
}

function showToast(message) {
    if (window.PawdarUI && PawdarUI.showToast) {
        PawdarUI.showToast(message, 4000);
    }
}

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}
