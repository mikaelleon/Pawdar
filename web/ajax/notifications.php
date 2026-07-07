<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/incidents.php';

require_login();

$pdo = db();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf(request_csrf_token())) {
        json_response(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }

    $update = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0');
    $update->execute([':user_id' => $userId]);

    json_response([
        'success' => true,
        'count' => fetch_unread_notification_count($pdo, $userId),
    ]);
}

$count = fetch_unread_notification_count($pdo, $userId);

$stmt = $pdo->prepare('
    SELECT notification_id, message, link, created_at, is_read
    FROM notifications
    WHERE user_id = :user_id
    ORDER BY created_at DESC
    LIMIT 10
');
$stmt->execute([':user_id' => $userId]);
$items = [];

foreach ($stmt->fetchAll() as $row) {
    $items[] = [
        'id' => (int) $row['notification_id'],
        'message' => $row['message'],
        'link' => $row['link'],
        'time' => time_elapsed_string((string) $row['created_at']),
        'is_read' => (int) $row['is_read'] === 1,
    ];
}

json_response([
    'success' => true,
    'count' => $count,
    'items' => $items,
]);
