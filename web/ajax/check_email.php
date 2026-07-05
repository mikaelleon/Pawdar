<?php

require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json');

$email = trim((string) ($_GET['email'] ?? ''));
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['exists' => false, 'valid' => false]);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT UserID FROM `user` WHERE Email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    json_response(['exists' => (bool) $stmt->fetch(), 'valid' => true]);
} catch (PDOException $exception) {
    json_response(['exists' => false, 'valid' => true]);
}
