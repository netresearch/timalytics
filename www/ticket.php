<?php

require_once __DIR__ . '/../src/bootstrap.php';

$ticket      = false;

if (isset($_GET['ticket'])) {
    $ticket = filter_var($_GET['ticket'], FILTER_SANITIZE_STRING);
}

if (isset($_GET['order'])) {
    $order = filter_var($_GET['order'], FILTER_SANITIZE_STRING);
}

// get all booked times for a specific ticket
$strSQL = <<<SQL
   SELECT entries.customer_id,
          entries.project_id,
          entries.ticket,
          entries.duration,
          entries.description,
          entries.day,
          entries.start,
          entries.duration,
          entries.id,
          users.abbr AS user,
          activities.name AS activity,
          projects.name AS project,
          projects.jira_id as ts_prefix,
          projects.estimation as project_estimation,
          customers.name AS customer,
          ticket_systems.url AS ts_url,
          ticket_systems.type as ts_type
     FROM entries
     JOIN projects ON entries.project_id = projects.id
     JOIN customers ON entries.customer_id = customers.id
     JOIN users ON entries.user_id = users.id
     JOIN activities ON entries.activity_id = activities.id
LEFT JOIN ticket_systems ON projects.ticket_system = ticket_systems.id
    WHERE entries.ticket = '{$ticket}'
 ORDER BY entries.day DESC,
          entries.start DESC
SQL;

$statement = $db->query($strSQL);

$arCustomers = array();
foreach ($statement as $arRow) {

    if (empty($arCustomers[$arRow['ticket']])) {
        $arCustomers[$arRow['ticket']] = array(
            'duration'   => intval($arRow['duration']),
            'name'       => $arRow['ticket'],
            'estimation' => 0,
            'duration_sum' => 0,
        );
    } else {
        $arCustomers[$arRow['ticket']]['duration']
            += $arRow['duration'];
    }

    $arTicket = &$arCustomers[$arRow['ticket']];

    if (empty($arTicket['entries'][$arRow['customer_id']])) {
        $arTicket['entries'][$arRow['customer_id']] = array(
            'duration'   => intval($arRow['duration']),
            'name'       => $arRow['customer'],
            'estimation' => 0,
            'duration_sum' => 0,
        );
    } else {
        $arTicket['entries'][$arRow['customer_id']]['duration']
            += $arRow['duration'];
    }

    $arCustomer = &$arTicket['entries'][$arRow['customer_id']];

    if (empty($arCustomer['entries'][$arRow['project_id']])) {

        if (! empty($arRow['ts_type'])
            && ! empty($arRow['ts_prefix'])
            && $arRow['ts_type'] == 'JIRA'
        ) {
            $arRow['url'] = $arRow['ts_url'] . '/browse/' . $arRow['ts_prefix'];
        } else {
            $arRow['url'] = '';
        }

        $arCustomer['entries'][$arRow['project_id']] = array(
            'duration'   => intval($arRow['duration']),
            'name'       => $arRow['project'],
            'url'        => $arRow['url'],
            'estimation' => intval($arRow['project_estimation']),
            'duration_sum' => 0,
        );
    } else {
        $arCustomer['entries'][$arRow['project_id']]['duration']
            += $arRow['duration'];
    }

    $arEntries[$arRow['id']] = $arRow;
}
unset($arCustomer, $arRow, $statement, $strSQL);

$arSelection['ticket'] = $ticket;

include __DIR__ . '/../data/templates/head.tpl.php';
include __DIR__ . '/../data/templates/support.tpl.php';
include __DIR__ . '/../data/templates/ticket.tpl.php';
include __DIR__ . '/../data/templates/foot.tpl.php';

?>
