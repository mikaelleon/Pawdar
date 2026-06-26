<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/first-aid-data.php';

$pdo = db();
$guides = fetch_first_aid_guides($pdo);
$selectedId = (int) ($_GET['id'] ?? ($guides[0]['guide_id'] ?? 0));
$selected = fetch_first_aid_guide($pdo, $selectedId) ?? ($guides ? fetch_first_aid_guide($pdo, (int) $guides[0]['guide_id']) : null);

app_layout_start('first-aid', 'First Aid Guides', [
    'showSearch' => false,
    'topbarTitle' => 'First Aid Guides',
    'scripts' => ['assets/js/first-aid.js'],
]);
?>

<div class="split-layout" data-first-aid-page>
    <div style="width:380px;flex:none;" class="hidden-mobile">
        <h1 style="font-weight:500;font-size:24px;margin:0;">First Aid Guides</h1>
        <p class="text-sm text-muted mt-sm" style="margin-bottom:12px;">Know what to do before help arrives.</p>
        <div class="search-bar search-bar-light mb-md">
            <i data-lucide="search"></i>
            <input type="search" id="guide-search" placeholder="Search guides..." style="border:none;background:transparent;flex:1;font-family:inherit;font-size:14px;">
        </div>
        <div class="first-aid-list scr" style="max-height:620px;overflow-y:auto;" data-guide-list>
            <?php foreach ($guides as $guide):
                $active = (int) $guide['guide_id'] === (int) ($selected['guide_id'] ?? 0);
                $badge = first_aid_severity_badge((string) $guide['severity_level']);
                $accent = first_aid_severity_accent((string) $guide['severity_level']);
                $icon = $guide['icon'] ?? 'dog';
                $severityIcon = $guide['severity_level'] === 'Severe' ? '⚠' : ($guide['severity_level'] === 'Moderate' ? '~' : '✓');
            ?>
                <a href="first-aid.php?id=<?= (int) $guide['guide_id'] ?>"
                   class="incident-card card-bordered first-aid-card<?= $active ? ' is-active' : '' ?> card-hoverable"
                   data-guide-item
                   data-guide-title="<?= htmlspecialchars(strtolower((string) $guide['title'])) ?>">
                    <div class="accent <?= $accent ?>"></div>
                    <div class="card-body" style="flex:1;">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-sm"><i data-lucide="<?= htmlspecialchars((string) $icon) ?>"></i><span style="font-weight:500;"><?= htmlspecialchars((string) $guide['incident_type']) ?></span></div>
                            <span class="badge <?= $badge ?>"><?= $severityIcon ?> <?= htmlspecialchars((string) $guide['severity_level']) ?></span>
                        </div>
                        <?php if ($active): ?><div class="text-xs" style="font-weight:500;margin-top:10px;color:var(--burnt-peach);">Viewing →</div><?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($selected): ?>
    <div class="guide-panel flex-1">
        <div class="card-body scr" style="flex:1;overflow-y:auto;padding:24px 26px;">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-md">
                    <div class="type-card-icon" style="background:rgba(224,118,94,.14);"><i data-lucide="<?= htmlspecialchars((string) ($selected['icon'] ?? 'dog')) ?>" style="color:var(--burnt-peach);"></i></div>
                    <h2 style="font-weight:500;font-size:24px;margin:0;"><?= htmlspecialchars((string) $selected['title']) ?></h2>
                </div>
                <span class="badge <?= first_aid_severity_badge((string) $selected['severity_level']) ?>"><?= htmlspecialchars((string) $selected['severity_level']) ?></span>
            </div>
            <p class="text-xs text-muted mt-sm flex items-center gap-sm"><i data-lucide="book-open"></i> Source: <?= htmlspecialchars((string) $selected['source_citation']) ?></p>

            <div class="warning-box mt-md first-aid-warning">
                <i data-lucide="alert-triangle" style="width:24px;height:24px;color:var(--sunlit-clay);flex:none;"></i>
                <div class="text-sm" style="font-weight:500;"><?= htmlspecialchars((string) $selected['warning_text']) ?></div>
            </div>

            <div class="step-list mt-md">
                <?php foreach ($selected['steps'] as $i => $step): ?>
                    <div class="step-row"><div class="step-circle"><?= $i + 1 ?></div><div class="text-sm" style="font-weight:500;padding-top:6px;"><?= htmlspecialchars((string) $step) ?></div></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div style="border-top:1px solid var(--border);padding:16px 26px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <a href="first-aid/download.php?id=<?= (int) $selected['guide_id'] ?>" class="btn-outline btn-sm"><i data-lucide="download"></i> Download as PDF</a>
            <button type="button" class="btn-primary btn-sm" data-report-from-guide="<?= htmlspecialchars((string) $selected['incident_type']) ?>"><i data-lucide="plus"></i> Report This Incident</button>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/partials/report-drawer.php'; ?>
<div class="toast-container" data-toast-container aria-live="polite"></div>
<?php app_layout_end([]); ?>
