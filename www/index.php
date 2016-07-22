<?php
require_once __DIR__ . '/../src/bootstrap.php';

$arSelection = array(
    'user'  => array(),
    'month' => array(),
    'year'  => array(),
);

loadUserSelection($db);

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

$strTitle = 'Monatsauswertung '
    . ucwords(str_replace('.', ' ', $user))
    . ' ' . $year . '-' . $month;

include __DIR__ . '/../data/templates/head.tpl.php';
include __DIR__ . '/../data/templates/month.tpl.php';
include __DIR__ . '/../data/templates/foot.tpl.php';
?>
