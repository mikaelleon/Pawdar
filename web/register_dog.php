<?php

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/dogs.php';
require_once __DIR__ . '/includes/breeds.php';
require_role(['Dog Owner', 'Admin']);

$pdo = db();
$breeds = fetch_all_breeds($pdo);
$step = max(1, min(3, (int) ($_GET['step'] ?? 1)));
$success = isset($_GET['success']) && $_GET['success'] === '1';
$newDogId = (int) ($_GET['dog_id'] ?? 0);
$newRegistryId = '';

if ($success && $newDogId > 0) {
    $dog = fetch_dog_profile($pdo, $newDogId);
    $newRegistryId = (string) ($dog['RegistryID'] ?? '');
}

$pageTitle = 'Register Dog';
require __DIR__ . '/includes/head.php';
?>

<body class="auth-page" style="background:var(--bg-soft);">
<div class="auth-form-side" style="max-width:720px;margin:40px auto;padding:24px;">
    <?php
    $breadcrumbs = [
        ['label' => 'Registry', 'url' => 'registry.php'],
        ['label' => 'Register Dog'],
    ];
    require __DIR__ . '/partials/breadcrumb.php';
    ?>
    <a href="registry.php" class="flex items-center gap-sm mb-md text-sm" style="color:var(--air-force);font-weight:700;">
        <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back to registry
    </a>

    <?php if ($success && $newDogId > 0): ?>
        <div class="card card-bordered card-body text-center">
            <div class="icon-box icon-box-lg" style="margin:0 auto 16px;background:var(--tea-green);"><i data-lucide="check"></i></div>
            <h1 class="feed-title">Dog registered</h1>
            <p class="text-sm text-muted">Registry ID: <?= htmlspecialchars($newRegistryId) ?></p>
            <img src="qr.php?id=<?= urlencode($newRegistryId) ?>" alt="QR code" style="width:180px;height:180px;margin:20px auto;border-radius:12px;border:1px solid var(--border-light);">
            <div class="flex gap-md justify-center mt-md flex-wrap">
                <a href="qr.php?id=<?= urlencode($newRegistryId) ?>" download="pawdar-qr.png" class="btn-outline btn-sm">Download QR tag</a>
                <a href="dog-profile.php?id=<?= $newDogId ?>" class="btn-primary btn-sm">View dog profile</a>
            </div>
        </div>
    <?php else: ?>
        <div class="register-steps mb-md">
            <?php foreach ([1 => 'Basic info', 2 => 'Health records', 3 => 'Review'] as $num => $label): ?>
                <div class="register-step<?= $step === $num ? ' is-active' : ($step > $num ? ' is-done' : '') ?>">
                    <span class="register-step-circle"><?= $step > $num ? '✓' : $num ?></span>
                    <span class="register-step-label"><?= htmlspecialchars($label) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <form class="card card-bordered card-body" id="register-dog-form" method="post" action="ajax/register_dog.php" enctype="multipart/form-data" data-step="1">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $_SESSION['csrf_token']) ?>">

            <div data-form-step="1">
                <h2 class="feed-title" style="font-size:20px;">Basic info</h2>
                <label class="field-label">Dog name *</label>
                <input class="field-input mb-md" type="text" name="dog_name" required>

                <label class="field-label">Breed *</label>
                <input class="field-input mb-sm" type="text" name="breed_search" list="breed-list" placeholder="Search breeds…" required>
                <datalist id="breed-list">
                    <?php foreach ($breeds as $breed): ?>
                        <option value="<?= htmlspecialchars((string) $breed['breed_name']) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <p class="text-xs text-muted mb-md">No match? Enter breed name — admin will review.</p>

                <label class="field-label">Sex</label>
                <div class="flex gap-sm mb-md">
                    <label class="chip chip-outline"><input type="radio" name="gender" value="Male" checked> Male</label>
                    <label class="chip chip-outline"><input type="radio" name="gender" value="Female"> Female</label>
                </div>

                <label class="field-label">Age (years)</label>
                <input class="field-input mb-md" type="number" name="age" min="0" max="30">

                <label class="field-label">Dog type</label>
                <div class="flex gap-sm mb-md flex-wrap">
                    <label class="report-type-card"><input type="radio" name="dog_type" value="Owned" checked> Owned</label>
                    <label class="report-type-card"><input type="radio" name="dog_type" value="Rescued"> Rescued</label>
                </div>

                <label class="field-label">Photo (optional)</label>
                <input class="field-input mb-md" type="file" name="photo" accept="image/jpeg,image/png">
            </div>

            <div data-form-step="2" hidden>
                <h2 class="feed-title" style="font-size:20px;">Health records</h2>
                <div class="vaccine-block">
                    <label class="field-label">Vaccination name</label>
                    <input class="field-input mb-md" type="text" name="vaccine_name" placeholder="Anti-Rabies Vaccine">

                    <label class="field-label">Date given</label>
                    <input class="field-input mb-md" type="date" name="vaccine_date">

                    <label class="field-label">Next due date</label>
                    <input class="field-input mb-md" type="date" name="vaccine_due">

                    <label class="field-label">Veterinarian name</label>
                    <input class="field-input mb-md" type="text" name="vet_name">

                    <label class="field-label">Health notes</label>
                    <textarea class="field-input mb-md" name="health_notes" rows="3"></textarea>
                </div>
            </div>

            <div data-form-step="3" hidden>
                <h2 class="feed-title" style="font-size:20px;">Review and submit</h2>
                <div class="card card-body" style="background:var(--bg-soft);" id="register-review">
                    <p class="text-sm text-muted">Complete steps 1–2, then review here before submitting.</p>
                </div>
            </div>

            <div class="flex justify-between mt-md">
                <button type="button" class="btn-outline btn-sm" data-step-back hidden>Back</button>
                <button type="button" class="btn-primary btn-sm" data-step-next>Continue</button>
                <button type="submit" class="btn-primary btn-sm" data-step-submit hidden>Register dog</button>
            </div>
        </form>
    <?php endif; ?>
</div>
<script src="assets/js/register-dog.js"></script>
<?php require __DIR__ . '/includes/foot.php'; ?>
