<?php

/**
 * Loads environment variables from .env files (KEY=VALUE format).
 * Checks repo root first, then web/.env (later files override earlier keys).
 */

/**
 * Loads .env files if present. Safe to call multiple times.
 */
function pawdar_load_env(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }

    $paths = [
        dirname(__DIR__, 2) . '/.env',
        dirname(__DIR__) . '/.env',
    ];

    foreach ($paths as $path) {
        if (is_readable($path)) {
            pawdar_parse_env_file($path);
        }
    }

    $loaded = true;
}

/**
 * Reads a config value from $_ENV first (InfinityFree-safe when putenv is disabled).
 */
function pawdar_env(string $key, ?string $default = null): ?string
{
    if (array_key_exists($key, $_ENV)) {
        $value = $_ENV[$key];

        return $value === '' && $default !== null ? $default : (string) $value;
    }

    if (function_exists('getenv')) {
        $value = getenv($key);
        if ($value !== false) {
            return (string) $value;
        }
    }

    return $default;
}

/**
 * @param string $path Absolute path to a .env file
 */
function pawdar_parse_env_file(string $path): void
{
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $equals = strpos($line, '=');
        if ($equals === false) {
            continue;
        }

        $key = trim(substr($line, 0, $equals));
        $value = trim(substr($line, $equals + 1));

        if ($key === '') {
            continue;
        }

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$key] = $value;
        if (function_exists('putenv')) {
            putenv($key . '=' . $value);
        }
    }
}
