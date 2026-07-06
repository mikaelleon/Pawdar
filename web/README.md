# Pawdar Web — InfinityFree Deployment

This folder contains the production-ready HTML/PHP site converted from the Pawdar Design System canvas file.

## Upload to InfinityFree

1. Log in to [InfinityFree](https://infinityfree.com/) and open your hosting control panel (VistaPanel).
2. Open **File Manager** (or connect via FTP using the credentials in your panel).
3. Go to **`htdocs`** (this is your website root).
4. Upload **all files and folders** from this `web/` directory into `htdocs`:
   - `index.html`
   - `feed.php`, `map.php`, `dog-profile.php`, etc.
   - `includes/`
   - `partials/`
   - `assets/`
5. Visit your site URL (e.g. `https://yoursite.infinityfreeapp.com/`).

## Site structure

| File | Description |
|------|-------------|
| `index.html` | Landing / marketing page (PHP-powered via `.htaccess`) |
| `login.php` | Log in |
| `signup.php` | Create account (3-step wizard) |
| `verify.php` | Email verification — check inbox / resend |
| `email_verified.php` | Confirmation after clicking verify link |
| `forgot_password.php` | Password reset request |
| `reset_password.php` | Set new password from email link |
| `feed.php` | Home incident feed |
| `map.php` | Incident map |
| `dog-profile.php` | Dog registry profile |
| `report.php` | Report incident (step 1) |
| `report-details.php` | Report incident (step 3) |
| `cases.php` | LGU case management |
| `case-detail.php` | Case detail + rabies watch |
| `breeds.php` | Breed directory |
| `first-aid.php` | First aid guides |

## Local setup (XAMPP)

1. Copy `.env.example` → `.env` at the **repo root** and set MySQL + Resend keys.
2. Optional: copy `includes/db.local.php.example` → `includes/db.local.php` for DB-only overrides.
3. Run schema: `php sql/setup.php`
4. Import barangays (local CLI): `php sql/import-barangays.php`  
   **InfinityFree:** import `sql/schema-v5-barangays-seed.sql` in phpMyAdmin instead (no shell access).
5. Import breeds (local CLI): `php sql/import-breeds.php`  
   **InfinityFree:** import `sql/schema-v3-breeds-seed.sql` in phpMyAdmin after `schema-v3-breeds.sql`  
   Regenerate from `archive/dogs_cleaned.csv`: `php sql/generate-breeds-seed.php`
6. Open `http://localhost/WS101_Aliwate/WS101-Pawdar/web/`

Demo login: any seeded account, password `password`.

## Email (Resend)

Verification and password reset use [Resend](https://resend.com). Configure `RESEND_API_KEY` in root `.env`. Full setup: **`docs/EMAIL_SETUP.md`**.

## Requirements

- **PHP 7.4+** with PDO MySQL and **cURL**
- **MariaDB/MySQL** (XAMPP or InfinityFree)
- Lucide icons and Google Fonts load from CDN (internet required)

## Notes

- Sign up sends a verification email; users must confirm before accessing the app.
- Login/signup forms require a verified email for feed and other protected pages.
- Design is **responsive**: sidebar on desktop, bottom nav on mobile.
- Original design canvas remains in `Pawdar Design System/` for reference.

## Local preview (optional)

If you have PHP installed locally:

```powershell
cd web
php -S localhost:8080
```

Then open http://localhost:8080/
