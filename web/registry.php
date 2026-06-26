<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/dogs.php';

$pdo = db();
$userRole = current_user_role();
$userId = (int) ($_SESSION['user_id'] ?? 0);
$barangay = (string) ($_SESSION['user_barangay'] ?? '');
$query = trim((string) ($_GET['q'] ?? ''));

$counts = fetch_registry_counts($pdo, $userRole, $userId, $barangay);
$dogs = fetch_registry_dogs($pdo, $userRole, $userId, $barangay, $query !== '' ? $query : null);

app_layout_start('registry', 'Dog Registry', [
    'showSearch' => false,
    'topbarTitle' => 'Dog Registry',
    'scripts' => ['assets/js/registry.js'],
]);
?>

<div class="feed-header">
    <div>
        <h1 class="feed-title">Dog Registry</h1>
        <p class="text-sm text-muted">
            <?php if ($userRole === 'Dog Owner'): ?>
                Your registered dogs on Pawdar.
            <?php else: ?>
                Registered dogs in Brgy. <?= htmlspecialchars($barangay) ?>.
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="summary-strip mb-md">
    <div class="summary-card resolved">
        <div class="summary-value"><?= (int) $counts['registered'] ?></div>
        <div class="summary-label">Registered</div>
    </div>
    <div class="summary-card investigating">
        <div class="summary-value"><?= (int) $counts['pending'] ?></div>
        <div class="summary-label">Pending</div>
    </div>
    <div class="summary-card">
        <div class="summary-value"><?= (int) $counts['total'] ?></div>
        <div class="summary-label">Total</div>
    </div>
</div>

<form method="get" class="search-bar search-bar-light mb-md" data-registry-search>
    <i data-lucide="search"></i>
    <input type="search" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Search dogs, breeds, owners, registry ID…" style="border:none;background:transparent;flex:1;font-family:inherit;font-size:14px;">
</form>

<?php if (count($dogs) === 0): ?>
    <div class="feed-empty-state">
        <p class="feed-empty-title">No dogs found<?= $query !== '' ? ' for \'' . htmlspecialchars($query) . '\'' : '' ?></p>
        <?php if ($query !== ''): ?>
            <a href="registry.php" class="btn-outline btn-sm">Clear search</a>
        <?php elseif ($userRole === 'Dog Owner'): ?>
            <p class="text-sm text-muted">Register your dog through your veterinarian or barangay vet clinic.</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="flex flex-col gap-md" data-registry-list>
        <?php foreach ($dogs as $dog):
            $status = (string) ($dog['Status'] ?? 'Registered');
            $badgeClass = $status === 'Registered' ? 'badge-verified' : 'badge-investigating';
        ?>
            <a href="dog-profile.php?id=<?= (int) $dog['dog_id'] ?>" class="card card-bordered card-body card-hoverable flex items-center gap-md">
                <div class="icon-box icon-box-md <?= htmlspecialchars(string_color_class((string) ($dog['breed_label'] ?? $dog['Breed'] ?? 'dog'))) ?>">
                    <i data-lucide="dog"></i>
                </div>
                <div class="flex-1">
                    <div style="font-weight:500;font-size:15px;"><?= htmlspecialchars((string) $dog['DogName']) ?></div>
                    <div class="text-xs text-muted">
                        <?= htmlspecialchars((string) ($dog['breed_label'] ?? $dog['Breed'] ?? 'Unknown breed')) ?>
                        · <?= htmlspecialchars((string) $dog['owner_name']) ?>
                        <?php if (!empty($dog['RegistryID'])): ?>
                            · <?= htmlspecialchars((string) $dog['RegistryID']) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--air-force);"></i>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php app_layout_end([]); ?>
