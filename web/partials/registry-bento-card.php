<?php
/** @var array<string, mixed> $dog */

$dogType = (string) ($dog['DogType'] ?? 'Owned');
$typeColors = [
    'Owned' => ['bg' => '#C0DAB5', 'text' => '#4a7a3a'],
    'Stray' => ['bg' => '#F8BC72', 'text' => '#a07820'],
    'Rescued' => ['bg' => '#87AFAE', 'text' => '#2d5a5a'],
];
$typeColor = $typeColors[$dogType] ?? $typeColors['Owned'];
$vax = registry_vax_badge($dog['vaccine_status'] ?? null);
$avatarBg = registry_avatar_color((string) ($dog['DogName'] ?? 'dog'));
$incidentCount = (int) ($dog['incident_count'] ?? 0);
$isAreaRegular = $dogType === 'Stray' && $incidentCount >= 3;
$breedLabel = (string) ($dog['breed_label'] ?? $dog['Breed'] ?? '');
$ownerName = (string) ($dog['owner_name'] ?? '');
$barangay = (string) ($dog['owner_barangay'] ?? 'Unknown');
?>
<a href="dog-profile.php?id=<?= (int) $dog['dog_id'] ?>" class="dog-card">
    <div class="dog-card-avatar" style="background:<?= htmlspecialchars($avatarBg) ?>;">
        <i data-lucide="dog" style="width:48px;height:48px;color:rgba(255,255,255,0.85);"></i>
        <?php if ($incidentCount > 0): ?>
            <span class="incident-badge"><?= $incidentCount ?></span>
        <?php endif; ?>
    </div>
    <div class="dog-card-body">
        <h3 class="dog-card-name"><?= htmlspecialchars((string) $dog['DogName']) ?></h3>
        <div class="dog-card-tags">
            <span class="type-chip" style="background:<?= htmlspecialchars($typeColor['bg']) ?>;color:<?= htmlspecialchars($typeColor['text']) ?>;">
                <?= htmlspecialchars($dogType) ?>
            </span>
            <?php if ($breedLabel !== ''): ?>
                <span class="breed-text"><?= htmlspecialchars($breedLabel) ?></span>
            <?php endif; ?>
        </div>
        <div class="dog-card-badges">
            <span class="vax-badge <?= htmlspecialchars($vax['class']) ?>"><?= htmlspecialchars($vax['text']) ?></span>
            <?php if ($isAreaRegular): ?>
                <span class="area-regular-badge">Area Regular</span>
            <?php endif; ?>
        </div>
        <?php if ($ownerName !== '' && $dogType !== 'Stray'): ?>
            <div class="owner-row">
                <span class="owner-initial"><?= htmlspecialchars(strtoupper(substr($ownerName, 0, 1))) ?></span>
                <span class="owner-name"><?= htmlspecialchars($ownerName) ?></span>
            </div>
        <?php else: ?>
            <div class="owner-row muted"><span>No owner registered</span></div>
        <?php endif; ?>
        <div class="location-row">Brgy. <?= htmlspecialchars($barangay) ?></div>
    </div>
</a>
