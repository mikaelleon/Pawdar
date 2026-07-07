<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/incidents.php';
require __DIR__ . '/partials/incident-cards.php';

$userRole = current_user_role();
$userId = (int) $_SESSION['user_id'];
$barangay = (string) $_SESSION['user_barangay'];
$filter = trim((string) ($_GET['filter'] ?? 'all'));
$searchQuery = trim((string) ($_GET['q'] ?? ''));
$incidentType = $filter === 'all' ? null : filter_to_incident_type($filter);
if ($filter !== 'all' && $incidentType === null) {
    $filter = 'all';
    $incidentType = null;
}

$pdo = db();
$incidents = fetch_incidents(
    $pdo,
    $barangay,
    $userId,
    $incidentType,
    0,
    10,
    $searchQuery !== '' ? $searchQuery : null
);
$mapCounts = fetch_map_counts($pdo, $barangay);
$mapPins = fetch_map_pins($pdo, $barangay, $incidentType);

app_layout_start('feed', 'Home Feed', [
    'scripts' => ['assets/js/feed.js'],
    'report_drawer' => true,
    'showSearch' => false,
    'showMobileSearch' => false,
    'breadcrumbs' => [['label' => 'Feed']],
]);

$typeMap = incident_type_map();
$chips = [['slug' => 'all', 'label' => 'All', 'icon' => 'layout-grid']];
foreach ($typeMap as $meta) {
    $chips[] = ['slug' => $meta['filter'], 'label' => $meta['label'], 'icon' => $meta['icon']];
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

<div class="feed-grid" data-feed-page data-csrf="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" data-filter="<?= htmlspecialchars($filter) ?>" data-search="<?= htmlspecialchars($searchQuery) ?>" data-barangay="<?= htmlspecialchars($barangay) ?>" data-next-offset="<?= count($incidents) ?>">
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

        <div class="feed-toolbar card card-bordered">
            <div class="search-bar search-bar-light feed-toolbar-search">
                <i data-lucide="search"></i>
                <input type="search"
                       id="feed-search"
                       value="<?= htmlspecialchars($searchQuery) ?>"
                       placeholder="Search incidents or dogs…"
                       aria-label="Search incidents or dogs">
            </div>
            <div class="feed-filter-chips-wrap">
                <div class="chips-row scr feed-filter-chips" data-filter-chips role="tablist" aria-label="Filter incidents">
                    <?php foreach ($chips as $chip):
                        $isActive = $filter === $chip['slug'];
                        $chipClasses = 'chip filter-chip feed-type-chip';
                        $chipClasses .= $isActive ? ' chip-active feed-type-chip--full' : ' chip-outline feed-type-chip--icon';
                        ?>
                        <button type="button"
                                class="<?= $chipClasses ?>"
                                data-filter="<?= htmlspecialchars($chip['slug']) ?>"
                                data-label="<?= htmlspecialchars($chip['label']) ?>"
                                data-icon="<?= htmlspecialchars($chip['icon']) ?>"
                                role="tab"
                                aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                                aria-label="<?= htmlspecialchars($chip['label']) ?>"
                                title="<?= htmlspecialchars($chip['label']) ?>">
                            <i data-lucide="<?= htmlspecialchars($chip['icon']) ?>" aria-hidden="true"></i>
                            <span class="feed-chip-label"><?= htmlspecialchars($chip['label']) ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
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

    <aside class="bento-column bento-column--feed sidebar-scroll">
        <?php require __DIR__ . '/partials/widget_my_reports.php'; ?>
        <?php require __DIR__ . '/partials/widget_firstaid.php'; ?>
        <?php require __DIR__ . '/partials/widget_map.php'; ?>
        <?php require __DIR__ . '/partials/widget_funfact.php'; ?>
    </aside>
</div>

<?php app_layout_end($fabOptions); ?>
