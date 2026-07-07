<?php
/** @var array<string, mixed> $dog */
$coatOptions = dog_coat_color_options();
$selectedCoat = (string) ($dog['coat_color'] ?? '');
$isOtherCoat = $selectedCoat !== '' && !in_array($selectedCoat, $coatOptions, true);
?>
<div class="modal-overlay dog-edit-modal-overlay" data-dog-edit-modal hidden>
    <div class="modal-card dog-edit-modal" role="dialog" aria-modal="true" aria-labelledby="dog-edit-title">
        <div class="dog-edit-modal-header">
            <h3 class="dog-edit-modal-title" id="dog-edit-title">Edit Dog Profile</h3>
            <button type="button" class="dog-edit-modal-close" data-close-dog-edit aria-label="Close edit profile">
                <i data-lucide="x"></i>
            </button>
        </div>
        <form data-dog-edit-form class="dog-edit-modal-form">
            <div class="dog-edit-modal-body">
                <div class="form-group">
                    <label class="form-label" for="edit-dog-name">Dog name</label>
                    <input class="form-input" id="edit-dog-name" name="dog_name" required value="<?= htmlspecialchars((string) $dog['DogName']) ?>">
                </div>
                <div class="register-form-row">
                    <div class="form-group">
                        <label class="form-label" for="edit-dog-gender">Sex</label>
                        <select class="form-input" id="edit-dog-gender" name="gender">
                            <?php foreach (['Male', 'Female', 'Unknown'] as $gender): ?>
                                <option value="<?= htmlspecialchars($gender) ?>" <?= ($dog['Gender'] ?? '') === $gender ? 'selected' : '' ?>><?= htmlspecialchars($gender) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit-dog-age">Age (years)</label>
                        <input class="form-input" id="edit-dog-age" type="number" name="age" min="0" max="30" value="<?= htmlspecialchars((string) ($dog['Age'] ?? '')) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-coat-color">Coat color</label>
                    <select class="form-input" id="edit-coat-color" name="coat_color" data-coat-select>
                        <option value="">— Select —</option>
                        <?php foreach ($coatOptions as $color): ?>
                            <option value="<?= htmlspecialchars($color) ?>" <?= (!$isOtherCoat && $selectedCoat === $color) || ($color === 'Other' && $isOtherCoat) ? 'selected' : '' ?>><?= htmlspecialchars($color) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" data-coat-other-wrap <?= $isOtherCoat ? '' : 'hidden' ?>>
                    <label class="form-label" for="edit-coat-other">Specify color</label>
                    <input class="form-input" id="edit-coat-other" name="coat_color_other" value="<?= $isOtherCoat ? htmlspecialchars($selectedCoat) : '' ?>" placeholder="e.g. brown and white">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-weight">Weight (kg)</label>
                    <input class="form-input" id="edit-weight" type="number" step="0.1" min="0" name="weight_kg" value="<?= htmlspecialchars((string) ($dog['weight_kg'] ?? '')) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-marks">Distinguishing marks</label>
                    <textarea class="form-input dog-edit-textarea dog-edit-textarea--short" id="edit-marks" name="distinguishing_marks" rows="2" placeholder="Scars, patches, docked tail…"><?= htmlspecialchars((string) ($dog['distinguishing_marks'] ?? '')) ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-temperament">Temperament notes</label>
                    <textarea class="form-input dog-edit-textarea dog-edit-textarea--tall" id="edit-temperament" name="temperament_notes" rows="4" placeholder="How this dog behaves day to day…"><?= htmlspecialchars((string) ($dog['temperament_notes'] ?? '')) ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-health">Health notes</label>
                    <textarea class="form-input dog-edit-textarea dog-edit-textarea--medium" id="edit-health" name="health_notes" rows="3" placeholder="Allergies, medications, chronic conditions…"><?= htmlspecialchars((string) ($dog['health_notes'] ?? '')) ?></textarea>
                </div>
            </div>
            <div class="dog-edit-modal-footer">
                <button type="button" class="btn-outline btn-sm" data-close-dog-edit>Cancel</button>
                <button type="submit" class="btn-primary btn-sm" data-dog-edit-submit>Save changes</button>
            </div>
        </form>
    </div>
</div>
