<?php

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$userId = (int) $_SESSION['user_id'];
$email = '';
$name = (string) ($_SESSION['user_name'] ?? '');
$resent = false;
$resendError = '';
$sendError = (string) ($_GET['send_error'] ?? '');

try {
    $stmt = db()->prepare('SELECT Email, Name, email_verified_at FROM user WHERE UserID = :id LIMIT 1');
    $stmt->execute([':id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $email = (string) $row['Email'];
        $name = (string) $row['Name'];
        if (!empty($row['email_verified_at'])) {
            $_SESSION['email_verified'] = true;
            header('Location: ' . redirect_after_signup([
                'Status' => $_SESSION['user_status'] ?? 'active',
                'email_verified_at' => $row['email_verified_at'],
                'Role' => $_SESSION['user_role'] ?? '',
            ]));
            exit;
        }
    }
} catch (PDOException) {
    header('Location: signup.php?error=db');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend'])) {
    if (!can_resend_verification_email()) {
        $resendError = 'Please wait a minute before requesting another email.';
    } elseif (!pawdar_resend_configured()) {
        $resendError = 'Email delivery is not configured. Contact your administrator.';
    } else {
        $sent = send_email_verification(db(), $userId, $email, $name);
        if ($sent) {
            mark_verification_email_sent();
            $resent = true;
        } else {
            $resendError = 'Could not send the email. Try again in a few minutes.';
        }
    }
}

$pageTitle = 'Verify Email · ' . SITE_NAME;
$error = (string) ($_GET['error'] ?? '');
$pageScripts = ['assets/js/ui.js'];
require __DIR__ . '/includes/head.php';

$stepLabels = [1 => 'Account', 2 => 'Role & location', 3 => 'Verify'];
$resendConfigured = pawdar_resend_configured();
?>

<div class="auth-page">
    <div class="auth-panel auth-desktop-only auth-panel-pattern">
        <a href="index.php" class="flex items-center gap-sm auth-panel-logo">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>
        <div class="auth-panel-copy">
            <h1 class="auth-panel-title">Almost there.</h1>
            <p class="auth-panel-sub">Confirm your email to finish setting up your Pawdar account.</p>
        </div>
    </div>

    <div class="auth-form-side auth-form-side-padded">
        <div class="auth-form-wrap signup-form-wrap">
            <nav class="register-stepper signup-stepper" aria-label="Sign up progress" aria-live="polite">
                <?php $index = 0; foreach ($stepLabels as $num => $label): ?>
                    <?php if ($index > 0): ?><div class="register-step-connector is-done" aria-hidden="true"></div><?php endif; ?>
                    <div class="register-step<?= $num === 3 ? ' is-active' : ' is-done' ?>">
                        <span class="register-step-circle"><?= $num === 3 ? '3' : '✓' ?></span>
                        <span class="register-step-label"><?= htmlspecialchars($label) ?></span>
                    </div>
                    <?php $index++; endforeach; ?>
            </nav>

            <div class="signup-verify-panel">
                <div class="icon-box icon-box-lg" style="margin:0 0 16px;background:var(--tea-green);">
                    <i data-lucide="mail"></i>
                </div>
                <h1 class="auth-title">Check your email</h1>
                <p class="text-sm text-muted" style="margin-top:12px;">
                    We sent a confirmation link to <strong><?= htmlspecialchars($email) ?></strong>.
                    Open it within 24 hours to activate your account.
                </p>

                <?php if (!$resendConfigured): ?>
                    <p class="field-hint field-hint--warning" role="status" style="margin-top:12px;">
                        Email delivery is not configured. Add <code>RESEND_API_KEY</code> to the project <code>.env</code> file.
                        See <code>docs/EMAIL_SETUP.md</code>.
                    </p>
                <?php endif; ?>

                <?php if ($sendError === '1'): ?>
                    <p class="field-error" role="alert">Your account was created, but we could not send the verification email. Use resend below.</p>
                <?php endif; ?>

                <?php if ($error === 'invalid'): ?>
                    <p class="field-error" role="alert">That verification link is invalid or expired. Request a new one below.</p>
                <?php endif; ?>

                <?php if ($resent): ?>
                    <p class="field-hint field-hint--success" role="status">Verification email sent again.</p>
                <?php endif; ?>

                <?php if ($resendError !== ''): ?>
                    <p class="field-error" role="alert"><?= htmlspecialchars($resendError) ?></p>
                <?php endif; ?>

                <p class="text-sm text-muted signup-privacy-note" style="margin-top:12px;">
                    Pawdar processes your data in compliance with the Philippine Data Privacy Act (RA 10173).
                </p>

                <form method="post" style="margin-top:20px;">
                    <button type="submit" name="resend" value="1" class="btn-outline btn-block" <?= $resendConfigured ? '' : 'disabled' ?>>
                        Resend confirmation email
                    </button>
                </form>
                <a href="auth/logout.php" class="btn-ghost btn-block" style="margin-top:10px;text-align:center;">Use a different email</a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/foot.php'; ?>
