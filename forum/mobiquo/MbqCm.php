<?php

defined('MBQ_IN_IT') or exit;

/**
 * common method class
 * 
 * @since  2012-7-2
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqCm extends MbqBaseCm {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * transform timestamp to iso8601 format
     *
     * @param  Integer  $timeStamp
     * TODO:need to be made more general.
     */
    public function datetimeIso8601Encode($timeStamp) {
        //return date("c", $timeStamp);
        return date('Ymd\TH:i:s', $timeStamp).'+00:00';
    }
    
    /**
     * Get part of string
     */
    public function exttSubstr($str, $start, $length) {
        //ref ValidateLength() of functions.validation.php
        if (function_exists('mb_substr')) {
            return mb_substr($str, $start, $length);
        } else {
            return substr($str, $start, $length);
        }
    }
    
    /**
     * get api key
     *
     * @param  String  $forumUrl
     * @return String
     */
    public function exttGetApiKey($forumUrl) {
        $apiUrl = 'http://directory.tapatalk.com/au_reg_verify.php';
        $boardurl = $this->removeSlashForUrl($forumUrl);
        $boardurl = urlencode($boardurl);
        $response = $this->getContentFromRemoteServer($apiUrl."?url=$boardurl", 10, $error);
        $apiKey = '';
        if($response)
            $result = json_decode($response, true);
        if(isset($result) && isset($result['result']) && $result['result'])
            $apiKey = $result['api_key'];
        else
        {
            $data = array(
                'url'   =>  $boardurl,
            );
            $response = $this->getContentFromRemoteServer($apiUrl, 10, $error, 'POST', $data);
            if($response)
                $result = json_decode($response, true);
            if(isset($result) && isset($result['result']) && $result['result'])
                $apiKey = $result['api_key'];
        }
        return $apiKey;
    }

    /* calculate the flags for In App Registration */
    public function exttMakeFlags() {
        /* calculate the flags for In App Registration */
        /* default */
        $exttMbqSignIn = 1;
        $exttMbqInappreg = 1;
        $exttMbqSsoLogin = 1;
        $exttMbqSsoSignin = 1;
        $exttMbqSsoRegister = 1;
        $exttMbqNativeRegister = 1;
        /* final */
        if (C('Garden.Registration.Method') == 'closed' || !MbqMain::$oMbqAppEnv->check3rdPluginEnabled('Tapatalk')) 
        {
            $exttMbqSignIn = 0;
            $exttMbqInappreg = 0;
            $exttMbqSsoSignin = 0;
            $exttMbqSsoRegister = 0;
            $exttMbqNativeRegister = 0;
        }
        if (!function_exists('curl_init') && !@ini_get('allow_url_fopen'))
        {
            $exttMbqSignIn = 0;
            $exttMbqInappreg = 0;
            $exttMbqSsoLogin = 0;
            $exttMbqSsoSignin = 0;
            $exttMbqSsoRegister = 0;
        }
        if (C('Plugin.Tapatalk.tapatalk_iar_registration_options')) {
            if (C('Plugin.Tapatalk.tapatalk_iar_registration_options') == 3) {
                $exttMbqSignIn = 0;
                $exttMbqInappreg = 0;
                $exttMbqSsoSignin = 0;
                $exttMbqSsoRegister = 0;
                $exttMbqNativeRegister = 0;
            } elseif (C('Plugin.Tapatalk.tapatalk_iar_registration_options') == 2) {
                $exttMbqSignIn = 0;
                $exttMbqInappreg = 0;
                $exttMbqSsoSignin = 0;
                $exttMbqSsoRegister = 0;
            }
        }
        
        MbqMain::$oMbqAppEnv->otherParams['exttMbqSignIn'] = $exttMbqSignIn;
        MbqMain::$oMbqAppEnv->otherParams['exttMbqInappreg'] = $exttMbqInappreg;
        MbqMain::$oMbqAppEnv->otherParams['exttMbqSsoLogin'] = $exttMbqSsoLogin;
        MbqMain::$oMbqAppEnv->otherParams['exttMbqSsoSignin'] = $exttMbqSsoSignin;
        MbqMain::$oMbqAppEnv->otherParams['exttMbqSsoRegister'] = $exttMbqSsoRegister;
        MbqMain::$oMbqAppEnv->otherParams['exttMbqNativeRegister'] = $exttMbqNativeRegister;
    }
    
}

?>