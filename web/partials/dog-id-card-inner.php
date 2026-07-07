<?php
/**
 * Printable dog registry ID card (inner markup only).
 *
 * @var array<string, mixed> $dog
 * @var array<string, mixed>|null $breedInfo
 */
$breedInfo = $breedInfo ?? null;
$registryId = (string) ($dog['RegistryID'] ?? ('PWD-2024-' . str_pad((string) ($dog['dog_id'] ?? 0), 5, '0', STR_PAD_LEFT)));
$photoUrl = dog_profile_image_url($dog, $breedInfo);
$avatarColor = string_color_class((string) ($dog['Breed'] ?? 'dog'));
$dogType = (string) ($dog['DogType'] ?? 'Owned');
$genderLabel = dog_gender_label($dog);
$breedLabel = trim((string) ($dog['Breed'] ?? 'Unknown breed'));
$sizeLabel = trim((string) ($dog['Size'] ?? ''));
$weightLabel = !empty($dog['weight_kg']) ? rtrim(rtrim((string) $dog['weight_kg'], '0'), '.') . ' kg' : '';
$physicalParts = array_filter([$sizeLabel, $weightLabel], static fn (string $part): bool => $part !== '');
$physicalLine = implode(' · ', $physicalParts);
$vaxBadge = dog_vaccination_badge($dog);
$vaxDate = !empty($dog['vaccine']['DateGiven']) ? (string) $dog['vaccine']['DateGiven'] : null;
$ownerName = trim((string) ($dog['owner_name'] ?? ''));
$ownerBarangay = trim((string) ($dog['owner_barangay'] ?? ''));
$ownerLine = $ownerName;
if ($ownerBarangay !== '') {
    $ownerLine .= ($ownerLine !== '' ? ' · ' : '') . 'Brgy. ' . $ownerBarangay;
}
$vaxLine = $vaxBadge['label'];
if ($vaxDate !== null) {
    $vaxLine .= ' · ' . $vaxDate;
}
?>
<article class="id-card" data-id-card-print-target aria-label="Dog registry ID card for <?= htmlspecialchars((string) $dog['DogName']) ?>">
    <header class="id-card-header">
        <div class="id-card-brand">
            <span class="logo-mark id-card-logo-mark" aria-hidden="true">
                <i data-lucide="paw-print"></i>
            </span>
            <span class="id-card-brand-name"><?= htmlspecialchars(SITE_NAME) ?></span>
        </div>
        <div class="id-card-header-id">
            <span class="id-card-header-id-label">Registry ID</span>
            <span class="id-card-header-id-value"><?= htmlspecialchars($registryId) ?></span>
        </div>
    </header>

    <div class="id-card-main">
        <div class="id-card-photo-wrap <?= htmlspecialchars($avatarColor) ?><?= $photoUrl ? ' id-card-photo-wrap--has-photo' : '' ?>">
            <?php if ($photoUrl): ?>
                <img
                    src="<?= htmlspecialchars($photoUrl) ?>"
                    alt=""
                    class="id-card-photo"
                    width="88"
                    height="88"
                    onerror="this.hidden=true; this.nextElementSibling.hidden=false; if(window.lucide){window.lucide.createIcons();}"
                >
                <div class="id-card-photo-placeholder dog-photo-placeholder" hidden aria-hidden="true">
                    <i data-lucide="dog"></i>
                </div>
            <?php else: ?>
                <div class="id-card-photo-placeholder dog-photo-placeholder" aria-hidden="true">
                    <i data-lucide="dog"></i>
                </div>
            <?php endif; ?>
        </div>

        <div class="id-card-details">
            <h1 class="id-card-name"><?= htmlspecialchars((string) $dog['DogName']) ?></h1>
            <p class="id-card-meta"><?= htmlspecialchars($dogType) ?> · <?= htmlspecialchars($breedLabel) ?> · <?= htmlspecialchars($genderLabel) ?></p>
            <?php if ($physicalLine !== ''): ?>
                <p class="id-card-physical"><?= htmlspecialchars($physicalLine) ?></p>
            <?php endif; ?>
            <div class="id-card-fields">
                <?php if ($ownerLine !== ''): ?>
                    <div class="id-card-field">
                        <span class="id-card-field-label">Owner</span>
                        <span class="id-card-field-value"><?= htmlspecialchars($ownerLine) ?></span>
                    </div>
                <?php endif; ?>
                <div class="id-card-field">
                    <span class="id-card-field-label">Vaccination</span>
                    <span class="id-card-field-value"><?= htmlspecialchars($vaxLine) ?></span>
                </div>
            </div>
        </div>

        <div class="id-card-qr-col">
            <img
                src="qr.php?id=<?= urlencode($registryId) ?>&amp;size=280"
                alt="QR code for <?= htmlspecialchars($registryId) ?>"
                class="id-card-qr"
                width="120"
                height="120"
            >
            <p class="id-card-scan-caption">Scan to view full profile</p>
        </div>
    </div>
</article>
