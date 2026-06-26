document.addEventListener('DOMContentLoaded', function () {
    var mapEl = document.getElementById('pawdar-map');
    if (!mapEl || typeof L === 'undefined') return;

    var map = L.map('pawdar-map').setView([13.7568, 121.0583], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var cluster = L.markerClusterGroup();
    var heatLayer = null;
    var mode = 'normal';
    var incidents = window.pawdarMapSeed || [];

    function render(data) {
        incidents = data || [];
        cluster.clearLayers();
        if (heatLayer) {
            map.removeLayer(heatLayer);
            heatLayer = null;
        }

        var heatPoints = [];
        incidents.forEach(function (item) {
            var lat = parseFloat(item.latitude);
            var lng = parseFloat(item.longitude);
            if (!lat || !lng) return;
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
    }

    function loadMap() {
        var filter = document.querySelector('.map-type-chip.chip-active')?.getAttribute('data-filter') || 'all';
        var range = document.getElementById('map-range')?.value || 'month';
        fetch('ajax/map_incidents.php?filter=' + encodeURIComponent(filter) + '&range=' + encodeURIComponent(range))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) render(data.incidents);
            });
    }

    document.querySelectorAll('.map-type-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.map-type-chip').forEach(function (c) {
                c.classList.toggle('chip-active', c === chip);
                c.classList.toggle('chip-outline', c !== chip);
            });
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

    render(incidents);
    setInterval(loadMap, 60000);
});

function escapeHtml(text) {
    var d = document.createElement('div');
    d.textContent = text || '';
    return d.innerHTML;
}
