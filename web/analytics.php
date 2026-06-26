<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/incidents.php';
require_role(['LGU Official', 'Admin']);

$pdo = db();
$barangay = (string) $_SESSION['user_barangay'];
$counts = fetch_map_counts($pdo, $barangay);
$total = array_sum($counts);

app_layout_start('analytics', 'Analytics', ['showSearch' => false]);
?>

<div class="feed-header">
    <div>
        <h1 class="feed-title">Barangay Analytics</h1>
        <p class="text-sm text-muted">Brgy. <?= htmlspecialchars($barangay) ?> · Last 30 days overview</p>
    </div>
</div>

<div class="summary-strip">
    <div class="summary-card investigating">
        <div class="summary-value"><?= (int) $counts['bites'] ?></div>
        <div class="summary-label">Animal Bites</div>
    </div>
    <div class="summary-card">
        <div class="summary-value"><?= (int) $counts['strays'] ?></div>
        <div class="summary-label">Injured Strays</div>
    </div>
    <div class="summary-card resolved">
        <div class="summary-value"><?= (int) $counts['aggressive'] ?></div>
        <div class="summary-label">Aggressive Reports</div>
    </div>
    <div class="summary-card rabies">
        <div class="summary-value"><?= (int) $total ?></div>
        <div class="summary-label">Total Incidents</div>
    </div>
</div>

<?php app_layout_end([]); ?>
