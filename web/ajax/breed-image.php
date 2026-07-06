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

if ($imageUrl !== null && breed_image_url_is_valid($imageUrl)) {
    header('Location: ' . $imageUrl, true, 302);
    exit;
}

header('Location: breed-silhouette.php?id=' . $breedId, true, 302);
exit;
