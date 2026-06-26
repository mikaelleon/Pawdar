<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/incidents.php';
require_once __DIR__ . '/../partials/incident-cards.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Invalid method'], 405);
}

$filter = trim((string) ($_GET['filter'] ?? 'all'));
$offset = max(0, (int) ($_GET['offset'] ?? 0));
$limit = min(20, max(1, (int) ($_GET['limit'] ?? 10)));

$incidentType = $filter === 'all' ? null : filter_to_incident_type($filter);
if ($filter !== 'all' && $incidentType === null) {
    json_response(['success' => false, 'message' => 'Invalid filter'], 400);
}

$pdo = db();
$barangay = (string) $_SESSION['user_barangay'];
$userId = (int) $_SESSION['user_id'];
$userRole = (string) $_SESSION['user_role'];

$incidents = fetch_incidents($pdo, $barangay, $userId, $incidentType, $offset, $limit);
$counts = fetch_map_counts($pdo, $barangay);
$pins = fetch_map_pins($pdo, $barangay, $incidentType);

$html = render_incident_cards_html($incidents, $userRole, $userId, $filter);
$hasMore = count($incidents) === $limit;

json_response([
    'success' => true,
    'html' => $html,
    'counts' => $counts,
    'pins' => $pins,
    'has_more' => $hasMore,
    'next_offset' => $offset + count($incidents),
]);
