<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/dogs.php';
require_once __DIR__ . '/includes/breeds.php';
require_once __DIR__ . '/includes/breed-media.php';

require_login_active();

$dogId = (int) ($_GET['id'] ?? 0);
$autoPrint = isset($_GET['print']) && $_GET['print'] === '1';
$pdo = db();
$dog = $dogId > 0 ? fetch_dog_profile($pdo, $dogId) : null;

if (!$dog) {
    http_response_code(404);
    $pageTitle = 'ID card not found';
    require __DIR__ . '/includes/head.php';
    echo '<body class="id-card-page"><main class="id-card-missing"><h1>Dog not found</h1><p class="text-sm text-muted">This registry record could not be loaded.</p><a href="registry.php" class="btn-outline btn-sm">Back to Registry</a></main></body></html>';
    exit;
}

$breedInfo = null;
if (!empty($dog['breed_id'])) {
    $breedInfo = fetch_breed_by_id($pdo, (int) $dog['breed_id']);
}
if (!$breedInfo) {
    $breedInfo = fetch_breed_by_name($pdo, (string) ($dog['Breed'] ?? ''));
}

$cssPath = 'assets/css/dog-id-card.css';
$cssVersion = is_file(__DIR__ . '/' . $cssPath) ? (string) filemtime(__DIR__ . '/' . $cssPath) : (string) time();
$pageTitle = htmlspecialchars((string) $dog['DogName']) . ' · ID Card';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $pageTitle ?> · <?= htmlspecialchars(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars($cssPath) ?>?v=<?= htmlspecialchars($cssVersion) ?>">
</head>
<body class="id-card-page<?= $autoPrint ? ' id-card-page--autoprint' : '' ?>">
    <div class="id-card-toolbar" data-id-card-toolbar>
        <p class="id-card-toolbar-hint">Preview · one card per page when printing</p>
        <div class="id-card-toolbar-actions">
            <button type="button" class="id-card-btn id-card-btn--primary" data-id-card-print>Print ID card</button>
            <button type="button" class="id-card-btn" data-id-card-close>Close</button>
        </div>
    </div>

    <main class="id-card-print-root id-card-stage">
        <?php require __DIR__ . '/partials/dog-id-card-inner.php'; ?>
    </main>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        (function () {
            var printBtn = document.querySelector('[data-id-card-print]');
            var closeBtn = document.querySelector('[data-id-card-close]');

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }

            if (printBtn) {
                printBtn.addEventListener('click', function () {
                    window.requestAnimationFrame(function () {
                        window.print();
                    });
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    if (window.history.length > 1) {
                        window.history.back();
                        return;
                    }
                    window.location.href = 'registry.php';
                });
            }

            if (document.body.classList.contains('id-card-page--autoprint')) {
                window.addEventListener('load', function () {
                    window.setTimeout(function () {
                        window.print();
                    }, 350);
                });
            }
        })();
    </script>
</body>
</html>
