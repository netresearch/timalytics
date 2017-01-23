<?php

$db = new PDO(
    'mysql:host=' . Netresearch\Timalytics\Config::TT_DB_HOST . ';dbname=' . Netresearch\Timalytics\Config::TT_DB_NAME,
    Netresearch\Timalytics\Config::TT_DB_USER,
    Netresearch\Timalytics\Config::TT_DB_PASS,
    array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET names utf8',
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    )
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
