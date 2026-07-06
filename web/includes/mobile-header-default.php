<?php
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
                <button type="button" class="notification-bell-btn notification-bell-btn--light" data-notification-bell aria-label="Notifications" aria-expanded="false" aria-haspopup="true">
                    <i data-lucide="bell" style="color:#fff;"></i>
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
            $avatarSize = 'sm';
            $avatarLight = true;
            require __DIR__ . '/../partials/avatar-menu.php';
            ?>
        </div>
    </div>
    <?php if ($showMobileSearch ?? true): ?>
    <div class="search-bar">
        <i data-lucide="search"></i>
        <span style="font-size:14px;color:var(--air-force);font-weight:600;">Search incidents or dogs…</span>
    </div>
    <?php endif; ?>
</header>
