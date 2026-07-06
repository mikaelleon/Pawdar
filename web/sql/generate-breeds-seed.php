<?php

declare(strict_types=1);

/**
 * Generates schema-v3-breeds-seed.sql from archive/dogs_cleaned.csv.
 * Run on XAMPP: php sql/generate-breeds-seed.php
 * Import via phpMyAdmin after schema-v3-breeds.sql (InfinityFree — no CLI).
 */

$root = dirname(__DIR__);
$csvPath = dirname($root) . '/archive/dogs_cleaned.csv';
$outPath = __DIR__ . '/schema-v3-breeds-seed.sql';

/**
 * @param array<int, string> $row
 * @param array<string, int> $index
 */
function pawdar_csv_val(array $row, array $index, string $key): ?string
{
    if (!isset($index[$key])) {
        return null;
    }

    $value = trim((string) ($row[$index[$key]] ?? ''));

    return $value === '' ? null : $value;
}

function pawdar_sql_escape(string $value): string
{
    return str_replace(["\\", "'"], ["\\\\", "\\'"], $value);
}

function pawdar_map_size(?string $dogSize): string
{
    $lower = strtolower((string) $dogSize);
    if (strpos($lower, 'small') !== false) {
        return 'Small';
    }
    if (strpos($lower, 'medium') !== false) {
        return 'Medium';
    }
    if (strpos($lower, 'large') !== false) {
        return 'Large';
    }

    return 'Medium';
}

function pawdar_map_weight(?string $weightKg, ?string $weightText): ?string
{
    if ($weightKg !== null && $weightKg !== '' && is_numeric($weightKg)) {
        return round((float) $weightKg, 1) . ' kg';
    }

    if ($weightText !== null && $weightText !== '') {
        return mb_substr($weightText, 0, 30);
    }

    return null;
}

function pawdar_score(?string $value, int $default = 3): int
{
    if ($value === null || $value === '' || !is_numeric($value)) {
        return $default;
    }

    return max(1, min(5, (int) round((float) $value)));
}

function pawdar_friendliness_score(?string $kid, ?string $dog, ?string $stranger): int
{
    $scores = [];
    foreach ([$kid, $dog, $stranger] as $value) {
        if ($value !== null && $value !== '' && is_numeric($value)) {
            $scores[] = (float) $value;
        }
    }

    if ($scores === []) {
        return 3;
    }

    return max(1, min(5, (int) round(array_sum($scores) / count($scores))));
}

function pawdar_health_risk(?string $generalHealth): string
{
    if ($generalHealth === null || $generalHealth === '' || !is_numeric($generalHealth)) {
        return 'Average breed health profile';
    }

    $score = (float) $generalHealth;
    if ($score <= 2.5) {
        return 'Higher health risk — regular vet monitoring recommended';
    }
    if ($score >= 4) {
        return 'Generally hardy breed';
    }

    return 'Average breed health profile';
}

function pawdar_temperament(?string $breedGroup): string
{
    $group = trim((string) $breedGroup);
    if ($group === '') {
        return 'Loyal companion breed';
    }

    return mb_substr($group . ' — loyal companion breed', 0, 255);
}

if (!is_readable($csvPath)) {
    fwrite(STDERR, "Missing CSV: {$csvPath}\n");
    exit(1);
}

$handle = fopen($csvPath, 'r');
if ($handle === false) {
    fwrite(STDERR, "Could not open CSV.\n");
    exit(1);
}

$headers = fgetcsv($handle);
if ($headers === false) {
    fwrite(STDERR, "CSV has no header row.\n");
    exit(1);
}

$index = [];
foreach ($headers as $i => $header) {
    $index[trim($header)] = $i;
}

$required = ['Breed Name', 'Dog Size', 'Life Span', 'Dog Breed Group', 'Energy Level'];
foreach ($required as $col) {
    if (!isset($index[$col])) {
        fwrite(STDERR, "Missing CSV column: {$col}\n");
        exit(1);
    }
}

$breeds = [];
while (($row = fgetcsv($handle)) !== false) {
    if (count($row) < 2) {
        continue;
    }

    $name = trim((string) ($row[$index['Breed Name']] ?? ''));
    if ($name === '') {
        continue;
    }

    $breedGroup = pawdar_csv_val($row, $index, 'Dog Breed Group');
    $breeds[$name] = [
        'breed_name' => $name,
        'size_category' => pawdar_map_size(pawdar_csv_val($row, $index, 'Dog Size')),
        'weight_range' => pawdar_map_weight(
            pawdar_csv_val($row, $index, 'Avg. Weight, kg'),
            pawdar_csv_val($row, $index, 'Weight')
        ),
        'lifespan' => mb_substr((string) (pawdar_csv_val($row, $index, 'Life Span') ?? ''), 0, 20) ?: null,
        'temperament_notes' => pawdar_temperament($breedGroup),
        'common_health_risks' => pawdar_health_risk(pawdar_csv_val($row, $index, 'General Health')),
        'loyalty_score' => pawdar_score(pawdar_csv_val($row, $index, 'Affectionate With Family')),
        'energy_score' => pawdar_score(pawdar_csv_val($row, $index, 'Energy Level')),
        'friendliness_score' => pawdar_friendliness_score(
            pawdar_csv_val($row, $index, 'Kid-Friendly'),
            pawdar_csv_val($row, $index, 'Dog Friendly'),
            pawdar_csv_val($row, $index, 'Friendly Toward Strangers')
        ),
    ];
}
fclose($handle);

$breeds['Aspin (Asong Pinoy)'] = [
    'breed_name' => 'Aspin (Asong Pinoy)',
    'size_category' => 'Medium',
    'weight_range' => '8 - 20 kg',
    'lifespan' => '10 - 14 years',
    'temperament_notes' => 'Loyal, alert, adaptable, territorial',
    'common_health_risks' => 'Generally hardy; skin allergies, tick-borne disease',
    'loyalty_score' => 5,
    'energy_score' => 4,
    'friendliness_score' => 3,
];

ksort($breeds, SORT_NATURAL | SORT_FLAG_CASE);

$lines = [];
$lines[] = '-- Pawdar v3 breed seed data (Kaggle archive/dogs_cleaned.csv + Aspin)';
$lines[] = '-- Source: archive/dogs_cleaned.csv (' . count($breeds) . ' breeds including Aspin)';
$lines[] = '-- Regenerate locally: php sql/generate-breeds-seed.php';
$lines[] = '-- Run AFTER schema-v3-breeds.sql (creates breeds table).';
$lines[] = '-- Safe to re-run: uses ON DUPLICATE KEY UPDATE on breed_name.';
$lines[] = '';
$lines[] = 'CREATE TABLE IF NOT EXISTS breeds (';
$lines[] = '    breed_id INT AUTO_INCREMENT PRIMARY KEY,';
$lines[] = '    breed_name VARCHAR(100) NOT NULL UNIQUE,';
$lines[] = '    size_category ENUM(\'Small\', \'Medium\', \'Large\') NOT NULL,';
$lines[] = '    weight_range VARCHAR(30) NULL,';
$lines[] = '    lifespan VARCHAR(20) NULL,';
$lines[] = '    temperament_notes VARCHAR(255) NULL,';
$lines[] = '    common_health_risks VARCHAR(255) NULL,';
$lines[] = '    loyalty_score TINYINT NOT NULL DEFAULT 3,';
$lines[] = '    energy_score TINYINT NOT NULL DEFAULT 3,';
$lines[] = '    friendliness_score TINYINT NOT NULL DEFAULT 3,';
$lines[] = '    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,';
$lines[] = '    CONSTRAINT chk_loyalty CHECK (loyalty_score BETWEEN 1 AND 5),';
$lines[] = '    CONSTRAINT chk_energy CHECK (energy_score BETWEEN 1 AND 5),';
$lines[] = '    CONSTRAINT chk_friendliness CHECK (friendliness_score BETWEEN 1 AND 5)';
$lines[] = ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
$lines[] = '';

foreach ($breeds as $breed) {
    $weight = $breed['weight_range'] !== null ? "'" . pawdar_sql_escape($breed['weight_range']) . "'" : 'NULL';
    $lifespan = $breed['lifespan'] !== null ? "'" . pawdar_sql_escape($breed['lifespan']) . "'" : 'NULL';

    $lines[] = 'INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (';
    $lines[] = "    '" . pawdar_sql_escape($breed['breed_name']) . "',";
    $lines[] = "    '" . pawdar_sql_escape($breed['size_category']) . "',";
    $lines[] = '    ' . $weight . ',';
    $lines[] = '    ' . $lifespan . ',';
    $lines[] = "    '" . pawdar_sql_escape($breed['temperament_notes']) . "',";
    $lines[] = "    '" . pawdar_sql_escape($breed['common_health_risks']) . "',";
    $lines[] = '    ' . (int) $breed['loyalty_score'] . ',';
    $lines[] = '    ' . (int) $breed['energy_score'] . ',';
    $lines[] = '    ' . (int) $breed['friendliness_score'];
    $lines[] = ') ON DUPLICATE KEY UPDATE';
    $lines[] = '    size_category = VALUES(size_category),';
    $lines[] = '    weight_range = VALUES(weight_range),';
    $lines[] = '    lifespan = VALUES(lifespan),';
    $lines[] = '    temperament_notes = VALUES(temperament_notes),';
    $lines[] = '    common_health_risks = VALUES(common_health_risks),';
    $lines[] = '    loyalty_score = VALUES(loyalty_score),';
    $lines[] = '    energy_score = VALUES(energy_score),';
    $lines[] = '    friendliness_score = VALUES(friendliness_score);';
    $lines[] = '';
}

$lines[] = '-- Link existing demo dogs to breeds by name (requires dog.breed_id column from setup/migrations)';
$lines[] = 'UPDATE dog d';
$lines[] = 'INNER JOIN breeds b ON LOWER(TRIM(d.Breed)) = LOWER(TRIM(b.breed_name))';
$lines[] = 'SET d.breed_id = b.breed_id;';
$lines[] = '';
$lines[] = 'UPDATE dog d';
$lines[] = 'INNER JOIN breeds b ON b.breed_name = \'Aspin (Asong Pinoy)\'';
$lines[] = 'SET d.breed_id = b.breed_id';
$lines[] = 'WHERE LOWER(TRIM(d.Breed)) LIKE \'%aspin%\';';
$lines[] = '';

$content = implode("\n", $lines) . "\n";
if (file_put_contents($outPath, $content) === false) {
    fwrite(STDERR, "Could not write {$outPath}\n");
    exit(1);
}

echo 'Wrote ' . count($breeds) . " breeds to {$outPath}\n";
