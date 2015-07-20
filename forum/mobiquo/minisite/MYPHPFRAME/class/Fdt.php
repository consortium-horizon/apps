<?php

/**
 * 字段定义类
 * 
 * @since  2012-7-18
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class Fdt {
    
    public static $df = array();     /* 定义数组 */
    
    /**
     * 返回对应的字段定义值
     *
     * @param  String  $fdtPath
     * @return  fixed  如果已经定义则返回之，否则报错
     */
    public static function getFdt($fdtPath) {
        $arr = explode(".", $fdtPath);
        $count = count($arr);
        if (is_array($arr) && $count > 0) {
            switch ($count) {
                case 1:
                    //if (isset(self::$df[$arr[0]])) {
                    if (is_array(self::$df) && array_key_exists($arr[0], self::$df)) {
                        return self::$df[$arr[0]];
                    }
                break;
                case 2:
                    //if (isset(self::$df[$arr[0]][$arr[1]])) {
                    if (is_array(self::$df[$arr[0]]) && array_key_exists($arr[1], self::$df[$arr[0]])) {
                        return self::$df[$arr[0]][$arr[1]];
                    }
                break;
                case 3:
                    //if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]])) {
                    if (is_array(self::$df[$arr[0]][$arr[1]]) && array_key_exists($arr[2], self::$df[$arr[0]][$arr[1]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]];
                    }
                break;
                case 4;
                    //if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]])) {
                    if (is_array(self::$df[$arr[0]][$arr[1]][$arr[2]]) && array_key_exists($arr[3], self::$df[$arr[0]][$arr[1]][$arr[2]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]];
                    }
                break;
                case 5;
                    //if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]])) {
                    if (is_array(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]]) && array_key_exists($arr[4], self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]];
                    }
                break;
                case 6;
                    //if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]])) {
                    if (is_array(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]]) && array_key_exists($arr[5], self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]];
                    }
                break;
                case 7;
                    //if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]])) {
                    if (is_array(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]]) && array_key_exists($arr[6], self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]];
                    }
                break;
                case 8;
                    //if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]][$arr[7]])) {
                    if (is_array(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]]) && array_key_exists($arr[7], self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]][$arr[7]];
                    }
                break;
                case 9;
                    //if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]][$arr[7]][$arr[8]])) {
                    if (is_array(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]][$arr[7]]) && array_key_exists($arr[8], self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]][$arr[7]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]][$arr[7]][$arr[8]];
                    }
                break;
                case 10;
                    //if (isset(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]][$arr[7]][$arr[8]][$arr[9]])) {
                    if (is_array(self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]][$arr[7]][$arr[8]]) && array_key_exists($arr[9], self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]][$arr[7]][$arr[8]])) {
                        return self::$df[$arr[0]][$arr[1]][$arr[2]][$arr[3]][$arr[4]][$arr[5]][$arr[6]][$arr[7]][$arr[8]][$arr[9]];
                    }
                break;
                default:
                break;
            }
            Error::alert('getFdt', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find field definition $fdtPath!", ERR_TOP);
        } else {
            Error::alert('getFdt', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find field definition $fdtPath!", ERR_TOP);
        }
    }
  
}

?>