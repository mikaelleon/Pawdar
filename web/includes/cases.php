<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * @return list<array<string, mixed>>
 */
function fetch_cases_for_barangay(
    PDO $pdo,
    string $barangay,
    ?string $status = null,
    ?string $type = null,
    ?string $sort = 'urgent'
): array {
    $sql = '
        SELECT c.CaseID, c.CaseStatus, c.RabiesMonitoring, c.assigned_to,
               i.IncidentID, i.IncidentType, i.Location, i.Date AS filed_date,
               u.Name AS reporter_name, u.Role AS reporter_role,
               d.dog_id, d.DogName, d.RegistryID,
               assignee.Name AS assignee_name
        FROM `case` c
        INNER JOIN incident i ON c.IncidentID = i.IncidentID
        INNER JOIN `user` u ON i.UserID = u.UserID
        LEFT JOIN dog d ON i.dog_id = d.dog_id
        LEFT JOIN `user` assignee ON assignee.UserID = c.assigned_to
        WHERE i.Location LIKE :barangay
    ';
    $params = [':barangay' => '%' . $barangay . '%'];

    if ($status !== null && $status !== '' && $status !== 'all') {
        $sql .= ' AND c.CaseStatus = :status';
        $params[':status'] = $status;
    }

    if ($type !== null && $type !== '' && $type !== 'all') {
        $sql .= ' AND i.IncidentType = :type';
        $params[':type'] = $type;
    }

    $sort = $sort ?? 'urgent';
    if ($sort === 'filed_asc') {
        $sql .= ' ORDER BY i.Date ASC';
    } elseif ($sort === 'filed_desc') {
        $sql .= ' ORDER BY i.Date DESC';
    } elseif ($sort === 'status') {
        $sql .= ' ORDER BY c.CaseStatus ASC, i.Date DESC';
    } else {
        $sql .= '
            ORDER BY
                CASE WHEN c.RabiesMonitoring = 1 AND c.CaseStatus NOT IN (\'Resolved\', \'Referred\') THEN 0 ELSE 1 END,
                CASE WHEN c.CaseStatus IN (\'Received\', \'Under Investigation\', \'Action Taken\') THEN 0 ELSE 1 END,
                i.Date DESC
        ';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/**
 * Whether case is on active rabies watch.
 *
 * @param array<string, mixed> $case
 */
function case_is_rabies_watch(array $case): bool
{
    return (int) ($case['RabiesMonitoring'] ?? 0) === 1
        && !in_array((string) ($case['CaseStatus'] ?? ''), ['Resolved', 'Referred'], true);
}

/**
 * @return array{received: int, investigating: int, resolved: int, rabies: int}
 */
function fetch_case_summary(PDO $pdo, string $barangay): array
{
    $stmt = $pdo->prepare('
        SELECT
            SUM(CASE WHEN c.CaseStatus = \'Received\' THEN 1 ELSE 0 END) AS received,
            SUM(CASE WHEN c.CaseStatus IN (\'Under Investigation\', \'Action Taken\') THEN 1 ELSE 0 END) AS investigating,
            SUM(CASE WHEN c.CaseStatus IN (\'Resolved\', \'Referred\') THEN 1 ELSE 0 END) AS resolved,
            SUM(CASE WHEN c.RabiesMonitoring = 1 AND c.CaseStatus NOT IN (\'Resolved\', \'Referred\') THEN 1 ELSE 0 END) AS rabies
        FROM `case` c
        INNER JOIN incident i ON c.IncidentID = i.IncidentID
        WHERE i.Location LIKE :barangay
    ');
    $stmt->execute([':barangay' => '%' . $barangay . '%']);
    $row = $stmt->fetch() ?: [];

    return [
        'received' => (int) ($row['received'] ?? 0),
        'investigating' => (int) ($row['investigating'] ?? 0),
        'resolved' => (int) ($row['resolved'] ?? 0),
        'rabies' => (int) ($row['rabies'] ?? 0),
    ];
}

/**
 * @return array<string, mixed>|null
 */
function fetch_case_detail(PDO $pdo, int $caseId): ?array
{
    $stmt = $pdo->prepare('
        SELECT c.*, i.*, u.Name AS reporter_name, u.Role AS reporter_role,
               d.dog_id, d.DogName, d.Breed, d.RegistryID
        FROM `case` c
        INNER JOIN incident i ON c.IncidentID = i.IncidentID
        INNER JOIN `user` u ON i.UserID = u.UserID
        LEFT JOIN dog d ON i.dog_id = d.dog_id
        WHERE c.CaseID = :id
        LIMIT 1
    ');
    $stmt->execute([':id' => $caseId]);
    $row = $stmt->fetch();

    return $row ?: null;
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_case_history(PDO $pdo, int $caseId): array
{
    $stmt = $pdo->prepare('
        SELECT h.*, u.Name AS updater_name
        FROM case_history h
        LEFT JOIN `user` u ON u.UserID = h.updated_by
        WHERE h.CaseID = :case_id
        ORDER BY h.created_at ASC
    ');
    $stmt->execute([':case_id' => $caseId]);

    return $stmt->fetchAll();
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_rabies_checklist(PDO $pdo, int $caseId): array
{
    $stmt = $pdo->prepare('
        SELECT * FROM rabies_checklist
        WHERE CaseID = :case_id
        ORDER BY day_number ASC
    ');
    $stmt->execute([':case_id' => $caseId]);

    return $stmt->fetchAll();
}

/**
 * Updates case status and logs history.
 */
function update_case_status(PDO $pdo, int $incidentId, string $newStatus, int $updatedBy): bool
{
    $allowed = ['Received', 'Under Investigation', 'Action Taken', 'Resolved', 'Referred'];
    if (!in_array($newStatus, $allowed, true)) {
        return false;
    }

    $caseCheck = $pdo->prepare('SELECT CaseID, CaseStatus FROM `case` WHERE IncidentID = :incident_id LIMIT 1');
    $caseCheck->execute([':incident_id' => $incidentId]);
    $case = $caseCheck->fetch();

    if ($case) {
        $caseId = (int) $case['CaseID'];
        $update = $pdo->prepare('UPDATE `case` SET CaseStatus = :status WHERE CaseID = :case_id');
        $update->execute([':status' => $newStatus, ':case_id' => $caseId]);
    } else {
        $insert = $pdo->prepare('INSERT INTO `case` (IncidentID, CaseStatus) VALUES (:incident_id, :status)');
        $insert->execute([':incident_id' => $incidentId, ':status' => $newStatus]);
        $caseId = (int) $pdo->lastInsertId();
    }

    $log = $pdo->prepare('INSERT INTO case_history (CaseID, CaseStatus, updated_by) VALUES (:case_id, :status, :user_id)');
    $log->execute([':case_id' => $caseId, ':status' => $newStatus, ':user_id' => $updatedBy]);

    $biteCheck = $pdo->prepare('SELECT IncidentType FROM incident WHERE IncidentID = :id LIMIT 1');
    $biteCheck->execute([':id' => $incidentId]);
    $incidentType = (string) ($biteCheck->fetchColumn() ?: '');

    if ($incidentType === 'Animal Bite') {
        $pdo->prepare('UPDATE `case` SET RabiesMonitoring = 1 WHERE CaseID = :case_id')->execute([':case_id' => $caseId]);
        ensure_rabies_checklist($pdo, $caseId, $incidentId);
    }

    return true;
}

/**
 * Creates 14-day rabies checklist rows if missing.
 */
function ensure_rabies_checklist(PDO $pdo, int $caseId, int $incidentId): void
{
    $exists = $pdo->prepare('SELECT COUNT(*) FROM rabies_checklist WHERE CaseID = :case_id');
    $exists->execute([':case_id' => $caseId]);
    if ((int) $exists->fetchColumn() > 0) {
        return;
    }

    $dateStmt = $pdo->prepare('SELECT Date FROM incident WHERE IncidentID = :id LIMIT 1');
    $dateStmt->execute([':id' => $incidentId]);
    $base = strtotime((string) ($dateStmt->fetchColumn() ?: 'now'));

    $insert = $pdo->prepare('
        INSERT INTO rabies_checklist (CaseID, day_number, check_date, status)
        VALUES (:case_id, :day, :check_date, \'Pending\')
    ');

    for ($day = 1; $day <= 14; $day++) {
        $insert->execute([
            ':case_id' => $caseId,
            ':day' => $day,
            ':check_date' => date('Y-m-d', strtotime('+' . ($day - 1) . ' days', $base)),
        ]);
    }
}

/**
 * Returns rabies day progress for a case.
 */
function rabies_day_progress(PDO $pdo, int $caseId): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM rabies_checklist WHERE CaseID = :case_id AND status = \'Checked\'');
    $stmt->execute([':case_id' => $caseId]);

    return (int) $stmt->fetchColumn();
}
