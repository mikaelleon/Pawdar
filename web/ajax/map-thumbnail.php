<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/map-thumbnail.php';

$latitude = isset($_GET['lat']) && is_numeric($_GET['lat']) ? (float) $_GET['lat'] : null;
$longitude = isset($_GET['lng']) && is_numeric($_GET['lng']) ? (float) $_GET['lng'] : null;
$width = isset($_GET['w']) && is_numeric($_GET['w']) ? (int) $_GET['w'] : 320;
$height = isset($_GET['h']) && is_numeric($_GET['h']) ? (int) $_GET['h'] : 176;

if ($latitude === null || $longitude === null) {
    http_response_code(400);
    exit('Missing coordinates');
}

if (!render_incident_map_thumbnail($latitude, $longitude, $width, $height)) {
    $tileUrl = incident_map_tile_url($latitude, $longitude);
    if ($tileUrl !== null) {
        header('Location: ' . $tileUrl, true, 302);
        exit;
    }

    http_response_code(503);
    exit('Map preview unavailable');
}
