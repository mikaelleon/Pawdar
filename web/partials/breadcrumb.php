<?php
/**
 * Breadcrumb trail. Set $breadcrumbs before include, or pass via app_layout_start().
 *
 * @var list<array{label: string, url?: string}> $breadcrumbs
 * @var bool $adminContext
 */
$breadcrumbs = $breadcrumbs ?? [];
$adminContext = $adminContext ?? false;

if (count($breadcrumbs) === 0) {
    return;
}

$baseLabel = $adminContext ? 'Admin' : 'Home';
$baseUrl = $adminContext ? 'admin.php' : 'feed.php';
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <ol>
        <li><a href="<?= htmlspecialchars($baseUrl) ?>"><?= htmlspecialchars($baseLabel) ?></a></li>
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <?php if ($index === array_key_last($breadcrumbs)): ?>
                <li aria-current="page"><?= htmlspecialchars($crumb['label']) ?></li>
            <?php else: ?>
                <li><a href="<?= htmlspecialchars((string) ($crumb['url'] ?? '#')) ?>"><?= htmlspecialchars($crumb['label']) ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
