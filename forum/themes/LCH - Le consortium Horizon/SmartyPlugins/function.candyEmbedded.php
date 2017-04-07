<?php defined('APPLICATION') or die();
function smarty_function_candy_embedded($params, &$smarty) {
  if (file_exists ( "themes/LCH - Le consortium Horizon/SmartyPlugins/candyEmbedded.html" )) {
      $file_contents = readfile( "themes/LCH - Le consortium Horizon/SmartyPlugins/candyEmbedded.html" );
  } else {
      die();
  }
}
