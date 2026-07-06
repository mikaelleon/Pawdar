<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/incidents.php';

$userRole = current_user_role();
$pdo = db();
$barangay = (string) $_SESSION['user_barangay'];
$incidents = fetch_map_incidents($pdo, $barangay, null, date('Y-m-d 00:00:00', strtotime('-30 days')));

app_layout_start('map', 'Incident Map', [
    'showSearch' => false,
    'scripts' => ['assets/js/map.js'],
    'report_drawer' => true,
    'breadcrumbs' => [['label' => 'Map']],
]);
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">

<div class="map-full-wrap" data-map-page>
    <div id="pawdar-map" class="pawdar-leaflet-map"></div>

    <div class="map-state-overlay map-loading-state" data-map-loading hidden aria-live="polite">
        <div class="state-panel">
            <div class="state-spinner" aria-hidden="true"></div>
            <p class="state-title">Loading map…</p>
        </div>
    </div>

    <div class="map-state-overlay map-empty-state" data-map-empty hidden>
        <div class="state-panel">
            <svg class="state-illustration" viewBox="0 0 200 160" aria-hidden="true">
                <ellipse cx="100" cy="140" rx="70" ry="10" fill="#C0DAB5" opacity="0.5"/>
                <path d="M40 110 L100 50 L160 110 Z" fill="none" stroke="#6C8B9F" stroke-width="3"/>
                <circle cx="100" cy="95" r="18" fill="#87AFAE"/>
                <path d="M92 95 L98 101 L110 87" stroke="#fff" stroke-width="3" fill="none" stroke-linecap="round"/>
            </svg>
            <p class="state-title">No incidents in this area</p>
            <p class="text-sm text-muted">Try widening the date range or changing filters.</p>
        </div>
    </div>

    <div class="map-geo-banner" data-map-geo-error hidden role="alert">
        <i data-lucide="map-pin-off"></i>
        <span>Location unavailable. Showing default map area.</span>
        <button type="button" class="map-geo-dismiss" data-map-geo-dismiss aria-label="Dismiss">×</button>
    </div>

    <button type="button" class="map-locate-btn" data-map-locate aria-label="Center map on my location">
        <i data-lucide="crosshair"></i>
        <span class="hidden-mobile">My location</span>
    </button>

    <div class="map-overlay-top hidden-mobile">
        <div class="card card-body map-toolbar">
            <div class="search-bar search-bar-light flex-1">
                <i data-lucide="search"></i>
                <input type="search" id="map-search" placeholder="Search by barangay, location…">
            </div>
            <div class="chips-row" data-map-type-chips>
                <button type="button" class="chip chip-active map-type-chip" data-filter="all">All</button>
                <button type="button" class="chip chip-outline map-type-chip" data-filter="animal_bite">Bite</button>
                <button type="button" class="chip chip-outline map-type-chip" data-filter="injured_stray">Injured</button>
                <button type="button" class="chip chip-outline map-type-chip" data-filter="aggressive">Aggressive</button>
                <button type="button" class="chip chip-outline map-type-chip" data-filter="vehicular">Vehicular</button>
                <button type="button" class="chip chip-outline map-type-chip" data-filter="disturbance">Disturbance</button>
            </div>
            <select id="map-range" class="registry-filter">
                <option value="today">Today</option>
                <option value="week">This week</option>
                <option value="month" selected>This month</option>
            </select>
            <div class="map-mode-toggle">
                <button type="button" class="is-active" data-map-mode="normal">Normal</button>
                <button type="button" data-map-mode="heatmap">Heatmap</button>
            </div>
        </div>
    </div>

    <aside class="map-side-panel hidden-mobile" data-map-list-panel>
        <div style="padding:20px 18px 14px;">
            <div style="font-weight:800;font-size:18px;" data-map-count-heading><?= count($incidents) ?> incidents in this area</div>
            <div class="text-sm text-muted">Last 30 days</div>
        </div>
        <div class="flex flex-col gap-md scr" style="flex:1;overflow-y:auto;padding:4px 18px 18px;" id="map-incident-list">
            <?php foreach ($incidents as $inc): ?>
                <a href="incident.php?id=<?= (int) $inc['IncidentID'] ?>" class="card card-body card-bordered text-sm">
                    <div style="font-weight:700;"><?= htmlspecialchars(generate_incident_title((string) $inc['IncidentType'], (string) $inc['Location'])) ?></div>
                    <div class="text-xs text-muted"><?= htmlspecialchars(time_elapsed_string((string) $inc['Date'])) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </aside>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script>window.pawdarMapSeed = <?= json_encode($incidents) ?>;</script>

<?php
$mapFab = role_can_report($userRole)
    ? ['show' => true, 'label' => 'Report', 'opensDrawer' => true]
    : ['show' => false];
app_layout_end($mapFab);
?>
