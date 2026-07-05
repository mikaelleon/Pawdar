<?php

/**
 * Location reference data helpers (city / barangay tables).
 */

/**
 * @return list<array{city_id: int, name: string}>
 */
function fetch_cities(PDO $pdo): array
{
    try {
        $stmt = $pdo->query('SELECT city_id, name FROM city ORDER BY name ASC');

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException) {
        return [];
    }
}

/**
 * @return list<array{barangay_id: int, name: string}>
 */
function fetch_barangays_by_city(PDO $pdo, int $cityId): array
{
    if ($cityId <= 0) {
        return [];
    }

    try {
        $stmt = $pdo->prepare(
            'SELECT barangay_id, name FROM barangay WHERE city_id = :city_id ORDER BY name ASC'
        );
        $stmt->execute([':city_id' => $cityId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException) {
        return [];
    }
}

/**
 * @return array{city_id: int, name: string, barangay_id: int, barangay_name: string}|null
 */
function fetch_location_by_barangay_id(PDO $pdo, int $barangayId): ?array
{
    if ($barangayId <= 0) {
        return null;
    }

    try {
        $stmt = $pdo->prepare('
            SELECT c.city_id, c.name AS city_name, b.barangay_id, b.name AS barangay_name
            FROM barangay b
            INNER JOIN city c ON c.city_id = b.city_id
            WHERE b.barangay_id = :barangay_id
            LIMIT 1
        ');
        $stmt->execute([':barangay_id' => $barangayId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return [
            'city_id' => (int) $row['city_id'],
            'name' => (string) $row['city_name'],
            'barangay_id' => (int) $row['barangay_id'],
            'barangay_name' => (string) $row['barangay_name'],
        ];
    } catch (PDOException) {
        return null;
    }
}

/**
 * Builds display name stored in user.Name from structured parts.
 */
function build_user_display_name(string $lastName, string $firstName, string $middleName = '', string $suffix = ''): string
{
    $name = trim($lastName) . ', ' . trim($firstName);
    $middle = trim($middleName);
    if ($middle !== '') {
        $name .= ' ' . mb_substr($middle, 0, 1) . '.';
    }

    $suffix = trim($suffix);
    if ($suffix !== '' && !in_array($suffix, ['N/A', 'None'], true)) {
        $name .= ' ' . $suffix;
    }

    return $name;
}

/**
 * Roles where a contact number is required at signup.
 *
 * @return list<string>
 */
function roles_requiring_phone(): array
{
    return ['Dog Owner', 'Veterinarian', 'LGU Official', 'Rescue Organization'];
}
