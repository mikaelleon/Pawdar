<?php

require_once __DIR__ . '/db.php';

/**
 * @return list<array<string, mixed>>
 */
function fetch_unclaimed_stray_cases(PDO $pdo, string $barangay): array
{
    $stmt = $pdo->prepare('
        SELECT i.IncidentID, i.Location, i.Date, i.Description, i.area_regular, i.photo_path,
               COUNT(s.sighting_id) AS sighting_count
        FROM incident i
        LEFT JOIN rescue_cases rc ON rc.incident_id = i.IncidentID
        LEFT JOIN stray_sightings s ON s.incident_id = i.IncidentID
        INNER JOIN `user` u ON u.UserID = i.UserID
        WHERE i.IncidentType = \'Injured Stray\'
          AND rc.rescue_case_id IS NULL
          AND u.Barangay = :barangay
        GROUP BY i.IncidentID, i.Location, i.Date, i.Description, i.area_regular, i.photo_path
        ORDER BY sighting_count DESC, i.Date DESC
    ');
    $stmt->execute([':barangay' => $barangay]);

    return $stmt->fetchAll();
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_rescue_org_cases(PDO $pdo, int $orgUserId, string $userRole = '', string $barangay = ''): array
{
    $sql = '
        SELECT rc.*,
               i.Location,
               i.Description,
               i.Date AS reported_date,
               i.photo_path,
               org.Name AS org_name,
               (
                   SELECT MAX(h.created_at)
                   FROM rescue_case_history h
                   WHERE h.rescue_case_id = rc.rescue_case_id
               ) AS last_status_at
        FROM rescue_cases rc
        INNER JOIN incident i ON i.IncidentID = rc.incident_id
        INNER JOIN `user` org ON org.UserID = rc.rescue_org_id
        INNER JOIN `user` reporter ON reporter.UserID = i.UserID
    ';

    if ($userRole === 'Admin' && $barangay !== '') {
        $sql .= ' WHERE reporter.Barangay = :barangay ORDER BY rc.updated_at DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':barangay' => $barangay]);
    } else {
        $sql .= ' WHERE rc.rescue_org_id = :org_id ORDER BY rc.updated_at DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':org_id' => $orgUserId]);
    }

    return $stmt->fetchAll();
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_adoption_listings(PDO $pdo, ?string $filter = null): array
{
    $sql = '
        SELECT l.*,
               u.Name AS org_name,
               u.Phone AS org_phone,
               COALESCE(NULLIF(l.photo_path, \'\'), i.photo_path) AS display_photo
        FROM adoption_listings l
        INNER JOIN `user` u ON u.UserID = l.rescue_org_id
        LEFT JOIN rescue_cases rc ON rc.rescue_case_id = l.rescue_case_id
        LEFT JOIN incident i ON i.IncidentID = rc.incident_id
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
 * Creates or refreshes an adoption listing when a rescue reaches Ready for Adoption.
 */
function ensure_adoption_listing_for_case(PDO $pdo, int $rescueCaseId, int $orgUserId): void
{
    $stmt = $pdo->prepare('
        SELECT rc.rescue_case_id, rc.rescue_org_id, i.Description, i.photo_path
        FROM rescue_cases rc
        INNER JOIN incident i ON i.IncidentID = rc.incident_id
        WHERE rc.rescue_case_id = :id
        LIMIT 1
    ');
    $stmt->execute([':id' => $rescueCaseId]);
    $case = $stmt->fetch();
    if (!$case) {
        return;
    }

    $existing = $pdo->prepare('SELECT listing_id, status FROM adoption_listings WHERE rescue_case_id = :id LIMIT 1');
    $existing->execute([':id' => $rescueCaseId]);
    $listing = $existing->fetch();

    $description = trim((string) ($case['Description'] ?? ''));
    if ($description === '') {
        $description = 'Rescue dog available for adoption through Pawdar.';
    }

    if ($listing) {
        if ((string) $listing['status'] !== 'Available') {
            $pdo->prepare('UPDATE adoption_listings SET status = \'Available\' WHERE listing_id = :id')
                ->execute([':id' => (int) $listing['listing_id']]);
        }

        return;
    }

    $insert = $pdo->prepare('
        INSERT INTO adoption_listings (rescue_case_id, rescue_org_id, dog_name, dog_description, photo_path, status)
        VALUES (:rescue_case_id, :org_id, :dog_name, :description, :photo_path, \'Available\')
    ');
    $insert->execute([
        ':rescue_case_id' => $rescueCaseId,
        ':org_id' => (int) $case['rescue_org_id'],
        ':dog_name' => 'Rescue dog',
        ':description' => $description,
        ':photo_path' => $case['photo_path'] ?? null,
    ]);
}

/**
 * Updates rescue case pipeline status.
 */
function update_rescue_status(PDO $pdo, int $rescueCaseId, string $status, int $userId, string $userRole = ''): bool
{
    $allowed = ['Spotted', 'Rescued', 'Under Vet Care', 'Ready for Adoption'];
    if (!in_array($status, $allowed, true)) {
        return false;
    }

    if ($userRole === 'Admin') {
        $currentStmt = $pdo->prepare('SELECT status, rescue_org_id FROM rescue_cases WHERE rescue_case_id = :id LIMIT 1');
        $currentStmt->execute([':id' => $rescueCaseId]);
    } else {
        $currentStmt = $pdo->prepare('
            SELECT status, rescue_org_id FROM rescue_cases
            WHERE rescue_case_id = :id AND rescue_org_id = :org_id
            LIMIT 1
        ');
        $currentStmt->execute([':id' => $rescueCaseId, ':org_id' => $userId]);
    }

    $current = $currentStmt->fetch();
    if (!$current) {
        return false;
    }

    $previousStatus = (string) $current['status'];
    if ($previousStatus === $status) {
        return true;
    }

    if ($userRole === 'Admin') {
        $update = $pdo->prepare('
            UPDATE rescue_cases
            SET status = :status, updated_at = CURRENT_TIMESTAMP
            WHERE rescue_case_id = :id
        ');
        $update->execute([':status' => $status, ':id' => $rescueCaseId]);
    } else {
        $update = $pdo->prepare('
            UPDATE rescue_cases
            SET status = :status, updated_at = CURRENT_TIMESTAMP
            WHERE rescue_case_id = :id AND rescue_org_id = :org_id
        ');
        $update->execute([':status' => $status, ':id' => $rescueCaseId, ':org_id' => $userId]);
    }

    if ($update->rowCount() === 0) {
        return false;
    }

    $pdo->prepare('INSERT INTO rescue_case_history (rescue_case_id, status, updated_by) VALUES (:id, :status, :user_id)')
        ->execute([':id' => $rescueCaseId, ':status' => $status, ':user_id' => $userId]);

    if ($status === 'Ready for Adoption') {
        ensure_adoption_listing_for_case($pdo, $rescueCaseId, (int) $current['rescue_org_id']);
    }

    return true;
}
