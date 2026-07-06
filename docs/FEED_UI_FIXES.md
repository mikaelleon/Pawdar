# Pawdar — Feed Screen UI Fixes (Addendum)

**Status:** Implemented (Jul 2026)  
**Related:** [UI_FEEDBACK_SPEC.md](./UI_FEEDBACK_SPEC.md), [CASE_STATUS_TAXONOMY.md](./CASE_STATUS_TAXONOMY.md)

---

## Critical

| # | Issue | Status | Implementation |
|---|-------|--------|----------------|
| 1 | Map preview decorative / not wired | Done | Real lat/lng pins projected to preview; color legend + stat grid match pin colors |
| 2 | Raw GPS in feed titles | Done | `includes/geocoding.php` reverse-geocodes via Nominatim; display uses readable address; GPS in More details |

## Moderate

| # | Issue | Status | Implementation |
|---|-------|--------|----------------|
| 3 | Referred status undocumented | Done | `docs/CASE_STATUS_TAXONOMY.md`; already in Case Management dropdown |
| 4 | Corroborate context | Done | Tooltips explain escalation; corroborated state styled |
| 5 | First aid step count | Done | "Step 1 of N" on widget |
| 6 | Dog fact widget priority | Done | Replaced with collapsible **Safety tip**; moved below map/first-aid |

## Minor / Polish

| # | Issue | Status | Implementation |
|---|-------|--------|----------------|
| 7 | Disturbance icon consistency | Done | `footprints` via `incident_type_map()` on cards + filter chips |
| 8 | Relative timestamps only | Done | `title` attribute + absolute time in More details |
| 9 | Stat grid missing Disturbance | Done | 5th bucket `disturbance` in counts + legend |

---

## Key files

| Area | Files |
|------|-------|
| Geocoding | `web/includes/geocoding.php`, `web/cache/geocode/` |
| Map preview | `web/partials/widget_map.php`, `fetch_map_pins()` in `incidents.php` |
| Report submit | `web/ajax/submit-report.php`, `report-drawer.js` (lat/lng hidden fields) |
| Feed cards | `web/partials/incident-cards.php` |
| Widgets | `widget_firstaid.php`, `widget_funfact.php` (safety tips), `feed.php` order |

---

## Notes

- Reverse geocode needs outbound HTTPS to `nominatim.openstreetmap.org` on first view/submit per coordinate (cached under `web/cache/geocode/`).
- Existing coord-only locations geocode at **display time** — no DB migration required.
