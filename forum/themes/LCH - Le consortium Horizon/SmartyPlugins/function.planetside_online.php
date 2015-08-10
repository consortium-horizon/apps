<?php defined('APPLICATION') or die();
/**
 * Returns a link to "Participated Discussions" (for logged in users).
 * Accepts an associative array with following options:
 * By default the link is wrapped in an <li> element - you could specify another html here ("" will surpress any wrap).
 * "text" is the text that is displayed as the links text.
 * With "format" you could fine tune the html that is created.
 *
 * @param mixed $params Array: [wrap|text|format].
 * @param object $smarty Smarty object.
 * @return string HTML of the Participated Link or empty string.
 */
function smarty_function_planetside_online($params, &$smarty) {
  if (file_exists ( "themes/LCH - Le consortium Horizon/SmartyPlugins/planetsideOnlineModule.html" )) {
    $file_contents = readfile( "themes/LCH - Le consortium Horizon/SmartyPlugins/planetsideOnlineModule.html" );
  } else {
    die();
  }


}
