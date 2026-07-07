document.addEventListener('DOMContentLoaded', function () {
    var mapEl = document.getElementById('pawdar-map');
    if (!mapEl || typeof L === 'undefined') return;

    var defaultCenter = [13.7568, 121.0583];
    var defaultZoom = 14;
    var map = L.map('pawdar-map').setView(defaultCenter, defaultZoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var cluster = L.markerClusterGroup();
    var heatLayer = null;
    var userMarker = null;
    var mode = 'normal';
    var incidents = window.pawdarMapSeed || [];
    var loadingEl = document.querySelector('[data-map-loading]');
    var emptyEl = document.querySelector('[data-map-empty]');
    var geoErrorEl = document.querySelector('[data-map-geo-error]');
    var listEl = document.getElementById('map-incident-list');
    var mapWrap = document.querySelector('[data-map-page]');
    var sidebarPanel = document.querySelector('[data-map-list-panel]');
    var sidebarToggleBtn = document.querySelector('[data-map-sidebar-toggle]');
    var sidebarToggleIcon = document.querySelector('[data-map-sidebar-toggle-icon]');
    var sidebarCountBadge = document.querySelector('[data-map-sidebar-count]');
    var sidebarStorageKey = 'pawdarMapSidebarOpen';
    var sidebarMedia = window.matchMedia('(min-width: 1024px)');

    function setSidebarOpen(isOpen) {
        if (!mapWrap) return;

        mapWrap.classList.toggle('map-sidebar-open', isOpen);
        mapWrap.classList.toggle('map-sidebar-collapsed', !isOpen);

        if (sidebarPanel) {
            sidebarPanel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        }
        if (sidebarToggleBtn) {
            sidebarToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            sidebarToggleBtn.setAttribute('aria-label', isOpen ? 'Hide incidents' : 'Show incidents');
        }
        if (sidebarToggleIcon) {
            sidebarToggleIcon.setAttribute('data-lucide', isOpen ? 'chevrons-right' : 'chevrons-left');
        }

        try {
            sessionStorage.setItem(sidebarStorageKey, isOpen ? '1' : '0');
        } catch (err) {
            /* ignore */
        }

        window.setTimeout(function () {
            map.invalidateSize();
        }, 220);

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    function initSidebarState() {
        var stored = null;
        try {
            stored = sessionStorage.getItem(sidebarStorageKey);
        } catch (err) {
            stored = null;
        }

        var defaultOpen = sidebarMedia.matches;
        var isOpen = stored === null ? defaultOpen : stored === '1';
        setSidebarOpen(isOpen);
    }

    function toggleSidebar() {
        var isOpen = mapWrap && mapWrap.classList.contains('map-sidebar-open');
        setSidebarOpen(!isOpen);
    }

    initSidebarState();

    sidebarToggleBtn?.addEventListener('click', toggleSidebar);

    initMapSecondaryControls();
    initMapFilterHint();

    if (typeof sidebarMedia.addEventListener === 'function') {
        sidebarMedia.addEventListener('change', function () {
            if (sessionStorage.getItem(sidebarStorageKey) === null) {
                setSidebarOpen(sidebarMedia.matches);
            }
        });
    }

    function setLoading(isLoading) {
        if (loadingEl) loadingEl.hidden = !isLoading;
    }

    function setEmpty(isEmpty) {
        if (emptyEl) emptyEl.hidden = !isEmpty;
    }

    function showGeoError(message) {
        if (!geoErrorEl) return;
        var text = geoErrorEl.querySelector('span');
        if (text && message) text.textContent = message;
        geoErrorEl.hidden = false;
        if (window.lucide) lucide.createIcons();
    }

    function hideGeoError() {
        if (geoErrorEl) geoErrorEl.hidden = true;
    }

    function renderList(data) {
        if (!listEl) return;

        if (!data.length) {
            listEl.innerHTML = '<div class="map-list-empty text-sm text-muted">No incidents match your filters.</div>';
            return;
        }

        listEl.innerHTML = data.map(function (item) {
            return '<a href="incident.php?id=' + item.IncidentID + '" class="card card-body card-bordered text-sm">' +
                '<div style="font-weight:700;">' + escapeHtml(generateTitle(item)) + '</div>' +
                '<div class="text-xs text-muted">' + escapeHtml(item.time_ago || '') + '</div>' +
                '</a>';
        }).join('');
    }

    function generateTitle(item) {
        return (item.IncidentType || 'Incident') + ' · ' + (item.Location || '');
    }

    function render(data) {
        incidents = data || [];
        cluster.clearLayers();
        if (heatLayer) {
            map.removeLayer(heatLayer);
            heatLayer = null;
        }

        var heatPoints = [];
        var plotted = 0;

        incidents.forEach(function (item) {
            var lat = parseFloat(item.latitude);
            var lng = parseFloat(item.longitude);
            if (!lat || !lng) return;
            plotted += 1;
            heatPoints.push([lat, lng, 0.6]);
            if (mode === 'normal') {
                var marker = L.circleMarker([lat, lng], {
                    radius: 10,
                    color: item.pin_color || '#E0765E',
                    fillColor: item.pin_color || '#E0765E',
                    fillOpacity: 0.85,
                    weight: 2
                });
                marker.bindPopup(
                    '<strong>' + escapeHtml(item.IncidentType) + '</strong><br>' +
                    escapeHtml(item.Location) + '<br>' +
                    '<a href="incident.php?id=' + item.IncidentID + '">View full report</a>'
                );
                cluster.addLayer(marker);
            }
        });

        if (mode === 'normal') {
            map.addLayer(cluster);
        } else if (typeof L.heatLayer === 'function' && heatPoints.length) {
            heatLayer = L.heatLayer(heatPoints, { radius: 28, blur: 18, maxZoom: 15 });
            map.addLayer(heatLayer);
        }

        var heading = document.querySelector('[data-map-count-heading]');
        if (heading) heading.textContent = incidents.length + ' incidents in this area';
        if (sidebarCountBadge) {
            sidebarCountBadge.textContent = String(incidents.length);
            sidebarCountBadge.hidden = incidents.length <= 0;
        }

        setEmpty(incidents.length === 0);
        renderList(incidents);

        if (plotted > 0 && incidents.length > 0) {
            var bounds = [];
            incidents.forEach(function (item) {
                var lat = parseFloat(item.latitude);
                var lng = parseFloat(item.longitude);
                if (lat && lng) bounds.push([lat, lng]);
            });
            if (bounds.length) {
                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
            }
        }
    }

    function loadMap() {
        setLoading(true);
        var filter = document.querySelector('.map-type-chip.chip-active')?.getAttribute('data-filter') || 'all';
        var range = document.getElementById('map-range')?.value || 'month';

        fetch('ajax/map_incidents.php?filter=' + encodeURIComponent(filter) + '&range=' + encodeURIComponent(range))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    render(data.incidents);
                } else {
                    showGeoError('Could not load incidents. Please try again.');
                }
            })
            .catch(function () {
                showGeoError('Network error while loading the map.');
            })
            .finally(function () {
                setLoading(false);
            });
    }

    function centerOnUser() {
        if (!navigator.geolocation) {
            showGeoError('Geolocation is not supported on this device.');
            return;
        }

        setLoading(true);
        navigator.geolocation.getCurrentPosition(function (pos) {
            hideGeoError();
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            map.setView([lat, lng], 15);

            if (userMarker) {
                map.removeLayer(userMarker);
            }

            userMarker = L.circleMarker([lat, lng], {
                radius: 8,
                color: '#fff',
                fillColor: '#6C8B9F',
                fillOpacity: 1,
                weight: 3
            }).addTo(map);
            userMarker.bindPopup('You are here');
            setLoading(false);
        }, function () {
            setLoading(false);
            showGeoError('Location unavailable. Showing default map area.');
        }, { enableHighAccuracy: true, timeout: 10000 });
    }

    var mapFilterHintKey = 'pawdarMapFilterHintSeen';

    function setActiveFilterChip(chip) {
        document.querySelectorAll('.map-type-chip').forEach(function (c) {
            var isActive = c.getAttribute('data-filter') === chip.getAttribute('data-filter');
            c.classList.toggle('chip-active', isActive);
            c.classList.toggle('chip-outline', !isActive);
            c.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        dismissMapFilterHint();

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    function initMapSecondaryControls() {
        var toggle = document.querySelector('[data-map-secondary-toggle]');
        var panel = document.querySelector('[data-map-secondary-panel]');
        if (!toggle || !panel) {
            return;
        }

        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            var isHidden = panel.hasAttribute('hidden');
            if (isHidden) {
                panel.removeAttribute('hidden');
                toggle.setAttribute('aria-expanded', 'true');
                toggle.setAttribute('aria-label', 'Hide time range and map view options');
            } else {
                panel.setAttribute('hidden', '');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Show time range and map view options');
            }

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    }

    function initMapFilterHint() {
        var hint = document.querySelector('[data-map-filter-hint]');
        if (!hint) {
            return;
        }

        try {
            if (sessionStorage.getItem(mapFilterHintKey) === '1') {
                return;
            }
        } catch (err) {
            /* ignore */
        }

        hint.hidden = false;
    }

    function dismissMapFilterHint() {
        var hint = document.querySelector('[data-map-filter-hint]');
        if (hint) {
            hint.hidden = true;
        }

        try {
            sessionStorage.setItem(mapFilterHintKey, '1');
        } catch (err) {
            /* ignore */
        }
    }

    document.querySelectorAll('.map-type-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            setActiveFilterChip(chip);
            loadMap();
        });
    });

    document.getElementById('map-range')?.addEventListener('change', loadMap);

    document.querySelectorAll('[data-map-mode]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            mode = btn.getAttribute('data-map-mode') || 'normal';
            document.querySelectorAll('[data-map-mode]').forEach(function (b) {
                b.classList.toggle('is-active', b === btn);
            });
            render(incidents);
        });
    });

    document.querySelector('[data-map-locate]')?.addEventListener('click', centerOnUser);
    document.querySelector('[data-map-geo-dismiss]')?.addEventListener('click', hideGeoError);

    render(incidents);
    setInterval(loadMap, 60000);
});

function escapeHtml(text) {
    var d = document.createElement('div');
    d.textContent = text || '';
    return d.innerHTML;
}
