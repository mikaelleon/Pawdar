<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/incidents.php';
require_once __DIR__ . '/../includes/geocoding.php';

require_login();

if (!role_can_report(current_user_role())) {
    json_response(['success' => false, 'message' => 'Not authorized'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid method'], 405);
}

if (!validate_csrf(request_csrf_token())) {
    json_response(['success' => false, 'message' => 'Invalid CSRF token'], 403);
}

$incidentType = trim((string) ($_POST['incident_type'] ?? ''));
$location = trim((string) ($_POST['location'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
$dogId = (int) ($_POST['dog_id'] ?? 0);
$observedBreed = trim((string) ($_POST['observed_breed'] ?? ''));
$observedCoat = trim((string) ($_POST['observed_coat_color'] ?? ''));
$observedCoatOther = trim((string) ($_POST['observed_coat_color_other'] ?? ''));
$observedSize = trim((string) ($_POST['observed_dog_size'] ?? ''));
$observedMarks = trim((string) ($_POST['observed_marks'] ?? ''));
$userId = (int) $_SESSION['user_id'];
$barangay = (string) $_SESSION['user_barangay'];
$latitude = trim((string) ($_POST['latitude'] ?? ''));
$longitude = trim((string) ($_POST['longitude'] ?? ''));

$allowedTypes = array_keys(incident_type_map());
if (!in_array($incidentType, $allowedTypes, true) || $location === '') {
    json_response(['success' => false, 'message' => 'Missing required fields'], 400);
}

$coords = null;
if ($latitude !== '' && $longitude !== '' && is_numeric($latitude) && is_numeric($longitude)) {
    $coords = ['lat' => (float) $latitude, 'lng' => (float) $longitude];
} else {
    $coords = parse_coordinates_from_text($location);
}

$barangaySuffix = extract_barangay_suffix($location);
if ($barangaySuffix === '' && stripos($location, $barangay) === false) {
    $barangaySuffix = ', Brgy. ' . $barangay;
}

if ($coords !== null && (location_is_coordinate_string($location) || $latitude !== '')) {
    $readable = reverse_geocode_label($coords['lat'], $coords['lng']);
    if ($readable !== null) {
        $location = $readable . $barangaySuffix;
    } elseif (stripos($location, 'Brgy.') === false) {
        $location = $location . $barangaySuffix;
    }
} elseif (stripos($location, $barangay) === false) {
    $location = $location . $barangaySuffix;
}

$pdo = db();

$photoPath = null;
if (isset($_FILES['photo']) && is_array($_FILES['photo']) && (int) $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['photo'];
    $maxSize = 5 * 1024 * 1024;

    if ((int) $file['size'] > $maxSize) {
        json_response(['success' => false, 'message' => 'Photo must be 5MB or less'], 400);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowedMimes[$mime])) {
        json_response(['success' => false, 'message' => 'Invalid photo type'], 400);
    }

    $uploadDir = dirname(__DIR__) . '/uploads/incidents';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowedMimes[$mime];
    $destination = $uploadDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        json_response(['success' => false, 'message' => 'Failed to save photo'], 500);
    }

    $photoPath = 'uploads/incidents/' . $filename;
}

if ($description === '' && $photoPath !== null) {
    $description = 'Photo attached.';
}

if ($dogId <= 0) {
    $coatColor = $observedCoat === 'Other' && $observedCoatOther !== ''
        ? $observedCoatOther
        : $observedCoat;
    $sizeLabel = $observedSize === 'Unknown' ? '' : $observedSize;
    $description = compose_observed_dog_description(
        $observedBreed,
        $coatColor,
        $sizeLabel,
        $observedMarks,
        $description
    );
}

$insert = $pdo->prepare('
    INSERT INTO incident (UserID, dog_id, IncidentType, Date, Location, Description, photo_path, latitude, longitude)
    VALUES (:user_id, :dog_id, :incident_type, NOW(), :location, :description, :photo_path, :latitude, :longitude)
');
$insert->execute([
    ':user_id' => $userId,
    ':dog_id' => $dogId > 0 ? $dogId : null,
    ':incident_type' => $incidentType,
    ':location' => $location,
    ':description' => $description !== '' ? $description : null,
    ':photo_path' => $photoPath,
    ':latitude' => $coords['lat'] ?? null,
    ':longitude' => $coords['lng'] ?? null,
]);

$incidentId = (int) $pdo->lastInsertId();
$title = generate_incident_title($incidentType, $location, $coords['lat'] ?? null, $coords['lng'] ?? null);

$caseInsert = $pdo->prepare('INSERT INTO `case` (IncidentID, CaseStatus) VALUES (:incident_id, \'Received\')');
$caseInsert->execute([':incident_id' => $incidentId]);

notify_barangay_of_incident($pdo, $incidentId, $barangay, $userId, $title);

if ($dogId > 0) {
    $dogStmt = $pdo->prepare('SELECT Breed FROM dog WHERE dog_id = :dog_id LIMIT 1');
    $dogStmt->execute([':dog_id' => $dogId]);
    $dog = $dogStmt->fetch();
    if ($dog) {
        notify_matching_dog_owners($pdo, $incidentId, (string) ($dog['Breed'] ?? ''), $barangay);
    }
}

json_response([
    'success' => true,
    'incident_id' => $incidentId,
    'message' => 'Incident reported successfully',
]);
