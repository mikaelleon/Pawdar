<?php
require_once __DIR__ . '/includes/app-layout.php';
app_layout_start('breeds', 'Breed Directory', [
    'topbarTitle' => 'Breed Directory',
    'searchPlaceholder' => 'Search breeds…',
]);
?>

<div class="split-layout">
    <div class="split-main">
        <div class="search-bar search-bar-light hidden-mobile mb-md">
            <i data-lucide="search"></i>
            <span class="text-muted text-sm">Search breeds…</span>
        </div>
        <div class="chips-row mb-md">
            <span class="chip chip-active">All Sizes</span>
            <span class="chip chip-outline">Small</span>
            <span class="chip chip-outline">Medium</span>
            <span class="chip chip-outline">Large</span>
        </div>
        <div class="breed-grid">
            <a href="dog-profile.php" class="breed-card is-selected">
                <div class="breed-card-image"><i data-lucide="dog" style="width:46px;height:46px;color:var(--muted-teal);"></i></div>
                <div class="card-body">
                    <div style="font-weight:800;font-size:16px;">Aspin (Asong Pinoy)</div>
                    <div class="flex items-center gap-sm mt-sm"><span class="badge badge-owned">Medium</span><span class="text-xs text-muted">Loyal &amp; alert</span></div>
                    <div class="text-muted" style="font-weight:800;font-size:13px;margin-top:10px;">Viewing →</div>
                </div>
            </a>
            <div class="breed-card">
                <div class="breed-card-image" style="background:#EEF4EA;"><i data-lucide="dog" style="width:46px;height:46px;color:var(--muted-teal);"></i></div>
                <div class="card-body">
                    <div style="font-weight:800;font-size:16px;">Labrador Retriever</div>
                    <div class="flex items-center gap-sm mt-sm"><span class="badge badge-owned">Large</span><span class="text-xs text-muted">Friendly</span></div>
                </div>
            </div>
            <div class="breed-card">
                <div class="breed-card-image" style="background:#EEF4EA;"><i data-lucide="dog" style="width:46px;height:46px;color:var(--muted-teal);"></i></div>
                <div class="card-body">
                    <div style="font-weight:800;font-size:16px;">Shih Tzu</div>
                    <div class="flex items-center gap-sm mt-sm"><span class="badge badge-owned">Small</span><span class="text-xs text-muted">Affectionate</span></div>
                </div>
            </div>
            <div class="breed-card">
                <div class="breed-card-image" style="background:#EEF4EA;"><i data-lucide="dog" style="width:46px;height:46px;color:var(--muted-teal);"></i></div>
                <div class="card-body">
                    <div style="font-weight:800;font-size:16px;">German Shepherd</div>
                    <div class="flex items-center gap-sm mt-sm"><span class="badge badge-owned">Large</span><span class="text-xs text-muted">Protective</span></div>
                </div>
            </div>
        </div>
    </div>

    <aside class="split-panel">
        <div class="card card-bordered card-body">
            <h2 style="font-weight:800;font-size:22px;margin:0;letter-spacing:-.3px;">Aspin (Asong Pinoy)</h2>
            <span class="badge badge-owned mt-sm">Medium · 12–18 kg</span>

            <div class="label-upper mt-md mb-md">Temperament</div>
            <?php
            $traits = [
                ['shield', 'Loyalty', 4],
                ['zap', 'Energy', 4],
                ['smile', 'Friendliness', 3],
            ];
            foreach ($traits as [$icon, $label, $filled]): ?>
                <div class="flex items-center gap-md mb-md">
                    <i data-lucide="<?= $icon ?>" style="color:var(--muted-teal);"></i>
                    <div class="flex-1 text-sm" style="font-weight:700;"><?= $label ?></div>
                    <div class="rating-dots">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <span class="rating-dot<?= $i >= $filled ? ' empty' : '' ?>"></span>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="label-upper mt-md mb-md">Common Health Risks</div>
            <ul class="text-sm" style="padding:0;margin:0;list-style:none;display:flex;flex-direction:column;gap:9px;">
                <li class="flex items-center gap-sm"><i data-lucide="alert-triangle" style="color:var(--sunlit-clay);"></i> Skin allergies &amp; hot spots</li>
                <li class="flex items-center gap-sm"><i data-lucide="alert-triangle" style="color:var(--sunlit-clay);"></i> Tick-borne disease</li>
                <li class="flex items-center gap-sm"><i data-lucide="alert-triangle" style="color:var(--sunlit-clay);"></i> Heat exhaustion</li>
            </ul>

            <div class="label-upper mt-md mb-md">Dogs of This Breed on Pawdar</div>
            <div class="flex flex-col gap-sm">
                <div class="flex items-center gap-sm" style="background:#EEF4EA;border-radius:10px;padding:9px 11px;">
                    <div class="icon-box icon-box-sm" style="background:var(--muted-teal);color:#fff;width:32px;height:32px;"><i data-lucide="dog" style="width:17px;height:17px;"></i></div>
                    <div class="flex-1"><div class="text-sm" style="font-weight:800;">Bantay</div><div class="text-xs text-muted">R. Castillo</div></div>
                    <span class="badge badge-verified">Registered</span>
                </div>
                <div class="flex items-center gap-sm" style="background:#EEF4EA;border-radius:10px;padding:9px 11px;">
                    <div class="icon-box icon-box-sm" style="background:var(--muted-teal);color:#fff;width:32px;height:32px;"><i data-lucide="dog"></i></div>
                    <div class="flex-1"><div class="text-sm" style="font-weight:800;">Bruno</div><div class="text-xs text-muted">J. Mendoza</div></div>
                    <span class="badge badge-investigating">Pending</span>
                </div>
            </div>
        </div>
    </aside>
</div>

<?php app_layout_end(false); ?>
