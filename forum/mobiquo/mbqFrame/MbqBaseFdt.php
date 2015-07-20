<?php

defined('MBQ_IN_IT') or exit;

/**
 * field definition base class
 * 
 * @since  2012-7-18
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseFdt {
    
    public static $df = array();     /* definition array */
    
    /**
     * return corresponding field definition value
     *
     * @param  String  $fdtPath
     * @return  fixed  if is set return the corresponding field definition value,else alert error info.
     */
    public static function getFdt($fdtPath) {
        $arr = explode(".", $fdtPath);
        $count = count($arr);
        if (is_array($arr) && $count > 0) {
            switch ($count) {
                case 1:
                    if (isset(self::$df[$arr[0]])) {
                        return self::$df[$arr[0]];
                    }
                break;
                case 2:
                    if (isset(self::$df[$arr[0]][$arr[1]])) {
                        return self::$df[$arr[0]][$arr[1]];
                    }
                break;
                case 3:
                    if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]];
                    }
                break;
                case 4;
                    if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]];
                    }
                break;
                case 5;
                    if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]];
                    }
                break;
                case 6;
                    if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]];
                    }
                break;
                default:
                break;
            }
            if (defined('MPF_IN_IT') && MPF_IN_IT)
                Error::alert('mpf', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find field definition $fdtPath!", ERR_TOP);   //for mpf
            else
                MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find field definition $fdtPath!");
        } else {
            if (defined('MPF_IN_IT') && MPF_IN_IT)
                Error::alert('mpf', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find field definition $fdtPath!", ERR_TOP);   //for mpf
            else
                MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find field definition $fdtPath!");
        }
    }
  
}

?>