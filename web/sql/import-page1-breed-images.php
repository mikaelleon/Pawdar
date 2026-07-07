<?php

declare(strict_types=1);

/**
 * Import archive images for breeds on directory page 1 (registered sort).
 *
 *   c:\xampp\php\php.exe web/sql/import-page1-breed-images.php
 */

require_once dirname(__DIR__) . '/includes/env.php';
pawdar_load_env();
require_once dirname(__DIR__) . '/includes/breed-content.php';
require_once dirname(__DIR__) . '/includes/breeds.php';
require_once dirname(__DIR__) . '/includes/breed-media.php';

$archiveRoot = dirname(__DIR__, 2) . '/archive/Dog-Breeds-Dataset-master';
$uploadsDir = dirname(__DIR__) . '/uploads/breeds';

if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0755, true) && !is_dir($uploadsDir)) {
    fwrite(STDERR, "Could not create uploads directory.\n");
    exit(1);
}

/**
 * @return array{path: string, name: string}|null
 */
function pick_archive_image(string $folderPath): ?array
{
    foreach (scandir($folderPath) ?: [] as $entry) {
        if (preg_match('/^image_1\./i', $entry) === 1) {
            $path = $folderPath . DIRECTORY_SEPARATOR . $entry;
            if (is_file($path)) {
                return ['path' => $path, 'name' => $entry];
            }
        }
    }

    foreach (scandir($folderPath) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $path = $folderPath . DIRECTORY_SEPARATOR . $entry;
        if (!is_file($path)) {
            continue;
        }
        $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return ['path' => $path, 'name' => $entry];
        }
    }

    return null;
}

/** @var array<string, string> breed_name => archive folder name */
$folderMap = [
    'Aspin (Asong Pinoy)' => 'aspin dog',
    'Afador' => 'afador dog',
    'Affenhuahua' => 'affenhuahua dog',
    'Akbash' => 'akbash dog',
    'Akita Chow' => 'akita chow dog',
    'Akita Pit' => 'akita pit dog',
    'Akita Shepherd' => 'akita shepherd dog',
    'Alaskan Klee Kai' => 'alaskan klee kai dog',
];

$host = pawdar_env('PAWDAR_DB_HOST', 'localhost') ?? 'localhost';
$user = pawdar_env('PAWDAR_DB_USER', 'root') ?? 'root';
$pass = pawdar_env('PAWDAR_DB_PASS', '') ?? '';
$dbName = pawdar_env('PAWDAR_DB_NAME', 'pawdar') ?? 'pawdar';

$pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4', $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$result = fetch_breeds_directory($pdo, ['sort' => 'registered', 'page' => 1, 'size' => 'all']);
$update = $pdo->prepare('UPDATE breeds SET image_url = :url, gallery_urls = NULL WHERE breed_id = :id');
$imported = 0;

foreach ($result['rows'] as $breed) {
    if (breed_directory_photo_url($breed) !== null) {
        continue;
    }

    $breedName = (string) $breed['breed_name'];
    $folderName = $folderMap[$breedName] ?? null;
    if ($folderName === null) {
        fwrite(STDERR, "No archive folder mapped for {$breedName}\n");
        continue;
    }

    $folderPath = $archiveRoot . DIRECTORY_SEPARATOR . $folderName;
    if (!is_dir($folderPath)) {
        fwrite(STDERR, "Archive folder missing: {$folderName}\n");
        continue;
    }

    $picked = pick_archive_image($folderPath);
    if ($picked === null) {
        fwrite(STDERR, "No image in {$folderName}\n");
        continue;
    }

    $slug = (string) ($breed['slug'] ?? breed_slug_from_name($breedName));
    $ext = strtolower(pathinfo($picked['name'], PATHINFO_EXTENSION));
    $destFile = $slug . '.' . $ext;
    $destPath = $uploadsDir . DIRECTORY_SEPARATOR . $destFile;
    $publicUrl = 'uploads/breeds/' . $destFile;

    if (!copy($picked['path'], $destPath)) {
        fwrite(STDERR, "Copy failed for {$breedName}\n");
        continue;
    }

    $update->execute([
        ':url' => $publicUrl,
        ':id' => (int) $breed['breed_id'],
    ]);

    echo "Imported {$breedName} -> {$publicUrl}\n";
    $imported++;
}

echo "Done. Imported {$imported} breed image(s) for page 1.\n";
