<?php
require_once __DIR__ . '/includes/app-layout.php';
app_layout_start('cases', 'Report Incident', [
    'showSearch' => false,
    'mobileHeader' => 'back',
    'backTitle' => 'Report Incident',
    'backHref' => 'feed.php',
]);
?>

<div style="max-width:640px;margin:0 auto;">
    <div class="report-step-bar hidden-mobile" style="justify-content:flex-start;margin-bottom:36px;">
        <div class="flex items-center gap-sm"><div class="step-circle" style="width:32px;height:32px;font-size:14px;">1</div><span style="font-weight:800;">What Happened</span></div>
        <div style="flex:1;height:2px;background:#E7EFE6;margin:0 14px;"></div>
        <div class="flex items-center gap-sm"><div style="width:32px;height:32px;border-radius:50%;border:1.5px solid var(--muted-teal);display:flex;align-items:center;justify-content:center;font-weight:800;color:var(--air-force);">2</div><span class="text-muted" style="font-weight:700;">Where &amp; When</span></div>
        <div style="flex:1;height:2px;background:#E7EFE6;margin:0 14px;"></div>
        <div class="flex items-center gap-sm"><div style="width:32px;height:32px;border-radius:50%;border:1.5px solid var(--muted-teal);display:flex;align-items:center;justify-content:center;font-weight:800;color:var(--air-force);">3</div><span class="text-muted" style="font-weight:700;">Details</span></div>
    </div>

    <div class="report-step-bar hidden-desktop">
        <div class="step-dot active"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
    </div>

    <div class="label-upper">Step 1</div>
    <h1 style="font-size:22px;font-weight:800;margin:4px 0 16px;letter-spacing:-.3px;">What happened?</h1>
    <p class="text-sm text-muted hidden-mobile" style="margin-bottom:22px;">Pick the incident type that best matches what you saw.</p>

    <div class="incident-type-grid incident-type-grid-desktop">
        <div class="type-card is-selected">
            <div style="position:absolute;top:8px;right:8px;width:22px;height:22px;border-radius:50%;background:var(--burnt-peach);display:flex;align-items:center;justify-content:center;"><i data-lucide="check" style="width:14px;height:14px;color:#fff;"></i></div>
            <div class="type-card-icon" style="background:rgba(224,118,94,.14);"><i data-lucide="dog" style="color:var(--burnt-peach);"></i></div>
            <span style="font-weight:800;font-size:14px;">Animal Bite</span>
        </div>
        <div class="type-card">
            <div class="type-card-icon" style="background:rgba(248,188,114,.18);"><i data-lucide="bandage" style="color:var(--sunlit-clay);"></i></div>
            <span style="font-weight:800;font-size:14px;">Injured Stray</span>
        </div>
        <div class="type-card">
            <div class="type-card-icon" style="background:rgba(108,139,159,.16);"><i data-lucide="alert-triangle" style="color:var(--air-force);"></i></div>
            <span style="font-weight:800;font-size:14px;">Aggressive</span>
        </div>
        <div class="type-card">
            <div class="type-card-icon" style="background:rgba(135,175,174,.2);"><i data-lucide="car" style="color:var(--muted-teal);"></i></div>
            <span style="font-weight:800;font-size:14px;">Vehicular</span>
        </div>
        <div class="type-card">
            <div class="type-card-icon" style="background:rgba(74,67,67,.1);"><i data-lucide="footprints"></i></div>
            <span style="font-weight:800;font-size:14px;">Disturbance</span>
        </div>
        <div class="type-card" style="border:1.5px dashed var(--muted-teal);background:transparent;box-shadow:none;">
            <i data-lucide="plus" style="color:var(--air-force);"></i>
            <span class="text-muted" style="font-weight:700;font-size:13px;text-align:center;">Add more details</span>
        </div>
    </div>

    <div class="flex justify-between items-center mt-lg" style="margin-top:36px;">
        <a href="feed.php" class="text-muted hidden-mobile" style="font-weight:700;display:flex;align-items:center;gap:8px;"><i data-lucide="arrow-left"></i> Back</a>
        <a href="report-details.php" class="btn-primary" style="margin-left:auto;">
            Next <i data-lucide="arrow-right"></i>
        </a>
    </div>
</div>

<?php app_layout_end([]); ?>
