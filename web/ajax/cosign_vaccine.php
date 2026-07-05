<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
require_role(['Veterinarian']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid method'], 405);
}

if (!validate_csrf(request_csrf_token())) {
    json_response(['success' => false, 'message' => 'Invalid CSRF token'], 403);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
$vaccineId = (int) ($input['vaccine_id'] ?? 0);

if ($vaccineId <= 0) {
    json_response(['success' => false, 'message' => 'Invalid vaccine record'], 400);
}

$pdo = db();
$vetId = (int) $_SESSION['user_id'];
$vetName = (string) ($_SESSION['user_name'] ?? 'Veterinarian');

$stmt = $pdo->prepare('
    UPDATE vaccinerecord
    SET VetName = :vet_name, vax_status = \'Verified\'
    WHERE VaccineID = :vaccine_id
');
$stmt->execute([
    ':vet_name' => $vetName,
    ':vaccine_id' => $vaccineId,
]);

if ($stmt->rowCount() === 0) {
    json_response(['success' => false, 'message' => 'Vaccine record not found.'], 404);
}

$ownerStmt = $pdo->prepare('
    SELECT d.dog_id, d.DogName, d.UserID AS owner_id
    FROM dog d
    INNER JOIN vaccinerecord v ON v.dog_id = d.dog_id
    WHERE v.VaccineID = :vaccine_id
    LIMIT 1
');
$ownerStmt->execute([':vaccine_id' => $vaccineId]);
$dog = $ownerStmt->fetch();

if ($dog && (int) $dog['owner_id'] > 0) {
    $notif = $pdo->prepare('
        INSERT INTO notifications (user_id, message, link, notification_type)
        VALUES (:uid, :msg, :link, \'vaccine\')
    ');
    $notif->execute([
        ':uid' => (int) $dog['owner_id'],
        ':msg' => 'Dr. ' . $vetName . ' verified ' . $dog['DogName'] . '\'s vaccination record.',
        ':link' => 'dog-profile.php?id=' . (int) $dog['dog_id'],
    ]);
}

json_response(['success' => true, 'message' => 'Vaccination record co-signed.']);
