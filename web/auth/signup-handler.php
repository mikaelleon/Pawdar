<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/locations.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../signup.php');
    exit;
}

$lastName = trim((string) ($_POST['last_name'] ?? ''));
$firstName = trim((string) ($_POST['first_name'] ?? ''));
$middleName = trim((string) ($_POST['middle_name'] ?? ''));
$nameSuffix = trim((string) ($_POST['name_suffix'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phoneLocal = preg_replace('/\D+/', '', trim((string) ($_POST['phone_local'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
$passwordConfirm = (string) ($_POST['password_confirm'] ?? '');
$role = trim((string) ($_POST['role'] ?? 'Community Reporter'));
$cityId = (int) ($_POST['city_id'] ?? 0);
$barangayId = (int) ($_POST['barangay_id'] ?? 0);
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

if ($lastName === '' || $firstName === '' || $email === '' || $password === '' || $cityId <= 0 || $barangayId <= 0) {
    header('Location: ../signup.php?error=missing');
    exit;
}

if ($password !== $passwordConfirm) {
    header('Location: ../signup.php?error=password');
    exit;
}

if (!in_array($role, $allowedRoles, true)) {
    $role = 'Community Reporter';
}

$phone = '';
if ($phoneLocal !== '') {
    if (strlen($phoneLocal) === 10 && str_starts_with($phoneLocal, '9')) {
        $phone = '+63' . $phoneLocal;
    } elseif (strlen($phoneLocal) === 11 && str_starts_with($phoneLocal, '09')) {
        $phone = '+63' . substr($phoneLocal, 1);
    } else {
        header('Location: ../signup.php?error=phone');
        exit;
    }
}

    if (in_array($role, roles_requiring_phone(), true) && $phone === '') {
        header('Location: ../signup.php?error=phone');
        exit;
    }

    $status = in_array($role, roles_requiring_approval(), true) ? 'pending' : 'active';
$displayName = build_user_display_name($lastName, $firstName, $middleName, $nameSuffix);

try {
    $pdo = db();

    $location = fetch_location_by_barangay_id($pdo, $barangayId);
    if (!$location || (int) $location['city_id'] !== $cityId) {
        header('Location: ../signup.php?error=location');
        exit;
    }

    $check = $pdo->prepare('SELECT UserID FROM user WHERE Email = :email LIMIT 1');
    $check->execute([':email' => $email]);
    if ($check->fetch()) {
        header('Location: ../signup.php?error=exists');
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $insert = $pdo->prepare('
        INSERT INTO user (
            Name, last_name, first_name, middle_name, name_suffix,
            Email, Password, Role, Status, Barangay, City, city_id, barangay_id, Phone
        ) VALUES (
            :name, :last_name, :first_name, :middle_name, :name_suffix,
            :email, :password, :role, :status, :barangay, :city, :city_id, :barangay_id, :phone
        )
    ');
    $insert->execute([
        ':name' => $displayName,
        ':last_name' => $lastName,
        ':first_name' => $firstName,
        ':middle_name' => $middleName !== '' ? $middleName : null,
        ':name_suffix' => $nameSuffix !== '' ? $nameSuffix : null,
        ':email' => $email,
        ':password' => $hash,
        ':role' => $role,
        ':status' => $status,
        ':barangay' => $location['barangay_name'],
        ':city' => $location['name'],
        ':city_id' => $cityId,
        ':barangay_id' => $barangayId,
        ':phone' => $phone !== '' ? $phone : null,
    ]);

    $userId = (int) $pdo->lastInsertId();
    $sent = send_email_verification($pdo, $userId, $email, $firstName);
    if ($sent) {
        mark_verification_email_sent();
    }

    $userRow = [
        'UserID' => $userId,
        'Name' => $displayName,
        'Role' => $role,
        'Barangay' => $location['barangay_name'],
        'Status' => $status,
        'email_verified_at' => null,
    ];
    login_user($userRow);
} catch (PDOException) {
    header('Location: ../signup.php?error=db');
    exit;
}

header('Location: ../verify.php' . ($sent ? '' : '?send_error=1'));
exit;
