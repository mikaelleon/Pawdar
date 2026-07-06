# Pawdar — Case Status Taxonomy

Single source of truth for LGU case workflow statuses shown on Feed, Case Management, and Incident Detail.

## Status values

| Status | Feed badge | Meaning |
|--------|------------|---------|
| **Received** | Received (neutral) | Incident filed; case created; awaiting LGU triage |
| **Under Investigation** | Investigating (amber) | LGU actively reviewing or dispatching response |
| **Action Taken** | Investigating (amber) | On-site or remedial action completed; monitoring may continue |
| **Resolved** | Resolved (green) | Case closed successfully |
| **Referred** | Referred (blue) | Escalated to another agency (e.g. city vet, hospital, neighboring barangay) |

## Typical flow

```
Received → Under Investigation → Action Taken → Resolved
                    ↘ Referred → (external handoff)
```

- **Referred** is not a failure state — it means the barangay routed the case outside its direct scope.
- **Rabies watch** (14-day monitoring) applies to Animal Bite cases and runs in parallel until Resolved or Referred.

## Where statuses appear

- Feed incident cards — badge + LGU status dropdown
- Case Management — table status column + bulk update
- Incident detail — timeline pills + status history with optional remarks

## Related

- Remarks on status change → `case_history.notes` (see `UI_FEEDBACK_SPEC.md` §2.7)
- Incident *types* (Animal Bite, Disturbance, etc.) are separate from case *status* — see `incident_type_map()` in `web/includes/helpers.php`
