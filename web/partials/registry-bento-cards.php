<?php
/**
 * Renders registry bento card markup for a result set.
 *
 * @var list<array<string, mixed>> $rows
 * @var bool $canRegister
 */

if (count($rows) === 0): ?>
    <div class="registry-empty">
        <p class="empty-title">No dogs found</p>
        <p class="empty-subtitle">Try a different filter or search term.</p>
        <?php if ($canRegister): ?>
            <a href="register_dog.php" class="btn-primary">+ Register Dog</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <?php foreach ($rows as $dog) {
        require __DIR__ . '/registry-bento-card.php';
    } ?>
<?php endif; ?>
