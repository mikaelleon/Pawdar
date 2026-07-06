<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/breed-content.php';

const BREED_DIRECTORY_PER_PAGE = 12;

/**
 * @return array<string, mixed>|null
 */
function fetch_breed_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT b.*, (SELECT COUNT(*) FROM dog d WHERE d.breed_id = b.breed_id) AS registered_count FROM breeds b WHERE b.slug = :slug LIMIT 1');
    $stmt->execute([':slug' => $slug]);
    $breed = $stmt->fetch();

    return $breed ?: null;
}

/**
 * @return array<string, mixed>|null
 */
function fetch_breed_by_id(PDO $pdo, int $breedId): ?array
{
    $stmt = $pdo->prepare('SELECT b.*, (SELECT COUNT(*) FROM dog d WHERE d.breed_id = b.breed_id) AS registered_count FROM breeds b WHERE b.breed_id = :id LIMIT 1');
    $stmt->execute([':id' => $breedId]);
    $breed = $stmt->fetch();

    return $breed ?: null;
}

/**
 * @param array<string, string> $filters
 * @return array{rows: list<array<string, mixed>>, total: int, page: int, per_page: int, total_pages: int}
 */
function fetch_breeds_directory(PDO $pdo, array $filters): array
{
    $page = max(1, (int) ($filters['page'] ?? 1));
    $perPage = BREED_DIRECTORY_PER_PAGE;
    $offset = ($page - 1) * $perPage;

    $sqlFrom = ' FROM breeds b LEFT JOIN dog d ON d.breed_id = b.breed_id WHERE 1=1';
    $params = [];
    $groupBy = ' GROUP BY b.breed_id';

    $size = trim((string) ($filters['size'] ?? 'all'));
    if ($size !== '' && $size !== 'all') {
        $sqlFrom .= ' AND b.size_category = :size';
        $params[':size'] = $size;
    }

    if (($filters['local'] ?? '') === '1') {
        $sqlFrom .= ' AND (b.is_local_breed = 1 OR b.breed_name IN (\'Aspin (Asong Pinoy)\', \'Aspin\', \'Asong Pinoy\', \'Mixed Breed\'))';
    }

    $mood = trim((string) ($filters['mood'] ?? ''));
    foreach (breed_mood_filters() as $moodFilter) {
        if ($moodFilter['slug'] === $mood) {
            $sqlFrom .= ' AND ' . $moodFilter['sql'];
            break;
        }
    }

    $query = trim((string) ($filters['q'] ?? ''));
    if ($query !== '') {
        $sqlFrom .= ' AND (b.breed_name LIKE :q OR b.breed_name LIKE :q_start OR b.slug LIKE :q_slug)';
        $params[':q'] = '%' . $query . '%';
        $params[':q_start'] = $query . '%';
        $params[':q_slug'] = '%' . breed_slug_from_name($query) . '%';
    }

    $countStmt = $pdo->prepare('SELECT COUNT(DISTINCT b.breed_id)' . $sqlFrom);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();
    $totalPages = max(1, (int) ceil($total / $perPage));
    if ($page > $totalPages) {
        $page = $totalPages;
        $offset = ($page - 1) * $perPage;
    }

    $sort = trim((string) ($filters['sort'] ?? 'name_asc'));
    $orderBy = match ($sort) {
        'name_desc' => 'b.breed_name DESC',
        'registered' => 'registered_count DESC, b.breed_name ASC',
        default => 'b.breed_name ASC',
    };

    $select = 'SELECT b.*, COUNT(d.dog_id) AS registered_count';
    $sql = $select . $sqlFrom . $groupBy . ' ORDER BY ' . $orderBy . ' LIMIT :limit OFFSET :offset';

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return [
        'rows' => $stmt->fetchAll(),
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => $totalPages,
    ];
}

/**
 * @return array<string, int>
 */
function fetch_breed_size_counts(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT size_category, COUNT(*) AS total FROM breeds GROUP BY size_category');
    $counts = ['Small' => 0, 'Medium' => 0, 'Large' => 0, 'all' => 0, 'local' => 0];

    foreach ($stmt->fetchAll() as $row) {
        $counts[$row['size_category']] = (int) $row['total'];
        $counts['all'] += (int) $row['total'];
    }

    $counts['local'] = (int) $pdo->query(
        'SELECT COUNT(*) FROM breeds WHERE is_local_breed = 1 OR breed_name IN (\'Aspin (Asong Pinoy)\', \'Aspin\', \'Asong Pinoy\', \'Mixed Breed\')'
    )->fetchColumn();

    return $counts;
}

/**
 * @param list<int> $breedIds
 * @return list<array<string, mixed>>
 */
function fetch_breeds_by_ids(PDO $pdo, array $breedIds): array
{
    $ids = array_values(array_filter(array_map('intval', $breedIds), static fn (int $id): bool => $id > 0));
    if ($ids === []) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare(
        'SELECT b.*, (SELECT COUNT(*) FROM dog d WHERE d.breed_id = b.breed_id) AS registered_count FROM breeds b WHERE b.breed_id IN (' . $placeholders . ') ORDER BY b.breed_name ASC'
    );
    $stmt->execute($ids);

    return $stmt->fetchAll();
}

/**
 * Builds breeds listing return URL preserving filters.
 *
 * @param array<string, string|int> $params
 */
function breeds_directory_url(array $params = []): string
{
    $defaults = ['size' => 'all', 'mood' => '', 'local' => '', 'q' => '', 'sort' => 'name_asc', 'page' => 1];
    $merged = array_merge($defaults, $params);
    $query = [];

    foreach ($merged as $key => $value) {
        $value = is_string($value) ? trim($value) : $value;
        if ($key === 'page' && (int) $value <= 1) {
            continue;
        }
        if ($value === '' || $value === 'all' || $value === 'name_asc') {
            continue;
        }
        $query[$key] = $value;
    }

    $qs = $query !== [] ? '?' . http_build_query($query) : '';

    return 'breeds.php' . $qs;
}

/**
 * @return list<string>
 */
function breed_gallery_urls(array $breed): array
{
    $urls = [];
    if (!empty($breed['image_url'])) {
        $urls[] = (string) $breed['image_url'];
    }

    if (!empty($breed['gallery_urls'])) {
        $decoded = json_decode((string) $breed['gallery_urls'], true);
        if (is_array($decoded)) {
            foreach ($decoded as $url) {
                if (is_string($url) && $url !== '' && !in_array($url, $urls, true)) {
                    $urls[] = $url;
                }
            }
        }
    }

    return $urls;
}

/**
 * @return list<array<string, mixed>>
 */
function fetch_all_breeds(PDO $pdo, ?string $size = null, ?string $query = null): array
{
    $result = fetch_breeds_directory($pdo, [
        'size' => $size ?? 'all',
        'q' => $query ?? '',
        'page' => 1,
        'sort' => 'name_asc',
    ]);

    return $result['rows'];
}
