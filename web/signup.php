<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Sign Up · ' . SITE_NAME;
require __DIR__ . '/includes/head.php';
?>

<div class="auth-page">
    <div class="auth-panel auth-desktop-only">
        <a href="index.php" class="flex items-center gap-sm">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>
        <h1 class="auth-panel-title">Join your<br>community.</h1>
        <p class="auth-panel-sub">One account to register dogs, report incidents, and help keep your barangay safe.</p>
    </div>

    <div class="auth-form-side">
        <div class="auth-mobile-header hidden-desktop">
            <a href="index.php"><i data-lucide="arrow-left"></i></a>
            <div class="flex-1 text-center" style="font-weight:800;font-size:17px;margin-left:-24px;">Sign Up</div>
        </div>

        <div class="auth-form-wrap">
            <div class="hidden-desktop text-center" style="margin-bottom:22px;">
                <div class="logo-mark" style="margin:0 auto;"><i data-lucide="paw-print" style="width:26px;height:26px;"></i></div>
                <h1 class="auth-title" style="font-size:22px;margin-top:12px;">Create your account</h1>
                <p class="text-sm text-muted">Already have one? <a href="login.php" style="text-decoration:underline;">Log in</a></p>
            </div>

            <h1 class="auth-title auth-desktop-only">Create your account</h1>
            <p class="text-sm text-muted auth-desktop-only">Already have an account? <a href="login.php" style="text-decoration:underline;">Log in</a></p>

            <form action="feed.php" method="get" style="margin-top:22px;">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input class="form-input" type="text" id="name" name="name" value="Rosa Castillo">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input class="form-input" type="email" id="email" name="email" value="rosa.castillo@email.com">
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Contact Number</label>
                    <input class="form-input" type="tel" id="phone" name="phone" value="0917 555 0142">
                </div>
                <div class="form-group auth-desktop-only">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div><label class="form-label" for="password">Password</label><input class="form-input" type="password" id="password" value="••••••••"></div>
                        <div><label class="form-label" for="password2">Confirm Password</label><input class="form-input" type="password" id="password2" value="••••••••"></div>
                    </div>
                </div>
                <div class="form-group hidden-desktop">
                    <label class="form-label" for="password-m">Password</label>
                    <input class="form-input" type="password" id="password-m" value="••••••••">
                </div>
                <div class="form-group hidden-desktop">
                    <label class="form-label" for="password2-m">Confirm Password</label>
                    <input class="form-input" type="password" id="password2-m" value="••••••••">
                </div>

                <div style="margin-top:18px;">
                    <div class="text-sm" style="font-weight:800;margin-bottom:10px;">I am a…</div>
                    <div class="role-grid">
                        <button type="button" class="role-chip is-selected" data-role-chip>Dog Owner</button>
                        <button type="button" class="role-chip" data-role-chip>Community Reporter</button>
                        <button type="button" class="role-chip" data-role-chip>Veterinarian</button>
                        <button type="button" class="role-chip" data-role-chip>LGU Official</button>
                        <button type="button" class="role-chip" data-role-chip>Rescue Org</button>
                        <button type="button" class="role-chip is-disabled" data-role-chip disabled>Admin</button>
                    </div>
                </div>

                <p class="text-xs text-muted mt-md flex items-center gap-sm"><i data-lucide="info"></i> Dog Owner accounts are activated instantly.</p>

                <div class="flex items-center gap-sm mt-md">
                    <div style="width:20px;height:20px;border-radius:5px;background:var(--burnt-peach);display:flex;align-items:center;justify-content:center;flex:none;"><i data-lucide="check" style="width:14px;height:14px;color:#fff;"></i></div>
                    <span class="text-sm">I agree to the <span class="text-muted" style="text-decoration:underline;">Terms of Service</span> and <span class="text-muted" style="text-decoration:underline;">Privacy Policy</span></span>
                </div>

                <button type="submit" class="btn-primary btn-block" style="margin-top:18px;">Create Account</button>

                <div class="divider-or"><span class="text-xs text-muted">or</span></div>
                <button type="button" class="btn-outline btn-block">
                    <span class="logo-mark" style="width:20px;height:20px;border-radius:50%;font-size:12px;">G</span> Continue with Google
                </button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/foot.php'; ?>
