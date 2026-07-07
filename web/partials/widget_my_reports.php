<?php
/**
 * Sidebar widget listing the logged-in user's submitted incident reports.
 *
 * @var int $userId
 * @var string $userRole
 */

$pdo = db();
$myReports = fetch_user_reports($pdo, $userId, 4);
?>
<div class="bento-card my-reports-card">
    <div class="bento-card-header">
        <span class="bento-icon" aria-hidden="true"><i data-lucide="clipboard-list"></i></span>
        <span class="bento-label">My reports</span>
    </div>

    <?php if (count($myReports) === 0): ?>
        <p class="my-reports-empty text-sm text-muted">You have not submitted any incident reports yet.</p>
    <?php else: ?>
        <ul class="my-reports-list">
            <?php foreach ($myReports as $report):
                $type = normalize_incident_type((string) $report['IncidentType']);
                $meta = incident_type_meta($type);
                $statusMeta = case_status_meta($report['CaseStatus'] ?? null);
                $reportDate = date('F j, Y', strtotime((string) $report['Date']));
                $locationParts = incident_location_display((string) $report['Location'], null, null);
                ?>
                <li class="my-reports-item">
                    <a href="incident.php?id=<?= (int) $report['IncidentID'] ?>" class="my-reports-link">
                        <span class="my-reports-title"><?= htmlspecialchars($meta['label']) ?></span>
                        <span class="my-reports-meta text-xs text-muted">
                            <?= htmlspecialchars(incident_location_short_label($locationParts['display'])) ?> · <?= htmlspecialchars($reportDate) ?>
                        </span>
                    </a>
                    <span class="badge badge-with-dot my-reports-status <?= htmlspecialchars($statusMeta['class']) ?>">
                        <?php if ($statusMeta['label'] === 'Resolved'): ?>
                            <i data-lucide="check" style="width:12px;height:12px;"></i>
                        <?php else: ?>
                            <span class="badge-dot" aria-hidden="true"></span>
                        <?php endif; ?>
                        <?= htmlspecialchars($statusMeta['label']) ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="my-reports-actions">
        <a href="my-reports.php" class="btn-outline btn-sm btn-block">View My Reports</a>
    </div>
</div>
