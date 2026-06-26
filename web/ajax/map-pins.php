<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/incidents.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Invalid method'], 405);
}

$filter = trim((string) ($_GET['filter'] ?? 'all'));
$incidentType = $filter === 'all' ? null : filter_to_incident_type($filter);

$pdo = db();
$barangay = (string) $_SESSION['user_barangay'];
$pins = fetch_map_pins($pdo, $barangay, $incidentType);
$counts = fetch_map_counts($pdo, $barangay);

json_response([
    'success' => true,
    'pins' => $pins,
    'counts' => $counts,
]);
