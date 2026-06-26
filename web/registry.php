<?php
require_once __DIR__ . '/../includes/app-layout.php';
require_once __DIR__ . '/../includes/dogs.php';

$pdo = db();
$stmt = $pdo->query('
    SELECT d.dog_id, d.DogName, d.Breed, d.RegistryID, d.Status, u.Name AS owner_name
    FROM dog d
    INNER JOIN user u ON u.UserID = d.UserID
    ORDER BY d.DogName ASC
');
$dogs = $stmt->fetchAll();

app_layout_start('registry', 'Dog Registry', ['showSearch' => true, 'searchPlaceholder' => 'Search dogs…']);
?>

<a href="feed.php" class="registry-back"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back to Feed</a>
<h1 class="feed-title">Dog Registry</h1>
<p class="text-sm text-muted" style="margin-bottom:18px;">Registered dogs in your community.</p>

<?php if (count($dogs) === 0): ?>
    <div class="feed-empty-state">
        <p class="feed-empty-title">No dogs registered yet</p>
        <a href="signup.php" class="btn-primary btn-sm">Register your dog</a>
    </div>
<?php else: ?>
    <div class="flex flex-col gap-md">
        <?php foreach ($dogs as $dog): ?>
            <a href="dog-profile.php?id=<?= (int) $dog['dog_id'] ?>" class="card card-bordered card-body card-hoverable flex items-center gap-md">
                <div class="icon-box icon-box-md <?= htmlspecialchars(string_color_class((string) $dog['Breed'])) ?>">
                    <i data-lucide="dog"></i>
                </div>
                <div class="flex-1">
                    <div style="font-weight:500;font-size:15px;"><?= htmlspecialchars((string) $dog['DogName']) ?></div>
                    <div class="text-xs text-muted"><?= htmlspecialchars((string) $dog['Breed']) ?> · <?= htmlspecialchars((string) $dog['owner_name']) ?></div>
                </div>
                <span class="badge <?= ($dog['Status'] ?? '') === 'Registered' ? 'badge-verified' : 'badge-investigating' ?>">
                    <?= htmlspecialchars((string) ($dog['Status'] ?? 'Registered')) ?>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php app_layout_end([]); ?>
