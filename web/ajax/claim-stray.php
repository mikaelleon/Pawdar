<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
require_role(['Rescue Organization', 'Admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid method'], 405);
}

if (!validate_csrf(request_csrf_token())) {
    json_response(['success' => false, 'message' => 'Invalid CSRF token'], 403);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
$incidentId = (int) ($input['incident_id'] ?? 0);

if ($incidentId <= 0) {
    json_response(['success' => false, 'message' => 'Invalid incident'], 400);
}

$pdo = db();

$check = $pdo->prepare('SELECT IncidentID, IncidentType FROM incident WHERE IncidentID = :incident_id LIMIT 1');
$check->execute([':incident_id' => $incidentId]);
$incident = $check->fetch();

if (!$incident || $incident['IncidentType'] !== 'Injured Stray') {
    json_response(['success' => false, 'message' => 'Only injured stray cases can be claimed'], 400);
}

$caseCheck = $pdo->prepare('SELECT CaseID FROM `case` WHERE IncidentID = :incident_id LIMIT 1');
$caseCheck->execute([':incident_id' => $incidentId]);

if ($caseCheck->fetch()) {
    $update = $pdo->prepare('UPDATE `case` SET CaseStatus = \'Under Investigation\' WHERE IncidentID = :incident_id');
    $update->execute([':incident_id' => $incidentId]);
} else {
    $insert = $pdo->prepare('INSERT INTO `case` (IncidentID, CaseStatus) VALUES (:incident_id, \'Under Investigation\')');
    $insert->execute([':incident_id' => $incidentId]);
}

$notify = $pdo->prepare('
    INSERT INTO notifications (user_id, message, link)
    SELECT i.UserID, :message, :link
    FROM incident i WHERE i.IncidentID = :incident_id
');
$notify->execute([
    ':message' => 'Your injured stray report has been claimed by a rescue organization.',
    ':link' => 'feed.php',
    ':incident_id' => $incidentId,
]);

json_response(['success' => true, 'message' => 'Case claimed']);
