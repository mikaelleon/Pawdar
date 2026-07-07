-- Pawdar schema v2: first-aid guides + dog registry demo data.
-- Run schema.sql first. These ALTERs are safe to re-run (IF NOT EXISTS).

ALTER TABLE dog ADD COLUMN IF NOT EXISTS RegistryID VARCHAR(20) NULL AFTER dog_id;
ALTER TABLE dog ADD COLUMN IF NOT EXISTS Gender VARCHAR(20) NULL AFTER Breed;
ALTER TABLE dog ADD COLUMN IF NOT EXISTS Size ENUM('Small', 'Medium', 'Large') NULL AFTER Gender;
ALTER TABLE dog ADD COLUMN IF NOT EXISTS DogType VARCHAR(50) NULL AFTER Size;
ALTER TABLE dog ADD COLUMN IF NOT EXISTS Status ENUM('Registered', 'Pending', 'Inactive') NOT NULL DEFAULT 'Registered' AFTER DogType;

UPDATE dog SET RegistryID = CONCAT('PWD-2024-', LPAD(dog_id, 5, '0'))
WHERE RegistryID IS NULL OR RegistryID = '';

UPDATE dog SET Gender = 'Male', Size = 'Medium', DogType = 'Owned'
WHERE DogName = 'Bantay';

CREATE TABLE IF NOT EXISTS first_aid_guides (
    guide_id INT AUTO_INCREMENT PRIMARY KEY,
    incident_type VARCHAR(50) NOT NULL UNIQUE,
    severity_level ENUM('Severe', 'Moderate', 'Mild') NOT NULL,
    title VARCHAR(150) NOT NULL,
    steps JSON NOT NULL,
    warning_text TEXT NOT NULL,
    source_citation VARCHAR(255) NOT NULL,
    icon VARCHAR(40) NOT NULL DEFAULT 'dog',
    sort_order INT NOT NULL DEFAULT 0
);

INSERT INTO first_aid_guides (incident_type, severity_level, title, steps, warning_text, source_citation, icon, sort_order) VALUES
('Animal Bite', 'Severe', 'Animal Bite First Aid',
 '["Wash the wound immediately with soap and running water for at least 15 minutes.","Apply an antiseptic such as povidone-iodine or alcohol to the cleaned area.","Control bleeding with a clean cloth and gentle pressure; do not close deep wounds.","Note the dog''s description and location, then go to the nearest clinic for anti-rabies evaluation."]',
 'Seek immediate veterinary or medical attention — any bite carries rabies risk.',
 'WHO & PH Dept. of Health rabies protocol (2024)', 'dog', 1),
('Vehicular Accident', 'Severe', 'Vehicular Accident First Aid',
 '["Ensure traffic is clear before approaching the dog.","Do not move the dog unless it is in immediate danger.","Check for breathing and visible bleeding; apply gentle pressure to wounds.","Cover the dog with a blanket to prevent shock and call a vet or rescue immediately."]',
 'Do not attempt to splint broken bones — improper handling can cause further injury.',
 'AVMA Emergency Care Guidelines (2023)', 'car', 2),
('Injured Stray', 'Moderate', 'Injured Stray First Aid',
 '["Approach slowly from the side; avoid direct eye contact.","Use a blanket or towel to gently restrain the dog if needed.","Check for visible wounds and apply light pressure to stop bleeding.","Contact a local rescue organization or vet — do not attempt complex treatment alone."]',
 'Injured strays may bite out of fear — protect yourself with gloves or a barrier.',
 'PAWS Animal Rescue Field Guide (2024)', 'bandage', 3),
('Aggressive Behavior', 'Moderate', 'Aggressive Behavior First Aid',
 '["Do not run — stand still or back away slowly without turning your back.","Put an object (bag, jacket, bike) between you and the dog.","If bitten, follow animal bite first aid steps immediately.","Report the incident on Pawdar so your barangay can respond."]',
 'Never attempt to physically confront or punish an aggressive dog.',
 'CDC Dog Bite Prevention Guidelines (2024)', 'alert-triangle', 4),
('Disturbance', 'Mild', 'Disturbance First Aid',
 '["Stay calm and keep a safe distance from the dog.","Avoid chasing or cornering the animal.","If the dog appears sick or injured, report it as an Injured Stray instead.","Contact the barangay if stray dogs are regularly causing disturbances in the area."]',
 'Stray dogs causing disturbances are often hungry or seeking shelter — avoid feeding them directly as it encourages return visits.',
 'Local Barangay Animal Control Advisory (2024)', 'footprints', 5)
ON DUPLICATE KEY UPDATE title = VALUES(title);

INSERT INTO dog (UserID, RegistryID, DogName, Breed, Gender, Size, DogType, Status)
SELECT u.UserID, 'PWD-2024-00832', 'Bruno', 'Aspin (Asong Pinoy)', 'Male', 'Medium', 'Owned', 'Pending'
FROM user u WHERE u.Email = 'maria.santos@email.com'
AND NOT EXISTS (SELECT 1 FROM dog d WHERE d.RegistryID = 'PWD-2024-00832');

INSERT INTO vaccinerecord (dog_id, VaccineName, DateGiven, VetName)
SELECT d.dog_id, 'Anti-Rabies · Annual', DATE_ADD(CURDATE(), INTERVAL 300 DAY), 'Dr. A. Lim'
FROM dog d WHERE d.DogName = 'Bantay'
AND NOT EXISTS (SELECT 1 FROM vaccinerecord v WHERE v.dog_id = d.dog_id);
