<?php
$backTitle = $backTitle ?? 'Back';
$backHref = $backHref ?? 'javascript:history.back()';
?>
<header class="auth-mobile-header hidden-desktop">
    <a href="<?= htmlspecialchars($backHref) ?>" class="flex items-center gap-sm" style="color: inherit;">
        <i data-lucide="arrow-left"></i>
    </a>
    <div class="flex-1 text-center" style="font-weight:800;font-size:17px;margin-left:-24px;">
        <?= htmlspecialchars($backTitle) ?>
    </div>
</header>
