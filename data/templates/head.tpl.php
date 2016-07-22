<?php
$userParam = '';
if (isset($user) && $user !== '') {
    $userParam = '?user=' . $user;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php if (! empty($strTitle)) echo $strTitle; else echo 'Timetracker Auswertung'; ?></title>
        <link type="text/css" href="<?= hs(\Netresearch\Timalytics\Config::URL_BOOTSTRAP_CSS); ?>" rel="stylesheet">
        <link type="text/css" href="<?= hs(\Netresearch\Timalytics\Config::URL_BOOTSTRAP_THEME); ?>" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="custom.css" />
        <script type="text/javascript" src="<?= hs(\Netresearch\Timalytics\Config::URL_JQUERY_JS); ?>"></script>
        <script type="text/javascript" src="<?= hs(\Netresearch\Timalytics\Config::URL_BOOTSTRAP_JS); ?>"></script>
<?php if (isset($additionalHeader)) { echo $additionalHeader; } ?>
    </head>
    <body>
        <div class="navbar navbar-inverse" role="navigation" id="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?= hs(\Netresearch\Timalytics\Config::URL_TIMETRACKER); ?>">Timetracker</a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li <?php if (basename($_SERVER['SCRIPT_NAME']) == 'index.php') echo 'class="active"'; ?>><a href="index.php<?php echo $userParam; ?>">Monat</a></li>
                    <li <?php if (basename($_SERVER['SCRIPT_NAME']) == 'projects.php') echo 'class="active"'; ?>><a href="projects.php">Projekt</a></li>
                    <li <?php if (basename($_SERVER['SCRIPT_NAME']) == 'support.php') echo 'class="active"'; ?>><a href="support.php">Support</a></li>
                    <li <?php if (basename($_SERVER['SCRIPT_NAME']) == 'standup.php') echo 'class="active"'; ?>><a href="standup.php">Standup</a></li>
                    <li <?php if (basename($_SERVER['SCRIPT_NAME']) == 'calendar.php') echo 'class="active"'; ?>><a href="calendar.php<?php echo $userParam;?>">Kalender</a></li>
                </ul>

<?php
if (! empty($arSelection)) {
    foreach (array_reverse($arSelection, true) as $selection => $arOptions) {
        echo '<form class="navbar-form pull-right" action="" method="get">';
        echo getBaseForm($selection);
        if (is_array($arOptions)) {
            echo '
    <select name="' . htmlspecialchars($selection) . '" onchange="this.form.submit();"
        class="form-control input-sm">
       ';

            foreach ($arOptions as $name => $userTitle) {
                if (! empty($_REQUEST[$selection]) && $_REQUEST[$selection] == $name) {
                    $selected = ' selected="selected"';
                } else {
                    $selected = '';
                }
                echo '<option value="' . htmlspecialchars($name)
                    . '"' . $selected . '>'
                    . htmlspecialchars($userTitle)
                    . '</option>';
            }

            echo '</select>';
        } else {
            echo '<input type="text" name="' . htmlspecialchars($selection) . '"
                value="' . htmlspecialchars($arOptions) . '" />';
        }
        echo '</form>';
    }
}
?>

            </div>
        </div>
