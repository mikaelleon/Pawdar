<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

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
    int $limit = 10
): array {
    $sql = '
        SELECT i.IncidentID,
               i.IncidentType,
               i.Date,
               i.Location,
               i.UserID AS reporter_id,
               i.dog_id,
               i.Description,
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
        INNER JOIN user u ON i.UserID = u.UserID
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

    $sql .= '
        GROUP BY i.IncidentID, i.IncidentType, i.Date, i.Location, i.UserID, i.dog_id,
                 i.Description, u.Name, c.CaseID, c.CaseStatus, d.DogName, d.Breed
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
            SUM(CASE WHEN i.IncidentType = \'Vehicular Accident\' THEN 1 ELSE 0 END) AS vehicular
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
    ];
}

/**
 * Returns map pin positions for preview panel.
 *
 * @return list<array<string, mixed>>
 */
function fetch_map_pins(PDO $pdo, string $barangay, ?string $incidentType = null, int $limit = 20): array
{
    $sql = '
        SELECT i.IncidentID, i.IncidentType, i.Location
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
    $typeMap = incident_type_map();
    $pins = [];

    foreach ($rows as $index => $row) {
        $meta = $typeMap[$row['IncidentType']] ?? null;
        $pins[] = [
            'id' => (int) $row['IncidentID'],
            'type' => $row['IncidentType'],
            'accent' => $meta['accent'] ?? 'accent-teal',
            'left' => 20 + (($index * 37) % 220),
            'top' => 30 + (($index * 53) % 170),
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
 * Creates notifications for users subscribed to a barangay when a new incident is reported.
 */
function notify_barangay_of_incident(PDO $pdo, int $incidentId, string $barangay, int $reporterId, string $title): void
{
    $stmt = $pdo->prepare('
        SELECT UserID FROM user
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
        ':link' => 'case-detail.php?id=' . $incidentId,
    ]);
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
        INNER JOIN user u ON u.UserID = d.UserID
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
            ':link' => 'feed.php',
        ]);
    }
}
