<?php
require_once __DIR__ . '/includes/app-layout.php';
require_role(['Rescue Organization', 'Admin']);
app_layout_start('rescue-board', 'Rescue Board', ['showSearch' => false]);
?>

<div class="feed-header">
    <div>
        <h1 class="feed-title">Rescue Board</h1>
        <p class="text-sm text-muted">Injured stray cases in Brgy. <?= htmlspecialchars((string) $_SESSION['user_barangay']) ?></p>
    </div>
</div>

<p class="text-sm text-muted">Open cases appear on your feed. Claim stray incidents directly from feed cards.</p>

<?php app_layout_end([]); ?>
