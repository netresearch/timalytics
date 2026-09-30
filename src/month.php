<?php
/**
 * Code that runs in the month view
 */

//Stundenmeldung
if (isset($_POST['report']) && isset($_POST['minutes'])) {
    $minutes = (int) $_POST['minutes'];
    dbQuery(
        $dbTools,
        'INSERT INTO plusminus'
        . '(pm_username, pm_year, pm_month, pm_minutes, pm_minutes_absolute)'
        . ' VALUES (?, ?, ?, ?, ?)',
        array($user, $year, $month, $minutes, $pmRow->pm_minutes_absolute + $minutes)
    );
    header('Location: ' . $urlThis);
    exit();
}

//delete Stundenmeldung
if ($GLOBALS['cfg']['allowDelete']
    && $plusminusHoursNextMonth === null
    && $plusminusHoursThisMonth !== null
    && isset($_POST['delete']) && $_POST['delete'] == 1
    && isset($_POST['really']) && $_POST['really'] === 'yes'
) {
    dbQuery(
        $dbTools,
        'DELETE FROM plusminus'
        . ' WHERE pm_username = ?'
        . ' AND pm_year = ?'
        . ' AND pm_month = ?',
        array($user, intval($year), intval($month))
    );
    header('Location: ' . $urlThis);
    exit();
}
?>
