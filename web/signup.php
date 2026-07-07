<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/locations.php';

if (isset($_SESSION['user_id'])) {
    if (empty($_SESSION['email_verified'])) {
        header('Location: verify.php');
    } else {
        header('Location: feed.php');
    }
    exit;
}

$pageTitle = 'Sign Up · ' . SITE_NAME;
$pageScripts = ['assets/js/ui.js', 'assets/js/auth.js', 'assets/js/signup-wizard.js'];
$loginStats = fetch_login_stats(db());
$cities = fetch_cities(db());
$approvalRoles = roles_requiring_approval();
$phoneRequiredRoles = roles_requiring_phone();
$error = (string) ($_GET['error'] ?? '');
$nameSuffixes = ['', 'N/A', 'Jr.', 'Sr.', 'II', 'III', 'IV'];
$showPawBackground = false;

require __DIR__ . '/includes/head.php';

$roles = [
    ['value' => 'Dog Owner', 'icon' => 'paw-print', 'title' => 'Dog Owner', 'desc' => 'Register and manage your dogs', 'approval' => false],
    ['value' => 'Community Reporter', 'icon' => 'megaphone', 'title' => 'Community Reporter', 'desc' => 'Report incidents in your area', 'approval' => false],
    ['value' => 'Veterinarian', 'icon' => 'stethoscope', 'title' => 'Veterinarian', 'desc' => 'Verify vaccination records', 'approval' => true],
    ['value' => 'LGU Official', 'icon' => 'shield', 'title' => 'LGU Official', 'desc' => 'Manage cases and advisories', 'approval' => true],
    ['value' => 'Rescue Organization', 'icon' => 'heart', 'title' => 'Rescue Org', 'desc' => 'Manage stray rescues and adoptions', 'approval' => true],
    ['value' => 'Admin', 'icon' => 'lock', 'title' => 'Admin', 'desc' => 'System administration', 'approval' => false, 'disabled' => true],
];

$stepLabels = [1 => 'Account', 2 => 'Role & location', 3 => 'Verify'];
?>

<div class="auth-page">
    <?php require __DIR__ . '/includes/auth-theme-toggle.php'; ?>
    <div class="auth-panel auth-desktop-only auth-panel-pattern">
        <a href="index.html" class="flex items-center gap-sm auth-panel-logo">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>
        <div class="auth-panel-copy">
            <h1 class="auth-panel-title">Join your<br>community.</h1>
            <p class="auth-panel-sub">One account to register dogs, report incidents, and help keep your barangay safe.</p>
        </div>
        <div class="auth-stat-strip" aria-label="Community impact">
            <span class="auth-stat-item"><i data-lucide="paw-print" aria-hidden="true"></i><strong><?= (int) $loginStats['dogs'] ?>+</strong> dogs registered</span>
            <span class="auth-stat-sep" aria-hidden="true">·</span>
            <span class="auth-stat-item"><strong><?= (int) $loginStats['barangays'] ?></strong> barangays connected</span>
            <span class="auth-stat-sep" aria-hidden="true">·</span>
            <span class="auth-stat-item"><strong><?= (int) $loginStats['resolved'] ?></strong> incidents resolved</span>
        </div>
    </div>

    <div class="auth-form-side auth-form-side-padded">
        <div class="auth-form-wrap signup-form-wrap">
            <h1 class="auth-title auth-desktop-only">Create your account</h1>
            <p class="text-sm text-muted auth-desktop-only">Already have an account? <a href="login.php" class="link-hover">Log in</a></p>

            <nav class="register-stepper signup-stepper" aria-label="Sign up progress" data-signup-stepper aria-live="polite">
                <?php $index = 0; foreach ($stepLabels as $num => $label): ?>
                    <?php if ($index > 0): ?><div class="register-step-connector" data-signup-connector="<?= $num - 1 ?>" aria-hidden="true"></div><?php endif; ?>
                    <div class="register-step" data-signup-step-indicator="<?= $num ?>">
                        <span class="register-step-circle"><?= $num ?></span>
                        <span class="register-step-label"><?= htmlspecialchars($label) ?></span>
                    </div>
                    <?php $index++; endforeach; ?>
            </nav>

            <form id="signup-form"
                  class="signup-form"
                  action="auth/signup-handler.php"
                  method="post"
                  data-signup-wizard="1"
                  data-phone-required-roles="<?= htmlspecialchars(json_encode($phoneRequiredRoles), ENT_QUOTES, 'UTF-8') ?>"
                  data-signup-error="<?= htmlspecialchars($error) ?>"
                  novalidate>
                <div class="signup-form-panel" data-form-step="1">
                    <h2 class="signup-panel-title">Account</h2>
                    <p class="signup-panel-desc">Enter your name as it appears on your government ID.</p>

                    <div class="signup-name-grid">
                        <div class="form-field">
                            <label class="form-field-label" for="last_name">Last name *</label>
                            <input class="form-input" type="text" id="last_name" name="last_name" required autocomplete="family-name">
                            <p class="field-hint">As shown on your government ID.</p>
                        </div>
                        <div class="form-field">
                            <label class="form-field-label" for="first_name">First name *</label>
                            <input class="form-input" type="text" id="first_name" name="first_name" required autocomplete="given-name">
                        </div>
                        <div class="form-field">
                            <label class="form-field-label" for="middle_name">Middle name</label>
                            <input class="form-input" type="text" id="middle_name" name="middle_name" autocomplete="additional-name">
                        </div>
                        <div class="form-field">
                            <label class="form-field-label" for="name_suffix">Name suffix</label>
                            <select class="form-input form-select" id="name_suffix" name="name_suffix">
                                <?php foreach ($nameSuffixes as $suffix): ?>
                                    <option value="<?= htmlspecialchars($suffix) ?>"><?= $suffix === '' ? 'None' : htmlspecialchars($suffix) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="float-field">
                        <input class="form-input" type="email" id="email" name="email" required autocomplete="email" placeholder=" ">
                        <label for="email">Email address</label>
                    </div>

                    <div class="float-field float-field-password">
                        <input class="form-input" type="password" id="password" name="password" required minlength="6" autocomplete="new-password" placeholder=" ">
                        <label for="password">Password</label>
                        <button type="button" class="password-toggle" data-toggle-password="password" aria-label="Show password" aria-pressed="false"><i data-lucide="eye"></i></button>
                    </div>
                    <div class="password-strength" aria-live="polite">
                        <div class="strength-row">
                            <div class="strength-bar" aria-hidden="true">
                                <span class="strength-seg" data-strength-seg></span><span class="strength-seg" data-strength-seg></span><span class="strength-seg" data-strength-seg></span><span class="strength-seg" data-strength-seg></span>
                            </div>
                            <span class="strength-label" data-strength-label></span>
                        </div>
                        <span class="sr-only" data-strength-sr></span>
                    </div>
                    <p class="field-hint field-hint--warning" data-password-weak hidden>Choose a stronger password.</p>

                    <div class="confirm-password-wrap">
                        <div class="float-field float-field-password float-field-confirm">
                            <input class="form-input" type="password" id="password_confirm" name="password_confirm" required minlength="6" autocomplete="new-password" placeholder=" ">
                            <label for="password_confirm">Confirm password</label>
                            <span class="match-icon" data-match-icon aria-hidden="true"></span>
                        </div>
                        <p class="confirm-match-feedback" data-match-message hidden aria-live="polite"></p>
                    </div>
                </div>

                <div class="signup-form-panel" data-form-step="2" hidden>
                    <h2 class="signup-panel-title">Role &amp; location</h2>
                    <p class="signup-panel-desc">Choose how you'll use Pawdar and where you're based.</p>

                    <div class="form-section-label">I am a…</div>
                    <div class="role-card-grid" role="radiogroup" aria-label="Account role">
                        <?php foreach ($roles as $role): ?>
                            <?php if (!empty($role['disabled'])): ?>
                                <div class="role-card is-disabled" role="radio" aria-disabled="true" aria-checked="false" tabindex="-1" aria-label="Admin role unavailable for self-registration">
                                    <i data-lucide="<?= htmlspecialchars($role['icon']) ?>" aria-hidden="true"></i>
                                    <strong><?= htmlspecialchars($role['title']) ?></strong>
                                    <span><?= htmlspecialchars($role['desc']) ?></span>
                                    <span class="role-admin-note">Admin accounts are created internally and cannot be self-registered.</span>
                                </div>
                            <?php else: ?>
                                <button type="button" class="role-card<?= $role['value'] === 'Dog Owner' ? ' is-selected' : '' ?>" role="radio" aria-checked="<?= $role['value'] === 'Dog Owner' ? 'true' : 'false' ?>" tabindex="<?= $role['value'] === 'Dog Owner' ? '0' : '-1' ?>" data-role-card data-role-value="<?= htmlspecialchars($role['value']) ?>" data-requires-approval="<?= !empty($role['approval']) ? '1' : '0' ?>">
                                    <?php if ($role['value'] === 'Dog Owner'): ?><span class="role-card-check" aria-hidden="true"><i data-lucide="check"></i></span><?php endif; ?>
                                    <i data-lucide="<?= htmlspecialchars($role['icon']) ?>" aria-hidden="true"></i>
                                    <strong><?= htmlspecialchars($role['title']) ?></strong>
                                    <span><?= htmlspecialchars($role['desc']) ?></span>
                                    <?php if (!empty($role['approval'])): ?><span class="role-approval-badge">Requires approval</span><?php endif; ?>
                                </button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="role" id="role-input" value="Dog Owner">
                    <p class="signup-approval-note" data-approval-note aria-live="polite">Your account will be reviewed within 1–2 business days. You'll receive an email once approved.</p>

                    <div class="form-field">
                        <label class="form-field-label" for="city_id">City *</label>
                        <select class="form-input form-select" id="city_id" name="city_id" required>
                            <option value="" disabled selected>Select city</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?= (int) $city['city_id'] ?>"><?= htmlspecialchars((string) $city['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($cities === []): ?>
                            <p class="field-hint field-hint--warning">Location data unavailable. Run <code>php sql/import-barangays.php</code>.</p>
                        <?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="barangay_id">Barangay *</label>
                        <select class="form-input form-select" id="barangay_id" name="barangay_id" required disabled>
                            <option value="" disabled selected>Select barangay</option>
                        </select>
                    </div>
                    <p class="signup-privacy-note">Your barangay is used to route reports to the correct LGU office and is visible to officials in your area.</p>

                    <div class="form-field">
                        <label class="form-field-label" for="phone_local"><span data-phone-label>Contact number</span></label>
                        <div class="phone-input-group">
                            <span class="phone-prefix" aria-hidden="true">+63</span>
                            <input class="form-input phone-input" type="tel" id="phone_local" name="phone_local" inputmode="numeric" maxlength="10" placeholder="9171234567" autocomplete="tel-national">
                        </div>
                        <p class="field-hint" data-phone-hint>Optional for Community Reporters.</p>
                    </div>

                    <label class="signup-terms">
                        <input type="checkbox" id="terms" name="terms" value="1" required>
                        <span>I agree to the <a href="index.html">Terms of Service</a> and <a href="index.html#about">Privacy Policy</a>. Pawdar processes your data in compliance with the Philippine Data Privacy Act (RA 10173).</span>
                    </label>
                </div>

                <div class="signup-form-actions">
                    <button type="button" class="btn-ghost signup-save-later" data-save-later>Save and finish later</button>
                    <div class="signup-form-actions-nav">
                        <button type="button" class="btn-outline" data-step-back hidden>← Back</button>
                        <button type="button" class="btn-primary btn-continue" data-step-next disabled>Continue</button>
                        <button type="submit" class="btn-primary" data-step-submit hidden>Create account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast-container" data-toast-container aria-live="polite"></div>
<?php require __DIR__ . '/includes/foot.php'; ?>
