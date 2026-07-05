<footer class="site-footer">
    <div class="site-footer-inner">
        <div>
            <div class="flex items-center gap-sm">
                <div class="logo-mark"><i data-lucide="paw-print"></i></div>
                <span class="logo-text"><?= SITE_NAME ?></span>
            </div>
            <p class="site-footer-tagline"><?= SITE_TAGLINE ?></p>
        </div>
        <div class="footer-links">
            <a href="login.php">Feed</a>
            <a href="login.php">Registry</a>
            <a href="login.php">Map</a>
            <a href="login.php">Cases</a>
            <a href="login.php">Breed Info</a>
        </div>
        <div class="flex flex-col items-center gap-md">
            <a href="signup.php" class="btn-primary btn-sm">Sign Up</a>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</span>
        <div class="flex gap-lg">
            <span>Privacy Policy</span>
            <span>Terms of Service</span>
        </div>
    </div>
</footer>
