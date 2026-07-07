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
    if (str_starts_with($imageUrl, 'uploads/')) {
        $localPath = breed_local_image_path($imageUrl);
        if ($localPath !== null) {
            $mime = match (strtolower(pathinfo($localPath, PATHINFO_EXTENSION))) {
                'png' => 'image/png',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
                default => 'image/jpeg',
            };
            header('Content-Type: ' . $mime);
            header('Cache-Control: public, max-age=86400');
            readfile($localPath);
            exit;
        }
    }

    header('Location: ' . $imageUrl, true, 302);
    exit;
}

header('Location: breed-silhouette.php?id=' . $breedId, true, 302);
exit;
