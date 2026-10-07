<?php
/**
 * Generate iCal file
 *
 * Validator: http://icalvalid.cloudapp.net/
 *
 * Parameters:
 * - user   - user name
 * - start  - start date, YYYY-MM-DD
 * - end    - end date, YYYY-MM-DD
 * - format - can be ical (default) or "json"
 *            json format is described at
 *            http://fullcalendar.io/docs/event_data/events_array/
 *
 * @link https://tools.ietf.org/html/rfc5545
 */
require_once __DIR__ . '/../src/bootstrap.php';

$user = loadUsername();
$start = time();
$end   = time();
if (isset($_GET['start'])) {
    $start = strtotime($_GET['start']);
    if ($start == 0) {
        header('HTTP/1.0 400 Bad Request');
        echo "Invalid start date\n";
        exit(2);
    }
}
if (isset($_GET['end'])) {
    $end = strtotime($_GET['end']);
    if ($end == 0) {
        header('HTTP/1.0 400 Bad Request');
        echo "Invalid end date\n";
        exit(2);
    }
}

$format = 'ical';
if (isset($_GET['format'])) {
    $format = $_GET['format'];
}

$stmt = dbQuery(
    $db,
    'SELECT day, start, end, duration, description'
    . ', entries.id AS entry_id'
    . ', customers.id AS cust_id'
    . ', customers.name AS cust_name'
    . ', activities.name AS activity_name'
    . ', projects.name AS project_name'
    . ' FROM entries'
    . ' JOIN users ON (users.id = entries.user_id)'
    . ' JOIN customers ON (customers.id = entries.customer_id)'
    . ' JOIN activities ON (activities.id = entries.activity_id)'
    . ' JOIN projects ON (projects.id = entries.project_id)'
    . ' WHERE users.username = ?'
    . ' AND day >= ?'
    . ' AND day <= ?'
    . ' ORDER BY day ASC',
    array($user, date('Y-m-d', $start), date('Y-m-d', $end))
);

if ($format == 'ical') {
    displayIcal($stmt, $user);
} else if ($format == 'json') {
    displayJson($stmt);
} else {
    header('HTTP/1.0 400 Bad Request');
    echo "Invalid format\n";
    exit(1);
}

function vcalDate($nTime)
{
    $date = gmdate('c', $nTime);
    return str_replace(
        array('+00:00', '-', ':'), array('Z', '', ''), $date
    );
}
function vcalText($text)
{
    return str_replace(
        array(';', "\n"),
        array('\\;', '\\n'),
        $text
    );
}

function getEventDescription($row)
{
    return $row['cust_name'] . "\n"
        . $row['project_name'] . "\n"
        . $row['activity_name'] . "\n"
        . $row['description'];
}

function displayIcal($stmt, $user)
{
    header('Content-Type: text/calendar');
    header('Content-Disposition: filename=timetracker-' . $user . '.ics');
    echo "BEGIN:VCALENDAR\r\n";
    echo "VERSION:2.0\r\n";
    echo "PRODID:ttt-data\r\n";
    foreach ($stmt as $row) {
        $nStart = strtotime($row['day'] . ' ' . $row['start']);
        $nEnd   = strtotime($row['day'] . ' ' . $row['end']);
        echo "BEGIN:VEVENT\r\n";
        echo "UID:" . $row['entry_id'] . "@ttt-data\r\n";
        echo "DTSTAMP:" . vcalDate($nEnd) . "\r\n";
        echo "DTSTART:" . vcalDate($nStart) . "\r\n";
        echo "DTEND:" . vcalDate($nEnd) . "\r\n";
        echo "SUMMARY:" . vcalText($row['description']) . "\r\n";
        echo "DESCRIPTION:"
            . vcalText(getEventDescription($row)) . "\r\n";
        echo "END:VEVENT\r\n";
    }
    echo "END:VCALENDAR\r\n";
}

/**
 * A fully saturated colour per customer: hue = customer id * 10 degrees,
 * saturation 1, lightness 0.5, as hex "#rrggbb". This is the HSL to RGB
 * conversion pear/image_color2 0.5.1 performed for these values, written out
 * because that package does not parse on PHP 8.
 *
 * @param int $customerId
 *
 * @return string
 */
function getCustomerColor($customerId)
{
    $hue = (($customerId * 10) % 360) / 360;
    $rgb = array();
    foreach (array($hue + 1 / 3, $hue, $hue - 1 / 3) as $vH) {
        if ($vH < 0) {
            $vH += 1;
        }
        if ($vH > 1) {
            $vH -= 1;
        }
        if (6 * $vH < 1) {
            $value = 6 * $vH;
        } elseif (2 * $vH < 1) {
            $value = 1;
        } elseif (3 * $vH < 2) {
            $value = ((2 / 3) - $vH) * 6;
        } else {
            $value = 0;
        }
        $rgb[] = (int) ($value * 255 + 0.5);
    }
    return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
}

function displayJson($stmt)
{
    $data = array();
    foreach ($stmt as $row) {
        $nStart = strtotime($row['day'] . ' ' . $row['start']);
        $nEnd   = strtotime($row['day'] . ' ' . $row['end']);

        $color = getCustomerColor((int) $row['cust_id']);

        $data[] = (object) array(
            'title' => $row['description'],
            'start' => date('c', $nStart),
            'end'   => date('c', $nEnd),
            'description' => getEventDescription($row),
            'color' => $color,
        );
    }
    header('Content-type: application/json');
    echo json_encode($data);
}
?>
