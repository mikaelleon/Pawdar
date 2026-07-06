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
$newStatus = trim((string) ($input['status'] ?? ''));
$userId = (int) $_SESSION['user_id'];

$incidentIds = [];
if (isset($input['incident_ids']) && is_array($input['incident_ids'])) {
    foreach ($input['incident_ids'] as $rawId) {
        $id = (int) $rawId;
        if ($id > 0) {
            $incidentIds[] = $id;
        }
    }
} else {
    $singleId = (int) ($input['incident_id'] ?? 0);
    if ($singleId > 0) {
        $incidentIds[] = $singleId;
    }
}

if ($incidentIds === [] || $newStatus === '') {
    json_response(['success' => false, 'message' => 'Invalid request'], 400);
}

$pdo = db();
$updated = 0;
foreach ($incidentIds as $incidentId) {
    if (!update_case_status($pdo, $incidentId, $newStatus, $userId)) {
        continue;
    }
    notify_case_status_change($pdo, $incidentId, $newStatus);
    $updated++;
}

if ($updated === 0) {
    json_response(['success' => false, 'message' => 'No cases were updated'], 400);
}

$meta = case_status_meta($newStatus);
$message = $updated === 1
    ? 'Case updated to ' . $meta['label']
    : $updated . ' cases updated to ' . $meta['label'];

json_response([
    'success' => true,
    'updated' => $updated,
    'status_label' => $meta['label'],
    'status_class' => $meta['class'],
    'message' => $message,
]);
