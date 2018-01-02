<?php
$files = glob(__DIR__ . '/../data/*.ics');

$arEvents = array();
foreach ($files as $file) {
    $lines = file($file);
    $event = false;
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line == 'BEGIN:VEVENT') {
            $event = true;
            $arEvent = array();
            continue;
        }
        if (!$event) {
            continue;
        }
        if ($line == 'END:VEVENT') {
            $event = false;
            $arEvents[$arEvent['date']] = $arEvent['title'];
        } else if (substr($line, 0, 19)  == 'DTSTART;VALUE=DATE:') {
            $date = substr($line, 19);
            $arEvent['date'] = substr($date, 0, 4)
                . '-' . substr($date, 4, 2) . '-' . substr($date, 6);
        } else if (substr($line, 0, 8)  == 'SUMMARY:') {
            $arEvent['title'] = substr($line, 8);
        }
    }
}

ksort($arEvents);

$file = __DIR__ . '/../data/feiertage.php';
file_put_contents(
    $file,
    '<?php return '
    . var_export($arEvents, true)
    . " ?>\n"
);
echo count($arEvents) . ' Feiertage nach ' . $file . " generiert\n";
?>