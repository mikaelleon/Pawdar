<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

if (($_SESSION['user_status'] ?? 'active') !== 'pending') {
    header('Location: feed.php');
    exit;
}

$pageTitle = 'Account Pending · ' . SITE_NAME;
require __DIR__ . '/includes/head.php';
?>

<div class="auth-page">
    <div class="auth-panel auth-desktop-only auth-panel-pattern">
        <a href="index.php" class="flex items-center gap-sm">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>
        <h1 class="auth-panel-title">Almost there.</h1>
        <p class="auth-panel-sub">Your role requires admin approval before you can access the platform.</p>
    </div>
    <div class="auth-form-side">
        <div class="auth-form-wrap text-center">
            <div class="icon-box icon-box-lg" style="margin:0 auto 16px;background:var(--sunlit-clay);">
                <i data-lucide="clock"></i>
            </div>
            <h1 class="auth-title">Account pending approval</h1>
            <p class="text-sm text-muted" style="margin-top:12px;">
                Thanks, <?= htmlspecialchars((string) $_SESSION['user_name']) ?>.
                Your <?= htmlspecialchars((string) $_SESSION['user_role']) ?> account is being reviewed.
            </p>
            <p class="text-sm text-muted" style="margin-top:8px;">We'll notify you by email once approved.</p>
            <a href="auth/logout.php" class="btn-outline btn-block" style="margin-top:24px;">Log out</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/foot.php'; ?>
