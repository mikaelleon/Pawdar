<div class="flex justify-between items-start">
    <div>
        <span class="text-xs text-muted">CASE #PWD-2026-0412</span>
        <h1 style="font-weight:800;font-size:21px;margin:3px 0;letter-spacing:-.3px;">Animal Bite</h1>
    </div>
    <span class="badge badge-bite"><i data-lucide="clock" style="width:13px;height:13px;"></i> Day 7 / 14</span>
</div>

<div class="card card-bordered card-body mt-md">
    <div class="flex items-center gap-sm mb-md"><i data-lucide="dog" style="color:var(--muted-teal);"></i><span class="text-sm" style="font-weight:700;">Bantay · Aspin · PWD-2024-00831</span></div>
    <div class="flex items-center gap-sm mb-md"><i data-lucide="map-pin" style="color:var(--muted-teal);"></i><span class="text-sm" style="font-weight:700;">Riverside Park, Brgy. San Roque</span></div>
    <div class="flex items-center gap-sm"><i data-lucide="user" style="color:var(--muted-teal);"></i><span class="text-sm" style="font-weight:700;">Reported by Rosa Castillo · 17 Jun, 8:42am</span></div>
</div>

<div class="label-upper mt-md mb-md">Update status</div>
<div class="status-pills">
    <span class="status-pill active-received">Received</span>
    <span class="status-pill active-investigating">Investigating</span>
    <span class="status-pill">Resolved</span>
    <span class="status-pill">Referred</span>
</div>

<div class="flex justify-between items-center mt-md mb-md">
    <span class="label-upper">Rabies monitoring · 14 days</span>
    <span class="text-xs" style="color:var(--burnt-peach);font-weight:800;">7 / 14</span>
</div>

<div class="rabies-grid">
    <?php for ($d = 1; $d <= 6; $d++): ?>
        <div class="rabies-day done"><i data-lucide="check" style="width:15px;height:15px;"></i><span>D<?= $d ?></span></div>
    <?php endfor; ?>
    <div class="rabies-day today"><span>D7</span><span style="font-size:7px;">today</span></div>
    <?php for ($d = 8; $d <= 14; $d++): ?>
        <div class="rabies-day"><span>D<?= $d ?></span></div>
    <?php endfor; ?>
</div>
