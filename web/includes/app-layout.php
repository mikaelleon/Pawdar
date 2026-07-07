<?php
require_once __DIR__ . '/bootstrap.php';
require_login_active();

function app_layout_start(string $activeNav, string $pageTitle, array $options = []): void
{
    global $bodyClass, $pageScripts, $pageStyles, $breadcrumbs, $adminContext, $includeReportDrawer, $showMobileSearch;
    $bodyClass = 'app-page';
    $pageTitle = $pageTitle . ' · ' . SITE_NAME;
    $activeNav = $activeNav;
    $topbarTitle = $options['topbarTitle'] ?? '';
    $showSearch = $options['showSearch'] ?? true;
    $showMobileSearch = $options['showMobileSearch'] ?? $showSearch;
    $searchPlaceholder = $options['searchPlaceholder'] ?? 'Search incidents or dogs…';
    $mobileHeader = $options['mobileHeader'] ?? 'default';
    $backTitle = $options['backTitle'] ?? 'Back';
    $backHref = $options['backHref'] ?? 'javascript:history.back()';
    $pageScripts = $options['scripts'] ?? [];
    $pageStyles = $options['styles'] ?? [];
    $breadcrumbs = $options['breadcrumbs'] ?? null;
    $adminContext = (bool) ($options['admin_context'] ?? false);
    $includeReportDrawer = (bool) ($options['report_drawer'] ?? false);

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
    if (is_array($breadcrumbs) && count($breadcrumbs) > 0) {
        echo '<div class="app-mobile-breadcrumb hidden-desktop">';
        require __DIR__ . '/../partials/breadcrumb.php';
        echo '</div>';
    }
    echo '<div class="app-content app-content-padded">';
}

/**
 * @param array<string, mixed> $fabOptions
 */
function app_layout_end(array $fabOptions = []): void
{
    global $includeReportDrawer;

    echo '</div></main>';

    require __DIR__ . '/bottom-nav.php';

    if (!empty($includeReportDrawer)) {
        require __DIR__ . '/../partials/report-drawer.php';
        echo '<div class="toast-container" data-toast-container aria-live="polite"></div>';
    }

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

    if (!empty($fabOptions['dog_edit_modal']) && is_array($fabOptions['dog_edit_modal'])) {
        $dog = $fabOptions['dog_edit_modal'];
        require __DIR__ . '/../partials/dog-edit-modal.php';
    }

    if (!empty($fabOptions['dog_id_card_modal']) && is_array($fabOptions['dog_id_card_modal'])) {
        $dog = $fabOptions['dog_id_card_modal']['dog'];
        $breedInfo = $fabOptions['dog_id_card_modal']['breedInfo'] ?? null;
        require __DIR__ . '/../partials/dog-id-card-modal.php';
    }

    require __DIR__ . '/foot.php';
}
