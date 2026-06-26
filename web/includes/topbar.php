<?php
$topbarTitle = $topbarTitle ?? '';
$showSearch = $showSearch ?? true;
$searchPlaceholder = $searchPlaceholder ?? 'Search incidents or dogs…';
?>
<header class="app-topbar hidden-mobile">
    <?php if ($topbarTitle): ?>
        <div class="page-title"><?= htmlspecialchars($topbarTitle) ?></div>
    <?php endif; ?>
    <?php if ($showSearch): ?>
        <div class="app-topbar-search search-bar search-bar-light">
            <i data-lucide="search"></i>
            <span class="text-muted"><?= htmlspecialchars($searchPlaceholder) ?></span>
        </div>
    <?php else: ?>
        <div class="flex-1"></div>
    <?php endif; ?>
    <div class="flex items-center gap-md">
        <div class="icon-box icon-box-sm" style="background: var(--bg-soft);">
            <i data-lucide="sun"></i>
        </div>
        <div style="position: relative;">
            <i data-lucide="bell"></i>
            <div style="position:absolute;top:-2px;right:-2px;width:8px;height:8px;border-radius:50%;background:var(--burnt-peach);border:1.5px solid #fff;"></div>
        </div>
        <div class="avatar avatar-md">MJ</div>
    </div>
</header>
