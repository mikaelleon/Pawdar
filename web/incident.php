<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/incidents.php';
require_once __DIR__ . '/includes/cases.php';
require_once __DIR__ . '/includes/geocoding.php';

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
$canUpdateCaseStatus = role_can_manage_cases($userRole);
$meta = incident_type_meta((string) $incident['IncidentType']);
$locationParts = incident_location_display(
    (string) $incident['Location'],
    isset($incident['latitude']) ? (float) $incident['latitude'] : null,
    isset($incident['longitude']) ? (float) $incident['longitude'] : null
);
$locationLine = incident_location_with_barangay(
    $locationParts['display'],
    (string) ($incident['reporter_barangay'] ?? '')
);
$title = generate_incident_title(
    (string) $incident['IncidentType'],
    (string) $incident['Location'],
    isset($incident['latitude']) ? (float) $incident['latitude'] : null,
    isset($incident['longitude']) ? (float) $incident['longitude'] : null
);
$statusMeta = case_status_meta($incident['CaseStatus'] ?? null);
$corroborators = fetch_incident_corroborators($pdo, $incidentId, 12);
$related = fetch_related_incidents($pdo, (string) $incident['reporter_barangay'], $incidentId);
$canCorroborate = $isLoggedIn && $userId !== (int) $incident['UserID'] && !(int) $incident['user_corroborated'];
$caseId = (int) ($incident['CaseID'] ?? 0);
$caseHistory = $caseId > 0 ? fetch_case_history($pdo, $caseId) : [];
$rabiesChecklist = $caseId > 0 && (int) ($incident['RabiesMonitoring'] ?? 0) === 1
    ? fetch_rabies_checklist($pdo, $caseId) : [];
$photoUrl = incident_photo_url((string) ($incident['photo_path'] ?? ''));
$descriptionParts = incident_description_parts($incident['Description'] ?? null);
$mapCoords = resolve_incident_coordinates($incident);
$mapLatitude = $mapCoords['lat'] ?? null;
$mapLongitude = $mapCoords['lng'] ?? null;
$mapThumbnail = incident_map_thumbnail_url($mapLatitude, $mapLongitude, 320, 160);
$caseStages = ['Received', 'Under Investigation', 'Action Taken', 'Resolved', 'Referred'];
$currentCaseStatus = (string) ($incident['CaseStatus'] ?? 'Received');
$currentCaseIndex = array_search($currentCaseStatus, $caseStages, true);
if ($currentCaseIndex === false) {
    $currentCaseIndex = 0;
    $currentCaseStatus = 'Received';
}

if ($isLoggedIn) {
    require_once __DIR__ . '/includes/app-layout.php';
    app_layout_start('feed', 'Incident Detail', [
        'showSearch' => false,
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

<div class="incident-detail-layout" data-incident-detail data-incident-id="<?= $incidentId ?>">
    <div class="incident-detail-main">
        <article class="card card-bordered card-body">
            <div class="incident-detail-badges">
                <span class="badge <?= htmlspecialchars($meta['badge']) ?>"><?= htmlspecialchars($meta['label']) ?></span>
                <span class="badge badge-with-dot <?= htmlspecialchars($statusMeta['class']) ?>"
                      data-status-badge="<?= $incidentId ?>">
                    <?php if ($statusMeta['label'] === 'Resolved'): ?>
                        <i data-lucide="check" style="width:12px;height:12px;"></i>
                    <?php else: ?>
                        <span class="badge-dot" aria-hidden="true"></span>
                    <?php endif; ?>
                    <?= htmlspecialchars($statusMeta['label']) ?>
                </span>
            </div>

            <h1 class="incident-detail-title"><?= htmlspecialchars($title) ?></h1>

            <div class="incident-detail-meta text-sm text-muted">
                <span class="incident-detail-meta-row">
                    <i data-lucide="map-pin" aria-hidden="true"></i>
                    <?= htmlspecialchars($locationLine) ?>
                </span>
                <span class="incident-detail-meta-row">
                    <i data-lucide="clock" aria-hidden="true"></i>
                    <?= date('F j, Y · g:i A', strtotime((string) $incident['Date'])) ?>
                </span>
                <span class="incident-detail-meta-row">
                    <i data-lucide="user" aria-hidden="true"></i>
                    Reported by <?= htmlspecialchars((string) $incident['reporter_name']) ?> · <?= htmlspecialchars((string) $incident['reporter_role']) ?>
                </span>
                <?php if (!empty($incident['edited_at'])): ?>
                    <span class="text-xs text-muted">Edited · <?= date('M j, Y g:i A', strtotime((string) $incident['edited_at'])) ?></span>
                <?php endif; ?>
            </div>

            <?php if ($photoUrl !== null): ?>
                <figure class="incident-photo-figure">
                    <img src="<?= htmlspecialchars($photoUrl) ?>" alt="Photo evidence for this incident" class="incident-photo incident-photo--detail">
                </figure>
            <?php endif; ?>

            <?php if ($descriptionParts['observed'] !== null): ?>
                <section class="incident-observed-section">
                    <div class="label-upper mb-sm">Dog appearance</div>
                    <p class="text-sm incident-observed-text"><?= htmlspecialchars($descriptionParts['observed']) ?></p>
                </section>
            <?php endif; ?>

            <?php if ($descriptionParts['narrative'] !== null): ?>
                <section class="incident-narrative-section">
                    <div class="label-upper mb-sm">Description</div>
                    <p class="text-sm incident-narrative-text"><?= nl2br(htmlspecialchars($descriptionParts['narrative'])) ?></p>
                </section>
            <?php endif; ?>

            <hr class="incident-detail-divider">

            <section class="incident-corroborate-section" aria-labelledby="incident-corroborate-heading">
                <div class="label-upper mb-sm" id="incident-corroborate-heading">Corroborate</div>
                <?php if (count($corroborators) > 0): ?>
                    <div class="incident-corroborator-stack" aria-label="People who corroborated this report">
                        <?php foreach ($corroborators as $corroborator): ?>
                            <span class="avatar avatar-sm <?= htmlspecialchars(avatar_color_class((int) $corroborator['UserID'])) ?>"
                                  title="<?= htmlspecialchars((string) $corroborator['Name']) ?>">
                                <?= htmlspecialchars(user_initials_from_name((string) $corroborator['Name'])) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <p class="text-sm text-muted incident-corroborate-count">
                    <?= (int) $incident['corroborate_count'] ?> corroboration<?= (int) $incident['corroborate_count'] === 1 ? '' : 's' ?>
                </p>
                <?php if ($isLoggedIn): ?>
                    <button type="button"
                            class="btn-primary btn-sm"
                            data-corroborate="<?= $incidentId ?>"
                            <?= $canCorroborate ? '' : 'disabled' ?>>
                        I witnessed this too
                    </button>
                <?php else: ?>
                    <a href="login.php" class="btn-outline btn-sm">Log in to corroborate</a>
                <?php endif; ?>
            </section>

            <section class="incident-case-status-section" aria-labelledby="incident-case-status-heading">
                <div class="label-upper mb-sm" id="incident-case-status-heading">Case status</div>
                <div class="case-timeline case-timeline--readonly mb-md"
                     role="list"
                     aria-label="Case status progression">
                    <?php foreach ($caseStages as $index => $stage):
                        $stepClass = 'case-timeline-step';
                        if ($stage === $currentCaseStatus) {
                            $stepClass .= ' is-current';
                        } elseif ($index < $currentCaseIndex) {
                            $stepClass .= ' is-done';
                        } else {
                            $stepClass .= ' is-pending';
                        }
                        ?>
                        <span class="<?= $stepClass ?>" role="listitem"><?= htmlspecialchars($stage) ?></span>
                    <?php endforeach; ?>
                </div>

                <?php if ($canUpdateCaseStatus): ?>
                    <label class="text-sm incident-case-status-label" for="case-status-<?= $incidentId ?>">Update status</label>
                    <select class="case-status-select text-sm"
                            id="case-status-<?= $incidentId ?>"
                            data-case-status="<?= $incidentId ?>"
                            aria-label="Update case status">
                        <?php foreach ($caseStages as $stage): ?>
                            <option value="<?= htmlspecialchars($stage) ?>" <?= $currentCaseStatus === $stage ? 'selected' : '' ?>>
                                <?= htmlspecialchars($stage) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-muted incident-case-status-hint">Status changes are logged with optional remarks for the audit trail.</p>
                <?php endif; ?>
            </section>

            <?php if (count($caseHistory) > 0): ?>
                <section class="incident-status-timeline" aria-labelledby="incident-status-timeline-heading">
                    <div class="label-upper mb-sm" id="incident-status-timeline-heading">Status timeline</div>
                    <div class="case-history-list">
                        <?php foreach (array_reverse($caseHistory) as $entry):
                            $entryMeta = case_status_meta($entry['CaseStatus'] ?? null); ?>
                            <div class="case-history-item">
                                <div class="flex justify-between items-center gap-sm">
                                    <span class="badge <?= htmlspecialchars($entryMeta['class']) ?>"><?= htmlspecialchars((string) $entry['CaseStatus']) ?></span>
                                    <span class="text-xs text-muted"><?= date('M j, Y g:i A', strtotime((string) $entry['created_at'])) ?></span>
                                </div>
                                <?php if (!empty($entry['updater_name'])): ?>
                                    <p class="text-xs text-muted case-history-updater"><?= htmlspecialchars((string) $entry['updater_name']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($entry['notes'])): ?>
                                    <p class="text-sm case-history-notes"><?= nl2br(htmlspecialchars((string) $entry['notes'])) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($incident['IncidentType'] === 'Animal Bite' && count($rabiesChecklist) > 0): ?>
                <section class="incident-rabies-section" aria-labelledby="incident-rabies-heading">
                    <div class="label-upper mb-sm" id="incident-rabies-heading">14-day rabies monitoring</div>
                    <div class="rabies-checklist">
                        <?php foreach ($rabiesChecklist as $row): ?>
                            <div class="rabies-checklist-row">
                                <span>Day <?= (int) $row['day_number'] ?></span>
                                <span class="text-xs text-muted"><?= htmlspecialchars((string) ($row['check_date'] ?? '')) ?></span>
                                <span class="badge badge-received"><?= htmlspecialchars((string) $row['status']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </article>
    </div>

    <aside class="incident-detail-sidebar">
        <?php if (!empty($incident['dog_id'])): ?>
            <div class="card card-bordered card-body mb-md">
                <div class="label-upper mb-sm">Linked dog</div>
                <a href="dog-profile.php?id=<?= (int) $incident['dog_id'] ?>" class="text-sm incident-linked-dog">
                    <?= htmlspecialchars((string) ($incident['DogName'] ?? 'Unknown')) ?>
                </a>
                <p class="text-xs text-muted"><?= htmlspecialchars((string) ($incident['Breed'] ?? '')) ?> · <?= htmlspecialchars((string) ($incident['RegistryID'] ?? '')) ?></p>
            </div>
        <?php endif; ?>

        <div class="card card-bordered card-body mb-md">
            <div class="label-upper mb-sm">Map preview</div>
            <a href="map.php" class="incident-detail-map-tile feed-incident-media-tile feed-incident-media-tile--map<?= $mapThumbnail === null ? ' is-map-fallback' : ' has-map-preview' ?>"
               aria-label="View incident location on full map">
                <?php if ($mapThumbnail !== null): ?>
                    <img src="<?= htmlspecialchars($mapThumbnail) ?>"
                         alt=""
                         class="feed-incident-media-map-img"
                         loading="lazy"
                         decoding="async"
                         onerror="this.closest('.feed-incident-media-tile').classList.add('is-map-fallback'); this.remove(); if(window.lucide){window.lucide.createIcons();}">
                <?php endif; ?>
                <span class="feed-incident-media-placeholder" aria-hidden="true">
                    <i data-lucide="map-pin"></i>
                </span>
            </a>
            <a href="map.php" class="text-sm link-hover incident-detail-map-link">View on full map</a>
        </div>

        <div class="card card-bordered card-body">
            <div class="label-upper mb-sm">Related incidents</div>
            <?php if (count($related) === 0): ?>
                <p class="text-sm text-muted incident-related-empty">No other incidents in this barangay.</p>
            <?php else: ?>
                <ul class="incident-related-list">
                    <?php foreach ($related as $rel):
                        $relTitle = generate_incident_title(
                            (string) $rel['IncidentType'],
                            (string) $rel['Location'],
                            isset($rel['latitude']) ? (float) $rel['latitude'] : null,
                            isset($rel['longitude']) ? (float) $rel['longitude'] : null
                        );
                        $relStatus = case_status_meta($rel['CaseStatus'] ?? null); ?>
                        <li class="incident-related-item">
                            <a href="incident.php?id=<?= (int) $rel['IncidentID'] ?>" class="incident-related-link">
                                <span class="incident-related-title"><?= htmlspecialchars($relTitle) ?></span>
                                <span class="text-xs text-muted"><?= date('M j, Y', strtotime((string) $rel['Date'])) ?></span>
                            </a>
                            <span class="badge <?= htmlspecialchars($relStatus['class']) ?> incident-related-status"><?= htmlspecialchars($relStatus['label']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </aside>
</div>

<?php if ($isLoggedIn): ?>
    <?php app_layout_end([]); ?>
<?php else: ?>
    </div>
    <?php require __DIR__ . '/includes/foot.php'; ?>
<?php endif; ?>
