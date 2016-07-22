<?php
    $sumRequiredToday = $days[$today]['required'];

    if ($sumRequiredUntilToday > $sumWorked) {
        $class = 'alert-error';
    } else {
        $class = 'alert-success';
    }
    if ($sumRequiredUntilYesterday > $sumWorkedUntilYesterday) {
        $classYesterday = 'alert-error';
    } else {
        $classYesterday = 'alert-success';
    }
    if ($sumRequiredToday > $days[$today]['worked']) {
        $classToday = 'alert-error';
    } else {
        $classToday = 'alert-success';
    }

    $monthDiff = $sumWorked - $sumRequiredUntilToday;
    $monthDiffToday = $days[$today]['worked'] - $days[$today]['required'];
    $monthDiffYesterday = $sumWorkedUntilYesterday - $sumRequiredUntilYesterday;

if ($plusminusHours !== null) {
    $classPlusminus = $plusminusHours > 0 ? 'alert-success' : 'alert-error';
    $totalDiff = $sumWorked - $sumRequiredUntilToday + $plusminusHours;
    $classTotal = $totalDiff > 0 ? 'alert-success' : 'alert-error';
}
?>
    <thead>
     <tr>
      <th>&#160;</th>
      <th>Soll</th>
      <th>Gearbeitet</th>
      <th>Diff</th>
     </tr>
    </thead>
    <tbody>
<?php if ($plusminusHours !== null) { ?>
     <tr>
      <th>Übertrag</th>
      <td class="r"></td>
      <td class="r"><?php echo getPrettyWorkingTime($plusminusHours); ?></td>
      <td class="r <?php echo $classPlusminus;?>"><?php echo getPrettyWorkingTime($plusminusHours, true); ?></td>
     </tr>
<?php } ?>
     <tr>
      <th>bis&#160;gestern</th>
      <td class="r"><?php echo $sumRequiredUntilYesterday; ?></td>
      <td class="r"><?php echo getPrettyWorkingTime($sumWorkedUntilYesterday); ?></td>
      <td class="r <?php echo $classYesterday;?>"><?php echo getPrettyWorkingTime($monthDiffYesterday, true); ?></td>
     </tr>
     <tr>
      <th>heute</th>
      <td class="r"><?php echo $sumRequiredToday; ?></td>
      <td class="r"><?php echo getPrettyWorkingTime($days[$today]['worked']); ?></td>
      <td class="r <?php echo $classToday;?>"><?php echo getPrettyWorkingTime($monthDiffToday, true); ?></td>
     </tr>
     <tr>
      <th>Monat</th>
      <td class="r"><?php echo $sumRequiredUntilToday; ?></td>
      <td class="r"><?php echo getPrettyWorkingTime($sumWorked); ?></td>
      <td class="r <?php echo $class;?>"><?php echo getPrettyWorkingTime($monthDiff, true); ?></td>
     </tr>
<?php if ($plusminusHours !== null) { ?>
     <tr class="<?php echo $classTotal;?>">
      <th>Summe</th>
      <td class="r"></td>
      <td class="r"></td>
      <td class="r"><?php echo getPrettyWorkingTime($totalDiff, true); ?></td>
     </tr>
<?php } ?>
    </tbody>