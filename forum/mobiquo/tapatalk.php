<?php

define('MBQ_PROTOCOL', 'json');   //define is using json protocol,if not defined(default) means is using xmlrpc protocol

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