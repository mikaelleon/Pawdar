<?php

require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/analytics.php';

require_role(['LGU Official', 'Admin']);

$pdo = db();
$barangay = (string) $_SESSION['user_barangay'];
$periodDays = 30;

$categories = analytics_category_catalog();
$categoryCounts = fetch_analytics_category_counts($pdo, $barangay, $periodDays);
$trend = fetch_analytics_incident_trend($pdo, $barangay, $periodDays);
$totalDelta = analytics_period_delta($categoryCounts['total'], $categoryCounts['previous_total']);

app_layout_start('analytics', 'Analytics', [
    'showSearch' => false,
    'breadcrumbs' => [['label' => 'Analytics']],
]);
?>

<div class="feed-header analytics-header">
    <div>
        <h1 class="feed-title">Barangay Analytics</h1>
        <p class="text-sm text-muted">Brgy. <?= htmlspecialchars($barangay) ?> · Last <?= (int) $periodDays ?> days overview</p>
    </div>
    <div class="analytics-export-actions">
        <a href="analytics/export.php?format=csv" class="btn-outline btn-sm"><i data-lucide="download"></i> Export CSV</a>
        <a href="analytics/export.php?format=pdf" class="btn-outline btn-sm"><i data-lucide="file-text"></i> Download PDF</a>
    </div>
</div>

<div class="analytics-kpi-grid">
    <?php foreach ($categories as $item):
        $key = (string) $item['key'];
        $current = (int) ($categoryCounts['current'][$key] ?? 0);
        $previous = (int) ($categoryCounts['previous'][$key] ?? 0);
        $delta = analytics_period_delta($current, $previous);
        ?>
        <article class="analytics-kpi-card <?= htmlspecialchars((string) $item['class']) ?>">
            <div class="analytics-kpi-value"><?= $current ?></div>
            <div class="analytics-kpi-label"><?= htmlspecialchars((string) $item['label']) ?></div>
            <div class="analytics-kpi-delta <?= htmlspecialchars($delta['class']) ?>"><?= htmlspecialchars($delta['text']) ?></div>
        </article>
    <?php endforeach; ?>
    <article class="analytics-kpi-card analytics-kpi--total">
        <div class="analytics-kpi-value"><?= (int) $categoryCounts['total'] ?></div>
        <div class="analytics-kpi-label">Total Incidents</div>
        <div class="analytics-kpi-delta <?= htmlspecialchars($totalDelta['class']) ?>"><?= htmlspecialchars($totalDelta['text']) ?></div>
    </article>
</div>

<section class="analytics-trend-panel card card-bordered">
    <div class="analytics-panel-head">
        <h2 class="analytics-panel-title"><i data-lucide="trending-up"></i> Incident trend</h2>
        <p class="text-xs text-muted">Reports per day over the last <?= (int) $periodDays ?> days</p>
    </div>
    <div class="analytics-trend-wrap">
        <?= analytics_render_trend_chart($trend) ?>
    </div>
</section>

<?php app_layout_end([]); ?>
