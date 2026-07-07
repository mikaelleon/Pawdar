<?php
require_once __DIR__ . '/config.php';

$pageTitle = $pageTitle ?? SITE_NAME;
$pageDescription = $pageDescription ?? SITE_DESCRIPTION;
$bodyClass = $bodyClass ?? '';
$pageScripts = $pageScripts ?? [];
$pageStyles = $pageStyles ?? [];
$includeReportDrawer = $includeReportDrawer ?? false;
$showPawBackground = $showPawBackground ?? true;

$assetVersion = static function (string $relativePath): string {
    $absolute = dirname(__DIR__) . '/' . ltrim($relativePath, '/');
    $version = is_file($absolute) ? (string) filemtime($absolute) : (string) time();

    return $relativePath . '?v=' . $version;
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        (function () {
            var saved = localStorage.getItem('pawdar-theme');
            var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
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
    <link rel="stylesheet" href="<?= htmlspecialchars($assetVersion('assets/css/pawdar.css')) ?>">
    <?php foreach ($pageStyles as $style): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($assetVersion($style)) ?>">
    <?php endforeach; ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
    <script src="<?= htmlspecialchars($assetVersion('assets/js/app.js')) ?>" defer></script>
    <script src="<?= htmlspecialchars($assetVersion('assets/js/ui.js')) ?>" defer></script>
    <script src="<?= htmlspecialchars($assetVersion('assets/js/corroborate.js')) ?>" defer></script>
    <script src="<?= htmlspecialchars($assetVersion('assets/js/case-status-update.js')) ?>" defer></script>
    <script src="<?= htmlspecialchars($assetVersion('assets/js/theme-toggle.js')) ?>" defer></script>
    <?php if (!empty($includeReportDrawer)): ?>
        <script src="<?= htmlspecialchars($assetVersion('assets/js/report-drawer.js')) ?>" defer></script>
    <?php endif; ?>
    <?php foreach ($pageScripts as $script): ?>
        <?php if (preg_match('#^https?://#i', $script)): ?>
            <script src="<?= htmlspecialchars($script) ?>" defer></script>
        <?php else: ?>
            <script src="<?= htmlspecialchars($assetVersion($script)) ?>" defer></script>
        <?php endif; ?>
    <?php endforeach; ?>
</head>
<body class="<?= htmlspecialchars($bodyClass) ?>">
<?php if ($showPawBackground): ?>
    <?php require __DIR__ . '/paw-bg-layer.php'; ?>
<?php endif; ?>
