<?php

require_once __DIR__ . '/../src/bootstrap.php';

$timespan = 7;

if (isset($_GET['timespan'])) {
    $timespan = filter_var($_GET['timespan'], FILTER_SANITIZE_NUMBER_INT);
}

$stmt = $db->query(
    'SELECT DISTINCT'
    . ' project_id, projects.name AS project, customers.name AS customer'
    . ', estimation, jira_id as ts_prefix'
    . ', ticket_systems.url AS ts_url, ticket_systems.type as ts_type'
    . ' FROM entries'
    . ' JOIN projects ON project_id = projects.id'
    . ' JOIN customers ON projects.customer_id = customers.id'
    . ' LEFT JOIN ticket_systems ON projects.ticket_system = ticket_systems.id'
    . ' WHERE day >= (NOW() - INTERVAL ' . $timespan . ' DAY)'
    . ' AND estimation > 0'
    . ' ORDER BY customers.name, projects.name'
);

$projects = array();
$ids = array();
foreach ($stmt as $row) {
    $projects[$row['project_id']] = $row;
    $ids[] = (int) $row['project_id'];
}

//load times
if (count($ids)) {
    $stmt = $db->query(
        'SELECT project_id, SUM(duration) as duration'
        . ' FROM entries'
        . ' WHERE project_id IN (' . implode(',', $ids) . ')'
        . ' GROUP BY project_id'
    );
    foreach ($stmt as $row) {
        $projects[$row['project_id']]['duration'] = $row['duration'];
    }
}

$customers = array();
foreach ($projects as $project) {
    if (!in_array($project['customer'], $customers)) {
        $customers[] = $project['customer'];
    }
}

$totals = array();
foreach ($projects as $project) {
    $customer = $project['customer'];

    if (!array_key_exists($customer, $totals)) {
        $totals[$customer] = array(
            'estimation' => 0,
            'duration'   => 0,
        );
    }

    $totals[$customer]['estimation'] += (int) $project['estimation'];
    $totals[$customer]['duration']   += (int) $project['duration'];
}

$arSelection['timespan'] = array(
    7  => '7 Tage',
    14 => '14 Tage',
    30 => '30 Tage',
);

include __DIR__ . '/../data/templates/head.tpl.php';
include __DIR__ . '/../data/templates/projects.tpl.php';
include __DIR__ . '/../data/templates/foot.tpl.php';

?>
