<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header('Location: feed.php');
    exit;
}

$pageTitle = 'Sign Up · ' . SITE_NAME;
$pageScripts = ['assets/js/ui.js', 'assets/js/auth.js'];
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
        <a href="index.php" class="flex items-center gap-sm">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>
        <h1 class="auth-panel-title">Join your<br>community.</h1>
        <p class="auth-panel-sub">One account to register dogs, report incidents, and help keep your barangay safe.</p>
    </div>

    <div class="auth-form-side auth-form-side-padded">
        <div class="auth-form-wrap signup-form-wrap">
            <h1 class="auth-title auth-desktop-only">Create your account</h1>
            <p class="text-sm text-muted auth-desktop-only">Already have an account? <a href="login.php" class="link-hover">Log in</a></p>

            <form id="signup-form" action="auth/signup-handler.php" method="post" style="margin-top:22px;" novalidate>
                <div class="form-section">
                    <div class="form-section-label">Personal info</div>
                    <div class="float-field"><input class="form-input" type="text" id="name" name="name" required placeholder=" "><label for="name">Full name</label></div>
                    <div class="float-field"><input class="form-input" type="email" id="email" name="email" required placeholder=" "><label for="email">Email address</label></div>
                    <div class="float-field"><input class="form-input" type="tel" id="phone" name="phone" placeholder=" "><label for="phone">Contact number</label></div>
                    <div class="float-field"><input class="form-input" type="text" id="barangay" name="barangay" required placeholder=" "><label for="barangay">Barangay</label></div>
                </div>

                <div class="form-section">
                    <div class="form-section-label">Account setup</div>
                    <div class="float-field float-field-password">
                        <input class="form-input" type="password" id="password" name="password" required minlength="6" placeholder=" ">
                        <label for="password">Password</label>
                        <button type="button" class="password-toggle" data-toggle-password="password" aria-label="Show password"><i data-lucide="eye"></i></button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar"><span class="strength-seg" data-strength-seg></span><span class="strength-seg" data-strength-seg></span><span class="strength-seg" data-strength-seg></span><span class="strength-seg" data-strength-seg></span></div>
                        <span class="text-xs text-muted" data-strength-label></span>
                    </div>
                    <div class="float-field float-field-password">
                        <input class="form-input" type="password" id="password_confirm" name="password_confirm" required minlength="6" placeholder=" ">
                        <label for="password_confirm">Confirm password</label>
                        <span class="match-icon" data-match-icon aria-hidden="true"></span>
                    </div>

                    <div class="text-sm form-section-label" style="margin-top:8px;">I am a…</div>
                    <div class="role-card-grid">
                        <?php foreach ($roles as $role): ?>
                            <button type="button"
                                    class="role-card<?= !empty($role['disabled']) ? ' is-disabled' : '' ?><?= $role['value'] === 'Dog Owner' ? ' is-selected' : '' ?>"
                                    data-role-card
                                    data-role-value="<?= htmlspecialchars($role['value']) ?>"
                                    <?= !empty($role['disabled']) ? 'disabled title="Contact your administrator to get access"' : '' ?>>
                                <?php if ($role['value'] === 'Dog Owner'): ?><span class="role-card-check"><i data-lucide="check"></i></span><?php endif; ?>
                                <i data-lucide="<?= htmlspecialchars($role['icon']) ?>"></i>
                                <strong><?= htmlspecialchars($role['title']) ?></strong>
                                <span><?= htmlspecialchars($role['desc']) ?></span>
                                <?php if (!empty($role['approval'])): ?><span class="role-approval-badge">Requires approval</span><?php endif; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="role" id="role-input" value="Dog Owner">
                </div>

                <button type="submit" class="btn-primary btn-block" style="margin-top:18px;" disabled>Create Account</button>
            </form>
        </div>
    </div>
</div>

<div class="toast-container" data-toast-container aria-live="polite"></div>
<?php require __DIR__ . '/includes/foot.php'; ?>
