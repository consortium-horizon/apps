<?php
define('IN_MOBIQUO', true);
if (isset($_GET['welcome']))
{
    include('./smartbanner/app.php');
    exit;
}

require_once('mobiquoCommon.php');

MbqMain::init();  /* frame init */
MbqMain::input();     /* handle input data */
require_once(MBQ_PATH.'IncludeBeforeMbqAppEnv.php');
MbqMain::initAppEnv();    /* application environment init */
@ ob_start();
MbqMain::action();    /* main program handle */
MbqMain::beforeOutput();  /* do something before output */
MbqMain::output();    /* handle output data */

?>