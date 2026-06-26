<?php

/**
 * PDO database connection singleton.
 */
if (file_exists(__DIR__ . '/db.local.php')) {
    require_once __DIR__ . '/db.local.php';
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = getenv('PAWDAR_DB_HOST') ?: 'localhost';
        $name = getenv('PAWDAR_DB_NAME') ?: 'pawdar';
        $user = getenv('PAWDAR_DB_USER') ?: 'root';
        $pass = getenv('PAWDAR_DB_PASS') ?: '';

        $dsn = 'mysql:host=' . $host . ';dbname=' . $name . ';charset=utf8mb4';

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    return $pdo;
}
