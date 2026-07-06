<?php
/** @var array<string, mixed> $dog */
$coatOptions = dog_coat_color_options();
$selectedCoat = (string) ($dog['coat_color'] ?? '');
$isOtherCoat = $selectedCoat !== '' && !in_array($selectedCoat, $coatOptions, true);
?>
<div class="modal-overlay" data-dog-edit-modal hidden>
    <div class="modal-card dog-edit-modal" role="dialog" aria-modal="true" aria-labelledby="dog-edit-title">
        <h3 class="modal-title" id="dog-edit-title">Edit dog profile</h3>
        <form data-dog-edit-form>
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
                <input class="form-input" id="edit-coat-other" name="coat_color_other" value="<?= $isOtherCoat ? htmlspecialchars($selectedCoat) : '' ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="edit-weight">Weight (kg)</label>
                <input class="form-input" id="edit-weight" type="number" step="0.1" min="0" name="weight_kg" value="<?= htmlspecialchars((string) ($dog['weight_kg'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="edit-marks">Distinguishing marks</label>
                <textarea class="form-input" id="edit-marks" name="distinguishing_marks" rows="2" style="height:auto;padding:12px;"><?= htmlspecialchars((string) ($dog['distinguishing_marks'] ?? '')) ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit-temperament">Temperament notes</label>
                <textarea class="form-input" id="edit-temperament" name="temperament_notes" rows="3" style="height:auto;padding:12px;" placeholder="How this dog behaves day to day…"><?= htmlspecialchars((string) ($dog['temperament_notes'] ?? '')) ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit-health">Health notes</label>
                <textarea class="form-input" id="edit-health" name="health_notes" rows="2" style="height:auto;padding:12px;"><?= htmlspecialchars((string) ($dog['health_notes'] ?? '')) ?></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-outline btn-sm" data-close-dog-edit>Cancel</button>
                <button type="submit" class="btn-primary btn-sm" data-dog-edit-submit>Save changes</button>
            </div>
        </form>
    </div>
</div>
