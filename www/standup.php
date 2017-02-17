<?php
require_once __DIR__ . '/../src/bootstrap.php';

$res = $db->query(
    'SELECT teams.name as teamname'
    . ', users.username as username, users.id as userid'
    . ' FROM users'
    . ' JOIN teams_users ON users.id = teams_users.user_id'
    . ' JOIN teams ON teams_users.team_id = teams.id'
    . ' ORDER BY teamname, username',
    PDO::FETCH_ASSOC
);

$arTeams = array();
foreach ($res as $row) {
    if (isset($GLOBALS['inactive-users'][$row['username']])) {
        continue;
    }
    $arTeams[$row['teamname']][] = array(
        'userid'     => $row['userid'],
        'name'       => $row['username'],
        'prettyname' => getPrettyName($row['username'])
    );
}

Twig_Autoloader::register();
$twig = new Twig_Environment(
    new Twig_Loader_Filesystem(__DIR__ . '/../data/templates/')
);
echo $twig->render('teams.twig', array('arTeams' => $arTeams, 'cfg' => $GLOBALS['cfg']));
?>
