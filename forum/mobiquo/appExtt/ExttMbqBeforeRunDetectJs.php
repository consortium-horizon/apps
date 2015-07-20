<?php
/**
 * define detect js vars before run detect js
 * 
 * @since  2012-11-30
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqBeforeRunDetectJs {
   
   public function __construct() {
   }
   
   public function run() {
        $filePath = dirname(__FILE__).'/../custom/customDetectJs.php';
        if (is_file($filePath)) {
            require_once($filePath);
            $str = 'var tapatalk_iphone_msg = "'.MbqCustomDetectJs::$MBQ_DETECTJS_IPHONEIPOD_CONFIRM_TITLE.'";';
            $str .= 'var tapatalk_iphone_url = "'.MbqCustomDetectJs::$MBQ_DETECTJS_IPHONEIPOD_DOWNLOAD_URL.'";';
            $str .= 'var tapatalk_ipad_msg = "'.MbqCustomDetectJs::$MBQ_DETECTJS_IPAD_CONFIRM_TITLE.'";';
            $str .= 'var tapatalk_ipad_url = "'.MbqCustomDetectJs::$MBQ_DETECTJS_IPAD_DOWNLOAD_URL.'";';
            $str .= 'var tapatalk_kindle_msg = "'.MbqCustomDetectJs::$MBQ_DETECTJS_KINDLEFIRE_CONFIRM_TITLE.'";';
            $str .= 'var tapatalk_kindle_url = "'.MbqCustomDetectJs::$MBQ_DETECTJS_KINDLEFIRE_DOWNLOAD_URL.'";';
            $str .= 'var tapatalk_kindle_hd_msg = "'.MbqCustomDetectJs::$MBQ_DETECTJS_KINDLEFIRE_HD_CONFIRM_TITLE.'";';
            $str .= 'var tapatalk_kindle_hd_url = "'.MbqCustomDetectJs::$MBQ_DETECTJS_KINDLEFIRE_HD_DOWNLOAD_URL.'";';
            $str .= 'var tapatalk_android_msg = "'.MbqCustomDetectJs::$MBQ_DETECTJS_ANDROID_CONFIRM_TITLE.'";';
            $str .= 'var tapatalk_android_url = "'.MbqCustomDetectJs::$MBQ_DETECTJS_ANDROID_DOWNLOAD_URL.'";';
            $str .= 'var tapatalk_android_hd_msg = "'.MbqCustomDetectJs::$MBQ_DETECTJS_ANDROID_HD_CONFIRM_TITLE.'";';
            $str .= 'var tapatalk_android_hd_url = "'.MbqCustomDetectJs::$MBQ_DETECTJS_ANDROID_HD_DOWNLOAD_URL.'";';
            $str .= 'var tapatalkdir = "'.MbqCustomDetectJs::$MBQ_DETECTJS_TAPATALKDIR.'";';
            echo $str;
        }
   }
}

ExttMbqBeforeRunDetectJs::run();

?>