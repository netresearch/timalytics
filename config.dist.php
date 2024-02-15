<?php
/**
 * Netresearch Timetracker Analytics
 *
 * PHP version 5
 *
 * @category   Netresearch
 * @package    Timalytics
 * @subpackage Config
 * @author     Various Artists <info@netresearch.de>
 * @license    http://www.gnu.org/licenses/agpl-3.0.html GNU AGPl 3
 * @link       http://www.netresearch.de
 */
$GLOBALS['cfg']['DB_HOST'] = 'db';
$GLOBALS['cfg']['DB_PASS'] = 'timalytics';
$GLOBALS['cfg']['DB_USER'] = 'timalytics';
$GLOBALS['cfg']['DB_NAME'] = 'timalytics';

$GLOBALS['cfg']['TT_DB_HOST'] = 'localhost';
$GLOBALS['cfg']['TT_DB_PASS'] = '';
$GLOBALS['cfg']['TT_DB_USER'] = 'timetracker';
$GLOBALS['cfg']['TT_DB_NAME'] = 'timetracker';

$GLOBALS['cfg']['arAllowedUsers']  = array();
$GLOBALS['cfg']['arIpUser']        = array();
$GLOBALS['cfg']['arInactiveUsers'] = array();

$GLOBALS['cfg']['allowDelete'] = false;

$GLOBALS['cfg']['URL_TIMETRACKER'] = 'https://github.com/netresearch/timetracker';
$GLOBALS['cfg']['URL_BOOTSTRAP_CSS'] = 'css/bootstrap.min.css';
$GLOBALS['cfg']['URL_BOOTSTRAP_THEME'] = 'css/bootstrap-theme.min.css';
$GLOBALS['cfg']['URL_JQUERY_JS'] = 'js/jquery.min.js';
$GLOBALS['cfg']['URL_BOOTSTRAP_JS'] = 'js/bootstrap.min.js';
$GLOBALS['cfg']['URL_MASONRY_JS'] = 'js/masonry.pkgd.min.js';
$GLOBALS['cfg']['URL_BUGTRACKER'] = 'https://bugs.nr/';

//Halve target time for special days (e.g. holidays)
$GLOBALS['cfg']['HALF_DAY_POLICY'] = false;
// If timalytics is running in a subdirectory of the timetracker installation,
// then enable the following line to restrict analatics access to the user
// (and the team members if the user is a project leader PL):
//require __DIR__ . '/src/timetrackersessionuser.php';
?>
