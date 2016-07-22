<?php

function printSummarizeRow($arData, $nLevel = 0)
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

    <tr class="<?php echo $style; ?> level<?php echo $nLevel; ?>">
        <td><?php
            if (! empty($arData['customer_cnt'])) {
                echo '<div class="tooltip" data-toggle="popover" title="Please fix" data-content="This ticket is booked on ' . (1 + $arData['customer_cnt']) . ' different customers.">';
                echo '<span class="badge alert-danger">' . (1 + $arData['customer_cnt']) . '</span>';
                echo '</div>';
                // no - i do not know how to fix this in a better way
                echo '<span class="badge alert-danger">' . (1 + $arData['customer_cnt']) . '</span>';
            }
            if (! empty($arData['project_cnt'])) {
                echo '<div class="tooltip" data-toggle="popover" title="Please fix" data-content="This ticket is booked on ' . (1 + $arData['project_cnt']) . ' different projects.">';
                echo '<span class="badge alert-warning">' . (1 + $arData['project_cnt']) . '</span>';
                echo '</div>';
                // no - i do not know how to fix this in a better way
                echo '<span class="badge alert-warning">' . (1 + $arData['project_cnt']) . '</span>';
            }
            echo str_repeat('&nbsp;', $nLevel);
            if (! empty($arData['url'])) {
                echo '<a href="' . hs($arData['url']) . '">';
            }
            echo $arData['name'];
            if (! empty($arData['url'])) {
                echo '</a>';
            }
        ?></td>
        <td class="r"><?php echo formatTime($arData['estimation']); ?></td>
        <td class="r"><?php echo formatTime($arData['duration']); ?></td>
        <td class="r"><?php echo formatTime($arData['duration_sum']); ?></td>
        <?php if (empty($arData['duration_sum'])): ?>
            <td class="r"></td>
            <td class="r"></td>
            <td class="r"></td>
        <?php elseif (($arData['estimation'] - $arData['duration_sum']) > 0): ?>
            <td class="r"><?php echo formatPercent($percent); ?></td>
            <td class="r"><?php echo formatTime($arData['estimation'] - $arData['duration_sum']); ?></td>
            <td class="r"></td>
        <?php else: ?>
            <td class="r"><?php echo formatPercent($percent); ?></td>
            <td class="r"></td>
            <td class="r"><?php echo formatTime($arData['duration_sum'] - $arData['estimation']); ?></td>
        <?php endif; ?>
    </tr>
<?php

    if (! empty($arData['entries'])) {
        $nLevel++;
        foreach ($arData['entries'] as $arEntries):
            printSummarizeRow($arEntries, $nLevel);
        endforeach;
    }

}

?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <table class="table table-bordered table-hover table-condensed">
                <colgroup>
                    <col width="50%">
                    <col width="12%">
                    <col width="12%">
                    <col width="2%">
                    <col width="12%">
                    <col width="12%">
                </colgroup>
                <thead>
                <tr>
                    <th>Kunde / Projekt / Ticket</th>
                    <th class="r">Geplant</th>
                    <th class="r">Aktuell</th>
                    <th class="r">Gesamt</th>
                    <th style="border-left: none;">&nbsp;</th>
                    <th class="r">Rest</th>
                    <th class="r">Überzug</th>
                </tr>
                </thead>
            <?php foreach ($arCustomers as $customer): ?>
                <tbody>
                    <?php
                    printSummarizeRow($customer, 1);
                    ?>
                </tbody>
            <?php endforeach; ?>
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
