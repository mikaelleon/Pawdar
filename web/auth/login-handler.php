<?php

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$remaining = login_lockout_remaining();
if ($remaining > 0) {
    header('Location: ../login.php?error=locked&seconds=' . $remaining);
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    header('Location: ../login.php?error=missing');
    exit;
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT UserID, Name, Email, Password, Role, Barangay, Status FROM user WHERE Email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
} catch (PDOException $exception) {
    header('Location: ../login.php?error=db');
    exit;
}

if (!$user || !password_verify($password, (string) $user['Password'])) {
    record_failed_login();
    header('Location: ../login.php?error=invalid');
    exit;
}

clear_login_attempts();
login_user($user);

if (($user['Status'] ?? 'active') === 'pending') {
    header('Location: ../pending.php');
    exit;
}

header('Location: ../' . redirect_after_login((string) $user['Role']));
exit;
