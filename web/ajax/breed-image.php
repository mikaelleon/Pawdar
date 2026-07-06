<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/breeds.php';
require_once __DIR__ . '/../includes/breed-media.php';

$breedId = (int) ($_GET['id'] ?? 0);

if ($breedId <= 0) {
    http_response_code(404);
    exit;
}

$pdo = db();
$breed = fetch_breed_by_id($pdo, $breedId);

if (!$breed) {
    http_response_code(404);
    exit;
}

$imageUrl = resolve_breed_image_url($pdo, $breedId, (string) $breed['breed_name']);

if ($imageUrl !== null) {
    header('Location: ' . $imageUrl, true, 302);
    exit;
}

header('Content-Type: image/svg+xml; charset=utf-8');
header('Cache-Control: public, max-age=86400');

$initial = strtoupper(substr((string) $breed['breed_name'], 0, 1));
$colorIndex = abs(crc32((string) $breed['breed_name'])) % 6;
$colors = ['#87AFAE', '#6C8B9F', '#C0DAB5', '#F8BC72', '#E0765E', '#b5c4c0'];
$fill = $colors[$colorIndex];

echo '<?xml version="1.0" encoding="UTF-8"?>'
    . '<svg xmlns="http://www.w3.org/2000/svg" width="240" height="240" viewBox="0 0 240 240">'
    . '<rect width="240" height="240" fill="' . htmlspecialchars($fill) . '"/>'
    . '<text x="120" y="138" text-anchor="middle" font-family="Nunito,sans-serif" font-size="72" fill="#fff">'
    . htmlspecialchars($initial)
    . '</text></svg>';
