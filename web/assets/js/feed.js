document.addEventListener('DOMContentLoaded', function () {
    var feedPage = document.querySelector('[data-feed-page]');
    if (!feedPage) {
        return;
    }

    var csrfToken = feedPage.getAttribute('data-csrf') || getCsrfToken();
    var currentFilter = feedPage.getAttribute('data-filter') || 'all';
    var currentSearch = feedPage.getAttribute('data-search') || '';
    var nextOffset = document.querySelectorAll('[data-incident-list] .incident-card:not(.incident-skeleton)').length;
    var incidentList = document.querySelector('[data-incident-list]');
    var loadMoreWrap = document.querySelector('[data-load-more-wrap]');
    var loadMoreBtn = document.querySelector('[data-load-more]');
    var searchInput = document.getElementById('feed-search');
    var searchDebounce = null;

    document.querySelectorAll('[data-filter-chips] .filter-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var filter = chip.getAttribute('data-filter');
            if (filter === currentFilter) {
                return;
            }

            currentFilter = filter;
            nextOffset = 0;
            updateFilterChips(filter);
            updateUrlState(filter, currentSearch);
            fetchFeed(filter, 0, false, currentSearch);
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentSearch = searchInput.value.trim();
            nextOffset = 0;
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(function () {
                updateUrlState(currentFilter, currentSearch);
                fetchFeed(currentFilter, 0, false, currentSearch);
            }, 300);
        });
    }

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            var offset = parseInt(feedPage.getAttribute('data-next-offset') || String(nextOffset), 10);
            fetchFeed(currentFilter, offset, true, currentSearch);
        });
    }

    if (incidentList) {
        incidentList.addEventListener('click', function (event) {
            if (event.target.closest('[data-corroborate]')) {
                return;
            }

            var claimBtn = event.target.closest('[data-claim-stray]');
            if (claimBtn) {
                handleClaimStray(claimBtn, csrfToken);
            }
        });
    }

    if (typeof window.initReportDrawer === 'function') {
        window.initReportDrawer(csrfToken, function () {
            nextOffset = 0;
            fetchFeed(currentFilter, 0, false, currentSearch);
            showToast('Incident reported successfully');
            window.location.reload();
        });
    }
});

function updateFilterChips(activeFilter) {
    document.querySelectorAll('[data-filter-chips] .filter-chip').forEach(function (chip) {
        var isActive = chip.getAttribute('data-filter') === activeFilter;
        chip.classList.toggle('chip-active', isActive);
        chip.classList.toggle('chip-outline', !isActive);
        chip.classList.toggle('feed-type-chip--full', isActive);
        chip.classList.toggle('feed-type-chip--icon', !isActive);
        chip.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
}

function updateUrlState(filter, search) {
    var url = new URL(window.location.href);
    if (filter === 'all') {
        url.searchParams.delete('filter');
    } else {
        url.searchParams.set('filter', filter);
    }

    if (search) {
        url.searchParams.set('q', search);
    } else {
        url.searchParams.delete('q');
    }

    window.history.replaceState({}, '', url.toString());

    var feedPage = document.querySelector('[data-feed-page]');
    if (feedPage) {
        feedPage.setAttribute('data-filter', filter);
        feedPage.setAttribute('data-search', search);
    }
}

function fetchFeed(filter, offset, append, search) {
    var incidentList = document.querySelector('[data-incident-list]');
    var loadMoreWrap = document.querySelector('[data-load-more-wrap]');
    var loadMoreBtn = document.querySelector('[data-load-more]');
    var query = 'ajax/feed-filter.php?filter=' + encodeURIComponent(filter) +
        '&offset=' + offset +
        '&limit=10';

    if (search) {
        query += '&q=' + encodeURIComponent(search);
    }

    if (!append) {
        incidentList.innerHTML = getSkeletonHtml(3);
    } else if (loadMoreBtn) {
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Loading…';
    }

    fetch(query)
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

            if (window.PawdarCaseStatus && typeof window.PawdarCaseStatus.bindSelects === 'function') {
                PawdarCaseStatus.bindSelects(incidentList);
            }

            updateMapPreview(data.counts);

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
        html += '<div class="feed-incident-card-wrap" aria-hidden="true">' +
            '<article class="incident-card feed-incident-card incident-skeleton card-bordered">' +
            '<div class="card-body feed-incident-card-body">' +
            '<div class="feed-incident-header">' +
            '<div class="feed-incident-icon skeleton-shimmer"></div>' +
            '<div class="feed-incident-meta">' +
            '<div class="skeleton-line skeleton-shimmer" style="width:45%;height:18px;margin-bottom:8px;"></div>' +
            '<div class="skeleton-line skeleton-shimmer" style="width:70%;height:14px;"></div>' +
            '</div>' +
            '<div class="skeleton-line skeleton-shimmer" style="width:76px;height:24px;border-radius:8px;"></div>' +
            '</div>' +
            '<div class="feed-incident-media incident-card-tiles feed-incident-media--no-photo">' +
            '<div class="feed-incident-media-tile incident-card-tile-photo skeleton-shimmer"></div>' +
            '<div class="feed-incident-media-tile incident-card-tile-map skeleton-shimmer"></div>' +
            '</div>' +
            '<div class="skeleton-line skeleton-shimmer" style="width:100%;height:36px;margin-top:16px;"></div>' +
            '<div class="feed-incident-open skeleton-shimmer"></div>' +
            '</div></article></div>';
    }
    return html;
}

function updateMapPreview(counts) {
    if (!counts) {
        return;
    }

    ['bites', 'strays', 'aggressive', 'vehicular', 'disturbance'].forEach(function (key) {
        var el = document.querySelector('[data-count-' + key + ']');
        if (el) {
            el.textContent = counts[key] || 0;
        }
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

function showToast(message) {
    if (window.PawdarUI && PawdarUI.showToast) {
        PawdarUI.showToast(message, 4000);
    }
}

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}
