<?php
$activeNav = $activeNav ?? '';
?>
<nav class="bottom-nav hidden-desktop">
    <a href="feed.php" class="bottom-nav-item<?= $activeNav === 'feed' ? ' is-active' : '' ?>">
        <i data-lucide="layout-list"></i>
        <span>Feed</span>
        <div class="nav-indicator"></div>
    </a>
    <a href="map.php" class="bottom-nav-item<?= $activeNav === 'map' ? ' is-active' : '' ?>">
        <i data-lucide="map"></i>
        <span>Map</span>
        <div class="nav-indicator"></div>
    </a>
    <a href="dog-profile.php" class="bottom-nav-item<?= $activeNav === 'registry' ? ' is-active' : '' ?>">
        <i data-lucide="book-marked"></i>
        <span>Registry</span>
        <div class="nav-indicator"></div>
    </a>
    <a href="cases.php" class="bottom-nav-item<?= $activeNav === 'cases' ? ' is-active' : '' ?>">
        <i data-lucide="folder-check"></i>
        <span>Cases</span>
        <div class="nav-indicator"></div>
    </a>
    <a href="login.php" class="bottom-nav-item">
        <i data-lucide="user"></i>
        <span>Profile</span>
        <div class="nav-indicator"></div>
    </a>
</nav>
