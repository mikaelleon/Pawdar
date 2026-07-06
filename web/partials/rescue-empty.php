<?php
/**
 * Empty state block for rescue board sections.
 *
 * @var string $icon Lucide icon name
 * @var string $title
 * @var string $message
 * @var string $actionHtml Optional button/link HTML
 */
$icon = $icon ?? 'inbox';
$title = $title ?? 'Nothing here yet';
$message = $message ?? '';
$actionHtml = $actionHtml ?? '';
?>
<div class="rescue-empty feed-empty-state">
    <div class="icon-box icon-box-lg rescue-empty-icon" aria-hidden="true">
        <i data-lucide="<?= htmlspecialchars($icon) ?>"></i>
    </div>
    <p class="feed-empty-title"><?= htmlspecialchars($title) ?></p>
    <?php if ($message !== ''): ?>
        <p class="text-sm text-muted"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <?php if ($actionHtml !== ''): ?>
        <div class="mt-md"><?= $actionHtml ?></div>
    <?php endif; ?>
</div>
