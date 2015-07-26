<?php
require_once dirname(__FILE__).'/accesscheck.php';

foreach ($GLOBALS['plugins'] as $pluginname => $plugin) {
  $plugin->logout();
}

// maybe some other time, this is a bit OTT
#if (!empty($_SESSION['logout_error'])) {
#    Warn($_SESSION['logout_error']);
#}

if (isset($_GET['err'])) {
    switch ($_GET['err']) {
        case "1":
           Info(s('You have been logged out, because the session token of your request was incorrect'),true);
           print PageLinkButton('home',s('Continue'));
           break;
    }
}

$_SESSION["adminloggedin"] = "";
$_SESSION["logindetails"] = "";
session_destroy();
