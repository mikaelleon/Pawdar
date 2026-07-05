<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/dogs.php';
require_once __DIR__ . '/includes/breeds.php';

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

$canContactOwner = in_array($userRole, ['Veterinarian', 'LGU Official', 'Admin'], true);
$canCosign = $userRole === 'Veterinarian'
    && !empty($dog['vaccine'])
    && (($dog['vaccine']['vax_status'] ?? '') !== 'Verified');

app_layout_start('registry', 'Dog Profile', [
    'showSearch' => false,
    'mobileHeader' => 'back',
    'backTitle' => 'Registry',
    'backHref' => 'registry.php',
    'breadcrumbs' => [
        ['label' => 'Registry', 'url' => 'registry.php'],
        ['label' => (string) ($dog['DogName'] ?? 'Dog Profile')],
    ],
    'scripts' => ['assets/js/dog-profile.js'],
]);
?>

<a href="registry.php" class="registry-back hidden-mobile"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back to Registry</a>

<div class="profile-layout">
    <div class="profile-main">
        <div class="card card-bordered profile-hero hidden-mobile card-hoverable" style="flex-direction:row;text-align:left;padding:24px;align-items:center;gap:20px;">
            <div class="icon-box icon-box-lg <?= htmlspecialchars($breedColor) ?>" style="color:#fff;width:120px;height:120px;border:4px solid #fff;box-shadow:0 4px 12px rgba(74,67,67,.15);">
                <i data-lucide="dog" style="width:58px;height:58px;"></i>
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
                <?php if ($isOwner): ?><a href="#" class="btn-outline btn-sm" style="margin-top:12px;">Edit Profile</a><?php endif; ?>
                <?php if ($canCosign): ?>
                    <button type="button" class="btn-primary btn-sm" style="margin-top:12px;" data-cosign-vaccine data-vaccine-id="<?= (int) $dog['vaccine']['VaccineID'] ?>">Co-sign Vaccination</button>
                <?php endif; ?>
                <?php if (in_array($userRole, ['LGU Official', 'Admin'], true)): ?><a href="cases.php" class="btn-primary btn-sm" style="margin-top:12px;">Create Case</a><?php endif; ?>
                <?php if ($userRole === 'Admin'): ?><button type="button" class="btn-ghost btn-sm" style="margin-top:12px;">Deactivate Registry Entry</button><?php endif; ?>
            </div>
        </div>

        <div class="profile-hero hidden-desktop">
            <div class="icon-box icon-box-lg <?= htmlspecialchars($breedColor) ?>" style="color:#fff;border:4px solid #fff;">
                <i data-lucide="dog" style="width:48px;height:48px;"></i>
            </div>
            <h1 class="profile-name"><?= htmlspecialchars((string) $dog['DogName']) ?></h1>
            <div class="registry-id text-xs text-muted">Registry ID · <?= htmlspecialchars($registryId) ?></div>
        </div>

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

        <div>
            <div style="font-size:14px;font-weight:500;color:var(--air-force);margin-bottom:10px;">Past Incidents</div>
            <?php if (empty($dog['incidents'])): ?>
                <div class="card card-bordered card-body text-center text-sm text-muted">
                    <i data-lucide="paw-print" style="width:20px;height:20px;margin-bottom:6px;"></i>
                    <div>No incidents recorded</div>
                </div>
            <?php else: ?>
                <div class="flex flex-col gap-md">
                    <?php foreach ($dog['incidents'] as $incident):
                        $meta = case_status_meta($incident['CaseStatus'] ?? null); ?>
                        <div class="card card-bordered card-body flex items-center gap-md card-hoverable">
                            <span class="badge badge-aggressive"><?= htmlspecialchars((string) $incident['IncidentType']) ?></span>
                            <div class="flex-1">
                                <div class="text-sm" style="font-weight:500;"><?= htmlspecialchars((string) ($incident['Description'] ?: $incident['IncidentType'])) ?></div>
                                <div class="text-xs text-muted"><?= htmlspecialchars(date('d M Y', strtotime((string) $incident['Date']))) ?></div>
                            </div>
                            <span class="badge <?= $meta['class'] ?>"><?= htmlspecialchars($meta['label']) ?></span>
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

<div class="sticky-cta hidden-desktop">
    <button type="button" class="btn-ghost btn-block" style="height:48px;" data-flag-dog>
        <i data-lucide="flag"></i> Flag This Dog
    </button>
</div>

<?php app_layout_end([]); ?>
