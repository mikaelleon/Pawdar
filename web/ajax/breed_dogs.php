<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/dogs.php';
require_login_active();

$breedId = (int) ($_GET['breed_id'] ?? 0);
if ($breedId <= 0) {
    json_response(['success' => false, 'dogs' => []]);
}

$pdo = db();
$dogs = fetch_dogs_by_breed_id($pdo, $breedId);

json_response(['success' => true, 'dogs' => $dogs]);
