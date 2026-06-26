<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/incidents.php';

require_login();
require_role(['LGU Official', 'Admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid method'], 405);
}

if (!validate_csrf(request_csrf_token())) {
    json_response(['success' => false, 'message' => 'Invalid CSRF token'], 403);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
$incidentId = (int) ($input['incident_id'] ?? 0);
$newStatus = trim((string) ($input['status'] ?? ''));

$allowed = ['Received', 'Under Investigation', 'Resolved', 'Referred'];
if ($incidentId <= 0 || !in_array($newStatus, $allowed, true)) {
    json_response(['success' => false, 'message' => 'Invalid request'], 400);
}

$pdo = db();

$caseCheck = $pdo->prepare('SELECT CaseID, CaseStatus FROM `case` WHERE IncidentID = :incident_id LIMIT 1');
$caseCheck->execute([':incident_id' => $incidentId]);
$case = $caseCheck->fetch();

if ($case) {
    $update = $pdo->prepare('UPDATE `case` SET CaseStatus = :status WHERE CaseID = :case_id');
    $update->execute([
        ':status' => $newStatus,
        ':case_id' => (int) $case['CaseID'],
    ]);
} else {
    $insert = $pdo->prepare('INSERT INTO `case` (IncidentID, CaseStatus) VALUES (:incident_id, :status)');
    $insert->execute([
        ':incident_id' => $incidentId,
        ':status' => $newStatus,
    ]);
}

notify_case_status_change($pdo, $incidentId, $newStatus);
$meta = case_status_meta($newStatus);

json_response([
    'success' => true,
    'status_label' => $meta['label'],
    'status_class' => $meta['class'],
]);
