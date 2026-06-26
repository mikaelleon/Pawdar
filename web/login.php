<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Log In · ' . SITE_NAME;
require __DIR__ . '/includes/head.php';
?>

<div class="auth-page">
    <div class="auth-panel auth-desktop-only">
        <a href="index.php" class="flex items-center gap-sm">
            <div class="logo-mark"><i data-lucide="paw-print"></i></div>
            <span class="logo-text">Pawdar</span>
        </a>
        <h1 class="auth-panel-title">Welcome back.</h1>
        <p class="auth-panel-sub">Pick up where you left off — your cases and dogs are waiting.</p>
    </div>

    <div class="auth-form-side">
        <div class="auth-mobile-header hidden-desktop">
            <a href="index.php"><i data-lucide="arrow-left"></i></a>
            <div class="flex-1 text-center" style="font-weight:800;font-size:17px;margin-left:-24px;">Log In</div>
        </div>

        <div class="auth-form-wrap">
            <div class="hidden-desktop text-center" style="margin-bottom:28px;">
                <div class="logo-mark" style="margin:0 auto;width:52px;height:52px;border-radius:15px;"><i data-lucide="paw-print" style="width:28px;height:28px;"></i></div>
                <h1 class="auth-title" style="font-size:24px;margin-top:14px;">Welcome back</h1>
                <p class="text-sm text-muted">Log in to your Pawdar account</p>
            </div>

            <h1 class="auth-title auth-desktop-only">Log in to Pawdar</h1>
            <p class="text-sm text-muted auth-desktop-only">Don't have an account? <a href="signup.php" style="text-decoration:underline;">Sign up</a></p>

            <form action="feed.php" method="get" style="margin-top:26px;">
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input class="form-input" type="email" id="email" name="email" value="rosa.castillo@email.com">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" type="password" id="password" name="password" value="password">
                    <div class="text-right text-sm text-muted" style="margin-top:8px;text-decoration:underline;">Forgot password?</div>
                </div>
                <button type="submit" class="btn-primary btn-block auth-desktop-only" style="margin-top:18px;">Log In</button>
                <div class="divider-or auth-desktop-only"><span class="text-xs text-muted">or</span></div>
                <button type="button" class="btn-outline btn-block auth-desktop-only">
                    <span class="logo-mark" style="width:20px;height:20px;border-radius:50%;font-size:12px;">G</span> Continue with Google
                </button>
            </form>

            <div class="hidden-desktop" style="position:fixed;left:0;right:0;bottom:0;padding:16px 18px;background:linear-gradient(to top,#fff 75%,transparent);">
                <a href="feed.php" class="btn-primary btn-block">Log In</a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/foot.php'; ?>
