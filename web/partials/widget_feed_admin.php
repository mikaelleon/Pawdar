<?php
/**
 * Sidebar snapshot for LGU Official / Admin on the Feed screen.
 *
 * @var string $userRole
 */

require_once __DIR__ . '/../includes/cases.php';
require_once __DIR__ . '/../includes/dogs.php';

$pdo = db();
$barangay = (string) ($_SESSION['user_barangay'] ?? '');
$caseSummary = fetch_case_summary($pdo, $barangay);
$openCases = $caseSummary['received'] + $caseSummary['investigating'];
$pendingApprovals = 0;

if ($userRole === 'Admin') {
    $pendingApprovals = count(fetch_pending_users($pdo));
}
?>
<div class="bento-card feed-admin-card">
    <div class="bento-card-header">
        <span class="bento-icon" aria-hidden="true"><i data-lucide="folder-check"></i></span>
        <span class="bento-label">Case overview</span>
    </div>

    <div class="feed-admin-stats">
        <div class="feed-admin-stat">
            <strong><?= (int) $openCases ?></strong>
            <span class="text-xs text-muted">Open cases</span>
        </div>
        <div class="feed-admin-stat">
            <strong><?= (int) $caseSummary['received'] ?></strong>
            <span class="text-xs text-muted">Received</span>
        </div>
        <div class="feed-admin-stat">
            <strong><?= (int) $caseSummary['investigating'] ?></strong>
            <span class="text-xs text-muted">In progress</span>
        </div>
        <?php if ($caseSummary['rabies'] > 0): ?>
            <div class="feed-admin-stat feed-admin-stat--alert">
                <strong><?= (int) $caseSummary['rabies'] ?></strong>
                <span class="text-xs text-muted">Rabies watch</span>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($userRole === 'Admin' && $pendingApprovals > 0): ?>
        <p class="text-sm feed-admin-pending">
            <i data-lucide="user-check" style="width:14px;height:14px;"></i>
            <?= (int) $pendingApprovals ?> account<?= $pendingApprovals === 1 ? '' : 's' ?> awaiting approval
        </p>
    <?php endif; ?>

    <div class="feed-admin-actions">
        <a href="cases.php" class="btn-primary btn-sm btn-block">Manage cases</a>
        <?php if ($userRole === 'Admin'): ?>
            <a href="admin.php" class="btn-outline btn-sm btn-block">Admin panel</a>
        <?php endif; ?>
    </div>
</div>
