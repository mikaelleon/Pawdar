<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/dogs.php';
require_once __DIR__ . '/../includes/breed-media.php';

require_login();
require_role(['Dog Owner', 'Admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid method'], 405);
}

if (!validate_csrf(request_csrf_token())) {
    json_response(['success' => false, 'message' => 'Invalid CSRF token'], 403);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
if (!is_array($input)) {
    json_response(['success' => false, 'message' => 'Invalid request'], 400);
}

$dogId = (int) ($input['dog_id'] ?? 0);
$dogName = trim((string) ($input['dog_name'] ?? ''));
$gender = trim((string) ($input['gender'] ?? ''));
$age = (int) ($input['age'] ?? 0);
$coatColor = trim((string) ($input['coat_color'] ?? ''));
$coatColorOther = trim((string) ($input['coat_color_other'] ?? ''));
$weightKg = trim((string) ($input['weight_kg'] ?? ''));
$marks = trim((string) ($input['distinguishing_marks'] ?? ''));
$temperament = trim((string) ($input['temperament_notes'] ?? ''));
$healthNotes = trim((string) ($input['health_notes'] ?? ''));

if ($dogId <= 0 || $dogName === '') {
    json_response(['success' => false, 'message' => 'Dog name is required'], 400);
}

if ($coatColor === 'Other' && $coatColorOther !== '') {
    $coatColor = $coatColorOther;
}

$pdo = db();
$userId = (int) $_SESSION['user_id'];
$userRole = current_user_role();

$dog = fetch_dog_profile($pdo, $dogId);
if (!$dog) {
    json_response(['success' => false, 'message' => 'Dog not found'], 404);
}

if ($userRole !== 'Admin' && (int) $dog['owner_id'] !== $userId) {
    json_response(['success' => false, 'message' => 'You may only edit your own dogs'], 403);
}

$weightValue = null;
if ($weightKg !== '' && is_numeric($weightKg)) {
    $weightValue = round((float) $weightKg, 2);
}

$update = $pdo->prepare('
    UPDATE dog SET
        DogName = :name,
        Gender = :gender,
        Age = :age,
        coat_color = :coat_color,
        weight_kg = :weight_kg,
        distinguishing_marks = :marks,
        temperament_notes = :temperament,
        health_notes = :health_notes
    WHERE dog_id = :dog_id
');
$update->execute([
    ':name' => $dogName,
    ':gender' => $gender !== '' ? $gender : null,
    ':age' => $age > 0 ? $age : null,
    ':coat_color' => $coatColor !== '' ? $coatColor : null,
    ':weight_kg' => $weightValue,
    ':marks' => $marks !== '' ? $marks : null,
    ':temperament' => $temperament !== '' ? $temperament : null,
    ':health_notes' => $healthNotes !== '' ? $healthNotes : null,
    ':dog_id' => $dogId,
]);

json_response(['success' => true, 'message' => 'Profile updated']);
