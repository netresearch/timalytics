<?php

/**
 * @param float $hours
 * @param bool  $sign
 *
 * @return string
 */
function getPrettyWorkingTime($hours, $sign = false)
{
    if (!$GLOBALS['pretty']) {
        return sprintf('%+.2f', $hours);
    }
    $hours       = round($hours, 2);
    $isNegative  = $hours < 0.0;
    $hours       = abs($hours);
    $fullHours   = floor($hours);
    $fullMinutes = (int) round(($hours - $fullHours) * 60);

    return sprintf(
        '%s%dh&#160;%02dm',
        $sign ? ($isNegative ? '-' : '+') : '',
        $fullHours,
        $fullMinutes
    );
}



/**
 * @param float  $maxWork
 * @param float  $hoursRequired
 * @param float  $hoursWorked
 * @param string $diff
 *
 * @return string
 */
function getMeter($maxWork, $hoursRequired, $hoursWorked, $diff)
{
    $max   = $maxWork;
    $low   = $hoursRequired * 0.9;
    $high  = $hoursRequired - 0.1;
    $value = $hoursWorked;
    $text  = ($hoursWorked - $hoursRequired) != 0.0 ? $diff : '';

    return <<<HTML
    <meter min="0" max="$max" low="$low" high="$high" value="$value" title="Gearbeitet">$text</meter>
HTML;
}



/**
 * @param PDO $db
 *
 * @return array
 */
function getActiveUsers(PDO $db)
{
    $res = $db->query(
        'SELECT users.username as username, users.id as userid'
        . ' FROM users'
        . ' WHERE username NOT IN ("' . implode('", "', array_keys(Netresearch\Timalytics\Config::$arInactiveUsers)) . '")'
        . ' ORDER BY username',
        PDO::FETCH_ASSOC
    );

    $arUsers = array();
    foreach ($res as $row) {
        $arUsers[$row['username']] = getPrettyName($row['username']);
    }

    return $arUsers;
}



/**
 * @param PDO $db
 */
function loadUserSelection(PDO $db)
{
    $GLOBALS['arSelection']['user'] = array();
    foreach (getActiveUsers($db) as $name => $userTitle) {
        $GLOBALS['arSelection']['user'][$name] = $userTitle;
    }
}



/**
 * @return string
 */
function loadUsername()
{
    if (isset($_GET['user']) && $_GET['user'] != '') {
        $user = filter_var($_GET['user'], FILTER_SANITIZE_EMAIL);
    } else {
        $user = $_REQUEST['user'] = $_GET['user'] = getUserByIp();
    }

    return $user;
}


/**
 * Determine current user by client IP.
 *
 * @return string
 */
function getUserByIp()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset(\Netresearch\Timalytics\Config::$arIpUser[$ip])) {
        $user = \Netresearch\Timalytics\Config::$arIpUser[$ip];
    } else {
        $arUsers = getActiveUsers($GLOBALS['db']);
        $user = reset($arUsers);
    }

    return $user;
}


/**
 * @param string $name
 *
 * @return string
 */
function getPrettyName($name)
{
    return implode(' ', array_map('ucfirst', explode('.', $name)));
}



/**
 * @param string $removeParam
 * @param scalar null $removeParamVal
 *
 * @return string
 */
function getBaseUrl($removeParam, $removeParamVal = null)
{
    $params = $_GET;
    if (isset($params[$removeParam])) {
        unset($params[$removeParam]);
    }
    if ($removeParamVal !== null) {
        $params[$removeParam] = $removeParamVal;
    }

    return htmlspecialchars(
        $_SERVER['PHP_SELF'] . '?' . http_build_query($params)
    );
}


/**
 * @param string $removeParam
 *
 * @return string
 */
function getBaseForm($removeParam)
{
    $params = $_GET;
    if (isset($params[$removeParam])) {
        unset($params[$removeParam]);
    }

    $html = '';
    foreach ($params as $key => $value) {
        $html .= '<input type="hidden" name="' . htmlspecialchars($key) . '"'
            . ' value="' . htmlspecialchars($value) . '"/>';
    }
    return $html;
}



/**
 * @param string $s
 *
 * @return string
 */
function hs($s)
{
    return htmlspecialchars($s);
}


/**
 * @param integer $minutes
 *
 * @return string
 */
function formatTime($minutes)
{
    $hours = 0;
    $days  = 0;
    $weeks = 0;
    if ($minutes > 60) {
        $hours = floor($minutes / 60);
        $minutes = $minutes - $hours * 60;
    }
    if ($hours > 8) {
        $days = floor($hours / 8);
        $hours = $hours - $days * 8;
    }
    if ($days > 5) {
        $weeks = floor($days / 5);
        $days = $days - $weeks * 5;
    }

    return '<tt>' . str_replace(
        ' ', '&#160;',
        ($weeks > 0 ? $weeks . 'w ' : '   ')
        . ($days > 0 ? $days . 'd ' : '   ')
        . ($hours > 0 ? $hours . 'h ' : '   ')
        . ($minutes > 0 ? str_pad($minutes, 2, ' ', STR_PAD_LEFT) . 'm' : '   ')
    ) . '</tt>';
}


/**
 * @param integer $minutes
 *
 * @return string
 */
function formatTimeDays($minutes)
{
    $days = $minutes / 60 / 8;
    return round($days) . ' PT';
}


/**
 * @param float $flValue
 *
 * @return string
 */
function formatPercent($flValue)
{
    return round($flValue) . '%';
}


?>
