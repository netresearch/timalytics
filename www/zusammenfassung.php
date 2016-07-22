<?php
/**
 * Zusammenfassung für Jira-Gadget
 */
require_once __DIR__ . '/../src/bootstrap.php';


echo <<<HTML
<!DOCTYPE html>
<html lang="en">
 <head>
  <title>Zusammenfassung {$user} {$year}-{$month}</title>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" rel="stylesheet" href="custom.css" />
HTML;
echo '<link type="text/css" href="' . hs(\Netresearch\Timalytics\Config::URL_BOOTSTRAP_CSS) . '" rel="stylesheet">';
echo '<link type="text/css" href="' . hs(\Netresearch\Timalytics\Config::URL_BOOTSTRAP_THEME) . '" rel="stylesheet">';
echo '<script type="text/javascript" src="' . hs(\Netresearch\Timalytics\Config::URL_JQUERY_JS) . '"></script>';
echo '<script type="text/javascript" src="' . hs(\Netresearch\Timalytics\Config::URL_BOOTSTRAP_JS) . '"></script>';
echo <<<HTML
 </head>
 <body>
 <div class="container">
<div class="row">
    <div class="col-md-4">
    <p style="margin-top: 0.5em">
     <a href="/?user={$user}" target="_blank">Monatsauswertung</a> {$user} {$year}-{$month}
    </p>
    <table class="table table-bordered table-hover table-condensed" style="margin-bottom: 0">
HTML;
include __DIR__ . '/../data/templates/zusammenfassung.tpl.php';
echo <<<HTML
    </table>
    </div>
    </div>
    </div>
 </body>
</html>
HTML;

?>
