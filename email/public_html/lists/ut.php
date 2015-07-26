<?php
ob_start();
$er = error_reporting(0); 
require_once dirname(__FILE__) .'/admin/inc/unregister_globals.php';
require_once dirname(__FILE__) .'/admin/inc/magic_quotes.php';

## none of our parameters can contain html for now
$_GET = removeXss($_GET);
$_POST = removeXss($_POST);
$_REQUEST = removeXss($_REQUEST);
$_COOKIE = removeXss($_COOKIE);

if (isset($_SERVER["ConfigFile"]) && is_file($_SERVER["ConfigFile"])) {
  include $_SERVER["ConfigFile"];
} elseif (is_file("config/config.php")) {
  include 'config/config.php';
} else {
  print "Error, cannot find config file\n";
  exit;
}

require_once dirname(__FILE__).'/admin/init.php';

$GLOBALS["database_module"] = basename($GLOBALS["database_module"]);
$GLOBALS["language_module"] = basename($GLOBALS["language_module"]);

require_once dirname(__FILE__).'/admin/'.$GLOBALS["database_module"];

# load default english and language
include_once dirname(__FILE__)."/texts/english.inc";
# Allow customisation per installation
if (is_file($_SERVER['DOCUMENT_ROOT'].'/'.$GLOBALS["language_module"])) {
  include_once $_SERVER['DOCUMENT_ROOT'].'/'.$GLOBALS["language_module"];
}

include_once dirname(__FILE__)."/admin/languages.php";
require_once dirname(__FILE__)."/admin/defaultconfig.php";
require_once dirname(__FILE__).'/admin/connect.php';
include_once dirname(__FILE__)."/admin/lib.php";

if (!empty($_GET["u"]) && !empty($_GET["m"])) {
  $_GET['u'] = preg_replace('/\W/','',$_GET['u']);
  $userid = Sql_Fetch_Row_Query(sprintf('select id from %s where uniqid = "%s"',
    $GLOBALS["tables"]["user"],$_GET["u"]));
  if ($userid[0]) {
    Sql_Query(sprintf('update %s set viewed = now() where messageid = %d and userid = %d',
      $GLOBALS["tables"]["usermessage"],$_GET["m"],$userid[0]));
    Sql_Query(sprintf('update %s set viewed = viewed + 1 where id = %d',
      $GLOBALS["tables"]["message"],$_GET["m"]));
  }
}

@ob_end_clean();
header("Content-Type: image/png");
print base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAABGdBTUEAALGPC/xhBQAAAAZQTFRF////AAAAVcLTfgAAAAF0Uk5TAEDm2GYAAAABYktHRACIBR1IAAAACXBIWXMAAAsSAAALEgHS3X78AAAAB3RJTUUH0gQCEx05cqKA8gAAAApJREFUeJxjYAAAAAIAAUivpHEAAAAASUVORK5CYII=');
