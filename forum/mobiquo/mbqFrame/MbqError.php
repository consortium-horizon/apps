<?php

defined('MBQ_IN_IT') or exit;

/**
 * error handle
 * 
 * @since  2012-7-2
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqError {

    public function __construct() {
    }
   
    /**
     * echo error info
     *
     * @param  String  $errTitle  error title
     * @param  Mixed  $errInfo  error info
     * @param  Array  $data  other data need return
     * @param  String  $errDegree  error degree(MBQ_ERR_TOP|MBQ_ERR_HIGH|MBQ_ERR_NOT_SUPPORT|MBQ_ERR_APP|MBQ_ERR_INFO|MBQ_ERR_TOP_NOIO)
     * @param  Boolean  $stop  need stop the program immediately flag,it often is true value in plugin development.
     */
    public static function alert($errTitle = '', $errInfo = '', $data = '', $errDegree = MBQ_ERR_TOP, $stop = TRUE) {
        if (!$errInfo) $errInfo = MBQ_ERR_DEFAULT_INFO;
        switch ($errDegree) {
            case MBQ_ERR_TOP:
                MbqMain::$oMbqIo->alert($errInfo, false, $errDegree);
                exit;
                break;
            case MBQ_ERR_HIGH:
                self::alert('', 'Not support MBQ_ERR_HIGH now!');
                exit;
                break;
            case MBQ_ERR_NOT_SUPPORT:
                MbqMain::$oMbqIo->alert($errInfo, false, $errDegree);
                exit;
                break;
            case MBQ_ERR_APP:
                MbqMain::$oMbqIo->alert($errInfo, false, $errDegree);
                exit;
                break;
            case MBQ_ERR_INFO:
                MbqMain::$oMbqIo->alert($errInfo, true, $errDegree);
                exit;
                break;
            case MBQ_ERR_TOP_NOIO:
                echo $errInfo;
                exit;
                break;
            default:
                MbqMain::$oMbqIo->alert($errInfo, false, $errDegree);   //for json Error Response
                exit;
                break;
        }
    }
  
}

?>