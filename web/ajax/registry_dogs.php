<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/dogs.php';
require_once __DIR__ . '/../includes/breeds.php';
require_login_active();

$pdo = db();
$userRole = current_user_role();
$canRegister = in_array($userRole, ['Dog Owner', 'Admin'], true);

$filters = [
    'q' => trim((string) ($_GET['q'] ?? '')),
    'type' => trim((string) ($_GET['type'] ?? 'all')),
    'barangay' => trim((string) ($_GET['barangay'] ?? 'all')),
    'breed' => trim((string) ($_GET['breed'] ?? 'all')),
    'vaccine' => trim((string) ($_GET['vaccine'] ?? 'all')),
];
$offset = max(0, (int) ($_GET['offset'] ?? 0));
$result = fetch_registry_list($pdo, $filters, $offset, 20);

ob_start();
$rows = $result['rows'];
require __DIR__ . '/../partials/registry-bento-cards.php';
$html = ob_get_clean();

json_response([
    'success' => true,
    'html' => $html,
    'total' => $result['total'],
    'offset' => $offset,
    'has_more' => ($offset + 20) < $result['total'],
    'can_register' => $canRegister,
]);
