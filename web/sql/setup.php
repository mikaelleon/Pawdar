<?php

declare(strict_types=1);

/**
 * One-time setup: creates database tables and demo data.
 * Run: php sql/setup.php
 *
 * Migration order: schema.sql → columns (runner.php) → v2 → v3 → v3 breeds seed → v4 → v5 → v6
 * Breed seed (separate step): php sql/import-breeds.php
 *   Or phpMyAdmin: schema-v3-breeds-seed.sql after schema-v3-breeds.sql
 */

require_once dirname(__DIR__) . '/includes/env.php';
pawdar_load_env();
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once __DIR__ . '/runner.php';

if (file_exists(dirname(__DIR__) . '/includes/db.local.php')) {
    require_once dirname(__DIR__) . '/includes/db.local.php';
}

$host = pawdar_env('PAWDAR_DB_HOST', 'localhost') ?? 'localhost';
$user = pawdar_env('PAWDAR_DB_USER', 'root') ?? 'root';
$pass = pawdar_env('PAWDAR_DB_PASS', '') ?? '';
$dbName = pawdar_env('PAWDAR_DB_NAME', 'pawdar') ?? 'pawdar';

try {
    $pdo = new PDO('mysql:host=' . $host . ';charset=utf8mb4', $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $safeDb = str_replace('`', '', $dbName);
    $pdo->exec(
        'CREATE DATABASE IF NOT EXISTS `' . $safeDb . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
    );
    $pdo->exec('USE `' . $safeDb . '`');

    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    if ($schemaSql === false) {
        throw new RuntimeException('Could not read schema.sql');
    }
    $schemaSql = preg_replace('/^\s*CREATE DATABASE[^;]+;\s*\n?/mi', '', $schemaSql) ?? $schemaSql;
    $schemaSql = preg_replace('/^\s*USE\s+\w+\s*;\s*\n?/mi', '', $schemaSql) ?? $schemaSql;
    pawdar_run_sql_contents($pdo, $schemaSql);

    pawdar_apply_column_migrations($pdo);
    pawdar_run_sql_file($pdo, __DIR__ . '/schema-v2.sql', true);
    pawdar_run_sql_file($pdo, __DIR__ . '/schema-v3-breeds.sql', true);
    pawdar_run_sql_file($pdo, __DIR__ . '/schema-v4-screens.sql', true);
    pawdar_run_sql_file($pdo, __DIR__ . '/schema-v5-locations.sql', true);
    pawdar_run_sql_file($pdo, __DIR__ . '/schema-v6-auth-user.sql', true);
    pawdar_ensure_breed_foreign_key($pdo);

    $breedCount = 0;
    $breedsTable = (int) $pdo->query(
        "SELECT COUNT(*) FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'breeds'"
    )->fetchColumn();

    if ($breedsTable > 0) {
        $breedCount = (int) $pdo->query('SELECT COUNT(*) FROM breeds')->fetchColumn();
    }

    echo "Database setup complete.\n";
    echo "Demo login: maria.santos@email.com / password\n";

    if ($breedCount === 0) {
        echo "Breeds table is empty — run: php sql/import-breeds.php\n";
        echo "  Or import sql/schema-v3-breeds-seed.sql in phpMyAdmin (after schema-v3-breeds.sql)\n";
    } else {
        echo "Breeds loaded: {$breedCount}\n";
    }

    $cityCount = 0;
    $cityTable = (int) $pdo->query(
        "SELECT COUNT(*) FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'city'"
    )->fetchColumn();
    if ($cityTable > 0) {
        $cityCount = (int) $pdo->query('SELECT COUNT(*) FROM city')->fetchColumn();
    }

    if ($cityCount === 0) {
        echo "City/barangay tables empty — run: php sql/import-barangays.php\n";
    } else {
        echo "Cities loaded: {$cityCount}\n";
    }
} catch (Throwable $e) {
    echo 'Setup failed: ' . $e->getMessage() . "\n";
    exit(1);
}
