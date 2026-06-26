<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/dogs.php';
require_role(['Admin']);

$pdo = db();
$pendingUsers = fetch_pending_users($pdo);
$pendingDogs = fetch_pending_dogs($pdo);
$counts = fetch_registry_counts($pdo, 'Admin', (int) $_SESSION['user_id'], (string) $_SESSION['user_barangay']);

app_layout_start('admin', 'Admin', [
    'showSearch' => false,
    'topbarTitle' => 'Admin Console',
    'scripts' => ['assets/js/admin.js'],
    'admin_context' => true,
    'breadcrumbs' => [['label' => 'Dashboard']],
]);
?>

<div class="feed-header">
    <div>
        <h1 class="feed-title">Admin Console</h1>
        <p class="text-sm text-muted">Approve accounts, verify dog registrations, monitor platform activity.</p>
    </div>
    <a href="analytics.php" class="btn-outline btn-sm">View Analytics</a>
</div>

<div class="summary-strip mb-md">
    <div class="summary-card investigating">
        <div class="summary-value"><?= count($pendingUsers) ?></div>
        <div class="summary-label">Pending Accounts</div>
    </div>
    <div class="summary-card">
        <div class="summary-value"><?= count($pendingDogs) ?></div>
        <div class="summary-label">Pending Dogs</div>
    </div>
    <div class="summary-card resolved">
        <div class="summary-value"><?= (int) $counts['registered'] ?></div>
        <div class="summary-label">Registered Dogs</div>
    </div>
</div>

<div class="card card-bordered card-body mb-md">
    <div class="label-upper mb-md">Pending Account Approvals</div>
    <?php if (count($pendingUsers) === 0): ?>
        <p class="text-sm text-muted">No accounts awaiting approval.</p>
    <?php else: ?>
        <div class="flex flex-col gap-sm">
            <?php foreach ($pendingUsers as $user): ?>
                <div class="flex items-center gap-md admin-row" style="padding:12px 0;border-bottom:1px solid var(--border-light);">
                    <div class="avatar avatar-md <?= htmlspecialchars(avatar_color_class((int) $user['UserID'])) ?>">
                        <?= htmlspecialchars(user_initials_from_name((string) $user['Name'])) ?>
                    </div>
                    <div class="flex-1">
                        <div style="font-weight:500;"><?= htmlspecialchars((string) $user['Name']) ?></div>
                        <div class="text-xs text-muted">
                            <?= htmlspecialchars((string) $user['Role']) ?>
                            · <?= htmlspecialchars((string) $user['Email']) ?>
                            · Brgy. <?= htmlspecialchars((string) $user['Barangay']) ?>
                        </div>
                    </div>
                    <button type="button" class="btn-primary btn-sm" data-admin-approve-user data-user-id="<?= (int) $user['UserID'] ?>">Approve</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="card card-bordered card-body">
    <div class="label-upper mb-md">Pending Dog Registrations</div>
    <?php if (count($pendingDogs) === 0): ?>
        <p class="text-sm text-muted">No dogs awaiting verification.</p>
    <?php else: ?>
        <div class="flex flex-col gap-sm">
            <?php foreach ($pendingDogs as $dog): ?>
                <div class="flex items-center gap-md admin-row" style="padding:12px 0;border-bottom:1px solid var(--border-light);">
                    <div class="icon-box icon-box-md <?= htmlspecialchars(string_color_class((string) ($dog['Breed'] ?? 'dog'))) ?>">
                        <i data-lucide="dog"></i>
                    </div>
                    <div class="flex-1">
                        <div style="font-weight:500;"><?= htmlspecialchars((string) $dog['DogName']) ?></div>
                        <div class="text-xs text-muted">
                            <?= htmlspecialchars((string) ($dog['Breed'] ?? '')) ?>
                            · <?= htmlspecialchars((string) $dog['owner_name']) ?>
                            · <?= htmlspecialchars((string) ($dog['RegistryID'] ?? '')) ?>
                        </div>
                    </div>
                    <a href="dog-profile.php?id=<?= (int) $dog['dog_id'] ?>" class="btn-outline btn-sm">Review</a>
                    <button type="button" class="btn-primary btn-sm" data-admin-approve-dog data-dog-id="<?= (int) $dog['dog_id'] ?>">Verify</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<meta name="csrf-token" content="<?= htmlspecialchars((string) $_SESSION['csrf_token']) ?>">

<?php app_layout_end([]); ?>
