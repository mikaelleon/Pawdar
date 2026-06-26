<?php
/** @var array<string, int> $mapCounts */
/** @var list<array<string, mixed>> $mapPins */
$mapCounts = $mapCounts ?? ['bites' => 0, 'strays' => 0, 'aggressive' => 0, 'vehicular' => 0];
$mapPins = $mapPins ?? [];
$barangay = (string) ($_SESSION['user_barangay'] ?? '');
?>
<div class="bento-card map-card">
    <div class="bento-card-header">
        <span class="bento-icon" aria-hidden="true">📍</span>
        <span class="bento-label">Map preview</span>
    </div>
    <div class="mini-map-container map-preview" data-map-preview>
        <div style="position:absolute;top:70px;left:-10px;right:-10px;height:12px;background:#fff;transform:rotate(-7deg);"></div>
        <div style="position:absolute;inset:0;background:var(--tea-green);opacity:.15;"></div>
        <?php foreach ($mapPins as $pin): ?>
            <div class="map-pin map-pin-drop <?= htmlspecialchars($pin['accent']) ?>"
                 style="left:<?= (int) $pin['left'] ?>px;top:<?= (int) $pin['top'] ?>px;width:26px;height:26px;"></div>
        <?php endforeach; ?>
    </div>
    <div class="map-counts" data-map-counts>
        <div class="map-count-item"><strong data-count-bites><?= (int) $mapCounts['bites'] ?></strong><span>Bites</span></div>
        <div class="map-count-item"><strong data-count-strays><?= (int) $mapCounts['strays'] ?></strong><span>Strays</span></div>
        <div class="map-count-item"><strong data-count-aggressive><?= (int) $mapCounts['aggressive'] ?></strong><span>Aggressive</span></div>
        <div class="map-count-item"><strong data-count-vehicular><?= (int) $mapCounts['vehicular'] ?></strong><span>Vehicular</span></div>
    </div>
    <a href="map.php?barangay=<?= urlencode($barangay) ?>" class="bento-link">Open full map →</a>
</div>
