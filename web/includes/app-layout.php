<?php
require_once __DIR__ . '/bootstrap.php';
require_login_active();

function app_layout_start(string $activeNav, string $pageTitle, array $options = []): void
{
    global $bodyClass, $pageScripts;
    $bodyClass = 'app-page';
    $pageTitle = $pageTitle . ' · ' . SITE_NAME;
    $activeNav = $activeNav;
    $topbarTitle = $options['topbarTitle'] ?? '';
    $showSearch = $options['showSearch'] ?? true;
    $searchPlaceholder = $options['searchPlaceholder'] ?? 'Search incidents or dogs…';
    $mobileHeader = $options['mobileHeader'] ?? 'default';
    $backTitle = $options['backTitle'] ?? 'Back';
    $backHref = $options['backHref'] ?? 'javascript:history.back()';
    $pageScripts = $options['scripts'] ?? [];

    require __DIR__ . '/head.php';
    echo '<div class="app-shell">';
    require __DIR__ . '/sidebar.php';

    if ($mobileHeader === 'default') {
        require __DIR__ . '/mobile-header-default.php';
    } elseif ($mobileHeader === 'cases') {
        require __DIR__ . '/mobile-header-cases.php';
    } elseif ($mobileHeader === 'back') {
        require __DIR__ . '/mobile-header-back.php';
    }

    echo '<main class="app-main">';
    require __DIR__ . '/topbar.php';
    echo '<div class="app-content app-content-padded">';
}

/**
 * @param array<string, mixed> $fabOptions
 */
function app_layout_end(array $fabOptions = []): void
{
    echo '</div></main>';
    require __DIR__ . '/bottom-nav.php';

    $showFab = (bool) ($fabOptions['show'] ?? false);
    if ($showFab) {
        $fabLabel = (string) ($fabOptions['label'] ?? 'Report');
        $opensDrawer = (bool) ($fabOptions['opensDrawer'] ?? false);
        $fabHref = (string) ($fabOptions['href'] ?? 'report.php');

        if ($opensDrawer) {
            echo '<button type="button" class="fab hidden-desktop" data-open-report-drawer>';
            echo '<i data-lucide="plus"></i><span>' . htmlspecialchars($fabLabel) . '</span></button>';
        } else {
            echo '<a href="' . htmlspecialchars($fabHref) . '" class="fab hidden-desktop">';
            echo '<i data-lucide="plus"></i><span>' . htmlspecialchars($fabLabel) . '</span></a>';
        }
    }

    echo '</div>';
    require __DIR__ . '/foot.php';
}
