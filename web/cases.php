<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/cases.php';
require_role(['LGU Official', 'Admin']);

$pdo = db();
$barangay = (string) $_SESSION['user_barangay'];
$statusFilter = trim((string) ($_GET['status'] ?? 'all'));
$typeFilter = trim((string) ($_GET['type'] ?? 'all'));
$sort = trim((string) ($_GET['sort'] ?? 'urgent'));
$allowedSort = ['urgent', 'filed_desc', 'filed_asc', 'status'];
if (!in_array($sort, $allowedSort, true)) {
    $sort = 'urgent';
}

$summary = fetch_case_summary($pdo, $barangay);
$cases = fetch_cases_for_barangay($pdo, $barangay, $statusFilter, $typeFilter, $sort);

$filterQuery = static function (array $overrides = []) use ($statusFilter, $typeFilter, $sort): string {
    $params = array_merge([
        'status' => $statusFilter,
        'type' => $typeFilter,
        'sort' => $sort,
    ], $overrides);

    return '?' . http_build_query($params);
};

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
        <p class="text-sm text-muted cases-scope-note">
            Showing cases from <strong>Brgy. <?= htmlspecialchars($barangay) ?></strong> only — barangay-scoped LGU view.
        </p>
    </div>
    <form method="get" class="cases-filter-form flex gap-sm flex-wrap">
        <select name="status" class="registry-filter" aria-label="Filter by status" onchange="this.form.submit()">
            <option value="all">All statuses</option>
            <?php foreach (['Received', 'Under Investigation', 'Action Taken', 'Resolved', 'Referred'] as $st): ?>
                <option value="<?= htmlspecialchars($st) ?>" <?= $statusFilter === $st ? 'selected' : '' ?>><?= htmlspecialchars($st) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="type" class="registry-filter" aria-label="Filter by incident type" onchange="this.form.submit()">
            <option value="all">All types</option>
            <?php foreach (array_keys(incident_type_map()) as $type): ?>
                <option value="<?= htmlspecialchars($type) ?>" <?= $typeFilter === $type ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="sort" class="registry-filter" aria-label="Sort cases" onchange="this.form.submit()">
            <option value="urgent" <?= $sort === 'urgent' ? 'selected' : '' ?>>Most urgent first</option>
            <option value="filed_desc" <?= $sort === 'filed_desc' ? 'selected' : '' ?>>Newest filed</option>
            <option value="filed_asc" <?= $sort === 'filed_asc' ? 'selected' : '' ?>>Oldest filed</option>
            <option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>By status</option>
        </select>
    </form>
</div>

<div class="summary-strip scr mb-md">
    <div class="summary-card"><div class="summary-value"><?= (int) $summary['received'] ?></div><div class="summary-label">Received</div></div>
    <div class="summary-card investigating"><div class="summary-value"><?= (int) $summary['investigating'] ?></div><div class="summary-label">Investigating</div></div>
    <div class="summary-card resolved"><div class="summary-value"><?= (int) $summary['resolved'] ?></div><div class="summary-label">Resolved</div></div>
    <div class="summary-card rabies"><div class="summary-value"><?= (int) $summary['rabies'] ?></div><div class="summary-label">Rabies watch</div></div>
</div>

<div class="cases-bulk-bar" data-cases-bulk hidden>
    <label class="cases-bulk-check-all">
        <input type="checkbox" data-cases-select-all aria-label="Select all cases on page">
    </label>
    <span class="text-sm" data-cases-bulk-count>0 selected</span>
    <select class="registry-filter case-status-select" data-cases-bulk-status aria-label="Bulk status">
        <?php foreach (['Received', 'Under Investigation', 'Action Taken', 'Resolved', 'Referred'] as $st): ?>
            <option value="<?= htmlspecialchars($st) ?>"><?= htmlspecialchars($st) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="button" class="btn-primary btn-sm" data-cases-bulk-apply>Update selected</button>
</div>

<div class="hidden-mobile cases-table" data-cases-table>
    <div class="cases-table-header">
        <div aria-hidden="true"></div>
        <div>Case ID</div>
        <div>Incident type</div>
        <div>Dog</div>
        <div>Reporter</div>
        <div>Assigned to</div>
        <div>
            <a href="<?= htmlspecialchars($filterQuery(['sort' => $sort === 'filed_desc' ? 'filed_asc' : 'filed_desc'])) ?>" class="cases-sort-link">
                Filed <?= $sort === 'filed_asc' ? '↑' : ($sort === 'filed_desc' ? '↓' : '') ?>
            </a>
        </div>
        <div>Status</div>
        <div>Actions</div>
    </div>
    <?php if (count($cases) === 0): ?>
        <div class="cases-table-empty feed-empty-state">
            <p class="feed-empty-title">No cases match your filters</p>
            <p class="text-sm text-muted">Try clearing status or type filters.</p>
        </div>
    <?php endif; ?>
    <?php foreach ($cases as $case):
        $statusMeta = case_status_meta($case['CaseStatus']);
        $rabiesWatch = case_is_rabies_watch($case);
        $rabiesDay = $rabiesWatch ? rabies_day_progress($pdo, (int) $case['CaseID']) : 0;
        $dotColor = incident_type_dot_color((string) $case['IncidentType']);
        $dogId = (int) ($case['dog_id'] ?? 0);
        $dogName = (string) ($case['DogName'] ?? '');
        ?>
        <div class="cases-table-row<?= $rabiesWatch ? ' is-rabies-watch' : '' ?>" data-case-row="<?= (int) $case['CaseID'] ?>" data-incident-id="<?= (int) $case['IncidentID'] ?>">
            <div>
                <input type="checkbox" class="cases-row-check" data-case-check value="<?= (int) $case['IncidentID'] ?>" aria-label="Select case #<?= (int) $case['CaseID'] ?>">
            </div>
            <div class="text-xs text-muted">#<?= (int) $case['CaseID'] ?></div>
            <div class="cases-type-cell">
                <span class="cases-type-dot" style="background:<?= htmlspecialchars($dotColor) ?>;"></span>
                <span class="cases-type-label"><?= htmlspecialchars((string) $case['IncidentType']) ?></span>
                <?php if ($rabiesWatch): ?>
                    <span class="cases-rabies-badge" title="Active 14-day rabies monitoring">
                        <i data-lucide="siren"></i>
                        Rabies watch<?= $rabiesDay > 0 ? ' · Day ' . $rabiesDay . '/14' : '' ?>
                    </span>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($dogId > 0 && $dogName !== ''): ?>
                    <a href="dog-profile.php?id=<?= $dogId ?>" class="cases-dog-link"><?= htmlspecialchars($dogName) ?></a>
                <?php else: ?>
                    <span class="text-muted cases-dog-unknown">Unknown</span>
                <?php endif; ?>
            </div>
            <div class="text-sm"><?= htmlspecialchars((string) $case['reporter_name']) ?></div>
            <div class="text-sm">
                <?php if (($case['assignee_name'] ?? '') !== ''): ?>
                    <?= htmlspecialchars((string) $case['assignee_name']) ?>
                <?php else: ?>
                    <span class="text-muted">Unassigned</span>
                <?php endif; ?>
            </div>
            <div class="text-xs text-muted"><?= date('M j', strtotime((string) $case['filed_date'])) ?></div>
            <div>
                <span class="badge <?= htmlspecialchars($statusMeta['class']) ?>" data-case-status-badge="<?= (int) $case['IncidentID'] ?>">
                    <?= htmlspecialchars($statusMeta['label']) ?>
                </span>
            </div>
            <div class="cases-action-cell">
                <a href="incident.php?id=<?= (int) $case['IncidentID'] ?>" class="cases-view-link">View</a>
                <span class="cases-action-divider" aria-hidden="true"></span>
                <label class="cases-status-field">
                    <span class="cases-status-label">Update status</span>
                    <select class="case-status-select" data-case-status="<?= (int) $case['IncidentID'] ?>" aria-label="Update status for case #<?= (int) $case['CaseID'] ?>">
                        <?php foreach (['Received', 'Under Investigation', 'Action Taken', 'Resolved', 'Referred'] as $st): ?>
                            <option value="<?= htmlspecialchars($st) ?>" <?= $case['CaseStatus'] === $st ? 'selected' : '' ?>><?= htmlspecialchars($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="hidden-desktop flex flex-col gap-md">
    <?php foreach ($cases as $case):
        $statusMeta = case_status_meta($case['CaseStatus']);
        $rabiesWatch = case_is_rabies_watch($case);
        ?>
        <article class="incident-card card-bordered cases-mobile-card<?= $rabiesWatch ? ' is-rabies-watch' : '' ?>">
            <div class="accent accent-bite"></div>
            <div class="card-body" style="flex:1;">
                <div class="flex justify-between items-start gap-sm">
                    <span class="text-xs text-muted">CASE #<?= (int) $case['CaseID'] ?></span>
                    <?php if ($rabiesWatch): ?>
                        <span class="cases-rabies-badge cases-rabies-badge--sm"><i data-lucide="siren"></i> Rabies watch</span>
                    <?php endif; ?>
                </div>
                <a href="incident.php?id=<?= (int) $case['IncidentID'] ?>" style="font-weight:800;font-size:16px;margin-top:7px;display:block;color:inherit;text-decoration:none;">
                    <?= htmlspecialchars((string) $case['IncidentType']) ?>
                </a>
                <div class="text-sm text-muted mt-sm">
                    <?php if ((int) ($case['dog_id'] ?? 0) > 0): ?>
                        <a href="dog-profile.php?id=<?= (int) $case['dog_id'] ?>"><?= htmlspecialchars((string) $case['DogName']) ?></a> ·
                    <?php else: ?>
                        Unknown dog ·
                    <?php endif; ?>
                    <?= htmlspecialchars((string) $case['reporter_name']) ?>
                </div>
                <div class="text-xs text-muted mt-sm">
                    Assigned: <?= ($case['assignee_name'] ?? '') !== '' ? htmlspecialchars((string) $case['assignee_name']) : 'Unassigned' ?>
                </div>
                <div class="mt-md">
                    <span class="badge <?= htmlspecialchars($statusMeta['class']) ?>"><?= htmlspecialchars($statusMeta['label']) ?></span>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</div>

<?php app_layout_end([]); ?>
