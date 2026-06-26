<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Case Detail · ' . SITE_NAME;
require __DIR__ . '/includes/head.php';
?>

<div class="case-sheet-backdrop hidden-desktop"></div>
<div class="case-sheet hidden-desktop">
    <div class="sheet-handle" style="margin-top:12px;"></div>
    <div class="scr" style="padding:8px 18px 90px;overflow-y:auto;">
        <?php require __DIR__ . '/partials/case-detail-content.php'; ?>
    </div>
    <div style="position:absolute;left:0;right:0;bottom:0;padding:14px 18px;background:#fff;">
        <button type="button" class="btn-primary btn-block" style="background:var(--air-force);box-shadow:0 8px 18px rgba(108,139,159,.3);">
            <i data-lucide="megaphone"></i> Publish Advisory
        </button>
    </div>
</div>

<div class="app-shell hidden-mobile" style="min-height:100vh;">
    <?php $activeNav = 'cases'; require __DIR__ . '/includes/sidebar.php'; ?>
    <main class="app-main">
        <?php
        $topbarTitle = 'Case Detail';
        $showSearch = false;
        require __DIR__ . '/includes/topbar.php';
        ?>
        <div class="app-content">
            <div class="card card-bordered card-body" style="max-width:720px;">
                <?php require __DIR__ . '/partials/case-detail-content.php'; ?>
                <button type="button" class="btn-primary mt-lg" style="background:var(--air-force);">
                    <i data-lucide="megaphone"></i> Publish Advisory
                </button>
            </div>
        </div>
    </main>
</div>

<a href="cases.php" class="btn-outline hidden-desktop" style="position:fixed;top:16px;left:16px;z-index:300;padding:8px 14px;background:#fff;">← Back</a>

<?php require __DIR__ . '/includes/foot.php'; ?>
