<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . redirect_after_login((string) ($_SESSION['user_role'] ?? '')));
    exit;
}

$pageTitle = 'Log In · ' . SITE_NAME;
$error = $_GET['error'] ?? '';
$lockSeconds = (int) ($_GET['seconds'] ?? 0);
$hasInlineError = $error === 'invalid';
$isLocked = $error === 'locked' || login_lockout_remaining() > 0;
$lockRemaining = login_lockout_remaining() ?: $lockSeconds;
$pageScripts = ['assets/js/ui.js', 'assets/js/auth.js'];
require __DIR__ . '/includes/head.php';
?>

<div class="auth-page">
    <div class="auth-panel auth-desktop-only auth-panel-pattern">
        <a href="index.php" class="flex items-center gap-sm">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>
        <h1 class="auth-panel-title">Welcome back.</h1>
        <p class="auth-panel-sub">Pick up where you left off — your cases and dogs are waiting.</p>
        <div class="demo-disclosure">
            <button type="button" class="demo-disclosure-toggle text-xs" data-demo-toggle aria-expanded="false">Demo access</button>
            <div class="demo-disclosure-panel text-xs" hidden>
                maria.santos@email.com / password<br>
                luis.cruz@email.com / password (LGU)
            </div>
        </div>
    </div>

    <div class="auth-form-side auth-form-side-padded">
        <div class="auth-mobile-header hidden-desktop">
            <a href="index.php"><i data-lucide="arrow-left"></i></a>
            <div class="flex-1 text-center" style="font-weight:500;font-size:17px;margin-left:-24px;">Log In</div>
        </div>

        <div class="auth-form-wrap">
            <div class="hidden-desktop text-center" style="margin-bottom:28px;">
                <div class="logo-mark" style="margin:0 auto;width:52px;height:52px;border-radius:15px;"><i data-lucide="paw-print" style="width:28px;height:28px;"></i></div>
                <h1 class="auth-title" style="font-size:24px;margin-top:14px;">Welcome back</h1>
                <p class="text-sm text-muted">Log in to your Pawdar account</p>
            </div>

            <h1 class="auth-title auth-desktop-only">Log in to Pawdar</h1>
            <p class="text-sm text-muted auth-desktop-only">Don't have an account? <a href="signup.php" class="link-hover">Sign up</a></p>

            <?php if ($isLocked): ?>
                <p class="field-error" style="margin-top:16px;">
                    <span aria-hidden="true">✕</span>
                    Too many attempts. Try again in <?= (int) ceil($lockRemaining / 60) ?> min.
                </p>
            <?php endif; ?>

            <form id="login-form"
                  action="auth/login-handler.php"
                  method="post"
                  style="margin-top:26px;"
                  data-login-error="<?= $hasInlineError ? '1' : '0' ?>"
                  <?= $isLocked ? 'data-locked="1"' : '' ?>>
                <div class="float-field">
                    <input class="form-input" type="email" id="email" name="email" required autocomplete="email" placeholder=" ">
                    <label for="email">Email address</label>
                </div>

                <div class="float-field float-field-password">
                    <input class="form-input" type="password" id="password" name="password" required autocomplete="current-password" placeholder=" ">
                    <label for="password">Password</label>
                    <button type="button" class="password-toggle" data-toggle-password="password" aria-label="Show password" aria-pressed="false">
                        <i data-lucide="eye"></i>
                    </button>
                </div>

                <div class="text-right" style="margin-top:8px;">
                    <a href="#" class="text-sm link-hover forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-primary btn-block auth-desktop-only" style="margin-top:18px;" data-login-submit <?= $isLocked ? 'disabled' : '' ?>>Log In</button>
            </form>

            <div class="hidden-desktop auth-mobile-submit">
                <button type="submit" form="login-form" class="btn-primary btn-block" data-login-submit <?= $isLocked ? 'disabled' : '' ?>>Log In</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" data-toast-container aria-live="polite"></div>
<?php require __DIR__ . '/includes/foot.php'; ?>
