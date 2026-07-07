<nav class="landing-nav">
    <a href="index.html" class="logo">
        <div class="logo-mark"><i data-lucide="paw-print"></i></div>
        <span class="logo-text"><?= SITE_NAME ?></span>
    </a>
    <div class="landing-nav-links">
        <a href="index.html">Home</a>
        <a href="index.html#features">Features</a>
        <a href="index.html#how-it-works">How It Works</a>
        <a href="index.html#about">About</a>
    </div>
    <div class="landing-nav-actions">
        <button type="button" class="icon-box icon-box-sm theme-toggle-btn landing-theme-toggle" id="darkModeToggle" data-theme-toggle aria-label="Switch to dark mode" title="Toggle theme">
            <i data-lucide="sun" data-theme-icon></i>
        </button>
        <a href="login.php" class="btn-outline btn-sm">Log In</a>
        <a href="signup.php" class="btn-primary btn-sm">Sign Up</a>
    </div>
    <button type="button" class="mobile-menu-btn" data-mobile-menu aria-label="Open menu" aria-expanded="false">
        <i data-lucide="menu"></i>
    </button>
</nav>
<div class="landing-mobile-nav" data-mobile-nav hidden>
    <a href="index.html">Home</a>
    <a href="index.html#features">Features</a>
    <a href="index.html#how-it-works">How It Works</a>
    <a href="index.html#about">About</a>
    <div class="landing-mobile-nav-actions">
        <a href="login.php" class="btn-outline btn-sm">Log In</a>
        <a href="signup.php" class="btn-primary btn-sm">Sign Up</a>
    </div>
</div>
