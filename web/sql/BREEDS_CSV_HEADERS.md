# Kaggle CSV column headers (archive/)

## dogs_cleaned.csv — **use this file** (391 breeds, comma-delimited)

`Breed Name`, `Detailed Description Link`, `Dog Size`, `Dog Breed Group`, `Height`, `Avg. Height, cm`, `Weight`, `Avg. Weight, kg`, `Life Span`, `Avg. Life Span, years`, `Adaptability`, `Adapts Well To Apartment Living`, `Good For Novice Owners`, `Sensitivity Level`, `Tolerates Being Alone`, `Tolerates Cold Weather`, `Tolerates Hot Weather`, `All Around Friendliness`, `Affectionate With Family`, `Kid-Friendly`, `Dog Friendly`, `Friendly Toward Strangers`, `Health And Grooming Needs`, `Amount Of Shedding`, `Drooling Potential`, `Easy To Groom`, `General Health`, `Potential For Weight Gain`, `Size`, `Trainability`, `Easy To Train`, `Intelligence`, `Potential For Mouthiness`, `Prey Drive`, `Tendency To Bark Or Howl`, `Wanderlust Potential`, `Physical Needs`, `Energy Level`, `Intensity`, `Exercise Needs`, `Potential For Playfulness`

## dogs.csv — raw export (comma-delimited)

First column has **no header** (breed name). Then: `Dog Breed Group`, `Height`, `Weight`, `Life Span`, rating columns, numeric `Size` (1–5), `Energy Level`, `Easy To Train`, `Intelligence`, etc., `Detailed Description Link`.

## dogs2.csv — semicolon-delimited duplicate

`Column1` (breed name), then same columns as `dogs.csv` separated by `;`.

## Mapping used for `breeds` table

| breeds column | CSV source |
|---|---|
| breed_name | Breed Name |
| size_category | Dog Size → Small/Medium/Large |
| weight_range | Avg. Weight, kg or Weight |
| lifespan | Life Span |
| temperament_notes | Dog Breed Group |
| common_health_risks | derived from General Health score |
| loyalty_score | Affectionate With Family (1–5) |
| energy_score | Energy Level (1–5) |
| friendliness_score | avg(Kid-Friendly, Dog Friendly, Friendly Toward Strangers) |

Aspin (Asong Pinoy) inserted manually — not in Kaggle data.
