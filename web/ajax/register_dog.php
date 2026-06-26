<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/dogs.php';
require_once __DIR__ . '/../includes/breeds.php';
require_role(['Dog Owner', 'Admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register_dog.php');
    exit;
}

if (!validate_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    header('Location: ../register_dog.php?error=csrf');
    exit;
}

$pdo = db();
$userId = (int) $_SESSION['user_id'];
$dogName = trim((string) ($_POST['dog_name'] ?? ''));
$breedName = trim((string) ($_POST['breed_search'] ?? ''));
$gender = trim((string) ($_POST['gender'] ?? 'Male'));
$age = (int) ($_POST['age'] ?? 0);
$dogType = trim((string) ($_POST['dog_type'] ?? 'Owned'));
$healthNotes = trim((string) ($_POST['health_notes'] ?? ''));

if ($dogName === '' || $breedName === '') {
    header('Location: ../register_dog.php?step=1&error=missing');
    exit;
}

$breedId = null;
$breedStmt = $pdo->prepare('SELECT breed_id FROM breeds WHERE breed_name = :name LIMIT 1');
$breedStmt->execute([':name' => $breedName]);
$breedRow = $breedStmt->fetch();
if ($breedRow) {
    $breedId = (int) $breedRow['breed_id'];
}

$photoPath = null;
if (!empty($_FILES['photo']['tmp_name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
    $uploadDir = dirname(__DIR__) . '/uploads/dogs';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext = pathinfo((string) $_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = 'dog_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext ?: 'jpg');
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . '/' . $filename)) {
        $photoPath = 'uploads/dogs/' . $filename;
    }
}

$insert = $pdo->prepare('
    INSERT INTO dog (UserID, DogName, Breed, breed_id, Gender, Age, DogType, Status, health_notes, photo_path)
    VALUES (:user_id, :name, :breed, :breed_id, :gender, :age, :dog_type, \'Pending\', :notes, :photo)
');
$insert->execute([
    ':user_id' => $userId,
    ':name' => $dogName,
    ':breed' => $breedName,
    ':breed_id' => $breedId,
    ':gender' => $gender,
    ':age' => $age > 0 ? $age : null,
    ':dog_type' => $dogType,
    ':notes' => $healthNotes !== '' ? $healthNotes : null,
    ':photo' => $photoPath,
]);

$dogId = (int) $pdo->lastInsertId();
$registryId = 'PWD-' . date('Y') . '-' . str_pad((string) $dogId, 5, '0', STR_PAD_LEFT);
$pdo->prepare('UPDATE dog SET RegistryID = :rid WHERE dog_id = :id')->execute([':rid' => $registryId, ':id' => $dogId]);

$vaccineName = trim((string) ($_POST['vaccine_name'] ?? ''));
$vaccineDate = trim((string) ($_POST['vaccine_date'] ?? ''));
if ($vaccineName !== '' && $vaccineDate !== '') {
    $pdo->prepare('
        INSERT INTO vaccinerecord (dog_id, VaccineName, DateGiven, NextDueDate, VetName, vax_status)
        VALUES (:dog_id, :name, :given, :due, :vet, \'Unverified\')
    ')->execute([
        ':dog_id' => $dogId,
        ':name' => $vaccineName,
        ':given' => $vaccineDate,
        ':due' => trim((string) ($_POST['vaccine_due'] ?? '')) ?: null,
        ':vet' => trim((string) ($_POST['vet_name'] ?? '')) ?: null,
    ]);
}

header('Location: ../register_dog.php?success=1&dog_id=' . $dogId);
exit;
