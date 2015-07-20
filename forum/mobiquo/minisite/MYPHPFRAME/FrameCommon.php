<?php
/** 
 * 框架公共包含文件 
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */

define('MPFFRAME_CLASS_PATH', dirname(__FILE__).'/class/');   /* 框架类目录路径 */
require_once(MPFFRAME_CLASS_PATH . 'CF.php');
$oCf = new CF();    /* 公共函数对象 */
define('SMARTY_DIR', dirname(__FILE__) . '/Smarty/libs/');     /* smarty库文件目录，必须以'/'结尾。 */

/* 错误常量（ref Error class） */
define('ERR_TOP', 1); /* 最严重的错误 */
define('ERR_HIGH', 3); /* 严重的错误 */
define('ERR_APP', 5); /* 应用程序中普通的错误 */
define('ERR_INFO', 7); /* 应用程序中输出一些辅助信息，用于处理成功的情况 */

/* 包含保留类 */
require_once($oCf->getPath(MPFFRAME_CLASS_PATH) . 'MainApp.php');
require_once($oCf->getPath(MPFFRAME_CLASS_PATH) . 'Select.php');
require_once($oCf->getPath(MPFFRAME_CLASS_PATH) . 'ClassLink.php');
require_once($oCf->getPath(SMARTY_DIR) . 'Smarty.class.php');     /* Smarty类 */

?>