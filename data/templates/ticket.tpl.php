<?php

function printTicketEntryRow($arData)
{
    $style   = '';
    $percent = 0;
    if (! emptY($arData['estimation'])) {
        $percent = 100 / $arData['estimation'] * $arData['duration_sum'];
    }

    if (! empty($arData['estimation']) && $arData['duration_sum'] > $arData['estimation']):
        $style = 'danger';
    elseif (round($percent) >= 90):
        $style = 'warning';
    endif;
    ?>

    <tr class="<?php echo $style; ?>">
        <td><?php echo $arData['user']; ?></td>
        <td><?php echo $arData['day']; ?></td>
        <td><?php echo $arData['start']; ?></td>
        <td><?php echo $arData['activity']; ?></td>
        <td><?php echo $arData['description']; ?></td>
        <td class="r"><?php echo formatTime($arData['duration']); ?></td>
    </tr>
<?php
}

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-condensed">
                <colgroup>
                    <col width="5%">
                    <col width="12%">
                    <col width="12%">
                    <col width="12%">
                    <col width="50%">
                    <col width="12%">
                </colgroup>
                <thead>
                <tr>
                    <th>User</th>
                    <th>Day</th>
                    <th>Start</th>
                    <th>Activity</th>
                    <th>Description</th>
                    <th class="r">Duration</th>
                </tr>
                </thead>
            <?php
            if (! empty($arEntries)) {
                $nDurationSum = 0;
                foreach ($arEntries as $arEntry) {
                    ?>
                    <tbody>
                    <?php
                    printTicketEntryRow($arEntry);
                    ?>
                    </tbody>
                    <?php
                }
            }
            ?>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $(".tooltip").popover({
            trigger: 'hover',
            container: 'body'
        });
    });
</script>
