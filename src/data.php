<?php
require_once __DIR__ . '/../src/db.php';

$dbTools = new PDO(
    'mysql:host=' . $GLOBALS['cfg']['DB_HOST'] . ';dbname=' . $GLOBALS['cfg']['DB_NAME'],
    $GLOBALS['cfg']['DB_USER'],
    $GLOBALS['cfg']['DB_PASS'],
    array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET names utf8',
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    )
);
$dbTools->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user        = loadUsername();
$month       = date('n');
$year        = date('Y');
$hoursPerDay = 8;
$pretty      = true;

if (isset($_GET['year'])) {
    $year = filter_var($_GET['year'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $_REQUEST['year'] = $_GET['year'] = $year;
}

if (isset($_GET['month'])) {
    $month = filter_var($_GET['month'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $_REQUEST['month'] = $_GET['month'] = $month;
}

if (isset($_GET['hoursPerDay'])) {
    $hoursPerDay = filter_var(
        $_GET['hoursPerDay'],
        FILTER_SANITIZE_NUMBER_FLOAT,
        FILTER_FLAG_ALLOW_FRACTION
    );
    for ($weekOfDay = 1; $weekOfDay <= 5; $weekOfDay++) {
        $defaultWorkWeek[$weekOfDay] = $hoursPerDay;
    }
    $defaultWorkWeek[0] = 0;
    $defaultWorkWeek[6] = 0;
} else {
    $monthStart = $year . '-' . $month . '-01';
    $monthEnd = date('Y-m-t', strtotime($monthStart));

    $contractStmt = $db->query(
        'SELECT contracts.*, contracts.start as contract_start, contracts.end as contract_end FROM contracts'
        . ' JOIN users ON (users.id = contracts.user_id)'
        . ' WHERE users.username = ' . $dbTools->quote($user)
        . ' AND (contracts.start IS NULL OR contracts.start <= "' . $monthEnd . '")'
        . ' AND (contracts.end IS NULL OR contracts.end >= "' . $monthStart . '")'
        . ' ORDER BY contracts.start ASC'
    );

    $arContracts = [];
    foreach ($contractStmt as $row) {
        $arContracts[] = [
            'start' => $row['contract_start'],
            'end' => $row['contract_end'],
            'hours' => [
                0 => (float) $row['hours_0'],
                1 => (float) $row['hours_1'],
                2 => (float) $row['hours_2'],
                3 => (float) $row['hours_3'],
                4 => (float) $row['hours_4'],
                5 => (float) $row['hours_5'],
                6 => (float) $row['hours_6'],
            ]
        ];
    }

    for ($weekOfDay = 1; $weekOfDay <= 5; $weekOfDay++) {
        $defaultWorkWeek[$weekOfDay] = $hoursPerDay;
    }
    $defaultWorkWeek[0] = 0;
    $defaultWorkWeek[6] = 0;
}

if (isset($_GET['pretty'])) {
    $pretty = (bool) filter_var($_GET['pretty'], FILTER_SANITIZE_NUMBER_INT);
}

$month = str_pad($month, 2, '0', STR_PAD_LEFT);

$prevYear = $year;
$prevMonth = $month;
if (--$prevMonth == 0) {
    $prevMonth = 12;
    --$prevYear;
}
$nextYear = $year;
$nextMonth = $month;
if (++$nextMonth == 13) {
    $nextMonth = 1;
    ++$nextYear;
}
$urlThis = '?month=' . $month . '&year=' . $year . '&user=' . $user;
$urlPrev = '?month=' . $prevMonth . '&year=' . $prevYear . '&user=' . $user;
$urlNext = '?month=' . $nextMonth . '&year=' . $nextYear . '&user=' . $user;
$urlToday = '?month=' . date('m') . '&year=' . date('Y') . '&user=' . $user;

$pmRow = $dbTools->query(
    'SELECT pm_minutes_absolute FROM plusminus'
    . ' WHERE pm_username = ' . $dbTools->quote($user)
    . ' AND pm_year = ' . (int)$prevYear
    . ' AND pm_month = ' . (int)$prevMonth
)->fetchObject();
$plusminusHours = null;
if ($pmRow) {
    $plusminusHours = $pmRow->pm_minutes_absolute / 60;
} else {
    $firstRow = $db->query(
        'SELECT day FROM entries'
        . ' JOIN users ON (users.id = entries.user_id)'
        . ' WHERE users.username = ' . $dbTools->quote($user)
        . ' ORDER BY day LIMIT 1'
    )->fetchObject();
    if ($firstRow !== false) {
        list($firstYear, $firstMonth) = explode('-', $firstRow->day);
    } else {
        $firstYear  = date('Y');
        $firstMonth = date('m');
    }
    if ($firstYear == $year && $firstMonth == $month
        || ($firstYear + 1 == $year && $firstMonth == 12 && $month = 1)
    ) {
        $plusminusHours = 0;
    }
}

$pmRowThisMonth = $dbTools->query(
    'SELECT pm_minutes, pm_minutes_absolute FROM plusminus'
    . ' WHERE pm_username = ' . $dbTools->quote($user)
    . ' AND pm_year = ' . (int) $year
    . ' AND pm_month = ' . (int) $month
)->fetchObject();
$plusminusHoursThisMonth = null;
if ($pmRowThisMonth) {
    $plusminusHoursThisMonth = $pmRowThisMonth->pm_minutes_absolute / 60;
}

$pmRowNextMonth = $dbTools->query(
    'SELECT pm_minutes_absolute FROM plusminus'
    . ' WHERE pm_username = ' . $dbTools->quote($user)
    . ' AND pm_year = ' . (int) $nextYear
    . ' AND pm_month = ' . (int) $nextMonth
)->fetchObject();
$plusminusHoursNextMonth = null;
if ($pmRowNextMonth) {
    $plusminusHoursNextMonth = $pmRowNextMonth->pm_minutes_absolute / 60;
}


$stmt = $db->query(
    'SELECT day, SUM(duration) as minutes FROM entries'
    . ' JOIN users ON (users.id = entries.user_id)'
    . ' WHERE users.username = ' . $dbTools->quote($user)
    . ' AND day LIKE "' . (int)$year . '-' . $month . '-%"'
    . ' GROUP BY day ORDER BY day ASC'
);
$holidays = require __DIR__ . '/../data/feiertage.php';

/**
 * Get working hours for a specific date based on active contracts
 *
 * @param string $date Date in Y-m-d format
 * @param int $weekDay Day of week (1=Monday, ..., 7=Sunday, from date('N'))
 * @param array $arContracts Array of contracts with start, end, and hours
 * @param array $defaultWorkWeek Default work week hours if no contract matches
 * @return float Hours required for this day
 */
function getContractHoursForDate(string $date, int $weekDay, array $arContracts, array $defaultWorkWeek) {
    $dayIndex = ($weekDay == 7) ? 0 : $weekDay;

    if (empty($arContracts)) {
        return $defaultWorkWeek[$dayIndex];
    }

    foreach ($arContracts as $contract) {
        $contractStart = $contract['start'];
        $contractEnd = $contract['end'];

        $startValid = ($contractStart === null || $contractStart <= $date);
        $endValid = ($contractEnd === null || $contractEnd >= $date);

        if ($startValid && $endValid) {
            return $contract['hours'][$dayIndex];
        }
    }

    return $defaultWorkWeek[$dayIndex];
}

$days = array();
$monthdays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$sumRequired = 0.0;
$sumRequiredUntilToday = 0.0;
$sumRequiredUntilYesterday = 0.0;
$sumWorked = 0.0;
$sumWorkedUntilYesterday = 0.0;
$today = date('Y-m-d');
$thisMonth = substr($today, 0, 7) == $year . '-' . $month;

for ($n = 1; $n <= $monthdays; $n++) {
    $ts   = mktime(0, 0, 0, $month, $n, $year);
    $date = date('Y-m-d', $ts);
    $weekDay = (int) date('N', $ts);

    $requiredHours = getContractHoursForDate($date, $weekDay, $arContracts, $defaultWorkWeek);

    $days[$date] = array(
        'date'     => $date,
        'dow'      => $weekDay,
        'required' => $requiredHours,
        'worked'   => 0.0,
        'holiday'  => isset($holidays[$date]),
        'future'   => $date > $today,
    );
    // set required worktime for christmas and new year eve to halve the normal time
    if ($GLOBALS['cfg']['HALF_DAY_POLICY']
        && (date('m-d', $ts) == '12-24' || date('m-d', $ts) == '12-31')
    ) {
        $days[$date]['required'] = $requiredHours / 2;
    }
    if ($days[$date]['holiday']) {
        $days[$date]['required'] = 0;
    }
    if ($days[$date]['required'] == 0) {
        if ($days[$date]['holiday']) {
            $days[$date]['name'] = $holidays[$date];
        } else {
            $days[$date]['name'] = strftime('%A', $ts);
        }
        $days[$date]['holiday'] = true;
    }
    $sumRequired += $days[$date]['required'];
    if ($date <= $today) {
        $sumRequiredUntilToday += $days[$date]['required'];
    }
    if ($date < $today) {
        $sumRequiredUntilYesterday += $days[$date]['required'];
    }
}

foreach ($stmt as $row) {
    $date = $row['day'];
    $days[$date]['worked'] = $row['minutes'] / 60.0;
    $sumWorked += $days[$date]['worked'];
    if ($date < $today) {
        $sumWorkedUntilYesterday += $days[$date]['worked'];
    }
}
?>
