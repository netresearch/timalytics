<?php
require_once __DIR__ . '/../src/bootstrap.php';

if (!isset($_GET['name'])) {
    header('HTTP/1.0 412 user parameter missing');
    header('Content-type: text/plain');
    echo "GET parameter 'name' is missing\n";
    exit();
}
$user = $_GET['name'];

$res = $db->query(
    'SELECT entries.*'
    . ', projects.name as project_name'
    . ', customers.name as customer_name'
    . ', activities.name as activity_name'
    . ' FROM entries'
    . ' JOIN projects ON entries.project_id = projects.id'
    . ' JOIN customers ON entries.customer_id = customers.id'
    . ' JOIN activities ON entries.activity_id = activities.id'
    . ' JOIN users ON entries.user_id = users.id'
    . ' WHERE users.username = '
    . $db->quote($user)
    . ' ORDER BY day DESC, start DESC'
    . ' LIMIT 40',
    PDO::FETCH_ASSOC
);

$arSinceStandup = array();
$nShowStandup = 0;
if (isset($_GET['showStandup'])) {
    $nShowStandup = (int) $_GET['showStandup'];
}
$nStandups = 0;
foreach ($res as $arRow) {
    if ($arRow['project_name'] == 'Standup'
        || $arRow['activity_name'] == 'Standup'
        || $arRow['description'] == 'Tägl Standup Support/Projekt Team'
        || $arRow['description'] == 'StandUps'
        || $arRow['description'] == 'Stehung'
    ) {
        ++$nStandups;
        continue;
    }
    if ($nStandups == $nShowStandup) {
        $arSinceStandup[] = $arRow;
    }
}

$arProjects = array();
foreach ($arSinceStandup as $arRow) {
    $arRow['mood'] = 'none';
    $nProjectId = $arRow['project_id'];
    $arProjects[$nProjectId]['name'] = $arRow['project_name'];
    $arProjects[$nProjectId]['customers'][$arRow['customer_id']]
        = $arRow['customer_name'];
    $arProjects[$nProjectId]['activities'][$arRow['activity_id']]
        = $arRow['activity_name'];
    $arProjects[$nProjectId]['entries'][$arRow['id']]
        = $arRow;
}
foreach ($arProjects as &$arProject) {
    $arProject['allcustomers'] = implode(', ', $arProject['customers']);
    //sort and group entries with same issue number + description
    usort(
        $arProject['entries'],
        function($a, $b) {
            $c = strnatcasecmp($a['ticket'], $b['ticket']);
            if ($c == 0) {
                $c = strcasecmp($a['description'], $b['description']);
            }
            return $c;
        }
    );

    $remove = array();
    $lastKey = '';
    $lastText = '';
    foreach ($arProject['entries'] as $id => &$entry) {
        $entry['count'] = 1;
        $text = $entry['ticket'] . '/' . $entry['description'];
        if ($text == $lastText) {
            $remove[] = $id;
            $arProject['entries'][$lastKey]['count']++;
        } else {
            $lastKey  = $id;
            $lastText = $text;
        }
    }
    foreach ($remove as $key) {
        unset($arProject['entries'][$key]);
    }
}
usort(
    $arProjects,
    function($a, $b) { return strcasecmp($a['name'], $b['name']); }
);

$moodmap = array(
    'c' => 'cool',
    'l' => 'solala',
    's' => 'sucks',
);
foreach ($arProjects as &$arProject) {
    foreach ($arProject['entries'] as &$entry) {
        if (substr($entry['description'], -2, 1) == '#') {
            $entry['mood'] = $moodmap[substr($entry['description'], -1, 1)];
            $entry['description'] = trim(substr($entry['description'], 0, -2));
        }
    }
}


Twig_Autoloader::register();
$twig = new Twig_Environment(
    new Twig_Loader_Filesystem(__DIR__ . '/../data/templates/')
);
echo $twig->render(
    'user.twig',
    array(
        'arProjects'    => $arProjects,
        'user'          => $user,
        'prettyname'    => getPrettyName($user),
        'arActiveUsers' => getActiveUsers($db),
        'showStandup'   => $nShowStandup,
        'cfg'           => $GLOBALS['cfg'],
    )
);
?>
