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

/**
 * Returns dogs for the registry list, scoped by role and barangay.
 *
 * @return list<array<string, mixed>>
 */
function fetch_registry_dogs(PDO $pdo, string $role, int $userId, string $barangay, ?string $query = null): array
{
    $sql = '
        SELECT d.dog_id, d.DogName, d.Breed, d.RegistryID, d.Status, d.DogType, d.Gender,
               u.Name AS owner_name, u.Barangay AS owner_barangay,
               COALESCE(b.breed_name, d.Breed) AS breed_label
        FROM dog d
        INNER JOIN user u ON u.UserID = d.UserID
        LEFT JOIN breeds b ON b.breed_id = d.breed_id
        WHERE 1=1
    ';
    $params = [];

    if ($role === 'Dog Owner') {
        $sql .= ' AND d.UserID = :user_id';
        $params[':user_id'] = $userId;
    } else {
        $sql .= ' AND u.Barangay = :barangay';
        $params[':barangay'] = $barangay;
    }

    if ($query !== null && $query !== '') {
        $sql .= ' AND (
            d.DogName LIKE :q OR d.Breed LIKE :q OR d.RegistryID LIKE :q
            OR u.Name LIKE :q OR b.breed_name LIKE :q
        )';
        $params[':q'] = '%' . $query . '%';
    }

    $sql .= ' ORDER BY d.DogName ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/**
 * Returns registry summary counts for the current scope.
 *
 * @return array{total: int, registered: int, pending: int}
 */
function fetch_registry_counts(PDO $pdo, string $role, int $userId, string $barangay): array
{
    $sql = '
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN d.Status = \'Registered\' THEN 1 ELSE 0 END) AS registered,
            SUM(CASE WHEN d.Status = \'Pending\' THEN 1 ELSE 0 END) AS pending
        FROM dog d
        INNER JOIN user u ON u.UserID = d.UserID
        WHERE 1=1
    ';
    $params = [];

    if ($role === 'Dog Owner') {
        $sql .= ' AND d.UserID = :user_id';
        $params[':user_id'] = $userId;
    } else {
        $sql .= ' AND u.Barangay = :barangay';
        $params[':barangay'] = $barangay;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch() ?: [];

    return [
        'total' => (int) ($row['total'] ?? 0),
        'registered' => (int) ($row['registered'] ?? 0),
        'pending' => (int) ($row['pending'] ?? 0),
    ];
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_pending_users(PDO $pdo): array
{
    $stmt = $pdo->query('
        SELECT UserID, Name, Email, Role, Barangay, Phone, created_at
        FROM user
        WHERE Status = \'pending\'
        ORDER BY created_at ASC
    ');

    return $stmt->fetchAll();
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_pending_dogs(PDO $pdo): array
{
    $stmt = $pdo->query('
        SELECT d.dog_id, d.DogName, d.Breed, d.RegistryID, d.Status, d.DogType,
               u.Name AS owner_name, u.Barangay AS owner_barangay
        FROM dog d
        INNER JOIN user u ON u.UserID = d.UserID
        WHERE d.Status = \'Pending\'
        ORDER BY d.dog_id ASC
    ');

    return $stmt->fetchAll();
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_rescue_stray_incidents(PDO $pdo, string $barangay, int $limit = 20): array
{
    $stmt = $pdo->prepare('
        SELECT i.IncidentID, i.IncidentType, i.Date, i.Location, i.Description,
               c.CaseStatus, u.Name AS reporter_name
        FROM incident i
        INNER JOIN user u ON u.UserID = i.UserID
        LEFT JOIN `case` c ON c.IncidentID = i.IncidentID
        WHERE i.IncidentType = \'Injured Stray\'
          AND u.Barangay = :barangay
        ORDER BY i.Date DESC
        LIMIT :limit
    ');
    $stmt->bindValue(':barangay', $barangay);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}
