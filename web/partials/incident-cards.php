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
        <article class="incident-card incident-skeleton card-bordered" aria-hidden="true">
            <div class="accent skeleton-shimmer"></div>
            <div class="card-body" style="flex:1;">
                <div class="skeleton-line skeleton-shimmer" style="width:30%;height:20px;margin-bottom:12px;"></div>
                <div class="skeleton-line skeleton-shimmer" style="width:85%;height:18px;margin-bottom:8px;"></div>
                <div class="skeleton-line skeleton-shimmer" style="width:60%;height:14px;"></div>
                <div class="skeleton-line skeleton-shimmer" style="width:100%;height:36px;margin-top:16px;"></div>
            </div>
        </article>
    <?php endfor;
}

/**
 * @param array<string, mixed> $incident
 */
function render_single_incident_card(array $incident, string $userRole, int $userId): void
{
    $type = normalize_incident_type((string) $incident['IncidentType']);
    $meta = incident_type_meta($type);

    $title = generate_incident_title($type, (string) $incident['Location']);
    $timeAgo = time_elapsed_string((string) $incident['Date']);
    $statusMeta = case_status_meta($incident['CaseStatus'] ?? null);
    $corroborateCount = (int) ($incident['corroborate_count'] ?? 0);
    $incidentId = (int) $incident['IncidentID'];
    $reporterId = (int) ($incident['reporter_id'] ?? 0);
    $hasCorroborated = (int) ($incident['user_corroborated'] ?? 0) === 1;
    $isOwnReport = $reporterId === $userId;
    $canCorroborate = !$isOwnReport && !$hasCorroborated;
    $dogId = isset($incident['dog_id']) ? (int) $incident['dog_id'] : 0;
    $fullTimestamp = date('M j, Y g:i A', strtotime((string) $incident['Date']));
    $reporterName = (string) ($incident['reporter_name'] ?? 'Anonymous');
    ?>
    <article class="incident-card card-bordered" data-incident-id="<?= $incidentId ?>">
        <div class="accent <?= htmlspecialchars($meta['accent']) ?>"></div>
        <div class="card-body" style="flex:1;">
            <div class="flex justify-between items-center mb-md" style="margin-bottom:10px;">
                <span class="badge <?= htmlspecialchars($meta['badge']) ?>"><?= htmlspecialchars($meta['label']) ?></span>
                    <span class="text-xs text-muted incident-card-time"><?= htmlspecialchars($timeAgo) ?></span>
            </div>
            <div class="flex gap-md" style="gap:11px;">
                <div class="icon-box icon-box-md"><i data-lucide="<?= htmlspecialchars($meta['icon']) ?>"></i></div>
                <div class="flex-1" style="min-width:0;">
                    <a href="incident.php?id=<?= $incidentId ?>" class="incident-card-title"><?= htmlspecialchars($title) ?></a>
                    <div class="text-xs text-muted mt-sm flex items-center gap-sm" style="margin-top:3px;">
                        <i data-lucide="map-pin" style="width:13px;height:13px;"></i>
                        <?= htmlspecialchars((string) $incident['Location']) ?>
                    </div>
                </div>
            </div>

            <button type="button" class="incident-details-toggle text-sm" data-details-toggle aria-expanded="false">
                <i data-lucide="chevron-down" class="chevron" style="width:14px;height:14px;"></i><span>More details</span>
            </button>
            <div class="incident-details-panel" hidden>
                <p class="text-xs text-muted" style="margin:0 0 4px;">Reported by <?= htmlspecialchars($reporterName) ?></p>
                <p class="text-xs text-muted" style="margin:0 0 8px;"><?= htmlspecialchars($fullTimestamp) ?></p>
                <?php if ($dogId > 0 && !empty($incident['DogName'])): ?>
                    <a href="dog-profile.php?id=<?= $dogId ?>" class="text-sm" style="font-weight:700;color:var(--air-force);">
                        View dog: <?= htmlspecialchars((string) $incident['DogName']) ?>
                        <?php if (!empty($incident['Breed'])): ?>
                            (<?= htmlspecialchars((string) $incident['Breed']) ?>)
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($incident['Description'])): ?>
                    <p class="text-sm" style="margin-top:8px;"><?= htmlspecialchars((string) $incident['Description']) ?></p>
                <?php endif; ?>
            </div>

            <div class="incident-card-footer flex justify-between items-center">
                <div class="incident-card-actions flex items-center gap-sm" style="flex-wrap:wrap;">
                    <?php if ($hasCorroborated): ?>
                        <div class="chip chip-outline corroborated is-corroborated" title="You corroborated this">
                            <i data-lucide="thumbs-up" style="width:14px;height:14px;color:var(--tea-green);"></i>
                            Corroborate · <?= $corroborateCount ?>
                        </div>
                    <?php elseif ($canCorroborate): ?>
                        <button type="button"
                                class="chip chip-outline corroborate-btn"
                                data-corroborate="<?= $incidentId ?>">
                            <i data-lucide="thumbs-up" style="width:14px;height:14px;color:var(--air-force);"></i>
                            Corroborate · <?= $corroborateCount ?>
                        </button>
                    <?php else: ?>
                        <div class="chip chip-outline corroborated"
                             <?= $isOwnReport ? 'title="You reported this incident"' : '' ?>>
                            <i data-lucide="thumbs-up" style="width:14px;height:14px;color:var(--air-force);"></i>
                            Corroborate · <?= $corroborateCount ?>
                        </div>
                    <?php endif; ?>

                    <?php if (in_array($userRole, ['LGU Official', 'Admin'], true)): ?>
                        <select class="case-status-select text-xs" data-case-status="<?= $incidentId ?>" aria-label="Update case status">
                            <option value="Received" <?= ($incident['CaseStatus'] ?? '') === 'Received' ? 'selected' : '' ?>>Received</option>
                            <option value="Under Investigation" <?= ($incident['CaseStatus'] ?? '') === 'Under Investigation' ? 'selected' : '' ?>>Investigating</option>
                            <option value="Resolved" <?= ($incident['CaseStatus'] ?? '') === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                            <option value="Referred" <?= ($incident['CaseStatus'] ?? '') === 'Referred' ? 'selected' : '' ?>>Referred</option>
                        </select>
                    <?php endif; ?>

                    <?php if ($userRole === 'Veterinarian' && $dogId > 0): ?>
                        <a href="dog-profile.php?id=<?= $dogId ?>" class="btn-ghost btn-sm">View Dog Record</a>
                    <?php endif; ?>

                    <?php if (in_array($userRole, ['Rescue Organization', 'Admin'], true) && $type === 'Injured Stray'): ?>
                        <button type="button" class="btn-ghost btn-sm claim-stray-btn" data-claim-stray="<?= $incidentId ?>">
                            Claim Stray Case
                        </button>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-sm">
                    <span class="badge badge-with-dot <?= htmlspecialchars($statusMeta['class']) ?>"
                          data-status-badge="<?= $incidentId ?>">
                        <?php if ($statusMeta['label'] === 'Resolved'): ?>
                            <i data-lucide="check" style="width:12px;height:12px;"></i>
                        <?php else: ?>
                            <span class="badge-dot" aria-hidden="true"></span>
                        <?php endif; ?>
                        <?= htmlspecialchars($statusMeta['label']) ?>
                    </span>
                    <span class="text-xs text-muted incident-distance" title="Same barangay">Same brgy.</span>
                </div>
            </div>
        </div>
    </article>
    <?php
}
