<?php

/**
 * Renders incident feed cards from database rows.
 *
 * @param list<array<string, mixed>> $incidents
 */
function render_incident_cards(array $incidents, string $userRole, int $userId): void
{
    echo render_incident_cards_html($incidents, $userRole, $userId);
}

/**
 * Returns HTML for incident cards.
 *
 * @param list<array<string, mixed>> $incidents
 */
function render_incident_cards_html(array $incidents, string $userRole, int $userId, string $filter = 'all'): string
{
    if (count($incidents) === 0) {
        return render_feed_empty_state($filter);
    }

    ob_start();

    foreach ($incidents as $incident) {
        render_single_incident_card($incident, $userRole, $userId);
    }

    return (string) ob_get_clean();
}

/**
 * Renders empty feed state with illustration.
 */
function render_feed_empty_state(string $filter): string
{
    $labels = [
        'all' => 'nearby',
        'animal_bite' => 'animal bite',
        'injured_stray' => 'injured stray',
        'aggressive' => 'aggressive',
        'vehicular' => 'vehicular',
        'disturbance' => 'disturbance',
        'trash' => 'disturbance',
    ];
    $label = $labels[$filter] ?? 'matching';

    ob_start(); ?>
    <div class="feed-empty-state">
        <svg class="feed-empty-illustration" viewBox="0 0 200 160" aria-hidden="true">
            <ellipse cx="100" cy="140" rx="70" ry="10" fill="#C0DAB5" opacity="0.5"/>
            <path d="M60 95c0-22 18-40 40-40s40 18 40 40v25H60V95z" fill="#87AFAE"/>
            <circle cx="82" cy="88" r="4" fill="#4A4343"/>
            <circle cx="118" cy="88" r="4" fill="#4A4343"/>
            <path d="M92 98c4 6 12 6 16 0" stroke="#4A4343" stroke-width="2" fill="none" stroke-linecap="round"/>
            <path d="M55 75c-8-10-5-22 5-26M145 75c8-10 5-22-5-26" stroke="#6C8B9F" stroke-width="6" stroke-linecap="round"/>
            <path d="M100 55c-6-12-18-14-24-6" stroke="#E0765E" stroke-width="4" stroke-linecap="round" fill="none"/>
        </svg>
        <p class="feed-empty-title">No <?= htmlspecialchars($label) ?> incidents reported nearby</p>
        <p class="text-sm text-muted">Your barangay feed is clear for now.</p>
        <?php if (role_can_report(current_user_role())): ?>
            <button type="button" class="btn-primary btn-sm" data-open-report-drawer style="margin-top:14px;">
                Be the first to report
            </button>
        <?php endif; ?>
    </div>
    <?php

    return (string) ob_get_clean();
}

/**
 * Renders skeleton loading cards.
 */
function render_feed_skeleton_cards(int $count = 3): void
{
    for ($i = 0; $i < $count; $i++): ?>
        <div class="feed-incident-card-wrap" aria-hidden="true">
            <article class="incident-card feed-incident-card incident-skeleton card-bordered">
                <div class="card-body feed-incident-card-body">
                    <div class="feed-incident-header">
                            <div class="feed-incident-icon skeleton-shimmer"></div>
                            <div class="feed-incident-meta">
                                <div class="skeleton-line skeleton-shimmer" style="width:45%;height:18px;margin-bottom:8px;"></div>
                                <div class="skeleton-line skeleton-shimmer" style="width:70%;height:14px;"></div>
                            </div>
                            <div class="skeleton-line skeleton-shimmer" style="width:76px;height:24px;border-radius:8px;"></div>
                        </div>
                        <div class="feed-incident-media incident-card-tiles feed-incident-media--no-photo">
                            <div class="feed-incident-media-tile incident-card-tile-photo skeleton-shimmer"></div>
                            <div class="feed-incident-media-tile incident-card-tile-map skeleton-shimmer"></div>
                        </div>
                        <div class="skeleton-line skeleton-shimmer" style="width:100%;height:36px;margin-top:16px;"></div>
                    <div class="feed-incident-open skeleton-shimmer" aria-hidden="true"></div>
                </div>
            </article>
        </div>
    <?php endfor;
}

/**
 * @param array<string, mixed> $incident
 */
function render_single_incident_card(array $incident, string $userRole, int $userId): void
{
    $type = normalize_incident_type((string) $incident['IncidentType']);
    $meta = incident_type_meta($type);
    $severity = incident_type_severity($type);
    $severitySurfaceClass = incident_severity_surface_class($severity, 'feed-incident-icon');
    $locationParts = incident_location_display(
        (string) $incident['Location'],
        isset($incident['latitude']) ? (float) $incident['latitude'] : null,
        isset($incident['longitude']) ? (float) $incident['longitude'] : null
    );
    $displayLocation = $locationParts['display'];

    $timeAgo = time_elapsed_string((string) $incident['Date']);
    $statusMeta = case_status_meta($incident['CaseStatus'] ?? null);
    $corroborateCount = (int) ($incident['corroborate_count'] ?? 0);
    $incidentId = (int) $incident['IncidentID'];
    $reporterId = (int) ($incident['reporter_id'] ?? 0);
    $hasCorroborated = (int) ($incident['user_corroborated'] ?? 0) === 1;
    $isOwnReport = $reporterId === $userId;
    $canCorroborate = !$isOwnReport && !$hasCorroborated;
    $dogId = isset($incident['dog_id']) ? (int) $incident['dog_id'] : 0;
    $fullTimestamp = date('F j, Y', strtotime((string) $incident['Date']));
    $photoPath = incident_photo_url((string) ($incident['photo_path'] ?? ''));
    $latitude = isset($incident['latitude']) ? (float) $incident['latitude'] : null;
    $longitude = isset($incident['longitude']) ? (float) $incident['longitude'] : null;
    $mapCoords = resolve_incident_coordinates($incident);
    if ($mapCoords !== null) {
        $latitude = $mapCoords['lat'];
        $longitude = $mapCoords['lng'];
    }
    $mapThumbnail = incident_map_thumbnail_url($latitude, $longitude, 800, 250);
    $descriptionSnippet = trim((string) ($incident['Description'] ?? ''));
    if ($descriptionSnippet !== '' && strlen($descriptionSnippet) > 48) {
        $descriptionSnippet = substr($descriptionSnippet, 0, 45) . '…';
    }

    $corroborateHint = $hasCorroborated
        ? 'You corroborated this report.'
        : ($isOwnReport
            ? 'You reported this incident.'
            : 'Community confirmations help LGU prioritize. Three or more may escalate review.');
    $canManageCaseStatus = role_can_manage_cases($userRole);
    $showVetDogLink = $userRole === 'Veterinarian' && $dogId > 0;
    $showClaimStray = in_array($userRole, ['Rescue Organization', 'Admin'], true) && $type === 'Injured Stray';
    $caseStages = ['Received', 'Under Investigation', 'Action Taken', 'Resolved', 'Referred'];
    ?>
    <div class="feed-incident-card-wrap">
        <article class="incident-card feed-incident-card card-bordered" data-incident-id="<?= $incidentId ?>">
            <div class="card-body feed-incident-card-body">
                <div class="feed-incident-header">
                    <div class="feed-incident-icon <?= htmlspecialchars($severitySurfaceClass) ?>" aria-hidden="true">
                        <i data-lucide="<?= htmlspecialchars($meta['icon']) ?>"></i>
                    </div>
                    <div class="feed-incident-meta">
                        <div class="feed-incident-title-row">
                            <a href="incident.php?id=<?= $incidentId ?>" class="feed-incident-type"><?= htmlspecialchars($meta['label']) ?></a>
                            <?= severity_badge_html($severity, false) ?>
                        </div>
                        <div class="feed-incident-location"><?= htmlspecialchars($displayLocation) ?></div>
                    </div>
                    <div class="feed-incident-header-end">
                        <span class="badge badge-with-dot feed-incident-status <?= htmlspecialchars($statusMeta['class']) ?>"
                              data-status-badge="<?= $incidentId ?>">
                            <?php if ($statusMeta['label'] === 'Resolved'): ?>
                                <i data-lucide="check" style="width:12px;height:12px;"></i>
                            <?php else: ?>
                                <span class="badge-dot" aria-hidden="true"></span>
                            <?php endif; ?>
                            <?= htmlspecialchars($statusMeta['label']) ?>
                        </span>
                        <a href="incident.php?id=<?= $incidentId ?>" class="feed-incident-open" aria-label="Open full incident details">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="feed-incident-media incident-card-tiles<?= $photoPath === null ? ' feed-incident-media--no-photo' : ' incident-card-tiles--has-photo' ?>">
                    <a href="incident.php?id=<?= $incidentId ?>" class="feed-incident-media-tile feed-incident-media-tile--photo incident-card-tile-photo" aria-label="View incident photo evidence">
                        <?php if ($photoPath !== null): ?>
                            <img src="<?= htmlspecialchars($photoPath) ?>" alt="Incident photo evidence" loading="lazy">
                        <?php else: ?>
                            <i data-lucide="image" aria-hidden="true"></i>
                        <?php endif; ?>
                    </a>
                    <a href="map.php" class="feed-incident-media-tile feed-incident-media-tile--map incident-card-tile-map<?= $mapThumbnail === null ? ' is-map-fallback' : ' has-map-preview' ?>" aria-label="View incident location on map">
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
                </div>

                <div class="incident-card-footer feed-incident-footer">
                    <div class="feed-incident-footer-row feed-incident-footer-row--meta">
                        <div class="feed-incident-corroborate">
                            <?php if ($hasCorroborated): ?>
                                <div class="chip chip-outline corroborated is-corroborated" title="<?= htmlspecialchars($corroborateHint) ?>">
                                    <i data-lucide="thumbs-up" style="width:14px;height:14px;color:var(--tea-green);"></i>
                                    <?= $corroborateCount ?>
                                </div>
                            <?php elseif ($canCorroborate): ?>
                                <button type="button"
                                        class="chip chip-outline corroborate-btn"
                                        data-corroborate="<?= $incidentId ?>"
                                        title="<?= htmlspecialchars($corroborateHint) ?>">
                                    <i data-lucide="thumbs-up" style="width:14px;height:14px;color:var(--air-force);"></i>
                                    <?= $corroborateCount ?>
                                </button>
                            <?php else: ?>
                                <div class="chip chip-outline corroborated"
                                     title="<?= htmlspecialchars($corroborateHint) ?>">
                                    <i data-lucide="thumbs-up" style="width:14px;height:14px;color:var(--air-force);"></i>
                                    <?= $corroborateCount ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <span class="feed-incident-date text-xs text-muted" title="<?= htmlspecialchars($timeAgo) ?>">
                            <?= htmlspecialchars($fullTimestamp) ?><?php if ($descriptionSnippet !== ''): ?> · <?= htmlspecialchars($descriptionSnippet) ?><?php endif; ?>
                        </span>
                    </div>

                    <?php if ($canManageCaseStatus): ?>
                        <div class="feed-incident-footer-row feed-incident-footer-row--status">
                            <label class="sr-only" for="feed-case-status-<?= $incidentId ?>">Update case status</label>
                            <select class="case-status-select feed-incident-status-select text-xs"
                                    id="feed-case-status-<?= $incidentId ?>"
                                    data-case-status="<?= $incidentId ?>"
                                    aria-label="Update case status">
                                <?php foreach ($caseStages as $stage):
                                    $optionLabel = $stage === 'Under Investigation' ? 'Investigating' : $stage; ?>
                                    <option value="<?= htmlspecialchars($stage) ?>" <?= ($incident['CaseStatus'] ?? '') === $stage ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($optionLabel) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if ($showVetDogLink || $showClaimStray): ?>
                        <div class="feed-incident-footer-row feed-incident-footer-row--action">
                            <?php if ($showVetDogLink): ?>
                                <a href="dog-profile.php?id=<?= $dogId ?>" class="btn-ghost btn-sm feed-incident-action-btn">View Dog Record</a>
                            <?php endif; ?>
                            <?php if ($showClaimStray): ?>
                                <button type="button"
                                        class="btn-ghost btn-sm feed-incident-action-btn claim-stray-btn"
                                        data-claim-stray="<?= $incidentId ?>">
                                    Claim Stray Case
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    </div>
    <?php
}
