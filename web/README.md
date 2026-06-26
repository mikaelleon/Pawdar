# Pawdar Web — InfinityFree Deployment

This folder contains the production-ready HTML/PHP site converted from the Pawdar Design System canvas file.

## Upload to InfinityFree

1. Log in to [InfinityFree](https://infinityfree.com/) and open your hosting control panel (VistaPanel).
2. Open **File Manager** (or connect via FTP using the credentials in your panel).
3. Go to **`htdocs`** (this is your website root).
4. Upload **all files and folders** from this `web/` directory into `htdocs`:
   - `index.php`
   - `feed.php`, `map.php`, `dog-profile.php`, etc.
   - `includes/`
   - `partials/`
   - `assets/`
5. Visit your site URL (e.g. `https://yoursite.infinityfreeapp.com/`).

## Site structure

| File | Description |
|------|-------------|
| `index.php` | Landing / marketing page |
| `login.php` | Log in |
| `signup.php` | Create account |
| `feed.php` | Home incident feed |
| `map.php` | Incident map |
| `dog-profile.php` | Dog registry profile |
| `report.php` | Report incident (step 1) |
| `report-details.php` | Report incident (step 3) |
| `cases.php` | LGU case management |
| `case-detail.php` | Case detail + rabies watch |
| `breeds.php` | Breed directory |
| `first-aid.php` | First aid guides |

## Requirements

- **PHP 7.4+** (InfinityFree provides PHP 8.x — fully supported)
- No database required for this static prototype
- Lucide icons and Google Fonts load from CDN (internet required)

## Notes

- Login/signup forms redirect to `feed.php` as a demo (no backend yet).
- Design is **responsive**: sidebar on desktop, bottom nav on mobile.
- Original design canvas remains in `Pawdar Design System/` for reference.

## Local preview (optional)

If you have PHP installed locally:

```powershell
cd web
php -S localhost:8080
```

Then open http://localhost:8080/
