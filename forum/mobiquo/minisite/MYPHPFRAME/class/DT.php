<?php
/**
 * 日期时间类
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class DT {

    /**
     * 构造函数
     */
    public function __construct() {
    }
    
    /**
     * 得到当前时间的mysql标准格式的日期时间值字符串
     *
     * mysql标准格式的日期时间例子：2007-01-01 11:11:11
     */
    public function getDateTime() {
        return date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s');
    }
    
    /**
     * 根据mysql标准格式的日期时间值字符串参数获取年
     */
    public function getYear($dateTime) {
        return substr($dateTime, 0, 4);
    }
    
    /**
     * 根据mysql标准格式的日期时间值字符串参数获取月
     */
    public function getMonth($dateTime) {
        return substr($dateTime, 5, 2);
    }
    
    /**
     * 根据mysql标准格式的日期时间值字符串参数获取日
     */
    public function getDay($dateTime) {
        return substr($dateTime, 8, 2);
    }
    
    /**
     * 根据mysql标准格式的日期时间值字符串参数获取时
     */
    public function getHour($dateTime) {
        return substr($dateTime, 11, 2);
    }
    
    /**
     * 根据mysql标准格式的日期时间值字符串参数获取分
     */
    public function getMinute($dateTime) {
        return substr($dateTime, 14, 2);
    }
    
    /**
     * 根据mysql标准格式的日期时间值字符串参数获取秒
     */
    public function getSecond($dateTime) {
        return substr($dateTime, 17, 2);
    }
    
    /**
     * 验证日期时间是否正确
     *
     * @param  Integer  $year  年
     * @param  Integer  $month  月
     * @param  Integer  $day  日
     * @param  Integer  $hour  时
     * @param  Integer  $minute  分
     * @param  Integer  $second  秒
     */
    public function validDateTime($year, $month, $day, $hour = 0, $minute = 0, $second = 0) {
        if (MainApp::$oCf->oV->isUnsignedInt($year) && 
            MainApp::$oCf->oV->isUnsignedInt($month) && 
            MainApp::$oCf->oV->isUnsignedInt($day) && 
            MainApp::$oCf->oV->isUnsignedInt($hour) && 
            MainApp::$oCf->oV->isUnsignedInt($minute) && 
            MainApp::$oCf->oV->isUnsignedInt($second) && 
            checkdate($month, $day, $year) && 
            ($hour >= 0 && $hour <= 23) && 
            ($minute >= 0 && $minute <= 59) && 
            ($second >= 0 && $second <=59)) {
                return true;
        } else {
            return false;
        }
    }
    
}

?>