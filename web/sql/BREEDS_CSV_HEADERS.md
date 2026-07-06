# Kaggle CSV column headers (archive/)

Source datasets live in **`archive/`** at the repo root:

| File | Format | Notes |
|---|---|---|
| **`dogs_cleaned.csv`** | Comma | **Canonical source** — 391 breeds, headers normalized |
| `dogs.csv` | Comma | Raw export; first column has no header (breed name) |
| `dogs2.csv` | Semicolon | Duplicate of `dogs.csv` |

## dogs_cleaned.csv columns

`Breed Name`, `Detailed Description Link`, `Dog Size`, `Dog Breed Group`, `Height`, `Avg. Height, cm`, `Weight`, `Avg. Weight, kg`, `Life Span`, `Avg. Life Span, years`, `Adaptability`, `Adapts Well To Apartment Living`, `Good For Novice Owners`, `Sensitivity Level`, `Tolerates Being Alone`, `Tolerates Cold Weather`, `Tolerates Hot Weather`, `All Around Friendliness`, `Affectionate With Family`, `Kid-Friendly`, `Dog Friendly`, `Friendly Toward Strangers`, `Health And Grooming Needs`, `Amount Of Shedding`, `Drooling Potential`, `Easy To Groom`, `General Health`, `Potential For Weight Gain`, `Size`, `Trainability`, `Easy To Train`, `Intelligence`, `Potential For Mouthiness`, `Prey Drive`, `Tendency To Bark Or Howl`, `Wanderlust Potential`, `Physical Needs`, `Energy Level`, `Intensity`, `Exercise Needs`, `Potential For Playfulness`

## Mapping to `breeds` table

| breeds column | CSV source |
|---|---|
| breed_name | Breed Name |
| size_category | Dog Size → Small / Medium / Large |
| weight_range | Avg. Weight, kg or Weight |
| lifespan | Life Span (max 20 chars) |
| temperament_notes | Dog Breed Group + suffix |
| common_health_risks | derived from General Health score |
| loyalty_score | Affectionate With Family (1–5) |
| energy_score | Energy Level (1–5) |
| friendliness_score | avg(Kid-Friendly, Dog Friendly, Friendly Toward Strangers) |

**Aspin (Asong Pinoy)** is inserted manually — not in Kaggle data.

## SQL seed (phpMyAdmin / InfinityFree)

No CLI on shared hosting? Use the pre-built seed file instead of CSV:

1. Run **`schema-v3-breeds.sql`** (creates `breeds` table).
2. Import **`schema-v3-breeds-seed.sql`** — 392 breeds (391 Kaggle + Aspin).

Regenerate after editing `archive/dogs_cleaned.csv`:

```powershell
c:\xampp\php\php.exe web\sql\generate-breeds-seed.php
```

Load into MySQL locally:

```powershell
c:\xampp\php\php.exe web\sql\import-breeds.php
```

## Full phpMyAdmin import order

1. `schema.sql`
2. `schema-v2.sql`
3. `schema-v3-breeds.sql`
4. **`schema-v3-breeds-seed.sql`**
5. `schema-v4-screens.sql`
6. `schema-v5-locations.sql`
7. `schema-v5-barangays-seed.sql`
8. `schema-v6-auth-user.sql`

The seed file ends with `UPDATE dog … SET breed_id` so demo dogs link to breeds after import.
