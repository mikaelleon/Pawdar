
CREATE TABLE IF NOT EXISTS user (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(120) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role ENUM(
        'Dog Owner',
        'Community Reporter',
        'Veterinarian',
        'LGU Official',
        'Rescue Organization',
        'Admin'
    ) NOT NULL DEFAULT 'Community Reporter',
    Status ENUM('active', 'pending') NOT NULL DEFAULT 'active',
    Barangay VARCHAR(100) NOT NULL,
    Phone VARCHAR(30) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS dog (
    dog_id INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    DogName VARCHAR(100) NOT NULL,
    Breed VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES user(UserID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS incident (
    IncidentID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    dog_id INT NULL,
    IncidentType VARCHAR(50) NOT NULL,
    Date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Location VARCHAR(255) NOT NULL,
    Description TEXT NULL,
    FOREIGN KEY (UserID) REFERENCES user(UserID) ON DELETE CASCADE,
    FOREIGN KEY (dog_id) REFERENCES dog(dog_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS vaccinerecord (
    VaccineID INT AUTO_INCREMENT PRIMARY KEY,
    dog_id INT NOT NULL,
    VaccineName VARCHAR(100) NOT NULL,
    DateGiven DATE NOT NULL,
    VetName VARCHAR(120) NULL,
    FOREIGN KEY (dog_id) REFERENCES dog(dog_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `case` (
    CaseID INT AUTO_INCREMENT PRIMARY KEY,
    IncidentID INT NOT NULL UNIQUE,
    CaseStatus ENUM('Received', 'Under Investigation', 'Resolved', 'Referred') NOT NULL DEFAULT 'Received',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (IncidentID) REFERENCES incident(IncidentID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS corroborations (
    corroboration_id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incident(IncidentID) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(UserID) ON DELETE CASCADE,
    UNIQUE KEY unique_corroboration (incident_id, user_id)
);

CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    link VARCHAR(255) NULL,
    FOREIGN KEY (user_id) REFERENCES user(UserID) ON DELETE CASCADE
);

-- Demo users (password for all: password)
INSERT INTO user (Name, Email, Password, Role, Status, Barangay, Phone) VALUES
('Maria Santos', 'maria.santos@email.com', '$2y$10$hejq7bVagXzje2wmf6HfIuseZbetNVXlz9xpc5TvrSt2njn1ZS83S', 'Community Reporter', 'active', 'San Roque', '09171234567'),
('Rosa Castillo', 'rosa.castillo@email.com', '$2y$10$hejq7bVagXzje2wmf6HfIuseZbetNVXlz9xpc5TvrSt2njn1ZS83S', 'Dog Owner', 'active', 'San Roque', '09175550142'),
('Dr. Ana Reyes', 'ana.reyes@email.com', '$2y$10$hejq7bVagXzje2wmf6HfIuseZbetNVXlz9xpc5TvrSt2njn1ZS83S', 'Veterinarian', 'active', 'San Roque', '09178881234'),
('Engr. Luis Cruz', 'luis.cruz@email.com', '$2y$10$hejq7bVagXzje2wmf6HfIuseZbetNVXlz9xpc5TvrSt2njn1ZS83S', 'LGU Official', 'active', 'San Roque', '09179990001'),
('Paws Rescue PH', 'rescue@pawdar.org', '$2y$10$hejq7bVagXzje2wmf6HfIuseZbetNVXlz9xpc5TvrSt2njn1ZS83S', 'Rescue Organization', 'active', 'San Roque', '09171112222'),
('Admin User', 'admin@pawdar.org', '$2y$10$hejq7bVagXzje2wmf6HfIuseZbetNVXlz9xpc5TvrSt2njn1ZS83S', 'Admin', 'active', 'San Roque', '09170000000')
ON DUPLICATE KEY UPDATE Name = VALUES(Name);

INSERT INTO dog (UserID, DogName, Breed)
SELECT u.UserID, 'Bantay', 'Aspin'
FROM user u WHERE u.Email = 'rosa.castillo@email.com'
AND NOT EXISTS (SELECT 1 FROM dog d WHERE d.UserID = u.UserID AND d.DogName = 'Bantay');

INSERT INTO incident (UserID, dog_id, IncidentType, Date, Location, Description)
SELECT u.UserID, NULL, 'Animal Bite', DATE_SUB(NOW(), INTERVAL 12 MINUTE), 'Riverside Park, Brgy. San Roque', 'Loose dog bit a jogger near the creek.'
FROM user u WHERE u.Email = 'maria.santos@email.com'
AND NOT EXISTS (SELECT 1 FROM incident i WHERE i.Location = 'Riverside Park, Brgy. San Roque');

INSERT INTO incident (UserID, dog_id, IncidentType, Date, Location, Description)
SELECT u.UserID, NULL, 'Injured Stray', DATE_SUB(NOW(), INTERVAL 38 MINUTE), 'Market St., Brgy. San Roque', 'Limping stray near the wet market.'
FROM user u WHERE u.Email = 'rosa.castillo@email.com'
AND NOT EXISTS (SELECT 1 FROM incident i WHERE i.Location = 'Market St., Brgy. San Roque');

INSERT INTO incident (UserID, dog_id, IncidentType, Date, Location, Description)
SELECT u.UserID, NULL, 'Aggressive Behavior', DATE_SUB(NOW(), INTERVAL 2 HOUR), 'Acacia Ave, Brgy. San Roque', 'Dog lunging at passersby.'
FROM user u WHERE u.Email = 'maria.santos@email.com'
AND NOT EXISTS (SELECT 1 FROM incident i WHERE i.Location = 'Acacia Ave, Brgy. San Roque');

INSERT INTO incident (UserID, dog_id, IncidentType, Date, Location, Description)
SELECT u.UserID, NULL, 'Vehicular Accident', DATE_SUB(NOW(), INTERVAL 5 HOUR), 'National Hwy, Brgy. San Roque', 'Dog hit by motorcycle, conscious.'
FROM user u WHERE u.Email = 'rosa.castillo@email.com'
AND NOT EXISTS (SELECT 1 FROM incident i WHERE i.Location = 'National Hwy, Brgy. San Roque');

INSERT INTO incident (UserID, dog_id, IncidentType, Date, Location, Description)
SELECT u.UserID, d.dog_id, 'Trash Disturbance', DATE_SUB(NOW(), INTERVAL 1 DAY), 'Barangay Hall area, Brgy. San Roque', 'Dogs scattering garbage bins.'
FROM user u
LEFT JOIN dog d ON d.UserID = u.UserID
WHERE u.Email = 'rosa.castillo@email.com'
AND NOT EXISTS (SELECT 1 FROM incident i WHERE i.Location = 'Barangay Hall area, Brgy. San Roque');

INSERT INTO `case` (IncidentID, CaseStatus)
SELECT i.IncidentID, 'Under Investigation'
FROM incident i
WHERE i.Location = 'Riverside Park, Brgy. San Roque'
AND NOT EXISTS (SELECT 1 FROM `case` c WHERE c.IncidentID = i.IncidentID);

INSERT INTO `case` (IncidentID, CaseStatus)
SELECT i.IncidentID, 'Received'
FROM incident i
WHERE i.Location = 'Market St., Brgy. San Roque'
AND NOT EXISTS (SELECT 1 FROM `case` c WHERE c.IncidentID = i.IncidentID);

INSERT INTO `case` (IncidentID, CaseStatus)
SELECT i.IncidentID, 'Resolved'
FROM incident i
WHERE i.Location = 'Acacia Ave, Brgy. San Roque'
AND NOT EXISTS (SELECT 1 FROM `case` c WHERE c.IncidentID = i.IncidentID);

INSERT INTO `case` (IncidentID, CaseStatus)
SELECT i.IncidentID, 'Resolved'
FROM incident i
WHERE i.Location = 'Barangay Hall area, Brgy. San Roque'
AND NOT EXISTS (SELECT 1 FROM `case` c WHERE c.IncidentID = i.IncidentID);

INSERT INTO corroborations (incident_id, user_id)
SELECT i.IncidentID, u.UserID
FROM incident i
CROSS JOIN user u
WHERE i.Location = 'Acacia Ave, Brgy. San Roque'
AND u.Email IN ('rosa.castillo@email.com', 'luis.cruz@email.com')
AND NOT EXISTS (
    SELECT 1 FROM corroborations c
    WHERE c.incident_id = i.IncidentID AND c.user_id = u.UserID
);

INSERT INTO notifications (user_id, message, is_read, link)
SELECT u.UserID, 'Welcome to Pawdar! Stay alert for incidents in your barangay.', 0, 'feed.php'
FROM user u
WHERE u.Email = 'maria.santos@email.com'
AND NOT EXISTS (
    SELECT 1 FROM notifications n
    WHERE n.user_id = u.UserID AND n.message LIKE 'Welcome to Pawdar%'
);
