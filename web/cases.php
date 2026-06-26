<?php
require_once __DIR__ . '/includes/app-layout.php';
app_layout_start('cases', 'Case Management', [
    'showSearch' => false,
    'topbarTitle' => 'Case Management',
    'mobileHeader' => 'cases',
]);
?>

<div class="summary-strip scr">
    <div class="summary-card"><div class="summary-value">8</div><div class="summary-label">Received</div></div>
    <div class="summary-card investigating"><div class="summary-value">5</div><div class="summary-label">Investigating</div></div>
    <div class="summary-card resolved"><div class="summary-value">23</div><div class="summary-label">Resolved</div></div>
    <div class="summary-card rabies"><div class="summary-value">2</div><div class="summary-label">Rabies Watch</div></div>
</div>

<div class="hidden-mobile cases-table">
    <div class="cases-table-header">
        <div>Case ID</div><div>Incident Type</div><div>Dog</div><div>Reporter</div><div>Filed</div><div>Status</div><div>Action</div>
    </div>
    <div class="cases-table-row">
        <div class="text-xs text-muted">PWD-0412</div>
        <div class="flex items-center gap-sm"><span style="width:8px;height:8px;border-radius:50%;background:var(--burnt-peach);"></span><span style="font-weight:800;">Animal Bite</span><span class="badge badge-bite">Day 7/14</span></div>
        <div style="font-style:italic;font-weight:700;">Bantay</div>
        <div class="text-sm">R. Castillo</div>
        <div class="text-xs text-muted">17 Jun</div>
        <div><span class="badge badge-investigating">Investigating</span></div>
        <div class="flex gap-sm"><a href="case-detail.php" class="text-muted" style="font-weight:800;font-size:12px;">View</a></div>
    </div>
    <div class="cases-table-row" style="border-left-color:var(--muted-teal);">
        <div class="text-xs text-muted">PWD-0408</div>
        <div class="flex items-center gap-sm"><span style="width:8px;height:8px;border-radius:50%;background:var(--air-force);"></span><span style="font-weight:800;">Aggressive Behavior</span></div>
        <div style="font-style:italic;font-weight:700;">Unknown</div>
        <div class="text-sm">J. Dela Cruz</div>
        <div class="text-xs text-muted">15 Jun</div>
        <div><span class="badge badge-received">Received</span></div>
        <div><a href="case-detail.php" class="text-muted" style="font-weight:800;font-size:12px;">View</a></div>
    </div>
    <div class="cases-table-row" style="border-left-color:var(--tea-green);">
        <div class="text-xs text-muted">PWD-0399</div>
        <div class="flex items-center gap-sm"><span style="font-weight:800;">Injured Stray</span></div>
        <div style="font-style:italic;font-weight:700;">Rescued #41</div>
        <div class="text-sm">Anonymous</div>
        <div class="text-xs text-muted">11 Jun</div>
        <div><span class="badge badge-resolved">Resolved</span></div>
        <div><a href="case-detail.php" class="text-muted" style="font-weight:800;font-size:12px;">View</a></div>
    </div>
</div>

<div class="hidden-desktop flex flex-col gap-md">
    <a href="case-detail.php" class="incident-card card-bordered">
        <div class="accent accent-bite"></div>
        <div class="card-body" style="flex:1;">
            <div class="flex justify-between items-center">
                <span class="text-xs text-muted">CASE #PWD-2026-0412</span>
                <span class="badge badge-bite"><i data-lucide="clock" style="width:12px;height:12px;"></i> Day 7 of 14</span>
            </div>
            <div style="font-weight:800;font-size:16px;margin-top:7px;">Animal Bite</div>
            <div class="text-sm text-muted"><em style="color:var(--taupe);font-weight:700;">Bantay</em> · reported by R. Castillo · 17 Jun</div>
            <div class="flex justify-between items-center" style="margin-top:13px;padding-top:12px;border-top:1px solid var(--border);">
                <span class="badge badge-investigating">Investigating</span>
                <i data-lucide="chevron-right" style="color:var(--muted-teal);"></i>
            </div>
        </div>
    </a>
    <div class="incident-card card-bordered">
        <div class="accent accent-teal"></div>
        <div class="card-body" style="flex:1;">
            <span class="text-xs text-muted">CASE #PWD-2026-0408</span>
            <div style="font-weight:800;font-size:16px;margin-top:7px;">Aggressive Behavior</div>
            <div class="text-sm text-muted"><em>Unknown</em> · J. Dela Cruz · 15 Jun</div>
            <div style="margin-top:13px;padding-top:12px;border-top:1px solid var(--border);"><span class="badge badge-received">Received</span></div>
        </div>
    </div>
</div>

<?php app_layout_end(false); ?>
