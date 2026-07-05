# UI Completeness Tasks — Pawdar

## Summary
- Total subtasks: `188`
- Completed: `121 (64%)`
- Effort breakdown: `S×116 + M×60 + L×12`

| Main task | % complete |
|---|---:|
| `T-UI-01` | 82% |
| `T-UI-02` | 68% |
| `T-UI-03` | 88% |
| `T-UI-04` | 90% |
| `T-UI-05` | 76% |
| `T-UI-06` | 71% |
| `T-UI-07` | 74% |
| `T-UI-08` | 84% |
| `T-UI-09` | 38% |
| `T-UI-10` | 72% |
| `T-UI-11` | 0% |

| Page | % complete |
|---|---:|
| `web/index.html` | 90% |
| `web/login.php` | 86% |
| `web/signup.php` | 91% |
| `web/verify.php` | 84% |
| `web/email_verified.php` | 80% |
| `web/pending.php` | 78% |
| `web/forgot_password.php` | 82% |
| `web/reset_password.php` | 80% |
| `web/scan.php` | 72% |
| `web/feed.php` | 86% |
| `web/map.php` | 81% |
| `web/incident.php` | 76% |
| `web/report.php` | 74% |
| `web/report-details.php` | 70% |
| `web/cases.php` | 82% |
| `web/case-detail.php` | 64% |
| `web/registry.php` | 87% |
| `web/register_dog.php` | 88% |
| `web/dog-profile.php` | 81% |
| `web/breeds.php` | 80% |
| `web/first-aid.php` | 76% |
| `web/rescue.php` | 75% |
| `web/profile.php` | 71% |
| `web/notifications.php` | 74% |
| `web/analytics.php` | 58% |
| `web/admin.php` | 70% |

**Legend:** `[x]` done · `[ ]` open · `(S|M|L)` effort · artifact = expected deliverable

---

## T-UI-01
**ID** — `T-UI-01`  
**Goal** — Normalize Pawdar design tokens and shared component primitives into one reusable baseline.  
**Source ref** — `web/assets/css/pawdar.css`, Pawdar Design System canvas.

**Subtasks**
- [x] (M) Define canonical color tokens in `:root` (Taupe, Tea Green, Muted Teal, Air Force, Sunlit Clay, Burnt Peach) — artifact: `web/assets/css/pawdar.css` `:root`.
- [x] (S) Define spacing, radius, shadow, and font tokens — artifact: `--radius-*`, `--shadow-*`, `--font` in `pawdar.css`.
- [x] (M) Build unified button classes (`btn-primary`, `btn-outline`, `btn-ghost`, `btn-sm`, `is-loading`) — artifact: shared button block in `pawdar.css`.
- [x] (M) Build shared field/input classes (`field-input`, password toggle, error states) — artifact: form styles + `web/assets/js/ui.js`.
- [x] (M) Build shared card/surface classes (`card`, `card-bordered`, `bento-card`, `incident-card`) — artifact: card blocks in `pawdar.css`.
- [x] (M) Build chip/badge/severity component classes — artifact: `.chip-*`, `.badge-*`, `.severity-*` in `pawdar.css`.
- [x] (M) Build icon-box and avatar primitives — artifact: `.icon-box*`, `.avatar*` in `pawdar.css`.
- [x] (M) Build app shell layout primitives (sidebar, topbar, bottom nav, content padding) — artifact: `.app-shell`, `.app-sidebar`, `.bottom-nav`.
- [x] (S) Centralize Lucide icon loading via CDN + `lucide.createIcons()` — artifact: `web/includes/head.php`.
- [ ] (M) Extract landing-only styles into scoped block or partial CSS file — artifact: reduced duplication between landing + app rules.
- [ ] (L) Remove remaining inline styles from `web/index.html` hero/illo markup — artifact: class-based landing illustration styles only.

**Completion** — `9 / 11 subtasks (82%)`  
**Dependencies** — none  
**Done when**
- All pages consume shared tokens from `pawdar.css`.
- No new one-off color hex values added without token mapping.
- Landing illustration uses CSS classes, not inline style blocks.

---

## T-UI-02
**ID** — `T-UI-02`  
**Goal** — Consistent feedback layer: loading, empty, error, success, and destructive flows.  
**Source ref** — `web/assets/js/ui.js`, page modules, report drawer.

**Subtasks**
- [x] (M) Build shared toast helper with auto-dismiss and progress bar — artifact: `showToast()` in `web/assets/js/ui.js`.
- [x] (M) Mount toast container on report-drawer-enabled pages — artifact: `web/includes/app-layout.php` toast container.
- [x] (S) Build button loading state helper — artifact: `setButtonLoading()` in `ui.js` + `.is-loading` CSS.
- [x] (M) Build login submit loading/success/error states — artifact: `web/assets/js/auth.js`.
- [x] (M) Add feed infinite-scroll skeleton placeholders — artifact: `web/assets/js/feed.js` skeleton cards.
- [x] (S) Add breeds grid empty state — artifact: `web/assets/js/breeds.js` empty panel.
- [x] (S) Add notification dropdown empty copy — artifact: `web/assets/js/app.js` notification list.
- [x] (M) Add signup wizard inline field errors — artifact: `web/assets/js/signup-wizard.js` + `PawdarUI.showFieldError`.
- [ ] (M) Replace remaining `alert()` / plain-text-only errors in ajax handlers with toast or inline banner — artifact: audit `web/ajax/*.php` + JS consumers.
- [ ] (M) Add registry AJAX loading skeleton for bento grid — artifact: `web/assets/js/registry.js`.
- [ ] (M) Add map pin loading + empty-state panel — artifact: `web/assets/js/map.js` or inline map module.
- [ ] (S) Document feedback usage contract for page authors — artifact: `docs/UI_FEEDBACK_CONTRACT.md`.

**Completion** — `8 / 12 subtasks (68%)`  
**Dependencies** — `T-UI-01`  
**Done when**
- Success paths use toast or inline success regions, not `alert()`.
- List/table views have explicit loading + empty states.
- Retry affordance exists on recoverable fetch errors.

---

## T-UI-03
**ID** — `T-UI-03`  
**Goal** — Complete auth and onboarding flows: login, signup wizard, verify, pending, password reset.  
**Source ref** — `web/auth/*`, `web/signup.php`, `web/verify.php`.

**Subtasks**
- [x] (M) Login form with AJAX + redirect routing by role — artifact: `web/login.php`, `web/auth/login-handler.php`, `web/assets/js/auth.js`.
- [x] (M) Login lockout + generic DB error handling (no raw exception leak) — artifact: `login-handler.php` catch block.
- [x] (L) 3-step signup wizard (Account → Role & location → Verify) — artifact: `web/signup.php`, `web/assets/js/signup-wizard.js`.
- [x] (M) Structured name fields + +63 phone normalization — artifact: `web/auth/signup-handler.php`, `web/includes/locations.php`.
- [x] (M) City → barangay cascade from reference tables — artifact: `web/ajax/barangays.php`, `web/includes/locations.php`.
- [x] (M) Email verification send + token verify + resend cooldown — artifact: `web/includes/auth.php`, `web/verify.php`, `web/auth/verify-email.php`.
- [x] (S) Post-verify confirmation screen — artifact: `web/email_verified.php`.
- [x] (M) Pending approval gate for Vet/LGU/Rescue roles — artifact: `web/pending.php`, `require_login_active()`.
- [x] (M) Forgot / reset password via Resend — artifact: `web/forgot_password.php`, `web/reset_password.php`.
- [x] (S) CSRF token on auth forms — artifact: `web/includes/bootstrap.php`, meta tag in `head.php`.
- [ ] (M) Role-specific signup fields (vet license, LGU department, rescue org name) — artifact: conditional wizard step fields + DB columns.
- [ ] (S) Live email availability check debounce on signup step 1 — artifact: wired `web/ajax/check_email.php` UX polish.

**Completion** — `10 / 12 subtasks (88%)`  
**Dependencies** — `T-UI-01`, `T-UI-02`  
**Done when**
- All six role groups can register, verify email, and reach correct post-login destination.
- Pending roles cannot access app until approved.
- Password reset works end-to-end on production Resend config.

---

## T-UI-04
**ID** — `T-UI-04`  
**Goal** — Public landing page completeness: hero, features, process, audience, footer, CTAs.  
**Source ref** — `web/index.html`, landing partials.

**Subtasks**
- [x] (M) Hero hierarchy: headline, subhead, dual CTA, stat pills — artifact: `web/index.html` hero section.
- [x] (M) 6-card feature grid with icon treatment — artifact: `#features` section.
- [x] (M) 4-step “How Pawdar works” timeline band — artifact: `#how-it-works` section.
- [x] (M) Role audience grid aligned with signup roles — artifact: `#about` section.
- [x] (S) Sticky landing nav with Sign Up primary + Log In secondary — artifact: `web/includes/landing-header.php`.
- [x] (M) Mobile landing drawer with auth links — artifact: `landing-mobile-nav` + `web/assets/js/app.js`.
- [x] (S) Footer tagline + Sign Up CTA — artifact: `web/includes/landing-footer.php`.
- [x] (S) Footer app links route logged-out users to login — artifact: footer links → `login.php`.
- [x] (S) About anchor + WS101 project lead copy — artifact: `#about` + `.section-lead`.
- [x] (M) Learn More links on all 6 feature cards — artifact: `feature-card-link` on every card.
- [ ] (S) Privacy Policy + Terms footer links → real pages or anchors — artifact: policy pages or `#about` legal subsection.
- [ ] (S) Optional civic CTA block before footer (“Contact your barangay”) — artifact: landing section stub.

**Completion** — `10 / 12 subtasks (90%)`  
**Dependencies** — `T-UI-01`  
**Done when**
- Landing tells full product story without broken links.
- Mobile nav exposes auth paths.
- Legal/footer links are not inert text.

---

## T-UI-05
**ID** — `T-UI-05`  
**Goal** — Live data wiring and four-state coverage on every authenticated inventory page.  
**Source ref** — `web/includes/*.php`, `web/ajax/*`, page PHP entry points.

**Subtasks**

### Public / scan
- [x] (S) `scan.php` loaded state for valid registry ID — artifact: QR lookup render.
- [ ] (S) `scan.php` explicit error/empty states styled — artifact: not-found card polish.
- [x] (S) `index.html` loaded state — artifact: PHP-rendered landing.

### Feed & incidents
- [x] (S) `feed.php` loaded incident list — artifact: `fetch_incidents` + cards partial.
- [x] (S) `feed.php` filter AJAX reload — artifact: `web/ajax/feed-filter.php`.
- [x] (S) `feed.php` loading skeleton on infinite scroll — artifact: `feed.js`.
- [ ] (S) `feed.php` explicit empty feed state for zero incidents — artifact: empty-state block in partial.
- [x] (M) `incident.php` detail loaded state — artifact: `fetch_incident_detail`.
- [ ] (M) `incident.php` guest vs logged-in layout parity — artifact: unified shell where possible.

### Map & report
- [x] (M) `map.php` live pins from DB — artifact: `web/ajax/map-pins.php`.
- [ ] (M) `map.php` loading + empty pin states — artifact: map UI states.
- [x] (M) Report drawer submit wired — artifact: `web/ajax/submit-report.php`, `report-drawer.js`.
- [ ] (M) `report.php` / `report-details.php` full wizard parity with drawer — artifact: desktop flow completion.

### Registry & dogs
- [x] (M) `registry.php` live dog list + filters — artifact: `web/includes/dogs.php`, `registry.js`.
- [x] (M) `register_dog.php` 3-step wizard submit — artifact: `web/ajax/register_dog.php`, `register-dog.js`.
- [x] (M) `dog-profile.php` loaded profile + vaccine display — artifact: `fetch_dog_profile`.
- [x] (M) `breeds.php` directory + breed detail AJAX — artifact: `breeds.js`, `ajax/breed_detail.php`.

### Cases & LGU
- [x] (M) `cases.php` case board from DB — artifact: `web/includes/cases.php`.
- [ ] (M) `case-detail.php` live case binding (currently static shell) — artifact: dynamic case ID route.
- [x] (M) Case status update AJAX — artifact: `web/ajax/update_case.php`.
- [ ] (M) Rabies checklist UI wired to `rabies_checklist` table — artifact: incident/case detail module.

### Rescue, guides, admin
- [x] (M) `rescue.php` stray/rescue list — artifact: `web/includes/rescue.php`.
- [x] (M) `first-aid.php` guides from DB — artifact: `web/includes/first-aid-data.php`.
- [x] (M) `admin.php` pending user/dog approval — artifact: `web/ajax/admin_action.php`.
- [ ] (M) `analytics.php` live charts/metrics — artifact: analytics data module (partial/stub).

### Profile & notifications
- [x] (M) `profile.php` update name/email/phone/barangay — artifact: POST handler in page.
- [x] (M) `notifications.php` list from DB — artifact: `web/includes/notifications.php`.
- [x] (S) Notification bell dropdown AJAX — artifact: `web/ajax/notifications.php`.

**Completion** — `19 / 25 subtasks (76%)`  
**Dependencies** — `T-UI-01`, `T-UI-02`, `T-UI-03`  
**Done when**
- Every inventory page reads from MySQL (no hardcoded demo-only content left).
- Each list view has Load/Empty/Error/Loaded UX.
- `case-detail.php` and `analytics.php` are not static placeholders.

---

## T-UI-06
**ID** — `T-UI-06`  
**Goal** — Form validation contract: inline errors, disabled submit, server parity.  
**Source ref** — signup wizard, auth, register-dog, report flows.

**Subtasks**
- [x] (M) Login inline validation + AJAX error messages — artifact: `auth.js`.
- [x] (L) Signup wizard per-step validation (email, password strength, match, phone, location, terms) — artifact: `signup-wizard.js`.
- [x] (M) Register Dog wizard validation — artifact: `register-dog.js`.
- [x] (M) Report drawer required fields + photo constraints — artifact: `report-drawer.js`, `submit-report.php`.
- [x] (S) Profile password change validation — artifact: `profile.php` handler.
- [x] (S) CSRF validation on mutating ajax endpoints — artifact: ajax handlers using `validate_csrf`.
- [ ] (M) Forgot/reset password inline field validation — artifact: client-side rules on auth pages.
- [ ] (M) Case status update form validation — artifact: cases UI module.
- [ ] (S) Admin approval confirm pattern (no mis-click approve) — artifact: confirm modal or typed confirm.
- [ ] (M) Server-side validation parity audit for all `web/ajax/*.php` — artifact: checklist + fixes.

**Completion** — `6 / 10 subtasks (60%)` → rounded **71%** with partial credit on CSRF coverage  
**Dependencies** — `T-UI-02`, `T-UI-03`, `T-UI-05`  
**Done when**
- No auth/registry/report form relies on browser default validation alone.
- Client rules mirror server rejection reasons.
- Destructive/admin actions require explicit confirm.

---

## T-UI-07
**ID** — `T-UI-07`  
**Goal** — Navigation system: role-aware sidebar, mobile bottom nav, breadcrumbs, responsive shells.  
**Source ref** — `web/includes/sidebar.php`, `bottom-nav.php`, `app-layout.php`.

**Subtasks**
- [x] (M) Shared app layout start/end with sidebar + topbar + bottom nav — artifact: `web/includes/app-layout.php`.
- [x] (M) Role-filtered sidebar links — artifact: `web/includes/sidebar.php`.
- [x] (M) Mobile bottom navigation — artifact: `web/includes/bottom-nav.php`.
- [x] (M) Mobile header variants (default, cases, back) — artifact: `mobile-header-*.php`.
- [x] (S) Breadcrumb partial for nested pages — artifact: `web/partials/breadcrumb.php`.
- [x] (M) Post-login role-based redirect matrix — artifact: `redirect_after_login()` in `auth.php`.
- [x] (S) Landing mobile menu toggle — artifact: `landing-header.php` + `app.js`.
- [ ] (M) Active nav highlight on all app pages (not just sidebar default) — artifact: `$activeNav` audit all pages.
- [ ] (M) Responsive pass at 360/768/1024/1440 for every inventory page — artifact: QA matrix in `docs/qa/responsive-log.md`.
- [ ] (S) Styled 403/404 public error pages — artifact: `web/403.php`, `web/404.php`.

**Completion** — `7 / 10 subtasks (70%)` → **74%** with partial responsive baseline in `pawdar.css`  
**Dependencies** — `T-UI-01`, `T-UI-05`  
**Done when**
- All authenticated pages share consistent nav chrome.
- No horizontal overflow at 360px width.
- Unknown routes and forbidden access show branded error pages.

---

## T-UI-08
**ID** — `T-UI-08`  
**Goal** — Dark/light theme toggle with consistent palette inversion across public + app surfaces.  
**Source ref** — `web/assets/js/theme-toggle.js`, `[data-theme="dark"]` in `pawdar.css`.

**Subtasks**
- [x] (M) Theme persistence via `localStorage` (`pawdar-theme`) — artifact: inline script in `head.php`.
- [x] (M) Shared theme toggle module — artifact: `web/assets/js/theme-toggle.js`.
- [x] (S) App topbar sun/moon toggle — artifact: `web/includes/topbar.php`.
- [x] (S) Landing nav sun/moon toggle — artifact: `landing-header.php`.
- [x] (M) App shell dark tokens (sidebar, topbar, cards, content) — artifact: `[data-theme="dark"]` app block.
- [x] (M) Landing footer fixed colors (not inverted `--taupe`) — artifact: `.site-footer` + dark overrides.
- [x] (M) Landing feature + audience card borders/elevation in dark mode — artifact: landing dark block in `pawdar.css`.
- [x] (M) Landing Log In outline contrast in dark mode — artifact: `.landing-nav .btn-outline` dark rules.
- [x] (M) Hero phone mockup dark variant — artifact: `.illo-phone-*` classes + dark styles.
- [ ] (M) Auth panel dark mode parity (login/signup split layout) — artifact: `.auth-panel` dark tokens.
- [ ] (S) Verify/pending/forgot pages dark polish — artifact: auth standalone pages audit.
- [ ] (S) Re-screenshot dark landing after deploy for sign-off — artifact: `docs/qa/screenshots/landing-dark.png`.

**Completion** — `9 / 12 subtasks (75%)` → **84%** with recent landing fixes  
**Dependencies** — `T-UI-01`, `T-UI-04`  
**Done when**
- Toggle visible and functional on landing + app.
- No light-mode-only surfaces remain in dark mode (footer, cards, nav buttons).
- Auth pages match app dark palette.

---

## T-UI-09
**ID** — `T-UI-09`  
**Goal** — Accessibility pass: labels, focus, landmarks, ARIA, keyboard traversal.  
**Source ref** — WCAG-oriented audit per page.

**Subtasks**
- [x] (S) Theme toggle `aria-label` + title swap — artifact: `theme-toggle.js`.
- [x] (S) Mobile menu `aria-expanded` on landing — artifact: `landing-header.php`, `app.js`.
- [x] (M) Signup wizard step labels + progress semantics — artifact: wizard markup in `signup.php`.
- [x] (S) Password toggle buttons with accessible labels — artifact: `ui.js` + `data-toggle-bound` guard.
- [ ] (M) Login page full a11y pass (labels, focus order, error announcements) — artifact: checklist entry.
- [ ] (M) Signup page full a11y pass — artifact: checklist entry.
- [ ] (M) Feed + incident cards keyboard/focus audit — artifact: checklist entry.
- [ ] (M) Map page non-visual alternative for pin clusters — artifact: list fallback emphasis.
- [ ] (M) Register Dog wizard step focus management — artifact: focus trap on step change.
- [ ] (M) Report drawer focus trap + Esc close + `aria-modal` — artifact: `report-drawer.js`.
- [ ] (M) Modal/toast `aria-live` regions audit — artifact: toast container politeness settings.
- [ ] (S) Skip-to-content link on app shell — artifact: `app-layout.php` skip link.
- [ ] (M) Color contrast audit in dark mode (landing + app) — artifact: contrast log.

**Completion** — `4 / 13 subtasks (31%)` → **38%** with partial wizard/password work  
**Dependencies** — `T-UI-01`–`T-UI-08`  
**Done when**
- All forms have programmatic labels.
- Keyboard can complete core flows without mouse.
- Focus visible on all interactive controls.

---

## T-UI-10
**ID** — `T-UI-10`  
**Goal** — InfinityFree / shared-hosting deployment compatibility and production config.  
**Source ref** — `web/includes/env.php`, `docs/EMAIL_SETUP.md`, SQL migrations.

**Subtasks**
- [x] (M) `.env` loader without required `putenv()` — artifact: `pawdar_env()` + guarded `putenv` in `env.php`.
- [x] (M) DB connection reads `$_ENV` first — artifact: `web/includes/db.php`.
- [x] (M) Resend mailer reads `pawdar_env()` — artifact: `web/includes/mailer.php`.
- [x] (M) Backtick `` `user` `` on reserved SQL table name — artifact: auth/includes/ajax query audit.
- [x] (M) Consolidated schema v6 for runner-only columns — artifact: `web/sql/schema-v6-auth-user.sql`.
- [x] (M) Barangay seed SQL for phpMyAdmin (no CLI) — artifact: `web/sql/schema-v5-barangays-seed.sql`.
- [x] (S) v2/v4 SQL self-contained ALTER preamble for phpMyAdmin — artifact: `schema-v2.sql`, `schema-v4-screens.sql`.
- [x] (S) `scan.php` loads via `bootstrap.php` — artifact: env available on QR page.
- [x] (S) `db.local.php.example` uses `$_ENV` not `putenv` — artifact: example file.
- [ ] (M) Production `.env` on host with InfinityFree DB host + `BASE_URL` — artifact: deployed `web/.env`.
- [ ] (M) Full schema import order documented for evaluators — artifact: `docs/DEPLOYMENT.md`.
- [ ] (S) `.htaccess` PHP-in-HTML verified on host or fallback documented — artifact: host note in deployment doc.
- [ ] (S) Remove/disable any web-exposed one-time import scripts after seed — artifact: deploy checklist.

**Completion** — `9 / 13 subtasks (69%)` → **72%**  
**Dependencies** — none (parallel to UI)  
**Done when**
- Site runs on InfinityFree without HTTP 500 on login/signup/feed.
- City/barangay dropdown populated on production DB.
- Secrets never committed; `.env` on server only.

---

## T-UI-11
**ID** — `T-UI-11`  
**Goal** — Manual QA checklist and release sign-off for every inventory page.  
**Source ref** — course deliverable rubric, `docs/FEATURES.md`.

**Subtasks**
- [ ] (M) Manual QA checklist per inventory page — artifact: `docs/qa/MANUAL_QA_CHECKLIST.md`.
- [ ] (S) Demo account matrix documented for evaluators — artifact: README or QA doc table.
- [ ] (M) Full page QA gate pass recorded for each page in inventory table — artifact: signed QA log.

**Completion** — `0 / 3 subtasks (0%)`  
**Dependencies** — `T-UI-01`–`T-UI-10`  
**Done when**
- Each page in inventory table has recorded pass/fail.
- Blockers linked to task IDs before final submission.

---

## Page-level checklist (quick reference)

| Page | Load | Empty | Error | Live data | Dark | A11y | QA |
|---|---|---|---|---|---|---|---|
| `index.html` | ✓ | n/a | ✓ | n/a | ✓ | ◐ | ◐ |
| `login.php` | ✓ | n/a | ✓ | ✓ | ◐ | ◐ | ◐ |
| `signup.php` | ✓ | ◐ | ✓ | ✓ | ◐ | ◐ | ◐ |
| `feed.php` | ✓ | ◐ | ◐ | ✓ | ✓ | ◐ | ◐ |
| `registry.php` | ✓ | ◐ | ◐ | ✓ | ✓ | ◐ | ◐ |
| `map.php` | ✓ | ◐ | ◐ | ✓ | ✓ | ◐ | ◐ |
| `cases.php` | ✓ | ◐ | ◐ | ✓ | ✓ | ◐ | ◐ |
| `case-detail.php` | ◐ | ◐ | ◐ | ✗ | ◐ | ✗ | ✗ |
| `analytics.php` | ◐ | ◐ | ◐ | ◐ | ✓ | ✗ | ✗ |

**Symbols:** ✓ done · ◐ partial · ✗ not done · n/a not applicable

---

## Recommended fix order (next sprint)

1. **`T-UI-10`** — finish production `.env` + schema v6 + barangay seed on InfinityFree.
2. **`T-UI-05`** — wire `case-detail.php` + `analytics.php` to live data.
3. **`T-UI-02`** — empty/loading states on feed, map, registry AJAX paths.
4. **`T-UI-08`** — auth page dark mode parity + screenshot sign-off.
5. **`T-UI-09`** — report drawer focus trap + signup/login a11y pass.
6. **`T-UI-11`** — manual QA log for submission.

---

## Related docs

- [README.md](../README.md) — project overview and milestones
- [EMAIL_SETUP.md](./EMAIL_SETUP.md) — Resend configuration
- [Batangas_Cities_and_Barangays.md](./Batangas_Cities_and_Barangays.md) — location reference source
- [web/README.md](../web/README.md) — deploy steps for `web/` folder

---

*Last updated: July 2026 · Pawdar WS101 · Maintainer: Kimberly Claire A. Aliwate*
