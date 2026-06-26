<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_login_active();

$query = trim((string) ($_GET['q'] ?? ''));
$size = trim((string) ($_GET['size'] ?? 'all'));

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

    $sql .= ' ORDER BY breed_name ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    json_response(['success' => true, 'breeds' => $stmt->fetchAll()]);
} catch (PDOException $exception) {
    json_response(['success' => false, 'breeds' => []]);
}
