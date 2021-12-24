<?php
/**
 * Code that runs in the month view
 */

//Stundenmeldung
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
    $dbTools->query(
        'DELETE FROM plusminus'
        . ' WHERE pm_username = ' . $dbTools->quote($user)
        . ' AND pm_year = ' . intval($year)
        . ' AND pm_month = ' . intval($month)
    );
    header('Location: ' . $urlThis);
    exit();
}
?>
