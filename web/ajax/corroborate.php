<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid method'], 405);
}

if (!validate_csrf(request_csrf_token())) {
    json_response(['success' => false, 'message' => 'Invalid CSRF token'], 403);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
$incidentId = (int) ($input['incident_id'] ?? 0);
$userId = (int) $_SESSION['user_id'];

if ($incidentId <= 0) {
    json_response(['success' => false, 'message' => 'Invalid incident'], 400);
}

$pdo = db();

$ownerCheck = $pdo->prepare('SELECT UserID FROM incident WHERE IncidentID = :incident_id LIMIT 1');
$ownerCheck->execute([':incident_id' => $incidentId]);
$incident = $ownerCheck->fetch();

if (!$incident) {
    json_response(['success' => false, 'message' => 'Incident not found'], 404);
}

if ((int) $incident['UserID'] === $userId) {
    json_response(['success' => false, 'message' => 'You cannot corroborate your own report'], 403);
}

$existing = $pdo->prepare('
    SELECT corroboration_id FROM corroborations
    WHERE incident_id = :incident_id AND user_id = :user_id
    LIMIT 1
');
$existing->execute([
    ':incident_id' => $incidentId,
    ':user_id' => $userId,
]);

if ($existing->fetch()) {
    json_response(['success' => false, 'message' => 'Already corroborated'], 409);
}

$insert = $pdo->prepare('
    INSERT INTO corroborations (incident_id, user_id)
    VALUES (:incident_id, :user_id)
');
$insert->execute([
    ':incident_id' => $incidentId,
    ':user_id' => $userId,
]);

$countStmt = $pdo->prepare('SELECT COUNT(*) FROM corroborations WHERE incident_id = :incident_id');
$countStmt->execute([':incident_id' => $incidentId]);

json_response([
    'success' => true,
    'new_count' => (int) $countStmt->fetchColumn(),
]);
