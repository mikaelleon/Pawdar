-- Pawdar schema v10: dog profile demo data polish.
-- Run after schema-v9-breeds-directory.sql. Safe to re-run.

UPDATE dog
SET Gender = 'Male',
    Size = COALESCE(Size, 'Medium'),
    DogType = COALESCE(DogType, 'Owned')
WHERE DogName IN ('Bruno', 'Bantay')
  AND (Gender IS NULL OR Gender = '' OR Gender = 'Unknown');

UPDATE dog
SET RegistryID = 'PWD-2024-00832'
WHERE DogName = 'Bruno'
  AND (RegistryID IS NULL OR RegistryID = '');

UPDATE dog
SET coat_color = 'Brown and white',
    weight_kg = 18.50,
    distinguishing_marks = 'White patch on chest; docked tail tip.',
    temperament_notes = 'Alert and friendly with family; cautious around strangers and loud traffic.'
WHERE DogName = 'Bruno'
  AND (coat_color IS NULL OR coat_color = '');

UPDATE dog
SET coat_color = 'Tan',
    weight_kg = 16.00,
    distinguishing_marks = 'Scar on left ear.',
    temperament_notes = 'Calm indoors; barks at unfamiliar visitors.'
WHERE DogName = 'Bantay'
  AND (coat_color IS NULL OR coat_color = '');

INSERT INTO vaccinerecord (dog_id, VaccineName, DateGiven, VetName, vax_status)
SELECT d.dog_id, 'Anti-Rabies · Annual', DATE_SUB(CURDATE(), INTERVAL 120 DAY), 'Dr. Ana Lim', 'Verified'
FROM dog d
INNER JOIN `user` u ON u.UserID = d.UserID AND u.Email = 'rosa.castillo@email.com'
WHERE d.DogName = 'Bruno'
AND NOT EXISTS (SELECT 1 FROM vaccinerecord v WHERE v.dog_id = d.dog_id);
