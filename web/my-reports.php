<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/incidents.php';

$userRole = current_user_role();
$userId = (int) $_SESSION['user_id'];
$pdo = db();
$reports = fetch_user_reports($pdo, $userId, 50);

app_layout_start('feed', 'My Reports', [
    'showSearch' => false,
    'showMobileSearch' => false,
    'topbarTitle' => 'My Reports',
    'report_drawer' => role_can_report($userRole),
    'breadcrumbs' => [
        ['label' => 'Feed', 'url' => 'feed.php'],
        ['label' => 'My Reports'],
    ],
]);
?>

<div class="feed-header">
    <div>
        <h1 class="feed-title">My Reports</h1>
        <p class="text-sm text-muted">Incidents you have submitted on Pawdar.</p>
    </div>
    <?php if (role_can_report($userRole)): ?>
        <div class="feed-header-actions hidden-mobile">
            <button type="button" class="btn-primary" style="height:44px;padding:0 20px;font-size:14px;" data-open-report-drawer>
                <i data-lucide="plus"></i> Report Incident
            </button>
        </div>
    <?php endif; ?>
</div>

<?php if (count($reports) === 0): ?>
    <div class="feed-empty-state">
        <p class="feed-empty-title">No reports yet</p>
        <p class="text-sm text-muted">When you report an incident, it will appear here with status updates.</p>
        <?php if (role_can_report($userRole)): ?>
            <button type="button" class="btn-primary btn-sm" data-open-report-drawer style="margin-top:14px;">
                Report your first incident
            </button>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="my-reports-page-list">
        <?php foreach ($reports as $report):
            $type = normalize_incident_type((string) $report['IncidentType']);
            $meta = incident_type_meta($type);
            $statusMeta = case_status_meta($report['CaseStatus'] ?? null);
            $reportDate = date('F j, Y', strtotime((string) $report['Date']));
            $locationParts = incident_location_display((string) $report['Location'], null, null);
            ?>
            <a href="incident.php?id=<?= (int) $report['IncidentID'] ?>" class="my-reports-page-item card-bordered">
                <div class="my-reports-page-main">
                    <span class="my-reports-page-type"><?= htmlspecialchars($meta['label']) ?></span>
                    <span class="text-sm text-muted">
                        <?= htmlspecialchars($locationParts['display']) ?> · <?= htmlspecialchars($reportDate) ?>
                    </span>
                </div>
                <span class="badge badge-with-dot <?= htmlspecialchars($statusMeta['class']) ?>">
                    <?php if ($statusMeta['label'] === 'Resolved'): ?>
                        <i data-lucide="check" style="width:12px;height:12px;"></i>
                    <?php else: ?>
                        <span class="badge-dot" aria-hidden="true"></span>
                    <?php endif; ?>
                    <?= htmlspecialchars($statusMeta['label']) ?>
                </span>
                <i data-lucide="chevron-right" class="my-reports-page-chevron" aria-hidden="true"></i>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$fabOptions = ['show' => false];
if (role_can_report($userRole)) {
    $fabOptions = ['show' => true, 'label' => 'Report', 'opensDrawer' => true];
}
app_layout_end($fabOptions);
?>
