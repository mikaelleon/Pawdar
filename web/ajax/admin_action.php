<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/dogs.php';
require_role(['Admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid method'], 405);
}

if (!validate_csrf(request_csrf_token())) {
    json_response(['success' => false, 'message' => 'Invalid CSRF token'], 403);
}

$action = (string) ($_POST['action'] ?? '');
$pdo = db();

try {
    if ($action === 'approve_user') {
        $userId = (int) ($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            json_response(['success' => false, 'message' => 'Invalid user'], 400);
        }

        $stmt = $pdo->prepare('UPDATE `user` SET Status = \'active\' WHERE UserID = :id AND Status = \'pending\'');
        $stmt->execute([':id' => $userId]);

        json_response(['success' => $stmt->rowCount() > 0, 'message' => $stmt->rowCount() > 0 ? 'User approved' : 'User not found']);
    }

    if ($action === 'approve_dog') {
        $dogId = (int) ($_POST['dog_id'] ?? 0);
        if ($dogId <= 0) {
            json_response(['success' => false, 'message' => 'Invalid dog'], 400);
        }

        $stmt = $pdo->prepare('UPDATE dog SET Status = \'Registered\' WHERE dog_id = :id AND Status = \'Pending\'');
        $stmt->execute([':id' => $dogId]);

        json_response(['success' => $stmt->rowCount() > 0, 'message' => $stmt->rowCount() > 0 ? 'Dog registered' : 'Dog not found']);
    }

    json_response(['success' => false, 'message' => 'Unknown action'], 400);
} catch (PDOException $exception) {
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
