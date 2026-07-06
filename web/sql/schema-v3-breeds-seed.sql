-- =============================================================================
-- IMPORT THIS FILE in phpMyAdmin (NOT generate-breeds-seed.php — that is PHP)
-- =============================================================================
-- Pawdar v3 breed seed data (Kaggle archive/dogs_cleaned.csv + Aspin)
-- Source: archive/dogs_cleaned.csv (392 breeds including Aspin)
-- Regenerate locally only: php sql/generate-breeds-seed.php
-- Run AFTER schema-v3-breeds.sql (creates breeds table).
-- Safe to re-run: uses ON DUPLICATE KEY UPDATE on breed_name.

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Afador',
    'Large',
    '28.1 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Affenhuahua',
    'Small',
    '3.6 kg',
    '13 to 18 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Affenpinscher',
    'Small',
    '3.6 kg',
    '12 to 14 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Afghan Hound',
    'Large',
    '24.8 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Airedale Terrier',
    'Large',
    '23.6 kg',
    '10 to 13 years',
    'Terrier Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Akbash',
    'Large',
    '48.4 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    2,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Akita',
    'Large',
    '45 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    1
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Akita Chow',
    'Large',
    '52.4 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    1,
    4,
    1
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Akita Pit',
    'Large',
    '22.5 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Akita Shepherd',
    'Large',
    '43.9 kg',
    '10 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Alaskan Klee Kai',
    'Medium',
    '5.6 kg',
    '12 to 16 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Alaskan Malamute',
    'Large',
    '39.4 kg',
    '12 to 15 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'American Bulldog',
    'Large',
    '40.5 kg',
    '10 to 16 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'American English Coonhound',
    'Large',
    '25.9 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'American Eskimo Dog',
    'Large',
    '13.5 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'American Foxhound',
    'Large',
    '22.5 kg',
    '12 to 13 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'American Hairless Terrier',
    'Medium',
    '5.9 kg',
    '13 to 16 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'American Leopard Hound',
    'Large',
    '24.8 kg',
    '12 to 15 years.',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'American Pit Bull Terrier',
    'Large',
    '25.9 kg',
    '12 to 16 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'American Pugabull',
    'Large',
    '21.4 kg',
    '12 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'American Staffordshire Terrier',
    'Large',
    '22.5 kg',
    '10 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'American Water Spaniel',
    'Large',
    '15.8 kg',
    '12 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Anatolian Shepherd Dog',
    'Large',
    '51.8 kg',
    '11 to 13 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    3,
    3,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Appenzeller Sennenhunde',
    'Large',
    '23.2 kg',
    '9 to 12 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Aspin (Asong Pinoy)',
    'Medium',
    '8 - 20 kg',
    '10 - 14 years',
    'Loyal, alert, adaptable, territorial',
    'Generally hardy; skin allergies, tick-borne disease',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Auggie',
    'Large',
    '11.3 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Aussiedoodle',
    'Large',
    '21.4 kg',
    '10 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Aussiepom',
    'Medium',
    '9 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Australian Cattle Dog',
    'Large',
    '18 kg',
    '12 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Australian Kelpie',
    'Large',
    '16 kg',
    '10 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Australian Retriever',
    'Large',
    '19.1 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Australian Shepherd',
    'Large',
    '23.6 kg',
    '12 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Australian Shepherd Husky',
    'Large',
    '23.6 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Australian Shepherd Lab Mix',
    'Large',
    '27 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Australian Shepherd Pit Bull Mix',
    'Large',
    '25.9 kg',
    '10 to 16 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Australian Stumpy Tail Cattle Dog',
    'Large',
    '19.4 kg',
    '13 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Australian Terrier',
    'Medium',
    '6.8 kg',
    'Up to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Azawakh',
    'Large',
    '19.8 kg',
    '12 to 15 years',
    'Hound Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    3,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Barbet',
    'Large',
    '22.3 kg',
    '13 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Basenji',
    'Medium',
    '10.4 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bassador',
    'Large',
    '25.9 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    3,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Basset Fauve de Bretagne',
    'Large',
    '13.5 kg',
    '12 to 15 years.',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Basset Hound',
    'Large',
    '25.9 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    2,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Basset Retriever',
    'Large',
    '24.8 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bavarian Mountain Scent Hound',
    'Large',
    '22.3 kg',
    '10 to 14 years.',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Beabull',
    'Large',
    '20.3 kg',
    '10 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Beagle',
    'Medium',
    '10.8 kg',
    '10 to 15 years',
    'Hound Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Beaglier',
    'Medium',
    '6.8 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bearded Collie',
    'Large',
    '22.5 kg',
    '12 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bedlington Terrier',
    'Medium',
    '9 kg',
    '14 to 16 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Belgian Laekenois',
    'Large',
    '24.8 kg',
    '10 to 12 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Belgian Malinois',
    'Large',
    '27 kg',
    '12 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Belgian Sheepdog',
    'Large',
    '30.4 kg',
    '10 to 12 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Belgian Tervuren',
    'Large',
    '24.8 kg',
    '10 to 12 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bergamasco Sheepdog',
    'Large',
    '31.5 kg',
    '13 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Berger Picard',
    'Large',
    '27 kg',
    '13 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bernedoodle',
    'Large',
    '22.5 kg',
    '12 to 18 years. Tiny',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bernese Mountain Dog',
    'Large',
    '41.6 kg',
    '6 to 10 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bichon Frise',
    'Small',
    '4.3 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Biewer Terrier',
    'Small',
    '2.7 kg',
    '12 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Black and Tan Coonhound',
    'Large',
    '39.4 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Black Mouth Cur',
    'Large',
    '30.4 kg',
    '12 to 18 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Black Russian Terrier',
    'Large',
    '49.5 kg',
    '10 to 11 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bloodhound',
    'Large',
    '42.8 kg',
    '11 to 15 years',
    'Hound Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Blue Lacy',
    'Large',
    '16.9 kg',
    '12 to 16 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bluetick Coonhound',
    'Large',
    '32.6 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bocker',
    'Large',
    '11.3 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boerboel',
    'Large',
    '69.8 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boglen Terrier',
    'Large',
    '11.3 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bohemian Shepherd',
    'Large',
    '21.4 kg',
    '10 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bolognese',
    'Small',
    '5 kg',
    '12 to 14 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Borador',
    'Large',
    '23.6 kg',
    '10 to 15 years',
    'Hybrid Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Border Collie',
    'Large',
    '16.9 kg',
    '12 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Border Sheepdog',
    'Large',
    '15.8 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Border Terrier',
    'Medium',
    '5.9 kg',
    '12 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bordoodle',
    'Large',
    '20.3 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Borzoi',
    'Large',
    '36 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    2,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'BoShih',
    'Medium',
    '6.8 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bossie',
    'Large',
    '14.6 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boston Boxer',
    'Large',
    '17.3 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boston Terrier',
    'Medium',
    '7.9 kg',
    '13 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boston Terrier Pekingese Mix',
    'Medium',
    '7.9 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bouvier des Flandres',
    'Large',
    '38.3 kg',
    '10 to 12 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boxador',
    'Large',
    '36 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boxer',
    'Large',
    '29.3 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boxerdoodle',
    'Large',
    '18.5 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boxmatian',
    'Large',
    '31.5 kg',
    '10 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boxweiler',
    'Large',
    '38.3 kg',
    '8 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Boykin Spaniel',
    'Large',
    '14.6 kg',
    '10 to 12 years',
    'Sporting Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bracco Italiano',
    'Large',
    '32.6 kg',
    '10 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Braque du Bourbonnais',
    'Large',
    '20.3 kg',
    '12 to 15 years.',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Briard',
    'Large',
    '38.3 kg',
    '10 to 12 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Brittany',
    'Large',
    '15.8 kg',
    '10 to 13 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Broholmer',
    'Large',
    '54 kg',
    '8 to 12 years.',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Brussels Griffon',
    'Small',
    '4.3 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bugg',
    'Medium',
    '7.9 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bull-Pei',
    'Large',
    '22.5 kg',
    '9 to 11 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    3,
    2,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bullador',
    'Large',
    '31.5 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bull Arab',
    'Large',
    '34.9 kg',
    '12 to 15 years',
    'Hybrid Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bullboxer Pit',
    'Large',
    '29.3 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bulldog',
    'Large',
    '20.3 kg',
    '8 to 12 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    2,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bullmastiff',
    'Large',
    '51.8 kg',
    '8 to 10 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    2,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bullmatian',
    'Large',
    '23.9 kg',
    '8 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Bull Terrier',
    'Large',
    '24.8 kg',
    '10 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cairn Terrier',
    'Medium',
    '6.1 kg',
    '12 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Canaan Dog',
    'Large',
    '20.3 kg',
    '12 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cane Corso',
    'Large',
    '47.3 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cardigan Welsh Corgi',
    'Large',
    '14.2 kg',
    '12 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Carolina Dog',
    'Large',
    '19.8 kg',
    '12 to 15 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    2,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Catahoula Bulldog',
    'Large',
    '39.4 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Catahoula Leopard Dog',
    'Large',
    '31.5 kg',
    '10 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Caucasian Shepherd Dog',
    'Large',
    '67.5 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    2,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cav-a-Jack',
    'Medium',
    '7 kg',
    '12 to 17 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cavachon',
    'Large',
    '11.3 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cavador',
    'Large',
    '17.3 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cavalier King Charles Spaniel',
    'Medium',
    '7 kg',
    '9 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cavapoo',
    'Medium',
    '7.7 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Central Asian Shepherd Dog',
    'Large',
    '44.6 kg',
    '12 to 15 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cesky Terrier',
    'Medium',
    '9.7 kg',
    '10 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    2,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chabrador',
    'Large',
    '28.1 kg',
    '9 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cheagle',
    'Medium',
    '6.5 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chesapeake Bay Retriever',
    'Large',
    '30.4 kg',
    '10 to 12 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chi-Poo',
    'Medium',
    '5.6 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chi Chi',
    'Small',
    '3.4 kg',
    '11 to 20 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chigi',
    'Medium',
    '6.8 kg',
    '12 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chihuahua',
    'Small',
    '2 kg',
    '10 to 18 years',
    'Companion Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chilier',
    'Small',
    '4.1 kg',
    '12 to 16 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chinese Crested',
    'Medium',
    '5.4 kg',
    '10 to 14 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    2,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chinese Shar-Pei',
    'Large',
    '21.4 kg',
    '8 to 12 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    2,
    2,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chinook',
    'Large',
    '28.1 kg',
    '12 to 15 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chion',
    'Small',
    '3.4 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chipin',
    'Small',
    '4.5 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chiweenie',
    'Small',
    '3.8 kg',
    '12 to 16 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chorkie',
    'Small',
    '5.2 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chow Chow',
    'Large',
    '24.8 kg',
    '12 to 15 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    2,
    2,
    1
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chow Shepherd',
    'Large',
    '30.4 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    3,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chug',
    'Medium',
    '6.8 kg',
    '10 to 13',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Chusky',
    'Large',
    '23.6 kg',
    '10 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cirneco dell’Etna',
    'Medium',
    '9.7 kg',
    '12 to 14 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Clumber Spaniel',
    'Large',
    '31.5 kg',
    '12 to 12 years',
    'Sporting Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cockalier',
    'Medium',
    '8.6 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cockapoo',
    'Medium',
    '5.6 kg',
    '12 to 15 years',
    'Hybrid Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Cocker Spaniel',
    'Large',
    '11.7 kg',
    '12 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Collie',
    'Large',
    '27 kg',
    '10 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Corgidor',
    'Large',
    '21.4 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Corgi Inu',
    'Medium',
    '9.9 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    3,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Corman Shepherd',
    'Large',
    '20.3 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Coton de Tulear',
    'Small',
    '4.7 kg',
    'Starts at 14 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Croatian Sheepdog',
    'Large',
    '16.7 kg',
    '12 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Curly-Coated Retriever',
    'Large',
    '37.1 kg',
    '9 to 12 years',
    'Sporting Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Dachsador',
    'Large',
    '15.8 kg',
    '12 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Dachshund',
    'Medium',
    '10.8 kg',
    '12 to 15 years',
    'Hound Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Dalmatian',
    'Large',
    '23.2 kg',
    '13 to 16 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Dandie Dinmont Terrier',
    'Medium',
    '9.5 kg',
    '12 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Daniff',
    'Large',
    '68.6 kg',
    '8 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Danish-Swedish Farmdog',
    'Medium',
    '7.9 kg',
    '11 to 13 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Deutscher Wachtelhund',
    'Large',
    '21.4 kg',
    '12 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Doberdor',
    'Large',
    '36 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Doberman Pinscher',
    'Large',
    '31.5 kg',
    '10 to 13 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Docker',
    'Large',
    '11.3 kg',
    '12 to 14 Years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Dogo Argentino',
    'Large',
    '40.5 kg',
    '9 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Dogue de Bordeaux',
    'Large',
    '45 kg',
    '8 to 12 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    2,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Dorgi',
    'Medium',
    '9.7 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Dorkie',
    'Small',
    '3.8 kg',
    '13 to 16 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    4,
    2,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Doxiepoo',
    'Medium',
    '7.9 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Doxle',
    'Medium',
    '9.2 kg',
    '12 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Drentsche Patrijshond',
    'Large',
    '22.5 kg',
    '11 to 14 years old',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Drever',
    'Large',
    '16.9 kg',
    '12 to 15 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Dutch Shepherd',
    'Large',
    '27 kg',
    '12 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'English Cocker Spaniel',
    'Large',
    '13.5 kg',
    '12 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'English Foxhound',
    'Large',
    '29.3 kg',
    '10 to 13 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'English Setter',
    'Large',
    '28.1 kg',
    '11 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'English Springer Spaniel',
    'Large',
    '22.5 kg',
    '9 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'English Toy Spaniel',
    'Small',
    '5 kg',
    '10 to 12 years',
    'Companion Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Entlebucher Mountain Dog',
    'Large',
    '24.8 kg',
    '10 to 13 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Estrela Mountain Dog',
    'Large',
    '46.1 kg',
    '11 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Eurasier',
    'Large',
    '25 kg',
    '12 to 14 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Field Spaniel',
    'Large',
    '18.5 kg',
    '10 to 12 years',
    'Sporting Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Fila Brasileiro',
    'Large',
    '60.8 kg',
    '9 to 12 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    3,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Finnish Lapphund',
    'Large',
    '19.4 kg',
    '12 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Finnish Spitz',
    'Large',
    '12.4 kg',
    '12 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Flat-Coated Retriever',
    'Large',
    '28.1 kg',
    '10 to 12 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Fox Terrier',
    'Medium',
    '7.7 kg',
    '10 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'French Bulldog',
    'Medium',
    '9.9 kg',
    '11 to 14 years',
    'Companion Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'French Bullhuahua',
    'Medium',
    '9 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'French Spaniel',
    'Large',
    '23.6 kg',
    '10 to 12 years.',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Frenchton',
    'Medium',
    '9 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Frengle',
    'Medium',
    '10.8 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'German Longhaired Pointer',
    'Large',
    '29.5 kg',
    '11 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'German Pinscher',
    'Large',
    '15.8 kg',
    '12 to 14 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'German Shepherd Dog',
    'Large',
    '38.3 kg',
    '10 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'German Shepherd Pit Bull',
    'Large',
    '27 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'German Shepherd Rottweiler Mix',
    'Large',
    '42.8 kg',
    '9 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'German Sheprador',
    'Large',
    '34.9 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'German Shorthaired Pointer',
    'Large',
    '25.9 kg',
    '12 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'German Spitz',
    'Large',
    '11.3 kg',
    '13 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'German Wirehaired Pointer',
    'Large',
    '29.3 kg',
    '12 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Giant Schnauzer',
    'Large',
    '30.4 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Glen of Imaal Terrier',
    'Large',
    '15.8 kg',
    '12 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Goberian',
    'Large',
    '30.4 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Goldador',
    'Large',
    '31.5 kg',
    '10 to 15 years',
    'Hybrid Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Golden Cocker Retriever',
    'Large',
    '16.9 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Goldendoodle',
    'Large',
    '31.5 kg',
    '10 to 15 years',
    'Hybrid Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Golden Mountain Dog',
    'Large',
    '43.9 kg',
    '9 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Golden Retriever',
    'Large',
    '29.3 kg',
    '10 to 12 years',
    'Sporting Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Golden Retriever Corgi',
    'Large',
    '23.6 kg',
    '10 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Golden Shepherd',
    'Large',
    '31.5 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Gollie',
    'Large',
    '28.1 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Gordon Setter',
    'Large',
    '28.1 kg',
    '10 to 12 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Great Dane',
    'Large',
    '67.5 kg',
    '7 to 10 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Greater Swiss Mountain Dog',
    'Large',
    '50.6 kg',
    '7 to 9 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Great Pyrenees',
    'Large',
    '55.1 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Greyador',
    'Large',
    '29.3 kg',
    '11 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Greyhound',
    'Large',
    '30.4 kg',
    '12 to 15 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Hamiltonstovare',
    'Large',
    '25.9 kg',
    '14 to 17 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Hanoverian Scenthound',
    'Large',
    '40.5 kg',
    '12 to 14 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Harrier',
    'Large',
    '23.6 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Havanese',
    'Small',
    '4.5 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Havapoo',
    'Medium',
    '8.3 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Hokkaido',
    'Large',
    '24.8 kg',
    '11 to 13 Years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Horgi',
    'Large',
    '15.8 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Hovawart',
    'Large',
    '37.1 kg',
    '10 to 14 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Huskita',
    'Large',
    '28.1 kg',
    '10 to 13 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Huskydoodle',
    'Large',
    '22.5 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Ibizan Hound',
    'Large',
    '21.4 kg',
    '10 to 14 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Icelandic Sheepdog',
    'Large',
    '11.3 kg',
    '14 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Irish Red And White Setter',
    'Large',
    '27 kg',
    '10 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Irish Setter',
    'Large',
    '29.3 kg',
    '11 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Irish Terrier',
    'Large',
    '11.7 kg',
    '12 to 16 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Irish Water Spaniel',
    'Large',
    '24.8 kg',
    '10 to 12 years',
    'Sporting Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Irish Wolfhound',
    'Large',
    '66.4 kg',
    '6 to 8 years',
    'Hound Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    2,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Italian Greyhound',
    'Small',
    '4.7 kg',
    '14 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Jack-A-Poo',
    'Medium',
    '8.6 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Jack Chi',
    'Medium',
    '5.9 kg',
    '13 to 18 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Jack Russell Terrier',
    'Medium',
    '6.8 kg',
    '10 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Jackshund',
    'Medium',
    '9.7 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Japanese Chin',
    'Small',
    '2.9 kg',
    '10 to 14 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    3,
    2,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Japanese Spitz',
    'Medium',
    '7 kg',
    '10 to 16 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Kai Ken',
    'Large',
    '15.8 kg',
    '12 to 16 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Karelian Bear Dog',
    'Large',
    '21.2 kg',
    '10 to 13 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Keeshond',
    'Large',
    '18 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Kerry Blue Terrier',
    'Large',
    '16.4 kg',
    '12 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'King Shepherd',
    'Large',
    '50.6 kg',
    '10 to 11 years',
    'Hybrid Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Kishu Ken',
    'Large',
    '20.3 kg',
    '9 to 13 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    3,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Komondor',
    'Large',
    '40.5 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    2,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Kooikerhondje',
    'Medium',
    '10.1 kg',
    '12 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Korean Jindo Dog',
    'Large',
    '21.4 kg',
    '12 - 15 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Kuvasz',
    'Large',
    '41.6 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    1
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Kyi-Leo',
    'Small',
    '5 kg',
    '13 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Labernese',
    'Large',
    '39.4 kg',
    '6 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Labmaraner',
    'Large',
    '36 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Lab Pointer',
    'Large',
    '25.9 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Labrabull',
    'Large',
    '30.4 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Labradane',
    'Large',
    '63 kg',
    '8 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Labradoodle',
    'Large',
    '25.9 kg',
    '12 to 14 years',
    'Hybrid Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Labrador Retriever',
    'Large',
    '30.4 kg',
    '10 to 12 years',
    'Sporting Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Labrastaff',
    'Large',
    '27 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Labsky',
    'Large',
    '22.5 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Lagotto Romagnolo',
    'Large',
    '13.3 kg',
    '14 to 17 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Lakeland Terrier',
    'Medium',
    '7.2 kg',
    '12 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Lancashire Heeler',
    'Medium',
    '6.3 kg',
    '9 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Leonberger',
    'Large',
    '65.3 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Lhasa Apso',
    'Medium',
    '6.1 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Lhasapoo',
    'Medium',
    '5.6 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Lowchen',
    'Medium',
    '6.1 kg',
    '13 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Maltese',
    'Small',
    '3.2 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Maltese Shih Tzu',
    'Small',
    '4.1 kg',
    '12 to 14 years',
    'Hybrid Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Maltipoo',
    'Medium',
    '5.6 kg',
    '10 to 13 years',
    'Hybrid Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Manchester Terrier',
    'Medium',
    '7.7 kg',
    '14 to 16 years',
    'Terrier Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Maremma Sheepdog',
    'Large',
    '37.1 kg',
    '11 to 13 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Mastador',
    'Large',
    '55.1 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Mastiff',
    'Large',
    '78.8 kg',
    '6 to 10 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Miniature Pinscher',
    'Small',
    '4.3 kg',
    '10 to 14 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Miniature Schnauzer',
    'Medium',
    '7 kg',
    '12 to 14 years',
    'Terrier Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Morkie',
    'Small',
    '4.5 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Mountain Cur',
    'Large',
    '20.3 kg',
    '10 to 16 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Mountain Feist',
    'Medium',
    '9 kg',
    '10 to 18 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Mudi',
    'Medium',
    '10.6 kg',
    '12 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Neapolitan Mastiff',
    'Large',
    '72 kg',
    '8 to 10 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Newfoundland',
    'Large',
    '56.3 kg',
    '8 to 10 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Norfolk Terrier',
    'Small',
    '5.2 kg',
    '12 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Northern Inuit Dog',
    'Large',
    '37.1 kg',
    '12 to 15 years',
    'Hybrid Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Norwegian Buhund',
    'Large',
    '14.9 kg',
    '12 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Norwegian Elkhound',
    'Large',
    '21.4 kg',
    '12 to 15 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Norwegian Lundehund',
    'Medium',
    '6.5 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Norwich Terrier',
    'Medium',
    '5.4 kg',
    '10 to 14 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Nova Scotia Duck Tolling Retriever',
    'Large',
    '19.1 kg',
    '10 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Old English Sheepdog',
    'Large',
    '40.5 kg',
    '10 to 12 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Otterhound',
    'Large',
    '43.9 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Papillon',
    'Small',
    '2.9 kg',
    '12 to 16 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Papipoo',
    'Small',
    '4.5 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Patterdale Terrier',
    'Medium',
    '5.4 kg',
    '11 to 14 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Peekapoo',
    'Medium',
    '5.4 kg',
    '10 to 15 years',
    'Hybrid Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pekingese',
    'Small',
    '4.7 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    2,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pembroke Welsh Corgi',
    'Large',
    '13.5 kg',
    '12 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Petit Basset Griffon Vendéen',
    'Large',
    '15.8 kg',
    'Starts at 14 years',
    'Hound Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pharaoh Hound',
    'Large',
    '22.5 kg',
    '11 to 14 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    2,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pitsky',
    'Large',
    '24.8 kg',
    '12 to 16 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Plott',
    'Large',
    '25.9 kg',
    '12 to 14 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pocket Beagle',
    'Small',
    '5 kg',
    'Starts at 10 years',
    'Hound Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pointer',
    'Large',
    '27 kg',
    '12 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Polish Lowland Sheepdog',
    'Large',
    '20.3 kg',
    '10 to 12 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pomapoo',
    'Small',
    '4.5 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pomchi',
    'Small',
    '3.6 kg',
    '12 to 18 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pomeagle',
    'Medium',
    '6.8 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pomeranian',
    'Small',
    '2.3 kg',
    '12 to 16 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pomsky',
    'Medium',
    '10.1 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Poochon',
    'Small',
    '5.2 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Poodle',
    'Large',
    '17.1 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Portuguese Podengo Pequeno',
    'Small',
    '5 kg',
    '12 to 15 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Portuguese Pointer',
    'Large',
    '21.2 kg',
    '12 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Portuguese Sheepdog',
    'Large',
    '21.6 kg',
    '12 to 13 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Portuguese Water Dog',
    'Large',
    '21.4 kg',
    '10 to 14 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pudelpointer',
    'Large',
    '23.6 kg',
    '10 to 14 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pug',
    'Medium',
    '7.2 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pugalier',
    'Medium',
    '6.8 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Puggle',
    'Medium',
    '10.8 kg',
    '10 to 15 years',
    'Hybrid Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Puginese',
    'Medium',
    '5.6 kg',
    '12 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    3,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Puli',
    'Large',
    '13.5 kg',
    '10 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pyredoodle',
    'Large',
    '41.6 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pyrenean Mastiff',
    'Large',
    '74.3 kg',
    '10 to 13 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Pyrenean Shepherd',
    'Large',
    '12.4 kg',
    '15 to 17 years',
    'Herding Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Rat Terrier',
    'Medium',
    '7.9 kg',
    '13 to 18 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Redbone Coonhound',
    'Large',
    '25.9 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Rhodesian Ridgeback',
    'Large',
    '34.9 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Rottador',
    'Large',
    '41.6 kg',
    '10 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Rottle',
    'Large',
    '33.8 kg',
    '9 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Rottweiler',
    'Large',
    '48.4 kg',
    '8 to 11 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Saint Berdoodle',
    'Large',
    '49.5 kg',
    '8 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Saint Bernard',
    'Large',
    '67.5 kg',
    '8 to 10 years',
    'Working Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    3,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Saluki',
    'Large',
    '23.6 kg',
    '12 to 14 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Samoyed',
    'Large',
    '24.8 kg',
    '12 to 14 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Samusky',
    'Large',
    '23.6 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Schipperke',
    'Medium',
    '6.5 kg',
    '13 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Schnoodle',
    'Large',
    '21.4 kg',
    '10 to 15 years',
    'Hybrid Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Scottish Deerhound',
    'Large',
    '41.6 kg',
    '8 to 11 years',
    'Hound Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Scottish Terrier',
    'Medium',
    '9 kg',
    '11 to 13 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Sealyham Terrier',
    'Medium',
    '10.4 kg',
    '12 to 14 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Sheepadoodle',
    'Large',
    '31.5 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shepsky',
    'Large',
    '29.9 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shetland Sheepdog',
    'Medium',
    '9 kg',
    '12 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shiba Inu',
    'Medium',
    '9 kg',
    '12 to 16 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shichon',
    'Medium',
    '5.4 kg',
    '12 to 18 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shih-Poo',
    'Medium',
    '5.9 kg',
    '13 to 17+ years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shih Tzu',
    'Medium',
    '5.6 kg',
    '10 to 16 years',
    'Companion Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    2,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shikoku',
    'Large',
    '20.3 kg',
    '10 to 12 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shiloh Shepherd',
    'Large',
    '47.3 kg',
    '9 to 14 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shiranian',
    'Small',
    '4.5 kg',
    '12 to 16 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    5,
    2,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shollie',
    'Large',
    '33.8 kg',
    '13 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Shorkie',
    'Small',
    '4.5 kg',
    '11 to 16 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Higher health risk — regular vet monitoring recommended',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Siberian Husky',
    'Large',
    '21.4 kg',
    '12 to 15 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Silken Windhound',
    'Large',
    '16.9 kg',
    '14 to 20 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Silky Terrier',
    'Small',
    '4.1 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Skye Terrier',
    'Large',
    '14.6 kg',
    '12 to 14 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Sloughi',
    'Large',
    '23.2 kg',
    '12 to 16 years',
    'Hound Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Small Munsterlander Pointer',
    'Large',
    '22.5 kg',
    '12 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Soft Coated Wheaten Terrier',
    'Large',
    '15.8 kg',
    '12 to 15 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Spanish Mastiff',
    'Large',
    '81 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    3,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Spinone Italiano',
    'Large',
    '33.1 kg',
    '10 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Springador',
    'Large',
    '31.5 kg',
    '10 to 14 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Stabyhoun',
    'Large',
    '21.4 kg',
    '13 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Staffordshire Bull Terrier',
    'Large',
    '14 kg',
    '12 to 14 years',
    'Terrier Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Staffy Bull Bullmastiff',
    'Large',
    '37.8 kg',
    '12 to 16 years',
    'Hybrid Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    1,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Standard Schnauzer',
    'Large',
    '18 kg',
    '13 to 16 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Sussex Spaniel',
    'Large',
    '18 kg',
    '11 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    2,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Swedish Lapphund',
    'Large',
    '16.9 kg',
    '12 to 14 years',
    'Herding Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Swedish Vallhund',
    'Large',
    '12.8 kg',
    '12 to 15 years',
    'Herding Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Taiwan Dog',
    'Large',
    '14.6 kg',
    '10 to 13 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Terripoo',
    'Medium',
    '5.4 kg',
    '10 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Texas Heeler',
    'Large',
    '16.9 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    4,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Thai Ridgeback',
    'Large',
    '24.8 kg',
    '10 to 13 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    3,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Tibetan Mastiff',
    'Large',
    '52.9 kg',
    '10 to 14 years',
    'Working Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Tibetan Spaniel',
    'Medium',
    '5.4 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Tibetan Terrier',
    'Medium',
    '9.9 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Toy Fox Terrier',
    'Small',
    '2.3 kg',
    '13 to 14 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Transylvanian Hound',
    'Large',
    '29.7 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Treeing Tennessee Brindle',
    'Large',
    '16.9 kg',
    '10 to 12 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Treeing Walker Coonhound',
    'Large',
    '28.1 kg',
    '12 to 13 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Valley Bulldog',
    'Large',
    '39.4 kg',
    '8 to 12 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Vizsla',
    'Large',
    '24.8 kg',
    '10 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Weimaraner',
    'Large',
    '31.5 kg',
    '11 to 13 years',
    'Sporting Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Welsh Springer Spaniel',
    'Large',
    '20.3 kg',
    '10 to 15 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Welsh Terrier',
    'Medium',
    '9 kg',
    '10 to 14 years',
    'Terrier Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'West Highland White Terrier',
    'Medium',
    '7.9 kg',
    '12 to 16 years',
    'Terrier Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Westiepoo',
    'Large',
    '12.4 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Whippet',
    'Large',
    '14.9 kg',
    '12 to 15 years',
    'Hound Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Whoodle',
    'Large',
    '14.6 kg',
    '12 to 15 years',
    'Mixed Breed Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Wirehaired Pointing Griffon',
    'Large',
    '24.8 kg',
    '10 to 14 years',
    'Sporting Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    5,
    5
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Xoloitzcuintli',
    'Large',
    '13.5 kg',
    '14 to 20 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    3,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Yakutian Laika',
    'Large',
    '21.4 kg',
    '10 to 12 years',
    'Working Dogs — loyal companion breed',
    'Generally hardy breed',
    5,
    4,
    4
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Yorkipoo',
    'Small',
    '3.8 kg',
    '10 to 15 years',
    'Hybrid Dogs — loyal companion breed',
    'Average breed health profile',
    5,
    5,
    3
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

INSERT INTO breeds (breed_name, size_category, weight_range, lifespan, temperament_notes, common_health_risks, loyalty_score, energy_score, friendliness_score) VALUES (
    'Yorkshire Terrier',
    'Small',
    '2.3 kg',
    '12 to 15 years',
    'Companion Dogs — loyal companion breed',
    'Generally hardy breed',
    4,
    5,
    2
) ON DUPLICATE KEY UPDATE
    size_category = VALUES(size_category),
    weight_range = VALUES(weight_range),
    lifespan = VALUES(lifespan),
    temperament_notes = VALUES(temperament_notes),
    common_health_risks = VALUES(common_health_risks),
    loyalty_score = VALUES(loyalty_score),
    energy_score = VALUES(energy_score),
    friendliness_score = VALUES(friendliness_score);

-- Link existing demo dogs to breeds by name (requires dog.breed_id column from setup/migrations)
UPDATE dog d
INNER JOIN breeds b ON LOWER(TRIM(d.Breed)) = LOWER(TRIM(b.breed_name))
SET d.breed_id = b.breed_id;

UPDATE dog d
INNER JOIN breeds b ON b.breed_name = 'Aspin (Asong Pinoy)'
SET d.breed_id = b.breed_id
WHERE LOWER(TRIM(d.Breed)) LIKE '%aspin%';

