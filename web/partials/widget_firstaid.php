<?php
require_once __DIR__ . '/../includes/first-aid-data.php';

$pdo = db();
$guides = fetch_first_aid_guides($pdo);
$guide = null;
$firstStep = '';
$stepCount = 0;

if (count($guides) > 0) {
    $week = (int) date('W');
    $guide = $guides[$week % count($guides)];
    $stepCount = count($guide['steps']);
    $firstStep = $stepCount > 0 ? (string) ($guide['steps'][0]['summary'] ?? '') : '';
}
?>
<?php if ($guide !== null): ?>
<div class="bento-card firstaid-card">
    <div class="bento-card-header">
        <span class="bento-icon" aria-hidden="true">🩹</span>
        <span class="bento-label">First aid reminder</span>
        <?= severity_badge_html((string) $guide['severity_level']) ?>
    </div>
    <p class="firstaid-type"><?= htmlspecialchars((string) ($guide['display_label'] ?? $guide['incident_type'])) ?></p>
    <p class="firstaid-step-meta text-xs text-muted">Step 1<?= $stepCount > 0 ? ' of ' . $stepCount : '' ?></p>
    <p class="firstaid-step"><?= htmlspecialchars($firstStep) ?></p>
    <a href="first-aid.php?id=<?= (int) $guide['guide_id'] ?>" class="bento-link">View full guide →</a>
</div>
<?php endif; ?>
