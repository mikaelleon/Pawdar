<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_login_active();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false], 405);
}

if (!validate_csrf(request_csrf_token())) {
    json_response(['success' => false], 403);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
$field = (string) ($input['field'] ?? '');
$value = (int) ($input['value'] ?? 0);

$allowed = ['notify_incidents', 'notify_dog_match', 'notify_case_updates', 'notify_vaccine'];
if (!in_array($field, $allowed, true)) {
    json_response(['success' => false], 400);
}

$pdo = db();
$stmt = $pdo->prepare('UPDATE `user` SET ' . $field . ' = :val WHERE UserID = :id');
$stmt->execute([':val' => $value, ':id' => (int) $_SESSION['user_id']]);

json_response(['success' => true]);
