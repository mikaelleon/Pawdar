<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/rescue.php';

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

if (!claim_stray_case($pdo, $incidentId, (int) $_SESSION['user_id'])) {
    json_response(['success' => false, 'message' => 'Case already claimed'], 400);
}

$notify = $pdo->prepare('
    INSERT INTO notifications (user_id, message, link, notification_type)
    SELECT i.UserID, :message, :link, \'rescue\'
    FROM incident i WHERE i.IncidentID = :incident_id
');
$notify->execute([
    ':message' => 'Your injured stray report has been claimed by a rescue organization.',
    ':link' => 'rescue.php',
    ':incident_id' => $incidentId,
]);

json_response(['success' => true, 'message' => 'Case claimed']);
