<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/dogs.php';
require_role(['Rescue Organization', 'Admin']);

$pdo = db();
$barangay = (string) $_SESSION['user_barangay'];
$strays = fetch_rescue_stray_incidents($pdo, $barangay);

app_layout_start('rescue-board', 'Rescue Board', ['showSearch' => false]);
?>

<div class="feed-header">
    <div>
        <h1 class="feed-title">Rescue Board</h1>
        <p class="text-sm text-muted">Injured stray cases in Brgy. <?= htmlspecialchars($barangay) ?></p>
    </div>
</div>

<?php if (count($strays) === 0): ?>
    <div class="feed-empty-state">
        <p class="feed-empty-title">No injured stray reports</p>
        <p class="text-sm text-muted">New cases appear here when reported on the feed.</p>
    </div>
<?php else: ?>
    <div class="flex flex-col gap-md">
        <?php foreach ($strays as $incident):
            $caseStatus = (string) ($incident['CaseStatus'] ?? 'Received');
            $badgeClass = match ($caseStatus) {
                'Resolved', 'Closed' => 'badge-resolved',
                'Under Investigation' => 'badge-investigating',
                default => 'badge-received',
            };
        ?>
            <div class="incident-card card-bordered">
                <div class="accent accent-teal"></div>
                <div class="card-body" style="flex:1;">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-muted"><?= htmlspecialchars(time_elapsed_string((string) $incident['Date'])) ?></span>
                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($caseStatus) ?></span>
                    </div>
                    <div style="font-weight:800;font-size:16px;margin-top:7px;">Injured Stray</div>
                    <div class="text-sm text-muted" style="margin-top:6px;">
                        <?= htmlspecialchars((string) $incident['Location']) ?>
                    </div>
                    <p class="text-sm" style="margin-top:8px;"><?= htmlspecialchars((string) $incident['Description']) ?></p>
                    <div class="text-xs text-muted" style="margin-top:10px;">
                        Reported by <?= htmlspecialchars((string) $incident['reporter_name']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="text-sm text-muted mt-md">Claim stray incidents directly from feed cards.</p>
<?php endif; ?>

<?php app_layout_end([]); ?>
