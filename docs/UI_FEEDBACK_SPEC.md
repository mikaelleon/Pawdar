# Pawdar — Dog Profile + Professor Feedback UI Fixes

**Status:** Implemented (Jul 2026)  
**Schema:** `web/sql/schema-v8-ui-fixes.sql` (import after v7)

---

## Part 1 — Dog Profile Screen

| Item | Status | Notes |
|------|--------|-------|
| QR card, registry ID copy, trait bars, vaccination badge, Flag action | Kept | No change |
| Past Incidents card styling | Done | `label-upper` inside bordered card; empty state illustration |
| Dog avatar / photo | Done | `photo_path` when uploaded; breed image via dog.ceo proxy fallback |

---

## Part 2 — Professor Feedback

| # | Item | Status | Implementation |
|---|------|--------|----------------|
| 2.1 | Create Case → Report Incident | Done | `Report Incident` opens drawer with dog pre-filled; LGU gets separate **Manage cases** link |
| 2.2 | Edit Profile + new fields | Done | Modal + `ajax/update_dog.php`; register form adds coat, weight, marks, temperament |
| 2.3 | Breed photos | Done | `breeds.image_url`, `ajax/breed-image.php` (dog.ceo), Breed Directory + profile fallback |
| 2.4 | Graphics polish | Done | Paw-pattern backgrounds, summary-card accents, empty-state illustrations |
| 2.5 | Report description | Done | Description on step 2; shown in Feed “More details” + Case Management + incident detail |
| 2.6 | Trash Disturbance → Disturbance | Done | Renamed in helpers, map, feed, first aid, SQL migration; icon `footprints` |
| 2.7 | Remarks on status update | Done | Remarks modal → `case_history.notes`; timeline on incident detail |

---

## Database migration (phpMyAdmin)

After `schema-v7-cases-demo.sql`:

```
web/sql/schema-v8-ui-fixes.sql
```

Adds:

- `dog.coat_color`, `weight_kg`, `distinguishing_marks`, `temperament_notes`
- `breeds.image_url`
- Renames `Trash Disturbance` → `Disturbance` in incidents + first aid guides

Local: `php sql/setup.php` includes v8 automatically.

---

## Key files

| Area | Files |
|------|-------|
| Dog profile | `dog-profile.php`, `partials/dog-edit-modal.php`, `assets/js/dog-profile.js` |
| Report drawer | `partials/report-drawer.php`, `assets/js/report-drawer.js` |
| Breed images | `includes/breed-media.php`, `ajax/breed-image.php` |
| Case remarks | `assets/js/case-status-update.js`, `assets/js/ui.js`, `includes/cases.php` |
| Register dog | `register_dog.php`, `ajax/register_dog.php` |

---

## Demo verification

1. **Owner** (`rosa.castillo@email.com` / `password`) → dog profile → Edit Profile, Report Incident pre-fill  
2. **Reporter** → Report drawer step 2 description  
3. **LGU** (`luis.cruz@email.com`) → Case Management status change → remarks modal; description column visible  
4. **Breed Directory** → breed photos load (requires outbound HTTPS to dog.ceo on first view)
