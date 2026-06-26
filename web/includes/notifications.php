<?php

require_once __DIR__ . '/db.php';

/**
 * @return list<array<string, mixed>>
 */
function fetch_user_notifications(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('
        SELECT * FROM notifications
        WHERE user_id = :user_id
        ORDER BY created_at DESC
        LIMIT 100
    ');
    $stmt->execute([':user_id' => $userId]);

    return $stmt->fetchAll();
}

/**
 * Marks one notification read.
 */
function mark_notification_read(PDO $pdo, int $notificationId, int $userId): void
{
    $stmt = $pdo->prepare('
        UPDATE notifications SET is_read = 1
        WHERE notification_id = :id AND user_id = :user_id
    ');
    $stmt->execute([':id' => $notificationId, ':user_id' => $userId]);
}

/**
 * Marks all notifications read for user.
 */
function mark_all_notifications_read(PDO $pdo, int $userId): void
{
    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = :user_id');
    $stmt->execute([':user_id' => $userId]);
}

/**
 * Returns icon name for notification type.
 */
function notification_icon(string $type): string
{
    return match ($type) {
        'dog_match' => 'paw-print',
        'case_update' => 'shield',
        'rescue' => 'heart',
        'incident' => 'alert-triangle',
        default => 'bell',
    };
}
