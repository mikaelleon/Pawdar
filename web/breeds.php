<?php
require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/breeds.php';
require_once __DIR__ . '/includes/dogs.php';

$pdo = db();
$sizeFilter = trim((string) ($_GET['size'] ?? 'all'));
$query = trim((string) ($_GET['q'] ?? ''));
$counts = fetch_breed_size_counts($pdo);
$breeds = fetch_all_breeds($pdo, $sizeFilter === 'all' ? null : $sizeFilter, $query !== '' ? $query : null);
$selectedId = (int) ($_GET['breed'] ?? ($breeds[0]['breed_id'] ?? 0));
$selected = fetch_breed_by_id($pdo, $selectedId) ?? ($breeds ? fetch_breed_by_id($pdo, (int) $breeds[0]['breed_id']) : null);
$dogsOfBreed = $selected ? fetch_dogs_by_breed_id($pdo, (int) $selected['breed_id']) : [];
$userRole = current_user_role();

app_layout_start('breeds', 'Breed Directory', [
    'topbarTitle' => 'Breed Directory',
    'searchPlaceholder' => 'Search breeds…',
    'scripts' => ['assets/js/breeds.js'],
]);
?>

<div class="split-layout" data-breeds-page data-size-filter="<?= htmlspecialchars($sizeFilter) ?>" data-selected-breed="<?= (int) ($selected['breed_id'] ?? 0) ?>">
    <div class="split-main">
        <div class="search-bar search-bar-light hidden-mobile mb-md">
            <i data-lucide="search"></i>
            <input type="search" id="breed-search" placeholder="Search breeds…" value="<?= htmlspecialchars($query) ?>" style="border:none;background:transparent;flex:1;font-family:inherit;font-size:14px;">
        </div>
        <div class="chips-row mb-md" data-size-chips>
            <?php
            $chips = ['all' => 'All Sizes', 'Small' => 'Small', 'Medium' => 'Medium', 'Large' => 'Large'];
            foreach ($chips as $slug => $label):
                $count = $counts[$slug] ?? $counts['all'];
            ?>
                <button type="button" class="chip breed-size-chip<?= $sizeFilter === $slug ? ' chip-active' : ' chip-outline' ?>" data-size="<?= htmlspecialchars($slug) ?>">
                    <?= htmlspecialchars($label) ?> (<?= (int) $count ?>)
                </button>
            <?php endforeach; ?>
        </div>

        <div class="breed-grid" data-breed-grid>
            <?php if (count($breeds) === 0): ?>
                <div class="feed-empty-state" data-breed-empty>
                    <p class="feed-empty-title">No breeds found<?= $query !== '' ? ' for \'' . htmlspecialchars($query) . '\'' : '' ?></p>
                    <button type="button" class="btn-outline btn-sm" data-clear-breed-search>Clear search</button>
                </div>
            <?php else: ?>
                <?php foreach ($breeds as $breed):
                    $active = $selected && (int) $breed['breed_id'] === (int) $selected['breed_id'];
                    $color = string_color_class((string) $breed['breed_name']);
                ?>
                    <button type="button"
                       class="breed-card card-hoverable<?= $active ? ' is-selected' : '' ?>"
                       data-breed-card
                       data-breed-id="<?= (int) $breed['breed_id'] ?>"
                       data-size="<?= htmlspecialchars((string) $breed['size_category']) ?>"
                       data-name="<?= htmlspecialchars(strtolower((string) $breed['breed_name'])) ?>">
                        <?php if ($active): ?><span class="role-card-check"><i data-lucide="check"></i></span><?php endif; ?>
                        <div class="breed-card-image <?= $color ?>"><i data-lucide="dog" style="width:46px;height:46px;color:var(--air-force);"></i></div>
                        <div class="card-body">
                            <div style="font-weight:500;font-size:16px;"><?= htmlspecialchars((string) $breed['breed_name']) ?></div>
                            <div class="flex items-center gap-sm mt-sm">
                                <span class="badge badge-owned"><?= htmlspecialchars((string) $breed['size_category']) ?></span>
                                <span class="text-xs text-muted"><?= htmlspecialchars((string) ($breed['temperament_notes'] ?? '')) ?></span>
                            </div>
                            <?php if ($active): ?><div style="font-weight:500;font-size:13px;margin-top:10px;color:var(--burnt-peach);">Viewing →</div><?php endif; ?>
                        </div>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($selected): ?>
    <aside class="split-panel" data-breed-detail>
        <a href="breeds.php" class="registry-back hidden-desktop"><i data-lucide="arrow-left"></i> Back to list</a>
        <div class="card card-bordered card-body">
            <h2 style="font-weight:500;font-size:22px;margin:0;" data-breed-name><?= htmlspecialchars((string) $selected['breed_name']) ?></h2>
            <span class="badge badge-owned mt-sm" data-breed-meta>
                <?= htmlspecialchars((string) $selected['size_category']) ?>
                · <?= htmlspecialchars((string) ($selected['weight_range'] ?? '')) ?>
                · <?= htmlspecialchars((string) ($selected['lifespan'] ?? '')) ?>
            </span>

            <div class="label-upper mt-md mb-md">Temperament</div>
            <p class="text-sm text-muted" data-breed-temperament><?= htmlspecialchars((string) ($selected['temperament_notes'] ?? '')) ?></p>
            <?php
            $traits = [
                ['shield', 'Loyalty', (int) $selected['loyalty_score']],
                ['zap', 'Energy', (int) $selected['energy_score']],
                ['smile', 'Friendliness', (int) $selected['friendliness_score']],
            ];
            foreach ($traits as [$icon, $label, $filled]): ?>
                <div class="flex items-center gap-md mb-md">
                    <i data-lucide="<?= $icon ?>" style="color:var(--muted-teal);"></i>
                    <div class="flex-1 text-sm" style="font-weight:500;"><?= $label ?></div>
                    <div>
                        <div class="rating-dots">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <span class="rating-dot<?= $i >= $filled ? ' empty' : '' ?>"></span>
                            <?php endfor; ?>
                        </div>
                        <div class="rating-scale-labels"><span>Low</span><span>High</span></div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="label-upper mt-md mb-md">Common Health Risks</div>
            <p class="text-sm health-risks-list" data-breed-health><?= htmlspecialchars((string) ($selected['common_health_risks'] ?? '')) ?></p>

            <div class="label-upper mt-md mb-md">Dogs of This Breed on Pawdar</div>
            <div class="flex flex-col gap-sm" data-breed-dogs>
                <?php if (count($dogsOfBreed) === 0): ?>
                    <div class="text-sm text-muted text-center" style="padding:12px;" data-breed-dogs-empty>
                        <i data-lucide="paw-print" style="width:18px;height:18px;margin-bottom:6px;"></i>
                        <div>No dogs of this breed registered yet</div>
                        <?php if ($userRole === 'Dog Owner'): ?>
                            <a href="registry.php" class="link-hover text-sm" style="display:inline-block;margin-top:8px;">Register your dog</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($dogsOfBreed as $dog):
                        $status = (string) ($dog['Status'] ?? 'Registered');
                        $tip = $status === 'Registered' ? 'Registration verified' : 'Awaiting admin review';
                    ?>
                        <a href="dog-profile.php?id=<?= (int) $dog['dog_id'] ?>" class="dog-breed-row" title="<?= htmlspecialchars($tip) ?>">
                            <div class="icon-box icon-box-sm" style="background:var(--muted-teal);color:#fff;width:32px;height:32px;"><i data-lucide="dog"></i></div>
                            <div class="flex-1"><div class="text-sm" style="font-weight:500;"><?= htmlspecialchars((string) $dog['DogName']) ?></div><div class="text-xs text-muted"><?= htmlspecialchars((string) $dog['owner_name']) ?></div></div>
                            <span class="badge <?= $status === 'Registered' ? 'badge-verified' : 'badge-investigating' ?>"><?= htmlspecialchars($status) ?></span>
                            <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--air-force);"></i>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </aside>
    <?php endif; ?>
</div>

<?php app_layout_end([]); ?>
