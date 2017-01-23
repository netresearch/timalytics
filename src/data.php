<?php
require_once __DIR__ . '/../src/db.php';

$dbTools = new PDO(
    'mysql:host=' . Netresearch\Timalytics\Config::DB_HOST . ';dbname=' . Netresearch\Timalytics\Config::DB_NAME,
    Netresearch\Timalytics\Config::DB_USER,
    Netresearch\Timalytics\Config::DB_PASS,
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
} else {
    $contractRow = $dbTools->query(
        $s='SELECT * FROM users_contracts'
        . ' WHERE uc_username = ' . $dbTools->quote($user)
        . ' AND (uc_start IS NULL OR uc_start <= "' . $year . '-' . $month . '-01")'
        . ' AND (uc_end IS NULL OR uc_end >= "' . $year . '-' . $month . '-01")'
    )->fetchObject();

    for ($weekOfDay = 1; $weekOfDay <= 5; $weekOfDay++) {
        $arWorkWeek[$weekOfDay] = $hoursPerDay;
    }
    $arWorkWeek[0] = 0;
    $arWorkWeek[6] = 0;

    if ($contractRow !== false) {
        $workWeek = $contractRow->uc_hours_1 + $contractRow->uc_hours_2
            + $contractRow->uc_hours_3 + $contractRow->uc_hours_4
            + $contractRow->uc_hours_5;
        $arWorkWeek[1] = $contractRow->uc_hours_1;
        $arWorkWeek[2] = $contractRow->uc_hours_2;
        $arWorkWeek[3] = $contractRow->uc_hours_3;
        $arWorkWeek[4] = $contractRow->uc_hours_4;
        $arWorkWeek[5] = $contractRow->uc_hours_5;
        $arWorkWeek[6] = $contractRow->uc_hours_6;
        $arWorkWeek[0] = $contractRow->uc_hours_0;
    }
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
    list($firstYear, $firstMonth) = explode('-', $firstRow->day);
    if ($firstYear == $year && $firstMonth == $month
        || ($firstYear + 1 == $year && $firstMonth == 12 && $month = 1)
    ) {
        $plusminusHours = 0;
    }
}

if (isset($_POST['report']) && isset($_POST['minutes'])) {
    $minutes = (int) $_POST['minutes'];
    $dbTools->query(
        'INSERT INTO plusminus'
        . '(pm_username, pm_year, pm_month, pm_minutes, pm_minutes_absolute)'
        . ' VALUES'
        . '('
        . $dbTools->quote($user)
        . ',' . $dbTools->quote($year)
        . ',' . $dbTools->quote($month)
        . ',' . $dbTools->quote($minutes)
        . ',' . $dbTools->quote($pmRow->pm_minutes_absolute + $minutes)
        . ')'
    );
}

$pmRowThisMonth = $dbTools->query(
    'SELECT pm_minutes_absolute FROM plusminus'
    . ' WHERE pm_username = ' . $dbTools->quote($user)
    . ' AND pm_year = ' . (int)$year
    . ' AND pm_month = ' . (int)$month
)->fetchObject();


$stmt = $db->query(
    'SELECT day, SUM(duration) as minutes FROM entries'
    . ' JOIN users ON (users.id = entries.user_id)'
    . ' WHERE users.username = ' . $dbTools->quote($user)
    . ' AND day LIKE "' . (int)$year . '-' . $month . '-%"'
    . ' GROUP BY day ORDER BY day ASC'
);
$holidays = require __DIR__ . '/../data/feiertage.php';

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
    $weekDay = date('N', $ts);
    $days[$date] = array(
        'date'     => $date,
        'dow'      => (int) date('N', $ts),
        'required' => $arWorkWeek[$weekDay],
        'worked'   => 0.0,
        'holiday'  => isset($holidays[$date]),
        'future'   => $date > $today,
    );
    if ($days[$date]['dow'] >= 6 || $days[$date]['holiday']) {
        $days[$date]['required'] = 0;
        if ($days[$date]['holiday']) {
            $days[$date]['name'] = $holidays[$date];
        } else if ($days[$date]['dow'] == 6) {
            $days[$date]['name'] = 'Sonnabend';
        } else if ($days[$date]['dow'] == 7) {
            $days[$date]['name'] = 'Sonntag';
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
