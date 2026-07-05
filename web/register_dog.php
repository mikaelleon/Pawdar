<?php

require_once __DIR__ . '/includes/app-layout.php';
require_once __DIR__ . '/includes/dogs.php';
require_once __DIR__ . '/includes/breeds.php';
require_role(['Dog Owner', 'Admin']);

$pdo = db();
$success = isset($_GET['success']) && $_GET['success'] === '1';
$newDogId = (int) ($_GET['dog_id'] ?? 0);
$newRegistryId = '';
$error = trim((string) ($_GET['error'] ?? ''));

if ($success && $newDogId > 0) {
    $dog = fetch_dog_profile($pdo, $newDogId);
    $newRegistryId = (string) ($dog['RegistryID'] ?? '');
}

$errorMessage = match ($error) {
    'csrf' => 'Your session expired. Please try again.',
    'missing' => 'Please fill in all required fields.',
    default => '',
};

app_layout_start('registry', 'Register Dog', [
    'showSearch' => false,
    'mobileHeader' => 'back',
    'backTitle' => 'Registry',
    'backHref' => 'registry.php',
    'breadcrumbs' => [
        ['label' => 'Registry', 'url' => 'registry.php'],
        ['label' => 'Register Dog'],
    ],
    'scripts' => ['assets/js/register-dog.js'],
]);
?>

<div class="register-dog-page">
    <?php if ($success && $newDogId > 0): ?>
        <div class="register-success-card card card-bordered card-body text-center">
            <div class="register-success-icon"><i data-lucide="check"></i></div>
            <h1 class="register-form-title">Dog registered</h1>
            <p class="page-subtitle">Registry ID: <?= htmlspecialchars($newRegistryId) ?></p>
            <img src="qr.php?id=<?= urlencode($newRegistryId) ?>" alt="QR code" class="register-qr-preview">
            <div class="register-success-actions">
                <a href="qr.php?id=<?= urlencode($newRegistryId) ?>" download="pawdar-qr.png" class="btn-outline btn-sm">Download QR tag</a>
                <a href="dog-profile.php?id=<?= $newDogId ?>" class="btn-primary btn-sm">View dog profile</a>
            </div>
        </div>
    <?php else: ?>
        <?php if ($errorMessage !== ''): ?>
            <div class="register-form-error" role="alert"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <nav class="register-stepper" aria-label="Registration progress">
            <?php
            $stepLabels = [1 => 'Basic info', 2 => 'Health records', 3 => 'Review'];
            $index = 0;
            foreach ($stepLabels as $num => $label):
                if ($index > 0): ?>
                    <div class="register-step-connector" aria-hidden="true"></div>
                <?php endif; ?>
                <div class="register-step" data-register-step-indicator="<?= $num ?>">
                    <span class="register-step-circle"><?= $num ?></span>
                    <span class="register-step-label"><?= htmlspecialchars($label) ?></span>
                </div>
                <?php $index++;
            endforeach; ?>
        </nav>

        <form class="register-form-card card card-bordered" id="register-dog-form" method="post" action="ajax/register_dog.php" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $_SESSION['csrf_token']) ?>">

            <div class="register-form-panel" data-form-step="1">
                <h2 class="register-form-title">Basic info</h2>
                <p class="register-form-desc">Tell us about your dog. Fields marked with * are required.</p>

                <div class="form-field">
                    <label class="field-label" for="dog-name">Dog name *</label>
                    <input class="field-input" id="dog-name" type="text" name="dog_name" required autocomplete="off" placeholder="e.g. Bruno" data-required-step="1">
                    <p class="field-error" data-field-error="dog-name" hidden>Dog name is required.</p>
                </div>

                <div class="form-field">
                    <label class="field-label" for="breed-search">Breed <span class="required-star">*</span></label>
                    <div class="breed-search-wrapper" data-breed-wrapper>
                        <div class="breed-input-row">
                            <i data-lucide="search" class="breed-search-icon"></i>
                            <input class="field-input breed-input" id="breed-search" type="text"
                                   name="breed_search" placeholder="Search breeds…" required autocomplete="off"
                                   role="combobox" aria-autocomplete="list" aria-controls="breed-dropdown" aria-expanded="false"
                                   data-breed-input>
                            <button type="button" class="breed-clear-btn" data-breed-clear title="Clear breed" hidden>
                                <i data-lucide="x"></i>
                            </button>
                        </div>
                        <input type="hidden" name="breed_id" value="" data-breed-id>
                        <ul class="breed-dropdown" id="breed-dropdown" role="listbox" aria-label="Breed suggestions" data-breed-dropdown hidden></ul>
                    </div>
                    <p class="field-hint" data-breed-hint>Type at least 2 letters, then pick a breed from the list.</p>
                    <p class="field-error" data-field-error="breed" hidden>Select a breed from the list or use a custom name.</p>
                    <p class="field-hint breed-no-match" data-breed-no-match hidden>
                        No match found.
                        <button type="button" class="breed-use-custom" data-breed-use-custom>Use “<span data-breed-custom-label></span>” anyway</button>
                        — admin will review.
                    </p>
                    <div class="breed-selected-info" data-breed-selected hidden>
                        <span class="breed-size-chip" data-breed-size></span>
                        <span class="breed-temperament" data-breed-temperament></span>
                    </div>
                </div>

                <div class="form-field">
                    <span class="field-label">Sex</span>
                    <div class="register-choice-row" role="radiogroup" aria-label="Sex">
                        <label class="register-choice-chip">
                            <input type="radio" name="gender" value="Male" checked>
                            <span>Male</span>
                        </label>
                        <label class="register-choice-chip">
                            <input type="radio" name="gender" value="Female">
                            <span>Female</span>
                        </label>
                    </div>
                </div>

                <div class="form-field form-field--half">
                    <label class="field-label" for="dog-age">Age (years)</label>
                    <input class="field-input" id="dog-age" type="number" name="age" min="0" max="30" placeholder="0">
                </div>

                <div class="form-field">
                    <span class="field-label">Dog type</span>
                    <div class="register-choice-row" role="radiogroup" aria-label="Dog type">
                        <label class="register-choice-chip">
                            <input type="radio" name="dog_type" value="Owned" checked>
                            <span>Owned</span>
                        </label>
                        <label class="register-choice-chip">
                            <input type="radio" name="dog_type" value="Rescued">
                            <span>Rescued</span>
                        </label>
                    </div>
                </div>

                <div class="form-field">
                    <span class="field-label">Photo (optional)</span>
                    <label class="register-photo-upload" data-photo-upload>
                        <input type="file" name="photo" accept="image/jpeg,image/png" hidden data-photo-input>
                        <i data-lucide="camera" data-photo-icon></i>
                        <span data-photo-label>Tap to add a photo (JPG or PNG, max 5MB)</span>
                        <img class="register-photo-preview" data-photo-preview hidden alt="Dog photo preview">
                        <button type="button" class="register-photo-remove" data-photo-remove hidden>Remove photo</button>
                    </label>
                    <p class="field-error" data-photo-error hidden></p>
                </div>

                <div class="register-requirements-hint" data-step-requirements hidden role="status" aria-live="polite"></div>
            </div>

            <div class="register-form-panel" data-form-step="2" hidden>
                <h2 class="register-form-title">Health records</h2>
                <p class="register-form-desc">Optional — skip this step if you do not have vaccination details yet. You can add or verify records later from the dog profile.</p>
                <div class="register-health-callout" role="note">
                    <i data-lucide="info"></i>
                    <p>If you enter a <strong>vaccination name</strong> or <strong>veterinarian</strong>, both <strong>date given</strong> and <strong>next due date</strong> become required.</p>
                </div>

                <div class="form-field">
                    <label class="field-label" for="vaccine-name">Vaccination name</label>
                    <input class="field-input" id="vaccine-name" type="text" name="vaccine_name" placeholder="Anti-Rabies Vaccine">
                </div>

                <div class="register-form-row">
                    <div class="form-field">
                        <label class="field-label" for="vaccine-date">Date given</label>
                        <div class="field-date">
                            <input class="field-input field-date-input" id="vaccine-date" type="date" name="vaccine_date">
                            <i data-lucide="calendar" class="field-date-icon" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="form-field">
                        <label class="field-label" for="vaccine-due">Next due date</label>
                        <div class="field-date">
                            <input class="field-input field-date-input" id="vaccine-due" type="date" name="vaccine_due">
                            <i data-lucide="calendar" class="field-date-icon" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>

                <div class="form-field">
                    <label class="field-label" for="vet-name">Veterinarian name</label>
                    <input class="field-input" id="vet-name" type="text" name="vet_name" placeholder="Dr. Lim">
                </div>

                <div class="form-field">
                    <label class="field-label" for="health-notes">Health notes</label>
                    <textarea class="field-input field-textarea" id="health-notes" name="health_notes" rows="3" placeholder="Allergies, conditions, or other notes…"></textarea>
                </div>
            </div>

            <div class="register-form-panel" data-form-step="3" hidden>
                <h2 class="register-form-title">Review and submit</h2>
                <p class="register-form-desc">Confirm the details below before registering your dog.</p>
                <div class="register-review-card" id="register-review"></div>
            </div>

            <div class="register-form-actions">
                <button type="button" class="btn-outline" data-step-back hidden>Back</button>
                <div class="register-form-actions-primary">
                    <button type="button" class="btn-primary btn-continue" data-step-next disabled>Continue</button>
                    <button type="submit" class="btn-primary" data-step-submit hidden>Register dog</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php app_layout_end([]); ?>
