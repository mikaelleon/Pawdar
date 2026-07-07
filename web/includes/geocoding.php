<?php

/**
 * Parses "lat, lng" from a location string.
 *
 * @return array{lat: float, lng: float}|null
 */
function parse_coordinates_from_text(string $text): ?array
{
    if (preg_match('/(-?\d{1,3}\.\d{3,})\s*,\s*(-?\d{1,3}\.\d{3,})/', $text, $matches) !== 1) {
        return null;
    }

    $lat = (float) $matches[1];
    $lng = (float) $matches[2];

    if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
        return null;
    }

    return ['lat' => $lat, 'lng' => $lng];
}

/**
 * Returns true when location is essentially raw GPS coordinates.
 */
function location_is_coordinate_string(string $location): bool
{
    $trimmed = trim($location);
    $coords = parse_coordinates_from_text($trimmed);

    if ($coords === null) {
        return false;
    }

    $withoutCoords = trim(preg_replace('/-?\d{1,3}\.\d{3,}\s*,\s*-?\d{1,3}\.\d{3,}/', '', $trimmed) ?? $trimmed);
    $withoutCoords = trim(preg_replace('/\b(near|at)\b/i', '', $withoutCoords) ?? $withoutCoords);
    $withoutCoords = trim(str_replace([',', 'Brgy.', 'Brgy'], '', $withoutCoords));

    return $withoutCoords === '';
}

/**
 * Reverse-geocodes coordinates via OpenStreetMap Nominatim (cached on disk).
 */
function reverse_geocode_label(float $lat, float $lng): ?string
{
    $cacheKey = sprintf('%.4f_%.4f', $lat, $lng);
    $cacheDir = dirname(__DIR__) . '/cache/geocode';
    $cacheFile = $cacheDir . '/' . $cacheKey . '.txt';

    if (is_file($cacheFile)) {
        $cached = trim((string) file_get_contents($cacheFile));

        return $cached !== '' ? $cached : null;
    }

    $url = 'https://nominatim.openstreetmap.org/reverse?'
        . http_build_query([
            'lat' => $lat,
            'lon' => $lng,
            'format' => 'json',
            'zoom' => 18,
            'addressdetails' => 1,
        ]);

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'header' => "User-Agent: Pawdar/1.0 (civic safety app)\r\n",
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return null;
    }

    $address = $data['address'] ?? [];
    $label = null;

    if (is_array($address)) {
        $parts = array_filter([
            $address['road'] ?? $address['pedestrian'] ?? $address['footway'] ?? null,
            $address['neighbourhood'] ?? $address['suburb'] ?? $address['village'] ?? null,
        ]);
        if ($parts !== []) {
            $label = implode(', ', $parts);
        }
    }

    if ($label === null && !empty($data['display_name'])) {
        $chunks = explode(',', (string) $data['display_name']);
        $label = trim($chunks[0] . (isset($chunks[1]) ? ', ' . $chunks[1] : ''));
    }

    if ($label === null || $label === '') {
        return null;
    }

    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }

    @file_put_contents($cacheFile, $label);

    return $label;
}

/**
 * Extracts trailing barangay suffix from a location string.
 */
function extract_barangay_suffix(string $location): string
{
    if (preg_match('/,\s*(Brgy\.?\s*.+)$/i', $location, $matches) === 1) {
        return ', ' . trim($matches[1]);
    }

    return '';
}

/**
 * Resolves display-friendly location text and optional coordinate string.
 *
 * @return array{display: string, coordinates: string|null}
 */
function incident_location_display(string $location, ?float $latitude = null, ?float $longitude = null): array
{
    $coords = null;
    if ($latitude !== null && $longitude !== null) {
        $coords = ['lat' => $latitude, 'lng' => $longitude];
    } else {
        $coords = parse_coordinates_from_text($location);
    }

    $barangaySuffix = extract_barangay_suffix($location);
    $coordinateString = $coords !== null
        ? sprintf('%.5f, %.5f', $coords['lat'], $coords['lng'])
        : null;

    if ($coords !== null && (location_is_coordinate_string($location) || preg_match('/\d+\.\d+\s*,\s*\d+\.\d+/', $location) === 1)) {
        $readable = reverse_geocode_label($coords['lat'], $coords['lng']);
        if ($readable !== null) {
            return [
                'display' => $readable . $barangaySuffix,
                'coordinates' => $coordinateString,
            ];
        }
    }

    $display = trim(preg_replace('/\b(near|at)\s+-?\d{1,3}\.\d{3,}\s*,\s*-?\d{1,3}\.\d{3,}/i', '', $location) ?? $location);
    $display = trim($display, " ,\t\n\r\0\x0B");

    return [
        'display' => $display !== '' ? $display : $location,
        'coordinates' => $coordinateString,
    ];
}

/**
 * Short place label for incident titles.
 */
function incident_location_short_label(string $displayLocation): string
{
    $parts = explode(',', $displayLocation);

    return trim($parts[0] !== '' ? $parts[0] : $displayLocation);
}

/**
 * Resolves lat/lng for an incident row.
 *
 * @param array<string, mixed> $row
 * @return array{lat: float, lng: float}|null
 */
function resolve_incident_coordinates(array $row): ?array
{
    $location = (string) ($row['Location'] ?? '');
    $preset = coordinates_for_location_label($location);
    if ($preset !== null) {
        return $preset;
    }

    if (!empty($row['latitude']) && !empty($row['longitude'])) {
        return ['lat' => (float) $row['latitude'], 'lng' => (float) $row['longitude']];
    }

    return parse_coordinates_from_text($location);
}

/**
 * Returns distinct map coordinates for known demo/reporting locations.
 *
 * @return array{lat: float, lng: float}|null
 */
function coordinates_for_location_label(string $location): ?array
{
    $normalized = strtolower(trim($location));
    if ($normalized === '') {
        return null;
    }

    $presets = [
        'riverside park' => ['lat' => 13.7545, 'lng' => 121.0550],
        'market st' => ['lat' => 13.7588, 'lng' => 121.0612],
        'acacia ave' => ['lat' => 13.7614, 'lng' => 121.0578],
        'national hwy' => ['lat' => 13.7521, 'lng' => 121.0684],
        'barangay hall' => ['lat' => 13.7596, 'lng' => 121.0542],
    ];

    foreach ($presets as $needle => $coords) {
        if (str_contains($normalized, $needle)) {
            return $coords;
        }
    }

    return null;
}

/**
 * Maps incident type to feed map stat bucket key.
 */
function incident_map_count_key(string $incidentType): string
{
    $normalized = normalize_incident_type($incidentType);

    return match ($normalized) {
        'Animal Bite' => 'bites',
        'Injured Stray' => 'strays',
        'Aggressive Behavior' => 'aggressive',
        'Vehicular Accident' => 'vehicular',
        'Disturbance' => 'disturbance',
        default => 'other',
    };
}
