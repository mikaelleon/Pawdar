<?php
/** @var array<string, mixed> $dog */
/** @var array<string, mixed>|null $breedInfo */
?>
<div class="id-card-modal-overlay" data-id-card-modal hidden>
    <div class="id-card-modal" role="dialog" aria-modal="true" aria-labelledby="id-card-modal-title">
        <div class="id-card-modal-chrome">
            <div class="id-card-modal-chrome-top">
                <h2 class="id-card-modal-title" id="id-card-modal-title">Print ID card</h2>
                <button type="button" class="id-card-modal-close" data-id-card-close aria-label="Close print preview">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <p class="id-card-toolbar-hint">Preview · one card per page when printing</p>
            <div class="id-card-toolbar-actions">
                <button type="button" class="id-card-btn id-card-btn--primary" data-id-card-print>Print ID card</button>
                <button type="button" class="id-card-btn" data-id-card-close-inline>Close</button>
            </div>
        </div>
        <div class="id-card-print-root id-card-stage">
            <?php require __DIR__ . '/dog-id-card-inner.php'; ?>
        </div>
    </div>
</div>
