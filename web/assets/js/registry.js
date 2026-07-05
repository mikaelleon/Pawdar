document.addEventListener('DOMContentLoaded', function () {
    var grid = document.querySelector('[data-registry-grid]');
    var search = document.getElementById('registry-search');
    var loadBtn = document.querySelector('[data-registry-load-more]');
    var loadWrap = document.querySelector('[data-registry-load-wrap]');
    var countEl = document.querySelector('[data-registry-count]');
    var resultsLabel = document.querySelector('.registry-results-label');
    var offset = grid ? grid.querySelectorAll('.dog-card').length : 0;
    var timer;
    var viewStorageKey = 'pawdar-registry-view';

    function filters() {
        var activeType = document.querySelector('.registry-type-chip.chip-active');
        return {
            q: search ? search.value.trim() : '',
            type: activeType ? activeType.getAttribute('data-type') : 'all',
            barangay: val('barangay'),
            breed: val('breed'),
            vaccine: val('vaccine')
        };
    }

    function val(name) {
        var el = document.querySelector('.registry-filter[data-filter="' + name + '"]');
        return el ? el.value : 'all';
    }

    function applyView(view) {
        if (!grid) return;
        grid.setAttribute('data-registry-view', view);
        localStorage.setItem(viewStorageKey, view);
        document.querySelectorAll('[data-registry-view-btn]').forEach(function (btn) {
            var active = btn.getAttribute('data-registry-view') === view;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    }

    function fetchDogs(reset) {
        if (!grid) return;
        if (reset) offset = 0;

        if (reset) {
            grid.innerHTML = getRegistrySkeletonHtml(6);
        }

        var params = new URLSearchParams(filters());
        params.set('offset', String(offset));

        fetch('ajax/registry_dogs.php?' + params.toString())
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.success) {
                    if (reset) {
                        grid.innerHTML = getRegistryErrorHtml('Could not load registry results.');
                    }
                    return;
                }
                if (reset) {
                    grid.innerHTML = data.html || getRegistryEmptyHtml();
                } else if (data.html) {
                    grid.insertAdjacentHTML('beforeend', data.html);
                }
                offset = grid.querySelectorAll('.dog-card').length;
                if (loadWrap) loadWrap.hidden = !data.has_more;
                if (countEl) countEl.textContent = offset + ' of ' + data.total + ' dogs';
                if (resultsLabel) resultsLabel.textContent = 'Showing ' + offset + ' of ' + data.total + ' dogs';
                if (window.lucide) lucide.createIcons();
            })
            .catch(function () {
                if (reset) {
                    grid.innerHTML = getRegistryErrorHtml('Network error. Check your connection and retry.');
                }
            });
    }

    function getRegistryEmptyHtml() {
        return '<div class="registry-empty"><p class="empty-title">No dogs found</p><p class="empty-subtitle">Try a different filter or search term.</p></div>';
    }

    function getRegistryErrorHtml(message) {
        return '<div class="registry-empty page-error-state">' +
            '<p class="empty-title">Something went wrong</p>' +
            '<p class="empty-subtitle">' + message + '</p>' +
            '<button type="button" class="btn-primary btn-sm" onclick="location.reload()">Retry</button></div>';
    }

    function getRegistrySkeletonHtml(count) {
        var html = '';
        for (var i = 0; i < count; i++) {
            html += '<div class="dog-card dog-card-skeleton card-bordered" aria-hidden="true">' +
                '<div class="skeleton-line skeleton-shimmer" style="height:120px;border-radius:12px;margin-bottom:12px;"></div>' +
                '<div class="skeleton-line skeleton-shimmer" style="width:70%;height:16px;margin-bottom:8px;"></div>' +
                '<div class="skeleton-line skeleton-shimmer" style="width:45%;height:12px;"></div></div>';
        }
        return html;
    }

    applyView(localStorage.getItem(viewStorageKey) || 'tiles');

    document.querySelectorAll('[data-registry-view-btn]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            applyView(btn.getAttribute('data-registry-view') || 'tiles');
        });
    });

    if (search) {
        search.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { fetchDogs(true); }, 300);
        });
    }

    document.querySelectorAll('.registry-type-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.registry-type-chip').forEach(function (c) {
                c.classList.toggle('chip-active', c === chip);
                c.classList.toggle('chip-outline', c !== chip);
            });
            fetchDogs(true);
        });
    });

    document.querySelectorAll('.registry-filter').forEach(function (sel) {
        sel.addEventListener('change', function () { fetchDogs(true); });
    });

    if (loadBtn) {
        loadBtn.addEventListener('click', function () { fetchDogs(false); });
    }
});
