<?php

require_once __DIR__ . '/db.php';

/**
 * @return list<array<string, mixed>>
 */
function fetch_unclaimed_stray_cases(PDO $pdo, string $barangay): array
{
    $stmt = $pdo->prepare('
        SELECT i.IncidentID, i.Location, i.Date, i.Description, i.area_regular,
               COUNT(s.sighting_id) AS sighting_count
        FROM incident i
        LEFT JOIN rescue_cases rc ON rc.incident_id = i.IncidentID
        LEFT JOIN stray_sightings s ON s.incident_id = i.IncidentID
        INNER JOIN user u ON u.UserID = i.UserID
        WHERE i.IncidentType = \'Injured Stray\'
          AND rc.rescue_case_id IS NULL
          AND u.Barangay = :barangay
        GROUP BY i.IncidentID, i.Location, i.Date, i.Description, i.area_regular
        ORDER BY sighting_count DESC, i.Date DESC
    ');
    $stmt->execute([':barangay' => $barangay]);

    return $stmt->fetchAll();
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_rescue_org_cases(PDO $pdo, int $orgUserId): array
{
    $stmt = $pdo->prepare('
        SELECT rc.*, i.Location, i.Description, i.Date AS reported_date
        FROM rescue_cases rc
        INNER JOIN incident i ON i.IncidentID = rc.incident_id
        WHERE rc.rescue_org_id = :org_id
        ORDER BY rc.updated_at DESC
    ');
    $stmt->execute([':org_id' => $orgUserId]);

    return $stmt->fetchAll();
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_adoption_listings(PDO $pdo, ?string $filter = null): array
{
    $sql = '
        SELECT l.*, u.Name AS org_name, u.Phone AS org_phone
        FROM adoption_listings l
        INNER JOIN user u ON u.UserID = l.rescue_org_id
        WHERE l.status = \'Available\'
    ';

    if ($filter === 'recent') {
        $sql .= ' AND l.created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)';
    }

    $sql .= ' ORDER BY l.created_at DESC';

    return $pdo->query($sql)->fetchAll();
}

/**
 * Claims stray incident for rescue org.
 */
function claim_stray_case(PDO $pdo, int $incidentId, int $orgUserId): bool
{
    $check = $pdo->prepare('SELECT rescue_case_id FROM rescue_cases WHERE incident_id = :id LIMIT 1');
    $check->execute([':id' => $incidentId]);
    if ($check->fetch()) {
        return false;
    }

    $insert = $pdo->prepare('
        INSERT INTO rescue_cases (incident_id, rescue_org_id, status)
        VALUES (:incident_id, :org_id, \'Spotted\')
    ');
    $insert->execute([':incident_id' => $incidentId, ':org_id' => $orgUserId]);

    $caseId = (int) $pdo->lastInsertId();
    $pdo->prepare('INSERT INTO rescue_case_history (rescue_case_id, status, updated_by) VALUES (:id, \'Spotted\', :user_id)')
        ->execute([':id' => $caseId, ':user_id' => $orgUserId]);

    return true;
}

/**
 * Updates rescue case pipeline status.
 */
function update_rescue_status(PDO $pdo, int $rescueCaseId, string $status, int $userId): bool
{
    $allowed = ['Spotted', 'Rescued', 'Under Vet Care', 'Ready for Adoption'];
    if (!in_array($status, $allowed, true)) {
        return false;
    }

    $update = $pdo->prepare('UPDATE rescue_cases SET status = :status WHERE rescue_case_id = :id');
    $update->execute([':status' => $status, ':id' => $rescueCaseId]);

    $pdo->prepare('INSERT INTO rescue_case_history (rescue_case_id, status, updated_by) VALUES (:id, :status, :user_id)')
        ->execute([':id' => $rescueCaseId, ':status' => $status, ':user_id' => $userId]);

    return $update->rowCount() > 0;
}
