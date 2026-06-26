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
$barangays = fetch_registry_barangays($pdo);
$breedOptions = fetch_all_breeds($pdo);

app_layout_start('registry', 'Dog Registry', [
    'showSearch' => false,
    'topbarTitle' => 'Dog Registry',
    'scripts' => ['assets/js/registry.js'],
]);
?>

<div class="feed-header flex justify-between items-start">
    <div>
        <h1 class="feed-title">Dog Registry</h1>
        <p class="text-sm text-muted">Browse, search, and filter registered dogs in your community.</p>
    </div>
    <?php if ($canRegister): ?>
        <a href="register_dog.php" class="btn-primary btn-sm hidden-mobile"><i data-lucide="plus"></i> Register a dog</a>
    <?php endif; ?>
</div>

<div class="search-bar search-bar-light mb-md" data-registry-search-wrap>
    <i data-lucide="search"></i>
    <input type="search" id="registry-search" value="<?= htmlspecialchars($filters['q']) ?>" placeholder="Search dogs by name, breed, or owner…" style="border:none;background:transparent;flex:1;font-family:inherit;font-size:14px;">
</div>

<div class="chips-row mb-md" data-registry-type-chips>
    <?php foreach (['all' => 'All', 'Owned' => 'Owned', 'Stray' => 'Stray', 'Rescued' => 'Rescued'] as $slug => $label): ?>
        <button type="button" class="chip registry-type-chip<?= $filters['type'] === $slug ? ' chip-active' : ' chip-outline' ?>" data-type="<?= htmlspecialchars($slug) ?>"><?= htmlspecialchars($label) ?></button>
    <?php endforeach; ?>
</div>

<div class="registry-filters flex flex-wrap gap-md mb-md">
    <label class="text-sm flex items-center gap-sm">
        <span class="text-muted">Barangay</span>
        <select class="registry-filter" data-filter="barangay">
            <option value="all">All barangays</option>
            <?php foreach ($barangays as $brgy): ?>
                <option value="<?= htmlspecialchars($brgy) ?>" <?= $filters['barangay'] === $brgy ? 'selected' : '' ?>><?= htmlspecialchars($brgy) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label class="text-sm flex items-center gap-sm">
        <span class="text-muted">Breed</span>
        <select class="registry-filter" data-filter="breed">
            <option value="all">All breeds</option>
            <?php foreach ($breedOptions as $breed): ?>
                <option value="<?= htmlspecialchars((string) $breed['breed_name']) ?>" <?= $filters['breed'] === $breed['breed_name'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $breed['breed_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label class="text-sm flex items-center gap-sm">
        <span class="text-muted">Vaccination</span>
        <select class="registry-filter" data-filter="vaccine">
            <?php foreach (['all' => 'All', 'Verified' => 'Verified', 'Unverified' => 'Unverified', 'Expired' => 'Expired'] as $val => $label): ?>
                <option value="<?= htmlspecialchars($val) ?>" <?= $filters['vaccine'] === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</div>

<div class="registry-grid" data-registry-grid>
    <?php if (count($result['rows']) === 0): ?>
        <div class="feed-empty-state" style="grid-column:1/-1;">
            <p class="feed-empty-title">No dogs found</p>
            <?php if ($canRegister): ?>
                <a href="register_dog.php" class="btn-primary btn-sm">Register a dog</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php foreach ($result['rows'] as $dog) {
            require __DIR__ . '/partials/registry-dog-card.php';
        } ?>
    <?php endif; ?>
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
