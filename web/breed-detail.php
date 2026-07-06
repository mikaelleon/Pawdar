<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/breeds.php';
require_once __DIR__ . '/includes/breed-content.php';
require_once __DIR__ . '/includes/breed-media.php';
require_once __DIR__ . '/includes/dogs.php';

$pdo = db();
ensure_breed_directory_metadata($pdo);

$slug = trim((string) ($_GET['slug'] ?? ''));
$breedId = (int) ($_GET['id'] ?? 0);
$breed = null;

if ($slug !== '') {
    $breed = fetch_breed_by_slug($pdo, $slug);
} elseif ($breedId > 0) {
    $breed = fetch_breed_by_id($pdo, $breedId);
    if ($breed && !empty($breed['slug'])) {
        header('Location: breed-detail.php?slug=' . urlencode((string) $breed['slug']) . (isset($_GET['from']) ? '&from=' . urlencode((string) $_GET['from']) : ''));
        exit;
    }
}

if (!$breed) {
    header('Location: breeds.php');
    exit;
}

$backHref = trim((string) ($_GET['from'] ?? ''));
if ($backHref === '' || !str_starts_with($backHref, 'breeds.php')) {
    $backHref = 'breeds.php';
}

$userRole = current_user_role();
$dogsOfBreed = fetch_dogs_by_breed_id($pdo, (int) $breed['breed_id']);
$care = breed_care_profile($breed);
$adoption = breed_adoption_guidance($breed);
$gallery = breed_gallery_urls($breed);
$heroImage = $gallery[0] ?? breed_thumbnail_url($breed);
$heroFallback = breed_silhouette_url($breed);
$weightLabel = breed_weight_display($breed);
$traits = [
    ['shield', 'Loyalty', (int) ($breed['loyalty_score'] ?? 3)],
    ['zap', 'Energy', (int) ($breed['energy_score'] ?? 3)],
    ['smile', 'Friendliness', (int) ($breed['friendliness_score'] ?? 3)],
];

app_layout_start('breeds', (string) $breed['breed_name'], [
    'showSearch' => false,
    'mobileHeader' => 'back',
    'backTitle' => 'Breed Directory',
    'backHref' => $backHref,
    'scripts' => ['assets/js/breed-detail.js'],
    'breadcrumbs' => [
        ['label' => 'Breed Directory', 'url' => $backHref],
        ['label' => (string) $breed['breed_name']],
    ],
]);
?>

<div class="breed-detail-page breed-page-enter">
    <a href="<?= htmlspecialchars($backHref) ?>" class="registry-back"><i data-lucide="arrow-left"></i> Back to Breed Directory</a>

    <section class="breed-detail-hero-wrap card card-bordered">
        <img src="<?= htmlspecialchars($heroImage) ?>"
             alt="<?= htmlspecialchars((string) $breed['breed_name']) ?>"
             class="breed-detail-hero-img"
             data-fallback="<?= htmlspecialchars($heroFallback) ?>"
             onerror="if(this.dataset.fallback){this.onerror=null;this.src=this.dataset.fallback;}">
        <div class="breed-detail-hero-overlay">
            <div class="breed-detail-hero-badges">
                <?php if (breed_is_local($breed)): ?><span class="badge badge-owned">Batangas match</span><?php endif; ?>
                <?php if ((int) ($breed['registered_count'] ?? 0) > 0): ?>
                    <span class="badge badge-verified"><?= (int) $breed['registered_count'] ?> registered on Pawdar</span>
                <?php endif; ?>
            </div>
            <h1 class="breed-detail-title"><?= htmlspecialchars((string) $breed['breed_name']) ?></h1>
            <p class="breed-detail-subtitle">
                <?= htmlspecialchars((string) $breed['size_category']) ?>
                · <?= htmlspecialchars(breed_group_label($breed)) ?>
                <?php if ($weightLabel !== ''): ?> · <?= htmlspecialchars($weightLabel) ?><?php endif; ?>
                <?php if (!empty($breed['lifespan'])): ?> · <?= htmlspecialchars((string) $breed['lifespan']) ?><?php endif; ?>
            </p>
        </div>
    </section>

    <?php if (count($gallery) > 1): ?>
        <div class="breed-gallery-strip">
            <?php foreach ($gallery as $index => $url): ?>
                <button type="button" class="breed-gallery-thumb<?= $index === 0 ? ' is-active' : '' ?>" data-gallery-thumb data-src="<?= htmlspecialchars($url) ?>">
                    <img src="<?= htmlspecialchars($url) ?>" alt="" loading="lazy">
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="breed-detail-sections">
        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Known For</div>
            <p class="text-sm"><?= nl2br(htmlspecialchars(breed_known_for_text($breed))) ?></p>
        </div>

        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Temperament</div>
            <?php foreach ($traits as [$icon, $label, $filled]): ?>
                <div class="breed-trait-row">
                    <div class="flex items-center gap-md mb-sm">
                        <i data-lucide="<?= $icon ?>" style="color:var(--muted-teal);"></i>
                        <div class="flex-1 text-sm" style="font-weight:500;"><?= $label ?></div>
                        <span class="text-xs trait-band <?= breed_score_band($filled)['class'] ?>"><?= breed_score_band($filled)['label'] ?></span>
                    </div>
                    <div class="rating-dots mb-sm">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <span class="rating-dot<?= $i >= $filled ? ' empty' : '' ?>"></span>
                        <?php endfor; ?>
                    </div>
                    <p class="text-xs text-muted trait-qualifier"><?= htmlspecialchars(breed_trait_qualifier($label, $filled)) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Common Traits &amp; Care Needs</div>
            <dl class="dog-detail-grid">
                <div><dt class="text-xs text-muted">Health risks</dt><dd class="text-sm"><?= htmlspecialchars($care['health']) ?></dd></div>
                <div><dt class="text-xs text-muted">Grooming</dt><dd class="text-sm"><?= htmlspecialchars($care['grooming']) ?></dd></div>
                <div><dt class="text-xs text-muted">Behavior</dt><dd class="text-sm"><?= htmlspecialchars($care['behavior']) ?></dd></div>
                <div><dt class="text-xs text-muted">Lifespan</dt><dd class="text-sm"><?= htmlspecialchars($care['lifespan']) ?></dd></div>
            </dl>
        </div>

        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Things to Consider Before Adopting</div>
            <p class="text-sm mb-md"><?= htmlspecialchars($adoption['summary']) ?></p>
            <ul class="breed-bullet-list text-sm">
                <li><strong>Living space:</strong> <?= htmlspecialchars($adoption['space']) ?></li>
                <li><strong>Children &amp; pets:</strong> <?= htmlspecialchars($adoption['children']) ?></li>
                <li><strong>Owner experience:</strong> <?= htmlspecialchars($adoption['experience']) ?></li>
                <li><strong>Time &amp; cost:</strong> <?= htmlspecialchars($adoption['commitment']) ?></li>
            </ul>
        </div>

        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Legal Considerations</div>
            <h3 class="text-sm" style="font-weight:700;margin:0 0 8px;">Global context</h3>
            <p class="text-sm text-muted mb-md"><?= htmlspecialchars(breed_legal_global_text($breed)) ?></p>
            <h3 class="text-sm" style="font-weight:700;margin:0 0 8px;">Philippines (RA 9482)</h3>
            <p class="text-sm text-muted mb-md"><?= htmlspecialchars(breed_legal_philippines_text($breed)) ?></p>
            <p class="text-xs text-muted"><em><?= htmlspecialchars(breed_legal_disclaimer()) ?></em></p>
        </div>

        <div class="card card-bordered card-body">
            <div class="label-upper mb-md">Dogs of This Breed on Pawdar</div>
            <?php if (count($dogsOfBreed) === 0): ?>
                <div class="breed-registry-empty">
                    <svg viewBox="0 0 120 100" width="120" height="100" aria-hidden="true" class="breed-empty-dog-art">
                        <ellipse cx="60" cy="88" rx="36" ry="8" fill="var(--tea-green)" opacity="0.3"/>
                        <circle cx="60" cy="42" r="18" fill="var(--muted-teal)" opacity="0.45"/>
                        <ellipse cx="60" cy="68" rx="22" ry="18" fill="var(--air-force)" opacity="0.35"/>
                        <circle cx="54" cy="40" r="2" fill="#fff"/><circle cx="66" cy="40" r="2" fill="#fff"/>
                    </svg>
                    <p class="text-sm text-muted">No dogs of this breed registered yet.</p>
                    <?php if ($userRole === 'Dog Owner'): ?>
                        <a href="register_dog.php" class="btn-primary btn-sm">Register your dog</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="breed-dog-preview-grid">
                    <?php foreach ($dogsOfBreed as $dog):
                        $dogThumb = !empty($dog['photo_path']) ? (string) $dog['photo_path'] : breed_silhouette_url($breed);
                    ?>
                        <a href="dog-profile.php?id=<?= (int) $dog['dog_id'] ?>" class="breed-dog-preview-card card-hoverable">
                            <img src="<?= htmlspecialchars($dogThumb) ?>" alt="" class="breed-dog-preview-photo" loading="lazy">
                            <div>
                                <div class="text-sm" style="font-weight:600;"><?= htmlspecialchars((string) $dog['DogName']) ?></div>
                                <div class="text-xs text-muted"><?= htmlspecialchars((string) $dog['owner_name']) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php app_layout_end([]); ?>
