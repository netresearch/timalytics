<?php
/**
 * Disable Jira gadget caching for development: &ignoreCache=true
 *
 * @link https://developers.google.com/gadgets/
 * @link http://docs.opensocial.org/display/OSREF/Gadgets+XML+Reference
 * @link https://developer.atlassian.com/display/GADGETS/Gadget+Developer+Documentation
 */
require_once __DIR__ . '/../src/bootstrap.php';

header('Content-type: text/xml');

echo <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<Module>
  <ModulePrefs title="Auswertung Timetracker"
    author="Christian Weiske"
    author_email="christian.weiske@netresearch.de"
    description="Zeigt die Monatsübersicht des Timetrackers an"
    height="230"
  >
    <!--<Require feature="dynamic-height"/>-->
  </ModulePrefs>
  <UserPref name="user" display_name="User name" datatype="string"
            default_value="christian.weiske"/>
  <Content type="html">
    <![CDATA[
 <script type="text/javascript">
 var prefs = new _IG_Prefs();
 var userPref = prefs.getString("user");
 document.write('<iframe height="230" width="100%" frameborder="0" border="0" scrolling="0" src="https://{$_SERVER['HTTP_HOST']}/zusammenfassung.php?user=' + userPref + '">iframe is loading ...</iframe>');
 </script>
    ]]>
  </Content>
</Module>
XML;

?>
