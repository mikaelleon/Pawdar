<?php

require_once __DIR__ . '/includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header('Location: feed.php');
    exit;
}

$token = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$error = '';
$success = false;
$user = null;

if ($token !== '') {
    try {
        $pdo = db();
        $stmt = $pdo->prepare('
            SELECT UserID FROM user
            WHERE reset_token = :token AND reset_token_expires > NOW()
            LIMIT 1
        ');
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch() ?: null;
    } catch (Throwable $exception) {
        error_log('reset_password.php: ' . $exception->getMessage());
    }
}

if (!$user && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $error = 'This password reset link is invalid or has expired. Please request a new one.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $pdo = db();
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $update = $pdo->prepare('
                UPDATE user SET Password = :password, reset_token = NULL, reset_token_expires = NULL
                WHERE UserID = :id
            ');
            $update->execute([
                ':password' => $hashed,
                ':id' => (int) $user['UserID'],
            ]);
            $success = true;
        } catch (Throwable $exception) {
            error_log('reset_password.php update: ' . $exception->getMessage());
            $error = 'Something went wrong. Please try again.';
        }
    }
}

$pageTitle = 'Reset Password · ' . SITE_NAME;
$pageScripts = ['assets/js/ui.js'];
require __DIR__ . '/includes/head.php';
?>

<div class="auth-page">
    <div class="auth-panel auth-desktop-only auth-panel-pattern">
        <a href="index.php" class="flex items-center gap-sm">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>
        <h1 class="auth-panel-title">Set a new password.</h1>
    </div>

    <div class="auth-form-side auth-form-side-padded">
        <div class="auth-form-wrap">
            <h1 class="auth-title">Reset password</h1>

            <?php if ($error !== ''): ?>
                <p class="field-error" style="margin-top:16px;"><span aria-hidden="true">✕</span><?= htmlspecialchars($error) ?></p>
                <p class="text-sm text-muted" style="margin-top:12px;"><a href="forgot_password.php" class="link-hover">Request a new link</a></p>
            <?php elseif ($success): ?>
                <p class="text-sm" style="margin-top:16px;color:var(--muted-teal);font-weight:600;">Your password has been updated. You can now log in.</p>
                <a href="login.php" class="btn-primary btn-block" style="margin-top:18px;">Go to log in</a>
            <?php else: ?>
                <form method="post" style="margin-top:26px;">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="float-field float-field-password">
                        <input class="form-input" type="password" id="password" name="password" required minlength="8" autocomplete="new-password" placeholder=" ">
                        <label for="password">New password</label>
                    </div>
                    <div class="float-field float-field-password" style="margin-top:14px;">
                        <input class="form-input" type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password" placeholder=" ">
                        <label for="confirm_password">Confirm new password</label>
                    </div>
                    <button type="submit" class="btn-primary btn-block" style="margin-top:18px;">Update password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/foot.php'; ?>
