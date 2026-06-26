<?php
require_once __DIR__ . '/includes/app-layout.php';
app_layout_start('cases', 'Report Details', [
    'showSearch' => false,
    'mobileHeader' => 'back',
    'backTitle' => 'Report Incident',
    'backHref' => 'report.php',
]);
?>

<div style="max-width:640px;margin:0 auto;">
    <div class="report-step-bar hidden-desktop">
        <div class="step-dot"></div>
        <div class="step-dot"></div>
        <div class="step-dot active"></div>
    </div>

    <div class="label-upper">Step 3</div>
    <h1 style="font-size:22px;font-weight:800;margin:4px 0 18px;letter-spacing:-.3px;">Add the details</h1>

    <div class="upload-zone">
        <div class="icon-box" style="width:54px;height:54px;border-radius:16px;background:#fff;box-shadow:var(--shadow-sm);"><i data-lucide="camera" style="color:var(--muted-teal);"></i></div>
        <span class="text-muted" style="font-weight:700;font-size:14px;">Add Photo or Video</span>
    </div>

    <div class="mt-md">
        <div class="flex justify-between items-center mb-md">
            <span class="label-upper">Description</span>
            <span class="text-xs text-muted">86 / 280</span>
        </div>
        <textarea class="form-input" rows="5">A brown medium dog without a collar bit a jogger on the calf, then ran toward the creek.</textarea>
    </div>

    <div class="card card-bordered card-body flex items-center gap-md mt-md">
        <div class="icon-box icon-box-sm"><i data-lucide="dog"></i></div>
        <div class="flex-1">
            <div class="text-xs text-muted">Dog involved (optional)</div>
            <div style="font-weight:800;font-size:14px;">Not in registry · Unknown</div>
        </div>
        <i data-lucide="chevron-right" style="color:var(--muted-teal);"></i>
    </div>

    <a href="feed.php" class="btn-primary btn-block mt-lg" style="margin-top:24px;">
        <i data-lucide="send"></i> Submit Report
    </a>
</div>

<?php app_layout_end([]); ?>
