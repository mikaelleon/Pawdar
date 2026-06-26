<?php

require_once __DIR__ . '/db.php';

/**
 * @return list<array<string, mixed>>
 */
function fetch_all_breeds(PDO $pdo, ?string $size = null, ?string $query = null): array
{
    $sql = 'SELECT * FROM breeds WHERE 1=1';
    $params = [];

    if ($size !== null && $size !== '' && $size !== 'all') {
        $sql .= ' AND size_category = :size';
        $params[':size'] = $size;
    }

    if ($query !== null && $query !== '') {
        $sql .= ' AND breed_name LIKE :q';
        $params[':q'] = '%' . $query . '%';
    }

    $sql .= ' ORDER BY breed_name ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/**
 * @return array<string, int>
 */
function fetch_breed_size_counts(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT size_category, COUNT(*) AS total FROM breeds GROUP BY size_category');
    $counts = ['Small' => 0, 'Medium' => 0, 'Large' => 0, 'all' => 0];

    foreach ($stmt->fetchAll() as $row) {
        $counts[$row['size_category']] = (int) $row['total'];
        $counts['all'] += (int) $row['total'];
    }

    return $counts;
}

/**
 * @return array<string, mixed>|null
 */
function fetch_breed_by_id(PDO $pdo, int $breedId): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM breeds WHERE breed_id = :id LIMIT 1');
    $stmt->execute([':id' => $breedId]);
    $breed = $stmt->fetch();

    return $breed ?: null;
}
