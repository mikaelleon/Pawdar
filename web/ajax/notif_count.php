<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/incidents.php';
require_login_active();

json_response([
    'success' => true,
    'count' => fetch_unread_notification_count(db(), (int) $_SESSION['user_id']),
]);
