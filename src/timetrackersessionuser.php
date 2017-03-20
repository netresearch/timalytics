<?php
/**
 * Load the user from the timetracker session and only make his data available.
 *
 * require this file in your config.php
 */

if (!isset($_COOKIE['PHPSESSID'])) {
    //no session id cookie
    die('Not logged into timetacker (no session id)');
}
$files = glob(__DIR__ . '/../../app/cache/*/sessions/sess_' . $_COOKIE['PHPSESSID']);
if (!count($files)) {
    //no session file
    die('Not logged into timetacker (no session file)');
}
$dir = dirname(reset($files));
ini_set('session.save_path', $dir);
session_start();
if (!isset($_SESSION['_sf2_attributes']['loggedIn'])
    || $_SESSION['_sf2_attributes']['loggedIn'] === false
    || !isset($_SESSION['_sf2_attributes']['loginUsername'])
    || $_SESSION['_sf2_attributes']['loginUsername'] == ''
) {
    //no session
    die('Not logged into timetacker (no valid session data)');
}

$GLOBALS['cfg']['arAllowedUsers'] = [
    $_SESSION['_sf2_attributes']['loginUsername']
];
?>
