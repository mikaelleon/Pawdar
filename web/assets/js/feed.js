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

    initReportDrawer(csrfToken, function () {
        nextOffset = 0;
        fetchFeed(currentFilter, 0, false);
        showToast('Incident reported successfully');
    });

    document.querySelectorAll('[data-open-report-drawer]').forEach(function (btn) {
        btn.addEventListener('click', openReportDrawer);
    });
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

            feedPage.setAttribute('data-next-offset', String(data.next_offset || 0));
            feedPage.setAttribute('data-filter', filter);
        });
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
    var incidentId = select.getAttribute('data-case-status');
    var status = select.value;

    fetch('ajax/update-case-status.php', {
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

function initReportDrawer(csrfToken, onSuccess) {
    var drawer = document.querySelector('[data-report-drawer]');
    var overlay = document.querySelector('[data-report-drawer-overlay]');
    var form = document.querySelector('[data-report-form]');
    if (!drawer || !overlay || !form) {
        return;
    }

    var currentStep = 1;
    var submitBtn = form.querySelector('[data-report-submit]');

    document.querySelectorAll('[data-close-report-drawer], [data-report-drawer-overlay]').forEach(function (el) {
        el.addEventListener('click', closeReportDrawer);
    });

    document.querySelectorAll('[data-report-next]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (currentStep === 2) {
                var location = form.querySelector('[name="location"]');
                if (!location.value.trim()) {
                    location.focus();
                    return;
                }
            }
            showReportStep(currentStep + 1);
        });
    });

    document.querySelectorAll('[data-report-back]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            showReportStep(currentStep - 1);
        });
    });

    var geoBtn = form.querySelector('[data-use-location]');
    if (geoBtn) {
        geoBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                showToast('Geolocation not supported');
                return;
            }
            geoBtn.disabled = true;
            navigator.geolocation.getCurrentPosition(function (pos) {
                form.querySelector('#report-location').value =
                    pos.coords.latitude.toFixed(5) + ', ' + pos.coords.longitude.toFixed(5);
                geoBtn.disabled = false;
            }, function () {
                showToast('Location permission denied');
                geoBtn.disabled = false;
            });
        });
    }

    var desc = form.querySelector('#report-description');
    var charCount = form.querySelector('[data-char-count]');
    if (desc && charCount) {
        desc.addEventListener('input', function () {
            var len = desc.value.length;
            charCount.textContent = len + ' / 280';
            charCount.style.color = len >= 280 ? '#E24B4A' : (len >= 250 ? '#F8BC72' : '');
        });
    }

    var dogSearch = form.querySelector('#report-dog-search');
    var dogResults = form.querySelector('[data-dog-search-results]');
    var dogTimer;
    if (dogSearch && dogResults) {
        dogSearch.addEventListener('input', function () {
            clearTimeout(dogTimer);
            dogTimer = setTimeout(function () {
                fetch('ajax/search_dogs.php?q=' + encodeURIComponent(dogSearch.value))
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (!data.dogs || !data.dogs.length) {
                            dogResults.hidden = true;
                            return;
                        }
                        dogResults.hidden = false;
                        dogResults.innerHTML = data.dogs.map(function (dog) {
                            return '<button type="button" class="dog-search-item" data-dog-id="' + dog.id + '" data-dog-name="' + dog.name + '">' + dog.name + ' · ' + (dog.breed || '') + '</button>';
                        }).join('');
                    });
            }, 300);
        });
        dogResults.addEventListener('click', function (e) {
            var item = e.target.closest('[data-dog-id]');
            if (!item) return;
            form.querySelector('#report-dog-id').value = item.getAttribute('data-dog-id');
            dogSearch.value = item.getAttribute('data-dog-name');
            dogResults.hidden = true;
        });
    }

    var dropzone = form.querySelector('[data-photo-dropzone]');
    var photoInput = form.querySelector('#report-photo');
    var preview = form.querySelector('[data-photo-preview]');
    if (dropzone && photoInput) {
        dropzone.addEventListener('click', function () { photoInput.click(); });
        dropzone.addEventListener('dragover', function (e) { e.preventDefault(); dropzone.classList.add('is-dragover'); });
        dropzone.addEventListener('dragleave', function () { dropzone.classList.remove('is-dragover'); });
        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropzone.classList.remove('is-dragover');
            if (e.dataTransfer.files[0]) setPhotoFile(e.dataTransfer.files[0]);
        });
        photoInput.addEventListener('change', function () {
            if (photoInput.files[0]) setPhotoFile(photoInput.files[0]);
        });
    }

    function setPhotoFile(file) {
        if (file.size > 5 * 1024 * 1024) {
            showToast('Photo must be 5MB or less');
            return;
        }
        if (!/^image\/(jpeg|png)$/.test(file.type)) {
            showToast('Use JPG or PNG only');
            return;
        }
        var reader = new FileReader();
        reader.onload = function () {
            preview.src = reader.result;
            preview.hidden = false;
        };
        reader.readAsDataURL(file);
        var dt = new DataTransfer();
        dt.items.add(file);
        photoInput.files = dt.files;
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        PawdarUI.setButtonLoading(submitBtn, true);
        var formData = new FormData(form);
        formData.set('csrf_token', csrfToken);

        fetch('ajax/submit-report.php', {
            method: 'POST',
            headers: { 'X-CSRF-Token': csrfToken },
            body: formData
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                PawdarUI.setButtonLoading(submitBtn, false);
                if (data.success) {
                    closeReportDrawer();
                    form.reset();
                    if (preview) preview.hidden = true;
                    showReportStep(1);
                    showToast('Your report has been submitted');
                    onSuccess();
                } else {
                    showToast(data.message || 'Failed to submit report');
                }
            })
            .catch(function () {
                PawdarUI.setButtonLoading(submitBtn, false);
            });
    });

    function showReportStep(step) {
        currentStep = Math.max(1, Math.min(3, step));
        form.querySelectorAll('[data-report-step]').forEach(function (panel) {
            panel.hidden = parseInt(panel.getAttribute('data-report-step'), 10) !== currentStep;
        });
        document.querySelectorAll('[data-step-indicator]').forEach(function (dot) {
            var n = parseInt(dot.getAttribute('data-step-indicator'), 10);
            dot.classList.remove('is-active', 'is-done');
            dot.textContent = n;
            if (n < currentStep) {
                dot.classList.add('is-done');
                dot.innerHTML = '<i data-lucide="check" style="width:14px;height:14px;"></i>';
            } else if (n === currentStep) {
                dot.classList.add('is-active');
            }
        });
        document.querySelectorAll('[data-progress-line]').forEach(function (line, i) {
            line.classList.toggle('is-done', (i + 1) < currentStep);
        });
        if (window.lucide) lucide.createIcons();
    }
}

window.openReportDrawer = openReportDrawer;

function openReportDrawer() {
    var drawer = document.querySelector('[data-report-drawer]');
    var overlay = document.querySelector('[data-report-drawer-overlay]');
    if (!drawer || !overlay) {
        return;
    }
    drawer.removeAttribute('hidden');
    drawer.setAttribute('aria-hidden', 'false');
    overlay.removeAttribute('hidden');
    document.body.classList.add('drawer-open');
}

function closeReportDrawer() {
    var drawer = document.querySelector('[data-report-drawer]');
    var overlay = document.querySelector('[data-report-drawer-overlay]');
    if (!drawer || !overlay) {
        return;
    }
    drawer.setAttribute('hidden', '');
    drawer.setAttribute('aria-hidden', 'true');
    overlay.setAttribute('hidden', '');
    document.body.classList.remove('drawer-open');
}

function showToast(message) {
    if (window.PawdarUI && PawdarUI.showToast) {
        PawdarUI.showToast(message, 4000);
        return;
    }
}

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}
