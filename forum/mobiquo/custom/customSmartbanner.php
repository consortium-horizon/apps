<?php

/**
 * custom smartbanner info
 * 
 * @since  2013-4-17
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqSmartbanner {
    public static $functionCallAfterWindowLoad = 0; //for those forum system which can not add js in html body, please set $functionCallAfterWindowLoad as 1
    public static $IS_MOBILE_SKIN = 0;  //0 or 1,judge this is on a mobile skin
    public static $APP_IOS_ID = '';  //set to '' if not byo,ios app id from byo option
    public static $APP_ANDROID_ID = '';  //set to '' if not byo,android app id from byo option
    public static $APP_KINDLE_URL = '';  //set to '' if not byo,kindle app url from byo option
    public static $APP_BANNER_MESSAGE = '';  //set to '' if not byo
    public static $APP_FORUM_NAME = '';  //required
    public static $APP_LOCATION_URL = '';  //optional,page location url with tapatalk scheme rules
    public static $BOARD_URL = '';  //forum url to root
    public static $API_KEY = '';     //tapatalk api key
    public static $CURRENT_URL = '';    //current url,used for $app_ads_referer
    public static $APP_ADS_ENABLE = true;
    public static $APP_ADS_URL = '';
    
    public static $TAPATALKDIR = 'mobiquo'; //default as 'mobiquo'
    public static $TAPATALKDIR_URL = ''; //$board_url.'/'.$tapatalk_dir
}

?>