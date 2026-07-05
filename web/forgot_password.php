<?php

require_once __DIR__ . '/includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header('Location: feed.php');
    exit;
}

$message = '';
$error = '';
$devResetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));

    if ($email === '') {
        $error = 'Please enter your email address.';
    } else {
        $message = 'If an account exists with that email, we\'ve sent password reset instructions.';

        try {
            $pdo = db();
            $stmt = $pdo->prepare('SELECT UserID, Name, Email FROM user WHERE Email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $update = $pdo->prepare('
                    UPDATE user SET reset_token = :token, reset_token_expires = :expires
                    WHERE UserID = :id
                ');
                $update->execute([
                    ':token' => $token,
                    ':expires' => $expires,
                    ':id' => (int) $user['UserID'],
                ]);

                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
                $resetLink = $scheme . '://' . $host . $basePath . '/reset_password.php?token=' . urlencode($token);

                $subject = 'Pawdar Password Reset';
                $body = "Hi {$user['Name']},\n\n"
                    . "We received a request to reset your Pawdar password. "
                    . "Use the link below to set a new password. This link expires in 1 hour.\n\n"
                    . $resetLink . "\n\n"
                    . "If you didn't request this, you can safely ignore this email.";

                $sent = @mail(
                    (string) $user['Email'],
                    $subject,
                    $body,
                    'From: no-reply@pawdar.local'
                );

                if (!$sent && preg_match('/localhost|127\.0\.0\.1/i', $host)) {
                    $devResetLink = $resetLink;
                }
            }
        } catch (Throwable $exception) {
            error_log('forgot_password.php: ' . $exception->getMessage());
        }
    }
}

$pageTitle = 'Forgot Password · ' . SITE_NAME;
$pageScripts = ['assets/js/ui.js'];
require __DIR__ . '/includes/head.php';
?>

<div class="auth-page">
    <div class="auth-panel auth-desktop-only auth-panel-pattern">
        <a href="index.php" class="flex items-center gap-sm">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>
        <h1 class="auth-panel-title">Reset your password.</h1>
        <p class="auth-panel-sub">We'll send you a link to get back into your account.</p>
    </div>

    <div class="auth-form-side auth-form-side-padded">
        <div class="auth-form-wrap">
            <h1 class="auth-title">Forgot password</h1>
            <p class="text-sm text-muted"><a href="login.php" class="link-hover">Back to log in</a></p>

            <?php if ($message !== ''): ?>
                <p class="text-sm" style="margin-top:16px;color:var(--muted-teal);font-weight:600;"><?= htmlspecialchars($message) ?></p>
                <?php if ($devResetLink !== ''): ?>
                    <p class="text-xs text-muted" style="margin-top:12px;">
                        Local dev reset link:
                        <a href="<?= htmlspecialchars($devResetLink) ?>" class="link-hover">Open reset page</a>
                    </p>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <p class="field-error" style="margin-top:16px;"><span aria-hidden="true">✕</span><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($message === ''): ?>
                <form method="post" style="margin-top:26px;">
                    <div class="float-field">
                        <input class="form-input" type="email" id="email" name="email" required autocomplete="email" placeholder=" ">
                        <label for="email">Email address</label>
                    </div>
                    <button type="submit" class="btn-primary btn-block" style="margin-top:18px;">Send reset link</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/foot.php'; ?>
