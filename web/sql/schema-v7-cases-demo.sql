-- Pawdar v7: case assignment column + demo case/dog linkage fixes
-- Run AFTER schema-v4-screens.sql. Safe to re-run.

ALTER TABLE `case` ADD COLUMN IF NOT EXISTS assigned_to INT NULL AFTER RabiesMonitoring;

-- Second LGU official for assignment demo (optional barangay peer)
INSERT INTO `user` (Name, Email, Password, Role, Status, Barangay, Phone)
SELECT 'Engr. Ana Reyes', 'ana.reyes@barangay.gov.ph',
       '$2y$10$hejq7bVagXzje2wmf6HfIuseZbetNVXlz9xpc5TvrSt2njn1ZS83S',
       'LGU Official', 'active', 'San Roque', '09179990002'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `user` u WHERE u.Email = 'ana.reyes@barangay.gov.ph');

-- Second registered dog for incident linkage demo
INSERT INTO dog (UserID, DogName, Breed)
SELECT u.UserID, 'Bruno', 'Aspin'
FROM `user` u
WHERE u.Email = 'rosa.castillo@email.com'
AND NOT EXISTS (SELECT 1 FROM dog d WHERE d.UserID = u.UserID AND d.DogName = 'Bruno');

-- Link incidents to registered dogs where applicable
UPDATE incident i
INNER JOIN dog d ON d.DogName = 'Bantay'
INNER JOIN `user` u ON u.UserID = d.UserID AND u.Email = 'rosa.castillo@email.com'
SET i.dog_id = d.dog_id
WHERE i.Location = 'Barangay Hall area, Brgy. San Roque'
  AND i.dog_id IS NULL;

UPDATE incident i
INNER JOIN dog d ON d.DogName = 'Bruno'
INNER JOIN `user` u ON u.UserID = d.UserID AND u.Email = 'rosa.castillo@email.com'
SET i.dog_id = d.dog_id
WHERE i.IncidentType = 'Animal Bite'
  AND i.Location LIKE '%San Roque%'
  AND i.dog_id IS NULL;

UPDATE incident i
INNER JOIN dog d ON d.DogName = 'Bruno'
INNER JOIN `user` u ON u.UserID = d.UserID AND u.Email = 'rosa.castillo@email.com'
SET i.dog_id = d.dog_id
WHERE i.IncidentType = 'Aggressive Behavior'
  AND i.Location = 'Acacia Ave, Brgy. San Roque'
  AND i.dog_id IS NULL;

-- Ensure case rows exist for all San Roque demo incidents
INSERT INTO `case` (IncidentID, CaseStatus, RabiesMonitoring, assigned_to)
SELECT i.IncidentID, 'Received', 0, NULL
FROM incident i
WHERE i.Location LIKE '%San Roque%'
AND NOT EXISTS (SELECT 1 FROM `case` c WHERE c.IncidentID = i.IncidentID);

-- Demo statuses + assignments (by location / type)
UPDATE `case` c
INNER JOIN incident i ON i.IncidentID = c.IncidentID
INNER JOIN `user` lgu ON lgu.Email = 'luis.cruz@email.com'
SET c.CaseStatus = 'Under Investigation',
    c.RabiesMonitoring = 1,
    c.assigned_to = lgu.UserID
WHERE i.Location = 'Riverside Park, Brgy. San Roque';

UPDATE `case` c
INNER JOIN incident i ON i.IncidentID = c.IncidentID
INNER JOIN `user` lgu ON lgu.Email = 'ana.reyes@barangay.gov.ph'
SET c.CaseStatus = 'Received',
    c.assigned_to = lgu.UserID
WHERE i.Location = 'Market St., Brgy. San Roque';

UPDATE `case` c
INNER JOIN incident i ON i.IncidentID = c.IncidentID
INNER JOIN `user` lgu ON lgu.Email = 'luis.cruz@email.com'
SET c.CaseStatus = 'Resolved',
    c.assigned_to = lgu.UserID
WHERE i.Location = 'Acacia Ave, Brgy. San Roque';

UPDATE `case` c
INNER JOIN incident i ON i.IncidentID = c.IncidentID
INNER JOIN `user` lgu ON lgu.Email = 'luis.cruz@email.com'
SET c.CaseStatus = 'Resolved',
    c.assigned_to = lgu.UserID
WHERE i.Location = 'Barangay Hall area, Brgy. San Roque';

UPDATE `case` c
INNER JOIN incident i ON i.IncidentID = c.IncidentID
SET c.CaseStatus = 'Referred'
WHERE i.Location = 'National Hwy, Brgy. San Roque';

-- Rabies monitoring flag on all bite cases
UPDATE `case` c
INNER JOIN incident i ON i.IncidentID = c.IncidentID
SET c.RabiesMonitoring = 1
WHERE i.IncidentType = 'Animal Bite';
