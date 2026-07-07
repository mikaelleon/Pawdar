<?php
/** @var array<string, mixed> $breed */
/** @var array<string, string|int> $listParams */
$thumb = breed_list_thumbnail_url($breed);
$fallback = breed_silhouette_url($breed);
$registered = (int) ($breed['registered_count'] ?? 0);
$slug = (string) ($breed['slug'] ?? breed_slug_from_name((string) $breed['breed_name']));
$href = 'breed-detail.php?slug=' . urlencode($slug) . '&from=' . urlencode(breeds_directory_url($listParams));
$isLocal = breed_is_local($breed);
$weightLabel = breed_weight_display($breed);
$blurb = breed_list_blurb(breed_known_for_text($breed));
$groupLabel = breed_group_label($breed);
$breedName = (string) $breed['breed_name'];
?>
<article class="breed-grid-card card card-bordered card-hoverable"
         data-breed-list-item
         data-breed-id="<?= (int) $breed['breed_id'] ?>"
         tabindex="0">
    <div class="breed-grid-card-media-wrap">
        <a href="<?= htmlspecialchars($href) ?>" class="breed-grid-card-link" title="<?= htmlspecialchars($blurb) ?>" aria-label="View <?= htmlspecialchars($breedName) ?>">
            <div class="breed-grid-card-media">
                <img src="<?= htmlspecialchars($thumb) ?>"
                     alt=""
                     class="breed-grid-card-photo"
                     loading="lazy"
                     data-fallback="<?= htmlspecialchars($fallback) ?>"
                     onerror="if(this.dataset.fallback){this.onerror=null;this.src=this.dataset.fallback;}">
                <span class="breed-grid-card-view" aria-hidden="true">
                    <i data-lucide="chevron-right"></i>
                </span>
            </div>
            <div class="breed-grid-card-body">
                <h2 class="breed-grid-card-name"><?= htmlspecialchars($breedName) ?></h2>
                <p class="breed-grid-card-meta text-sm text-muted">
                    <?= htmlspecialchars((string) $breed['size_category']) ?> · <?= htmlspecialchars($groupLabel) ?>
                </p>
                <?php if ($weightLabel !== ''): ?>
                    <p class="breed-grid-card-weight text-xs text-muted"><?= htmlspecialchars($weightLabel) ?></p>
                <?php endif; ?>
                <?php if ($isLocal || $registered > 0): ?>
                    <div class="breed-grid-card-badges">
                        <?php if ($isLocal): ?>
                            <span class="badge badge-owned">Local</span>
                        <?php endif; ?>
                        <?php if ($registered > 0): ?>
                            <span class="badge badge-verified"><?= $registered ?> on Pawdar</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </a>
        <div class="breed-grid-card-compare" data-compare-toggle-wrap>
            <label class="breed-grid-compare-check" title="Compare <?= htmlspecialchars($breedName) ?>">
                <input type="checkbox" data-compare-breed="<?= (int) $breed['breed_id'] ?>" aria-label="Compare <?= htmlspecialchars($breedName) ?>">
            </label>
        </div>
    </div>
</article>
