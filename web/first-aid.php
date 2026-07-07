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
    'breadcrumbs' => [['label' => 'First Aid Guides']],
    'report_drawer' => true,
]);
?>

<div class="split-layout" data-first-aid-page>
    <div style="width:380px;flex:none;" class="hidden-mobile">
        <h1 style="font-weight:500;font-size:24px;margin:0;">First Aid Guides</h1>
        <p class="text-sm text-muted mt-sm" style="margin-bottom:12px;">Know what to do before help arrives.</p>
        <div class="search-bar search-bar-light mb-md">
            <i data-lucide="search"></i>
            <input type="search" id="guide-search" placeholder="Search guides...">
        </div>
        <div class="first-aid-list scr" style="max-height:620px;overflow-y:auto;" data-guide-list>
            <?php foreach ($guides as $guide):
                $active = (int) $guide['guide_id'] === (int) ($selected['guide_id'] ?? 0);
                $severityClass = first_aid_list_severity_class((string) $guide['severity_level']);
                $icon = (string) ($guide['icon'] ?? 'dog');
                $label = (string) ($guide['display_label'] ?? $guide['incident_type']);
                $subtitle = first_aid_list_subtitle((string) $guide['incident_type']);
                $searchBlob = strtolower($label . ' ' . (string) $guide['title'] . ' ' . $subtitle);
            ?>
                <a href="first-aid.php?id=<?= (int) $guide['guide_id'] ?>"
                   class="first-aid-card card-hoverable <?= $severityClass ?><?= $active ? ' is-selected' : '' ?>"
                   data-guide-item
                   data-guide-title="<?= htmlspecialchars($searchBlob) ?>"
                   <?= $active ? 'aria-current="page"' : '' ?>>
                    <span class="first-aid-card-icon" aria-hidden="true">
                        <i data-lucide="<?= htmlspecialchars($icon) ?>"></i>
                    </span>
                    <span class="first-aid-card-content">
                        <span class="first-aid-card-title"><?= htmlspecialchars($label) ?></span>
                        <?php if ($subtitle !== ''): ?>
                            <span class="first-aid-card-subtitle"><?= htmlspecialchars($subtitle) ?></span>
                        <?php endif; ?>
                    </span>
                    <?php if (!$active): ?>
                        <?= severity_badge_html((string) $guide['severity_level']) ?>
                    <?php endif; ?>
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
                <?= severity_badge_html((string) $selected['severity_level']) ?>
            </div>
            <p class="text-xs text-muted mt-sm flex items-center gap-sm"><i data-lucide="book-open"></i> Source: <?= htmlspecialchars((string) $selected['source_citation']) ?></p>

            <div class="warning-box mt-md first-aid-warning">
                <i data-lucide="alert-triangle" class="first-aid-warning-icon" aria-hidden="true"></i>
                <div class="text-sm" style="font-weight:500;"><?= htmlspecialchars((string) $selected['warning_text']) ?></div>
            </div>

            <p class="guide-step-hint text-xs text-muted" data-guide-step-hint hidden>
                Tap &ldquo;See more&rdquo; on any step for detailed clinical guidance.
            </p>

            <div class="step-list mt-md">
                <?php foreach ($selected['steps'] as $i => $step): ?>
                    <div class="guide-step" data-guide-step>
                        <div class="guide-step-header">
                            <div class="step-row guide-step-row">
                                <div class="step-circle"><?= $i + 1 ?></div>
                                <div class="guide-step-icon" aria-hidden="true">
                                    <i data-lucide="<?= htmlspecialchars((string) $step['icon']) ?>"></i>
                                </div>
                                <div class="guide-step-summary text-sm"><?= htmlspecialchars((string) $step['summary']) ?></div>
                            </div>
                            <?php if ($step['detail'] !== ''): ?>
                                <button type="button"
                                        class="guide-step-toggle"
                                        data-guide-step-toggle
                                        aria-expanded="false"
                                        aria-controls="guide-step-detail-<?= (int) $selected['guide_id'] ?>-<?= $i ?>">
                                    <span class="guide-step-toggle-label">See more</span>
                                    <i data-lucide="chevron-down" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php if ($step['detail'] !== ''): ?>
                            <div class="guide-step-detail text-sm text-muted"
                                 id="guide-step-detail-<?= (int) $selected['guide_id'] ?>-<?= $i ?>"
                                 data-guide-step-detail
                                 hidden>
                                <?= nl2br(htmlspecialchars((string) $step['detail'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($selected['facts'])): ?>
                <section class="guide-facts-card mt-lg" aria-labelledby="guide-facts-heading-<?= (int) $selected['guide_id'] ?>">
                    <div class="guide-facts-card-header">
                        <i data-lucide="lightbulb" aria-hidden="true"></i>
                        <h3 id="guide-facts-heading-<?= (int) $selected['guide_id'] ?>">Did you know?</h3>
                    </div>
                    <div class="guide-facts-list">
                        <?php foreach ($selected['facts']['items'] as $fact): ?>
                            <div class="guide-facts-item">
                                <?php if ($fact['heading'] !== ''): ?>
                                    <h4><?= htmlspecialchars($fact['heading']) ?></h4>
                                <?php endif; ?>
                                <p><?= htmlspecialchars($fact['body']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (($selected['facts']['source'] ?? '') !== ''): ?>
                        <p class="guide-facts-source text-xs text-muted">
                            <i data-lucide="book-open" aria-hidden="true"></i>
                            Source: <?= htmlspecialchars((string) $selected['facts']['source']) ?>
                        </p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
        <div style="border-top:1px solid var(--border);padding:16px 26px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <a href="first-aid/download.php?id=<?= (int) $selected['guide_id'] ?>" class="btn-outline btn-sm"><i data-lucide="download"></i> Download as PDF</a>
            <button type="button" class="btn-primary btn-sm" data-report-from-guide="<?= htmlspecialchars((string) $selected['incident_type']) ?>"><i data-lucide="plus"></i> Report This Incident</button>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php app_layout_end([]); ?>
