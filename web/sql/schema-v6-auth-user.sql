-- Pawdar schema v6: auth, signup, notifications, and registry columns missing from v2–v5.
-- Run after schema.sql, v2, v3, v4, and v5. Safe to re-run (IF NOT EXISTS).
-- Import via phpMyAdmin on InfinityFree — no runner.php / CLI required.

-- dog (registry extended fields; v2 adds RegistryID/Gender/Size/DogType/Status)
ALTER TABLE dog ADD COLUMN IF NOT EXISTS breed_id INT NULL AFTER Breed;
ALTER TABLE dog ADD COLUMN IF NOT EXISTS Age INT NULL AFTER Status;
ALTER TABLE dog ADD COLUMN IF NOT EXISTS photo_path VARCHAR(255) NULL AFTER Age;
ALTER TABLE dog ADD COLUMN IF NOT EXISTS health_notes TEXT NULL AFTER photo_path;

-- user (structured signup, email verification, password reset, notification prefs)
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS notify_incidents TINYINT NOT NULL DEFAULT 1 AFTER Phone;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS notify_dog_match TINYINT NOT NULL DEFAULT 1 AFTER notify_incidents;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS notify_case_updates TINYINT NOT NULL DEFAULT 1 AFTER notify_dog_match;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS notify_vaccine TINYINT NOT NULL DEFAULT 1 AFTER notify_case_updates;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS reset_token VARCHAR(64) NULL AFTER notify_vaccine;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS reset_token_expires DATETIME NULL AFTER reset_token;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS last_name VARCHAR(80) NULL AFTER Name;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS first_name VARCHAR(80) NULL AFTER last_name;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS middle_name VARCHAR(80) NULL AFTER first_name;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS name_suffix VARCHAR(20) NULL AFTER middle_name;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS City VARCHAR(100) NULL AFTER Barangay;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS city_id INT NULL AFTER City;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS barangay_id INT NULL AFTER city_id;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS email_verified_at DATETIME NULL AFTER barangay_id;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS email_verify_token VARCHAR(64) NULL AFTER email_verified_at;
ALTER TABLE `user` ADD COLUMN IF NOT EXISTS email_verify_expires DATETIME NULL AFTER email_verify_token;

-- vaccinerecord
ALTER TABLE vaccinerecord ADD COLUMN IF NOT EXISTS NextDueDate DATE NULL AFTER DateGiven;
ALTER TABLE vaccinerecord ADD COLUMN IF NOT EXISTS vax_status ENUM('Verified', 'Unverified', 'Expired') NOT NULL DEFAULT 'Unverified' AFTER VetName;

-- notifications
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS notification_type VARCHAR(30) NOT NULL DEFAULT 'general' AFTER message;

-- Existing seed/demo rows predate email verification — mark verified so login still works.
UPDATE `user`
SET email_verified_at = COALESCE(email_verified_at, created_at, NOW())
WHERE email_verified_at IS NULL;
