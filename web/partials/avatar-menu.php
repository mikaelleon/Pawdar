<?php
$avatarSize = $avatarSize ?? 'md';
$userInitials = htmlspecialchars((string) ($_SESSION['user_initials'] ?? '?'));
$userName = htmlspecialchars((string) ($_SESSION['user_name'] ?? ''));
$userRole = htmlspecialchars((string) ($_SESSION['user_role'] ?? ''));
$avatarClass = avatar_color_class((int) ($_SESSION['user_id'] ?? 0));
$avatarLight = !empty($avatarLight);
?>
<div class="avatar-menu-wrap">
    <button type="button"
            class="avatar avatar-<?= htmlspecialchars($avatarSize) ?> <?= htmlspecialchars($avatarClass) ?><?= $avatarLight ? ' avatar-on-dark' : '' ?>"
            data-avatar-menu
            aria-label="Account menu for <?= $userName ?>"
            aria-expanded="false"
            aria-haspopup="true">
        <?= $userInitials ?>
    </button>
    <div class="avatar-dropdown" data-avatar-dropdown hidden>
        <div class="avatar-dropdown-header">
            <div class="avatar avatar-sm <?= htmlspecialchars($avatarClass) ?>"><?= $userInitials ?></div>
            <div>
                <div class="avatar-dropdown-name"><?= $userName ?></div>
                <div class="avatar-dropdown-role text-xs text-muted"><?= $userRole ?></div>
            </div>
        </div>
        <nav class="avatar-dropdown-nav" aria-label="Account">
            <a href="profile.php" class="avatar-dropdown-link">
                <i data-lucide="user"></i> Profile
            </a>
            <a href="profile.php#settings" class="avatar-dropdown-link">
                <i data-lucide="settings"></i> Settings
            </a>
            <a href="auth/logout.php" class="avatar-dropdown-link avatar-dropdown-link--danger">
                <i data-lucide="log-out"></i> Log out
            </a>
        </nav>
    </div>
</div>
