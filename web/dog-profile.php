<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/dogs.php';
require_once __DIR__ . '/includes/breeds.php';
require_once __DIR__ . '/includes/breed-media.php';

$dogId = (int) ($_GET['id'] ?? 0);
$pdo = db();
$dog = $dogId > 0 ? fetch_dog_profile($pdo, $dogId) : null;

if (!$dog) {
    $fallback = $pdo->query('SELECT dog_id FROM dog ORDER BY dog_id ASC LIMIT 1')->fetch();
    if ($fallback) {
        header('Location: dog-profile.php?id=' . (int) $fallback['dog_id']);
        exit;
    }
}

$userRole = current_user_role();
$userId = current_user_id();
$isOwner = $dog && (int) $dog['owner_id'] === $userId;
$canReport = role_can_report($userRole);
$breedInfo = null;
if ($dog) {
    if (!empty($dog['breed_id'])) {
        $breedInfo = fetch_breed_by_id($pdo, (int) $dog['breed_id']);
    }
    if (!$breedInfo) {
        $breedInfo = fetch_breed_by_name($pdo, (string) ($dog['Breed'] ?? ''));
    }
}
$registryId = (string) ($dog['RegistryID'] ?? ('PWD-2024-' . str_pad((string) ($dog['dog_id'] ?? 0), 5, '0', STR_PAD_LEFT)));
$breedColor = string_color_class((string) ($dog['Breed'] ?? 'dog'));
$photoUrl = dog_profile_image_url($dog, $breedInfo);
$hasPhysical = trim((string) ($dog['coat_color'] ?? '')) !== ''
    || !empty($dog['weight_kg'])
    || trim((string) ($dog['distinguishing_marks'] ?? '')) !== ''
    || trim((string) ($dog['temperament_notes'] ?? '')) !== '';

$canContactOwner = in_array($userRole, ['Veterinarian', 'LGU Official', 'Admin'], true);
$canCosign = $userRole === 'Veterinarian'
    && !empty($dog['vaccine'])
    && (($dog['vaccine']['vax_status'] ?? '') !== 'Verified');

app_layout_start('registry', 'Dog Profile', [
    'showSearch' => false,
    'mobileHeader' => 'back',
    'backTitle' => 'Registry',
    'backHref' => 'registry.php',
    'report_drawer' => $canReport,
    'breadcrumbs' => [
        ['label' => 'Registry', 'url' => 'registry.php'],
        ['label' => (string) ($dog['DogName'] ?? 'Dog Profile')],
    ],
    'scripts' => ['assets/js/dog-profile.js'],
]);
?>

<a href="registry.php" class="registry-back hidden-mobile"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back to Registry</a>

<div class="profile-layout" data-dog-profile
     data-dog-id="<?= (int) $dog['dog_id'] ?>"
     data-dog-name="<?= htmlspecialchars((string) $dog['DogName']) ?>"
     data-registry-id="<?= htmlspecialchars($registryId) ?>"
     data-owner-name="<?= htmlspecialchars((string) $dog['owner_name']) ?>">
    <div class="profile-main">
        <div class="card card-bordered profile-hero hidden-mobile card-hoverable profile-hero-pattern" style="flex-direction:row;text-align:left;padding:24px;align-items:center;gap:20px;">
            <div class="profile-avatar-wrap <?= htmlspecialchars($breedColor) ?>">
                <?php if ($photoUrl): ?>
                    <img src="<?= htmlspecialchars($photoUrl) ?>" alt="<?= htmlspecialchars((string) $dog['DogName']) ?>" class="profile-avatar-img">
                <?php else: ?>
                    <i data-lucide="dog" class="profile-avatar-icon"></i>
                <?php endif; ?>
            </div>
            <div class="flex-1">
                <h1 style="font-weight:500;font-size:30px;letter-spacing:-.5px;margin:0;"><?= htmlspecialchars((string) $dog['DogName']) ?></h1>
                <div class="flex items-center gap-sm mt-sm">
                    <span class="badge badge-owned"><?= htmlspecialchars((string) ($dog['DogType'] ?? 'Owned')) ?></span>
                    <span class="text-sm"><?= htmlspecialchars((string) $dog['Breed']) ?> · <?= htmlspecialchars((string) ($dog['Gender'] ?? 'Unknown')) ?></span>
                </div>
                <div class="text-xs text-muted mt-sm registry-id flex items-center gap-sm">
                    Registry ID · <?= htmlspecialchars($registryId) ?>
                    <button type="button" class="copy-btn" data-copy="<?= htmlspecialchars($registryId) ?>" title="Copy ID"><i data-lucide="copy" style="width:14px;height:14px;"></i></button>
                </div>
                <div class="flex gap-sm flex-wrap" style="margin-top:12px;">
                    <?php if ($isOwner): ?>
                        <button type="button" class="btn-outline btn-sm" data-open-edit-dog>Edit Profile</button>
                    <?php endif; ?>
                    <?php if ($canReport): ?>
                        <button type="button" class="btn-primary btn-sm" data-report-dog-incident>
                            <i data-lucide="flag-triangle-right" style="width:14px;height:14px;"></i> Report Incident
                        </button>
                    <?php endif; ?>
                    <?php if ($canCosign): ?>
                        <button type="button" class="btn-primary btn-sm" data-cosign-vaccine data-vaccine-id="<?= (int) $dog['vaccine']['VaccineID'] ?>">Co-sign Vaccination</button>
                    <?php endif; ?>
                    <?php if (in_array($userRole, ['LGU Official', 'Admin'], true)): ?>
                        <a href="cases.php" class="btn-ghost btn-sm">Manage cases</a>
                    <?php endif; ?>
                    <?php if ($userRole === 'Admin'): ?>
                        <button type="button" class="btn-ghost btn-sm">Deactivate Registry Entry</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="profile-hero hidden-desktop profile-hero-pattern">
            <div class="profile-avatar-wrap <?= htmlspecialchars($breedColor) ?>">
                <?php if ($photoUrl): ?>
                    <img src="<?= htmlspecialchars($photoUrl) ?>" alt="<?= htmlspecialchars((string) $dog['DogName']) ?>" class="profile-avatar-img">
                <?php else: ?>
                    <i data-lucide="dog" class="profile-avatar-icon"></i>
                <?php endif; ?>
            </div>
            <h1 class="profile-name"><?= htmlspecialchars((string) $dog['DogName']) ?></h1>
            <div class="registry-id text-xs text-muted">Registry ID · <?= htmlspecialchars($registryId) ?></div>
            <?php if ($isOwner): ?>
                <button type="button" class="btn-outline btn-sm mt-sm" data-open-edit-dog>Edit Profile</button>
            <?php endif; ?>
            <?php if ($canReport): ?>
                <button type="button" class="btn-primary btn-sm mt-sm" data-report-dog-incident>Report Incident</button>
            <?php endif; ?>
        </div>

        <?php if ($hasPhysical): ?>
        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Physical &amp; temperament</div>
            <dl class="dog-detail-grid">
                <?php if (trim((string) ($dog['coat_color'] ?? '')) !== ''): ?>
                    <div><dt class="text-xs text-muted">Coat color</dt><dd class="text-sm"><?= htmlspecialchars((string) $dog['coat_color']) ?></dd></div>
                <?php endif; ?>
                <?php if (!empty($dog['weight_kg'])): ?>
                    <div><dt class="text-xs text-muted">Weight</dt><dd class="text-sm"><?= htmlspecialchars((string) $dog['weight_kg']) ?> kg</dd></div>
                <?php endif; ?>
                <?php if (trim((string) ($dog['distinguishing_marks'] ?? '')) !== ''): ?>
                    <div class="dog-detail-full"><dt class="text-xs text-muted">Distinguishing marks</dt><dd class="text-sm"><?= nl2br(htmlspecialchars((string) $dog['distinguishing_marks'])) ?></dd></div>
                <?php endif; ?>
                <?php if (trim((string) ($dog['temperament_notes'] ?? '')) !== ''): ?>
                    <div class="dog-detail-full"><dt class="text-xs text-muted">Temperament notes</dt><dd class="text-sm"><?= nl2br(htmlspecialchars((string) $dog['temperament_notes'])) ?></dd></div>
                <?php endif; ?>
            </dl>
        </div>
        <?php endif; ?>

        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Owner</div>
            <div class="flex items-center gap-md owner-card-row">
                <div class="avatar avatar-lg"><?= htmlspecialchars(user_initials_from_name((string) $dog['owner_name'])) ?></div>
                <div class="flex-1">
                    <div style="font-weight:500;font-size:15px;"><?= htmlspecialchars((string) $dog['owner_name']) ?></div>
                    <div class="text-xs text-muted"><?= htmlspecialchars((string) $dog['owner_role']) ?> · Brgy. <?= htmlspecialchars((string) $dog['owner_barangay']) ?></div>
                    <?php if ($canContactOwner && !empty($dog['owner_phone'])): ?>
                        <div class="text-xs text-muted owner-contact-line hidden-mobile" data-owner-contact-line hidden>
                            <?= htmlspecialchars((string) $dog['owner_phone']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($canContactOwner): ?>
                    <button type="button"
                            class="btn-outline btn-sm btn-call-owner"
                            data-owner-contact="<?= htmlspecialchars((string) ($dog['owner_phone'] ?? '')) ?>"
                            data-owner-name="<?= htmlspecialchars((string) $dog['owner_name']) ?>">
                        Call owner
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="card card-bordered card-body">
            <div class="flex justify-between items-center mb-md">
                <div class="label-upper">Vaccination</div>
                <?php if ($dog['vaccine']): ?>
                    <span class="badge badge-verified"><i data-lucide="shield-check" style="width:13px;height:13px;"></i> Verified</span>
                <?php else: ?>
                    <span class="badge badge-investigating">Pending</span>
                <?php endif; ?>
            </div>
            <?php if ($dog['vaccine']): ?>
                <div class="flex items-center gap-md">
                    <div class="icon-box icon-box-sm"><i data-lucide="syringe"></i></div>
                    <div>
                        <div style="font-weight:500;font-size:15px;"><?= htmlspecialchars((string) $dog['vaccine']['VaccineName']) ?></div>
                        <div class="text-xs text-muted mt-sm">Verified by <?= htmlspecialchars((string) ($dog['vaccine']['VetName'] ?? 'Vet')) ?> · <?= htmlspecialchars((string) $dog['vaccine']['DateGiven']) ?></div>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-sm text-muted">No verified vaccination on file.</p>
            <?php endif; ?>
        </div>

        <?php if ($breedInfo): ?>
            <div class="card card-bordered card-body hidden-mobile">
                <div class="label-upper mb-md">Breed Info</div>
                <?php
                $traits = [
                    ['shield', 'Loyalty', (int) ($breedInfo['loyalty_score'] ?? 3)],
                    ['zap', 'Energy', (int) ($breedInfo['energy_score'] ?? 3)],
                    ['smile', 'Friendliness', (int) ($breedInfo['friendliness_score'] ?? 3)],
                ];
                foreach ($traits as [$icon, $label, $filled]): ?>
                    <div class="flex items-center gap-md mb-md">
                        <i data-lucide="<?= $icon ?>" style="color:var(--muted-teal);"></i>
                        <div class="flex-1 text-sm" style="font-weight:500;"><?= $label ?></div>
                        <div>
                            <div class="rating-dots">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <span class="rating-dot<?= $i >= $filled ? ' empty' : '' ?>"></span>
                                <?php endfor; ?>
                            </div>
                            <div class="rating-scale-labels"><span>Low</span><span>High</span></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Past Incidents</div>
            <?php if (empty($dog['incidents'])): ?>
                <div class="empty-state-inline">
                    <svg class="feed-empty-illustration" viewBox="0 0 200 160" aria-hidden="true" style="width:120px;">
                        <ellipse cx="100" cy="130" rx="70" ry="12" fill="var(--tea-green)" opacity="0.25"/>
                        <circle cx="75" cy="70" r="28" fill="var(--muted-teal)" opacity="0.35"/>
                        <circle cx="125" cy="70" r="28" fill="var(--air-force)" opacity="0.3"/>
                        <path d="M88 95c8 14 16 14 24 0" stroke="var(--taupe)" stroke-width="3" fill="none" stroke-linecap="round"/>
                    </svg>
                    <p class="text-sm text-muted" style="margin:0;">No incidents recorded</p>
                </div>
            <?php else: ?>
                <div class="flex flex-col gap-md">
                    <?php foreach ($dog['incidents'] as $incident):
                        $incidentType = normalize_incident_type((string) $incident['IncidentType']);
                        $typeMeta = incident_type_meta($incidentType);
                        $statusMeta = case_status_meta($incident['CaseStatus'] ?? null); ?>
                        <div class="card card-bordered card-body flex items-center gap-md card-hoverable" style="padding:14px;">
                            <span class="badge <?= htmlspecialchars($typeMeta['badge']) ?>"><?= htmlspecialchars($typeMeta['label']) ?></span>
                            <div class="flex-1">
                                <div class="text-sm" style="font-weight:500;"><?= htmlspecialchars((string) ($incident['Description'] ?: $incidentType)) ?></div>
                                <div class="text-xs text-muted"><?= htmlspecialchars(date('d M Y', strtotime((string) $incident['Date']))) ?></div>
                            </div>
                            <span class="badge <?= $statusMeta['class'] ?>"><?= htmlspecialchars($statusMeta['label']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="profile-actions hidden-mobile">
            <button type="button" class="btn-ghost" data-flag-dog>
                <i data-lucide="flag"></i> Flag This Dog
            </button>
        </div>
    </div>

    <aside class="profile-side">
        <div class="card card-bordered card-body text-center">
            <img src="qr.php?id=<?= urlencode($registryId) ?>" alt="QR code for dog profile" width="180" height="180" style="border-radius:8px;">
            <div style="font-weight:500;font-size:15px;margin-top:14px;">Scan to View Profile</div>
            <div class="text-xs text-muted registry-id"><?= htmlspecialchars($registryId) ?></div>
            <a href="qr.php?id=<?= urlencode($registryId) ?>" download="<?= htmlspecialchars($registryId) ?>.png" class="text-sm link-hover" style="display:inline-block;margin-top:10px;">Download QR</a>
        </div>
    </aside>
</div>

<?php if ($isOwner): ?>
    <?php require __DIR__ . '/partials/dog-edit-modal.php'; ?>
<?php endif; ?>

<div class="sticky-cta hidden-desktop">
    <button type="button" class="btn-ghost btn-block" style="height:48px;" data-flag-dog>
        <i data-lucide="flag"></i> Flag This Dog
    </button>
</div>

<?php app_layout_end([]); ?>
