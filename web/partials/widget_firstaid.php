<?php
$pdo = db();
$guides = $pdo->query('SELECT guide_id, incident_type, severity_level, steps FROM first_aid_guides ORDER BY guide_id ASC')->fetchAll();
$guide = null;
$firstStep = '';
$stepCount = 0;

if (count($guides) > 0) {
    $week = (int) date('W');
    $guide = $guides[$week % count($guides)];
    $steps = json_decode((string) $guide['steps'], true);
    $stepCount = is_array($steps) ? count($steps) : 0;
    $firstStep = is_array($steps) ? (string) ($steps[0] ?? '') : '';
}
?>
<?php if ($guide !== null): ?>
<div class="bento-card firstaid-card">
    <div class="bento-card-header">
        <span class="bento-icon" aria-hidden="true">🩹</span>
        <span class="bento-label">First aid reminder</span>
        <?= severity_badge_html((string) $guide['severity_level']) ?>
    </div>
    <p class="firstaid-type"><?= htmlspecialchars((string) $guide['incident_type']) ?></p>
    <p class="firstaid-step-meta text-xs text-muted">Step 1<?= $stepCount > 0 ? ' of ' . $stepCount : '' ?></p>
    <p class="firstaid-step"><?= htmlspecialchars($firstStep) ?></p>
    <a href="first-aid.php?id=<?= (int) $guide['guide_id'] ?>" class="bento-link">View full guide →</a>
</div>
<?php endif; ?>
