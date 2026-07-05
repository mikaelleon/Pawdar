<?php

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

if (empty($_SESSION['email_verified'])) {
    header('Location: verify.php');
    exit;
}

$next = 'feed.php';
if (($_SESSION['user_status'] ?? 'active') === 'pending') {
    $next = 'pending.php?verified=1';
}

$pageTitle = 'Email Verified · ' . SITE_NAME;
require __DIR__ . '/includes/head.php';
?>

<div class="auth-page">
    <div class="auth-panel auth-desktop-only auth-panel-pattern">
        <a href="index.html" class="flex items-center gap-sm auth-panel-logo">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>
        <div class="auth-panel-copy">
            <h1 class="auth-panel-title">You're verified.</h1>
            <p class="auth-panel-sub">Your email is confirmed. Continue to your Pawdar account.</p>
        </div>
    </div>

    <div class="auth-form-side auth-form-side-padded">
        <div class="auth-form-wrap signup-form-wrap text-center">
            <div class="icon-box icon-box-lg" style="margin:0 auto 16px;background:var(--tea-green);">
                <i data-lucide="check"></i>
            </div>
            <h1 class="auth-title">Email verified</h1>
            <p class="text-sm text-muted" style="margin-top:12px;">
                Thanks, <?= htmlspecialchars((string) $_SESSION['user_name']) ?>.
                Your email address is now confirmed.
            </p>
            <?php if (($_SESSION['user_status'] ?? 'active') === 'pending'): ?>
                <p class="text-sm text-muted signup-privacy-note" style="margin-top:12px;text-align:left;">
                    Your <?= htmlspecialchars((string) $_SESSION['user_role']) ?> account still requires admin approval before you can use the platform.
                </p>
            <?php endif; ?>
            <a href="<?= htmlspecialchars($next) ?>" class="btn-primary btn-block" style="margin-top:24px;">
                <?= ($_SESSION['user_status'] ?? 'active') === 'pending' ? 'Continue to approval status' : 'Continue to Pawdar' ?>
            </a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/foot.php'; ?>
