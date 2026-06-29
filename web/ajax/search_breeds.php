<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_login_active();

$query = trim((string) ($_GET['q'] ?? ''));
$size = trim((string) ($_GET['size'] ?? 'all'));

if ($query !== '' && mb_strlen($query) < 2 && $size === 'all') {
    json_response(['success' => true, 'breeds' => []]);
}

try {
    $pdo = db();
    $sql = 'SELECT breed_id, breed_name, size_category, temperament_notes FROM breeds WHERE 1=1';
    $params = [];

    if ($query !== '') {
        $sql .= ' AND breed_name LIKE :q';
        $params[':q'] = '%' . $query . '%';
    }

    if ($size !== '' && $size !== 'all') {
        $sql .= ' AND size_category = :size';
        $params[':size'] = $size;
    }

    if ($query !== '') {
        $sql .= ' ORDER BY (CASE WHEN breed_name LIKE :exact THEN 0 ELSE 1 END), breed_name ASC LIMIT 8';
        $params[':exact'] = $query . '%';
    } else {
        $sql .= ' ORDER BY breed_name ASC';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    json_response(['success' => true, 'breeds' => $stmt->fetchAll()]);
} catch (PDOException $exception) {
    json_response(['success' => false, 'breeds' => []]);
}
