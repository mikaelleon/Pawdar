<?php
$topbarTitle = $topbarTitle ?? '';
$showSearch = $showSearch ?? true;
$searchPlaceholder = $searchPlaceholder ?? 'Search incidents or dogs…';
$bellBadgeCount = 0;

try {
    require_once __DIR__ . '/incidents.php';
    if (isset($_SESSION['user_id'])) {
        $bellBadgeCount = fetch_bell_badge_count(
            db(),
            (int) $_SESSION['user_id'],
            (string) ($_SESSION['user_barangay'] ?? '')
        );
    }
} catch (Throwable $exception) {
    $bellBadgeCount = 0;
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
                    <?php render_bell_badge($bellBadgeCount); ?>
                </button>
                <div class="notification-dropdown" data-notification-dropdown hidden>
                    <div class="notification-dropdown-header">Notifications</div>
                    <div class="notification-list" data-notification-list></div>
                    <div class="notification-dropdown-footer">
                        <a href="notifications.php" class="notification-read-more">Read more</a>
                    </div>
                </div>
            </div>
            <?php
            $avatarSize = 'md';
            require __DIR__ . '/../partials/avatar-menu.php';
            ?>
        </div>
    </div>
</header>
