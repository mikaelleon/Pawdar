<?php

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$wantsJson = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')
    || (string) ($_POST['ajax'] ?? '') === '1';

function login_json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

$remaining = login_lockout_remaining();
if ($remaining > 0) {
    if ($wantsJson) {
        login_json_response([
            'success' => false,
            'error' => 'locked',
            'seconds' => $remaining,
            'message' => 'Too many attempts. Try again later.',
        ], 429);
    }

    header('Location: ../login.php?error=locked&seconds=' . $remaining);
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    if ($wantsJson) {
        login_json_response([
            'success' => false,
            'error' => 'missing',
            'message' => 'This field is required.',
        ], 422);
    }

    header('Location: ../login.php?error=missing');
    exit;
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT UserID, Name, Email, Password, Role, Barangay, Status, email_verified_at FROM user WHERE Email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
} catch (PDOException $exception) {
    if ($wantsJson) {
        login_json_response(['success' => false, 'error' => 'db', 'message' => 'Unable to sign in right now.'], 500);
    }

    header('Location: ../login.php?error=db');
    exit;
}

if (!$user || !password_verify($password, (string) $user['Password'])) {
    record_failed_login();

    if ($wantsJson) {
        login_json_response([
            'success' => false,
            'error' => 'invalid',
            'message' => 'Incorrect email or password.',
        ], 401);
    }

    header('Location: ../login.php?error=invalid');
    exit;
}

clear_login_attempts();
login_user($user);

if (empty($user['email_verified_at'])) {
    $redirect = 'verify.php';
} elseif (($user['Status'] ?? 'active') === 'pending') {
    $redirect = 'pending.php';
} else {
    $redirect = redirect_after_login((string) $user['Role']);
}

if ($wantsJson) {
    login_json_response([
        'success' => true,
        'redirect' => $redirect,
    ]);
}

header('Location: ../' . $redirect);
exit;
