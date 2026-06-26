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
 * Requires login and active account status.
 */
function require_login_active(): void
{
    require_login();
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
