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
    const DB_HOST = 'db';

    const DB_PASS = 'timalytics';

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
    const URL_JQUERY_JS = 'https://code.jquery.com/jquery-2.2.4.min.js';
    const URL_BOOTSTRAP_JS = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js';
    const URL_MASONRY_JS = 'https://npmcdn.com/masonry-layout@4.1.0/dist/masonry.pkgd.min.js';
    const URL_BUGTRACKER = 'https://bugs.nr/';
}
