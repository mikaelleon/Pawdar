<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/dogs.php';
require_once __DIR__ . '/includes/breeds.php';

$pdo = db();
$userRole = current_user_role();
$canRegister = in_array($userRole, ['Dog Owner', 'Admin'], true);

$filters = [
    'q' => trim((string) ($_GET['q'] ?? '')),
    'type' => trim((string) ($_GET['type'] ?? 'all')),
    'barangay' => trim((string) ($_GET['barangay'] ?? 'all')),
    'breed' => trim((string) ($_GET['breed'] ?? 'all')),
    'vaccine' => trim((string) ($_GET['vaccine'] ?? 'all')),
];
$result = fetch_registry_list($pdo, $filters, 0, 20);
$summary = fetch_registry_summary($pdo);
$barangays = fetch_registry_barangays($pdo);
$breedOptions = fetch_all_breeds($pdo);

app_layout_start('registry', 'Dog Registry', [
    'showSearch' => false,
    'breadcrumbs' => [['label' => 'Registry']],
    'scripts' => ['assets/js/registry.js'],
]);
?>

<div class="page-title-row">
    <div>
        <h1>Dog Registry</h1>
        <p class="page-subtitle">Browse, search, and filter registered dogs in your community.</p>
    </div>
    <?php if ($canRegister): ?>
        <a href="register_dog.php" class="btn-primary hidden-mobile"><i data-lucide="plus"></i> Register Dog</a>
    <?php endif; ?>
</div>

<div class="registry-summary-strip">
    <div class="summary-card">
        <span class="summary-number"><?= (int) $summary['total'] ?></span>
        <span class="summary-label">Total Dogs</span>
    </div>
    <div class="summary-card summary-card--owned">
        <span class="summary-number"><?= (int) $summary['owned'] ?></span>
        <span class="summary-label">Owned</span>
    </div>
    <div class="summary-card summary-card--stray">
        <span class="summary-number"><?= (int) $summary['stray'] ?></span>
        <span class="summary-label">Strays</span>
    </div>
    <div class="summary-card summary-card--vax">
        <span class="summary-number"><?= (int) $summary['vax_verified'] ?></span>
        <span class="summary-label">Vaccination Verified</span>
    </div>
</div>

<div class="registry-search-bar search-bar search-bar-light" data-registry-search-wrap>
    <i data-lucide="search"></i>
    <input type="search" id="registry-search" value="<?= htmlspecialchars($filters['q']) ?>" placeholder="Search dogs by name, breed, or owner…" style="border:none;background:transparent;flex:1;font-family:inherit;font-size:14px;">
</div>

<div class="filter-chips chips-row" data-registry-type-chips>
    <?php foreach (['all' => 'All', 'Owned' => 'Owned', 'Stray' => 'Stray', 'Rescued' => 'Rescued'] as $slug => $label): ?>
        <button type="button" class="chip filter-chip registry-type-chip<?= $filters['type'] === $slug ? ' chip-active' : ' chip-outline' ?>" data-type="<?= htmlspecialchars($slug) ?>"><?= htmlspecialchars($label) ?></button>
    <?php endforeach; ?>
</div>

<div class="filter-secondary registry-filters">
    <label>
        Barangay
        <select class="registry-filter" data-filter="barangay">
            <option value="all">All barangays</option>
            <?php foreach ($barangays as $brgy): ?>
                <option value="<?= htmlspecialchars($brgy) ?>" <?= $filters['barangay'] === $brgy ? 'selected' : '' ?>><?= htmlspecialchars($brgy) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        Breed
        <select class="registry-filter" data-filter="breed">
            <option value="all">All breeds</option>
            <?php foreach ($breedOptions as $breed): ?>
                <option value="<?= htmlspecialchars((string) $breed['breed_name']) ?>" <?= $filters['breed'] === $breed['breed_name'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $breed['breed_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        Vaccination
        <select class="registry-filter" data-filter="vaccine">
            <?php foreach (['all' => 'All', 'Verified' => 'Verified', 'Unverified' => 'Unverified', 'Expired' => 'Expired'] as $val => $label): ?>
                <option value="<?= htmlspecialchars($val) ?>" <?= $filters['vaccine'] === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</div>

<div class="registry-results-bar">
    <span class="registry-results-label text-sm text-muted">Showing <?= count($result['rows']) ?> of <?= (int) $result['total'] ?> dogs</span>
    <div class="registry-view-toggle" role="group" aria-label="View layout">
        <button type="button" class="registry-view-btn is-active" data-registry-view-btn data-registry-view="tiles" title="Tile view" aria-pressed="true">
            <i data-lucide="layout-grid"></i>
            <span>Tiles</span>
        </button>
        <button type="button" class="registry-view-btn" data-registry-view-btn data-registry-view="compact" title="Compact tiles" aria-pressed="false">
            <i data-lucide="grid-3x3"></i>
            <span>Compact</span>
        </button>
        <button type="button" class="registry-view-btn" data-registry-view-btn data-registry-view="list" title="List view" aria-pressed="false">
            <i data-lucide="list"></i>
            <span>List</span>
        </button>
    </div>
</div>

<div class="registry-bento-grid" id="registryGrid" data-registry-grid data-registry-view="tiles">
    <?php
    $rows = $result['rows'];
    require __DIR__ . '/partials/registry-bento-cards.php';
    ?>
</div>

<div class="text-center mt-md" data-registry-load-wrap <?= ($result['total'] <= 20) ? 'hidden' : '' ?>>
    <button type="button" class="btn-outline" data-registry-load-more>Load more</button>
    <p class="text-xs text-muted mt-sm" data-registry-count><?= count($result['rows']) ?> of <?= (int) $result['total'] ?> dogs</p>
</div>

<?php if ($canRegister): ?>
    <a href="register_dog.php" class="btn-primary fab hidden-desktop" style="position:fixed;bottom:80px;right:16px;z-index:40;border-radius:50%;width:56px;height:56px;padding:0;">
        <i data-lucide="plus"></i>
    </a>
<?php endif; ?>

<?php app_layout_end([]); ?>
