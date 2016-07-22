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

namespace Netresearch\Timalytics;

/**
 * Timalytics configuration
 *
 * @category   Netresearch
 * @package    Timalytics
 * @subpackage Config
 * @author     Various Artists <info@netresearch.de>
 * @license    http://www.gnu.org/licenses/agpl-3.0.html GNU AGPl 3
 * @link       http://www.netresearch.de
 */
class Config_Dist
{
    const DB_HOST = 'localhost';

    const DB_PASS = '';

    const DB_USER = 'timalytics';

    const DB_NAME = 'timalytics';

    const TT_DB_HOST = 'localhost';

    const TT_DB_PASS = '';

    const TT_DB_USER = 'timetracker';

    const TT_DB_NAME = 'timetracker';

    static $arIpUser = array();

    static $arInactiveUsers = array();

    const URL_TIMETRACKER = 'https://github.com/netresearch/timetracker';

    const URL_BOOTSTRAP_CSS = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css';
    const URL_BOOTSTRAP_THEME = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css';
    const URL_JQUERY_JS = 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js';
    const URL_BOOTSTRAP_JS = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js';
    const URL_MASONRY_JS = 'js/masonry.pkgd.min.js';
    const URL_BUGTRACKER = 'https://bugs.nr/';
}
