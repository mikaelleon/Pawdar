<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/dogs.php';

if (isset($_SESSION['user_id'])) {
    header('Location: feed.php');
    exit;
}

$pageTitle = 'Sign Up · ' . SITE_NAME;
$pageScripts = ['assets/js/ui.js', 'assets/js/auth.js'];
$loginStats = fetch_login_stats(db());
$barangays = fetch_registry_barangays(db());
if (count($barangays) === 0) {
    $barangays = ['San Roque'];
}
$approvalRoles = roles_requiring_approval();
$error = (string) ($_GET['error'] ?? '');

require __DIR__ . '/includes/head.php';

$roles = [
    ['value' => 'Dog Owner', 'icon' => 'paw-print', 'title' => 'Dog Owner', 'desc' => 'Register and manage your dogs', 'approval' => false],
    ['value' => 'Community Reporter', 'icon' => 'megaphone', 'title' => 'Community Reporter', 'desc' => 'Report incidents in your area', 'approval' => false],
    ['value' => 'Veterinarian', 'icon' => 'stethoscope', 'title' => 'Veterinarian', 'desc' => 'Verify vaccination records', 'approval' => true],
    ['value' => 'LGU Official', 'icon' => 'shield', 'title' => 'LGU Official', 'desc' => 'Manage cases and advisories', 'approval' => true],
    ['value' => 'Rescue Organization', 'icon' => 'heart', 'title' => 'Rescue Org', 'desc' => 'Manage stray rescues and adoptions', 'approval' => true],
    ['value' => 'Admin', 'icon' => 'lock', 'title' => 'Admin', 'desc' => 'System administration', 'approval' => false, 'disabled' => true],
];
?>

<div class="auth-page">
    <div class="auth-panel auth-desktop-only auth-panel-pattern">
        <a href="index.php" class="flex items-center gap-sm auth-panel-logo">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>

        <div class="auth-panel-copy">
            <h1 class="auth-panel-title">Join your<br>community.</h1>
            <p class="auth-panel-sub">One account to register dogs, report incidents, and help keep your barangay safe.</p>
        </div>

        <div class="auth-stat-strip" aria-label="Community impact">
            <span class="auth-stat-item">
                <i data-lucide="paw-print" aria-hidden="true"></i>
                <strong><?= (int) $loginStats['dogs'] ?>+</strong> dogs registered
            </span>
            <span class="auth-stat-sep" aria-hidden="true">·</span>
            <span class="auth-stat-item">
                <strong><?= (int) $loginStats['barangays'] ?></strong> barangays connected
            </span>
            <span class="auth-stat-sep" aria-hidden="true">·</span>
            <span class="auth-stat-item">
                <strong><?= (int) $loginStats['resolved'] ?></strong> incidents resolved
            </span>
        </div>
    </div>

    <div class="auth-form-side auth-form-side-padded">
        <div class="auth-form-wrap signup-form-wrap">
            <h1 class="auth-title auth-desktop-only">Create your account</h1>
            <p class="text-sm text-muted auth-desktop-only">Already have an account? <a href="login.php" class="link-hover">Log in</a></p>

            <form id="signup-form"
                  class="signup-form"
                  action="auth/signup-handler.php"
                  method="post"
                  style="margin-top:22px;"
                  data-approval-roles="<?= htmlspecialchars(json_encode($approvalRoles), ENT_QUOTES, 'UTF-8') ?>"
                  data-signup-error="<?= htmlspecialchars($error) ?>"
                  novalidate>
                <div class="form-section">
                    <div class="form-section-label">I am a…</div>
                    <div class="role-card-grid" role="radiogroup" aria-label="Account role">
                        <?php foreach ($roles as $role): ?>
                            <?php if (!empty($role['disabled'])): ?>
                                <div class="role-card is-disabled"
                                     role="radio"
                                     aria-disabled="true"
                                     aria-checked="false"
                                     tabindex="-1"
                                     aria-label="Admin role unavailable for self-registration">
                                    <i data-lucide="<?= htmlspecialchars($role['icon']) ?>" aria-hidden="true"></i>
                                    <strong><?= htmlspecialchars($role['title']) ?></strong>
                                    <span><?= htmlspecialchars($role['desc']) ?></span>
                                    <span class="role-admin-note">Admin accounts are created internally and cannot be self-registered.</span>
                                </div>
                            <?php else: ?>
                                <button type="button"
                                        class="role-card<?= $role['value'] === 'Dog Owner' ? ' is-selected' : '' ?>"
                                        role="radio"
                                        aria-checked="<?= $role['value'] === 'Dog Owner' ? 'true' : 'false' ?>"
                                        tabindex="<?= $role['value'] === 'Dog Owner' ? '0' : '-1' ?>"
                                        data-role-card
                                        data-role-value="<?= htmlspecialchars($role['value']) ?>"
                                        data-requires-approval="<?= !empty($role['approval']) ? '1' : '0' ?>">
                                    <?php if ($role['value'] === 'Dog Owner'): ?>
                                        <span class="role-card-check" aria-hidden="true"><i data-lucide="check"></i></span>
                                    <?php endif; ?>
                                    <i data-lucide="<?= htmlspecialchars($role['icon']) ?>" aria-hidden="true"></i>
                                    <strong><?= htmlspecialchars($role['title']) ?></strong>
                                    <span><?= htmlspecialchars($role['desc']) ?></span>
                                    <?php if (!empty($role['approval'])): ?>
                                        <span class="role-approval-badge">Requires approval</span>
                                    <?php endif; ?>
                                </button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="role" id="role-input" value="Dog Owner">
                    <p class="signup-approval-note" data-approval-note aria-live="polite">
                        Your account will be reviewed within 1–2 business days. You'll receive an email once approved.
                    </p>
                </div>

                <div class="form-section">
                    <div class="form-section-label">Personal info</div>
                    <div class="float-field">
                        <input class="form-input" type="text" id="name" name="name" required autocomplete="name" placeholder=" ">
                        <label for="name">Full name</label>
                    </div>
                    <div class="float-field">
                        <input class="form-input" type="email" id="email" name="email" required autocomplete="email" placeholder=" ">
                        <label for="email">Email address</label>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="phone">Contact number</label>
                        <input class="form-input"
                               type="tel"
                               id="phone"
                               name="phone"
                               autocomplete="tel"
                               placeholder="09XX XXX XXXX"
                               inputmode="numeric"
                               aria-describedby="phone-format-hint">
                        <p class="field-hint" id="phone-format-hint">Optional. Philippine mobile format shown above.</p>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="barangay">Barangay</label>
                        <select class="form-input form-select" id="barangay" name="barangay" required>
                            <option value="" disabled selected>Select barangay</option>
                            <?php foreach ($barangays as $brgy): ?>
                                <option value="<?= htmlspecialchars($brgy) ?>"><?= htmlspecialchars($brgy) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-label">Account setup</div>
                    <div class="float-field float-field-password">
                        <input class="form-input" type="password" id="password" name="password" required minlength="6" autocomplete="new-password" placeholder=" ">
                        <label for="password">Password</label>
                        <button type="button" class="password-toggle" data-toggle-password="password" aria-label="Show password" aria-pressed="false">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" aria-live="polite">
                        <div class="strength-bar" aria-hidden="true">
                            <span class="strength-seg" data-strength-seg></span>
                            <span class="strength-seg" data-strength-seg></span>
                            <span class="strength-seg" data-strength-seg></span>
                            <span class="strength-seg" data-strength-seg></span>
                        </div>
                        <span class="text-xs text-muted" data-strength-label></span>
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

                    <label class="signup-terms">
                        <input type="checkbox" id="terms" name="terms" value="1" required>
                        <span>I agree to the <a href="index.php">Terms of Service</a> and <a href="index.php#about">Privacy Policy</a>.</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary btn-block" style="margin-top:18px;" disabled>Create Account</button>
            </form>
        </div>
    </div>
</div>

<div class="toast-container" data-toast-container aria-live="polite"></div>
<?php require __DIR__ . '/includes/foot.php'; ?>
