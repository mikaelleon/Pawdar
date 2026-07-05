<?php
$activeNav = $activeNav ?? '';
$userRole = (string) ($_SESSION['user_role'] ?? '');

$bottomItems = [
    ['key' => 'feed', 'href' => 'feed.php', 'icon' => 'layout-list', 'label' => 'Feed'],
    ['key' => 'map', 'href' => 'map.php', 'icon' => 'map', 'label' => 'Map'],
    ['key' => 'registry', 'href' => 'registry.php', 'icon' => 'book-marked', 'label' => 'Registry'],
    ['key' => 'cases', 'href' => 'cases.php', 'icon' => 'folder-check', 'label' => 'Cases', 'nav' => 'cases'],
    ['key' => 'profile', 'href' => 'profile.php', 'icon' => 'user', 'label' => 'Profile', 'always' => true],
];
?>
<nav class="bottom-nav hidden-desktop">
    <?php foreach ($bottomItems as $item): ?>
        <?php
        if (!($item['always'] ?? false)) {
            $navKey = $item['nav'] ?? $item['key'];
            if (!role_can_see_nav($navKey, $userRole)) {
                continue;
            }
        }
        ?>
        <a href="<?= htmlspecialchars($item['href']) ?>" class="bottom-nav-item<?= $activeNav === $item['key'] ? ' is-active' : '' ?>"<?= $activeNav === $item['key'] ? ' aria-current="page"' : '' ?>>
            <span class="bottom-nav-icon"><i data-lucide="<?= htmlspecialchars($item['icon']) ?>"></i></span>
            <span class="bottom-nav-label"><?= htmlspecialchars($item['label']) ?></span>
            <div class="nav-indicator"></div>
        </a>
    <?php endforeach; ?>
</nav>
