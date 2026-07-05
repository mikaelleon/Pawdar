<?php

/**
 * Redirects unauthenticated users to login.
 */
function require_login(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Requires login and a verified email address.
 */
function require_email_verified(): void
{
    if (empty($_SESSION['email_verified'])) {
        header('Location: verify.php');
        exit;
    }
}

/**
 * Requires login and active account status.
 */
function require_login_active(): void
{
    require_login();
    require_email_verified();
    require_active_account();
}

/**
 * Ensures the current user has one of the allowed roles.
 *
 * @param list<string> $roles
 */
function require_role(array $roles): void
{
    require_login();

    $role = $_SESSION['user_role'] ?? '';
    if (!in_array($role, $roles, true)) {
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }
}

/**
 * Populates session after successful authentication.
 *
 * @param array<string, mixed> $user
 */
function login_user(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $user['UserID'];
    $_SESSION['user_name'] = (string) $user['Name'];
    $_SESSION['user_role'] = (string) $user['Role'];
    $_SESSION['user_initials'] = user_initials_from_name((string) $user['Name']);
    $_SESSION['user_barangay'] = (string) $user['Barangay'];
    $_SESSION['user_status'] = (string) ($user['Status'] ?? 'active');
    $_SESSION['email_verified'] = !empty($user['email_verified_at']) || !empty($user['EmailVerified']);
}

/**
 * Returns post-signup / post-login redirect based on account state.
 */
function redirect_after_signup(array $user): string
{
    $status = (string) ($user['Status'] ?? 'active');
    $verified = !empty($user['email_verified_at']);

    if (!$verified) {
        return 'verify.php';
    }

    if ($status === 'pending') {
        return 'pending.php';
    }

    return redirect_after_login((string) ($user['Role'] ?? ''));
}

/**
 * Issues a new email verification token and sends the message via Resend.
 *
 * @return bool True when the email was accepted by Resend.
 */
function send_email_verification(PDO $pdo, int $userId, string $email, string $name): bool
{
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 86400);

    $stmt = $pdo->prepare('
        UPDATE user
        SET email_verify_token = :token, email_verify_expires = :expires
        WHERE UserID = :id
    ');
    $stmt->execute([
        ':token' => $token,
        ':expires' => $expires,
        ':id' => $userId,
    ]);

    $link = pawdar_app_url('auth/verify-email.php?token=' . urlencode($token));
    $content = pawdar_verification_email_content($name, $link);

    return pawdar_send_email(
        $email,
        'Confirm your Pawdar account',
        $content['html'],
        $content['text']
    );
}

/**
 * Sends a password reset link via Resend.
 *
 * @return bool True when the email was accepted by Resend.
 */
function send_password_reset_email(string $email, string $name, string $resetUrl): bool
{
    $content = pawdar_password_reset_email_content($name, $resetUrl);

    return pawdar_send_email(
        $email,
        'Reset your Pawdar password',
        $content['html'],
        $content['text']
    );
}

/**
 * Returns true if the current user may request another verification email (60s cooldown).
 */
function can_resend_verification_email(): bool
{
    $lastSent = (int) ($_SESSION['verify_email_last_sent'] ?? 0);

    return (time() - $lastSent) >= 60;
}

/**
 * Records a verification resend attempt timestamp.
 */
function mark_verification_email_sent(): void
{
    $_SESSION['verify_email_last_sent'] = time();
}

/**
 * @return array<string, mixed>|null
 */
function verify_email_by_token(PDO $pdo, string $token): ?array
{
    $token = trim($token);
    if ($token === '') {
        return null;
    }

    $stmt = $pdo->prepare('
        SELECT UserID, Name, Email, Role, Barangay, Status, email_verified_at
        FROM user
        WHERE email_verify_token = :token
          AND email_verify_expires > NOW()
        LIMIT 1
    ');
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        return null;
    }

    $update = $pdo->prepare('
        UPDATE user
        SET email_verified_at = NOW(), email_verify_token = NULL, email_verify_expires = NULL
        WHERE UserID = :id
    ');
    $update->execute([':id' => (int) $user['UserID']]);
    $user['email_verified_at'] = date('Y-m-d H:i:s');

    return $user;
}

/**
 * Returns post-login redirect URL based on role.
 */
function redirect_after_login(string $role): string
{
    $map = [
        'Veterinarian' => 'registry.php',
        'LGU Official' => 'cases.php',
        'Rescue Organization' => 'rescue.php',
        'Admin' => 'admin.php',
    ];

    return $map[$role] ?? 'feed.php';
}

/**
 * Roles that require admin approval before platform access.
 *
 * @return list<string>
 */
function roles_requiring_approval(): array
{
    return ['Veterinarian', 'LGU Official', 'Rescue Organization'];
}

/**
 * Blocks pending users from accessing the app.
 */
function require_active_account(): void
{
    require_login();

    if (($_SESSION['user_status'] ?? 'active') === 'pending') {
        header('Location: pending.php');
        exit;
    }
}

/**
 * Tracks failed login attempts by IP.
 */
function record_failed_login(): void
{
    $key = login_attempt_key();
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'first_at' => time(), 'locked_until' => 0];
    }

    $_SESSION[$key]['count']++;
    if ($_SESSION[$key]['count'] >= 5) {
        $_SESSION[$key]['locked_until'] = time() + 300;
    }
}

/**
 * Returns lockout seconds remaining, or 0 if not locked.
 */
function login_lockout_remaining(): int
{
    $key = login_attempt_key();
    $lockedUntil = (int) ($_SESSION[$key]['locked_until'] ?? 0);
    $remaining = $lockedUntil - time();

    if ($remaining > 0) {
        return $remaining;
    }

    if (isset($_SESSION[$key]) && (time() - (int) $_SESSION[$key]['first_at']) > 600) {
        unset($_SESSION[$key]);
    }

    return 0;
}

/**
 * Clears failed login attempts after success.
 */
function clear_login_attempts(): void
{
    unset($_SESSION[login_attempt_key()]);
}

/**
 * @return string
 */
function login_attempt_key(): string
{
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');

    return 'login_attempts_' . md5($ip);
}

/**
 * Clears authentication session data.
 */
function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

/**
 * Returns current user id or null.
 */
function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

/**
 * Returns current user role or empty string.
 */
function current_user_role(): string
{
    return (string) ($_SESSION['user_role'] ?? '');
}
