<?php
/**
 * One-time setup: creates database tables and demo users via PHP/PDO.
 * Run: php sql/setup.php
 */

require_once dirname(__DIR__) . '/includes/helpers.php';
if (file_exists(dirname(__DIR__) . '/includes/db.local.php')) {
    require_once dirname(__DIR__) . '/includes/db.local.php';
}

$host = getenv('PAWDAR_DB_HOST') ?: 'localhost';
$user = getenv('PAWDAR_DB_USER') ?: 'root';
$pass = getenv('PAWDAR_DB_PASS') ?: '';

try {
    $pdo = new PDO('mysql:host=' . $host . ';charset=utf8mb4', $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $sql = file_get_contents(__DIR__ . '/schema.sql');
    if ($sql === false) {
        throw new RuntimeException('Could not read schema.sql');
    }

    foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
        if ($statement === '' || stripos($statement, '--') === 0) {
            continue;
        }

        $pdo->exec($statement);
    }

    $v2 = file_get_contents(__DIR__ . '/schema-v2.sql');
    if ($v2 !== false) {
        foreach (array_filter(array_map('trim', explode(';', $v2))) as $statement) {
            if ($statement === '' || stripos($statement, '--') === 0 || stripos($statement, 'USE pawdar') === 0) {
                continue;
            }
            try {
                $pdo->exec($statement);
            } catch (PDOException $ignored) {
                // Ignore duplicate migration errors on re-run.
            }
        }
    }

    echo "Database setup complete.\n";
    echo "Demo login: maria.santos@email.com / password\n";
} catch (Throwable $e) {
    echo 'Setup failed: ' . $e->getMessage() . "\n";
    exit(1);
}
