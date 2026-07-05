<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

/**
 * @return list<array<string, mixed>>
 */
function fetch_first_aid_guides(PDO $pdo): array
{
    $stmt = $pdo->query('
        SELECT * FROM first_aid_guides
        ORDER BY FIELD(severity_level, "Severe", "Moderate", "Mild"), sort_order ASC
    ');

    return $stmt->fetchAll();
}

/**
 * @return array<string, mixed>|null
 */
function fetch_first_aid_guide(PDO $pdo, int $guideId): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM first_aid_guides WHERE guide_id = :id LIMIT 1');
    $stmt->execute([':id' => $guideId]);
    $guide = $stmt->fetch();

    if (!$guide) {
        return null;
    }

    $guide['steps'] = json_decode((string) $guide['steps'], true) ?: [];

    return $guide;
}

/**
 * @return array<string, mixed>|null
 */
function fetch_first_aid_by_type(PDO $pdo, string $incidentType): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM first_aid_guides WHERE incident_type = :type LIMIT 1');
    $stmt->execute([':type' => $incidentType]);
    $guide = $stmt->fetch();

    if (!$guide) {
        return null;
    }

    $guide['steps'] = json_decode((string) $guide['steps'], true) ?: [];

    return $guide;
}

/**
 * Maps severity to badge class.
 */
function first_aid_severity_badge(string $severity): string
{
    return severity_badge_class($severity);
}

/**
 * Maps severity to accent class.
 */
function first_aid_severity_accent(string $severity): string
{
    return match ($severity) {
        'Severe' => 'accent-bite',
        'Moderate' => 'accent-injured',
        default => 'accent-resolved',
    };
}
