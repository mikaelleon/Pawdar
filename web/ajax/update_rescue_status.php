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
$rescueCaseId = (int) ($input['rescue_case_id'] ?? 0);
$newStatus = trim((string) ($input['status'] ?? ''));

if ($rescueCaseId <= 0) {
    json_response(['success' => false, 'message' => 'Invalid rescue case'], 400);
}

$pdo = db();
$userId = (int) $_SESSION['user_id'];
$userRole = current_user_role();

if (!update_rescue_status($pdo, $rescueCaseId, $newStatus, $userId, $userRole)) {
    json_response(['success' => false, 'message' => 'Case not found or not owned by your organization.'], 400);
}

json_response(['success' => true, 'status' => $newStatus]);
