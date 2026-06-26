<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/incidents.php';
require_login_active();

$filter = trim((string) ($_GET['filter'] ?? 'all'));
$range = trim((string) ($_GET['range'] ?? 'month'));
$incidentType = $filter === 'all' ? null : filter_to_incident_type($filter);
if ($incidentType === null && $filter !== 'all') {
    $map = incident_type_map();
    foreach ($map as $type => $meta) {
        if ($meta['filter'] === $filter) {
            $incidentType = $type;
            break;
        }
    }
}

$dateFrom = match ($range) {
    'today' => date('Y-m-d 00:00:00'),
    'week' => date('Y-m-d 00:00:00', strtotime('-7 days')),
    'month' => date('Y-m-d 00:00:00', strtotime('-30 days')),
    default => null,
};

$pdo = db();
$barangay = (string) $_SESSION['user_barangay'];
$incidents = fetch_map_incidents($pdo, $barangay, $incidentType, $dateFrom);
$counts = fetch_map_counts($pdo, $barangay);

json_response([
    'success' => true,
    'incidents' => $incidents,
    'counts' => $counts,
    'total' => count($incidents),
]);
