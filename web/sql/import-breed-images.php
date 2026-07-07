<?php

declare(strict_types=1);

/**
 * Copies one image per breed from archive/Dog-Breeds-Dataset-master into web/uploads/breeds/,
 * updates breeds.image_url, and deletes surplus images from the archive folders.
 *
 *   c:\xampp\php\php.exe web\sql\import-breed-images.php
 *   c:\xampp\php\php.exe web\sql\import-breed-images.php --dry-run
 */

require_once dirname(__DIR__) . '/includes/env.php';
pawdar_load_env();
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/breed-content.php';
if (file_exists(dirname(__DIR__) . '/includes/db.local.php')) {
    require_once dirname(__DIR__) . '/includes/db.local.php';
}

$dryRun = in_array('--dry-run', $argv ?? [], true);
$archiveRoot = dirname(__DIR__, 2) . '/archive/Dog-Breeds-Dataset-master';
$uploadsDir = dirname(__DIR__) . '/uploads/breeds';

if (!is_dir($archiveRoot)) {
    fwrite(STDERR, "Archive not found: {$archiveRoot}\n");
    exit(1);
}

if (!$dryRun && !is_dir($uploadsDir) && !mkdir($uploadsDir, 0755, true) && !is_dir($uploadsDir)) {
    fwrite(STDERR, "Could not create uploads directory: {$uploadsDir}\n");
    exit(1);
}

/**
 * Normalizes a breed label for loose matching.
 */
function breed_image_match_key(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/\s+dog$/', '', $value) ?? $value;
    $value = preg_replace('/\([^)]*\)/', '', $value) ?? $value;
    $value = preg_replace('/\s*-\s*/', ' ', $value) ?? $value;
    $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;

    return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
}

/**
 * FCI archive folder label → DogTime / directory breed name overrides.
 *
 * @return array<string, string>
 */
function breed_archive_aliases(): array
{
    return [
        'borzoi russian hunting sighthound' => 'Borzoi',
        'bull terrier' => 'Bull Terrier',
        'catalan sheepdog' => 'Catalan Sheepdog',
        'collie rough' => 'Collie',
        'collie smooth' => 'Collie',
        'continental toy spaniel' => 'Papillon',
        'czechoslovakian wolfdog' => 'Czechoslovakian Wolfdog',
        'danish swedish farmdog' => 'Danish-Swedish Farmdog',
        'deerhound' => 'Scottish Deerhound',
        'dobermann' => 'Doberman Pinscher',
        'english toy terrier black tan' => 'Manchester Terrier',
        'fox terrier smooth' => 'Fox Terrier',
        'fox terrier wire' => 'Wire Fox Terrier',
        'french pointing dog pyrenean type' => 'Pointer',
        'german short haired pointing dog' => 'German Shorthaired Pointer',
        'german wire haired pointing dog' => 'German Wirehaired Pointer',
        'griffon belge' => 'Brussels Griffon',
        'griffon bruxellois' => 'Brussels Griffon',
        'irish soft coated wheaten terrier' => 'Soft Coated Wheaten Terrier',
        'italian cane corso' => 'Cane Corso',
        'italian sighthound' => 'Italian Greyhound',
        'jack russell terrier' => 'Jack Russell Terrier',
        'king charles spaniel' => 'Cavalier King Charles Spaniel',
        'little lion dog' => 'Löwchen',
        'maremma and the abruzzes sheepdog' => 'Maremma Sheepdog',
        'miniature american shepherd' => 'Miniature American Shepherd',
        'petit brabancon' => 'Brussels Griffon',
        'poodle' => 'Poodle',
        'portuguese warren hound portuguese podengo' => 'Portuguese Podengo',
        'presa canario' => 'Perro de Presa Canario',
        'pyrenean mountain dog' => 'Great Pyrenees',
        'rhodesian ridgeback' => 'Rhodesian Ridgeback',
        'saint bernard' => 'Saint Bernard',
        'shar pei' => 'Shar-Pei',
        'shetland sheepdog' => 'Shetland Sheepdog',
        'shiba' => 'Shiba Inu',
        'tibetan spaniel' => 'Tibetan Spaniel',
        'welsh corgi cardigan' => 'Cardigan Welsh Corgi',
        'welsh corgi pembroke' => 'Pembroke Welsh Corgi',
        'west highland white terrier' => 'West Highland White Terrier',
        'wire haired pointing griffon korthals' => 'Wirehaired Pointing Griffon',
        'xoloitzcuintle' => 'Xoloitzcuintli',
    ];
}

/**
 * Picks Image_1.* when present, otherwise the first image in natural order.
 *
 * @return array{path: string, name: string}|null
 */
function pick_archive_breed_image(string $folderPath): ?array
{
    $preferred = null;
    $all = [];

    foreach (scandir($folderPath) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        $path = $folderPath . DIRECTORY_SEPARATOR . $entry;
        if (!is_file($path)) {
            continue;
        }

        $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            continue;
        }

        $all[] = ['path' => $path, 'name' => $entry];
        if (preg_match('/^image_1\./i', $entry) === 1) {
            $preferred = ['path' => $path, 'name' => $entry];
        }
    }

    if ($preferred !== null) {
        return $preferred;
    }

    if ($all === []) {
        return null;
    }

    usort($all, static function (array $a, array $b): int {
        return strnatcasecmp($a['name'], $b['name']);
    });

    return $all[0];
}

/**
 * @param array<string, array{breed_id: int, breed_name: string, slug: string}> $breedIndex
 * @param list<array{breed_id: int, breed_name: string, slug: string, key: string}> $breedList
 */
function resolve_breed_for_folder(
    string $folderName,
    array $breedIndex,
    array $breedList,
    array $usedBreedIds
): ?array {
    $folderKey = breed_image_match_key($folderName);

    if (isset($breedIndex[$folderKey])) {
        $entry = $breedIndex[$folderKey];
        if (!in_array($entry['breed_id'], $usedBreedIds, true)) {
            return $entry;
        }
    }

    $aliases = breed_archive_aliases();
    if (isset($aliases[$folderKey])) {
        $aliasKey = breed_image_match_key($aliases[$folderKey]);
        if (isset($breedIndex[$aliasKey])) {
            $entry = $breedIndex[$aliasKey];
            if (!in_array($entry['breed_id'], $usedBreedIds, true)) {
                return $entry;
            }
        }
    }

    $best = null;
    $bestPercent = 0.0;
    foreach ($breedList as $breed) {
        if (in_array($breed['breed_id'], $usedBreedIds, true)) {
            continue;
        }

        $breedKey = $breed['key'];
        if ($folderKey === $breedKey) {
            return $breed;
        }

        if (
            strlen($folderKey) >= 6
            && strlen($breedKey) >= 6
            && (str_contains($folderKey, $breedKey) || str_contains($breedKey, $folderKey))
        ) {
            similar_text($folderKey, $breedKey, $percent);
            if ($percent > $bestPercent) {
                $bestPercent = $percent;
                $best = $breed;
            }
            continue;
        }

        similar_text($folderKey, $breedKey, $percent);
        if ($percent > $bestPercent) {
            $bestPercent = $percent;
            $best = $breed;
        }
    }

    if ($best !== null && $bestPercent >= 88.0) {
        return $best;
    }

    return null;
}

/**
 * Deletes every image in an archive breed folder except the kept file.
 */
function prune_archive_folder_images(string $folderPath, ?string $keepFileName, bool $dryRun): int
{
    $deleted = 0;

    foreach (scandir($folderPath) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        if ($keepFileName !== null && strcasecmp($entry, $keepFileName) === 0) {
            continue;
        }

        $filePath = $folderPath . DIRECTORY_SEPARATOR . $entry;
        if (!is_file($filePath)) {
            continue;
        }

        $fileExt = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
        if (!in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            continue;
        }

        if ($dryRun) {
            $deleted++;
            continue;
        }

        if (@unlink($filePath)) {
            $deleted++;
        }
    }

    return $deleted;
}

$host = pawdar_env('PAWDAR_DB_HOST', 'localhost') ?? 'localhost';
$user = pawdar_env('PAWDAR_DB_USER', 'root') ?? 'root';
$pass = pawdar_env('PAWDAR_DB_PASS', '') ?? '';
$dbName = pawdar_env('PAWDAR_DB_NAME', 'pawdar') ?? 'pawdar';

/** @var array<string, array{breed_id: int, breed_name: string, slug: string}> $breedIndex */
$breedIndex = [];
/** @var list<array{breed_id: int, breed_name: string, slug: string, key: string}> $breedList */
$breedList = [];
$pdo = null;

try {
    $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4', $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $breeds = $pdo->query('SELECT breed_id, breed_name, slug FROM breeds')->fetchAll(PDO::FETCH_ASSOC);

    foreach ($breeds as $breed) {
        $breedId = (int) $breed['breed_id'];
        $breedName = (string) $breed['breed_name'];
        $slug = (string) ($breed['slug'] ?? '');
        if ($slug === '') {
            $slug = breed_slug_from_name($breedName);
        }

        $entry = [
            'breed_id' => $breedId,
            'breed_name' => $breedName,
            'slug' => $slug,
        ];

        $keys = [breed_image_match_key($breedName)];
        $withoutParens = trim(preg_replace('/\s*\([^)]*\)\s*/', ' ', $breedName) ?? $breedName);
        if ($withoutParens !== '') {
            $keys[] = breed_image_match_key($withoutParens);
        }

        foreach ($keys as $key) {
            if ($key === '') {
                continue;
            }
            $breedIndex[$key] = $entry;
            $breedList[] = $entry + ['key' => $key];
        }
    }
} catch (Throwable $e) {
    fwrite(STDERR, "Database unavailable ({$e->getMessage()}) — matching breeds from archive/dogs_cleaned.csv only.\n");
    $csvPath = dirname(__DIR__, 2) . '/archive/dogs_cleaned.csv';
    if (!is_readable($csvPath)) {
        fwrite(STDERR, "Cannot load breeds without database or CSV.\n");
        exit(1);
    }

    $handle = fopen($csvPath, 'rb');
    fgetcsv($handle);
    while (($row = fgetcsv($handle)) !== false) {
        $breedName = trim((string) ($row[0] ?? ''));
        if ($breedName === '') {
            continue;
        }

        $entry = [
            'breed_id' => 0,
            'breed_name' => $breedName,
            'slug' => breed_slug_from_name($breedName),
        ];
        $key = breed_image_match_key($breedName);
        $breedIndex[$key] = $entry;
        $breedList[] = $entry + ['key' => $key];
    }
    fclose($handle);
}

$matched = 0;
$skippedNoImages = 0;
$unmappedFolders = [];
$deletedFiles = 0;
$prunedOnly = 0;
/** @var list<int> $usedBreedIds */
$usedBreedIds = [];
$updateStmt = $pdo instanceof PDO
    ? $pdo->prepare('UPDATE breeds SET image_url = :url, gallery_urls = NULL WHERE breed_id = :id')
    : null;

foreach (scandir($archiveRoot) ?: [] as $folderName) {
    if ($folderName === '.' || $folderName === '..') {
        continue;
    }

    $folderPath = $archiveRoot . DIRECTORY_SEPARATOR . $folderName;
    if (!is_dir($folderPath)) {
        continue;
    }

    $picked = pick_archive_breed_image($folderPath);
    if ($picked === null) {
        $skippedNoImages++;
        continue;
    }

    $breed = resolve_breed_for_folder($folderName, $breedIndex, $breedList, $usedBreedIds);
    if ($breed === null) {
        $unmappedFolders[] = $folderName;
        $deletedFiles += prune_archive_folder_images($folderPath, $picked['name'], $dryRun);
        $prunedOnly++;
        continue;
    }

    $ext = strtolower(pathinfo($picked['name'], PATHINFO_EXTENSION));
    $destFile = $breed['slug'] . '.' . $ext;
    $destPath = $uploadsDir . DIRECTORY_SEPARATOR . $destFile;
    $publicUrl = 'uploads/breeds/' . $destFile;

    if (!$dryRun) {
        if (!copy($picked['path'], $destPath)) {
            fwrite(STDERR, "Failed to copy image for {$breed['breed_name']} from {$picked['path']}\n");
            continue;
        }

        if ($updateStmt instanceof PDOStatement && $breed['breed_id'] > 0) {
            $updateStmt->execute([
                ':url' => $publicUrl,
                ':id' => $breed['breed_id'],
            ]);
        }
    }

    $usedBreedIds[] = $breed['breed_id'];
    $matched++;
    $deletedFiles += prune_archive_folder_images($folderPath, null, $dryRun);
}

echo ($dryRun ? '[dry-run] ' : '') . "Breed images imported: {$matched}\n";
echo ($dryRun ? '[dry-run] ' : '') . "Archive images removed: {$deletedFiles}\n";
echo "Archive folders pruned only (no DB breed): {$prunedOnly}\n";
echo "Folders without images: {$skippedNoImages}\n";
echo 'Unmapped archive folders: ' . count($unmappedFolders) . "\n";

if ($unmappedFolders !== []) {
    echo "Sample unmapped folders:\n";
    foreach (array_slice($unmappedFolders, 0, 20) as $folder) {
        echo "  - {$folder}\n";
    }
}
