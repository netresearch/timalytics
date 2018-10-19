<?php
/**
 * Load the user from the timetracker session and only make his data available.
 *
 * require this file in your config.php
 */

require_once __DIR__ . '/../src/db.php';

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

$teamMembers = $db->query(
    'SELECT DISTINCT members.username
    FROM users, teams, teams_users, users AS members
    WHERE users.id = teams.lead_user_id
     AND users.username = ' . $db->quote($_SESSION['_sf2_attributes']['loginUsername']) . '
     AND users.type = "PL"
     AND teams.id = teams_users.team_id
     AND teams_users.user_id = members.id
    ORDER BY members.username'
)->fetchAll();

if (empty($teamMembers)) {
    $GLOBALS['cfg']['arAllowedUsers'] = [
        $_SESSION['_sf2_attributes']['loginUsername']
    ];
} else {
    $GLOBALS['cfg']['arAllowedUsers'] = array_column($teamMembers, "username");
}
?>
