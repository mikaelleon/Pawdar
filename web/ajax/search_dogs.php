<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_login_active();

$query = trim((string) ($_GET['q'] ?? ''));
if (strlen($query) < 2) {
    json_response(['success' => true, 'dogs' => []]);
}

$pdo = db();
$userId = (int) $_SESSION['user_id'];
$stmt = $pdo->prepare('
    SELECT d.dog_id, d.DogName, d.Breed, d.RegistryID
    FROM dog d
    WHERE d.UserID = :user_id
      AND (d.DogName LIKE :q OR d.RegistryID LIKE :q)
    ORDER BY d.DogName ASC
    LIMIT 8
');
$stmt->execute([
    ':user_id' => $userId,
    ':q' => '%' . $query . '%',
]);

$dogs = [];
foreach ($stmt->fetchAll() as $row) {
    $dogs[] = [
        'id' => (int) $row['dog_id'],
        'name' => $row['DogName'],
        'breed' => $row['Breed'],
        'registry_id' => $row['RegistryID'],
    ];
}

json_response(['success' => true, 'dogs' => $dogs]);
