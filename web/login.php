<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . redirect_after_login((string) ($_SESSION['user_role'] ?? '')));
    exit;
}

$pageTitle = 'Log In · ' . SITE_NAME;
$error = $_GET['error'] ?? '';
$lockSeconds = (int) ($_GET['seconds'] ?? 0);
$hasInlineError = in_array($error, ['invalid', 'missing'], true);
$isLocked = $error === 'locked' || login_lockout_remaining() > 0;
$lockRemaining = login_lockout_remaining() ?: $lockSeconds;
$loginStats = fetch_login_stats(db());
$pageScripts = ['assets/js/ui.js', 'assets/js/auth.js'];
$showPawBackground = false;
require __DIR__ . '/includes/head.php';
?>

<div class="auth-page">
    <?php require __DIR__ . '/includes/auth-theme-toggle.php'; ?>
    <div class="auth-panel auth-desktop-only auth-panel-pattern">
        <a href="index.html" class="flex items-center gap-sm auth-panel-logo">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>

        <div class="auth-panel-copy">
            <h1 class="auth-panel-title">Welcome back.</h1>
            <p class="auth-panel-sub">Pick up where you left off — your cases and dogs are waiting.</p>
        </div>

        <div class="auth-stat-strip" aria-label="Community impact">
            <span class="auth-stat-item">
                <i data-lucide="paw-print" aria-hidden="true"></i>
                <strong><?= (int) $loginStats['dogs'] ?>+</strong> dogs registered
            </span>
            <span class="auth-stat-sep" aria-hidden="true">·</span>
            <span class="auth-stat-item">
                <strong><?= (int) $loginStats['barangays'] ?></strong> barangays connected
            </span>
            <span class="auth-stat-sep" aria-hidden="true">·</span>
            <span class="auth-stat-item">
                <strong><?= (int) $loginStats['resolved'] ?></strong> incidents resolved
            </span>
        </div>
    </div>

    <div class="auth-form-side auth-form-side-padded">
        <div class="auth-mobile-header hidden-desktop">
            <a href="index.html" aria-label="Back to home"><i data-lucide="arrow-left"></i></a>
            <div class="flex-1 text-center auth-mobile-title">Log In</div>
        </div>

        <div class="auth-form-wrap">
            <div class="hidden-desktop auth-mobile-brand">
                <div class="logo-mark auth-mobile-logo"><i data-lucide="paw-print"></i></div>
                <h1 class="auth-title auth-mobile-headline">Welcome back</h1>
                <p class="text-sm text-muted">Log in to your Pawdar account</p>
            </div>

            <h1 class="auth-title auth-desktop-only">Log in to Pawdar</h1>
            <p class="auth-signup-line auth-desktop-only">
                Don't have an account?
                <a href="signup.php" class="auth-signup-link">Sign up</a>
            </p>

            <div class="auth-form-alert" data-login-alert aria-live="polite" <?= $hasInlineError || $isLocked ? '' : 'hidden' ?>>
                <?php if ($isLocked): ?>
                    <p class="field-error">Too many attempts. Try again in <?= (int) ceil($lockRemaining / 60) ?> min.</p>
                <?php endif; ?>
            </div>

            <form id="login-form"
                  action="auth/login-handler.php"
                  method="post"
                  class="auth-login-form"
                  data-login-error="<?= $hasInlineError ? '1' : '0' ?>"
                  data-login-error-type="<?= htmlspecialchars($error) ?>"
                  <?= $isLocked ? 'data-locked="1"' : '' ?>
                  novalidate>
                <input type="hidden" name="ajax" value="1">

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

                <div class="auth-forgot-row">
                    <a href="forgot_password.php" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-primary btn-block auth-submit-btn" data-login-submit <?= $isLocked ? 'disabled' : '' ?>>
                    Log In
                </button>
            </form>

            <p class="auth-role-note">Logging in as a Vet or LGU Official? You'll see your dashboard automatically.</p>

            <nav class="auth-footer-links" aria-label="Legal and help">
                <a href="index.html">Terms</a>
                <span aria-hidden="true">·</span>
                <a href="index.html#about">Privacy</a>
                <span aria-hidden="true">·</span>
                <a href="index.html#how-it-works">Help</a>
            </nav>

            <div class="auth-demo-fallback">
                <button type="button" class="auth-demo-toggle" data-demo-toggle aria-expanded="false">For evaluation purposes: Demo access</button>
                <div class="auth-demo-panel text-xs" hidden>
                    maria.santos@email.com / password<br>
                    luis.cruz@email.com / password (LGU)
                </div>
            </div>

            <p class="auth-signup-line hidden-desktop">
                Don't have an account?
                <a href="signup.php" class="auth-signup-link">Sign up</a>
            </p>
        </div>
    </div>
</div>

<div class="toast-container" data-toast-container aria-live="polite"></div>
<?php require __DIR__ . '/includes/foot.php'; ?>
