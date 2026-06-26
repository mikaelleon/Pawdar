<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/cases.php';
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

if ($incidentId <= 0 || !update_case_status(db(), $incidentId, $newStatus, (int) $_SESSION['user_id'])) {
    json_response(['success' => false, 'message' => 'Invalid request'], 400);
}

notify_case_status_change(db(), $incidentId, $newStatus);
$meta = case_status_meta($newStatus);

json_response([
    'success' => true,
    'status_label' => $meta['label'],
    'status_class' => $meta['class'],
]);
