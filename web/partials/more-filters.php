<?php
/**
 * Reusable "More filters" disclosure wrapper.
 *
 * @var string $summary Toggle label (default: More filters)
 * @var string $body Inner HTML for overflow filter controls
 * @var bool $open Whether disclosure starts open
 * @var string $class Extra CSS classes on the root element
 * @var string $attrs Extra HTML attributes on the root element
 */
$summary = $summary ?? 'More filters';
$body = $body ?? '';
$open = !empty($open);
$class = trim('more-filters ' . ($class ?? ''));
$attrs = $attrs ?? '';
?>
<details class="<?= htmlspecialchars($class) ?>"<?= $open ? ' open' : '' ?> <?= $attrs ?>>
    <summary class="more-filters-toggle"><?= htmlspecialchars($summary) ?></summary>
    <div class="more-filters-body">
        <?= $body ?>
    </div>
</details>
