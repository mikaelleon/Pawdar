<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

/**
 * Normalizes a guide step from legacy string or structured object.
 *
 * @param mixed $step
 * @return array{summary: string, icon: string, detail: string}
 */
function normalize_guide_step(mixed $step): array
{
    if (is_string($step)) {
        return [
            'summary' => $step,
            'icon' => 'circle',
            'detail' => '',
        ];
    }

    if (!is_array($step)) {
        return [
            'summary' => '',
            'icon' => 'circle',
            'detail' => '',
        ];
    }

    return [
        'summary' => (string) ($step['summary'] ?? ''),
        'icon' => (string) ($step['icon'] ?? 'circle'),
        'detail' => (string) ($step['detail'] ?? ''),
    ];
}

/**
 * @param list<mixed> $steps
 * @return list<array{summary: string, icon: string, detail: string}>
 */
function normalize_guide_steps(array $steps): array
{
    $normalized = [];

    foreach ($steps as $step) {
        $normalized[] = normalize_guide_step($step);
    }

    return $normalized;
}

/**
 * @return array{items: list<array{heading: string, body: string}>, source: string}|null
 */
function parse_guide_facts(mixed $factsSection): ?array
{
    if ($factsSection === null || $factsSection === '') {
        return null;
    }

    $decoded = is_string($factsSection) ? json_decode($factsSection, true) : $factsSection;

    if (!is_array($decoded) || empty($decoded['items']) || !is_array($decoded['items'])) {
        return null;
    }

    $items = [];

    foreach ($decoded['items'] as $item) {
        if (!is_array($item)) {
            continue;
        }

        $heading = trim((string) ($item['heading'] ?? ''));
        $body = trim((string) ($item['body'] ?? ''));

        if ($heading === '' && $body === '') {
            continue;
        }

        $items[] = [
            'heading' => $heading,
            'body' => $body,
        ];
    }

    if ($items === []) {
        return null;
    }

    return [
        'items' => $items,
        'source' => trim((string) ($decoded['source'] ?? '')),
    ];
}

/**
 * Applies canonical labels, icons, and parsed step/facts content to a guide row.
 *
 * @param array<string, mixed> $guide
 * @return array<string, mixed>
 */
function normalize_first_aid_guide(array $guide): array
{
    $incidentType = normalize_incident_type((string) ($guide['incident_type'] ?? ''));
    $meta = incident_type_meta($incidentType);
    $stepsRaw = $guide['steps'] ?? '[]';
    $stepsDecoded = is_string($stepsRaw) ? json_decode($stepsRaw, true) : $stepsRaw;

    $guide['incident_type'] = $incidentType;
    $guide['display_label'] = $meta['label'];
    $guide['icon'] = $meta['icon'];
    $guide['steps'] = normalize_guide_steps(is_array($stepsDecoded) ? $stepsDecoded : []);
    $guide['facts'] = parse_guide_facts($guide['facts_section'] ?? null);

    return $guide;
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_first_aid_guides(PDO $pdo): array
{
    $stmt = $pdo->query('
        SELECT * FROM first_aid_guides
        ORDER BY FIELD(severity_level, "Severe", "Moderate", "Mild"), sort_order ASC
    ');

    $guides = $stmt->fetchAll();

    return array_map('normalize_first_aid_guide', $guides);
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

    return normalize_first_aid_guide($guide);
}

/**
 * @return array<string, mixed>|null
 */
function fetch_first_aid_by_type(PDO $pdo, string $incidentType): ?array
{
    $normalizedType = normalize_incident_type($incidentType);
    $stmt = $pdo->prepare('
        SELECT * FROM first_aid_guides
        WHERE incident_type = :type OR incident_type = :legacy
        LIMIT 1
    ');
    $stmt->execute([
        ':type' => $normalizedType,
        ':legacy' => $normalizedType === 'Disturbance' ? 'Trash Disturbance' : $normalizedType,
    ]);
    $guide = $stmt->fetch();

    if (!$guide) {
        return null;
    }

    return normalize_first_aid_guide($guide);
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

/**
 * Returns a list-card modifier class for severity-based selected styling.
 */
function first_aid_list_severity_class(string $severity): string
{
    return match ($severity) {
        'Severe' => 'first-aid-card--severe',
        'Moderate' => 'first-aid-card--moderate',
        default => 'first-aid-card--mild',
    };
}

/**
 * Optional secondary line under a guide list title.
 */
function first_aid_list_subtitle(string $incidentType): string
{
    return match (normalize_incident_type($incidentType)) {
        'Disturbance' => '(Garbage Raiding, Excessive Barking, etc.)',
        default => '',
    };
}
