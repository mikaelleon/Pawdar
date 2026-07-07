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

$severityClass = 'firstaid-card--mild';
if ($guide !== null) {
    $severityClass = match ((string) $guide['severity_level']) {
        'Severe' => 'firstaid-card--severe',
        'Moderate' => 'firstaid-card--moderate',
        default => 'firstaid-card--mild',
    };
}
?>
<?php if ($guide !== null): ?>
<div class="bento-card firstaid-card <?= htmlspecialchars($severityClass) ?>">
    <div class="bento-card-header firstaid-card-header">
        <span class="bento-icon" aria-hidden="true"><i data-lucide="heart-pulse"></i></span>
        <span class="bento-label">First aid reminder</span>
        <span class="firstaid-severity-label"><?= htmlspecialchars((string) $guide['severity_level']) ?></span>
    </div>
    <p class="firstaid-type"><?= htmlspecialchars((string) ($guide['display_label'] ?? $guide['incident_type'])) ?></p>
    <p class="firstaid-step-meta">Step 1<?= $stepCount > 0 ? ' of ' . $stepCount : '' ?></p>
    <p class="firstaid-step"><?= htmlspecialchars($firstStep) ?></p>
    <a href="first-aid.php?id=<?= (int) $guide['guide_id'] ?>" class="bento-link firstaid-card-link">View full guide →</a>
</div>
<?php endif; ?>
