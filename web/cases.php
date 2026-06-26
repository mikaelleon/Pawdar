<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/cases.php';
require_role(['LGU Official', 'Admin']);

$pdo = db();
$barangay = (string) $_SESSION['user_barangay'];
$statusFilter = trim((string) ($_GET['status'] ?? 'all'));
$typeFilter = trim((string) ($_GET['type'] ?? 'all'));
$summary = fetch_case_summary($pdo, $barangay);
$cases = fetch_cases_for_barangay($pdo, $barangay, $statusFilter, $typeFilter);

app_layout_start('cases', 'Case Management', [
    'showSearch' => false,
    'topbarTitle' => 'Case Management',
    'mobileHeader' => 'cases',
    'scripts' => ['assets/js/cases.js'],
    'breadcrumbs' => [['label' => 'Case Management']],
]);
?>

<div class="feed-header">
    <div>
        <h1 class="feed-title">Case management</h1>
        <p class="text-sm text-muted">Brgy. <?= htmlspecialchars($barangay) ?></p>
    </div>
    <form method="get" class="flex gap-sm flex-wrap">
        <select name="status" class="registry-filter" onchange="this.form.submit()">
            <option value="all">All statuses</option>
            <?php foreach (['Received', 'Under Investigation', 'Action Taken', 'Resolved', 'Referred'] as $st): ?>
                <option value="<?= htmlspecialchars($st) ?>" <?= $statusFilter === $st ? 'selected' : '' ?>><?= htmlspecialchars($st) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="type" class="registry-filter" onchange="this.form.submit()">
            <option value="all">All types</option>
            <?php foreach (array_keys(incident_type_map()) as $type): ?>
                <option value="<?= htmlspecialchars($type) ?>" <?= $typeFilter === $type ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="summary-strip scr mb-md">
    <div class="summary-card"><div class="summary-value"><?= (int) $summary['received'] ?></div><div class="summary-label">Received</div></div>
    <div class="summary-card investigating"><div class="summary-value"><?= (int) $summary['investigating'] ?></div><div class="summary-label">Investigating</div></div>
    <div class="summary-card resolved"><div class="summary-value"><?= (int) $summary['resolved'] ?></div><div class="summary-label">Resolved</div></div>
    <div class="summary-card rabies"><div class="summary-value"><?= (int) $summary['rabies'] ?></div><div class="summary-label">Rabies watch</div></div>
</div>

<div class="hidden-mobile cases-table">
    <div class="cases-table-header">
        <div>Case ID</div><div>Incident type</div><div>Dog</div><div>Reporter</div><div>Filed</div><div>Status</div><div>Action</div>
    </div>
    <?php foreach ($cases as $case):
        $statusMeta = case_status_meta($case['CaseStatus']);
        $rabiesDay = (int) ($case['RabiesMonitoring'] ?? 0) === 1 ? rabies_day_progress($pdo, (int) $case['CaseID']) : 0;
    ?>
        <div class="cases-table-row" data-case-row="<?= (int) $case['CaseID'] ?>">
            <div class="text-xs text-muted">#<?= (int) $case['CaseID'] ?></div>
            <div class="flex items-center gap-sm">
                <span style="width:8px;height:8px;border-radius:50%;background:var(--burnt-peach);"></span>
                <span style="font-weight:700;"><?= htmlspecialchars((string) $case['IncidentType']) ?></span>
                <?php if ($rabiesDay > 0): ?><span class="badge badge-bite">Day <?= $rabiesDay ?>/14</span><?php endif; ?>
            </div>
            <div style="font-style:italic;"><?= htmlspecialchars((string) ($case['DogName'] ?? 'Unknown')) ?></div>
            <div class="text-sm"><?= htmlspecialchars((string) $case['reporter_name']) ?></div>
            <div class="text-xs text-muted"><?= date('M j', strtotime((string) $case['filed_date'])) ?></div>
            <div><span class="badge <?= htmlspecialchars($statusMeta['class']) ?>"><?= htmlspecialchars($statusMeta['label']) ?></span></div>
            <div class="flex gap-sm">
                <a href="incident.php?id=<?= (int) $case['IncidentID'] ?>" class="text-muted" style="font-weight:700;font-size:12px;">View</a>
                <select class="case-status-select text-xs" data-case-status="<?= (int) $case['IncidentID'] ?>">
                    <?php foreach (['Received', 'Under Investigation', 'Action Taken', 'Resolved', 'Referred'] as $st): ?>
                        <option value="<?= htmlspecialchars($st) ?>" <?= $case['CaseStatus'] === $st ? 'selected' : '' ?>><?= htmlspecialchars($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="hidden-desktop flex flex-col gap-md">
    <?php foreach ($cases as $case):
        $statusMeta = case_status_meta($case['CaseStatus']);
    ?>
        <a href="incident.php?id=<?= (int) $case['IncidentID'] ?>" class="incident-card card-bordered">
            <div class="accent accent-bite"></div>
            <div class="card-body" style="flex:1;">
                <span class="text-xs text-muted">CASE #<?= (int) $case['CaseID'] ?></span>
                <div style="font-weight:800;font-size:16px;margin-top:7px;"><?= htmlspecialchars((string) $case['IncidentType']) ?></div>
                <div class="text-sm text-muted"><?= htmlspecialchars((string) ($case['DogName'] ?? 'Unknown')) ?> · <?= htmlspecialchars((string) $case['reporter_name']) ?></div>
                <div style="margin-top:13px;"><span class="badge <?= htmlspecialchars($statusMeta['class']) ?>"><?= htmlspecialchars($statusMeta['label']) ?></span></div>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<?php app_layout_end([]); ?>
