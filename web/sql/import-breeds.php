<?php

declare(strict_types=1);

/**
 * Load breed directory into pawdar.breeds from schema-v3-breeds-seed.sql.
 *
 * Regenerate seed from archive/dogs_cleaned.csv:
 *   php sql/generate-breeds-seed.php
 *
 * Run locally:
 *   php sql/import-breeds.php
 *
 * InfinityFree / phpMyAdmin (no CLI):
 *   Import sql/schema-v3-breeds-seed.sql after schema-v3-breeds.sql
 */

require_once dirname(__DIR__) . '/includes/env.php';
pawdar_load_env();
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once __DIR__ . '/runner.php';
if (file_exists(dirname(__DIR__) . '/includes/db.local.php')) {
    require_once dirname(__DIR__) . '/includes/db.local.php';
}

$seedPath = __DIR__ . '/schema-v3-breeds-seed.sql';
$csvPath = dirname(dirname(__DIR__)) . '/archive/dogs_cleaned.csv';

if (!is_readable($seedPath) && is_readable($csvPath)) {
    echo "Seed file missing — generating from archive/dogs_cleaned.csv...\n";
    passthru(PHP_BINARY . ' ' . escapeshellarg(__DIR__ . '/generate-breeds-seed.php'), $exitCode);
    if ($exitCode !== 0) {
        exit($exitCode);
    }
}

if (!is_readable($seedPath)) {
    fwrite(STDERR, "Missing seed SQL: {$seedPath}\n");
    fwrite(STDERR, "Run: php sql/generate-breeds-seed.php (requires archive/dogs_cleaned.csv)\n");
    exit(1);
}

$host = pawdar_env('PAWDAR_DB_HOST', 'localhost') ?? 'localhost';
$user = pawdar_env('PAWDAR_DB_USER', 'root') ?? 'root';
$pass = pawdar_env('PAWDAR_DB_PASS', '') ?? '';
$dbName = pawdar_env('PAWDAR_DB_NAME', 'pawdar') ?? 'pawdar';

try {
    $pdo = new PDO('mysql:host=' . $host . ';charset=utf8mb4', $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('USE `' . str_replace('`', '', $dbName) . '`');

    pawdar_run_sql_file($pdo, __DIR__ . '/schema-v3-breeds.sql', true);
    pawdar_add_column($pdo, 'dog', 'breed_id', 'INT NULL AFTER Breed');
    pawdar_run_sql_file($pdo, $seedPath);
    pawdar_ensure_breed_foreign_key($pdo);

    $total = (int) $pdo->query('SELECT COUNT(*) FROM breeds')->fetchColumn();
    echo "Breed import complete. Total breeds: {$total}\n";
    foreach ($pdo->query('SELECT size_category, COUNT(*) AS cnt FROM breeds GROUP BY size_category ORDER BY size_category') as $row) {
        echo "  {$row['size_category']}: {$row['cnt']}\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Import failed: ' . $e->getMessage() . "\n");
    exit(1);
}
