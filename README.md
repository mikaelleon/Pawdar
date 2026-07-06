# Pawdar

Community dog registry and incident reporting for barangays, owners, veterinarians, LGU officials, and rescue groups in Batangas Province.

## What Project Does

Pawdar helps six role groups work in one place:

- **Dog Owners** register dogs, manage vaccination records, and receive QR registry tags.
- **Community Reporters** report incidents in their barangay without owning a dog.
- **Veterinarians** verify vaccination records and co-sign health data.
- **LGU Officials** manage cases, advisories, and barangay-level oversight.
- **Rescue Organizations** track stray rescues and adoption-related workflows.
- **Admins** approve pending accounts and govern platform access.

Main goal: give barangays a single trusted system for dog registration, incident reporting, and LGU response — with clean location data and role-appropriate access.

---

## Group Members

- Kimberly Claire A. Aliwate — UI/UX & Product Design
- WS101 group — update roster before final submission

---

## Who This Is For

- **Dog owners** who need official registration and vaccination tracking.
- **Residents and community reporters** who want to flag animal incidents quickly.
- **Veterinarians** who verify health records tied to registered dogs.
- **LGU officials** who need case management, maps, and barangay-filtered visibility.
- **Rescue organizations** coordinating stray intake and adoption support.
- **Admins** who approve Vet/LGU/Rescue sign-ups and maintain governance.
- **Non-technical stakeholders** (barangay leaders, evaluators, civic partners).
- **Technical team** building and maintaining the PHP/MySQL web platform.

## Current Progress Snapshot

Current state: core auth, registry, feed, map, cases, and sign-up wizard are implemented. Email verification via Resend is wired; production domain sending still requires Resend domain verification.

### Implementation Milestones

| Milestone | Focus | Status |
|---|---|---|
| M1 | Auth pages (login, signup, forgot/reset password) | Done |
| M2 | Role-based navigation, guards, and pending approval flow | Done |
| M3 | Dog registry, breed data, QR tags, Register Dog wizard | Done |
| M4 | Incident feed, map pins, report drawer, case management | Done |
| M5 | Sign-up wizard (Account → Role & location → Verify) + Batangas city/barangay data | Done |
| M6 | Resend email verification + password reset | Done (API integration) |
| M7 | Production hardening, manual QA pass, compliance depth | In progress |

### Completed Highlights

- Multi-step sign-up with structured name fields, role cards, city→barangay cascade, and RA 10173 terms copy.
- Email verification flow: `verify.php`, `email_verified.php`, resend with cooldown.
- Resend API integration for verification and password reset (configured via `.env`).
- Role-aware login routing (feed, registry, cases, rescue, admin).
- Dog registry with filters, breed autocomplete, vaccination records, and QR generation.
- Incident feed and map with barangay-scoped pins and severity badges.
- LGU case board, first-aid guides, breed directory, and admin approval queue.
- Normalized **city/barangay** reference tables (5 Batangas cities, 287 barangays).

### In Progress

- Manual QA checklist and release sign-off per page.
- Role-specific sign-up fields (license number, LGU office) — planned.
- SMS OTP for approval-required roles — optional future enhancement.

## Feature Overview

- **Role-based login and registration** with approval gates for Vet, LGU, and Rescue Org.
- **3-step sign-up wizard** — Account, Role & location, email verification.
- **Dog registry** with search, filters, breed data, photos, and QR tags.
- **Register Dog wizard** — Basic info → Health records → Review.
- **Incident feed and map** with type filters and barangay scoping.
- **Report incident drawer** for mobile-friendly submission.
- **LGU case management** with status workflow and rabies monitoring flag.
- **Vaccination records** with vet co-sign and status tracking.
- **First-aid guides** and **breed directory** for community reference.
- **Admin panel** for pending account approval.
- **Email verification and password reset** via Resend.
- **City → Barangay** location selectors sourced from reference tables.

## Feature Overview (Non-Technical)

### Dog Owner Functions

- Register dogs with breed, photo, and health details.
- Download QR registry tags.
- View and update dog profiles and vaccination history.

### Community Reporter Functions

- Report incidents in their barangay.
- Follow feed updates and map activity.

### Veterinarian Functions

- Access registry for verification workflows.
- Co-sign vaccination records.

### LGU Official Functions

- Manage cases linked to incidents.
- View barangay-scoped feed and map data.
- Monitor operational activity in assigned area.

### Rescue Organization Functions

- Access rescue-oriented views and registry context.

### Admin Functions

- Review and approve pending Vet/LGU/Rescue accounts.
- Govern platform access and user status.

---

## Batangas pilot scope

This deployment targets **Batangas Province** (5 cities):

- **Batangas City**, **Calaca City**, **Lipa City**, **Santo Tomas City**, **Tanauan City**
- Barangay lists imported from `docs/Batangas_Cities_and_Barangays.md` into normalized `city` / `barangay` tables.
- Sign-up uses cascading **City → Barangay** selectors (not free-text barangay entry).

See `docs/Batangas_Cities_and_Barangays.md` for the canonical barangay reference.

---

## Technology Stack

### Application Stack

- Frontend: HTML5, CSS3, vanilla JavaScript, [Lucide](https://lucide.dev) icons.
- Backend: PHP 7.4+ with PDO (MySQL/MariaDB).
- Database: MySQL (XAMPP local; InfinityFree or similar for hosting).
- Email: [Resend](https://resend.com) REST API (cURL).

### Key Runtime Requirements

- PHP with **PDO MySQL** and **cURL** extensions.
- MySQL/MariaDB.
- Outbound HTTPS to `api.resend.com` (verification and password reset).

### Dev and QA Tooling

- `php sql/setup.php` — schema and seed data.
- `php sql/import-barangays.php` — city/barangay reference import.
- `php sql/import-breeds.php` — load breeds from `schema-v3-breeds-seed.sql`
- `php sql/generate-breeds-seed.php` — rebuild seed SQL from `archive/dogs_cleaned.csv`

## Language and Accessibility

- Current locale: **English**.
- Sign-up and auth flows include `aria-live` step announcements, radiogroup semantics for role cards, and labeled form fields.
- Ongoing: deeper keyboard and screen-reader consistency across all app screens.

## System Boundaries (What Works Now vs Later)

### Can Do

- Full sign-up, email verification (with Resend key), login, and role-based routing.
- Dog registration, registry browse/filter, QR tags.
- Incident reporting, feed, map, and LGU case tracking.
- Admin approval for pending roles.
- Password reset via email link.
- City/barangay data for all five Batangas cities in scope.

### Works Now

- Core PHP/MySQL backend with session auth.
- Resend-backed transactional email when `.env` is configured.
- Multi-step wizards for sign-up and dog registration.
- Barangay-scoped incident and user data.

### Cannot Do Yet / Not Fully Complete

- Resend production sending to arbitrary recipients until a domain is verified.
- SMS OTP for approval-required roles (spec optional; not built).
- Role-specific sign-up fields (vet license, LGU office assignment).
- Exact map pin-drop at account creation (by design — scoped to Register Dog / Report Incident).
- Full production compliance audit (RA 10173 consent flows beyond terms checkbox).

## Quick Start (Local)

1. Copy `.env.example` → `.env` at the **repo root** and set database + Resend keys.
2. Copy `web/includes/db.local.php.example` → `web/includes/db.local.php` if you prefer PHP DB overrides.
3. Run database setup:
   ```powershell
   c:\xampp\php\php.exe web\sql\setup.php
   c:\xampp\php\php.exe web\sql\import-barangays.php
   c:\xampp\php\php.exe web\sql\import-breeds.php
   ```
   **phpMyAdmin only:** import `schema-v3-breeds-seed.sql` and `schema-v5-barangays-seed.sql` after their schema files (see `web/sql/BREEDS_CSV_HEADERS.md`).
4. Open `http://localhost/WS101_Aliwate/WS101-Pawdar/web/`
6. Demo login: `maria.santos@email.com` / `password`

## Project Structure (High Level)

| Path | Purpose |
|---|---|
| `web/` | Deployable PHP site (upload to hosting `htdocs`) |
| `web/includes/` | Bootstrap, auth, DB, mailer, helpers |
| `web/assets/` | CSS, JavaScript, images |
| `web/sql/` | Schema, migrations, import scripts |
| `web/auth/` | Login, signup, verify-email handlers |
| `docs/` | Barangay reference, email setup, feature audit |
| `Pawdar Design System/` | Original design canvas reference |

## Important Documents

- Deployable site guide: [`web/README.md`](web/README.md)
- Email / Resend setup: [`docs/EMAIL_SETUP.md`](docs/EMAIL_SETUP.md)
- Batangas barangay reference: [`docs/Batangas_Cities_and_Barangays.md`](docs/Batangas_Cities_and_Barangays.md)
- Breed CSV mapping: [`web/sql/BREEDS_CSV_HEADERS.md`](web/sql/BREEDS_CSV_HEADERS.md)
- Environment template: [`.env.example`](.env.example)

## Roadmap Priorities

- Verify Resend domain for production email to all sign-up addresses.
- Complete manual QA checklist for all inventory pages.
- Add role-specific sign-up fields for Vet/LGU/Rescue.
- Privacy/consent depth aligned with RA 10173 for production.
- Deployment hardening for InfinityFree or institutional hosting.

## Deployment (InfinityFree / PHP host)

- **Upload target:** entire `web/` folder contents to hosting `htdocs`.
- **PHP:** 7.4+ with PDO MySQL and cURL.
- **Database:** create MySQL database; run migrations via host shell or phpMyAdmin (`schema.sql` through `schema-v6-auth-user.sql`, plus `schema-v3-breeds-seed.sql` and `schema-v5-barangays-seed.sql` — see `web/sql/BREEDS_CSV_HEADERS.md`).
- **Environment:** set `PAWDAR_DB_*` and `RESEND_*` via host env panel, or place `.env` in `web/` if supported. Otherwise use `includes/db.local.php` for DB and host env for Resend.
- **Required env vars:** `PAWDAR_DB_HOST`, `PAWDAR_DB_NAME`, `PAWDAR_DB_USER`, `PAWDAR_DB_PASS`, `RESEND_API_KEY`, `RESEND_FROM`.

## Notes

- Platform aligns with community safety and responsible pet ownership goals.
- Privacy copy references **RA 10173** (Philippine Data Privacy Act) at sign-up.
- Email verification required before accessing protected app pages.

### Documentation Summary for Non-Technical Readers

- Pawdar already delivers working registration, incident reporting, and LGU case tools.
- Sign-up collects structured identity and location data for reliable barangay routing.
- Email confirmation protects accounts before users enter the platform.

## Future / Missing Improvements

**Backend & data**

- Server-side validation parity on all AJAX endpoints.
- Audit trail for LGU/admin actions.

**Features**

- SMS OTP optional path for approval-required roles.
- Role-specific onboarding fields (vet license, LGU department).
- Push/email notifications when cases or approvals change.

**Security, compliance & ops**

- HTTPS enforcement, CSRF on all mutating forms, rate limiting on auth endpoints.
- Expanded RA 10173 consent and data-retention documentation.
- Monitoring, backups, and deployment runbooks.

**UX & content**

- Tagalog locale support.
- Full WCAG accessibility pass on all screens.

**Testing & quality**

- Manual QA checklist per inventory page.
- Cross-browser QA and load testing for barangay-scale usage.
