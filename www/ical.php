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

$stmt = $db->query(
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
    . ' WHERE users.username = ' . $db->quote($user)
    . ' AND day >= ' . $db->quote(date('Y-m-d', $start))
    . ' AND day <= ' . $db->quote(date('Y-m-d', $end))
    . ' ORDER BY day ASC'
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

function displayJson($stmt)
{
    $data = array();
    foreach ($stmt as $row) {
        $nStart = strtotime($row['day'] . ' ' . $row['start']);
        $nEnd   = strtotime($row['day'] . ' ' . $row['end']);

        $hsl = Image_Color2_Model_Hsl::fromArray(
            array(($row['cust_id'] * 10) % 360, 1, 0.5)
        );
        $arColor = $hsl->getRgb();
        $ic = new Image_Color2($arColor);
        $color  = $ic->getHex();

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
