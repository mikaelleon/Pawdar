<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Fetches a dog profile with owner and vaccination data.
 *
 * @return array<string, mixed>|null
 */
function fetch_dog_profile(PDO $pdo, int $dogId): ?array
{
    $stmt = $pdo->prepare('
        SELECT d.*, u.Name AS owner_name, u.Phone AS owner_phone, u.Barangay AS owner_barangay, u.Role AS owner_role, u.UserID AS owner_id
        FROM dog d
        INNER JOIN user u ON u.UserID = d.UserID
        WHERE d.dog_id = :dog_id
        LIMIT 1
    ');
    $stmt->execute([':dog_id' => $dogId]);
    $dog = $stmt->fetch();

    if (!$dog) {
        return null;
    }

    $vax = $pdo->prepare('SELECT * FROM vaccinerecord WHERE dog_id = :dog_id ORDER BY DateGiven DESC LIMIT 1');
    $vax->execute([':dog_id' => $dogId]);
    $dog['vaccine'] = $vax->fetch() ?: null;

    $incidents = $pdo->prepare('
        SELECT i.IncidentType, i.Date, i.Description, c.CaseStatus
        FROM incident i
        LEFT JOIN `case` c ON c.IncidentID = i.IncidentID
        WHERE i.dog_id = :dog_id
        ORDER BY i.Date DESC
        LIMIT 10
    ');
    $incidents->execute([':dog_id' => $dogId]);
    $dog['incidents'] = $incidents->fetchAll();

    return $dog;
}

/**
 * Fetches breed info by name.
 *
 * @return array<string, mixed>|null
 */
function fetch_breed_by_name(PDO $pdo, string $breedName): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM breeds WHERE breed_name = :name LIMIT 1');
    $stmt->execute([':name' => $breedName]);

    return $stmt->fetch() ?: null;
}

/**
 * Returns dogs registered under a breed by breed_id.
 *
 * @return list<array<string, mixed>>
 */
function fetch_dogs_by_breed_id(PDO $pdo, int $breedId): array
{
    $stmt = $pdo->prepare('
        SELECT d.dog_id, d.DogName, d.DogType, d.Status, d.RegistryID, u.Name AS owner_name
        FROM dog d
        INNER JOIN user u ON u.UserID = d.UserID
        WHERE d.breed_id = :breed_id
        ORDER BY d.DogName ASC
        LIMIT 20
    ');
    $stmt->execute([':breed_id' => $breedId]);

    return $stmt->fetchAll();
}

/**
 * Returns dogs registered under a breed by name (legacy fallback).
 *
 * @return list<array<string, mixed>>
 */
function fetch_dogs_by_breed(PDO $pdo, string $breedName): array
{
    $stmt = $pdo->prepare('
        SELECT d.dog_id, d.DogName, d.DogType, d.Status, d.RegistryID, u.Name AS owner_name
        FROM dog d
        INNER JOIN user u ON u.UserID = d.UserID
        LEFT JOIN breeds b ON b.breed_id = d.breed_id
        WHERE b.breed_name = :breed OR d.Breed = :breed_text
        ORDER BY d.DogName ASC
        LIMIT 20
    ');
    $stmt->execute([
        ':breed' => $breedName,
        ':breed_text' => $breedName,
    ]);

    return $stmt->fetchAll();
}
