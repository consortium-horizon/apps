<?php
/**
 * 错误处理类
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class Error {
    
    /* 错误信息条目对象二维数组，第一维是操作码，
    第二维是错误信息条目对象数组 */
    public static $errInfo = array();
    
    /* 与self::$errInfo对应的所有错误信息条目对象组成的一维数组 */
    public static $errInfoList = array();

    /**
     * 构造函数
     */
    public function __construct() {
    }
    
    /**
     * 判断是否有与$cmd对应的出错信息(不包括ERR_INFO级别信息)
     * 如果$cmd为NULL则表示判断在self::$errInfo中是否出错信息(不包括ERR_INFO级别信息)
     *
     * @param  String  $cmd  操作码，
     */
    public static function hasErr($cmd = NULL) {
        foreach (self::$errInfo as $key => $objsErrorInfo) {
            foreach ($objsErrorInfo as $oErrorInfo) {
                if (is_null($cmd)) {
                    if (!$oErrorInfo->isErrInfo()) {
                        return true;    /* 有出错信息 */
                    }
                } else {
                    if ($key == $cmd && !$oErrorInfo->isErrInfo()) {
                        return true;    /* 有出错信息 */
                    }
                }
            }
        }
        return false;   /* 没有出错信息 */
    }
    
    /**
     * 增加错误信息条目
     *
     * @param  String  $cmd  操作码
     * @param  String  $errKey  错误信息条目的key
     * @param  Mixed  $errValue  错误信息条目的内容，其值可以为一个一维数组
     * @param  String  $errDegree  错误级别
     */
    private static function addErr($cmd, $errKey, $errValue, $errDegree) {
        foreach (self::$errInfo as $key => $value) {
            if ($key == $cmd) {
                self::addErrItem($cmd, $errKey, $errValue, $errDegree);
                return true; /* 退出函数 */
            }
        }
        /* 错误信息数组中没有对应的cmd则创建新的操作码错误信息数组 */
        self::$errInfo[$cmd] = array();    /* 新的操作码错误信息数组 */
        self::addErrItem($cmd, $errKey, $errValue, $errDegree);
        return true;
    }
    
    /**
     * 增加一个错误信息条目
     *
     * @param  String  $cmd  操作码
     * @param  String  $errKey  错误信息条目的key
     * @param  Mixed  $errValue  错误信息条目的内容，其值可以为一个一维数组
     * @param  String  $errDegree  错误级别
     */
    private static function addErrItem($cmd, $errKey, $errValue, $errDegree) {
        if (is_array($errValue)) {
            foreach ($errValue as $value) {
                $oErrorInfo = new ErrorInfo();
                $oErrorInfo->init($cmd, $errKey, $value, $errDegree);
                self::$errInfo[$cmd][] = $oErrorInfo;
                self::$errInfoList[] = $oErrorInfo;
            }
        } else {
            $oErrorInfo = new ErrorInfo();
            $oErrorInfo->init($cmd, $errKey, $errValue, $errDegree);
            self::$errInfo[$cmd][] = $oErrorInfo;
            self::$errInfoList[] = $oErrorInfo;
        }
    }
    
    /**
     * 获取最后一个错误信息
     *
     * @param  String  $cmd  操作码
     * @return  Mixed
     */
    public static function getLatestErrorValue($cmd = NULL) {
        if (!$cmd) {
            $cmd = MainApp::$cmd;
        }
        if (isset(self::$errInfo[$cmd]) && self::$errInfo[$cmd]) {
            $tempArr = self::$errInfo[$cmd];
            while ($oErrorInfo = array_pop($tempArr)) {
                if (!$oErrorInfo->isErrInfo()) {
                    return $oErrorInfo->errValue;
                }
            }
        }
        return false;   //found no error
    } 
   
    /**
     * 显示错误信息
     *
     * @param  String  $errKey  错误信息条目的key；
     * @param  Mixed  $errInfo  错误信息字符串或数组；对于ajax请求返回的是错误提示信息字符串（在对话框中显示这个信息）；
     * @param  String  $errDegree  错误级别
     * @param  Array  $retData  返回的附加数据信息数组，用于ajax返回；
     * 错误级别分为：
     * 1、顶级：ERR_TOP，最严重的错误，需立即停止程序，直接用die()函数输出错误信息；
     * 2、严重级：ERR_HIGH，严重的错误，需立即停止程序，用smarty模板输出错误信息；
     * 3、普通级：ERR_APP，应用程序中普通的错误，并不停止程序运行，用smarty模板输出错误信息；
     * 4、信息级：ERR_INFO，应用程序中输出一些辅助信息（例如操作成功之类的），并不停止程序运行，用smarty模板输出信息；
     */
    public static function alert($errKey = '', $errInfo = 'error!', $errDegree = ERR_HIGH, $retData = array()) {
        /* 操作码 */
        $cmd = MainApp::$cmd;
        if (MainApp::$isAjax) {
            MainApp::$oCf->pageReturn('', '', '', '', '', array('status' => $errDegree, 'info' => $errInfo, 'returnData' => $retData));
        } else {
            if ($errKey == '') {
                $errKey = '_default_error_key_';   /* 默认的错误信息条目的key */
            }
            switch ($errDegree) {
                case ERR_TOP:
                    self::addErr($cmd, $errKey, $errInfo, $errDegree);
                    foreach (self::$errInfoList as $oErrorInfo) {
                        if (!$oErrorInfo->isErrInfo()) {
                            /* echo MainApp::$oCf->cv($oErrorInfo->errValue) . '<br>'; */
                            echo $oErrorInfo->errValue;
                        }
                    }
                    die();
                    break;
                case ERR_HIGH:
                    self::addErr($cmd, $errKey, $errInfo, $errDegree);
                    MainApp::setTpl('public_error.html');
                    MainApp::cmEnd();
                    MainApp::display();
                    die();
                    break;
                case ERR_APP:
                    self::addErr($cmd, $errKey, $errInfo, $errDegree);
                    break;
                case ERR_INFO:
                    self::addErr($cmd, $errKey, $errInfo, $errDegree);
                    break;
                default:
                    die(__METHOD__ . ',line:' . __LINE__ . '.' . 'Invalid errDegree!');
                    break;
            }
        }
    }
  
}




/**
 * 错误信息条目类
 */
Class ErrorInfo {
    
    public $cmd;   /* 所属的cmd操作码 */
    public $errKey;     /* 错误信息条目的key */
    public $errValue;   /* 错误信息字符串 */
    public $errDegree;     /* 错误级别 */
    
    /**
     * 构造函数
     */
    public function __construct() {
    }
    
    /**
     * 初始化
     */
    public function init($cmd, $errKey, $errValue, $errDegree) {
        $this->cmd = $cmd;
        $this->errKey = $errKey;
        $this->errValue = $errValue;
        $this->errDegree = $errDegree;
    }
    
    /**
     * 判断是否是ERR_INFO级别错误信息
     */
    public function isErrInfo() {
        if ($this->errDegree == ERR_INFO) {
            return true;
        } else {
            return false;
        }
    }
    
}
?>