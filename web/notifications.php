<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/notifications.php';

$pdo = db();
$userId = (int) $_SESSION['user_id'];

if (isset($_GET['read_all'])) {
    mark_all_notifications_read($pdo, $userId);
    header('Location: notifications.php');
    exit;
}

if (isset($_GET['read'])) {
    $nid = (int) $_GET['read'];
    mark_notification_read($pdo, $nid, $userId);
    $linkStmt = $pdo->prepare('SELECT link FROM notifications WHERE notification_id = :id AND user_id = :uid LIMIT 1');
    $linkStmt->execute([':id' => $nid, ':uid' => $userId]);
    $link = (string) ($linkStmt->fetchColumn() ?: 'notifications.php');
    header('Location: ' . $link);
    exit;
}

$items = fetch_user_notifications($pdo, $userId);

app_layout_start('feed', 'Notifications', [
    'showSearch' => false,
    'topbarTitle' => 'Notifications',
    'breadcrumbs' => [['label' => 'Notifications']],
]);
?>

<div class="feed-header flex justify-between items-center">
    <h1 class="feed-title">Notifications</h1>
    <a href="notifications.php?read_all=1" class="text-sm link-hover">Mark all as read</a>
</div>

<?php if (count($items) === 0): ?>
    <div class="feed-empty-state">
        <p class="feed-empty-title">You're all caught up</p>
        <a href="feed.php" class="btn-outline btn-sm">Browse your feed</a>
    </div>
<?php else: ?>
    <div class="notification-page-list">
        <?php foreach ($items as $item):
            $unread = !(int) $item['is_read'];
            $icon = notification_icon((string) ($item['notification_type'] ?? 'general'));
        ?>
            <a href="notifications.php?read=<?= (int) $item['notification_id'] ?>" class="notification-page-row<?= $unread ? ' is-unread' : '' ?>">
                <div class="icon-box icon-box-sm"><i data-lucide="<?= htmlspecialchars($icon) ?>"></i></div>
                <div class="flex-1">
                    <div class="text-sm"><?= htmlspecialchars((string) $item['message']) ?></div>
                    <div class="text-xs text-muted"><?= htmlspecialchars(time_elapsed_string((string) $item['created_at'])) ?></div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php app_layout_end([]); ?>
