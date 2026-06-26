<?php
require_once __DIR__ . '/config.php';

$pageTitle = $pageTitle ?? SITE_NAME;
$pageDescription = $pageDescription ?? SITE_DESCRIPTION;
$bodyClass = $bodyClass ?? '';
$pageScripts = $pageScripts ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <?php if (isset($_SESSION['csrf_token'])): ?>
        <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/pawdar.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
    <script src="assets/js/app.js" defer></script>
    <script src="assets/js/ui.js" defer></script>
    <?php foreach ($pageScripts as $script): ?>
        <script src="<?= htmlspecialchars($script) ?>" defer></script>
    <?php endforeach; ?>
</head>
<body class="<?= htmlspecialchars($bodyClass) ?>">
