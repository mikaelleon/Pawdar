<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/breeds.php';
require_login_active();

$breedId = (int) ($_GET['breed_id'] ?? 0);
if ($breedId <= 0) {
    json_response(['success' => false, 'breed' => null]);
}

$breed = fetch_breed_by_id(db(), $breedId);

json_response(['success' => $breed !== null, 'breed' => $breed]);
