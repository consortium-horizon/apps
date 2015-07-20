<?php

defined('MBQ_IN_IT') or exit;

/**
 * frame main program base class
 * 
 * @since  2012-7-2
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseMain {
    
    public static $oMbqCm;
    public static $oMbqConfig;
    public static $customConfig;    /* user custom config,defined in customConfig.php or customAdvConfig.php */
    public static $oMbqAppEnv;
    public static $oClk;  /* instance of class MbqClassLink */
    public static $oMbqCookie;
    public static $oMbqSession;
    public static $oMbqIo;
    public static $simpleV;   /* an empty MbqValue object for simple value initialization */
    
    /* please always use isJsonProtocol() or isXmlRpcProtocol() in your code,instead of directly access this property */
    public static $protocol;    /* xmlrpc/json */
    public static $module;  /* module name */
    public static $cmd; /* action command name,must unique in all actions. */
    public static $input;   /* input params array */
    
    public static $data;   /* data need return */
    public static $oAct;   /* action object */
    
    public static $oCurMbqEtUser;  /* current user obj after login. */

    public static function init() {
        self::$simpleV = new MbqValue();
        self::$oClk = new MbqClassLink();
        self::$oMbqConfig = new MbqConfig();
        self::$oMbqCm = self::$oClk->newObj('MbqCm');
        self::$oMbqAppEnv = self::$oClk->newObj('MbqAppEnv');
        self::$oMbqCookie = self::$oClk->newObj('MbqCookie');
        self::$oMbqSession = self::$oClk->newObj('MbqSession');
        self::$oMbqIo = self::$oClk->newObj('MbqIo');
    }
    
    /**
     * judge is using json protocol
     *
     * @return  Boolean
     */
    public static function isJsonProtocol() {
        return (self::$protocol == 'json') ? TRUE : FALSE;
    }
    
    /**
     * judge is using json protocol
     *
     * @return  Boolean
     */
    public static function isAdvJsonProtocol() {
        return (self::$protocol == 'advjson') ? TRUE : FALSE;
    }
    
    /**
     * judge is using xmlrpc protocol
     *
     * @return  Boolean
     */
    public static function isXmlRpcProtocol() {
        return (self::$protocol == 'xmlrpc') ? TRUE : FALSE;
    }
    
    /**
     * data input
     */
    public static function input() {
        self::$oMbqIo->input();
    }
    
    /**
     * init application environment
     */
    public static function initAppEnv() {
        self::$oMbqAppEnv->init();
    }
    
    /**
     * action
     */
    public static function action() {
    }

    
    /**
     * data output
     */
    public static function output() {
        self::$oMbqIo->output();
    }
    
    /**
     * judge if has login
     *
     * @return  Boolean
     */
    public static function hasLogin() {
        return self::$oCurMbqEtUser ? true : false;
    }
    
    /**
     * do something before output
     */
    public static function beforeOutPut() {
        if (MBQ_DEBUG) {
            self::$oMbqCm->writeMemLog();
        }
        @ ob_end_clean();
    }
    
    /**
     * regist shutdown function
     */
    public static function regShutDown() {
        if (MBQ_REG_SHUTDOWN && function_exists('mbqShutdownHandle') && function_exists('register_shutdown_function')) 
        register_shutdown_function('mbqShutdownHandle');
    }
  
}

?>