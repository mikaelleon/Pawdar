<?php

/**
 * Incident type metadata for badges, accents, and filters.
 *
 * @return array<string, array<string, string>>
 */
function incident_type_map(): array
{
    return [
        'Animal Bite' => [
            'filter' => 'animal_bite',
            'badge' => 'badge-bite',
            'accent' => 'accent-bite',
            'icon' => 'dog',
            'label' => 'Animal Bite',
        ],
        'Injured Stray' => [
            'filter' => 'injured_stray',
            'badge' => 'badge-injured',
            'accent' => 'accent-injured',
            'icon' => 'paw-print',
            'label' => 'Injured Stray',
        ],
        'Aggressive Behavior' => [
            'filter' => 'aggressive',
            'badge' => 'badge-aggressive',
            'accent' => 'accent-aggressive',
            'icon' => 'alert-triangle',
            'label' => 'Aggressive',
        ],
        'Vehicular Accident' => [
            'filter' => 'vehicular',
            'badge' => 'badge-vehicular',
            'accent' => 'accent-teal',
            'icon' => 'car',
            'label' => 'Vehicular',
        ],
        'Disturbance' => [
            'filter' => 'disturbance',
            'badge' => 'badge-trash',
            'accent' => 'accent-dark',
            'icon' => 'footprints',
            'label' => 'Disturbance',
        ],
    ];
}

/**
 * Normalizes legacy incident type labels to current vocabulary.
 */
function normalize_incident_type(string $type): string
{
    if ($type === 'Trash Disturbance') {
        return 'Disturbance';
    }

    return $type;
}

/**
 * Returns incident type metadata, including legacy aliases.
 *
 * @return array<string, string>
 */
function incident_type_meta(string $type): array
{
    $map = incident_type_map();
    $normalized = normalize_incident_type($type);

    if (isset($map[$normalized])) {
        return $map[$normalized];
    }

    return [
        'filter' => 'all',
        'badge' => 'badge-received',
        'accent' => 'accent-teal',
        'icon' => 'alert-circle',
        'label' => $normalized,
    ];
}

/**
 * Maps URL filter slug to DB incident type.
 */
function filter_to_incident_type(string $filter): ?string
{
    if ($filter === 'trash') {
        return 'Disturbance';
    }

    foreach (incident_type_map() as $type => $meta) {
        if ($meta['filter'] === $filter) {
            return $type;
        }
    }

    return null;
}

/**
 * Generates a readable incident title from type and location.
 */
function generate_incident_title(string $type, string $location, ?float $latitude = null, ?float $longitude = null): string
{
    $locationParts = incident_location_display($location, $latitude, $longitude);
    $place = incident_location_short_label($locationParts['display']);

    $phrases = [
        'Animal Bite' => 'Animal bite reported near',
        'Injured Stray' => 'Injured stray spotted near',
        'Aggressive Behavior' => 'Aggressive dog reported at',
        'Vehicular Accident' => 'Dog involved in road incident near',
        'Disturbance' => 'Disturbance reported near',
        'Trash Disturbance' => 'Disturbance reported near',
    ];

    return ($phrases[$type] ?? 'Incident reported near') . ' ' . $place;
}

/**
 * Returns a human-readable elapsed time string.
 */
function time_elapsed_string(string $datetime): string
{
    $timestamp = strtotime($datetime);
    if ($timestamp === false) {
        return 'Unknown';
    }

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return max(1, $diff) . 's ago';
    }

    if ($diff < 3600) {
        return (int) floor($diff / 60) . 'm ago';
    }

    if ($diff < 86400) {
        return (int) floor($diff / 3600) . 'h ago';
    }

    if ($diff < 604800) {
        return (int) floor($diff / 86400) . 'd ago';
    }

    return date('M j, Y', $timestamp);
}

/**
 * Derives display initials from a full name.
 */
function user_initials_from_name(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));

    if ($parts === false || count($parts) === 0) {
        return '?';
    }

    if (count($parts) === 1) {
        return strtoupper(substr($parts[0], 0, 1));
    }

    return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
}

/**
 * Avatar background class derived from user id.
 */
function avatar_color_class(int $userId): string
{
    return 'avatar-color-' . ($userId % 6);
}

/**
 * Derives a pastel block color from a string (breed name, etc.).
 */
function string_color_class(string $value): string
{
    return 'pastel-color-' . (abs(crc32($value)) % 6);
}

/**
 * Dot color for incident type in case tables.
 */
function incident_type_dot_color(string $incidentType): string
{
    $map = [
        'Animal Bite' => 'var(--burnt-peach)',
        'Injured Stray' => 'var(--air-force)',
        'Aggressive Behavior' => 'var(--sunlit-clay)',
        'Vehicular Accident' => 'var(--muted-teal)',
        'Disturbance' => 'var(--taupe)',
        'Trash Disturbance' => 'var(--taupe)',
    ];

    return $map[$incidentType] ?? 'var(--air-force)';
}

/**
 * Case status display label and badge class.
 *
 * @return array{label: string, class: string}
 */
function case_status_meta(?string $status): array
{
    $map = [
        'Received' => ['label' => 'Received', 'class' => 'badge-received'],
        'Under Investigation' => ['label' => 'Investigating', 'class' => 'badge-investigating'],
        'Action Taken' => ['label' => 'Action Taken', 'class' => 'badge-investigating'],
        'Resolved' => ['label' => 'Resolved', 'class' => 'badge-resolved'],
        'Referred' => ['label' => 'Referred', 'class' => 'badge-referred'],
    ];

    if ($status === null || !isset($map[$status])) {
        return ['label' => 'No case', 'class' => 'badge-received'];
    }

    return $map[$status];
}

/**
 * Builds a direct OpenStreetMap tile URL centered near incident coordinates.
 */
function incident_map_tile_url(?float $latitude, ?float $longitude, int $zoom = 15): ?string
{
    if ($latitude === null || $longitude === null) {
        return null;
    }

    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        return null;
    }

    $zoom = max(10, min(18, $zoom));
    $scale = 2 ** $zoom;
    $tileX = (int) floor(($longitude + 180.0) / 360.0 * $scale);
    $latitudeRadians = deg2rad($latitude);
    $tileY = (int) floor((1.0 - log(tan($latitudeRadians) + 1.0 / cos($latitudeRadians)) / M_PI) / 2.0 * $scale);

    return 'https://tile.openstreetmap.org/' . $zoom . '/' . $tileX . '/' . $tileY . '.png';
}

/**
 * Builds a static map thumbnail URL for feed preview tiles.
 */
function incident_map_thumbnail_url(?float $latitude, ?float $longitude, int $width = 280, int $height = 168): ?string
{
    if ($latitude === null || $longitude === null) {
        return null;
    }

    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        return null;
    }

    if (function_exists('imagecreatetruecolor')) {
        return 'ajax/map-thumbnail.php?lat=' . rawurlencode((string) $latitude)
            . '&lng=' . rawurlencode((string) $longitude)
            . '&w=' . max(120, min(800, $width))
            . '&h=' . max(88, min(300, $height));
    }

    return incident_map_tile_url($latitude, $longitude);
}

/**
 * Normalizes stored incident photo paths for public URLs.
 */
function incident_photo_url(?string $photoPath): ?string
{
    $photoPath = trim((string) $photoPath);
    if ($photoPath === '') {
        return null;
    }

    if (str_starts_with($photoPath, 'uploads/') || str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://')) {
        return $photoPath;
    }

    return 'uploads/incidents/' . ltrim($photoPath, '/');
}

/**
 * Returns true when the role may access a nav item.
 *
 * Community Reporter intentionally shares Dog Owner nav (Feed, Map, Registry,
 * First Aid, Breeds): reporters need map/registry context when flagging incidents.
 * LGU-only items (Cases, Analytics) stay restricted. Change here only with product sign-off.
 */
function role_can_see_nav(string $item, string $role): bool
{
    $rules = [
        'feed' => true,
        'map' => true,
        'registry' => true,
        'cases' => in_array($role, ['LGU Official', 'Admin'], true),
        'first-aid' => true,
        'breeds' => true,
        'rescue-board' => in_array($role, ['Rescue Organization', 'Admin'], true),
        'analytics' => in_array($role, ['LGU Official', 'Admin'], true),
        'admin' => $role === 'Admin',
    ];

    return $rules[$item] ?? false;
}

/**
 * Returns true when the role may report incidents.
 */
function role_can_report(string $role): bool
{
    return in_array($role, ['Dog Owner', 'Community Reporter', 'Admin'], true);
}

/**
 * Returns true when the role may update case status from Feed, Cases, or Incident Detail.
 * Intentional quick-action for management roles; browsing users see read-only status only.
 */
function role_can_manage_cases(string $role): bool
{
    return in_array($role, ['LGU Official', 'Admin'], true);
}

/**
 * Validates CSRF token from header or POST body.
 */
function validate_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Reads CSRF token from request headers or body.
 */
function request_csrf_token(): ?string
{
    $header = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (is_string($header) && $header !== '') {
        return $header;
    }

    if (isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])) {
        return $_POST['csrf_token'];
    }

    $input = file_get_contents('php://input');
    if ($input !== false && $input !== '') {
        $json = json_decode($input, true);
        if (is_array($json) && isset($json['csrf_token']) && is_string($json['csrf_token'])) {
            return $json['csrf_token'];
        }
    }

    return null;
}

/**
 * Returns public stats for the login page trust strip.
 *
 * @return array{dogs: int, barangays: int, resolved: int}
 */
function fetch_login_stats(PDO $pdo): array
{
    $defaults = ['dogs' => 240, 'barangays' => 12, 'resolved' => 86];

    try {
        $dogs = (int) $pdo->query('SELECT COUNT(*) FROM dog')->fetchColumn();
        $resolved = (int) $pdo->query('SELECT COUNT(*) FROM `case` WHERE CaseStatus = \'Resolved\'')->fetchColumn();
        $barangays = (int) $pdo->query('
            SELECT COUNT(DISTINCT Barangay) FROM `user`
            WHERE Barangay IS NOT NULL AND Barangay != \'\'
        ')->fetchColumn();

        return [
            'dogs' => max($dogs, $defaults['dogs']),
            'barangays' => max($barangays, $defaults['barangays']),
            'resolved' => max($resolved, $defaults['resolved']),
        ];
    } catch (Throwable $exception) {
        return $defaults;
    }
}

/**
 * Returns Lucide icon for a severity level.
 */
function severity_icon_name(string $severity): string
{
    return match ($severity) {
        'Severe' => 'alert-triangle',
        'Moderate' => 'alert-circle',
        default => 'check-circle',
    };
}

/**
 * Returns default first-aid severity tier for an incident type.
 */
function incident_type_severity(string $type): string
{
    return match (normalize_incident_type($type)) {
        'Animal Bite', 'Vehicular Accident' => 'Severe',
        'Injured Stray', 'Aggressive Behavior' => 'Moderate',
        default => 'Mild',
    };
}

/**
 * Returns a severity surface modifier for icon backgrounds.
 */
function incident_severity_surface_class(string $severity, string $prefix = 'severity-surface'): string
{
    return match ($severity) {
        'Severe' => $prefix . '--severe',
        'Moderate' => $prefix . '--moderate',
        default => $prefix . '--mild',
    };
}

/**
 * Returns CSS classes for a severity badge.
 */
function severity_badge_class(string $severity): string
{
    return match ($severity) {
        'Severe' => 'severity-badge severity-severe',
        'Moderate' => 'severity-badge severity-moderate',
        default => 'severity-badge severity-mild',
    };
}

/**
 * Renders an accessible severity badge with icon and label.
 */
function severity_badge_html(string $severity, bool $withIcon = true): string
{
    $class = severity_badge_class($severity);
    if (!$withIcon) {
        $class .= ' severity-badge--text-only first-aid-severity-badge';
    }

    $label = htmlspecialchars($severity, ENT_QUOTES, 'UTF-8');
    $iconHtml = '';

    if ($withIcon) {
        $icon = severity_icon_name($severity);
        $iconHtml = '<i data-lucide="' . $icon . '" aria-hidden="true"></i>';
    }

    return '<span class="' . $class . '" role="status" aria-label="Severity: ' . $label . '">'
        . $iconHtml
        . '<span class="severity-label">' . $label . '</span></span>';
}

/**
 * Builds incident description text from observed dog appearance fields.
 */
function compose_observed_dog_description(
    string $breed = '',
    string $coatColor = '',
    string $dogSize = '',
    string $marks = '',
    string $userDescription = ''
): string {
    $parts = [];

    if ($breed !== '') {
        $parts[] = 'Breed: ' . $breed;
    }

    if ($coatColor !== '') {
        $parts[] = 'Coat: ' . $coatColor;
    }

    if ($dogSize !== '') {
        $parts[] = 'Size: ' . $dogSize;
    }

    if ($marks !== '') {
        $parts[] = 'Appearance: ' . $marks;
    }

    $observed = count($parts) > 0 ? 'Dog observed — ' . implode(' · ', $parts) : '';
    $userDescription = trim($userDescription);

    if ($observed !== '' && $userDescription !== '') {
        return $observed . "\n\n" . $userDescription;
    }

    return $observed !== '' ? $observed : $userDescription;
}

/**
 * Splits stored incident description into observed-dog fields and free-text narrative.
 *
 * @return array{observed: string|null, narrative: string|null}
 */
function incident_description_parts(?string $description): array
{
    $description = trim((string) $description);
    if ($description === '') {
        return ['observed' => null, 'narrative' => null];
    }

    if (preg_match('/^Dog observed — (.+?)(?:\r?\n\r?\n(.*))?$/s', $description, $matches) === 1) {
        $observed = trim($matches[1]);
        $narrative = isset($matches[2]) ? trim($matches[2]) : null;

        return [
            'observed' => $observed !== '' ? $observed : null,
            'narrative' => $narrative !== '' ? $narrative : null,
        ];
    }

    return ['observed' => null, 'narrative' => $description];
}

/**
 * Renders notification bell badge count.
 */
function render_bell_badge(int $count): void
{
    if ($count > 0) {
        $display = $count > 99 ? '99+' : (string) $count;
        echo '<span class="notification-badge" data-notification-count aria-label="' . (int) $count . ' unread">' . htmlspecialchars($display) . '</span>';
        return;
    }

    echo '<span class="notification-badge is-hidden" data-notification-count aria-hidden="true">0</span>';
}

/**
 * Sends JSON response and exits.
 *
 * @param array<string, mixed> $payload
 */
function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}
