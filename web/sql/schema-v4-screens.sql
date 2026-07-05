-- Pawdar schema v4: screen features + demo map data.
-- Run schema.sql, v2, and v3 first. Column ALTERs are safe to re-run (IF NOT EXISTS).

ALTER TABLE `case` ADD COLUMN IF NOT EXISTS RabiesMonitoring TINYINT NOT NULL DEFAULT 0 AFTER CaseStatus;

ALTER TABLE incident ADD COLUMN IF NOT EXISTS latitude DECIMAL(10, 7) NULL AFTER Location;
ALTER TABLE incident ADD COLUMN IF NOT EXISTS longitude DECIMAL(10, 7) NULL AFTER latitude;
ALTER TABLE incident ADD COLUMN IF NOT EXISTS photo_path VARCHAR(255) NULL AFTER Description;
ALTER TABLE incident ADD COLUMN IF NOT EXISTS edited_at DATETIME NULL AFTER photo_path;
ALTER TABLE incident ADD COLUMN IF NOT EXISTS area_regular TINYINT NOT NULL DEFAULT 0 AFTER edited_at;

ALTER TABLE `case`
    MODIFY CaseStatus ENUM(
        'Received',
        'Under Investigation',
        'Action Taken',
        'Resolved',
        'Referred'
    ) NOT NULL DEFAULT 'Received';

CREATE TABLE IF NOT EXISTS case_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    CaseID INT NOT NULL,
    CaseStatus VARCHAR(50) NOT NULL,
    updated_by INT NULL,
    notes VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CaseID) REFERENCES `case`(CaseID) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES user(UserID) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS rabies_checklist (
    checklist_id INT AUTO_INCREMENT PRIMARY KEY,
    CaseID INT NOT NULL,
    day_number TINYINT NOT NULL,
    check_date DATE NULL,
    status ENUM('Pending', 'Checked', 'Flagged') NOT NULL DEFAULT 'Pending',
    notes VARCHAR(255) NULL,
    checked_by INT NULL,
    checked_at DATETIME NULL,
    UNIQUE KEY uniq_case_day (CaseID, day_number),
    FOREIGN KEY (CaseID) REFERENCES `case`(CaseID) ON DELETE CASCADE,
    FOREIGN KEY (checked_by) REFERENCES user(UserID) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS rescue_cases (
    rescue_case_id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL UNIQUE,
    rescue_org_id INT NOT NULL,
    status ENUM('Spotted', 'Rescued', 'Under Vet Care', 'Ready for Adoption') NOT NULL DEFAULT 'Spotted',
    claimed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incident(IncidentID) ON DELETE CASCADE,
    FOREIGN KEY (rescue_org_id) REFERENCES user(UserID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS rescue_case_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    rescue_case_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    updated_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rescue_case_id) REFERENCES rescue_cases(rescue_case_id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES user(UserID) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS stray_sightings (
    sighting_id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    user_id INT NULL,
    location_note VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incident(IncidentID) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(UserID) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS adoption_listings (
    listing_id INT AUTO_INCREMENT PRIMARY KEY,
    rescue_case_id INT NULL,
    rescue_org_id INT NOT NULL,
    dog_name VARCHAR(100) NULL,
    dog_description TEXT NOT NULL,
    estimated_age VARCHAR(30) NULL,
    temperament_notes VARCHAR(255) NULL,
    photo_path VARCHAR(255) NULL,
    sponsor_link VARCHAR(255) NULL,
    status ENUM('Available', 'Pending', 'Adopted') NOT NULL DEFAULT 'Available',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rescue_case_id) REFERENCES rescue_cases(rescue_case_id) ON DELETE SET NULL,
    FOREIGN KEY (rescue_org_id) REFERENCES user(UserID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS advisories (
    advisory_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    barangay VARCHAR(100) NOT NULL,
    title VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,
    is_pinned TINYINT NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(UserID) ON DELETE CASCADE
);

-- Pseudo map coordinates for demo incidents (Batangas area)
UPDATE incident SET latitude = 13.7568 + (IncidentID % 7) * 0.002, longitude = 121.0583 + (IncidentID % 5) * 0.003
WHERE latitude IS NULL;

UPDATE `case` c
JOIN incident i ON i.IncidentID = c.IncidentID
SET c.RabiesMonitoring = 1
WHERE i.IncidentType = 'Animal Bite' AND c.RabiesMonitoring = 0;

INSERT INTO stray_sightings (incident_id, user_id, location_note)
SELECT i.IncidentID, i.UserID, i.Location
FROM incident i
WHERE i.IncidentType = 'Injured Stray'
AND NOT EXISTS (SELECT 1 FROM stray_sightings s WHERE s.incident_id = i.IncidentID);

UPDATE incident i
SET area_regular = 1
WHERE i.IncidentType = 'Injured Stray'
AND (SELECT COUNT(*) FROM stray_sightings s WHERE s.incident_id = i.IncidentID) >= 1
AND i.Location LIKE '%San Roque%';
