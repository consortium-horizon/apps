<?php

define('MBQ_IN_IT', true);  /* is in mobiquo flag */
define('MBQ_DEBUG', false);  /* is in debug mode flag */
define('MBQ_REG_SHUTDOWN', true);  /* register shutdown function flag */

if (MBQ_DEBUG) {
    ini_set('display_errors','1');
    ini_set('display_startup_errors','1');
    //error_reporting(E_ALL);
    error_reporting(E_ALL ^ E_NOTICE);
} else {    // Turn off all error reporting
    error_reporting(0);
}
set_time_limit(120);

require_once('MbqConfig.php');

/**
 * frame main program
 * 
 * @since  2012-7-2
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqMain extends MbqBaseMain {

    public static function init() {
        parent::init();
        self::$oMbqCm->changeWorkDir('..');  /* change work dir to parent dir.Important!!! */
        self::regShutDown();
    }
    
    /**
     * action
     */
    public static function action() {
        parent::action();
        if (self::hasLogin()) {
            header('Mobiquo_is_login: true');
        } else {
            header('Mobiquo_is_login: false');
        }
        self::$oMbqConfig->calCfg();    /* you should do some modify within this function in multiple different type applications! */
        if (!self::$oMbqConfig->pluginIsOpen()) {
            MbqError::alert('', "Plugin is not in service!");
        }
        if (isset($_GET['method_name']) && $_GET['method_name']) {     //for more flexibility
            self::$cmd = $_GET['method_name'];
        }
        if (isset($_POST['method_name']) && $_POST['method_name']) {    //for upload_attach and other post method
            self::$cmd = $_POST['method_name'];
            foreach ($_POST as $k => $v) {
                self::$input[$k] = $v;
            }
        }
        if (self::$cmd) {
            self::$cmd = (string) self::$cmd;
            //MbqError::alert('', self::$cmd);
            if (preg_match('/[A-Za-z0-9_]{1,128}/', self::$cmd)) {
                $arr = explode('_', self::$cmd);
                foreach ($arr as &$v) {
                    $v = ucfirst(strtolower($v));
                }
                $actionClassName = 'MbqAct'.implode('', $arr);
                if (self::$oClk->hasReg($actionClassName)) {
                    self::$oAct = self::$oClk->newObj($actionClassName);
                    self::$oAct->actionImplement();
                } else {
                    //MbqError::alert('', "Not support action for ".self::$cmd."!", '', MBQ_ERR_NOT_SUPPORT);
                    MbqError::alert('', "Sorry!This feature is not available in this forum.Method name:".self::$cmd, '', MBQ_ERR_NOT_SUPPORT);
                }
            } else {
                MbqError::alert('', "Need valid cmd!");
            }
        } else {
            MbqError::alert('', "Need not empty cmd!");
        }
    }
    
    /**
     * do something before output
     */
    public static function beforeOutPut() {
        parent::beforeOutput();
    }
    
}

?>