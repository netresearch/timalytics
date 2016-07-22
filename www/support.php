<?php

require_once __DIR__ . '/../src/bootstrap.php';

$month       = date('n');
$year        = date('Y');

if (isset($_GET['year'])) {
    $year = filter_var($_GET['year'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $_REQUEST['year'] = $_GET['year'] = $year;
}

if (isset($_GET['month'])) {
    $month = filter_var($_GET['month'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $_REQUEST['month'] = $_GET['month'] = $month;
}

// get all booked times grouped/summed up by customer-project-ticket
// this could return the same ticket multiple times - so we execute another
// query to get the whole sum later
$strSQL = <<<SQL
   SELECT entries.customer_id,
          entries.project_id,
          entries.ticket,
          SUM(entries.duration) as duration_sum,
          projects.name AS project,
          projects.jira_id as ts_prefix,
          projects.estimation as project_estimation,
          customers.name AS customer,
          ticket_systems.url AS ts_url,
          ticket_systems.type as ts_type
     FROM entries
     JOIN projects ON entries.project_id = projects.id
     JOIN customers ON entries.customer_id = customers.id
LEFT JOIN ticket_systems ON projects.ticket_system = ticket_systems.id
    WHERE MONTH(entries.day) = {$month}
      AND YEAR(day) = {$year}
      AND entries.ticket <> ''
      AND projects.billing = 3 -- 3 = support
 GROUP BY entries.ticket, entries.project_id, entries.customer_id
 ORDER BY customers.name,
          projects.name,
          entries.ticket
SQL;

$statement = $db->query($strSQL);

$arTickets = array();

$arCustomers = array();
foreach ($statement as $arRow) {
    if (! empty($arRow['ticket'])) {
        $arTickets[$arRow['ticket']] = $arRow['ticket'];
    }

    if (empty($arCustomers[$arRow['customer_id']])) {
        $arCustomers[$arRow['customer_id']] = array(
            'duration'     => intval($arRow['duration_sum']),
            'duration_sum' => 0,
            'name'         => $arRow['customer'],
            'estimation'   => 0,
        );
    } else {
        $arCustomers[$arRow['customer_id']]['duration']
            += $arRow['duration_sum'];
    }

    if (empty($arCustomers[$arRow['customer_id']]['entries'][$arRow['project_id']])) {

        if (! empty($arRow['ts_type'])
            && ! empty($arRow['ts_prefix'])
            && $arRow['ts_type'] == 'JIRA'
        ) {
            $arRow['url'] = $arRow['ts_url'] . '/browse/' . $arRow['ts_prefix'];
        } else {
            $arRow['url'] = '';
        }

        $arCustomers[$arRow['customer_id']]['entries'][$arRow['project_id']] = array(
            'duration'     => intval($arRow['duration_sum']),
            'name'         => $arRow['project'],
            'url'          => $arRow['url'],
            'estimation'   => intval($arRow['project_estimation']),
            'duration_sum' => 0,
        );
    } else {
        $arCustomers[$arRow['customer_id']]['entries'][$arRow['project_id']]['duration']
            += $arRow['duration_sum'];
    }

    if (! empty($arRow['ticket'])) {
        $arCustomers[$arRow['customer_id']]['entries'][$arRow['project_id']]['entries'][$arRow['ticket']]
            = &$arTickets[$arRow['ticket']];
    }
}

if (count($arTickets) > 0) {
    $strTickets = "'" . implode("','", $arTickets) . "'";
    // get booked ticket duration for selected month - independent
    // of project and customer
    $strSQL = <<<SQL
       SELECT ticket AS name,
--              COUNT(DISTINCT customer_id) - 1 AS customer_cnt,
--              COUNT(DISTINCT project_id) - 1 AS project_cnt,
--              COUNT(*) AS duration_cnt,
              SUM(duration) AS duration
         FROM entries
        WHERE ticket IN ({$strTickets})
          AND MONTH(entries.day) = {$month}
          AND YEAR(day) = {$year}
     GROUP BY ticket
SQL;

    $statement = $db->query($strSQL);
    foreach ($statement as $arRow) {
        $arRow['url']
            = 'ticket.php?ticket=' . $arRow['name'];

        $arTickets[$arRow['name']] = $arRow;
        $arTickets[$arRow['name']]['estimation'] = 60 * 8;
    }

    // get booked ticket duration for whole lifetime - independent
    // of project and customer
    $strSQL = <<<SQL
       SELECT ticket AS name,
              COUNT(DISTINCT customer_id) - 1 AS customer_cnt,
              COUNT(DISTINCT project_id) - 1 AS project_cnt,
              COUNT(*) AS duration_cnt,
              SUM(duration) AS duration_sum
         FROM entries
        WHERE ticket IN ({$strTickets})
     GROUP BY ticket
SQL;

    $statement = $db->query($strSQL);
    foreach ($statement as $arRow) {
        $arTickets[$arRow['name']] = array_merge(
            $arTickets[$arRow['name']], $arRow
        );
    }
}

$arSelection['month'] = array(
    1 => 'Januar',
    2 => 'Februar',
    3 => 'März',
    4 => 'April',
    5 => 'Mai',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'August',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Dezember',
);

$arSelection['year'] = array();
$i = 2011;
while ($i <= date('Y')) {
    $arSelection['year'][$i] = $i;
    $i++;
}

include __DIR__ . '/../data/templates/head.tpl.php';
include __DIR__ . '/../data/templates/support.tpl.php';
include __DIR__ . '/../data/templates/foot.tpl.php';

?>
