<?php

declare(strict_types=1);

/**
 * One-time local generator: writes schema-v5-barangays-seed.sql from docs markdown.
 * Run on XAMPP: php sql/generate-barangays-seed.php
 * Upload the output .sql to InfinityFree phpMyAdmin — no CLI on host needed.
 */

$root = dirname(__DIR__);
$mdPath = dirname($root) . '/docs/Batangas_Cities_and_Barangays.md';
$outPath = __DIR__ . '/schema-v5-barangays-seed.sql';

/**
 * @return list<array{name: string, barangays: list<string>}>
 */
function pawdar_parse_barangay_markdown_for_seed(string $path): array
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

function pawdar_sql_escape(string $value): string
{
    return str_replace(["\\", "'"], ["\\\\", "\\'"], $value);
}

if (!is_readable($mdPath)) {
    fwrite(STDERR, "Missing: {$mdPath}\n");
    exit(1);
}

$parsed = pawdar_parse_barangay_markdown_for_seed($mdPath);
if ($parsed === []) {
    fwrite(STDERR, "No cities parsed.\n");
    exit(1);
}

$lines = [];
$lines[] = '-- Pawdar v5 barangay seed data (Batangas Province)';
$lines[] = '-- Generated from docs/Batangas_Cities_and_Barangays.md';
$lines[] = '-- Run AFTER schema-v5-locations.sql (creates city/barangay tables).';
$lines[] = '-- Safe to re-run: uses ON DUPLICATE KEY UPDATE.';
$lines[] = '';
$lines[] = '-- Ensure tables exist (skip if you already ran schema-v5-locations.sql)';
$lines[] = file_get_contents(__DIR__ . '/schema-v5-locations.sql');
$lines[] = '';

$cityId = 1;
foreach ($parsed as $city) {
    $name = pawdar_sql_escape($city['name']);
    $lines[] = "INSERT INTO city (city_id, name) VALUES ({$cityId}, '{$name}')";
    $lines[] = 'ON DUPLICATE KEY UPDATE name = VALUES(name);';
    $lines[] = '';

    $batch = [];
    foreach ($city['barangays'] as $barangayName) {
        $bName = pawdar_sql_escape($barangayName);
        $batch[] = "({$cityId}, '{$bName}')";
    }

    foreach (array_chunk($batch, 50) as $chunk) {
        $lines[] = 'INSERT INTO barangay (city_id, name) VALUES';
        $lines[] = implode(",\n", $chunk);
        $lines[] = 'ON DUPLICATE KEY UPDATE name = VALUES(name);';
        $lines[] = '';
    }

    $cityId++;
}

$barangayTotal = array_sum(array_map(static fn (array $c): int => count($c['barangays']), $parsed));
$lines[] = '-- Cities: ' . count($parsed) . ', Barangays: ' . $barangayTotal;

file_put_contents($outPath, implode("\n", $lines) . "\n");

echo "Wrote {$outPath}\n";
echo 'Cities: ' . count($parsed) . ', Barangays: ' . $barangayTotal . "\n";
