-- Pawdar schema v8: professor feedback UI fixes.
-- Run after schema-v7-cases-demo.sql. Safe to re-run (IF NOT EXISTS / idempotent UPDATEs).

ALTER TABLE dog ADD COLUMN IF NOT EXISTS coat_color VARCHAR(80) NULL AFTER health_notes;
ALTER TABLE dog ADD COLUMN IF NOT EXISTS weight_kg DECIMAL(6, 2) NULL AFTER coat_color;
ALTER TABLE dog ADD COLUMN IF NOT EXISTS distinguishing_marks TEXT NULL AFTER weight_kg;
ALTER TABLE dog ADD COLUMN IF NOT EXISTS temperament_notes TEXT NULL AFTER distinguishing_marks;

ALTER TABLE breeds ADD COLUMN IF NOT EXISTS image_url VARCHAR(512) NULL AFTER friendliness_score;

UPDATE incident SET IncidentType = 'Disturbance' WHERE IncidentType = 'Trash Disturbance';
UPDATE first_aid_guides SET incident_type = 'Disturbance' WHERE incident_type = 'Trash Disturbance';
UPDATE first_aid_guides SET title = 'Disturbance First Aid' WHERE title = 'Trash Disturbance First Aid';
UPDATE first_aid_guides SET icon = 'footprints' WHERE incident_type = 'Disturbance' AND icon = 'trash-2';
