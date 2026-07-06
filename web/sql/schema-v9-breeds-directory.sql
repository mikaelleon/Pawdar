-- Pawdar schema v9: Breed Directory redesign columns.
-- Run after schema-v8-ui-fixes.sql. Safe to re-run.

ALTER TABLE breeds ADD COLUMN IF NOT EXISTS slug VARCHAR(120) NULL UNIQUE AFTER breed_name;
ALTER TABLE breeds ADD COLUMN IF NOT EXISTS breed_group VARCHAR(120) NULL AFTER lifespan;
ALTER TABLE breeds ADD COLUMN IF NOT EXISTS known_for TEXT NULL AFTER breed_group;
ALTER TABLE breeds ADD COLUMN IF NOT EXISTS grooming_notes VARCHAR(255) NULL AFTER known_for;
ALTER TABLE breeds ADD COLUMN IF NOT EXISTS adoption_notes TEXT NULL AFTER grooming_notes;
ALTER TABLE breeds ADD COLUMN IF NOT EXISTS legal_global TEXT NULL AFTER adoption_notes;
ALTER TABLE breeds ADD COLUMN IF NOT EXISTS legal_philippines TEXT NULL AFTER legal_global;
ALTER TABLE breeds ADD COLUMN IF NOT EXISTS is_local_breed TINYINT NOT NULL DEFAULT 0 AFTER legal_philippines;
ALTER TABLE breeds ADD COLUMN IF NOT EXISTS gallery_urls TEXT NULL AFTER image_url;

UPDATE breeds
SET breed_group = TRIM(SUBSTRING_INDEX(temperament_notes, '—', 1))
WHERE (breed_group IS NULL OR breed_group = '')
  AND temperament_notes LIKE '%—%';

UPDATE breeds
SET is_local_breed = 1
WHERE breed_name IN ('Aspin (Asong Pinoy)', 'Aspin', 'Asong Pinoy', 'Mixed Breed');

UPDATE breeds SET known_for = 'Aspin (Asong Pinoy) dogs are the resilient native mixed-breed companions found throughout the Philippines. Known for adaptability, loyalty to their household, and street-smart alertness, they are deeply woven into Batangas community life — from barangay yards to rescue and adoption programs.'
WHERE breed_name = 'Aspin (Asong Pinoy)' AND (known_for IS NULL OR known_for = '');

UPDATE breeds SET adoption_notes = 'Ideal for Filipino households that want a hardy, medium-sized companion. Aspin thrive with daily walks, basic grooming, and consistent vaccination under RA 9482. They suit owners who appreciate an alert watchdog temperament without extreme exercise demands.'
WHERE breed_name = 'Aspin (Asong Pinoy)' AND (adoption_notes IS NULL OR adoption_notes = '');
