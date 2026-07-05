<?php

require_once __DIR__ . '/../includes/bootstrap.php';

$token = trim((string) ($_GET['token'] ?? ''));
if ($token === '') {
    header('Location: ../verify.php?error=invalid');
    exit;
}

$user = verify_email_by_token(db(), $token);
if (!$user) {
    header('Location: ../verify.php?error=invalid');
    exit;
}

login_user($user);
header('Location: ../email_verified.php');
exit;
