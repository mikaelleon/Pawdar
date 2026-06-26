<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/incidents.php';
require __DIR__ . '/partials/incident-cards.php';

$userRole = current_user_role();
$userId = (int) $_SESSION['user_id'];
$barangay = (string) $_SESSION['user_barangay'];
$filter = trim((string) ($_GET['filter'] ?? 'all'));
$incidentType = $filter === 'all' ? null : filter_to_incident_type($filter);
if ($filter !== 'all' && $incidentType === null) {
    $filter = 'all';
    $incidentType = null;
}

$pdo = db();
$incidents = fetch_incidents($pdo, $barangay, $userId, $incidentType, 0, 10);
$mapCounts = fetch_map_counts($pdo, $barangay);
$mapPins = fetch_map_pins($pdo, $barangay, $incidentType);

app_layout_start('feed', 'Home Feed', ['scripts' => ['assets/js/feed.js']]);

$typeMap = incident_type_map();
$chips = [['slug' => 'all', 'label' => 'All']];
foreach ($typeMap as $meta) {
    $chips[] = ['slug' => $meta['filter'], 'label' => $meta['label']];
}

$fabOptions = ['show' => false];
if (role_can_report($userRole)) {
    $fabOptions = ['show' => true, 'label' => 'Report', 'opensDrawer' => true];
} elseif ($userRole === 'LGU Official' || $userRole === 'Admin') {
    $fabOptions = ['show' => true, 'label' => 'Cases', 'href' => 'cases.php'];
} elseif ($userRole === 'Veterinarian') {
    $fabOptions = ['show' => true, 'label' => 'Verify', 'href' => 'dog-profile.php'];
} elseif ($userRole === 'Rescue Organization') {
    $fabOptions = ['show' => true, 'label' => 'Rescue', 'href' => 'rescue-board.php'];
}
?>

<div class="feed-layout" data-feed-page data-csrf="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" data-filter="<?= htmlspecialchars($filter) ?>" data-barangay="<?= htmlspecialchars($barangay) ?>" data-next-offset="<?= count($incidents) ?>">
    <div class="feed-column">
        <div class="feed-header">
            <div>
                <h1 class="feed-title">Nearby Incidents</h1>
                <p class="text-sm text-muted">Within 5 km · Brgy. <?= htmlspecialchars($barangay) ?></p>
            </div>
            <div class="feed-header-actions hidden-mobile">
                <?php if (role_can_report($userRole)): ?>
                    <button type="button" class="btn-primary" style="height:44px;padding:0 20px;font-size:14px;" data-open-report-drawer>
                        <i data-lucide="plus"></i> Report Incident
                    </button>
                <?php elseif ($userRole === 'LGU Official' || $userRole === 'Admin'): ?>
                    <a href="cases.php" class="btn-primary" style="height:44px;padding:0 20px;font-size:14px;">
                        <i data-lucide="folder-check"></i> Manage Cases
                    </a>
                <?php elseif ($userRole === 'Veterinarian'): ?>
                    <a href="dog-profile.php" class="btn-primary" style="height:44px;padding:0 20px;font-size:14px;">
                        <i data-lucide="shield-check"></i> Verify Records
                    </a>
                <?php elseif ($userRole === 'Rescue Organization'): ?>
                    <a href="rescue-board.php" class="btn-primary" style="height:44px;padding:0 20px;font-size:14px;">
                        <i data-lucide="life-buoy"></i> Rescue Board
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="chips-row scr" data-filter-chips role="tablist" aria-label="Filter incidents">
            <?php foreach ($chips as $chip): ?>
                <button type="button"
                        class="chip filter-chip<?= $filter === $chip['slug'] ? ' chip-active' : ' chip-outline' ?>"
                        data-filter="<?= htmlspecialchars($chip['slug']) ?>"
                        role="tab"
                        aria-selected="<?= $filter === $chip['slug'] ? 'true' : 'false' ?>">
                    <?= htmlspecialchars($chip['label']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="flex flex-col gap-md" data-incident-list>
            <?php render_incident_cards($incidents, $userRole, $userId); ?>
        </div>

        <div class="feed-load-more-wrap" data-load-more-wrap<?= count($incidents) < 10 ? ' hidden' : '' ?>>
            <button type="button" class="btn-outline btn-block btn-sm feed-load-more" data-load-more>
                Load more
            </button>
        </div>
    </div>

    <aside class="map-preview-col hidden-mobile">
        <div style="font-weight:800;font-size:15px;color:var(--air-force);">Map preview</div>
        <div class="map-preview" data-map-preview>
            <div style="position:absolute;top:70px;left:-10px;right:-10px;height:12px;background:#fff;transform:rotate(-7deg);"></div>
            <div style="position:absolute;inset:0;background:var(--tea-green);opacity:.15;"></div>
            <?php foreach ($mapPins as $pin): ?>
                <div class="map-pin map-pin-drop <?= htmlspecialchars($pin['accent']) ?>"
                     style="left:<?= (int) $pin['left'] ?>px;top:<?= (int) $pin['top'] ?>px;width:26px;height:26px;"></div>
            <?php endforeach; ?>
        </div>
        <div class="grid-2x2" data-map-counts>
            <div class="summary-card" style="padding:12px;border-top-color:var(--burnt-peach);">
                <div class="summary-value" style="font-size:22px;" data-count-bites><?= (int) $mapCounts['bites'] ?></div>
                <div class="summary-label" style="font-size:11px;">Bites</div>
            </div>
            <div class="summary-card" style="padding:12px;border-top-color:var(--sunlit-clay);">
                <div class="summary-value" style="font-size:22px;" data-count-strays><?= (int) $mapCounts['strays'] ?></div>
                <div class="summary-label" style="font-size:11px;">Strays</div>
            </div>
            <div class="summary-card" style="padding:12px;border-top-color:var(--air-force);">
                <div class="summary-value" style="font-size:22px;" data-count-aggressive><?= (int) $mapCounts['aggressive'] ?></div>
                <div class="summary-label" style="font-size:11px;">Aggressive</div>
            </div>
            <div class="summary-card" style="padding:12px;">
                <div class="summary-value" style="font-size:22px;" data-count-vehicular><?= (int) $mapCounts['vehicular'] ?></div>
                <div class="summary-label" style="font-size:11px;">Vehicular</div>
            </div>
        </div>
        <a href="map.php?barangay=<?= urlencode($barangay) ?>" class="btn-outline btn-block btn-sm" style="height:42px;">
            <i data-lucide="map"></i> Open full map
        </a>
    </aside>
</div>

<?php require __DIR__ . '/partials/report-drawer.php'; ?>
<div class="toast-container" data-toast-container aria-live="polite"></div>

<?php app_layout_end($fabOptions); ?>
