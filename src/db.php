<?php

$db = new PDO(
    'mysql:host=' . $GLOBALS['cfg']['TT_DB_HOST'] . ';dbname=' . $GLOBALS['cfg']['TT_DB_NAME'],
    $GLOBALS['cfg']['TT_DB_USER'],
    $GLOBALS['cfg']['TT_DB_PASS'],
    array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET names utf8',
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    )
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
