<?php
$userInitials = htmlspecialchars((string) ($_SESSION['user_initials'] ?? '?'));
$avatarClass = avatar_color_class((int) ($_SESSION['user_id'] ?? 0));
$notificationCount = 0;

try {
    require_once __DIR__ . '/incidents.php';
    if (isset($_SESSION['user_id'])) {
        $notificationCount = fetch_unread_notification_count(db(), (int) $_SESSION['user_id']);
    }
} catch (Throwable $exception) {
    $notificationCount = 0;
}
?>
<header class="app-mobile-header hidden-desktop">
    <div class="status-bar">
        <span>9:41</span>
        <div class="flex items-center gap-sm">
            <i data-lucide="signal"></i>
            <i data-lucide="wifi"></i>
            <i data-lucide="battery-full"></i>
        </div>
    </div>
    <div class="top-row">
        <div class="flex items-center gap-sm">
            <div class="icon-box icon-box-sm" style="background: rgba(255,255,255,.18); color: #fff;">
                <i data-lucide="paw-print"></i>
            </div>
            <span style="font-weight:800;font-size:21px;color:#fff;"><?= SITE_NAME ?></span>
        </div>
        <div class="flex items-center gap-md">
            <div class="notification-wrap">
                <button type="button" class="notification-bell-btn notification-bell-btn--light" data-notification-bell aria-label="Notifications">
                    <i data-lucide="bell" style="color:#fff;"></i>
                    <?php if ($notificationCount > 0): ?>
                        <span class="notification-badge" data-notification-count><?= (int) $notificationCount ?></span>
                    <?php else: ?>
                        <span class="notification-badge is-hidden" data-notification-count>0</span>
                    <?php endif; ?>
                </button>
                <div class="notification-dropdown" data-notification-dropdown hidden>
                    <div class="notification-dropdown-header">Notifications</div>
                    <div class="notification-list" data-notification-list></div>
                </div>
            </div>
            <div class="avatar avatar-sm <?= htmlspecialchars($avatarClass) ?>"><?= $userInitials ?></div>
        </div>
    </div>
    <div class="search-bar">
        <i data-lucide="search"></i>
        <span style="font-size:14px;color:var(--air-force);font-weight:600;">Search incidents or dogs…</span>
    </div>
</header>
