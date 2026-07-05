CREATE TABLE IF NOT EXISTS breeds (
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

-- Staging table used only by import-breeds.php (created/dropped during import).
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
