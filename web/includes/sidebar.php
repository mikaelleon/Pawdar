<?php
$activeNav = $activeNav ?? '';
$userName = htmlspecialchars((string) ($_SESSION['user_name'] ?? ''));
$userRole = htmlspecialchars((string) ($_SESSION['user_role'] ?? ''));
$userInitials = htmlspecialchars((string) ($_SESSION['user_initials'] ?? '?'));
$avatarClass = avatar_color_class((int) ($_SESSION['user_id'] ?? 0));

$navItems = [
    'feed' => ['href' => 'feed.php', 'icon' => 'layout-list', 'label' => 'Feed'],
    'map' => ['href' => 'map.php', 'icon' => 'map', 'label' => 'Map'],
    'registry' => ['href' => 'registry.php', 'icon' => 'book-marked', 'label' => 'Registry'],
    'cases' => ['href' => 'cases.php', 'icon' => 'folder-check', 'label' => 'Cases'],
    'first-aid' => ['href' => 'first-aid.php', 'icon' => 'heart-pulse', 'label' => 'First Aid'],
    'breeds' => ['href' => 'breeds.php', 'icon' => 'dog', 'label' => 'Breeds'],
    'rescue-board' => ['href' => 'rescue.php', 'icon' => 'life-buoy', 'label' => 'Rescue Board'],
    'analytics' => ['href' => 'analytics.php', 'icon' => 'bar-chart-3', 'label' => 'Analytics'],
    'admin' => ['href' => 'admin.php', 'icon' => 'shield-check', 'label' => 'Admin'],
];
?>
<aside class="app-sidebar hidden-mobile">
    <a href="feed.php" class="flex items-center gap-sm" style="padding: 4px 8px 22px;">
        <div class="logo-mark"><i data-lucide="paw-print"></i></div>
        <span class="logo-text"><?= SITE_NAME ?></span>
    </a>
    <nav class="sidebar-nav">
        <?php foreach ($navItems as $key => $item): ?>
            <?php if (!role_can_see_nav($key, (string) ($_SESSION['user_role'] ?? ''))) {
                continue;
            } ?>
            <a href="<?= htmlspecialchars($item['href']) ?>" class="sidebar-link<?= $activeNav === $key ? ' is-active' : '' ?>"<?= $activeNav === $key ? ' aria-current="page"' : '' ?>>
                <span class="sidebar-link-icon"><i data-lucide="<?= htmlspecialchars($item['icon']) ?>"></i></span>
                <span class="sidebar-link-label"><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-user">
        <div class="avatar avatar-md <?= htmlspecialchars($avatarClass) ?>"><?= $userInitials ?></div>
        <div>
            <div class="sidebar-user-name"><?= $userName ?></div>
            <div class="sidebar-user-role"><?= $userRole ?></div>
        </div>
    </div>
    <a href="auth/logout.php" class="sidebar-logout text-xs">Log out</a>
</aside>
