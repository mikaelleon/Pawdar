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
        INNER JOIN `user` u ON u.UserID = d.UserID
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
        SELECT d.dog_id, d.DogName, d.DogType, d.Status, d.RegistryID, d.photo_path, u.Name AS owner_name
        FROM dog d
        INNER JOIN `user` u ON u.UserID = d.UserID
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
        INNER JOIN `user` u ON u.UserID = d.UserID
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
        INNER JOIN `user` u ON u.UserID = d.UserID
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
        INNER JOIN `user` u ON u.UserID = d.UserID
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
        FROM `user`
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
        INNER JOIN `user` u ON u.UserID = d.UserID
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
        INNER JOIN `user` u ON u.UserID = i.UserID
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

/**
 * Paginated registry listing with filters.
 *
 * @param array<string, string> $filters
 * @return array{rows: list<array<string, mixed>>, total: int}
 */
function fetch_registry_list(PDO $pdo, array $filters, int $offset = 0, int $limit = 20): array
{
    $sql = '
        FROM dog d
        INNER JOIN `user` u ON u.UserID = d.UserID
        LEFT JOIN breeds b ON b.breed_id = d.breed_id
        LEFT JOIN vaccinerecord v ON v.dog_id = d.dog_id AND v.VaccineID = (
            SELECT VaccineID FROM vaccinerecord WHERE dog_id = d.dog_id ORDER BY DateGiven DESC LIMIT 1
        )
        WHERE 1=1
    ';
    $params = [];

    $query = trim((string) ($filters['q'] ?? ''));
    if ($query !== '') {
        $sql .= ' AND (d.DogName LIKE :q OR d.Breed LIKE :q OR d.RegistryID LIKE :q OR u.Name LIKE :q OR b.breed_name LIKE :q)';
        $params[':q'] = '%' . $query . '%';
    }

    $type = trim((string) ($filters['type'] ?? 'all'));
    if ($type !== '' && $type !== 'all') {
        $sql .= ' AND d.DogType = :dog_type';
        $params[':dog_type'] = $type;
    }

    $barangay = trim((string) ($filters['barangay'] ?? 'all'));
    if ($barangay !== '' && $barangay !== 'all') {
        $sql .= ' AND u.Barangay = :barangay';
        $params[':barangay'] = $barangay;
    }

    $breed = trim((string) ($filters['breed'] ?? 'all'));
    if ($breed !== '' && $breed !== 'all') {
        $sql .= ' AND (b.breed_name = :breed OR d.Breed = :breed_text)';
        $params[':breed'] = $breed;
        $params[':breed_text'] = $breed;
    }

    $vax = trim((string) ($filters['vaccine'] ?? 'all'));
    if ($vax === 'Verified') {
        $sql .= ' AND v.vax_status = \'Verified\'';
    } elseif ($vax === 'Unverified') {
        $sql .= ' AND (v.vax_status IS NULL OR v.vax_status = \'Unverified\')';
    } elseif ($vax === 'Expired') {
        $sql .= ' AND v.vax_status = \'Expired\'';
    }

    $countStmt = $pdo->prepare('SELECT COUNT(DISTINCT d.dog_id) ' . $sql);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $select = '
        SELECT d.dog_id, d.DogName, d.Breed, d.RegistryID, d.Status, d.DogType, d.Gender, d.Age,
               u.Name AS owner_name, u.Barangay AS owner_barangay, u.UserID AS owner_id,
               COALESCE(b.breed_name, d.Breed) AS breed_label,
               v.vax_status AS vaccine_status, v.VaccineName, v.DateGiven, v.NextDueDate,
               (SELECT COUNT(*) FROM incident i WHERE i.dog_id = d.dog_id) AS incident_count
    ';
    $sql .= ' ORDER BY d.dog_id DESC LIMIT :limit OFFSET :offset';

    $stmt = $pdo->prepare($select . $sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return ['rows' => $stmt->fetchAll(), 'total' => $total];
}

/**
 * Registry summary counts for the summary strip.
 *
 * @return array{total: int, owned: int, stray: int, rescued: int, vax_verified: int}
 */
function fetch_registry_summary(PDO $pdo): array
{
    $row = $pdo->query('
        SELECT
            COUNT(*) AS total,
            SUM(DogType = \'Owned\') AS owned,
            SUM(DogType = \'Stray\') AS stray,
            SUM(DogType = \'Rescued\') AS rescued
        FROM dog
    ')->fetch() ?: [];

    $vaxVerified = (int) $pdo->query('
        SELECT COUNT(DISTINCT d.dog_id)
        FROM dog d
        INNER JOIN vaccinerecord v ON v.dog_id = d.dog_id
        WHERE v.vax_status = \'Verified\'
    ')->fetchColumn();

    return [
        'total' => (int) ($row['total'] ?? 0),
        'owned' => (int) ($row['owned'] ?? 0),
        'stray' => (int) ($row['stray'] ?? 0),
        'rescued' => (int) ($row['rescued'] ?? 0),
        'vax_verified' => $vaxVerified,
    ];
}

/**
 * Splits registry rows into featured (newest on first page) and regular cards.
 *
 * @param list<array<string, mixed>> $rows
 * @return array{featured: array<string, mixed>|null, regular: list<array<string, mixed>>}
 */
function split_registry_bento_rows(array $rows, int $offset): array
{
    if ($offset !== 0 || count($rows) === 0) {
        return ['featured' => null, 'regular' => $rows];
    }

    $featuredId = (int) max(array_column($rows, 'dog_id'));
    $featured = null;
    $regular = [];

    foreach ($rows as $dog) {
        if ($featured === null && (int) $dog['dog_id'] === $featuredId) {
            $featured = $dog;
        } else {
            $regular[] = $dog;
        }
    }

    return ['featured' => $featured, 'regular' => $regular];
}

/**
 * CSS classes for a registry bento card.
 *
 * @param array<string, mixed> $dog
 */
function registry_bento_card_classes(array $dog, ?array $featured): string
{
    $classes = ['dog-card'];

    if ($featured !== null && (int) $dog['dog_id'] === (int) $featured['dog_id']) {
        $classes[] = 'dog-card--featured';

        return implode(' ', $classes);
    }

    if (($dog['DogType'] ?? '') === 'Stray' && (int) ($dog['incident_count'] ?? 0) >= 3) {
        $classes[] = 'dog-card--wide';
    }

    return implode(' ', $classes);
}

/**
 * Vaccination badge meta for bento cards.
 *
 * @return array{text: string, class: string}
 */
function registry_vax_badge(?string $status): array
{
    if ($status === 'Verified') {
        return ['text' => 'Verified', 'class' => 'vax--verified'];
    }
    if ($status === 'Expired') {
        return ['text' => 'Expired', 'class' => 'vax--expired'];
    }
    if ($status === 'Unverified') {
        return ['text' => 'Unverified', 'class' => 'vax--unverified'];
    }

    return ['text' => 'No record', 'class' => 'vax--none'];
}

/**
 * Avatar background color for registry bento cards.
 */
function registry_avatar_color(string $dogName): string
{
    $colors = ['#C0DAB5', '#87AFAE', '#6C8B9F', '#F8BC72', '#E0765E', '#b5c4c0'];

    return $colors[abs(crc32($dogName)) % count($colors)];
}

/**
 * @return list<string>
 */
function fetch_registry_barangays(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT DISTINCT Barangay FROM `user` ORDER BY Barangay ASC');

    return array_column($stmt->fetchAll(), 'Barangay');
}

/**
 * @return array<string, mixed>|null
 */
function fetch_dog_by_registry_id(PDO $pdo, string $registryId): ?array
{
    $stmt = $pdo->prepare('
        SELECT d.*, u.Name AS owner_name, u.Phone AS owner_phone, u.Barangay AS owner_barangay,
               v.VaccineName, v.DateGiven, v.NextDueDate, v.vax_status
        FROM dog d
        LEFT JOIN `user` u ON u.UserID = d.UserID
        LEFT JOIN vaccinerecord v ON v.dog_id = d.dog_id AND v.VaccineID = (
            SELECT VaccineID FROM vaccinerecord WHERE dog_id = d.dog_id ORDER BY DateGiven DESC LIMIT 1
        )
        WHERE d.RegistryID = :registry_id OR d.dog_id = :dog_id_num
        LIMIT 1
    ');
    $stmt->execute([
        ':registry_id' => $registryId,
        ':dog_id_num' => ctype_digit($registryId) ? (int) $registryId : 0,
    ]);
    $row = $stmt->fetch();

    return $row ?: null;
}

/**
 * @return array{class: string, label: string}
 */
function vaccine_status_badge(?string $status): array
{
    if ($status === 'Verified') {
        return ['class' => 'badge-verified', 'label' => 'Verified'];
    }
    if ($status === 'Expired') {
        return ['class' => 'badge-bite', 'label' => 'Expired'];
    }

    return ['class' => 'badge-received', 'label' => 'Unverified'];
}

/**
 * Returns dog type chip class.
 */
function dog_type_chip_class(?string $dogType): string
{
    if ($dogType === 'Stray') {
        return 'badge-investigating';
    }
    if ($dogType === 'Rescued') {
        return 'badge-owned';
    }

    return 'badge-verified';
}
