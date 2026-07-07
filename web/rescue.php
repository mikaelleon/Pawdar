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
$tracked = $canManage ? fetch_rescue_org_cases($pdo, $userId, $userRole, $barangay) : [];
$listings = fetch_adoption_listings($pdo);

/**
 * CSS accent class for rescue pipeline status.
 */
function rescue_status_accent(string $status): string
{
    $map = [
        'Spotted' => 'is-spotted',
        'Rescued' => 'is-rescued',
        'Under Vet Care' => 'is-vet',
        'Ready for Adoption' => 'is-adoption',
    ];

    return $map[$status] ?? 'is-spotted';
}

/**
 * Badge class for rescue pipeline status.
 */
function rescue_status_badge_class(string $status): string
{
    $map = [
        'Spotted' => 'badge-investigating',
        'Rescued' => 'badge-resolved',
        'Under Vet Care' => 'badge-received',
        'Ready for Adoption' => 'badge-resolved',
    ];

    return $map[$status] ?? 'badge-received';
}

/**
 * Best timestamp for last pipeline activity.
 */
function rescue_case_updated_label(array $case): string
{
    $raw = (string) ($case['last_status_at'] ?? $case['claimed_at'] ?? $case['updated_at'] ?? '');
    if ($raw === '') {
        return 'Unknown';
    }

    return time_elapsed_string($raw);
}

app_layout_start('rescue-board', 'Rescue Board', [
    'showSearch' => false,
    'scripts' => ['assets/js/rescue.js'],
    'breadcrumbs' => [['label' => 'Rescue Board']],
]);
?>

<div class="feed-header">
    <div>
        <h1 class="feed-title">Rescue board</h1>
        <p class="text-sm text-muted">Manage stray cases and adoption listings · Brgy. <?= htmlspecialchars($barangay) ?></p>
    </div>
</div>

<?php if ($canManage): ?>
<div class="summary-strip scr mb-md">
    <div class="summary-card investigating">
        <div class="summary-value"><?= count($unclaimed) ?></div>
        <div class="summary-label">Unclaimed strays</div>
    </div>
    <div class="summary-card">
        <div class="summary-value"><?= count($tracked) ?></div>
        <div class="summary-label">Tracked rescues</div>
    </div>
    <div class="summary-card resolved">
        <div class="summary-value"><?= count($listings) ?></div>
        <div class="summary-label">Open adoptions</div>
    </div>
</div>
<?php endif; ?>

<div class="rescue-panels split-layout">
    <?php if ($canManage): ?>
    <section class="rescue-section split-main">
        <h2 class="rescue-section-title">
            <i data-lucide="siren"></i>
            Active stray cases
        </h2>
        <p class="text-xs text-muted rescue-section-lead">Injured stray reports waiting for a rescue organization to claim.</p>

        <?php if (count($unclaimed) === 0): ?>
            <?php
            $icon = 'search';
            $title = 'No unclaimed stray cases';
            $message = 'New injured stray reports in your barangay will appear here for your team to claim.';
            require __DIR__ . '/partials/rescue-empty.php';
            ?>
        <?php else: ?>
            <div class="rescue-claim-list">
                <?php foreach ($unclaimed as $row):
                    $claimPhoto = incident_photo_url((string) ($row['photo_path'] ?? ''));
                    ?>
                    <article class="rescue-claim-card">
                        <?php if ($claimPhoto): ?>
                            <div class="rescue-card-thumb">
                                <img src="<?= htmlspecialchars($claimPhoto) ?>" alt="Stray case photo near <?= htmlspecialchars((string) $row['Location']) ?>">
                            </div>
                        <?php else: ?>
                            <div class="rescue-card-thumb rescue-card-thumb-placeholder" aria-hidden="true">
                                <i data-lucide="camera-off"></i>
                            </div>
                        <?php endif; ?>
                        <div class="rescue-claim-card-body">
                            <div class="flex items-start gap-sm">
                                <div class="icon-box icon-box-sm" aria-hidden="true"><i data-lucide="map-pin"></i></div>
                                <div>
                                    <div class="rescue-track-location"><?= htmlspecialchars((string) $row['Location']) ?></div>
                                    <p class="text-xs text-muted mt-sm">
                                        <?= htmlspecialchars(time_elapsed_string((string) $row['Date'])) ?>
                                        · <?= (int) $row['sighting_count'] ?> sighting<?= (int) $row['sighting_count'] === 1 ? '' : 's' ?>
                                    </p>
                                    <?php if ((string) ($row['Description'] ?? '') !== ''): ?>
                                        <p class="text-sm mt-sm"><?= htmlspecialchars((string) $row['Description']) ?></p>
                                    <?php endif; ?>
                                    <?php if ((int) ($row['area_regular'] ?? 0) === 1 || (int) $row['sighting_count'] >= 3): ?>
                                        <span class="badge badge-bite mt-sm">Area regular</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-primary btn-sm rescue-claim-btn" data-claim-stray="<?= (int) $row['IncidentID'] ?>">
                            <i data-lucide="hand-heart"></i>
                            Claim case
                        </button>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 class="rescue-section-title mt-lg">
            <i data-lucide="life-buoy"></i>
            Tracked rescues
        </h2>
        <p class="text-xs text-muted rescue-section-lead">Cases your organization has claimed and is moving through the pipeline. Set status to <strong>Ready for Adoption</strong> to publish in Adoption listings.</p>

        <?php if (count($tracked) === 0): ?>
            <?php
            $icon = 'life-buoy';
            $title = 'No tracked rescues yet';
            $message = 'Claim a stray case above to start tracking its rescue progress here.';
            require __DIR__ . '/partials/rescue-empty.php';
            ?>
        <?php else: ?>
            <div class="rescue-track-list">
                <?php foreach ($tracked as $case):
                    $status = (string) $case['status'];
                    $accent = rescue_status_accent($status);
                    $badgeClass = rescue_status_badge_class($status);
                    $caseId = (int) $case['rescue_case_id'];
                    $trackPhoto = incident_photo_url((string) ($case['photo_path'] ?? ''));
                    ?>
                    <article class="rescue-track-card card card-bordered" data-rescue-card="<?= $caseId ?>">
                        <div class="rescue-track-card-accent <?= htmlspecialchars($accent) ?>" data-rescue-accent="<?= $caseId ?>"></div>
                        <div class="card-body">
                            <div class="rescue-track-card-head">
                                <?php if ($trackPhoto): ?>
                                    <div class="rescue-card-thumb">
                                        <img src="<?= htmlspecialchars($trackPhoto) ?>" alt="Rescue case photo near <?= htmlspecialchars((string) $case['Location']) ?>">
                                    </div>
                                <?php else: ?>
                                    <div class="rescue-card-thumb rescue-card-thumb-placeholder" aria-hidden="true">
                                        <i data-lucide="camera-off"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <div class="rescue-track-location"><?= htmlspecialchars((string) $case['Location']) ?></div>
                                    <div class="text-xs text-muted" data-rescue-updated="<?= $caseId ?>">Updated <?= htmlspecialchars(rescue_case_updated_label($case)) ?></div>
                                    <div class="text-xs text-muted mt-sm">
                                        <i data-lucide="building-2" class="rescue-inline-icon"></i>
                                        Handled by <?= htmlspecialchars((string) ($case['org_name'] ?? 'Rescue organization')) ?>
                                    </div>
                                </div>
                                <span class="badge <?= htmlspecialchars($badgeClass) ?> rescue-status-badge" data-rescue-badge="<?= $caseId ?>"><?= htmlspecialchars($status) ?></span>
                            </div>
                            <?php if ((string) ($case['Description'] ?? '') !== ''): ?>
                                <p class="text-sm text-muted mt-sm"><?= htmlspecialchars((string) $case['Description']) ?></p>
                            <?php endif; ?>
                            <div class="rescue-status-field mt-md">
                                <label class="rescue-field-label" for="rescue-status-<?= $caseId ?>">Pipeline status</label>
                                <select
                                    id="rescue-status-<?= $caseId ?>"
                                    class="registry-filter rescue-status-select"
                                    data-rescue-status="<?= $caseId ?>"
                                    aria-label="Update pipeline status for <?= htmlspecialchars((string) $case['Location']) ?>"
                                >
                                    <?php foreach (['Spotted', 'Rescued', 'Under Vet Care', 'Ready for Adoption'] as $st): ?>
                                        <option value="<?= htmlspecialchars($st) ?>" <?= $status === $st ? 'selected' : '' ?>><?= htmlspecialchars($st) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <section class="rescue-section <?= $canManage ? 'split-panel' : 'split-main' ?>">
        <h2 class="rescue-section-title">
            <i data-lucide="heart"></i>
            Adoption listings
        </h2>
        <p class="text-xs text-muted rescue-section-lead">Dogs marked ready for adoption by rescue organizations in the network.</p>

        <?php if (count($listings) === 0): ?>
            <?php
            $icon = 'heart';
            $title = 'No dogs available for adoption yet';
            $message = 'When rescues mark dogs as ready for adoption, they will be listed here for the community.';
            require __DIR__ . '/partials/rescue-empty.php';
            ?>
        <?php else: ?>
            <div class="rescue-adoption-grid">
                <?php foreach ($listings as $listing):
                    $listingPhoto = incident_photo_url((string) ($listing['display_photo'] ?? ''));
                    ?>
                    <article class="card card-bordered card-body rescue-adoption-card">
                        <?php if ($listingPhoto): ?>
                            <div class="rescue-adoption-photo">
                                <img src="<?= htmlspecialchars($listingPhoto) ?>" alt="Photo of <?= htmlspecialchars((string) ($listing['dog_name'] ?: 'rescue dog')) ?>">
                            </div>
                        <?php else: ?>
                            <div class="registry-dog-avatar pastel-color-1 rescue-adoption-avatar" aria-hidden="true">
                                <i data-lucide="dog"></i>
                            </div>
                        <?php endif; ?>
                        <div class="rescue-adoption-name"><?= htmlspecialchars((string) ($listing['dog_name'] ?: 'Rescue dog')) ?></div>
                        <p class="text-xs text-muted">
                            <?= htmlspecialchars((string) ($listing['estimated_age'] ?? 'Age unknown')) ?>
                            · <?= htmlspecialchars((string) $listing['org_name']) ?>
                        </p>
                        <p class="text-sm mt-sm"><?= htmlspecialchars((string) ($listing['temperament_notes'] ?? $listing['dog_description'])) ?></p>
                        <button type="button" class="btn-primary btn-sm mt-md" data-adopt-contact="<?= htmlspecialchars((string) $listing['org_phone']) ?>">
                            <i data-lucide="phone"></i>
                            Contact to adopt
                        </button>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php app_layout_end([]); ?>
