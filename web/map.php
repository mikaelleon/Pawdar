<?php
require_once __DIR__ . '/includes/app-layout.php';
app_layout_start('map', 'Incident Map', ['showSearch' => false]);
?>

<div class="map-full" style="margin:-28px -32px;min-height:calc(100vh - 64px);">
    <div style="position:absolute;inset:0;background:
        repeating-linear-gradient(0deg, transparent 0 58px, rgba(135,175,174,.25) 58px 60px),
        repeating-linear-gradient(90deg, transparent 0 58px, rgba(135,175,174,.25) 58px 60px),
        var(--bg-map);">
        <div style="position:absolute;top:180px;left:-20px;right:-20px;height:16px;background:#fff;transform:rotate(-8deg);"></div>
        <div style="position:absolute;top:480px;left:-20px;right:-20px;height:22px;background:#fff;transform:rotate(5deg);"></div>
        <div style="position:absolute;inset:0;background:var(--tea-green);opacity:.15;"></div>
    </div>

    <div class="map-pin map-pin-drop" style="left:96px;top:250px;background:var(--burnt-peach);"><i data-lucide="dog" style="width:16px;height:16px;color:#fff;"></i></div>
    <div class="map-pin map-pin-cluster" style="left:230px;top:330px;background:var(--air-force);">7</div>
    <div class="map-pin map-pin-drop" style="left:170px;top:430px;background:var(--sunlit-clay);"><i data-lucide="bandage" style="width:15px;height:15px;"></i></div>
    <div class="map-pin map-pin-drop" style="left:285px;top:540px;background:var(--muted-teal);"><i data-lucide="car" style="width:15px;height:15px;color:#fff;"></i></div>

    <div class="map-overlay-top hidden-mobile" style="top:16px;right:352px;">
        <div class="card card-body flex items-center gap-md" style="box-shadow:var(--shadow-sm);padding:12px 14px;">
            <div class="flex-1 flex items-center gap-sm"><i data-lucide="search"></i><span class="text-muted text-sm">Search this area…</span></div>
            <span class="chip chip-active" style="padding:7px 14px;font-size:12px;">All</span>
            <span class="chip" style="padding:7px 14px;font-size:12px;background:var(--bg-soft);">Bite</span>
            <div style="background:var(--bg-soft);border-radius:10px;padding:3px;display:flex;gap:3px;">
                <span style="padding:6px 12px;border-radius:8px;background:var(--air-force);color:#fff;font-weight:700;font-size:12px;">Normal</span>
                <span style="padding:6px 12px;font-weight:700;font-size:12px;color:var(--air-force);">Heatmap</span>
            </div>
        </div>
    </div>

    <div class="map-overlay-top hidden-desktop">
        <div class="card card-body flex items-center gap-sm" style="padding:12px 14px;">
            <i data-lucide="search"></i>
            <span class="text-muted text-sm flex-1">Search this area…</span>
            <div class="icon-box icon-box-sm"><i data-lucide="sliders-horizontal"></i></div>
        </div>
        <div class="chips-row scr" style="margin-top:10px;">
            <span class="chip chip-active" style="font-size:12px;padding:7px 14px;">All</span>
            <span class="chip" style="font-size:12px;padding:7px 14px;background:#fff;box-shadow:0 1px 4px rgba(74,67,67,.1);">Bite</span>
            <span class="chip" style="font-size:12px;padding:7px 14px;background:#fff;">Injured</span>
        </div>
    </div>

    <a href="report.php" class="btn-primary hidden-mobile" style="position:absolute;bottom:24px;right:368px;height:48px;padding:0 20px;font-size:14px;">
        <i data-lucide="plus"></i> Report Incident
    </a>

    <aside class="map-side-panel hidden-mobile">
        <div style="padding:20px 18px 14px;">
            <div style="font-weight:800;font-size:18px;">Incidents in View</div>
            <div class="text-sm text-muted">12 incidents · last 48h</div>
        </div>
        <div class="grid-2x2" style="padding:0 18px 14px;">
            <div class="summary-card" style="padding:11px;"><div class="summary-value" style="font-size:20px;">8</div><div class="summary-label" style="font-size:10px;">Received</div></div>
            <div class="summary-card investigating" style="padding:11px;"><div class="summary-value" style="font-size:20px;">5</div><div class="summary-label" style="font-size:10px;">Investigating</div></div>
        </div>
        <div class="flex flex-col gap-md scr" style="flex:1;overflow-y:auto;padding:4px 18px 18px;">
            <?php require __DIR__ . '/partials/map-incident-list.php'; ?>
        </div>
    </aside>

    <div class="map-bottom-sheet hidden-desktop">
        <div class="sheet-handle"></div>
        <div class="flex justify-between items-center">
            <div><div style="font-weight:800;font-size:17px;">12 incidents</div><div class="text-xs text-muted">in this area · last 48h</div></div>
            <span class="text-muted" style="font-weight:700;font-size:13px;">View list <i data-lucide="chevron-up"></i></span>
        </div>
        <div class="card" style="margin-top:14px;background:#EEF4EA;padding:10px 12px;display:flex;align-items:center;gap:10px;">
            <div style="width:5px;height:32px;border-radius:3px;background:var(--burnt-peach);"></div>
            <div class="flex-1"><div class="text-sm" style="font-weight:800;">Loose dog bit a jogger</div><div class="text-xs text-muted">Riverside Park · 0.4 km</div></div>
            <span class="badge badge-investigating">Investigating</span>
        </div>
    </div>
</div>

<?php
$userRole = current_user_role();
$mapFab = role_can_report($userRole)
    ? ['show' => true, 'label' => 'Report', 'href' => 'feed.php']
    : ['show' => false];
app_layout_end($mapFab);
?>
