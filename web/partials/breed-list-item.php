<?php
/** @var array<string, mixed> $breed */
/** @var array<string, string|int> $listParams */
$thumb = breed_thumbnail_url($breed);
$fallback = breed_silhouette_url($breed);
$registered = (int) ($breed['registered_count'] ?? 0);
$slug = (string) ($breed['slug'] ?? breed_slug_from_name((string) $breed['breed_name']));
$href = 'breed-detail.php?slug=' . urlencode($slug) . '&from=' . urlencode(breeds_directory_url($listParams));
$isLocal = breed_is_local($breed);
$weightLabel = breed_weight_display($breed);
?>
<article class="breed-list-item card card-bordered card-hoverable"
         data-breed-list-item
         data-breed-id="<?= (int) $breed['breed_id'] ?>"
         tabindex="0">
    <div class="breed-list-compare-col" data-compare-toggle-wrap>
        <label class="breed-compare-check" title="Add to comparison (max 3)">
            <input type="checkbox" data-compare-breed="<?= (int) $breed['breed_id'] ?>" aria-label="Compare <?= htmlspecialchars((string) $breed['breed_name']) ?>">
            <span class="breed-compare-label">Compare</span>
        </label>
    </div>
    <a href="<?= htmlspecialchars($href) ?>" class="breed-list-link">
        <div class="breed-list-media">
            <img src="<?= htmlspecialchars($thumb) ?>"
                 alt=""
                 class="breed-list-photo"
                 loading="lazy"
                 data-fallback="<?= htmlspecialchars($fallback) ?>"
                 onerror="if(this.dataset.fallback){this.onerror=null;this.src=this.dataset.fallback;}">
        </div>
        <div class="breed-list-body">
            <div class="breed-list-head">
                <h2 class="breed-list-name"><?= htmlspecialchars((string) $breed['breed_name']) ?></h2>
                <div class="breed-list-badges">
                    <?php if ($isLocal): ?>
                        <span class="badge badge-owned">Batangas match</span>
                    <?php endif; ?>
                    <?php if ($registered > 0): ?>
                        <span class="badge badge-verified" title="Dogs registered on Pawdar"><?= $registered ?> on Pawdar</span>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-sm text-muted breed-list-meta">
                <?= htmlspecialchars((string) $breed['size_category']) ?>
                · <?= htmlspecialchars(breed_group_label($breed)) ?>
                <?php if ($weightLabel !== ''): ?>
                    · <?= htmlspecialchars($weightLabel) ?>
                <?php endif; ?>
            </p>
            <p class="text-sm breed-list-blurb"><?= htmlspecialchars(breed_list_blurb(breed_known_for_text($breed))) ?></p>
        </div>
        <i data-lucide="chevron-right" class="breed-list-chevron" aria-hidden="true"></i>
    </a>
</article>
