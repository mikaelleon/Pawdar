<?php

declare(strict_types=1);

/**
 * Import Batangas city/barangay reference data from docs markdown.
 * Run: php sql/import-barangays.php
 */

$root = dirname(__DIR__);
require_once $root . '/includes/env.php';
pawdar_load_env();
require_once $root . '/includes/helpers.php';
require_once __DIR__ . '/runner.php';

if (file_exists($root . '/includes/db.local.php')) {
    require_once $root . '/includes/db.local.php';
}

$mdPath = dirname($root) . '/docs/Batangas_Cities_and_Barangays.md';
if (!is_readable($mdPath)) {
    fwrite(STDERR, "Missing markdown: {$mdPath}\n");
    exit(1);
}

$host = pawdar_env('PAWDAR_DB_HOST', 'localhost') ?? 'localhost';
$user = pawdar_env('PAWDAR_DB_USER', 'root') ?? 'root';
$pass = pawdar_env('PAWDAR_DB_PASS', '') ?? '';
$dbName = pawdar_env('PAWDAR_DB_NAME', 'pawdar') ?? 'pawdar';

/**
 * @return list<array{name: string, barangays: list<string>}>
 */
function pawdar_parse_barangay_markdown(string $path): array
{
    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException('Could not read markdown');
    }

    $cities = [];
    $lines = preg_split('/\R/', $content) ?: [];
    $currentIndex = -1;

    foreach ($lines as $line) {
        if (preg_match('/^## \d+\. (.+?) \(\d+ Barangays\)\s*$/', $line, $match)) {
            $cities[] = [
                'name' => trim($match[1]),
                'barangays' => [],
            ];
            $currentIndex = count($cities) - 1;
            continue;
        }

        if ($currentIndex < 0) {
            continue;
        }

        if (preg_match('/^## /', $line)) {
            $currentIndex = -1;
            continue;
        }

        if (preg_match('/^\d+\.\s+(.+)$/', trim($line), $item)) {
            $name = trim($item[1]);
            if ($name !== '') {
                $cities[$currentIndex]['barangays'][] = $name;
            }
        }
    }

    return array_values(array_filter($cities, static fn (array $city): bool => $city['barangays'] !== []));
}

try {
    $pdo = new PDO('mysql:host=' . $host . ';charset=utf8mb4', $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $safeDb = str_replace('`', '', $dbName);
    $pdo->exec('USE `' . $safeDb . '`');

    pawdar_run_sql_file($pdo, __DIR__ . '/schema-v5-locations.sql', true);

    $parsed = pawdar_parse_barangay_markdown($mdPath);
    if ($parsed === []) {
        throw new RuntimeException('No cities parsed from markdown');
    }

    $pdo->beginTransaction();

    $cityStmt = $pdo->prepare('INSERT INTO city (name) VALUES (:name) ON DUPLICATE KEY UPDATE name = VALUES(name)');
    $cityIdStmt = $pdo->prepare('SELECT city_id FROM city WHERE name = :name LIMIT 1');
    $barangayStmt = $pdo->prepare(
        'INSERT INTO barangay (city_id, name) VALUES (:city_id, :name)
         ON DUPLICATE KEY UPDATE name = VALUES(name)'
    );

    $cityCount = 0;
    $barangayCount = 0;

    foreach ($parsed as $city) {
        $cityStmt->execute([':name' => $city['name']]);
        $cityIdStmt->execute([':name' => $city['name']]);
        $cityId = (int) $cityIdStmt->fetchColumn();
        $cityCount++;

        foreach ($city['barangays'] as $barangayName) {
            $barangayStmt->execute([
                ':city_id' => $cityId,
                ':name' => $barangayName,
            ]);
            $barangayCount++;
        }
    }

    $pdo->commit();

    echo "Barangay import complete.\n";
    echo "Cities: {$cityCount}\n";
    echo "Barangay rows processed: {$barangayCount}\n";
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'Import failed: ' . $e->getMessage() . "\n");
    exit(1);
}
