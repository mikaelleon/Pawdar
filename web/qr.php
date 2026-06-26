<?php
header('Content-Type: image/png');

$id = trim((string) ($_GET['id'] ?? 'PWD-2024-00831'));
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$path = dirname($_SERVER['SCRIPT_NAME'] ?? '/web');
$target = rtrim($baseUrl . $path, '/') . '/dog-profile.php?id=' . rawurlencode($id);

$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . rawurlencode($target);
$data = @file_get_contents($qrUrl);

if ($data === false) {
    http_response_code(500);
    exit;
}

echo $data;
