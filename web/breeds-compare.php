<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/breeds.php';
require_once __DIR__ . '/includes/breed-content.php';
require_once __DIR__ . '/includes/breed-media.php';

$pdo = db();
ensure_breed_directory_metadata($pdo);

$idsRaw = trim((string) ($_GET['ids'] ?? ''));
$ids = array_values(array_filter(array_map('intval', explode(',', $idsRaw)), static fn (int $id): bool => $id > 0));
$ids = array_slice(array_unique($ids), 0, 3);

if ($ids === []) {
    header('Location: breeds.php');
    exit;
}

$breeds = fetch_breeds_by_ids($pdo, $ids);

app_layout_start('breeds', 'Compare Breeds', [
    'showSearch' => false,
    'topbarTitle' => 'Compare Breeds',
    'breadcrumbs' => [
        ['label' => 'Breed Directory', 'url' => 'breeds.php'],
        ['label' => 'Compare'],
    ],
]);
?>

<div class="breed-compare-page">
    <div class="breed-directory-header">
        <div>
            <h1 class="feed-title">Compare breeds</h1>
            <p class="text-sm text-muted">Side-by-side traits, care notes, and adoption guidance (up to 3 breeds).</p>
        </div>
        <a href="breeds.php" class="btn-outline btn-sm">Back to directory</a>
    </div>

    <div class="breed-compare-grid">
        <?php foreach ($breeds as $breed):
            $care = breed_care_profile($breed);
            $adoption = breed_adoption_guidance($breed);
            $thumb = breed_thumbnail_url($breed);
            $thumbColor = string_color_class((string) $breed['breed_name']);
        ?>
            <article class="card card-bordered card-body breed-compare-col">
                <?php if ($thumb): ?>
                    <img src="<?= htmlspecialchars($thumb) ?>" alt="" class="breed-compare-photo" loading="lazy">
                <?php else: ?>
                    <div class="breed-compare-photo breed-compare-photo--placeholder dog-photo-placeholder <?= htmlspecialchars($thumbColor) ?>" aria-hidden="true">
                        <i data-lucide="dog"></i>
                    </div>
                <?php endif; ?>
                <h2 class="text-lg" style="font-weight:700;margin:12px 0 4px;">
                    <a href="breed-detail.php?slug=<?= urlencode((string) $breed['slug']) ?>"><?= htmlspecialchars((string) $breed['breed_name']) ?></a>
                </h2>
                <p class="text-xs text-muted mb-md"><?= htmlspecialchars((string) $breed['size_category']) ?> · <?= htmlspecialchars(breed_group_label($breed)) ?></p>

                <?php foreach (['Loyalty' => 'loyalty_score', 'Energy' => 'energy_score', 'Friendliness' => 'friendliness_score'] as $label => $key): ?>
                    <?php $score = (int) ($breed[$key] ?? 3); ?>
                    <div class="mb-md">
                        <div class="text-sm" style="font-weight:600;"><?= $label ?></div>
                        <div class="rating-dots mt-sm">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <span class="rating-dot<?= $i >= $score ? ' empty' : '' ?>"></span>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <p class="text-sm"><strong>Health:</strong> <?= htmlspecialchars(mb_strimwidth($care['health'], 0, 120, '…')) ?></p>
                <p class="text-sm mt-sm"><strong>Adoption:</strong> <?= htmlspecialchars(mb_strimwidth($adoption['space'], 0, 120, '…')) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<?php app_layout_end([]); ?>
