<?php
/**
 * do some init work before run smartbanner
 * will be abolished and removed
 * 
 * @since  2013-4-18
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqBeforeRunSmartbanner {
    
    public function run() {
        $filePath = dirname(__FILE__).'/../custom/customSmartbanner.php';
        if (is_file($filePath)) {
            require_once($filePath);
            define('APPLICATION', 'Vanilla');   //for include vanilla config file
            $vanillaConfigFilePath = dirname(__FILE__).'/../../conf/config.php';
            if (is_file($vanillaConfigFilePath)) {
                require_once($vanillaConfigFilePath);
                MbqSmartbanner::$MBQ_SMARTBANNER_APP_FORUM_NAME = $Configuration['Garden']['Title'];
            }
            $temp = $_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
            MbqSmartbanner::$MBQ_SMARTBANNER_APP_LOCATION_URL = 'tapatalk://'.substr($temp, 0, strpos($temp, MbqSmartbanner::$MBQ_SMARTBANNER_TAPATALKDIR.'/appExtt/ExttMbqBeforeRunSmartbanner.php')).'?location=index';
            $str = '';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_IS_MOBILE_SKIN)
            $str .= 'var is_mobile_skin = '.MbqSmartbanner::$MBQ_SMARTBANNER_IS_MOBILE_SKIN.';';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_IOS_ID)
            $str .= 'var app_ios_id = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_IOS_ID.'";';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_ANDROID_URL)
            $str .= 'var app_android_url = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_ANDROID_URL.'";';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_KINDLE_URL)
            $str .= 'var app_kindle_url = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_KINDLE_URL.'";';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_BANNER_MESSAGE)
            $str .= 'var app_banner_message = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_BANNER_MESSAGE.'";';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_FORUM_NAME)
            $str .= 'var app_forum_name = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_FORUM_NAME.'";';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_LOCATION_URL)
            $str .= 'var app_location_url = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_LOCATION_URL.'";';
            echo $str;
        }
    }
}

ExttMbqBeforeRunSmartbanner::run();
?>