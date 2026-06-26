-- Pawdar breeds schema v3 (Kaggle CSV import)
-- Run via: php sql/import-breeds.php

USE pawdar;

DROP TABLE IF EXISTS breeds_staging;

CREATE TABLE IF NOT EXISTS breeds_staging (
    breed_name VARCHAR(120) NOT NULL,
    dog_size VARCHAR(50) NULL,
    weight_text VARCHAR(80) NULL,
    weight_kg VARCHAR(30) NULL,
    lifespan VARCHAR(40) NULL,
    breed_group VARCHAR(120) NULL,
    affection_family VARCHAR(20) NULL,
    kid_friendly VARCHAR(20) NULL,
    dog_friendly VARCHAR(20) NULL,
    stranger_friendly VARCHAR(20) NULL,
    general_health VARCHAR(20) NULL,
    energy_level VARCHAR(20) NULL,
    easy_to_train VARCHAR(20) NULL,
    intelligence VARCHAR(20) NULL
);

-- Replace legacy breeds table with normalized schema
DROP TABLE IF EXISTS breeds;

CREATE TABLE breeds (
    breed_id INT AUTO_INCREMENT PRIMARY KEY,
    breed_name VARCHAR(100) NOT NULL UNIQUE,
    size_category ENUM('Small', 'Medium', 'Large') NOT NULL,
    weight_range VARCHAR(30) NULL,
    lifespan VARCHAR(20) NULL,
    temperament_notes VARCHAR(255) NULL,
    common_health_risks VARCHAR(255) NULL,
    loyalty_score TINYINT NOT NULL DEFAULT 3,
    energy_score TINYINT NOT NULL DEFAULT 3,
    friendliness_score TINYINT NOT NULL DEFAULT 3,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_loyalty CHECK (loyalty_score BETWEEN 1 AND 5),
    CONSTRAINT chk_energy CHECK (energy_score BETWEEN 1 AND 5),
    CONSTRAINT chk_friendliness CHECK (friendliness_score BETWEEN 1 AND 5)
);

-- dog.breed_id FK (safe re-run skips if exists)
SET @col_exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dog' AND COLUMN_NAME = 'breed_id'
);
SET @sql = IF(@col_exists = 0, 'ALTER TABLE dog ADD COLUMN breed_id INT NULL AFTER Breed', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
