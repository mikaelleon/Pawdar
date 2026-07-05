<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/locations.php';

header('Content-Type: application/json; charset=utf-8');

$cityId = (int) ($_GET['city_id'] ?? 0);
if ($cityId <= 0) {
    echo json_encode(['barangays' => []]);
    exit;
}

$barangays = fetch_barangays_by_city(db(), $cityId);
echo json_encode(['barangays' => $barangays]);
