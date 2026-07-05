-- Pawdar v5: normalized city/barangay reference tables (Batangas Province)
-- Seed via: php sql/import-barangays.php

CREATE TABLE IF NOT EXISTS city (
    city_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    UNIQUE KEY uniq_city_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS barangay (
    barangay_id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    UNIQUE KEY uniq_city_barangay (city_id, name),
    CONSTRAINT fk_barangay_city FOREIGN KEY (city_id) REFERENCES city(city_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
