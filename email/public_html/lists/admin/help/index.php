<?php
ob_start();
error_reporting(0);
if (!empty($_SERVER["ConfigFile"]) && is_file($_SERVER["ConfigFile"])) {
#  print '<!-- using '.$_SERVER["ConfigFile"].'-->'."\n";
  include $_SERVER["ConfigFile"];
} elseif (is_file("../../config/config.php")) {
#  print '<!-- using ../../config/config.php-->'."\n";
  include "../../config/config.php";
} else {
  print "Error, cannot find config file\n";
  exit;
}
$now =  gettimeofday();
$GLOBALS["pagestats"] = array();
$GLOBALS["pagestats"]["time_start"] = $now["sec"] * 1000000 + $now["usec"];
$GLOBALS["pagestats"]["number_of_queries"] = 0;

if (!isset($systemroot)) {
  $systemroot = dirname(__FILE__).'/..';
}
require_once $systemroot.'/init.php';
require_once $systemroot.'/'.$GLOBALS["database_module"];
require_once $systemroot."/../texts/english.inc";
include_once $systemroot."/../texts/".$GLOBALS["language_module"];
include_once $systemroot."/languages.php";
require_once $systemroot."/defaultconfig.php";
require_once $systemroot.'/connect.php';
require_once $systemroot."/commonlib/lib/interfacelib.php";
include_once $systemroot."/ui/".$GLOBALS['ui']."/pagetop_minimal.php";
# record the start time(usec) of script

if (!isset($_GET["topic"]))
  $topic = "home";
else
  $topic = $_GET["topic"];

preg_match("/([\w_]+)/",$topic,$regs);
$topic = $regs[1];
$include = '';
$en_include = '';
$topic = basename($topic);
if ($topic) {
  if (is_file($_SESSION['adminlanguage']['iso'].'/'.$topic.".php")) {
    $include = $_SESSION['adminlanguage']['iso'].'/'.$topic . ".php";
  }
  if (is_file('en/'.$topic.".php")) {
    $en_include = 'en/'.$topic . ".php";
  }
}

?>
<title>help</title>
</head>
<body>
<!-- content -->
<?php
print "<h3>phplist Help: $topic</h3>";
if ($include) {
  include $include;
} else {
  if ($en_include && is_file($en_include)) {
    print $GLOBALS['I18N']->get('Sorry, this help topic does not exist in your language. Below is the english version.')." ".'<i>'.$topic.'</i><hr/>';
    
    include $en_include;
  } else {
    print $GLOBALS['I18N']->get('Sorry, this help topic does not exist in your language.')." ".'<i>'.$topic.'</i>';
  }
}

ob_end_flush();

print '</body></html>';

