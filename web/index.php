<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = SITE_NAME . ' — Community Dog Registry';
require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/landing-header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Know your dogs.<br>Protect your community.</h1>
        <p class="hero-subtitle">Pawdar gives barangays, dog owners, vets, and rescue groups one place to register dogs, report incidents, and resolve cases together.</p>
        <div class="hero-actions">
            <a href="signup.php" class="btn-primary">Get Started</a>
            <a href="#how-it-works" class="btn-outline">See How It Works</a>
        </div>
        <div class="hero-stats">
            <div class="stat-box"><div class="stat-value">500+</div><div class="stat-label">Dogs Registered</div></div>
            <div class="stat-box"><div class="stat-value">200+</div><div class="stat-label">Incidents Resolved</div></div>
            <div class="stat-box"><div class="stat-value">12</div><div class="stat-label">Barangays Active</div></div>
        </div>
    </div>
    <div class="hero-illustration">
        <div class="illo-scene">
            <div style="position:absolute;left:32px;top:26px;width:60px;height:60px;border-radius:50%;background:var(--sunlit-clay);"></div>
            <div style="position:absolute;left:-30px;bottom:-20px;width:200px;height:120px;border-radius:50%;background:var(--muted-teal);"></div>
            <div style="position:absolute;right:-30px;bottom:-30px;width:220px;height:140px;border-radius:50%;background:var(--air-force);"></div>
        </div>
        <div class="illo-phone">
            <div style="height:34px;background:var(--air-force);display:flex;align-items:center;padding:0 12px;gap:6px;color:#fff;font-weight:800;font-size:11px;">
                <i data-lucide="paw-print" style="width:13px;height:13px;"></i> Pawdar
            </div>
            <div style="padding:9px;display:flex;flex-direction:column;gap:7px;">
                <div class="incident-card"><div class="accent accent-bite"></div><div class="card-body" style="padding:8px;"><div style="width:30px;height:6px;border-radius:3px;background:var(--burnt-peach);margin-bottom:6px;"></div><div style="width:80%;height:5px;border-radius:3px;background:var(--tea-green);margin-bottom:4px;"></div></div></div>
                <div class="incident-card"><div class="accent accent-injured"></div><div class="card-body" style="padding:8px;"><div style="width:30px;height:6px;border-radius:3px;background:var(--sunlit-clay);margin-bottom:6px;"></div><div style="width:75%;height:5px;border-radius:3px;background:var(--tea-green);"></div></div></div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="features">
    <h2 class="section-title">Everything your community needs</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon" style="background:var(--tea-green);"><i data-lucide="book-marked"></i></div>
            <h3 style="font-weight:800;font-size:18px;">Dog Registry</h3>
            <p class="text-sm" style="font-weight:500;line-height:1.55;margin-top:8px;">Every dog gets a profile, QR tag, and verified vaccination record.</p>
            <a href="dog-profile.php" class="text-muted" style="font-weight:800;font-size:13px;margin-top:14px;display:inline-block;">Learn More →</a>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(224,118,94,.16);"><i data-lucide="alert-triangle" style="color:var(--burnt-peach);"></i></div>
            <h3 style="font-weight:800;font-size:18px;">Incident Reporting</h3>
            <p class="text-sm" style="font-weight:500;line-height:1.55;margin-top:8px;">File a bite, stray, or accident report in under two minutes.</p>
            <a href="report.php" class="text-muted" style="font-weight:800;font-size:13px;margin-top:14px;display:inline-block;">Learn More →</a>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(108,139,159,.18);"><i data-lucide="map-pin" style="color:var(--air-force);"></i></div>
            <h3 style="font-weight:800;font-size:18px;">Live Incident Map</h3>
            <p class="text-sm" style="font-weight:500;line-height:1.55;margin-top:8px;">See geotagged incidents and hotspots across your barangay.</p>
            <a href="map.php" class="text-muted" style="font-weight:800;font-size:13px;margin-top:14px;display:inline-block;">Learn More →</a>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(248,188,114,.22);"><i data-lucide="folder-check" style="color:var(--sunlit-clay);"></i></div>
            <h3 style="font-weight:800;font-size:18px;">Case Management</h3>
            <p class="text-sm" style="font-weight:500;line-height:1.55;margin-top:8px;">Officials track status and run the 14-day rabies watch.</p>
            <a href="cases.php" class="text-muted" style="font-weight:800;font-size:13px;margin-top:14px;display:inline-block;">Learn More →</a>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(135,175,174,.25);"><i data-lucide="heart-handshake" style="color:var(--muted-teal);"></i></div>
            <h3 style="font-weight:800;font-size:18px;">Rescue Board</h3>
            <p class="text-sm" style="font-weight:500;line-height:1.55;margin-top:8px;">Connect strays with rescue orgs, sponsors, and adopters.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:var(--tea-green);"><i data-lucide="badge-check"></i></div>
            <h3 style="font-weight:800;font-size:18px;">Vet Verification</h3>
            <p class="text-sm" style="font-weight:500;line-height:1.55;margin-top:8px;">Licensed vets confirm vaccines and flag at-risk dogs.</p>
        </div>
    </div>
</section>

<section class="how-it-works" id="how-it-works">
    <h2 class="section-title">How Pawdar works</h2>
    <div class="steps-row">
        <div class="step-item"><div class="step-num">1</div><div style="font-weight:800;font-size:16px;margin-top:14px;">Register your dog</div><p class="text-sm" style="margin-top:5px;max-width:200px;">Create a profile and get a scannable QR tag.</p></div>
        <div class="step-item"><div class="step-num">2</div><div style="font-weight:800;font-size:16px;margin-top:14px;">Community reports</div><p class="text-sm" style="margin-top:5px;max-width:200px;">Anyone nearby can file and corroborate incidents.</p></div>
        <div class="step-item"><div class="step-num">3</div><div style="font-weight:800;font-size:16px;margin-top:14px;">Officials manage cases</div><p class="text-sm" style="margin-top:5px;max-width:200px;">LGU staff update status and monitor rabies.</p></div>
        <div class="step-item"><div class="step-num">4</div><div style="font-weight:800;font-size:16px;margin-top:14px;">Vets &amp; rescues respond</div><p class="text-sm" style="margin-top:5px;max-width:200px;">Verify vaccines and coordinate rescue.</p></div>
    </div>
</section>

<section class="section" id="about">
    <h2 class="section-title">Built for everyone in the community</h2>
    <div class="audience-grid">
        <div class="audience-card"><div class="icon-box icon-box-sm"><i data-lucide="dog"></i></div><div><div style="font-weight:800;font-size:15px;">Dog Owners</div><div class="text-xs text-muted">Register &amp; manage your dogs</div></div></div>
        <div class="audience-card"><div class="icon-box icon-box-sm"><i data-lucide="megaphone"></i></div><div><div style="font-weight:800;font-size:15px;">Community Reporters</div><div class="text-xs text-muted">Flag incidents as they happen</div></div></div>
        <div class="audience-card"><div class="icon-box icon-box-sm"><i data-lucide="landmark"></i></div><div><div style="font-weight:800;font-size:15px;">Barangay Officials</div><div class="text-xs text-muted">Manage cases in your jurisdiction</div></div></div>
        <div class="audience-card"><div class="icon-box icon-box-sm"><i data-lucide="stethoscope"></i></div><div><div style="font-weight:800;font-size:15px;">Veterinarians</div><div class="text-xs text-muted">Verify vaccines &amp; health records</div></div></div>
        <div class="audience-card"><div class="icon-box icon-box-sm"><i data-lucide="heart-handshake"></i></div><div><div style="font-weight:800;font-size:15px;">Rescue Organizations</div><div class="text-xs text-muted">Coordinate rescue &amp; adoption</div></div></div>
        <div class="audience-card"><div class="icon-box icon-box-sm"><i data-lucide="graduation-cap"></i></div><div><div style="font-weight:800;font-size:15px;">School &amp; Health Officers</div><div class="text-xs text-muted">Run awareness &amp; safety drives</div></div></div>
    </div>
</section>

<?php require __DIR__ . '/includes/landing-footer.php'; ?>
<?php require __DIR__ . '/includes/foot.php'; ?>
