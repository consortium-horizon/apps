<?php
verifyCsrfGetToken();

$status = 'FAIL';
$disabled_plugins = unserialize(getConfig('plugins_disabled'));

if (isset($_GET['disable'])) {
  if (isset($GLOBALS['plugins'][$_GET['disable']])) {
    $disabled_plugins[$_GET['disable']] = 1;
  }
  saveConfig('plugins_disabled',serialize($disabled_plugins),0);
  $status = $GLOBALS['img_cross'].'<script type="text/javascript">document.location = document.location; </script>';
} elseif (isset($_GET['enable'])) {
  if (pluginCanEnable($_GET['enable'])) {
    if (isset($disabled_plugins[$_GET['enable']])) {
      unset($disabled_plugins[$_GET['enable']]);
    }
    if (isset($GLOBALS['allplugins'][$_GET['enable']])) {
       $GLOBALS['allplugins'][$_GET['enable']]->initialise();
    }
  #  var_dump($disabled_plugins);
    saveConfig('plugins_disabled',serialize($disabled_plugins),0);
    $status = $GLOBALS['img_tick'].'<script type="text/javascript">document.location = document.location; </script>';
  }
} elseif (isset($_GET['initialise'])) {
  if (isset($GLOBALS['plugins'][$_GET['initialise']])) {
    $status = $GLOBALS['plugins'][$_GET['initialise']]->initialise();
  }
}
#var_dump($_GET);

return $status;
