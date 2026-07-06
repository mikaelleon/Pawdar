# Pawdar — Feature Audit

**Last updated:** July 2026  
**Scope:** `web/` PHP application, MySQL schema, JS modules  
**Related:** [UI_COMPLETENESS_TASKS.md](./UI_COMPLETENESS_TASKS.md) (UI task tracker), [UI_FEEDBACK_SPEC.md](./UI_FEEDBACK_SPEC.md) (professor feedback fixes), [README.md](../README.md) (project overview)

---

## Executive Summary

Pawdar is a **PHP + MySQL** civic platform for dog registration, incident reporting, and barangay-level response across six user roles in Batangas Province.

| Area | Status |
|---|---|
| Auth & onboarding | **Mostly complete** — login, 3-step signup, email verify, password reset, pending approval |
| Dog registry & QR | **Complete** — browse, filter, register wizard, profile (edit, photos, physical fields), vet co-sign |
| Incidents & feed | **Mostly complete** — feed, map, report drawer (description step 2, dog pre-fill), corroborate, LGU case updates + remarks |
| LGU / admin tools | **Partial** — case board live; analytics + advisories + case-detail page incomplete |
| Rescue & adoption | **Partial** — rescue board + claim/status live; adoption listings often empty |
| Infrastructure | **Mostly complete** — env loader, Resend mail, InfinityFree-compatible SQL seeds |
| QA / manual testing | **Not started** — no automated E2E; manual QA checklist pending |

**Overall product readiness:** ~**70%** of stated README goals are functional end-to-end. Remaining gaps are concentrated in analytics, advisories, desktop report wizard, interactive rabies checklist, and several profile/admin polish items.

---

## Implemented Features

### 1. Authentication & Account Lifecycle

| Feature | Description | Key files | Level |
|---|---|---|---|
| Login | AJAX login, lockout after failed attempts, role-based redirect | `web/login.php`, `web/auth/login-handler.php`, `web/assets/js/auth.js` | **Full** |
| Signup wizard | 3 steps: Account → Role & location → Verify | `web/signup.php`, `web/auth/signup-handler.php`, `web/assets/js/signup-wizard.js` | **Full** |
| Structured identity | First/middle/last name, +63 phone normalization | `signup-handler.php`, `web/includes/locations.php` | **Full** |
| City → barangay cascade | Reference tables, AJAX dropdown | `web/ajax/barangays.php`, `schema-v5-locations.sql`, `schema-v5-barangays-seed.sql` | **Full** |
| Email availability check | Debounced AJAX on signup step 1 | `web/ajax/check_email.php` | **Full** |
| Email verification | Token send, verify link, resend with 60s cooldown | `web/verify.php`, `web/auth/verify-email.php`, `web/email_verified.php`, `web/includes/mailer.php` | **Full** *(needs Resend API key)* |
| Pending approval | Vet/LGU/Rescue blocked until admin approves | `web/pending.php`, `require_login_active()` in `web/includes/auth.php` | **Full** |
| Forgot / reset password | Email link + token expiry | `web/forgot_password.php`, `web/reset_password.php` | **Full** |
| Logout | Session destroy | `web/auth/logout.php` | **Full** |
| CSRF protection | Token on forms and mutating AJAX | `web/includes/bootstrap.php`, ajax handlers | **Full** |
| Session guards | Role checks, active-user gate, email-verified gate | `web/includes/auth.php`, `web/includes/helpers.php` | **Full** |

**Demo accounts** (password `password`): seeded in `web/sql/schema.sql` + auto-verified in `schema-v6-auth-user.sql`.

---

### 2. Public & Marketing

| Feature | Description | Key files | Level |
|---|---|---|---|
| Landing page | Hero, features, how-it-works, audience grid, footer CTAs | `web/index.html`, `web/includes/landing-header.php`, `web/includes/landing-footer.php` | **Full** |
| Dark/light theme (landing) | Toggle + persistence | `web/assets/js/theme-toggle.js`, `web/assets/css/pawdar.css` | **Full** |
| Mobile landing nav | Drawer with auth links | `landing-header.php`, `web/assets/js/app.js` | **Full** |
| Entry redirect | `index.php` → `index.html` | `web/index.php`, `web/.htaccess` | **Full** |

---

### 3. App Shell & Shared UI

| Feature | Description | Key files | Level |
|---|---|---|---|
| App layout | Sidebar, topbar, bottom nav, content area | `web/includes/app-layout.php`, `sidebar.php`, `topbar.php`, `bottom-nav.php` | **Full** |
| Role-filtered navigation | Nav items hidden by role | `web/includes/sidebar.php`, `role_can_see_nav()` in `helpers.php` | **Full** |
| Design system | Tokens, buttons, cards, chips, forms | `web/assets/css/pawdar.css` | **Full** |
| Toast & field errors | Shared feedback helpers | `web/assets/js/ui.js` | **Full** |
| Theme toggle (app) | Sun/moon in topbar | `web/includes/topbar.php`, `theme-toggle.js` | **Full** |
| Breadcrumbs | Partial for nested pages | `web/partials/breadcrumb.php` | **Partial** |

---

### 4. Incident Feed & Reporting

| Feature | Description | Key files | Level |
|---|---|---|---|
| Incident feed | Barangay-scoped cards from DB | `web/feed.php`, `web/includes/incidents.php`, `web/partials/incident-cards.php` | **Full** |
| Filter chips | Type/severity filter via AJAX | `web/ajax/feed-filter.php`, `web/assets/js/feed.js` | **Full** |
| Infinite scroll | Paginated load-more with skeleton | `feed.js` | **Full** |
| Corroborate incident | Witness support action | `web/ajax/corroborate.php`, `feed.js` | **Full** |
| Incident detail page | Live incident + case info | `web/incident.php`, `web/includes/incidents.php` | **Full** |
| Report drawer (mobile) | 3-step wizard → create incident | `web/partials/report-drawer.php`, `web/ajax/submit-report.php`, `web/assets/js/report-drawer.js` | **Full** |
| Guest incident view | Public read without app shell | `incident.php` | **Partial** |
| Feed empty state | Dedicated zero-incident UI | — | **Missing** |

---

### 5. Map

| Feature | Description | Key files | Level |
|---|---|---|---|
| Leaflet map | Pins, clusters, heat layer options | `web/map.php`, `web/assets/js/map.js` | **Full** |
| Live pin refresh | AJAX filter by type/range | `web/ajax/map_incidents.php` | **Full** |
| Loading / empty / geo-error UI | Client-side states | `map.js` | **Partial** |
| Feed widget map preview | Server-rendered mini map | `web/partials/widget_map.php` | **Full** |

---

### 6. Dog Registry

| Feature | Description | Key files | Level |
|---|---|---|---|
| Registry browse | Bento/grid/list views, filters | `web/registry.php`, `web/includes/dogs.php`, `web/ajax/registry_dogs.php`, `web/assets/js/registry.js` | **Full** |
| Register Dog wizard | 3 steps → create dog + vaccine record | `web/register_dog.php`, `web/ajax/register_dog.php`, `web/assets/js/register-dog.js` | **Full** |
| Dog profile | Photo, health, vaccination history | `web/dog-profile.php`, `web/includes/dogs.php` | **Partial** |
| QR registry tag | QR image generation | `web/qr.php` (external `api.qrserver.com`) | **Full** |
| Public QR scan | Lookup by registry ID | `web/scan.php` | **Full** |
| Vet co-sign vaccination | Verify vax record | `web/ajax/cosign_vaccine.php`, `web/assets/js/dog-profile.js` | **Full** |
| Breed autocomplete | Search breeds during registration | `web/ajax/search_breeds.php` | **Full** |

---

### 7. Breed Directory

| Feature | Description | Key files | Level |
|---|---|---|---|
| Breed grid | DB-backed directory | `web/breeds.php`, `web/includes/breeds.php` | **Full** |
| Search & filters | AJAX search, size filter | `web/ajax/search_breeds.php`, `web/ajax/breed_detail.php`, `web/assets/js/breeds.js` | **Full** |
| Breed detail panel | Scores, health notes, related dogs | `web/ajax/breed_dogs.php` | **Full** |
| Kaggle / archive breed seed | CLI or phpMyAdmin SQL | `web/sql/schema-v3-breeds-seed.sql`, `generate-breeds-seed.php`, `import-breeds.php` | **Full** |

---

### 8. LGU Case Management

| Feature | Description | Key files | Level |
|---|---|---|---|
| Case board | Filterable list, summary strip | `web/cases.php`, `web/includes/cases.php` | **Full** |
| Case status updates | 5-state workflow incl. "Action Taken" | `web/ajax/update_case.php`, `web/assets/js/cases.js`, `feed.js` | **Full** |
| Case history audit | Status change log | `case_history` table, `update_case.php` | **Full** |
| Rabies monitoring flag | Auto-set on bite cases | `web/includes/cases.php` | **Full** |
| Rabies checklist seed | 14-day rows created on status change | `rabies_checklist` table, `ensure_rabies_checklist()` | **Partial** (read-only display) |
| Case detail page | Dedicated case + rabies UI | `web/case-detail.php` | **Not wired** (static mock) |
| LGU advisories | Publish barangay advisories | — | **Missing** (table only) |

**Note:** `cases.php` links to `incident.php?id=…`, not `case-detail.php`. Live case work happens on the incident detail page today.

---

### 9. Rescue & Adoption

| Feature | Description | Key files | Level |
|---|---|---|---|
| Rescue board | Stray/rescue case list | `web/rescue.php`, `web/includes/rescue.php` | **Full** |
| Claim stray | Rescue org claims injured stray | `web/ajax/claim-stray.php`, `web/assets/js/rescue.js` | **Full** |
| Update rescue status | Pipeline status changes | `web/ajax/update_rescue_status.php`, `rescue.js` | **Full** |
| Adoption listings | Display adoptable dogs | `fetch_adoption_listings()` in `rescue.php` | **Partial** (no seed data; table unused in UI flows) |

---

### 10. First Aid & Community Guides

| Feature | Description | Key files | Level |
|---|---|---|---|
| First-aid guides | DB-backed step guides | `web/first-aid.php`, `web/includes/first-aid-data.php` | **Full** |
| Client search | Filter guides in browser | `web/assets/js/first-aid.js` | **Full** |
| PDF download | Export guide | `web/first-aid/download.php` | **Full** |
| Report from guide | Link to report flow | `first-aid.js` | **Full** |

---

### 11. Profile & Notifications

| Feature | Description | Key files | Level |
|---|---|---|---|
| Profile update | Name, email, phone, barangay text | `web/profile.php` | **Partial** (no city/barangay cascade) |
| Password change | In-profile update | `profile.php` | **Full** |
| Notification preferences | AJAX toggles | `web/ajax/profile_prefs.php`, `web/assets/js/profile.js` | **Full** |
| Notifications page | Full list, mark read | `web/notifications.php`, `web/includes/notifications.php` | **Full** |
| Bell dropdown | Unread count + preview | `web/ajax/notifications.php`, `web/ajax/notif_count.php`, `web/assets/js/app.js` | **Full** |

---

### 12. Admin

| Feature | Description | Key files | Level |
|---|---|---|---|
| Pending user queue | Vet/LGU/Rescue awaiting approval | `web/admin.php` | **Full** |
| Pending dog queue | Dogs awaiting approval | `admin.php` | **Full** |
| Approve / reject actions | AJAX approve user or dog | `web/ajax/admin_action.php`, `web/assets/js/admin.js` | **Full** |
| Approve confirmation | Typed confirm or modal guard | — | **Missing** |

---

### 13. Analytics (LGU / Admin)

| Feature | Description | Key files | Level |
|---|---|---|---|
| Summary count cards | Bites, strays, aggressive, total | `web/analytics.php`, `fetch_map_counts()` | **Partial** |
| Charts / trends | Time-series, drill-down | — | **Missing** |
| Export / DENR reports | Regulatory export | — | **Missing** |

---

### 14. Database & Schema

| Table | Purpose | Migration |
|---|---|---|
| `user` | Accounts, roles, auth tokens, notify prefs | `schema.sql`, `schema-v6-auth-user.sql` |
| `dog` | Registry records | `schema.sql`, `schema-v2.sql` |
| `incident` | Community reports | `schema.sql`, `schema-v4-screens.sql` |
| `vaccinerecord` | Vaccination history | `schema.sql`, `schema-v2.sql` |
| `case` | LGU case tracking | `schema-v4-screens.sql` |
| `case_history` | Case audit trail | `schema-v4-screens.sql` |
| `rabies_checklist` | 14-day monitoring rows | `schema-v4-screens.sql` |
| `corroborations` | Witness corroboration | `schema-v4-screens.sql` |
| `notifications` | In-app alerts | `schema-v4-screens.sql` |
| `rescue_cases` / `rescue_case_history` | Rescue pipeline | `schema-v4-screens.sql` |
| `stray_sightings` | Stray reports | `schema-v4-screens.sql` |
| `adoption_listings` | Adoption | `schema-v4-screens.sql` *(no app wiring)* |
| `advisories` | LGU advisories | `schema-v4-screens.sql` *(no app wiring)* |
| `first_aid_guides` | Community guides | `schema-v2.sql` |
| `breeds` | Breed directory | `schema-v3-breeds.sql` |
| `city` / `barangay` | Batangas locations (5 cities, 287 barangays) | `schema-v5-locations.sql`, `schema-v5-barangays-seed.sql` |

**Setup order:** `schema.sql` → `schema-v2.sql` → `schema-v3-breeds.sql` → **`schema-v3-breeds-seed.sql`** → `schema-v4-screens.sql` → `schema-v5-locations.sql` → `schema-v5-barangays-seed.sql` → `schema-v6-auth-user.sql` (or `php web/sql/setup.php` + import scripts locally).

---

### 15. Infrastructure & Deployment

| Feature | Description | Key files | Level |
|---|---|---|---|
| Env loading | `$_ENV`-first, optional `putenv` | `web/includes/env.php` | **Full** |
| DB connection | PDO MySQL via env | `web/includes/db.php` | **Full** |
| Resend email | Verification + password reset | `web/includes/mailer.php`, `docs/EMAIL_SETUP.md` | **Full** *(domain verify needed for prod)* |
| InfinityFree compatibility | No-CLI barangay seed, v6 auth columns | `schema-v5-barangays-seed.sql`, `schema-v6-auth-user.sql` | **Full** |
| PHP in HTML | Landing served as `.html` with PHP includes | `web/.htaccess` | **Partial** (host-dependent) |
| Reserved SQL table name | Backtick `` `user` `` in queries | auth/includes/ajax files | **Full** |

---

## Missing or Incomplete Features

### Critical gaps (blocks stated product goals)

| Gap | What's missing | Impact | Related files |
|---|---|---|---|
| **Case detail page** | No auth, no DB query, hardcoded mock data; `fetch_case_detail()` unused | LGU rabies-watch UI not reachable as designed | `web/case-detail.php`, `web/partials/case-detail-content.php`, `web/includes/cases.php` |
| **Analytics dashboard** | Count cards only; no charts, trends, exports | "Analytics" nav under-delivers for LGU | `web/analytics.php` |
| **LGU advisories** | `advisories` table exists; no CRUD, no publish flow | Publish Advisory buttons are decorative | `case-detail.php`, `schema-v4-screens.sql` |
| **Desktop report wizard** | Steps 1–3 are static HTML; no submit | Desktop users lack full-page report path | `web/report.php`, `web/report-details.php` |
| **Rabies checklist interaction** | Rows auto-created but cannot mark Checked/Flagged | 14-day monitoring is display-only | `rabies_checklist` table, `incident.php` |
| **Missing JS file** | `incident-detail.js` referenced but not in repo | 404 on incident page | `web/incident.php` line 34 |

### Feature gaps (planned in README / spec)

| Gap | What's missing | Related files |
|---|---|---|
| **Role-specific signup fields** | Vet license, LGU department, rescue org name | `signup.php`, `signup-handler.php` |
| **SMS OTP** | Optional approval-path verification | Not started |
| **Dog profile edit / deactivate** | Buttons exist, no backend | `dog-profile.php`, `dog-profile.js` |
| **Flag dog** | Toast only; no server persistence | `dog-profile.js` |
| **Profile location cascade** | Free-text barangay vs signup city/barangay IDs | `profile.php` |
| **Feed empty state** | No "no incidents yet" block | `feed.php`, `incident-cards.php` |
| **403 / 404 pages** | Branded error pages not built | — |
| **Adoption workflow** | Listings table unused; no create/list/manage UI | `adoption_listings`, `rescue.php` |
| **Push / email on case change** | Notifications table exists; no event triggers on case/rescue updates | `notifications` table |
| **Audit trail (admin/LGU)** | Case history exists; broader admin action log missing | — |
| **Tagalog locale** | English only | — |
| **Full WCAG pass** | Partial aria on auth; incomplete app-wide | — |
| **Rate limiting on auth** | Lockout exists; no IP/rate cap | `login-handler.php` |
| **DEPLOYMENT.md** | Referenced in UI tasks doc, not written | — |

### Dead code / legacy

| Item | Notes |
|---|---|
| `web/ajax/map-pins.php` | Exists; frontend uses `map_incidents.php` instead |
| `web/ajax/update-case-status.php` | Legacy duplicate of `update_case.php`; unused |
| `web/case-detail.php` | Static shell disconnected from live case flow |

---

## Role Capability Matrix

Post-login landing from `redirect_after_login()` in `web/includes/auth.php`. Nav visibility from `role_can_see_nav()` in `web/includes/helpers.php`.

| Capability | Community Reporter | Dog Owner | Veterinarian | LGU Official | Rescue Org | Admin |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Default landing | Feed | Feed | Registry | Cases | Rescue | Admin |
| Feed / Map / Registry / Breeds / First Aid | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Report incidents | ✓ | ✓ | ✗ | ✗ | ✗ | ✓ |
| Register dog | ✗ | ✓ | ✗ | ✗ | ✗ | ✓ |
| Cases nav + status updates | ✗ | ✗ | ✗ | ✓ | ✗ | ✓ |
| Analytics nav | ✗ | ✗ | ✗ | ✓ | ✗ | ✓ |
| Rescue board | ✗ | ✗ | ✗ | ✗ | ✓ | ✓ |
| Admin console | ✗ | ✗ | ✗ | ✗ | ✗ | ✓ |
| Co-sign vaccines | ✗ | ✗ | ✓ | ✗ | ✗ | ✓ |
| Claim strays | ✗ | ✗ | ✗ | ✗ | ✓ | ✓ |
| Approve pending accounts/dogs | ✗ | ✗ | ✗ | ✗ | ✗ | ✓ |
| Signup → pending until approved | ✗ | ✗ | ✓ | ✓ | ✓ | N/A |
| Phone required at signup | ✗ | ✓ | ✓ | ✓ | ✓ | — |

**Approval flow:** Vet / LGU / Rescue sign up → email verify → `pending.php` → Admin approves via `admin.php`.

---

## Page Inventory

| Page | Wired to DB? | Notes |
|---|---|---|
| `index.html` | N/A | Marketing landing |
| `login.php` | ✓ | AJAX auth |
| `signup.php` | ✓ | Full wizard |
| `verify.php` | ✓ | Email verify + resend |
| `email_verified.php` | Session | Post-verify |
| `pending.php` | Session | Approval gate |
| `forgot_password.php` | ✓ | Resend email |
| `reset_password.php` | ✓ | Token reset |
| `feed.php` | ✓ | Live feed + drawer |
| `map.php` | ✓ | Live pins |
| `incident.php` | ✓ | Live detail |
| `report.php` | ✗ | **Static UI only** |
| `report-details.php` | ✗ | **Static UI only** |
| `cases.php` | ✓ | Live case board |
| `case-detail.php` | ✗ | **Static mock** |
| `registry.php` | ✓ | Live registry |
| `register_dog.php` | ✓ | Live wizard |
| `dog-profile.php` | ✓ | Live profile; edit stub |
| `breeds.php` | ✓ | Live directory |
| `first-aid.php` | ✓ | Live guides |
| `rescue.php` | ✓ | Live rescue board |
| `profile.php` | ✓ | Live update |
| `notifications.php` | ✓ | Live list |
| `analytics.php` | ✓ | **Counts only** |
| `admin.php` | ✓ | Live approval queue |
| `scan.php` | ✓ | Public QR lookup |

---

## AJAX Endpoint Inventory

| Endpoint | Auth | Function | Status |
|---|---|---|---|
| `admin_action.php` | Admin | Approve user/dog | **Active** |
| `barangays.php` | Public | City → barangay list | **Active** |
| `breed_detail.php` | Active login | Breed JSON | **Active** |
| `breed_dogs.php` | Active login | Dogs by breed | **Active** |
| `check_email.php` | Public | Email availability | **Active** |
| `claim-stray.php` | Rescue, Admin | Claim stray | **Active** |
| `corroborate.php` | Logged in | Corroborate incident | **Active** |
| `cosign_vaccine.php` | Veterinarian | Verify vaccination | **Active** |
| `feed-filter.php` | Logged in | Filter/paginate feed | **Active** |
| `map_incidents.php` | Logged in | Map pin JSON | **Active** |
| `map-pins.php` | Logged in | Pin JSON | **Unused** |
| `notifications.php` | Logged in | Bell + mark read | **Active** |
| `notif_count.php` | Active login | Badge count | **Active** |
| `profile_prefs.php` | Active login | Notify toggles | **Active** |
| `register_dog.php` | Owner, Admin | Create dog | **Active** |
| `registry_dogs.php` | Active login | Registry HTML | **Active** |
| `search_breeds.php` | Active login | Breed search | **Active** |
| `search_dogs.php` | Active login | Dog search | **Active** |
| `submit-report.php` | Reporter roles | Create incident | **Active** |
| `update_case.php` | LGU, Admin | Case status + rabies rows | **Active** |
| `update_rescue_status.php` | Rescue, Admin | Rescue pipeline | **Active** |
| `update-case-status.php` | LGU, Admin | Legacy status | **Unused** |

---

## Manual QA Gaps

No automated browser test suite is in the repository. The following flows need manual QA sign-off before release:

| Area | Status |
|---|---|
| Login + role redirects | Needs manual pass |
| Signup full path (with email) | Needs manual pass |
| Email verify flow | Needs manual pass |
| Pending approval page | Needs manual pass |
| Feed, registry, cases, breeds, rescue | Needs manual pass |
| Report drawer | Needs manual pass |
| Analytics page | Needs manual pass |
| Case detail page | Needs manual pass |
| Admin approve action | Needs manual pass |
| Notifications page | Needs manual pass |

---

## Recommended Fix Priority

1. **Add `incident-detail.js`** or remove broken script reference — quick 404 fix.
2. **Wire `case-detail.php`** to `fetch_case_detail()` + auth + link from `cases.php`.
3. **Build analytics charts** or rename nav until charts exist.
4. **Wire `report.php`** to same backend as report drawer, or redirect to feed with drawer open.
5. **Rabies checklist AJAX** — mark days Checked/Flagged.
6. **LGU advisories CRUD** — use `advisories` table; wire Publish Advisory.
7. **Role-specific signup fields** — DB columns + wizard step conditionals.
8. **Manual QA checklist** — record pass/fail per inventory page.
9. **Production `.env` + schema v5/v6 on host** — InfinityFree deploy checklist.

---

## Cross-Reference

| Document | Purpose |
|---|---|
| [UI_COMPLETENESS_TASKS.md](./UI_COMPLETENESS_TASKS.md) | UI task checklist with % completion |
| [EMAIL_SETUP.md](./EMAIL_SETUP.md) | Resend configuration |
| [Batangas_Cities_and_Barangays.md](./Batangas_Cities_and_Barangays.md) | Location reference source |
| [web/README.md](../web/README.md) | Deploy steps for hosting |
| [README.md](../README.md) | Project overview and milestones |

---

*Maintainer: Kimberly Claire A. Aliwate · WS101 Pawdar*
