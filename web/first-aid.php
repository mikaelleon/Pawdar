<?php
require_once __DIR__ . '/includes/app-layout.php';
app_layout_start('first-aid', 'First Aid Guides', ['showSearch' => false, 'topbarTitle' => 'First Aid Guides']);
?>

<div class="split-layout">
    <div style="width:380px;flex:none;" class="hidden-mobile">
        <h1 style="font-weight:800;font-size:24px;margin:0;letter-spacing:-.3px;">First Aid Guides</h1>
        <p class="text-sm text-muted mt-sm" style="margin-bottom:18px;">Know what to do before help arrives.</p>
        <div class="first-aid-list scr" style="max-height:620px;overflow-y:auto;">
            <?php require __DIR__ . '/partials/first-aid-list.php'; ?>
        </div>
    </div>

    <div class="guide-panel flex-1">
        <div class="card-body scr" style="flex:1;overflow-y:auto;padding:24px 26px;">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-md">
                    <div class="type-card-icon" style="background:rgba(224,118,94,.14);"><i data-lucide="dog" style="color:var(--burnt-peach);"></i></div>
                    <h2 style="font-weight:800;font-size:24px;margin:0;letter-spacing:-.3px;">Animal Bite First Aid</h2>
                </div>
                <span class="badge badge-bite">Severe</span>
            </div>
            <p class="text-xs text-muted mt-sm flex items-center gap-sm"><i data-lucide="book-open"></i> Source: WHO &amp; PH Dept. of Health rabies protocol (2024)</p>

            <div class="warning-box mt-md">
                <i data-lucide="alert-triangle" style="width:24px;height:24px;color:var(--sunlit-clay);flex:none;"></i>
                <div class="text-sm" style="font-weight:800;">Seek immediate veterinary or medical attention — any bite carries rabies risk.</div>
            </div>

            <div class="step-list mt-md">
                <div class="step-row"><div class="step-circle">1</div><div class="text-sm" style="font-weight:700;padding-top:6px;">Wash the wound immediately with soap and running water for at least 15 minutes.</div></div>
                <div class="step-row"><div class="step-circle">2</div><div class="text-sm" style="font-weight:700;padding-top:6px;">Apply an antiseptic such as povidone-iodine or alcohol to the cleaned area.</div></div>
                <div class="step-row"><div class="step-circle">3</div><div class="text-sm" style="font-weight:700;padding-top:6px;">Control bleeding with a clean cloth and gentle pressure; do not close deep wounds.</div></div>
                <div class="step-row"><div class="step-circle">4</div><div class="text-sm" style="font-weight:700;padding-top:6px;">Note the dog's description and location, then go to the nearest clinic for anti-rabies evaluation.</div></div>
            </div>
        </div>
        <div style="border-top:1px solid var(--border);padding:16px 26px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <button type="button" class="btn-outline btn-sm"><i data-lucide="download"></i> Download as PDF</button>
            <a href="report.php" class="btn-primary btn-sm"><i data-lucide="plus"></i> Report This Incident</a>
        </div>
    </div>
</div>

<div class="hidden-desktop mt-md">
    <?php require __DIR__ . '/partials/first-aid-list.php'; ?>
</div>

<?php app_layout_end(false); ?>
