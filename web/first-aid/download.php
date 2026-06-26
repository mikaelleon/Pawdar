<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/first-aid-data.php';
require_login_active();

$guideId = (int) ($_GET['id'] ?? 0);
$pdo = db();
$guide = fetch_first_aid_guide($pdo, $guideId);

if (!$guide) {
    http_response_code(404);
    exit('Guide not found');
}

$filename = preg_replace('/[^a-z0-9_-]+/i', '-', (string) $guide['title']) . '.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$lines = [
    'Pawdar First Aid Guide',
    (string) $guide['title'],
    '',
    'Warning: ' . (string) $guide['warning_text'],
    '',
];

foreach ($guide['steps'] as $index => $step) {
    $lines[] = ($index + 1) . '. ' . $step;
}

$lines[] = '';
$lines[] = 'Source: ' . (string) $guide['source_citation'];

$y = 750;
$stream = "BT /F1 11 Tf\n";
foreach ($lines as $line) {
    $safe = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
    $stream .= "50 {$y} Td ({$safe}) Tj\n0 -16 Td\n";
    $y -= 16;
}
$stream .= "ET";

echo "%PDF-1.4\n";
echo "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n";
echo "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n";
echo "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>endobj\n";
echo "4 0 obj<< /Length " . strlen($stream) . " >>stream\n" . $stream . "\nendstream endobj\n";
echo "5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n";
echo "xref\n0 6\n0000000000 65535 f \n0000000010 00000 n \n0000000060 00000 n \n0000000117 00000 n \n0000000270 00000 n \n0000000400 00000 n \n";
echo "trailer<< /Size 6 /Root 1 0 R >>\nstartxref\n450\n%%EOF";
