<?php
/** @var array<string, int> $mapCounts */
$mapCounts = $mapCounts ?? ['bites' => 0, 'strays' => 0, 'aggressive' => 0, 'vehicular' => 0, 'disturbance' => 0];
$barangay = (string) ($_SESSION['user_barangay'] ?? '');
$statItems = [
    ['key' => 'bites', 'label' => 'Bites', 'class' => 'map-kpi-card--bites'],
    ['key' => 'strays', 'label' => 'Strays', 'class' => 'map-kpi-card--strays'],
    ['key' => 'aggressive', 'label' => 'Aggressive', 'class' => 'map-kpi-card--aggressive'],
    ['key' => 'vehicular', 'label' => 'Vehicular', 'class' => 'map-kpi-card--vehicular'],
    ['key' => 'disturbance', 'label' => 'Disturbance', 'class' => 'map-kpi-card--disturbance'],
];
?>
<div class="bento-card map-card map-card--kpi">
    <div class="bento-card-header">
        <span class="bento-icon" aria-hidden="true"><i data-lucide="map-pin"></i></span>
        <span class="bento-label">Barangay incidents</span>
    </div>
    <div class="map-kpi-grid map-counts--five" data-map-counts>
        <?php foreach ($statItems as $item): ?>
            <div class="map-kpi-card <?= htmlspecialchars($item['class']) ?>">
                <strong data-count-<?= htmlspecialchars($item['key']) ?>><?= (int) ($mapCounts[$item['key']] ?? 0) ?></strong>
                <span><?= htmlspecialchars($item['label']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="map.php?barangay=<?= urlencode($barangay) ?>" class="bento-link">Open full map →</a>
</div>
