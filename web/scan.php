<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/dogs.php';
require_once __DIR__ . '/includes/helpers.php';

$registryId = trim((string) ($_GET['id'] ?? ''));
$pdo = db();
$dog = $registryId !== '' ? fetch_dog_by_registry_id($pdo, $registryId) : null;

$pageTitle = 'Dog Registry Tag · ' . SITE_NAME;
require __DIR__ . '/includes/head.php';
?>

<body class="scan-page">
<div class="scan-wrap">
    <div class="flex items-center justify-center gap-sm mb-md">
        <div class="logo-mark"><i data-lucide="paw-print"></i></div>
        <span class="logo-text">Pawdar</span>
    </div>
    <p class="text-sm text-muted text-center mb-md">Community dog registry</p>

    <?php if (!$dog): ?>
        <div class="card card-bordered card-body text-center">
            <h1 class="feed-title" style="font-size:20px;">Registry ID not found</h1>
            <p class="text-sm text-muted mt-sm">This tag may be outdated or not yet registered.</p>
            <a href="report.php" class="btn-outline btn-sm mt-md">Report this dog as found</a>
        </div>
    <?php else:
        $dogType = (string) ($dog['DogType'] ?? 'Owned');
        $vax = vaccine_status_badge($dog['vax_status'] ?? null);
        $ownerFirst = explode(' ', trim((string) ($dog['owner_name'] ?? '')))[0] ?? 'Owner';
    ?>
        <div class="card card-bordered card-body text-center">
            <div class="scan-avatar <?= htmlspecialchars(string_color_class((string) ($dog['Breed'] ?? 'dog'))) ?>">
                <i data-lucide="dog" style="width:48px;height:48px;color:#fff;"></i>
            </div>
            <h1 style="font-weight:800;font-size:28px;margin-top:16px;"><?= htmlspecialchars((string) $dog['DogName']) ?></h1>
            <span class="badge <?= htmlspecialchars(dog_type_chip_class($dogType)) ?>"><?= htmlspecialchars($dogType) ?></span>
            <p class="text-sm text-muted mt-sm"><?= htmlspecialchars((string) ($dog['Breed'] ?? '')) ?> · <?= htmlspecialchars((string) ($dog['Gender'] ?? '')) ?></p>

            <?php if ($dogType === 'Owned'): ?>
                <hr style="border:none;border-top:1px solid var(--border-light);margin:20px 0;">
                <div class="label-upper mb-sm">Contact owner</div>
                <p class="text-sm"><?= htmlspecialchars($ownerFirst) ?> · Brgy. <?= htmlspecialchars((string) ($dog['owner_barangay'] ?? '')) ?></p>
                <button type="button" class="btn-primary btn-sm mt-md" id="reveal-phone" data-phone="<?= htmlspecialchars((string) ($dog['owner_phone'] ?? '')) ?>">Call owner</button>
                <p class="text-xs text-muted mt-sm" id="phone-display" hidden></p>
                <div class="mt-md">
                    <span class="badge <?= htmlspecialchars($vax['class']) ?>"><?= htmlspecialchars($vax['label']) ?></span>
                    <?php if (!empty($dog['VaccineName'])): ?>
                        <p class="text-xs text-muted mt-sm"><?= htmlspecialchars((string) $dog['VaccineName']) ?> · <?= htmlspecialchars((string) ($dog['DateGiven'] ?? '')) ?></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-sm mt-md">This is a registered stray on Pawdar.</p>
                <a href="report.php?type=injured_stray" class="btn-primary btn-sm mt-md">Report a sighting</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <p class="text-xs text-muted text-center mt-md">Powered by Pawdar · Batangas community dog registry</p>
</div>
<script>
document.getElementById('reveal-phone')?.addEventListener('click', function () {
    var phone = this.getAttribute('data-phone');
    var el = document.getElementById('phone-display');
    if (!phone || !el) return;
    el.hidden = false;
    el.innerHTML = '<a href="tel:' + phone + '">' + phone + '</a>';
    this.textContent = 'Call owner';
});
</script>
<?php require __DIR__ . '/includes/foot.php'; ?>
