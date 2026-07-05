<?php
/**
 * Import Kaggle breed CSV into pawdar.breeds
 * Run: php sql/import-breeds.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/includes/helpers.php';
require_once __DIR__ . '/runner.php';
if (file_exists($root . '/includes/db.local.php')) {
    require_once $root . '/includes/db.local.php';
}

$csvPath = dirname($root) . '/archive/dogs_cleaned.csv';
if (!is_readable($csvPath)) {
    fwrite(STDERR, "Missing CSV: {$csvPath}\n");
    exit(1);
}

$host = getenv('PAWDAR_DB_HOST') ?: 'localhost';
$user = getenv('PAWDAR_DB_USER') ?: 'root';
$pass = getenv('PAWDAR_DB_PASS') ?: '';
$dbName = getenv('PAWDAR_DB_NAME') ?: 'pawdar';

try {
    $pdo = new PDO('mysql:host=' . $host . ';charset=utf8mb4', $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('USE `' . str_replace('`', '', $dbName) . '`');
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

    $fkName = $pdo->query("
        SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dog'
          AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'fk_dog_breed'
        LIMIT 1
    ")->fetchColumn();
    if ($fkName) {
        $pdo->exec('ALTER TABLE dog DROP FOREIGN KEY fk_dog_breed');
    }

    $pdo->exec('DROP TABLE IF EXISTS breeds_staging');
    $pdo->exec('DROP TABLE IF EXISTS breeds');
    pawdar_run_sql_file($pdo, __DIR__ . '/schema-v3-breeds.sql');
    pawdar_add_column($pdo, 'dog', 'breed_id', 'INT NULL AFTER Breed');

    $handle = fopen($csvPath, 'r');
    if ($handle === false) {
        throw new RuntimeException('Could not open CSV');
    }

    $headers = fgetcsv($handle);
    if ($headers === false) {
        throw new RuntimeException('CSV has no header row');
    }

    $index = [];
    foreach ($headers as $i => $header) {
        $index[trim($header)] = $i;
    }

    $required = ['Breed Name', 'Dog Size', 'Life Span', 'Dog Breed Group', 'Energy Level'];
    foreach ($required as $col) {
        if (!isset($index[$col])) {
            throw new RuntimeException("Missing CSV column: {$col}");
        }
    }

    $staging = $pdo->prepare('
        INSERT INTO breeds_staging (
            breed_name, dog_size, weight_text, weight_kg, lifespan, breed_group,
            affection_family, kid_friendly, dog_friendly, stranger_friendly,
            general_health, energy_level, easy_to_train, intelligence
        ) VALUES (
            :breed_name, :dog_size, :weight_text, :weight_kg, :lifespan, :breed_group,
            :affection_family, :kid_friendly, :dog_friendly, :stranger_friendly,
            :general_health, :energy_level, :easy_to_train, :intelligence
        )
    ');

    $rowCount = 0;
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) < 2) {
            continue;
        }

        $name = trim((string) ($row[$index['Breed Name']] ?? ''));
        if ($name === '') {
            continue;
        }

        $staging->execute([
            ':breed_name' => $name,
            ':dog_size' => val($row, $index, 'Dog Size'),
            ':weight_text' => val($row, $index, 'Weight'),
            ':weight_kg' => val($row, $index, 'Avg. Weight, kg'),
            ':lifespan' => val($row, $index, 'Life Span'),
            ':breed_group' => val($row, $index, 'Dog Breed Group'),
            ':affection_family' => val($row, $index, 'Affectionate With Family'),
            ':kid_friendly' => val($row, $index, 'Kid-Friendly'),
            ':dog_friendly' => val($row, $index, 'Dog Friendly'),
            ':stranger_friendly' => val($row, $index, 'Friendly Toward Strangers'),
            ':general_health' => val($row, $index, 'General Health'),
            ':energy_level' => val($row, $index, 'Energy Level'),
            ':easy_to_train' => val($row, $index, 'Easy To Train'),
            ':intelligence' => val($row, $index, 'Intelligence'),
        ]);
        $rowCount++;
    }
    fclose($handle);

    echo "Loaded {$rowCount} rows into breeds_staging\n";

    $pdo->exec('
        INSERT INTO breeds (
            breed_name, size_category, weight_range, lifespan,
            temperament_notes, common_health_risks,
            loyalty_score, energy_score, friendliness_score
        )
        SELECT
            TRIM(breed_name),
            CASE
                WHEN LOWER(dog_size) LIKE \'%small%\' THEN \'Small\'
                WHEN LOWER(dog_size) LIKE \'%medium%\' THEN \'Medium\'
                WHEN LOWER(dog_size) LIKE \'%large%\' THEN \'Large\'
                WHEN LOWER(dog_size) LIKE \'%very large%\' THEN \'Large\'
                ELSE \'Medium\'
            END,
            CASE
                WHEN weight_kg IS NOT NULL AND weight_kg != \'\' THEN CONCAT(ROUND(CAST(weight_kg AS DECIMAL(8,2)), 1), \' kg\')
                WHEN weight_text IS NOT NULL AND weight_text != \'\' THEN LEFT(weight_text, 30)
                ELSE NULL
            END,
            LEFT(lifespan, 20),
            LEFT(CONCAT(breed_group, \' — loyal companion breed\'), 255),
            CASE
                WHEN CAST(general_health AS DECIMAL(4,2)) <= 2.5 THEN \'Higher health risk — regular vet monitoring recommended\'
                WHEN CAST(general_health AS DECIMAL(4,2)) >= 4 THEN \'Generally hardy breed\'
                ELSE \'Average breed health profile\'
            END,
            LEAST(5, GREATEST(1, ROUND(COALESCE(CAST(affection_family AS DECIMAL(4,2)), 3)))),
            LEAST(5, GREATEST(1, ROUND(COALESCE(CAST(energy_level AS DECIMAL(4,2)), 3)))),
            LEAST(5, GREATEST(1, ROUND((
                COALESCE(CAST(kid_friendly AS DECIMAL(4,2)), 3) +
                COALESCE(CAST(dog_friendly AS DECIMAL(4,2)), 3) +
                COALESCE(CAST(stranger_friendly AS DECIMAL(4,2)), 3)
            ) / 3)))
        FROM breeds_staging
        WHERE breed_name IS NOT NULL AND breed_name != \'\'
        ON DUPLICATE KEY UPDATE
            temperament_notes = VALUES(temperament_notes),
            common_health_risks = VALUES(common_health_risks),
            loyalty_score = VALUES(loyalty_score),
            energy_score = VALUES(energy_score),
            friendliness_score = VALUES(friendliness_score)
    ');

    $pdo->prepare('
        INSERT INTO breeds (
            breed_name, size_category, weight_range, lifespan,
            temperament_notes, common_health_risks,
            loyalty_score, energy_score, friendliness_score
        ) VALUES (
            \'Aspin (Asong Pinoy)\', \'Medium\', \'8 - 20 kg\', \'10 - 14 years\',
            \'Loyal, alert, adaptable, territorial\',
            \'Generally hardy; skin allergies, tick-borne disease\',
            5, 4, 3
        )
        ON DUPLICATE KEY UPDATE
            temperament_notes = VALUES(temperament_notes),
            common_health_risks = VALUES(common_health_risks),
            loyalty_score = VALUES(loyalty_score),
            energy_score = VALUES(energy_score),
            friendliness_score = VALUES(friendliness_score)
    ')->execute();

    $pdo->exec('
        UPDATE dog d
        JOIN breeds b ON LOWER(TRIM(d.Breed)) = LOWER(TRIM(b.breed_name))
        SET d.breed_id = b.breed_id
    ');
    $pdo->exec('
        UPDATE dog d
        JOIN breeds b ON b.breed_name = \'Aspin (Asong Pinoy)\'
        SET d.breed_id = b.breed_id
        WHERE LOWER(TRIM(d.Breed)) LIKE \'%aspin%\'
    ');

    pawdar_ensure_breed_foreign_key($pdo);

    $pdo->exec('DROP TABLE IF EXISTS breeds_staging');

    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

    $total = (int) $pdo->query('SELECT COUNT(*) FROM breeds')->fetchColumn();
    echo "Import complete. Total breeds: {$total}\n";
    foreach ($pdo->query('SELECT size_category, COUNT(*) AS cnt FROM breeds GROUP BY size_category ORDER BY size_category') as $row) {
        echo "  {$row['size_category']}: {$row['cnt']}\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Import failed: ' . $e->getMessage() . "\n");
    exit(1);
}

/**
 * @param array<int, string> $row
 * @param array<string, int> $index
 */
function val(array $row, array $index, string $key): ?string
{
    if (!isset($index[$key])) {
        return null;
    }

    $value = trim((string) ($row[$index[$key]] ?? ''));

    return $value === '' ? null : $value;
}
