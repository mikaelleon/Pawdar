<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/incidents.php';
require_once __DIR__ . '/includes/cases.php';

$incidentId = (int) ($_GET['id'] ?? 0);
$userId = (int) ($_SESSION['user_id'] ?? 0);
$pdo = db();
$incident = $incidentId > 0 ? fetch_incident_detail($pdo, $incidentId, $userId) : null;

if (!$incident) {
    http_response_code(404);
    echo 'Incident not found.';
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = current_user_role();
$meta = incident_type_meta((string) $incident['IncidentType']);
$locationParts = incident_location_display(
    (string) $incident['Location'],
    isset($incident['latitude']) ? (float) $incident['latitude'] : null,
    isset($incident['longitude']) ? (float) $incident['longitude'] : null
);
$title = generate_incident_title(
    (string) $incident['IncidentType'],
    (string) $incident['Location'],
    isset($incident['latitude']) ? (float) $incident['latitude'] : null,
    isset($incident['longitude']) ? (float) $incident['longitude'] : null
);
$statusMeta = case_status_meta($incident['CaseStatus'] ?? null);
$corroborators = fetch_incident_corroborators($pdo, $incidentId);
$related = fetch_related_incidents($pdo, (string) $incident['reporter_barangay'], $incidentId);
$canCorroborate = $isLoggedIn && $userId !== (int) $incident['UserID'] && !(int) $incident['user_corroborated'];
$caseId = (int) ($incident['CaseID'] ?? 0);
$caseHistory = $caseId > 0 ? fetch_case_history($pdo, $caseId) : [];
$rabiesChecklist = $caseId > 0 && (int) ($incident['RabiesMonitoring'] ?? 0) === 1
    ? fetch_rabies_checklist($pdo, $caseId) : [];

if ($isLoggedIn) {
    require_once __DIR__ . '/includes/app-layout.php';
    app_layout_start('feed', 'Incident Detail', [
        'showSearch' => false,
        'scripts' => ['assets/js/incident-detail.js'],
        'breadcrumbs' => [
            ['label' => 'Feed', 'url' => 'feed.php'],
            ['label' => 'Incident #' . $incidentId],
        ],
    ]);
} else {
    $pageTitle = 'Incident · ' . SITE_NAME;
    require __DIR__ . '/includes/head.php';
    echo '<div class="auth-form-side" style="max-width:960px;margin:24px auto;padding:16px;">';
}
?>

<div class="incident-detail-layout">
    <div class="incident-detail-main">
        <article class="card card-bordered card-body">
            <span class="badge <?= htmlspecialchars($meta['badge']) ?> mb-sm"><?= htmlspecialchars($meta['label']) ?></span>
            <h1 class="feed-title" style="font-size:24px;"><?= htmlspecialchars($title) ?></h1>
            <div class="text-sm text-muted flex items-center gap-sm mt-sm">
                <i data-lucide="map-pin" style="width:14px;height:14px;"></i>
                <?= htmlspecialchars($locationParts['display']) ?> · Brgy. <?= htmlspecialchars((string) $incident['reporter_barangay']) ?>
            </div>
            <p class="text-sm text-muted mt-sm"><?= date('F j, Y · g:i A', strtotime((string) $incident['Date'])) ?></p>
            <p class="text-xs text-muted">Reported by <?= htmlspecialchars((string) $incident['reporter_name']) ?> · <?= htmlspecialchars((string) $incident['reporter_role']) ?></p>
            <?php if (!empty($incident['edited_at'])): ?>
                <p class="text-xs text-muted">Edited · <?= date('M j, Y g:i A', strtotime((string) $incident['edited_at'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($incident['photo_path'])): ?>
                <img src="<?= htmlspecialchars((string) $incident['photo_path']) ?>" alt="Incident photo" class="incident-photo mt-md">
            <?php endif; ?>

            <?php if (!empty($incident['Description'])): ?>
                <p class="text-sm mt-md"><?= nl2br(htmlspecialchars((string) $incident['Description'])) ?></p>
            <?php endif; ?>

            <hr style="border:none;border-top:1px solid var(--border-light);margin:20px 0;">

            <div class="label-upper mb-sm">Corroborate</div>
            <div class="flex items-center gap-sm mb-md flex-wrap">
                <?php foreach ($corroborators as $c): ?>
                    <span class="avatar avatar-sm <?= htmlspecialchars(avatar_color_class((int) $c['UserID'])) ?>"><?= htmlspecialchars(user_initials_from_name((string) $c['Name'])) ?></span>
                <?php endforeach; ?>
                <span class="text-sm text-muted"><?= (int) $incident['corroborate_count'] ?> corroborations</span>
            </div>
            <?php if ($isLoggedIn): ?>
                <div class="flex gap-sm flex-wrap">
                    <button type="button" class="btn-primary btn-sm" data-corroborate="<?= $incidentId ?>" <?= $canCorroborate ? '' : 'disabled' ?>>
                        I witnessed this too
                    </button>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn-outline btn-sm">Log in to corroborate</a>
            <?php endif; ?>

            <div class="label-upper mt-md mb-sm">Case status</div>
            <div class="case-timeline mb-md">
                <?php
                $stages = ['Received', 'Under Investigation', 'Action Taken', 'Resolved', 'Referred'];
                $current = (string) ($incident['CaseStatus'] ?? 'Received');
                $currentIndex = array_search($current, $stages, true);
                if ($currentIndex === false) {
                    $currentIndex = 0;
                }
                foreach ($stages as $index => $stage):
                ?>
                    <span class="case-timeline-step<?= $stage === $current ? ' is-current' : ($index < $currentIndex ? ' is-done' : '') ?>"><?= htmlspecialchars($stage) ?></span>
                <?php endforeach; ?>
            </div>
            <?php if (in_array($userRole, ['LGU Official', 'Admin'], true)): ?>
                <select class="case-status-select text-sm" data-case-status="<?= $incidentId ?>">
                    <?php foreach (['Received', 'Under Investigation', 'Action Taken', 'Resolved', 'Referred'] as $st): ?>
                        <option value="<?= htmlspecialchars($st) ?>" <?= ($incident['CaseStatus'] ?? '') === $st ? 'selected' : '' ?>><?= htmlspecialchars($st) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <span class="badge <?= htmlspecialchars($statusMeta['class']) ?>"><?= htmlspecialchars($statusMeta['label']) ?></span>
            <?php endif; ?>

            <?php if (count($caseHistory) > 0): ?>
                <div class="label-upper mt-md mb-sm">Status timeline</div>
                <div class="case-history-list">
                    <?php foreach (array_reverse($caseHistory) as $entry): ?>
                        <div class="case-history-item">
                            <div class="flex justify-between items-center gap-sm">
                                <span class="badge badge-received"><?= htmlspecialchars((string) $entry['CaseStatus']) ?></span>
                                <span class="text-xs text-muted"><?= date('M j, Y g:i A', strtotime((string) $entry['created_at'])) ?></span>
                            </div>
                            <?php if (!empty($entry['updater_name'])): ?>
                                <p class="text-xs text-muted" style="margin:4px 0 0;"><?= htmlspecialchars((string) $entry['updater_name']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($entry['notes'])): ?>
                                <p class="text-sm" style="margin:6px 0 0;"><?= nl2br(htmlspecialchars((string) $entry['notes'])) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($incident['IncidentType'] === 'Animal Bite' && count($rabiesChecklist) > 0): ?>
                <div class="label-upper mt-md mb-sm">14-day rabies monitoring</div>
                <div class="rabies-checklist">
                    <?php foreach ($rabiesChecklist as $row): ?>
                        <div class="rabies-checklist-row">
                            <span>Day <?= (int) $row['day_number'] ?></span>
                            <span class="text-xs text-muted"><?= htmlspecialchars((string) ($row['check_date'] ?? '')) ?></span>
                            <span class="badge badge-received"><?= htmlspecialchars((string) $row['status']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>
    </div>

    <aside class="incident-detail-sidebar">
        <?php if (!empty($incident['dog_id'])): ?>
            <div class="card card-bordered card-body mb-md">
                <div class="label-upper mb-sm">Linked dog</div>
                <a href="dog-profile.php?id=<?= (int) $incident['dog_id'] ?>" class="text-sm" style="font-weight:700;">
                    <?= htmlspecialchars((string) ($incident['DogName'] ?? 'Unknown')) ?>
                </a>
                <p class="text-xs text-muted"><?= htmlspecialchars((string) ($incident['Breed'] ?? '')) ?> · <?= htmlspecialchars((string) ($incident['RegistryID'] ?? '')) ?></p>
            </div>
        <?php endif; ?>

        <div class="card card-bordered card-body mb-md">
            <div class="label-upper mb-sm">Map preview</div>
            <div id="incident-mini-map" style="height:160px;border-radius:12px;background:var(--bg-map);"></div>
            <a href="map.php" class="text-sm link-hover mt-sm" style="display:inline-block;">View on full map</a>
        </div>

        <div class="card card-bordered card-body">
            <div class="label-upper mb-sm">Related incidents</div>
            <?php foreach ($related as $rel): ?>
                <a href="incident.php?id=<?= (int) $rel['IncidentID'] ?>" class="text-sm block mb-sm">
                    <?= htmlspecialchars(generate_incident_title((string) $rel['IncidentType'], (string) $rel['Location'])) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </aside>
</div>

<?php if ($isLoggedIn): ?>
    <?php app_layout_end([]); ?>
<?php else: ?>
    </div>
    <?php require __DIR__ . '/includes/foot.php'; ?>
<?php endif; ?>

<script>
window.incidentMapPin = {
    lat: <?= json_encode((float) ($incident['latitude'] ?? 13.7568)) ?>,
    lng: <?= json_encode((float) ($incident['longitude'] ?? 121.0583)) ?>
};
</script>
