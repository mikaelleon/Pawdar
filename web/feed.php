<?php
require_once __DIR__ . '/includes/app-layout.php';
app_layout_start('feed', 'Home Feed');

require __DIR__ . '/partials/incident-cards.php';
?>

<div class="feed-layout">
    <div class="feed-column">
        <div class="feed-header">
            <div>
                <h1 class="feed-title">Nearby Incidents</h1>
                <p class="text-sm text-muted">Within 5 km · Brgy. San Roque</p>
            </div>
            <a href="report.php" class="btn-primary hidden-mobile" style="height:44px;padding:0 20px;font-size:14px;">
                <i data-lucide="plus"></i> Report Incident
            </a>
        </div>

        <div class="chips-row scr">
            <span class="chip chip-active">All</span>
            <span class="chip chip-outline">Animal Bite</span>
            <span class="chip chip-outline">Injured Stray</span>
            <span class="chip chip-outline">Aggressive</span>
            <span class="chip chip-outline">Vehicular</span>
        </div>

        <div class="flex flex-col gap-md">
            <?php render_incident_cards(); ?>
        </div>
    </div>

    <aside class="map-preview-col hidden-mobile">
        <div style="font-weight:800;font-size:15px;color:var(--air-force);">Map preview</div>
        <div class="map-preview">
            <div style="position:absolute;top:70px;left:-10px;right:-10px;height:12px;background:#fff;transform:rotate(-7deg);"></div>
            <div style="position:absolute;inset:0;background:var(--tea-green);opacity:.15;"></div>
            <div class="map-pin map-pin-drop accent-bite" style="left:60px;top:60px;width:26px;height:26px;background:var(--burnt-peach);"></div>
            <div class="map-pin map-pin-cluster" style="left:120px;top:160px;background:var(--air-force);font-size:13px;">7</div>
        </div>
        <div class="grid-2x2">
            <div class="summary-card" style="padding:12px;border-top-color:var(--burnt-peach);"><div class="summary-value" style="font-size:22px;">3</div><div class="summary-label" style="font-size:11px;">Bites</div></div>
            <div class="summary-card" style="padding:12px;border-top-color:var(--sunlit-clay);"><div class="summary-value" style="font-size:22px;">5</div><div class="summary-label" style="font-size:11px;">Strays</div></div>
            <div class="summary-card" style="padding:12px;border-top-color:var(--air-force);"><div class="summary-value" style="font-size:22px;">2</div><div class="summary-label" style="font-size:11px;">Aggressive</div></div>
            <div class="summary-card" style="padding:12px;"><div class="summary-value" style="font-size:22px;">2</div><div class="summary-label" style="font-size:11px;">Vehicular</div></div>
        </div>
        <a href="map.php" class="btn-outline btn-block btn-sm" style="height:42px;">
            <i data-lucide="map"></i> Open full map
        </a>
    </aside>
</div>

<?php app_layout_end(true); ?>
