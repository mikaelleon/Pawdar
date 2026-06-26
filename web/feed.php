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

app_layout_start('feed', 'Home Feed', [
    'scripts' => ['assets/js/feed.js'],
    'report_drawer' => true,
]);

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

<div class="feed-grid" data-feed-page data-csrf="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" data-filter="<?= htmlspecialchars($filter) ?>" data-barangay="<?= htmlspecialchars($barangay) ?>" data-next-offset="<?= count($incidents) ?>">
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

    <aside class="bento-column hidden-mobile">
        <?php require __DIR__ . '/partials/widget_funfact.php'; ?>
        <?php require __DIR__ . '/partials/widget_firstaid.php'; ?>
        <?php require __DIR__ . '/partials/widget_map.php'; ?>
    </aside>
</div>

<?php app_layout_end($fabOptions); ?>
