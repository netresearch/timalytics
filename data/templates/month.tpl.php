<div class="container">
<div class="row">
    <div class="col-md-4">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr>
                <th>Tag</th>
                <th>Soll</th>
                <th>Ist</th>
                <th>Diff</th>
            </tr>
            </thead>
            <tfoot>
<?php if ($thisMonth) {
    if ($sumRequiredUntilToday > $sumWorked) {
        $class = 'alert-danger';
    } else {
        $class = 'alert-success';
    }
    $totalWorked = $sumWorked;
    $totalDiff   = $sumWorked - $sumRequiredUntilToday;
?>
                <tr class="<?php echo $class; ?>">
                    <th>Bisher</th>
                    <th class="r"><?php echo $sumRequiredUntilToday; ?></th>
                    <th class="r"><?php echo getPrettyWorkingTime($totalWorked); ?></th>
                    <th class="r"><?php echo getPrettyWorkingTime($totalDiff, true); ?></th>
                </tr>
                <tr>
                    <th colspan="4">
                        <progress style="width:100%"
                               max="<?php echo $sumRequiredUntilToday; ?>"
                               value="<?php echo $sumWorked; ?>"
                               title="Bisher"><?php echo $totalWorked; ?></progress>
                    </th>
                </tr>
<?php } ?>
<?php
if ($sumRequired > $sumWorked) {
    $class = 'alert-danger';
} else {
    $class = 'alert-success';
}

$totalWorked = $sumWorked;
$totalDiff   = $sumWorked - $sumRequired;
?>
                <tr class="<?php echo $class; ?>">
                    <th>Monat</th>
                    <th class="r"><?php echo $sumRequired; ?></th>
                    <th class="r"><?php echo getPrettyWorkingTime($totalWorked); ?></th>
                    <th class="r"><?php echo getPrettyWorkingTime($totalDiff, true); ?></th>
                </tr>
                <tr>
                    <th colspan="4">
                        <progress style="width:100%"
                               max="<?php echo $sumRequired; ?>"
                               value="<?php echo $sumWorked; ?>"
                               title="Gesamt"><?php echo $totalWorked; ?></progress>
                    </th>
                </tr>
            </tfoot>
            <tbody>
<?php
$max_work = 0.0;
foreach ($days as $day) {
    $max_work = max($max_work, $day['worked']);
}
$high = min(12, $max_work);

foreach ($days as $day) {
    $class = '';
    if ($day['future']) {
    } else if ($day['worked'] > (1.12 * $day['required'])) {
        $class = 'alert-warning';
    } else if ($day['required'] < $day['worked']) {
        $class = 'alert-success';
    } else if ($day['required'] == $day['worked']) {
    } else {
        $class = 'alert-danger';
    }
    $mainclass = '';
    if ($day['dow'] >= 6 || $day['holiday']) {
        $mainclass = 'muted';
    }
    if ($day['dow'] == 7) {
        $mainclass .= ' sonntag';
    }

    $worked = $day['worked'];
    $diff   = $day['worked'] - $day['required'];

    echo "<tr class='$mainclass'>"
        . '<td><nobr>' . $day['date'] . '</nobr></td>';
    if ($day['holiday'] && $day['worked'] == 0.0) {
        echo '<td colspan="4">' . $day['name'] . '</td>';
    } else {
        echo '<td class="r">' . $day['required'] . '</td>'
            . '<td class="r">' . getPrettyWorkingTime($worked) . '</td>'
            . "<td class='r $class'>" . (($day['worked'] - $day['required']) != 0.0 ? getPrettyWorkingTime($diff, true) : '') . '</td>';
    }
    echo '</tr>';
}

if (!$pretty) {
    $totalWorked = sprintf('%.2f', $sumWorked);
} else {
    $totalWorked = getPrettyWorkingTime($sumWorked);
}
?>
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <table class="table table-bordered table-hover table-condensed">
<?php
$class = '';
if ($thisMonth) {
    require 'zusammenfassung.tpl.php';
} else {
    if ($sumRequired > $sumWorked) {
        $class = 'alert-danger';
    } else {
        $class = 'alert-success';
    }
    $monthDiff = $sumWorked - $sumRequired;
    if ($plusminusHours !== null) {
        $totalDiff = $sumWorked - $sumRequired + $plusminusHours;
        $classPlusminus = $plusminusHours > 0 ? 'alert-success' : 'alert-danger';
        $classTotal = $totalDiff > 0 ? 'alert-success' : 'alert-danger';
    }
    ?>
    <?php if ($plusminusHours !== null) { ?>
            <tr>
                <th>Übertrag</th>
                <td class="r"></td>
                <td class="r <?php echo $classPlusminus;?>"><?php echo getPrettyWorkingTime($plusminusHours, true); ?></td>
            </tr>
    <?php } ?>
            <tr>
                <th>Soll&#160;Monat</th>
                <td class="r"><?php echo $sumRequired; ?></td>
                <td class="r <?php echo $class;?>"><?php echo getPrettyWorkingTime($monthDiff, true); ?></td>
            </tr>
    <?php if ($plusminusHours !== null) { ?>
            <tr class="<?php echo $classTotal;?>">
                <th>Gesamt</th>
                <td class="r"></td>
                <td class="r"><?php echo getPrettyWorkingTime($totalDiff, true); ?></td>
            </tr>
    <?php }
}
?>
        </table>

    </div>
    <div class="col-md-4">

        <p style="text-align: center; width:100%">
            <a href="<?php echo htmlspecialchars($urlPrev); ?>">&lt;&lt; zurück</a>
            &nbsp;&nbsp;Monat&nbsp;&nbsp;
            <a href="<?php echo htmlspecialchars($urlNext); ?>">vor &gt;&gt;</a>
        </p>

        <h4>Parameter</h4>
        <ul>
            <li>user</li>
            <li>month</li>
            <li>year</li>
            <li>hoursPerDay</li>
            <li>pretty (0 oder 1)</li>
        </ul>

        <p>
            Code at
            <a href="https://github.com/netresearch/timalytics">timalytics</a>.
        </p>

<?php if ($pmRowThisMonth === false && $plusminusHours !== null && date('m') != $month) { ?>
        <br/>
        <br/>
        <h4>Stundenmeldung</h4>
        <p>
            Wenn der Monat zu Ende ist, können hier die Plus/Minusstunden in die
            Datenbank übertragen werden.
            Sie werden dann für die Berechnung im nächsten Monat herangezogen.
        </p>
        <form class="form-inline" method="post" action="<?php echo htmlspecialchars($urlThis); ?>">
            <input name="report" type="hidden" value="1"/>
            <input name="minutes" type="text" class="input-small" value="<?php echo round($monthDiff * 60);?>" style="text-align: right"/> Minuten
            <button type="submit" class="btn">Melden</button>
        </form>
        <p>
	   <?php echo round($monthDiff * 60); ?> Minuten sind <?php echo getPrettyWorkingTime($monthDiff, true); ?>.
        </p>
<?php } ?>

        </div>
    </div><!-- /row -->
</div><!-- /container -->
