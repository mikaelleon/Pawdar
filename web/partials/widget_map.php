<?php
/** @var array<string, int> $mapCounts */
/** @var list<array<string, mixed>> $mapPins */
$mapCounts = $mapCounts ?? ['bites' => 0, 'strays' => 0, 'aggressive' => 0, 'vehicular' => 0, 'disturbance' => 0];
$mapPins = $mapPins ?? [];
$barangay = (string) ($_SESSION['user_barangay'] ?? '');
$pinColors = incident_pin_colors();
$statItems = [
    ['key' => 'bites', 'label' => 'Bites', 'color' => $pinColors['Animal Bite']],
    ['key' => 'strays', 'label' => 'Strays', 'color' => $pinColors['Injured Stray']],
    ['key' => 'aggressive', 'label' => 'Aggressive', 'color' => $pinColors['Aggressive Behavior']],
    ['key' => 'vehicular', 'label' => 'Vehicular', 'color' => $pinColors['Vehicular Accident']],
    ['key' => 'disturbance', 'label' => 'Disturbance', 'color' => $pinColors['Disturbance']],
];
?>
<div class="bento-card map-card">
    <div class="bento-card-header">
        <span class="bento-icon" aria-hidden="true">📍</span>
        <span class="bento-label">Map preview</span>
    </div>
    <div class="mini-map-container map-preview" data-map-preview aria-label="Incident map preview for your barangay">
        <?php if (count($mapPins) === 0): ?>
            <div class="map-preview-empty text-xs text-muted">No incidents in this barangay yet.</div>
        <?php else: ?>
            <?php foreach ($mapPins as $pin): ?>
                <div class="map-pin map-pin-dot"
                     style="left:<?= (int) $pin['left'] ?>px;top:<?= (int) $pin['top'] ?>px;background:<?= htmlspecialchars((string) $pin['color']) ?>;"
                     title="<?= htmlspecialchars((string) $pin['label']) ?>"
                     data-pin-type="<?= htmlspecialchars((string) $pin['count_key']) ?>"></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="map-preview-legend" aria-hidden="true">
        <?php foreach ($statItems as $item): ?>
            <span class="map-legend-item">
                <span class="map-legend-dot" style="background:<?= htmlspecialchars((string) $item['color']) ?>;"></span>
                <?= htmlspecialchars($item['label']) ?>
            </span>
        <?php endforeach; ?>
    </div>
    <div class="map-counts map-counts--five" data-map-counts>
        <?php foreach ($statItems as $item): ?>
            <div class="map-count-item">
                <span class="map-count-dot" style="background:<?= htmlspecialchars((string) $item['color']) ?>;"></span>
                <strong data-count-<?= htmlspecialchars($item['key']) ?>><?= (int) ($mapCounts[$item['key']] ?? 0) ?></strong>
                <span><?= htmlspecialchars($item['label']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="map.php?barangay=<?= urlencode($barangay) ?>" class="bento-link">Open full map →</a>
</div>
