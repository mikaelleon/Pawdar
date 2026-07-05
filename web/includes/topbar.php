<?php
$topbarTitle = $topbarTitle ?? '';
$showSearch = $showSearch ?? true;
$searchPlaceholder = $searchPlaceholder ?? 'Search incidents or dogs…';
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
<header class="app-topbar hidden-mobile">
    <div class="app-topbar-inner">
        <?php if ($topbarTitle): ?>
            <div class="page-title"><?= htmlspecialchars($topbarTitle) ?></div>
        <?php endif; ?>
        <?php if ($showSearch): ?>
            <div class="app-topbar-search search-bar search-bar-light">
                <i data-lucide="search"></i>
                <span class="text-muted"><?= htmlspecialchars($searchPlaceholder) ?></span>
            </div>
        <?php else: ?>
            <div class="flex-1"></div>
        <?php endif; ?>
        <div class="header-actions flex items-center gap-md">
            <button type="button" class="icon-box icon-box-sm theme-toggle-btn" id="darkModeToggle" aria-label="Switch to dark mode" title="Toggle theme">
                <i data-lucide="sun" data-theme-icon></i>
            </button>
            <div class="notification-wrap" style="position:relative;">
                <button type="button" class="notification-bell-btn" data-notification-bell aria-label="Notifications" aria-expanded="false" aria-haspopup="true">
                    <i data-lucide="bell"></i>
                    <?php if ($notificationCount > 0): ?>
                        <span class="notification-badge" data-notification-count><?= (int) $notificationCount ?></span>
                    <?php else: ?>
                        <span class="notification-badge is-hidden" data-notification-count>0</span>
                    <?php endif; ?>
                </button>
                <div class="notification-dropdown" data-notification-dropdown hidden>
                    <div class="notification-dropdown-header">Notifications</div>
                    <div class="notification-list" data-notification-list></div>
                    <div class="notification-dropdown-footer">
                        <a href="notifications.php" class="notification-read-more">Read more</a>
                    </div>
                </div>
            </div>
            <a href="profile.php" class="avatar avatar-md <?= htmlspecialchars($avatarClass) ?>" title="<?= $userInitials ?>"><?= $userInitials ?></a>
        </div>
    </div>
</header>
