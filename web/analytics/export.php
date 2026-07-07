<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/analytics.php';

require_login();
require_role(['LGU Official', 'Admin']);

$format = strtolower(trim((string) ($_GET['format'] ?? 'csv')));
if (!in_array($format, ['csv', 'pdf'], true)) {
    http_response_code(400);
    exit('Unsupported export format');
}

$pdo = db();
$barangay = (string) $_SESSION['user_barangay'];
$periodDays = 30;

$categories = analytics_category_catalog();
$categoryCounts = fetch_analytics_category_counts($pdo, $barangay, $periodDays);
$trend = fetch_analytics_incident_trend($pdo, $barangay, $periodDays);
$caseStatus = fetch_analytics_case_status_breakdown($pdo, $barangay);
$avgResolutionDays = fetch_analytics_avg_resolution_days($pdo, $barangay);
$topLocations = fetch_analytics_top_locations($pdo, $barangay, $periodDays);
$rabiesWatch = fetch_analytics_rabies_watch($pdo, $barangay);
$registryGrowth = fetch_analytics_registry_growth($pdo, $barangay, $periodDays);

$lines = [
    'Pawdar Barangay Analytics',
    'Barangay: ' . $barangay,
    'Period: Last ' . $periodDays . ' days',
    'Generated: ' . date('Y-m-d H:i'),
    '',
    'Incident categories (current period)',
];

foreach ($categories as $item) {
    $key = (string) $item['key'];
    $lines[] = $item['label'] . ': ' . (int) ($categoryCounts['current'][$key] ?? 0);
}

$lines[] = 'Total incidents: ' . (int) $categoryCounts['total'];
$lines[] = '';
$lines[] = 'Case status breakdown';

foreach ($caseStatus as $status => $count) {
    $lines[] = $status . ': ' . (int) $count;
}

$lines[] = '';
$lines[] = 'Average resolution time (days): ' . ($avgResolutionDays !== null ? (string) $avgResolutionDays : 'N/A');
$lines[] = 'Vaccination coverage: ' . $registryGrowth['coverage_pct'] . '%';
$lines[] = '';
$lines[] = 'Top locations';

foreach ($topLocations as $index => $row) {
    $lines[] = ($index + 1) . '. ' . $row['location'] . ' (' . $row['total'] . ')';
}

$lines[] = '';
$lines[] = 'Daily incident trend';

foreach ($trend as $point) {
    $lines[] = $point['label'] . ': ' . $point['total'];
}

$lines[] = '';
$lines[] = 'Active rabies watch: ' . count($rabiesWatch);

foreach ($rabiesWatch as $watch) {
    $lines[] = '- ' . $watch['dog_name'] . ' · ' . $watch['days_remaining'] . ' days remaining · ' . $watch['location'];
}

$safeName = preg_replace('/[^a-z0-9_-]+/i', '-', $barangay) ?: 'barangay';

if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="pawdar-analytics-' . $safeName . '.csv"');

    $out = fopen('php://output', 'w');
    if ($out === false) {
        exit('Could not open output stream');
    }

    foreach ($lines as $line) {
        fputcsv($out, [$line]);
    }

    fclose($out);
    exit;
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="pawdar-analytics-' . $safeName . '.pdf"');

$y = 750;
$stream = "BT /F1 11 Tf\n";
foreach ($lines as $line) {
    $safe = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
    $stream .= "50 {$y} Td ({$safe}) Tj\n0 -16 Td\n";
    $y -= 16;
    if ($y < 60) {
        break;
    }
}
$stream .= 'ET';

echo "%PDF-1.4\n";
echo "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n";
echo "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n";
echo "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>endobj\n";
echo '4 0 obj<< /Length ' . strlen($stream) . " >>stream\n" . $stream . "\nendstream endobj\n";
echo "5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n";
echo "xref\n0 6\n0000000000 65535 f \n0000000010 00000 n \n0000000060 00000 n \n0000000117 00000 n \n0000000270 00000 n \n0000000400 00000 n \n";
echo "trailer<< /Size 6 /Root 1 0 R >>\nstartxref\n450\n%%EOF";
