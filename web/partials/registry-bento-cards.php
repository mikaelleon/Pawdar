<?php
/**
 * Renders registry bento card markup for a result set.
 *
 * @var list<array<string, mixed>> $rows
 * @var bool $canRegister
 */

if (count($rows) === 0): ?>
    <div class="registry-empty">
        <svg class="state-illustration" viewBox="0 0 200 160" aria-hidden="true">
            <ellipse cx="100" cy="140" rx="70" ry="10" fill="#C0DAB5" opacity="0.5"/>
            <circle cx="78" cy="88" r="22" fill="#87AFAE"/>
            <circle cx="122" cy="88" r="22" fill="#87AFAE"/>
            <path d="M70 110 Q100 130 130 110" stroke="#6C8B9F" stroke-width="4" fill="none"/>
            <circle cx="100" cy="70" r="8" fill="#E0765E" opacity="0.8"/>
            <path d="M96 70 L104 70 M100 66 L100 74" stroke="#fff" stroke-width="2"/>
        </svg>
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
