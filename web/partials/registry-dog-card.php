<?php
/** @var array<string, mixed> $dog */
$dogType = (string) ($dog['DogType'] ?? 'Owned');
$typeClass = dog_type_chip_class($dogType);
$vax = vaccine_status_badge($dog['vaccine_status'] ?? null);
$color = string_color_class((string) ($dog['breed_label'] ?? $dog['Breed'] ?? 'dog'));
$ownerInitials = user_initials_from_name((string) ($dog['owner_name'] ?? '?'));
$ownerAvatar = avatar_color_class((int) ($dog['owner_id'] ?? 0));
?>
<article class="registry-dog-card card card-bordered card-hoverable" data-dog-id="<?= (int) $dog['dog_id'] ?>">
    <a href="dog-profile.php?id=<?= (int) $dog['dog_id'] ?>" class="registry-dog-card-link">
        <div class="registry-dog-avatar <?= htmlspecialchars($color) ?>">
            <i data-lucide="dog"></i>
        </div>
        <div class="registry-dog-body">
            <div class="registry-dog-name"><?= htmlspecialchars((string) $dog['DogName']) ?></div>
            <div class="registry-dog-meta flex items-center gap-sm flex-wrap">
                <span class="badge <?= htmlspecialchars($typeClass) ?>"><?= htmlspecialchars($dogType) ?></span>
                <span class="text-xs text-muted">
                    <?= htmlspecialchars((string) ($dog['breed_label'] ?? $dog['Breed'] ?? 'Unknown')) ?>
                    <?php if (!empty($dog['Gender'])): ?> · <?= htmlspecialchars((string) $dog['Gender']) ?><?php endif; ?>
                </span>
            </div>
            <div class="registry-dog-badges flex items-center gap-sm mt-sm">
                <span class="badge <?= htmlspecialchars($vax['class']) ?>">
                    <i data-lucide="shield" style="width:12px;height:12px;"></i>
                    <?= htmlspecialchars($vax['label']) ?>
                </span>
            </div>
            <div class="registry-dog-owner flex items-center gap-sm mt-sm">
                <?php if ($dogType === 'Stray' && empty($dog['owner_name'])): ?>
                    <span class="text-xs text-muted">Stray — No owner</span>
                <?php else: ?>
                    <span class="avatar avatar-sm <?= htmlspecialchars($ownerAvatar) ?>"><?= htmlspecialchars($ownerInitials) ?></span>
                    <span class="text-xs"><?= htmlspecialchars((string) $dog['owner_name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="text-xs text-muted flex items-center gap-sm mt-sm">
                <i data-lucide="map-pin" style="width:12px;height:12px;"></i>
                Brgy. <?= htmlspecialchars((string) ($dog['owner_barangay'] ?? '')) ?>
            </div>
        </div>
        <span class="registry-dog-view btn-ghost btn-sm">View profile</span>
    </a>
</article>
