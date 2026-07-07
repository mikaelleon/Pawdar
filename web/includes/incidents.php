<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/geocoding.php';

/**
 * Fetches incidents for the feed with corroboration and case data.
 *
 * @return list<array<string, mixed>>
 */
function fetch_incidents(
    PDO $pdo,
    string $barangay,
    int $userId,
    ?string $incidentType = null,
    int $offset = 0,
    int $limit = 10,
    ?string $search = null
): array {
    $sql = '
        SELECT i.IncidentID,
               i.IncidentType,
               i.Date,
               i.Location,
               i.latitude,
               i.longitude,
               i.UserID AS reporter_id,
               i.dog_id,
               i.Description,
               i.photo_path,
               u.Name AS reporter_name,
               c.CaseID,
               c.CaseStatus,
               COUNT(DISTINCT corr.corroboration_id) AS corroborate_count,
               d.DogName,
               d.Breed,
               EXISTS(
                   SELECT 1 FROM corroborations uc
                   WHERE uc.incident_id = i.IncidentID AND uc.user_id = :current_user_exists
               ) AS user_corroborated
        FROM incident i
        INNER JOIN `user` u ON i.UserID = u.UserID
        LEFT JOIN `case` c ON c.IncidentID = i.IncidentID
        LEFT JOIN corroborations corr ON corr.incident_id = i.IncidentID
        LEFT JOIN dog d ON d.dog_id = i.dog_id
        WHERE i.Location LIKE :barangay
    ';

    $params = [
        ':barangay' => '%' . $barangay . '%',
        ':current_user_exists' => $userId,
    ];

    if ($incidentType !== null) {
        $sql .= ' AND i.IncidentType = :incident_type';
        $params[':incident_type'] = $incidentType;
    }

    $searchTerm = trim((string) ($search ?? ''));
    if ($searchTerm !== '') {
        $sql .= ' AND (
            i.Location LIKE :search
            OR i.IncidentType LIKE :search
            OR i.Description LIKE :search
            OR COALESCE(d.DogName, \'\') LIKE :search
            OR COALESCE(d.Breed, \'\') LIKE :search
        )';
        $params[':search'] = '%' . $searchTerm . '%';
    }

    $sql .= '
        GROUP BY i.IncidentID, i.IncidentType, i.Date, i.Location, i.latitude, i.longitude, i.UserID, i.dog_id,
                 i.Description, i.photo_path, u.Name, c.CaseID, c.CaseStatus, d.DogName, d.Breed
        ORDER BY i.Date DESC
        LIMIT :limit OFFSET :offset
    ';

    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * Fetches incidents submitted by the logged-in user.
 *
 * @return list<array<string, mixed>>
 */
function fetch_user_reports(PDO $pdo, int $userId, int $limit = 5): array
{
    $sql = '
        SELECT i.IncidentID,
               i.IncidentType,
               i.Date,
               i.Location,
               c.CaseStatus
        FROM incident i
        LEFT JOIN `case` c ON c.IncidentID = i.IncidentID
        WHERE i.UserID = :user_id
        ORDER BY i.Date DESC
        LIMIT :limit
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * Returns map preview counts grouped by incident category.
 *
 * @return array<string, int>
 */
function fetch_map_counts(PDO $pdo, string $barangay): array
{
    $sql = '
        SELECT
            SUM(CASE WHEN i.IncidentType = \'Animal Bite\' THEN 1 ELSE 0 END) AS bites,
            SUM(CASE WHEN i.IncidentType = \'Injured Stray\' THEN 1 ELSE 0 END) AS strays,
            SUM(CASE WHEN i.IncidentType = \'Aggressive Behavior\' THEN 1 ELSE 0 END) AS aggressive,
            SUM(CASE WHEN i.IncidentType = \'Vehicular Accident\' THEN 1 ELSE 0 END) AS vehicular,
            SUM(CASE WHEN i.IncidentType IN (\'Disturbance\', \'Trash Disturbance\') THEN 1 ELSE 0 END) AS disturbance
        FROM incident i
        WHERE i.Location LIKE :barangay
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':barangay' => '%' . $barangay . '%']);
    $row = $stmt->fetch();

    return [
        'bites' => (int) ($row['bites'] ?? 0),
        'strays' => (int) ($row['strays'] ?? 0),
        'aggressive' => (int) ($row['aggressive'] ?? 0),
        'vehicular' => (int) ($row['vehicular'] ?? 0),
        'disturbance' => (int) ($row['disturbance'] ?? 0),
    ];
}

/**
 * Returns map pin positions for preview panel.
 *
 * @return list<array<string, mixed>>
 */
function fetch_map_pins(PDO $pdo, string $barangay, ?string $incidentType = null, int $limit = 30): array
{
    $sql = '
        SELECT i.IncidentID, i.IncidentType, i.Location, i.latitude, i.longitude
        FROM incident i
        WHERE i.Location LIKE :barangay
    ';

    $params = [':barangay' => '%' . $barangay . '%'];

    if ($incidentType !== null) {
        $sql .= ' AND i.IncidentType = :incident_type';
        $params[':incident_type'] = $incidentType;
    }

    $sql .= ' ORDER BY i.Date DESC LIMIT :limit';

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll();
    $colors = incident_pin_colors();
    $coordsList = [];

    foreach ($rows as $row) {
        $resolved = resolve_incident_coordinates($row);
        if ($resolved === null) {
            $id = (int) $row['IncidentID'];
            $resolved = [
                'lat' => 13.7568 + ($id % 7) * 0.002,
                'lng' => 121.0583 + ($id % 5) * 0.003,
            ];
        }
        $coordsList[] = $resolved;
    }

    if ($coordsList === []) {
        return [];
    }

    $lats = array_column($coordsList, 'lat');
    $lngs = array_column($coordsList, 'lng');
    $minLat = min($lats);
    $maxLat = max($lats);
    $minLng = min($lngs);
    $maxLng = max($lngs);
    $latSpan = max(0.0008, $maxLat - $minLat);
    $lngSpan = max(0.0008, $maxLng - $minLng);
    $previewWidth = 260;
    $previewHeight = 120;
    $pins = [];

    foreach ($rows as $index => $row) {
        $coords = $coordsList[$index];
        $type = normalize_incident_type((string) $row['IncidentType']);
        $meta = incident_type_meta($type);
        $countKey = incident_map_count_key($type);
        $left = (int) round((($coords['lng'] - $minLng) / $lngSpan) * ($previewWidth - 24) + 12);
        $top = (int) round((1 - (($coords['lat'] - $minLat) / $latSpan)) * ($previewHeight - 24) + 12);

        $pins[] = [
            'id' => (int) $row['IncidentID'],
            'type' => $type,
            'label' => $meta['label'],
            'accent' => $meta['accent'],
            'color' => $colors[$type] ?? '#87AFAE',
            'count_key' => $countKey,
            'left' => $left,
            'top' => $top,
        ];
    }

    return $pins;
}

/**
 * Returns unread notification count for a user.
 */
function fetch_unread_notification_count(PDO $pdo, int $userId): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0');
    $stmt->execute([':user_id' => $userId]);

    return (int) $stmt->fetchColumn();
}

/**
 * Returns count of unresolved incidents in a barangay.
 */
function fetch_active_incident_count(PDO $pdo, string $barangay): int
{
    $stmt = $pdo->prepare('
        SELECT COUNT(*)
        FROM incident i
        LEFT JOIN `case` c ON c.IncidentID = i.IncidentID
        WHERE i.Location LIKE :barangay
          AND (c.CaseStatus IS NULL OR c.CaseStatus NOT IN (\'Resolved\'))
    ');
    $stmt->execute([':barangay' => '%' . $barangay . '%']);

    return (int) $stmt->fetchColumn();
}

/**
 * Returns bell badge count from unread notifications only.
 */
function fetch_bell_badge_count(PDO $pdo, int $userId, string $barangay = ''): int
{
    unset($barangay);

    return fetch_unread_notification_count($pdo, $userId);
}

/**
 * Creates notifications for users subscribed to a barangay when a new incident is reported.
 */
function notify_barangay_of_incident(PDO $pdo, int $incidentId, string $barangay, int $reporterId, string $title): void
{
    $stmt = $pdo->prepare('
        SELECT UserID FROM `user`
        WHERE Barangay = :barangay AND UserID != :reporter_id
    ');
    $stmt->execute([
        ':barangay' => $barangay,
        ':reporter_id' => $reporterId,
    ]);

    $insert = $pdo->prepare('
        INSERT INTO notifications (user_id, message, link)
        VALUES (:user_id, :message, :link)
    ');

    foreach ($stmt->fetchAll() as $row) {
        $insert->execute([
            ':user_id' => (int) $row['UserID'],
            ':message' => 'New incident in ' . $barangay . ': ' . $title,
            ':link' => 'feed.php',
        ]);
    }
}

/**
 * Notifies the incident reporter when case status changes.
 */
function notify_case_status_change(PDO $pdo, int $incidentId, string $newStatus): void
{
    $stmt = $pdo->prepare('
        SELECT i.UserID, i.IncidentType, i.Location
        FROM incident i
        WHERE i.IncidentID = :incident_id
        LIMIT 1
    ');
    $stmt->execute([':incident_id' => $incidentId]);
    $incident = $stmt->fetch();

    if (!$incident) {
        return;
    }

    $title = generate_incident_title((string) $incident['IncidentType'], (string) $incident['Location']);
    $insert = $pdo->prepare('
        INSERT INTO notifications (user_id, message, link)
        VALUES (:user_id, :message, :link)
    ');
    $insert->execute([
        ':user_id' => (int) $incident['UserID'],
        ':message' => 'Case status updated to ' . $newStatus . ': ' . $title,
        ':link' => 'incident.php?id=' . $incidentId,
    ]);
}

/**
 * @return array<string, mixed>|null
 */
function fetch_incident_detail(PDO $pdo, int $incidentId, int $currentUserId = 0): ?array
{
    $stmt = $pdo->prepare('
        SELECT i.*, u.Name AS reporter_name, u.Role AS reporter_role, u.Barangay AS reporter_barangay,
               c.CaseID, c.CaseStatus, c.RabiesMonitoring,
               d.dog_id, d.DogName, d.Breed, d.RegistryID, d.Status AS dog_status,
               COUNT(DISTINCT corr.corroboration_id) AS corroborate_count,
               EXISTS(
                   SELECT 1 FROM corroborations uc
                   WHERE uc.incident_id = i.IncidentID AND uc.user_id = :uid
               ) AS user_corroborated
        FROM incident i
        INNER JOIN `user` u ON i.UserID = u.UserID
        LEFT JOIN `case` c ON c.IncidentID = i.IncidentID
        LEFT JOIN dog d ON d.dog_id = i.dog_id
        LEFT JOIN corroborations corr ON corr.incident_id = i.IncidentID
        WHERE i.IncidentID = :id
        GROUP BY i.IncidentID
        LIMIT 1
    ');
    $stmt->execute([':id' => $incidentId, ':uid' => $currentUserId]);
    $row = $stmt->fetch();

    return $row ?: null;
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_incident_corroborators(PDO $pdo, int $incidentId, int $limit = 5): array
{
    $stmt = $pdo->prepare('
        SELECT u.UserID, u.Name
        FROM corroborations c
        INNER JOIN `user` u ON u.UserID = c.user_id
        WHERE c.incident_id = :id
        ORDER BY c.created_at ASC
        LIMIT :limit
    ');
    $stmt->bindValue(':id', $incidentId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_related_incidents(PDO $pdo, string $barangay, int $excludeId, int $limit = 3): array
{
    $stmt = $pdo->prepare('
        SELECT i.IncidentID, i.IncidentType, i.Location, i.Date, i.latitude, i.longitude, c.CaseStatus
        FROM incident i
        INNER JOIN `user` u ON u.UserID = i.UserID
        LEFT JOIN `case` c ON c.IncidentID = i.IncidentID
        WHERE u.Barangay = :barangay AND i.IncidentID != :exclude
        ORDER BY i.Date DESC
        LIMIT :limit
    ');
    $stmt->bindValue(':barangay', $barangay);
    $stmt->bindValue(':exclude', $excludeId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_map_incidents(
    PDO $pdo,
    string $barangay,
    ?string $incidentType = null,
    ?string $dateFrom = null,
    int $limit = 100
): array {
    $sql = '
        SELECT i.IncidentID, i.IncidentType, i.Location, i.Date, i.latitude, i.longitude,
               c.CaseStatus, i.Description
        FROM incident i
        LEFT JOIN `case` c ON c.IncidentID = i.IncidentID
        INNER JOIN `user` u ON u.UserID = i.UserID
        WHERE u.Barangay = :barangay
    ';
    $params = [':barangay' => $barangay];

    if ($incidentType !== null && $incidentType !== '') {
        $sql .= ' AND i.IncidentType = :type';
        $params[':type'] = $incidentType;
    }

    if ($dateFrom !== null && $dateFrom !== '') {
        $sql .= ' AND i.Date >= :date_from';
        $params[':date_from'] = $dateFrom;
    }

    $sql .= ' ORDER BY i.Date DESC LIMIT :limit';

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll();
    $colors = incident_pin_colors();

    foreach ($rows as &$row) {
        $row['pin_color'] = $colors[$row['IncidentType']] ?? '#87AFAE';
        if (empty($row['latitude']) || empty($row['longitude'])) {
            $id = (int) $row['IncidentID'];
            $row['latitude'] = 13.7568 + ($id % 7) * 0.002;
            $row['longitude'] = 121.0583 + ($id % 5) * 0.003;
        }
    }

    return $rows;
}

/**
 * @return array<string, string>
 */
function incident_pin_colors(): array
{
    return [
        'Animal Bite' => '#E0765E',
        'Injured Stray' => '#F8BC72',
        'Aggressive Behavior' => '#6C8B9F',
        'Vehicular Accident' => '#87AFAE',
        'Trash Disturbance' => '#4A4343',
        'Disturbance' => '#4A4343',
    ];
}

/**
 * Notifies dog owners when a reported incident may match their dog.
 */
function notify_matching_dog_owners(PDO $pdo, int $incidentId, ?string $breed, string $barangay): void
{
    if ($breed === null || $breed === '') {
        return;
    }

    $stmt = $pdo->prepare('
        SELECT DISTINCT u.UserID
        FROM dog d
        INNER JOIN `user` u ON u.UserID = d.UserID
        WHERE d.Breed = :breed AND u.Barangay = :barangay
    ');
    $stmt->execute([
        ':breed' => $breed,
        ':barangay' => $barangay,
    ]);

    $insert = $pdo->prepare('
        INSERT INTO notifications (user_id, message, link)
        VALUES (:user_id, :message, :link)
    ');

    foreach ($stmt->fetchAll() as $row) {
        $insert->execute([
            ':user_id' => (int) $row['UserID'],
            ':message' => 'An incident may involve a ' . $breed . ' in your barangay.',
            ':link' => 'incident.php?id=' . $incidentId,
        ]);
    }
}
