<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/breeds.php';
require_once __DIR__ . '/../includes/breed-media.php';

$breedId = (int) ($_GET['id'] ?? 0);
$pdo = db();
$breed = $breedId > 0 ? fetch_breed_by_id($pdo, $breedId) : null;

if (!$breed) {
    $breed = [
        'breed_id' => 0,
        'energy_score' => 3,
        'loyalty_score' => 3,
        'friendliness_score' => 3,
    ];
}

$fill = breed_trait_accent_color($breed);
header('Content-Type: image/svg+xml; charset=utf-8');
header('Cache-Control: public, max-age=86400');

echo '<?xml version="1.0" encoding="UTF-8"?>'
    . '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="240" viewBox="0 0 320 240">'
    . '<rect width="320" height="240" fill="' . htmlspecialchars($fill) . '" opacity="0.22"/>'
    . '<g transform="translate(80,36)" fill="' . htmlspecialchars($fill) . '">'
    . '<circle cx="80" cy="52" r="34"/>'
    . '<ellipse cx="48" cy="28" rx="16" ry="30" transform="rotate(-28 48 28)"/>'
    . '<ellipse cx="112" cy="28" rx="16" ry="30" transform="rotate(28 112 28)"/>'
    . '<ellipse cx="80" cy="118" rx="52" ry="62"/>'
    . '<rect x="52" y="168" width="18" height="42" rx="8"/>'
    . '<rect x="90" y="168" width="18" height="42" rx="8"/>'
    . '</g></svg>';
