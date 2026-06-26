<?php
$activeNav = $activeNav ?? '';
?>
<aside class="app-sidebar hidden-mobile">
    <a href="feed.php" class="flex items-center gap-sm" style="padding: 4px 8px 22px;">
        <div class="logo-mark"><i data-lucide="paw-print"></i></div>
        <span class="logo-text"><?= SITE_NAME ?></span>
    </a>
    <nav class="sidebar-nav">
        <a href="feed.php" class="sidebar-link<?= $activeNav === 'feed' ? ' is-active' : '' ?>">
            <i data-lucide="layout-list"></i> Feed
        </a>
        <a href="map.php" class="sidebar-link<?= $activeNav === 'map' ? ' is-active' : '' ?>">
            <i data-lucide="map"></i> Map
        </a>
        <a href="dog-profile.php" class="sidebar-link<?= $activeNav === 'registry' ? ' is-active' : '' ?>">
            <i data-lucide="book-marked"></i> Registry
        </a>
        <a href="cases.php" class="sidebar-link<?= $activeNav === 'cases' ? ' is-active' : '' ?>">
            <i data-lucide="folder-check"></i> Cases
        </a>
        <a href="first-aid.php" class="sidebar-link<?= $activeNav === 'first-aid' ? ' is-active' : '' ?>">
            <i data-lucide="heart-pulse"></i> First Aid
        </a>
        <a href="breeds.php" class="sidebar-link<?= $activeNav === 'breeds' ? ' is-active' : '' ?>">
            <i data-lucide="dog"></i> Breeds
        </a>
    </nav>
    <div class="sidebar-user">
        <div class="avatar avatar-md">MJ</div>
        <div>
            <div class="sidebar-user-name">Maria J.</div>
            <div class="sidebar-user-role">Community member</div>
        </div>
    </div>
</aside>
