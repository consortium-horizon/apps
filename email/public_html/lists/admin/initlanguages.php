<?php

if (!defined('PHPLISTINIT')) die();

if (empty($GLOBALS['commandline'])) {
  print $GLOBALS['I18N']->get("This page can only be called from the commandline");
  return;
}
ob_end_clean();
print ClineSignature();
ob_start();

$locale_root = dirname(__FILE__).'/locale/';

if (is_dir($locale_root)) {
  $dir = opendir($locale_root);
  while ($lan = readdir($dir)) {
    if (is_file($locale_root.'/'.$lan.'/phplist.po')) {
      cl_output($lan);
      $lastUpdate = getConfig('lastlanguageupdate-'.$lan);
      $thisUpdate = filemtime($locale_root.'/'.$lan.'/phplist.po');
      if ($thisUpdate > $lastUpdate) {
        cl_output(s('Initialising language').' '.$lan);
        $GLOBALS['I18N']->initFSTranslations($lan);
      } else {
        cl_output(s('Up to date'));
      }
    }
  }
}
