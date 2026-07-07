<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/breeds.php';
require_once __DIR__ . '/includes/breed-content.php';
require_once __DIR__ . '/includes/breed-media.php';

$pdo = db();
ensure_breed_directory_metadata($pdo);

$listParams = [
    'size' => trim((string) ($_GET['size'] ?? 'all')),
    'mood' => trim((string) ($_GET['mood'] ?? '')),
    'local' => trim((string) ($_GET['local'] ?? '')),
    'q' => trim((string) ($_GET['q'] ?? '')),
    'sort' => trim((string) ($_GET['sort'] ?? 'name_asc')),
    'page' => (int) ($_GET['page'] ?? 1),
];

$result = fetch_breeds_directory($pdo, $listParams);
$counts = fetch_breed_size_counts($pdo);
$moreFiltersOpen = $listParams['local'] === '1' || $listParams['mood'] !== '';

app_layout_start('breeds', 'Breed Directory', [
    'showSearch' => false,
    'showMobileSearch' => false,
    'scripts' => ['assets/js/breeds.js'],
    'breadcrumbs' => [['label' => 'Breed Directory']],
]);
?>

<div class="breed-directory" data-breeds-directory
     data-list-params="<?= htmlspecialchars(json_encode($listParams, JSON_THROW_ON_ERROR)) ?>">
    <header class="breed-directory-header">
        <div>
            <h1 class="feed-title">Breed Directory</h1>
            <p class="text-sm text-muted">Explore <?= (int) $counts['all'] ?> breeds — photos, temperament, adoption guidance, and local Pawdar registrations.</p>
        </div>
    </header>

    <form class="breed-directory-toolbar" method="get" action="breeds.php" data-breed-filter-form>
        <div class="search-bar search-bar-light breed-directory-search">
            <i data-lucide="search"></i>
            <input type="search" name="q" id="breed-search" placeholder="Search breeds…" value="<?= htmlspecialchars($listParams['q']) ?>" aria-label="Search breeds">
        </div>
        <input type="hidden" name="size" value="<?= htmlspecialchars($listParams['size']) ?>" data-size-input>
        <input type="hidden" name="mood" value="<?= htmlspecialchars($listParams['mood']) ?>" data-mood-input>
        <input type="hidden" name="local" value="<?= htmlspecialchars($listParams['local']) ?>" data-local-input>

        <div class="breed-directory-filters">
            <div class="breed-filter-primary">
                <div class="chips-row breed-filter-row breed-filter-sort-row">
                    <span class="breed-filter-row-label" id="breed-sort-label">Sort</span>
                    <select name="sort" id="breed-sort" class="registry-filter breed-sort-select" aria-labelledby="breed-sort-label">
                        <?php foreach (breed_sort_options() as $option): ?>
                            <option value="<?= htmlspecialchars($option['slug']) ?>" <?= $listParams['sort'] === $option['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($option['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="chips-row breed-filter-row" data-size-chips>
                    <span class="breed-filter-row-label">Size</span>
                    <?php
                    $sizeChips = [
                        'all' => 'All sizes',
                        'Small' => 'Small',
                        'Medium' => 'Medium',
                        'Large' => 'Large',
                    ];
                    foreach ($sizeChips as $slug => $label):
                        $active = $listParams['local'] !== '1' && $listParams['size'] === $slug;
                    ?>
                        <button type="button"
                                class="chip breed-filter-chip<?= $active ? ' chip-active' : ' chip-outline' ?>"
                                data-filter-size="<?= htmlspecialchars($slug) ?>">
                            <?= htmlspecialchars($label) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <button type="button"
                        class="chip breed-compare-mode-toggle chip-outline"
                        data-compare-mode-toggle
                        aria-pressed="false">
                    Compare
                </button>
            </div>

            <details class="breed-more-filters" data-breed-more-filters <?= $moreFiltersOpen ? 'open' : '' ?>>
                <summary class="breed-more-filters-toggle">More filters</summary>
                <div class="breed-more-filters-body">
                    <div class="chips-row breed-filter-row" data-origin-chips>
                        <span class="breed-filter-row-label">Origin</span>
                        <button type="button"
                                class="chip breed-filter-chip<?= $listParams['local'] !== '1' ? ' chip-active' : ' chip-outline' ?>"
                                data-filter-local="">
                            All breeds
                        </button>
                        <button type="button"
                                class="chip breed-filter-chip<?= $listParams['local'] === '1' ? ' chip-active' : ' chip-outline' ?>"
                                data-filter-local="1">
                            Local — Aspin (<?= (int) $counts['local'] ?>)
                        </button>
                    </div>

                    <div class="chips-row breed-filter-row" data-mood-chips>
                        <span class="breed-filter-row-label">Mood</span>
                        <button type="button" class="chip breed-mood-chip<?= $listParams['mood'] === '' ? ' chip-active' : ' chip-outline' ?>" data-filter-mood="">All moods</button>
                        <?php foreach (breed_mood_filters() as $mood): ?>
                            <button type="button"
                                    class="chip breed-mood-chip<?= $listParams['mood'] === $mood['slug'] ? ' chip-active' : ' chip-outline' ?>"
                                    data-filter-mood="<?= htmlspecialchars($mood['slug']) ?>">
                                <?= htmlspecialchars($mood['label']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </details>
        </div>
    </form>

    <div class="breed-grid" data-breed-list role="list">
        <?php if (count($result['rows']) === 0): ?>
            <div class="feed-empty-state breed-empty-state">
                <svg class="feed-empty-illustration" viewBox="0 0 200 160" aria-hidden="true" style="width:140px;">
                    <ellipse cx="100" cy="130" rx="70" ry="12" fill="var(--tea-green)" opacity="0.25"/>
                    <circle cx="75" cy="70" r="28" fill="var(--muted-teal)" opacity="0.35"/>
                    <circle cx="125" cy="70" r="28" fill="var(--air-force)" opacity="0.3"/>
                </svg>
                <p class="feed-empty-title">No breeds found<?= $listParams['q'] !== '' ? ' for \'' . htmlspecialchars($listParams['q']) . '\'' : '' ?></p>
                <a href="breeds.php" class="btn-outline btn-sm">Clear filters</a>
            </div>
        <?php else: ?>
            <?php foreach ($result['rows'] as $breed): ?>
                <?php require __DIR__ . '/partials/breed-list-item.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($result['total_pages'] > 1): ?>
        <nav class="breed-pagination" aria-label="Breed pages">
            <?php if ($result['page'] > 1): ?>
                <a class="btn-outline btn-sm" href="<?= htmlspecialchars(breeds_directory_url(array_merge($listParams, ['page' => $result['page'] - 1]))) ?>">Previous</a>
            <?php endif; ?>
            <label class="breed-page-jump-wrap text-sm text-muted">
                <span class="sr-only">Jump to page</span>
                <select class="breed-page-jump registry-filter" data-page-jump aria-label="Jump to page">
                    <?php for ($pageNum = 1; $pageNum <= $result['total_pages']; $pageNum++): ?>
                        <option value="<?= htmlspecialchars(breeds_directory_url(array_merge($listParams, ['page' => $pageNum]))) ?>"
                            <?= $pageNum === $result['page'] ? 'selected' : '' ?>>
                            Page <?= $pageNum ?> of <?= (int) $result['total_pages'] ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </label>
            <?php if ($result['page'] < $result['total_pages']): ?>
                <a class="btn-outline btn-sm" href="<?= htmlspecialchars(breeds_directory_url(array_merge($listParams, ['page' => $result['page'] + 1]))) ?>">Next</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

    <div class="breed-compare-bar" data-compare-bar hidden>
        <div class="breed-compare-bar-inner">
            <span class="text-sm" data-compare-status>Select breeds to compare (up to 3)</span>
            <div class="breed-compare-bar-actions">
                <button type="button" class="btn-outline btn-sm" data-compare-clear hidden>Clear</button>
                <a href="breeds-compare.php" class="btn-primary btn-sm" data-compare-open hidden>Compare selected →</a>
            </div>
        </div>
        <p class="text-xs breed-compare-hint" data-compare-hint hidden></p>
    </div>
</div>

<?php app_layout_end([]); ?>
