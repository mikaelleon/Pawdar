<?php
require_once __DIR__ . '/includes/app-layout.php';
app_layout_start('registry', 'Dog Profile', [
    'showSearch' => false,
    'mobileHeader' => 'back',
    'backTitle' => 'Registry',
    'backHref' => 'feed.php',
]);
?>

<div class="profile-layout">
    <div class="profile-main">
        <div class="card card-bordered profile-hero hidden-mobile" style="flex-direction:row;text-align:left;padding:24px;align-items:center;gap:20px;">
            <div class="icon-box icon-box-lg" style="background:var(--muted-teal);color:#fff;width:120px;height:120px;border:4px solid #fff;box-shadow:0 4px 12px rgba(74,67,67,.15);">
                <i data-lucide="dog" style="width:58px;height:58px;"></i>
            </div>
            <div>
                <h1 style="font-weight:800;font-size:30px;letter-spacing:-.5px;margin:0;">Bantay</h1>
                <div class="flex items-center gap-sm mt-sm">
                    <span class="badge badge-owned">Owned</span>
                    <span class="text-sm">Aspin (Asong Pinoy) · Male</span>
                </div>
                <div class="text-xs text-muted mt-sm">Registry ID · PWD-2024-00831</div>
            </div>
        </div>

        <div class="profile-hero hidden-desktop">
            <div class="icon-box icon-box-lg" style="background:var(--muted-teal);color:#fff;border:4px solid #fff;box-shadow:0 4px 12px rgba(74,67,67,.15);">
                <i data-lucide="dog" style="width:48px;height:48px;"></i>
            </div>
            <h1 class="profile-name">Bantay</h1>
            <div class="flex items-center gap-sm">
                <span class="badge badge-owned">Owned</span>
                <span class="text-sm">Aspin (Asong Pinoy) · Male</span>
            </div>
            <div class="text-xs text-muted mt-sm">Registry ID · PWD-2024-00831</div>
        </div>

        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Owner</div>
            <div class="flex items-center gap-md">
                <div class="avatar avatar-lg">RC</div>
                <div class="flex-1">
                    <div style="font-weight:800;font-size:15px;">Rosa Castillo</div>
                    <div class="text-xs text-muted">Dog Owner · Brgy. San Roque</div>
                </div>
                <div class="icon-box icon-box-sm hidden-mobile"><i data-lucide="phone"></i></div>
                <a href="tel:09175550142" class="btn-outline btn-sm hidden-desktop">Call</a>
            </div>
        </div>

        <div class="card card-bordered card-body">
            <div class="flex justify-between items-center mb-md">
                <div class="label-upper">Vaccination</div>
                <span class="badge badge-verified"><i data-lucide="check" style="width:13px;height:13px;"></i> Verified</span>
            </div>
            <div class="flex items-center gap-md">
                <div class="icon-box icon-box-sm"><i data-lucide="syringe"></i></div>
                <div>
                    <div style="font-weight:800;font-size:15px;">Anti-Rabies · Annual</div>
                    <div class="text-xs text-muted mt-sm">Verified by Dr. A. Lim · 14 Mar 2026</div>
                </div>
            </div>
        </div>

        <div class="card card-bordered card-body hidden-mobile">
            <div class="label-upper mb-md">Breed Info</div>
            <?php require __DIR__ . '/partials/breed-info-grid.php'; ?>
        </div>

        <div>
            <div style="font-size:14px;font-weight:800;color:var(--air-force);margin-bottom:10px;">Past Incidents</div>
            <div class="flex flex-col gap-md">
                <div class="card card-bordered card-body flex items-center gap-md">
                    <span class="badge badge-aggressive">Aggressive</span>
                    <div class="flex-1"><div class="text-sm" style="font-weight:700;">Barking at delivery rider</div><div class="text-xs text-muted">08 Feb 2026</div></div>
                    <span class="badge badge-resolved">Resolved</span>
                </div>
                <div class="card card-bordered card-body flex items-center gap-md">
                    <span class="badge badge-bite">Bite</span>
                    <div class="flex-1"><div class="text-sm" style="font-weight:700;">Nipped a child at the gate</div><div class="text-xs text-muted">22 Nov 2025</div></div>
                    <span class="badge badge-resolved">Resolved</span>
                </div>
            </div>
        </div>

        <a href="report.php" class="btn-ghost hidden-mobile" style="align-self:flex-start;">
            <i data-lucide="flag"></i> Flag This Dog
        </a>
    </div>

    <aside class="profile-side">
        <div class="card card-bordered card-body text-center">
            <div class="qr-box" style="margin:0 auto;">
                <div style="grid-column:1/4;grid-row:1/4;border:5px solid var(--taupe);border-radius:4px;"></div>
                <div style="grid-column:5/8;grid-row:1/4;border:5px solid var(--taupe);border-radius:4px;"></div>
                <div style="grid-column:1/4;grid-row:5/8;border:5px solid var(--taupe);border-radius:4px;"></div>
            </div>
            <div style="font-weight:800;font-size:15px;margin-top:14px;">Scan to View Profile</div>
            <div class="text-xs text-muted">PWD-2024-00831</div>
        </div>
        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Breed Info</div>
            <?php require __DIR__ . '/partials/breed-info-grid.php'; ?>
        </div>
    </aside>
</div>

<div class="sticky-cta hidden-desktop">
    <a href="report.php" class="btn-ghost btn-block" style="height:48px;">
        <i data-lucide="flag"></i> Flag This Dog
    </a>
</div>

<?php app_layout_end(false); ?>
