<?php
require_once __DIR__ . '/../includes/breed-media.php';

$incidentTypes = incident_type_map();
$coatOptions = dog_coat_color_options();
$today = date('Y-m-d');
$now = date('H:i');
?>
<div class="report-drawer-overlay" data-report-drawer-overlay aria-hidden="true"></div>
<aside class="report-drawer sidebar-scroll" data-report-drawer aria-hidden="true">
    <div class="report-drawer-header">
        <h2>Report Incident</h2>
        <button type="button" class="report-drawer-close" data-close-report-drawer aria-label="Close">
            <i data-lucide="x"></i>
        </button>
    </div>

    <div class="report-progress" data-report-progress>
        <div class="report-progress-step is-active" data-step-indicator="1">1</div>
        <div class="report-progress-line" data-progress-line="1"></div>
        <div class="report-progress-step" data-step-indicator="2">2</div>
        <div class="report-progress-line" data-progress-line="2"></div>
        <div class="report-progress-step" data-step-indicator="3">3</div>
    </div>

    <form class="report-drawer-form" data-report-form enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="report-drawer-panel" data-report-step="1">
            <p class="label-upper">Step 1</p>
            <h3>What happened?</h3>
            <div class="incident-type-grid incident-type-grid-compact">
                <?php foreach ($incidentTypes as $type => $meta): ?>
                    <label class="type-card report-type-card">
                        <input type="radio" name="incident_type" value="<?= htmlspecialchars($type) ?>"<?= $type === 'Animal Bite' ? ' checked' : '' ?>>
                        <span class="type-card-check"><i data-lucide="check"></i></span>
                        <div class="type-card-icon"><i data-lucide="<?= htmlspecialchars($meta['icon']) ?>"></i></div>
                        <span><?= htmlspecialchars($meta['label']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="report-nav-row">
                <span></span>
                <button type="button" class="btn-primary" data-report-next>Next</button>
            </div>
        </div>

        <div class="report-drawer-panel" data-report-step="2" hidden>
            <p class="label-upper">Step 2</p>
            <h3>Where and when?</h3>
            <div class="float-field">
                <input class="form-input" type="text" id="report-location" name="location" required placeholder=" ">
                <label for="report-location">Location</label>
            </div>
            <button type="button" class="btn-outline btn-sm btn-block" data-use-location style="margin-bottom:8px;">
                <i data-lucide="crosshair"></i> Use my current location
            </button>
            <p class="text-xs text-muted" data-geo-status style="margin-bottom:12px;"></p>
            <div class="form-group">
                <label class="form-label" for="report-date">Date</label>
                <input class="form-input" type="date" id="report-date" name="report_date" value="<?= $today ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="report-time">Time</label>
                <input class="form-input" type="time" id="report-time" name="report_time" value="<?= $now ?>">
            </div>
            <input type="hidden" name="latitude" id="report-latitude" value="">
            <input type="hidden" name="longitude" id="report-longitude" value="">

            <div class="report-dog-section">
                <p class="form-label" style="margin-bottom:10px;">Describe the dog involved</p>

                <div class="form-group">
                    <label class="form-label" for="report-observed-breed">Breed</label>
                    <div class="breed-autocomplete-wrap" data-report-breed-wrap>
                        <input class="form-input" type="text" id="report-observed-breed" name="observed_breed" placeholder="Search breeds or choose unknown" autocomplete="off" aria-expanded="false">
                        <ul class="breed-dropdown" data-report-breed-dropdown hidden role="listbox"></ul>
                    </div>
                    <button type="button" class="btn-ghost btn-sm report-breed-unknown" data-report-breed-unknown style="margin-top:6px;">
                        Unknown / Not sure
                    </button>
                </div>

                <div class="form-group">
                    <label class="form-label" for="report-observed-coat">Coat color</label>
                    <select class="form-input" id="report-observed-coat" name="observed_coat_color" data-coat-select>
                        <option value="">— Select —</option>
                        <?php foreach ($coatOptions as $color): ?>
                            <option value="<?= htmlspecialchars($color) ?>"><?= htmlspecialchars($color) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" data-coat-other-wrap hidden>
                    <label class="form-label" for="report-observed-coat-other">Specify color</label>
                    <input class="form-input" id="report-observed-coat-other" name="observed_coat_color_other" placeholder="e.g. brown and white">
                </div>

                <div class="form-group">
                    <label class="form-label" for="report-observed-size">Size</label>
                    <select class="form-input" id="report-observed-size" name="observed_dog_size">
                        <option value="">— Select —</option>
                        <option value="Small">Small</option>
                        <option value="Medium">Medium</option>
                        <option value="Large">Large</option>
                        <option value="Unknown">Unknown / Not sure</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="report-observed-marks">Distinguishing marks / appearance</label>
                    <textarea class="form-input" id="report-observed-marks" name="observed_marks" rows="3" placeholder="e.g. brown and white, collar visible, limping on left hind leg" style="height:auto;padding:12px;"></textarea>
                </div>
            </div>

            <details class="report-registered-dog-toggle">
                <summary>I know this is a registered dog →</summary>
                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="report-dog-search">Search your dogs</label>
                    <input class="form-input" type="text" id="report-dog-search" placeholder="Search your dogs…" autocomplete="off">
                    <input type="hidden" name="dog_id" id="report-dog-id" value="">
                    <div class="dog-search-results" data-dog-search-results hidden></div>
                </div>
            </details>

            <div class="form-group">
                <label class="form-label" for="report-description">Additional notes <span class="text-muted">(optional)</span></label>
                <textarea class="form-input" id="report-description" name="description" rows="3" maxlength="280" placeholder="Anything else responders should know…" style="height:auto;padding:12px;"></textarea>
                <div class="text-xs text-muted" data-char-count>0 / 280</div>
            </div>
            <div class="report-nav-row">
                <button type="button" class="link-hover" data-report-back>Back</button>
                <button type="button" class="btn-primary" data-report-next>Next</button>
            </div>
        </div>

        <div class="report-drawer-panel" data-report-step="3" hidden>
            <p class="label-upper">Step 3</p>
            <h3>Photo evidence</h3>
            <div class="photo-dropzone" data-photo-dropzone>
                <input type="file" id="report-photo" name="photo" accept="image/jpeg,image/png" hidden>
                <i data-lucide="camera"></i>
                <p class="text-sm">Drag and drop a photo here, or click to browse</p>
                <p class="text-xs text-muted">JPG/PNG, max 5MB</p>
                <img data-photo-preview hidden alt="Upload preview">
            </div>
            <div class="report-nav-row">
                <button type="button" class="link-hover" data-report-back>Back</button>
                <button type="submit" class="btn-primary" data-report-submit>Report Incident</button>
            </div>
        </div>
    </form>
</aside>
