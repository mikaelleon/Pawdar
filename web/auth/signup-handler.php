<?php

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../signup.php');
    exit;
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = preg_replace('/\s+/', '', trim((string) ($_POST['phone'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
$passwordConfirm = (string) ($_POST['password_confirm'] ?? '');
$role = trim((string) ($_POST['role'] ?? 'Community Reporter'));
$barangay = trim((string) ($_POST['barangay'] ?? ''));
$termsAccepted = !empty($_POST['terms']);

$allowedRoles = [
    'Dog Owner',
    'Community Reporter',
    'Veterinarian',
    'LGU Official',
    'Rescue Organization',
];

if (!$termsAccepted) {
    header('Location: ../signup.php?error=terms');
    exit;
}

if ($name === '' || $email === '' || $password === '' || $barangay === '') {
    header('Location: ../signup.php?error=missing');
    exit;
}

if ($phone !== '' && !preg_match('/^(\+639|09)\d{9}$/', $phone)) {
    header('Location: ../signup.php?error=phone');
    exit;
}

if ($password !== $passwordConfirm) {
    header('Location: ../signup.php?error=password');
    exit;
}

if (!in_array($role, $allowedRoles, true)) {
    $role = 'Community Reporter';
}

$status = in_array($role, roles_requiring_approval(), true) ? 'pending' : 'active';

try {
    $pdo = db();
    $check = $pdo->prepare('SELECT UserID FROM user WHERE Email = :email LIMIT 1');
    $check->execute([':email' => $email]);

    if ($check->fetch()) {
        header('Location: ../signup.php?error=exists');
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $insert = $pdo->prepare('
        INSERT INTO user (Name, Email, Password, Role, Status, Barangay, Phone)
        VALUES (:name, :email, :password, :role, :status, :barangay, :phone)
    ');
    $insert->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $hash,
        ':role' => $role,
        ':status' => $status,
        ':barangay' => $barangay,
        ':phone' => $phone !== '' ? $phone : null,
    ]);

    $userId = (int) $pdo->lastInsertId();

    $subject = 'Welcome to Pawdar';
    $body = "Hi {$name},\n\nYour Pawdar account was created as {$role}.";
    if ($status === 'pending') {
        $body .= "\n\nYour account is pending admin approval. We'll email you when it's active.";
    } else {
        $body .= "\n\nYou can log in now at Pawdar.";
    }
    @mail($email, $subject, $body, 'From: noreply@pawdar.local');

    login_user([
        'UserID' => $userId,
        'Name' => $name,
        'Role' => $role,
        'Barangay' => $barangay,
        'Status' => $status,
    ]);
} catch (PDOException $exception) {
    header('Location: ../signup.php?error=db');
    exit;
}

if ($status === 'pending') {
    header('Location: ../pending.php');
    exit;
}

header('Location: ../feed.php');
exit;
