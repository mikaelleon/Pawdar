<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/incidents.php';

/**
 * Incident category keys used across analytics charts and KPI cards.
 *
 * @return list<array{key: string, label: string, color: string, class: string}>
 */
function analytics_category_catalog(): array
{
    return [
        ['key' => 'bites', 'label' => 'Animal Bites', 'color' => '#E0765E', 'class' => 'analytics-kpi--bites'],
        ['key' => 'strays', 'label' => 'Injured Strays', 'color' => '#F8BC72', 'class' => 'analytics-kpi--strays'],
        ['key' => 'aggressive', 'label' => 'Aggressive Reports', 'color' => '#6C8B9F', 'class' => 'analytics-kpi--aggressive'],
        ['key' => 'vehicular', 'label' => 'Vehicular', 'color' => '#87AFAE', 'class' => 'analytics-kpi--vehicular'],
        ['key' => 'disturbance', 'label' => 'Disturbance', 'color' => '#4A4343', 'class' => 'analytics-kpi--disturbance'],
    ];
}

/**
 * Returns category counts for current and previous periods.
 *
 * @return array{
 *     current: array<string, int>,
 *     previous: array<string, int>,
 *     total: int,
 *     previous_total: int
 * }
 */
function fetch_analytics_category_counts(PDO $pdo, string $barangay, int $days = 30): array
{
    $sql = '
        SELECT
            SUM(CASE WHEN i.IncidentType = \'Animal Bite\' THEN 1 ELSE 0 END) AS bites,
            SUM(CASE WHEN i.IncidentType = \'Injured Stray\' THEN 1 ELSE 0 END) AS strays,
            SUM(CASE WHEN i.IncidentType = \'Aggressive Behavior\' THEN 1 ELSE 0 END) AS aggressive,
            SUM(CASE WHEN i.IncidentType = \'Vehicular Accident\' THEN 1 ELSE 0 END) AS vehicular,
            SUM(CASE WHEN i.IncidentType IN (\'Disturbance\', \'Trash Disturbance\') THEN 1 ELSE 0 END) AS disturbance
        FROM incident i
        WHERE i.Location LIKE :barangay
          AND i.Date >= :start_date
          AND i.Date < :end_date
    ';

    $currentStart = date('Y-m-d 00:00:00', strtotime('-' . $days . ' days'));
    $currentEnd = date('Y-m-d 23:59:59', strtotime('+1 day'));
    $previousStart = date('Y-m-d 00:00:00', strtotime('-' . ($days * 2) . ' days'));
    $previousEnd = $currentStart;

    $run = static function (PDO $pdo, string $sql, string $barangay, string $start, string $end): array {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':barangay' => '%' . $barangay . '%',
            ':start_date' => $start,
            ':end_date' => $end,
        ]);
        $row = $stmt->fetch() ?: [];

        return [
            'bites' => (int) ($row['bites'] ?? 0),
            'strays' => (int) ($row['strays'] ?? 0),
            'aggressive' => (int) ($row['aggressive'] ?? 0),
            'vehicular' => (int) ($row['vehicular'] ?? 0),
            'disturbance' => (int) ($row['disturbance'] ?? 0),
        ];
    };

    $current = $run($pdo, $sql, $barangay, $currentStart, $currentEnd);
    $previous = $run($pdo, $sql, $barangay, $previousStart, $previousEnd);

    return [
        'current' => $current,
        'previous' => $previous,
        'total' => array_sum($current),
        'previous_total' => array_sum($previous),
    ];
}

/**
 * Daily incident totals for trend chart.
 *
 * @return list<array{label: string, total: int}>
 */
function fetch_analytics_incident_trend(PDO $pdo, string $barangay, int $days = 30): array
{
    $stmt = $pdo->prepare('
        SELECT DATE(i.Date) AS day_key, COUNT(*) AS total
        FROM incident i
        WHERE i.Location LIKE :barangay
          AND i.Date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
        GROUP BY DATE(i.Date)
        ORDER BY day_key ASC
    ');
    $stmt->bindValue(':barangay', '%' . $barangay . '%');
    $stmt->bindValue(':days', $days, PDO::PARAM_INT);
    $stmt->execute();

    $byDay = [];
    foreach ($stmt->fetchAll() as $row) {
        $byDay[(string) $row['day_key']] = (int) $row['total'];
    }

    $series = [];
    for ($offset = $days - 1; $offset >= 0; $offset--) {
        $dayKey = date('Y-m-d', strtotime('-' . $offset . ' days'));
        $series[] = [
            'label' => date('M j', strtotime($dayKey)),
            'total' => $byDay[$dayKey] ?? 0,
        ];
    }

    return $series;
}

/**
 * Case status counts for bar chart.
 *
 * @return array<string, int>
 */
function fetch_analytics_case_status_breakdown(PDO $pdo, string $barangay): array
{
    $stmt = $pdo->prepare('
        SELECT c.CaseStatus, COUNT(*) AS total
        FROM `case` c
        INNER JOIN incident i ON i.IncidentID = c.IncidentID
        WHERE i.Location LIKE :barangay
        GROUP BY c.CaseStatus
    ');
    $stmt->execute([':barangay' => '%' . $barangay . '%']);

    $defaults = [
        'Received' => 0,
        'Under Investigation' => 0,
        'Action Taken' => 0,
        'Resolved' => 0,
        'Referred' => 0,
    ];

    foreach ($stmt->fetchAll() as $row) {
        $status = (string) $row['CaseStatus'];
        if (array_key_exists($status, $defaults)) {
            $defaults[$status] = (int) $row['total'];
        }
    }

    return $defaults;
}

/**
 * Average days from Received to Resolved for resolved cases.
 */
function fetch_analytics_avg_resolution_days(PDO $pdo, string $barangay): ?float
{
    $stmt = $pdo->prepare('
        SELECT AVG(TIMESTAMPDIFF(HOUR, received_at, resolved_at)) / 24 AS avg_days
        FROM (
            SELECT c.CaseID,
                   COALESCE(
                       (SELECT MIN(ch.created_at)
                        FROM case_history ch
                        WHERE ch.CaseID = c.CaseID AND ch.CaseStatus = \'Received\'),
                       i.Date
                   ) AS received_at,
                   (SELECT MIN(ch.created_at)
                    FROM case_history ch
                    WHERE ch.CaseID = c.CaseID AND ch.CaseStatus = \'Resolved\') AS resolved_at
            FROM `case` c
            INNER JOIN incident i ON i.IncidentID = c.IncidentID
            WHERE i.Location LIKE :barangay
              AND c.CaseStatus = \'Resolved\'
        ) metrics
        WHERE resolved_at IS NOT NULL
          AND received_at IS NOT NULL
          AND resolved_at >= received_at
    ');
    $stmt->execute([':barangay' => '%' . $barangay . '%']);
    $value = $stmt->fetchColumn();

    if ($value === false || $value === null) {
        return null;
    }

    return round((float) $value, 1);
}

/**
 * Top incident locations in the barangay.
 *
 * @return list<array{location: string, total: int}>
 */
function fetch_analytics_top_locations(PDO $pdo, string $barangay, int $days = 30, int $limit = 8): array
{
    $stmt = $pdo->prepare('
        SELECT i.Location AS location, COUNT(*) AS total
        FROM incident i
        WHERE i.Location LIKE :barangay
          AND i.Date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
        GROUP BY i.Location
        ORDER BY total DESC, i.Location ASC
        LIMIT :limit
    ');
    $stmt->bindValue(':barangay', '%' . $barangay . '%');
    $stmt->bindValue(':days', $days, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $rows = [];
    foreach ($stmt->fetchAll() as $row) {
        $rows[] = [
            'location' => (string) $row['location'],
            'total' => (int) $row['total'],
        ];
    }

    return $rows;
}

/**
 * Active rabies watch cases with days remaining.
 *
 * @return list<array<string, mixed>>
 */
function fetch_analytics_rabies_watch(PDO $pdo, string $barangay): array
{
    $stmt = $pdo->prepare('
        SELECT c.CaseID,
               i.IncidentID,
               i.Location,
               i.Date AS filed_date,
               d.DogName,
               d.RegistryID,
               c.CaseStatus,
               (
                   SELECT COUNT(*)
                   FROM rabies_checklist rc
                   WHERE rc.CaseID = c.CaseID AND rc.status = \'Checked\'
               ) AS days_checked
        FROM `case` c
        INNER JOIN incident i ON i.IncidentID = c.IncidentID
        LEFT JOIN dog d ON d.dog_id = i.dog_id
        WHERE i.Location LIKE :barangay
          AND c.RabiesMonitoring = 1
          AND c.CaseStatus NOT IN (\'Resolved\', \'Referred\')
        ORDER BY i.Date DESC
    ');
    $stmt->execute([':barangay' => '%' . $barangay . '%']);

    $rows = [];
    foreach ($stmt->fetchAll() as $row) {
        $daysChecked = (int) ($row['days_checked'] ?? 0);
        $rows[] = [
            'case_id' => (int) $row['CaseID'],
            'incident_id' => (int) $row['IncidentID'],
            'location' => (string) $row['Location'],
            'filed_date' => (string) $row['filed_date'],
            'dog_name' => (string) ($row['DogName'] ?? 'Unknown dog'),
            'registry_id' => (string) ($row['RegistryID'] ?? ''),
            'case_status' => (string) $row['CaseStatus'],
            'days_checked' => $daysChecked,
            'days_remaining' => max(0, 14 - $daysChecked),
        ];
    }

    return $rows;
}

/**
 * Cumulative registered dogs over time for barangay.
 *
 * @return array{
 *     labels: list<string>,
 *     cumulative: list<int>,
 *     coverage_pct: float,
 *     total_dogs: int,
 *     vaccinated_dogs: int
 * }
 */
function fetch_analytics_registry_growth(PDO $pdo, string $barangay, int $days = 30): array
{
    $baseStmt = $pdo->prepare('
        SELECT COUNT(*) AS total
        FROM dog d
        INNER JOIN `user` u ON u.UserID = d.UserID
        WHERE u.Barangay = :barangay
          AND d.created_at < DATE_SUB(CURDATE(), INTERVAL :days DAY)
    ');
    $baseStmt->bindValue(':barangay', $barangay);
    $baseStmt->bindValue(':days', $days, PDO::PARAM_INT);
    $baseStmt->execute();
    $runningTotal = (int) ($baseStmt->fetchColumn() ?: 0);

    $dailyStmt = $pdo->prepare('
        SELECT DATE(d.created_at) AS day_key, COUNT(*) AS added
        FROM dog d
        INNER JOIN `user` u ON u.UserID = d.UserID
        WHERE u.Barangay = :barangay
          AND d.created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
        GROUP BY DATE(d.created_at)
        ORDER BY day_key ASC
    ');
    $dailyStmt->bindValue(':barangay', $barangay);
    $dailyStmt->bindValue(':days', $days, PDO::PARAM_INT);
    $dailyStmt->execute();

    $byDay = [];
    foreach ($dailyStmt->fetchAll() as $row) {
        $byDay[(string) $row['day_key']] = (int) $row['added'];
    }

    $labels = [];
    $cumulative = [];
    for ($offset = $days - 1; $offset >= 0; $offset--) {
        $dayKey = date('Y-m-d', strtotime('-' . $offset . ' days'));
        $runningTotal += $byDay[$dayKey] ?? 0;
        $labels[] = date('M j', strtotime($dayKey));
        $cumulative[] = $runningTotal;
    }

    $coverageStmt = $pdo->prepare('
        SELECT
            COUNT(DISTINCT d.dog_id) AS total_dogs,
            COUNT(DISTINCT CASE WHEN v.vax_status = \'Verified\' THEN d.dog_id END) AS vaccinated_dogs
        FROM dog d
        INNER JOIN `user` u ON u.UserID = d.UserID
        LEFT JOIN vaccinerecord v ON v.dog_id = d.dog_id
        WHERE u.Barangay = :barangay
    ');
    $coverageStmt->execute([':barangay' => $barangay]);
    $coverageRow = $coverageStmt->fetch() ?: [];
    $totalDogs = (int) ($coverageRow['total_dogs'] ?? 0);
    $vaccinatedDogs = (int) ($coverageRow['vaccinated_dogs'] ?? 0);
    $coveragePct = $totalDogs > 0 ? round(($vaccinatedDogs / $totalDogs) * 100, 1) : 0.0;

    return [
        'labels' => $labels,
        'cumulative' => $cumulative,
        'coverage_pct' => $coveragePct,
        'total_dogs' => $totalDogs,
        'vaccinated_dogs' => $vaccinatedDogs,
    ];
}

/**
 * Formats period-over-period delta label for KPI cards.
 */
function analytics_period_delta(int $current, int $previous): array
{
    $delta = $current - $previous;

    if ($delta === 0) {
        return ['text' => 'No change vs prior 30 days', 'class' => 'is-flat'];
    }

    if ($delta > 0) {
        return ['text' => '↑ ' . $delta . ' vs prior 30 days', 'class' => 'is-up'];
    }

    return ['text' => '↓ ' . abs($delta) . ' vs prior 30 days', 'class' => 'is-down'];
}

/**
 * Renders an SVG area/line chart for incident trend (no external chart library).
 *
 * @param list<array{label: string, total: int}> $trend
 */
function analytics_render_trend_chart(array $trend, int $height = 240): string
{
    if ($trend === []) {
        return '<p class="text-sm text-muted">No incident data for this period.</p>';
    }

    $values = array_column($trend, 'total');
    $labels = array_column($trend, 'label');
    $count = count($values);
    $width = 800;
    $padLeft = 36;
    $padRight = 16;
    $padTop = 16;
    $padBottom = 32;
    $chartWidth = $width - $padLeft - $padRight;
    $chartHeight = $height - $padTop - $padBottom;
    $maxValue = max(1, (int) max($values));

    $points = [];
    for ($index = 0; $index < $count; $index++) {
        $x = $padLeft + ($count === 1 ? $chartWidth / 2 : ($index / ($count - 1)) * $chartWidth);
        $y = $padTop + $chartHeight - (($values[$index] / $maxValue) * $chartHeight);
        $points[] = [
            'x' => $x,
            'y' => $y,
            'value' => (int) $values[$index],
            'label' => (string) $labels[$index],
        ];
    }

    $linePath = '';
    foreach ($points as $index => $point) {
        $linePath .= ($index === 0 ? 'M' : 'L') . round($point['x'], 1) . ',' . round($point['y'], 1);
    }

    $baselineY = $padTop + $chartHeight;
    $areaPath = $linePath
        . ' L' . round($points[$count - 1]['x'], 1) . ',' . $baselineY
        . ' L' . round($points[0]['x'], 1) . ',' . $baselineY
        . ' Z';

    $svg = '<svg class="analytics-trend-svg" viewBox="0 0 ' . $width . ' ' . $height . '" role="img" aria-label="Incident trend chart" preserveAspectRatio="none">';

    for ($tick = 0; $tick <= $maxValue; $tick++) {
        if ($maxValue > 5 && $tick !== 0 && $tick !== $maxValue && $tick % (int) ceil($maxValue / 4) !== 0) {
            continue;
        }

        $tickY = $padTop + $chartHeight - (($tick / $maxValue) * $chartHeight);
        $svg .= '<line class="analytics-trend-grid" x1="' . $padLeft . '" y1="' . round($tickY, 1) . '" x2="' . ($width - $padRight) . '" y2="' . round($tickY, 1) . '"/>';
        $svg .= '<text class="analytics-trend-axis" x="' . ($padLeft - 8) . '" y="' . round($tickY + 4, 1) . '" text-anchor="end">' . $tick . '</text>';
    }

    $svg .= '<path class="analytics-trend-area" d="' . $areaPath . '"/>';
    $svg .= '<path class="analytics-trend-line" d="' . $linePath . '"/>';

    foreach ($points as $point) {
        if ($point['value'] <= 0) {
            continue;
        }

        $title = htmlspecialchars($point['label'] . ': ' . $point['value'], ENT_QUOTES, 'UTF-8');
        $svg .= '<circle class="analytics-trend-dot" cx="' . round($point['x'], 1) . '" cy="' . round($point['y'], 1) . '" r="4">'
            . '<title>' . $title . '</title></circle>';
    }

    $labelStep = max(1, (int) floor($count / 6));
    for ($index = 0; $index < $count; $index += $labelStep) {
        $point = $points[$index];
        $svg .= '<text class="analytics-trend-axis analytics-trend-axis--x" x="' . round($point['x'], 1) . '" y="' . ($height - 8) . '" text-anchor="middle">'
            . htmlspecialchars($point['label']) . '</text>';
    }

    $svg .= '</svg>';

    return $svg;
}
