<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/incidents.php';
require_once __DIR__ . '/includes/rescue.php';

$pdo = db();
$userRole = current_user_role();
$userId = (int) $_SESSION['user_id'];
$barangay = (string) $_SESSION['user_barangay'];
$canManage = in_array($userRole, ['Rescue Organization', 'Admin'], true);

$unclaimed = $canManage ? fetch_unclaimed_stray_cases($pdo, $barangay) : [];
$tracked = $canManage ? fetch_rescue_org_cases($pdo, $userId) : [];
$listings = fetch_adoption_listings($pdo);

app_layout_start('rescue-board', 'Rescue Board', [
    'showSearch' => false,
    'scripts' => ['assets/js/rescue.js'],
    'breadcrumbs' => [['label' => 'Rescue Board']],
]);
?>

<div class="feed-header">
    <h1 class="feed-title">Rescue board</h1>
    <p class="text-sm text-muted">Manage stray cases and adoption listings in Brgy. <?= htmlspecialchars($barangay) ?></p>
</div>

<div class="rescue-panels split-layout">
    <?php if ($canManage): ?>
    <section class="split-main">
        <div class="label-upper mb-md">Active stray cases (claim queue)</div>
        <?php if (count($unclaimed) === 0): ?>
            <p class="text-sm text-muted">No unclaimed stray cases.</p>
        <?php else: ?>
            <?php foreach ($unclaimed as $row): ?>
                <div class="card card-bordered card-body mb-md flex justify-between items-start gap-md">
                    <div>
                        <div class="text-sm" style="font-weight:700;"><?= htmlspecialchars((string) $row['Location']) ?></div>
                        <p class="text-xs text-muted mt-sm"><?= htmlspecialchars(time_elapsed_string((string) $row['Date'])) ?> · <?= (int) $row['sighting_count'] ?> sightings</p>
                        <p class="text-sm mt-sm"><?= htmlspecialchars((string) $row['Description']) ?></p>
                        <?php if ((int) ($row['area_regular'] ?? 0) === 1 || (int) $row['sighting_count'] >= 3): ?>
                            <span class="badge badge-bite mt-sm">Area regular</span>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn-primary btn-sm" data-claim-stray="<?= (int) $row['IncidentID'] ?>">Claim case</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="label-upper mt-md mb-md">Tracked rescues</div>
        <?php if (count($tracked) === 0): ?>
            <p class="text-sm text-muted">No cases assigned to your organization yet.</p>
        <?php else: ?>
            <?php foreach ($tracked as $case): ?>
                <div class="card card-bordered card-body mb-md">
                    <div class="flex justify-between items-center">
                        <span class="badge badge-owned"><?= htmlspecialchars((string) $case['status']) ?></span>
                        <span class="text-xs text-muted"><?= htmlspecialchars(time_elapsed_string((string) $case['updated_at'])) ?></span>
                    </div>
                    <p class="text-sm mt-sm"><?= htmlspecialchars((string) $case['Location']) ?></p>
                    <select class="registry-filter mt-sm" data-rescue-status="<?= (int) $case['rescue_case_id'] ?>">
                        <?php foreach (['Spotted', 'Rescued', 'Under Vet Care', 'Ready for Adoption'] as $st): ?>
                            <option value="<?= htmlspecialchars($st) ?>" <?= $case['status'] === $st ? 'selected' : '' ?>><?= htmlspecialchars($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <section class="<?= $canManage ? 'split-panel' : 'split-main' ?>">
        <div class="label-upper mb-md">Adoption listings</div>
        <div class="registry-grid">
            <?php if (count($listings) === 0): ?>
                <p class="text-sm text-muted">No dogs available for adoption yet.</p>
            <?php else: ?>
                <?php foreach ($listings as $listing): ?>
                    <article class="card card-bordered card-body">
                        <div class="registry-dog-avatar pastel-color-1" style="margin-bottom:12px;"><i data-lucide="dog"></i></div>
                        <div style="font-weight:700;"><?= htmlspecialchars((string) ($listing['dog_name'] ?: 'Rescue dog')) ?></div>
                        <p class="text-xs text-muted"><?= htmlspecialchars((string) ($listing['estimated_age'] ?? '')) ?> · <?= htmlspecialchars((string) $listing['org_name']) ?></p>
                        <p class="text-sm mt-sm"><?= htmlspecialchars((string) ($listing['temperament_notes'] ?? $listing['dog_description'])) ?></p>
                        <button type="button" class="btn-primary btn-sm mt-md" data-adopt-contact="<?= htmlspecialchars((string) $listing['org_phone']) ?>">Adopt this dog</button>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php app_layout_end([]); ?>
