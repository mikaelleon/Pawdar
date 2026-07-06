<?php

declare(strict_types=1);

/**
 * Shared helpers for Pawdar SQL migrations (MariaDB 10.4+ / MySQL 5.7+).
 */

/**
 * Returns true when a column already exists on the current database table.
 */
function pawdar_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $stmt->execute([$table, $column]);

    return (int) $stmt->fetchColumn() > 0;
}

/**
 * Adds a column only when it is missing (safe to re-run).
 */
function pawdar_add_column(PDO $pdo, string $table, string $column, string $definition): void
{
    if (pawdar_column_exists($pdo, $table, $column)) {
        return;
    }

    $pdo->exec('ALTER TABLE `' . str_replace('`', '', $table) . '` ADD COLUMN `' . str_replace('`', '', $column) . '` ' . $definition);
}

/**
 * Executes SQL text (semicolon-terminated statements).
 *
 * @param list<string> $benignErrorFragments Error substrings ignored when $ignoreBenignErrors is true
 */
function pawdar_run_sql_contents(PDO $pdo, string $contents, bool $ignoreBenignErrors = false, array $benignErrorFragments = []): void
{
    $contents = preg_replace('/^\s*--[^\n]*\n/m', '', $contents) ?? $contents;
    $contents = preg_replace('/^\s*USE\s+\w+\s*;\s*\n?/mi', '', $contents) ?? $contents;

    $statements = preg_split('/;\s*(?=\n|$)/', $contents) ?: [];
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement === '') {
            continue;
        }

        try {
            $pdo->exec($statement);
        } catch (PDOException $exception) {
            if (!$ignoreBenignErrors) {
                throw $exception;
            }

            $message = $exception->getMessage();
            $defaultBenign = [
                'Duplicate column',
                'already exists',
                'Duplicate key name',
                'Duplicate entry',
            ];
            $needles = array_merge($defaultBenign, $benignErrorFragments);
            $isBenign = false;
            foreach ($needles as $needle) {
                if (stripos($message, $needle) !== false) {
                    $isBenign = true;
                    break;
                }
            }

            if (!$isBenign) {
                throw $exception;
            }
        }
    }
}

/**
 * Executes statements from a .sql file (semicolon-terminated, one statement per block).
 *
 * @param list<string> $benignErrorFragments Error substrings ignored when $ignoreBenignErrors is true
 */
function pawdar_run_sql_file(PDO $pdo, string $path, bool $ignoreBenignErrors = false, array $benignErrorFragments = []): void
{
    $contents = file_get_contents($path);
    if ($contents === false) {
        throw new RuntimeException('Could not read ' . $path);
    }

    pawdar_run_sql_contents($pdo, $contents, $ignoreBenignErrors, $benignErrorFragments);
}

/**
 * Applies idempotent column migrations introduced after schema.sql.
 */
function pawdar_apply_column_migrations(PDO $pdo): void
{
    pawdar_add_column($pdo, 'dog', 'RegistryID', "VARCHAR(20) NULL AFTER dog_id");
    pawdar_add_column($pdo, 'dog', 'Gender', "VARCHAR(20) NULL AFTER Breed");
    pawdar_add_column($pdo, 'dog', 'Size', "ENUM('Small', 'Medium', 'Large') NULL AFTER Gender");
    pawdar_add_column($pdo, 'dog', 'DogType', "VARCHAR(50) NULL AFTER Size");
    pawdar_add_column($pdo, 'dog', 'Status', "ENUM('Registered', 'Pending', 'Inactive') NOT NULL DEFAULT 'Registered' AFTER DogType");
    pawdar_add_column($pdo, 'dog', 'breed_id', 'INT NULL AFTER Breed');
    pawdar_add_column($pdo, 'dog', 'Age', 'INT NULL AFTER Status');
    pawdar_add_column($pdo, 'dog', 'photo_path', 'VARCHAR(255) NULL AFTER Age');
    pawdar_add_column($pdo, 'dog', 'health_notes', 'TEXT NULL AFTER photo_path');
    pawdar_add_column($pdo, 'dog', 'coat_color', 'VARCHAR(80) NULL AFTER health_notes');
    pawdar_add_column($pdo, 'dog', 'weight_kg', 'DECIMAL(6, 2) NULL AFTER coat_color');
    pawdar_add_column($pdo, 'dog', 'distinguishing_marks', 'TEXT NULL AFTER weight_kg');
    pawdar_add_column($pdo, 'dog', 'temperament_notes', 'TEXT NULL AFTER distinguishing_marks');

    pawdar_add_column($pdo, 'breeds', 'image_url', 'VARCHAR(512) NULL AFTER friendliness_score');

    pawdar_add_column($pdo, 'case', 'RabiesMonitoring', 'TINYINT NOT NULL DEFAULT 0 AFTER CaseStatus');
    pawdar_add_column($pdo, 'case', 'assigned_to', 'INT NULL AFTER RabiesMonitoring');

    pawdar_add_column($pdo, 'incident', 'latitude', 'DECIMAL(10, 7) NULL AFTER Location');
    pawdar_add_column($pdo, 'incident', 'longitude', 'DECIMAL(10, 7) NULL AFTER latitude');
    pawdar_add_column($pdo, 'incident', 'photo_path', 'VARCHAR(255) NULL AFTER Description');
    pawdar_add_column($pdo, 'incident', 'edited_at', 'DATETIME NULL AFTER photo_path');
    pawdar_add_column($pdo, 'incident', 'area_regular', 'TINYINT NOT NULL DEFAULT 0 AFTER edited_at');

    pawdar_add_column($pdo, 'notifications', 'notification_type', "VARCHAR(30) NOT NULL DEFAULT 'general' AFTER message");

    pawdar_add_column($pdo, 'user', 'notify_incidents', 'TINYINT NOT NULL DEFAULT 1 AFTER Phone');
    pawdar_add_column($pdo, 'user', 'notify_dog_match', 'TINYINT NOT NULL DEFAULT 1 AFTER notify_incidents');
    pawdar_add_column($pdo, 'user', 'notify_case_updates', 'TINYINT NOT NULL DEFAULT 1 AFTER notify_dog_match');
    pawdar_add_column($pdo, 'user', 'notify_vaccine', 'TINYINT NOT NULL DEFAULT 1 AFTER notify_case_updates');

    pawdar_add_column($pdo, 'vaccinerecord', 'NextDueDate', 'DATE NULL AFTER DateGiven');
    pawdar_add_column(
        $pdo,
        'vaccinerecord',
        'vax_status',
        "ENUM('Verified', 'Unverified', 'Expired') NOT NULL DEFAULT 'Unverified' AFTER VetName"
    );

    pawdar_add_column($pdo, 'user', 'reset_token', 'VARCHAR(64) NULL AFTER Phone');
    pawdar_add_column($pdo, 'user', 'reset_token_expires', 'DATETIME NULL AFTER reset_token');

    pawdar_add_column($pdo, 'user', 'last_name', 'VARCHAR(80) NULL AFTER Name');
    pawdar_add_column($pdo, 'user', 'first_name', 'VARCHAR(80) NULL AFTER last_name');
    pawdar_add_column($pdo, 'user', 'middle_name', 'VARCHAR(80) NULL AFTER first_name');
    pawdar_add_column($pdo, 'user', 'name_suffix', 'VARCHAR(20) NULL AFTER middle_name');
    pawdar_add_column($pdo, 'user', 'City', 'VARCHAR(100) NULL AFTER Barangay');
    pawdar_add_column($pdo, 'user', 'city_id', 'INT NULL AFTER City');
    pawdar_add_column($pdo, 'user', 'barangay_id', 'INT NULL AFTER city_id');
    pawdar_add_column($pdo, 'user', 'email_verified_at', 'DATETIME NULL AFTER barangay_id');
    pawdar_add_column($pdo, 'user', 'email_verify_token', 'VARCHAR(64) NULL AFTER email_verified_at');
    pawdar_add_column($pdo, 'user', 'email_verify_expires', 'DATETIME NULL AFTER email_verify_token');
}

/**
 * Ensures dog.breed_id references breeds when both tables exist.
 */
function pawdar_ensure_breed_foreign_key(PDO $pdo): void
{
    $breedsExists = (int) $pdo->query(
        "SELECT COUNT(*) FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'breeds'"
    )->fetchColumn();

    if ($breedsExists === 0 || !pawdar_column_exists($pdo, 'dog', 'breed_id')) {
        return;
    }

    $fkExists = (int) $pdo->query(
        "SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dog'
           AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'fk_dog_breed'"
    )->fetchColumn();

    if ($fkExists > 0) {
        return;
    }

    try {
        $pdo->exec(
            'ALTER TABLE dog ADD CONSTRAINT fk_dog_breed
             FOREIGN KEY (breed_id) REFERENCES breeds(breed_id) ON DELETE SET NULL'
        );
    } catch (PDOException $exception) {
        // Ignore when legacy data prevents FK creation; import-breeds.php remaps rows.
    }
}
