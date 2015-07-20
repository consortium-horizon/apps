<?php
/**
 * cookie类
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class AppCookie {

    /**
     * 构造函数
     */
    public function __construct() {
    }
    
    /**
     * 判断是否已经设置了某个cookie变量
     *
     * @param  String  $varName  变量名
     */
    public function hasVar($varName) {
        if (isset($_COOKIE[$varName])) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 设置cookie变量
     *
     * @param  String  $varName  变量名
     * @param  Mixed  $varValue  变量值
     * @param  Integer  $expire  有效期（秒数）
     * @param  String  $path    路径
     * @param  String  $domain  域
     * @param  Boolean  $secure
     * @param  Boolean  $httponly
     * @param  Boolean  $needSerialize  标记是否需要保存序列化值
     */
    public function setVar($varName, $varValue, $expire = MPF_C_COOKIE_LEFT_TIME, $path = MPF_C_COOKIE_PATH, $domain = MPF_C_COOKIE_DOMAIN, $secure = MPF_C_COOKIE_SECURE, $httponly = MPF_C_COOKIE_HTTPONLY, $needSerialize = false) {
        $varValue = $needSerialize ? serialize($varValue) : $varValue;
        if ($expire == 0) {
            setcookie($varName, $varValue, $expire, $path, $domain, $secure, $httponly);
        } else {
            setcookie($varName, $varValue, time() + $expire, $path, $domain, $secure, $httponly);
        }
    }
    
    /**
     * 删除cookie变量
     *
     * @param  String  $varName  变量名
     * @param  Mixed  $varValue  变量值
     * @param  Integer  $expire  有效期（秒数）
     * @param  String  $path    路径
     * @param  String  $domain  域
     * @param  Boolean  $secure
     * @param  Boolean  $httponly
     */
    public function delVar($varName, $varValue = '', $expire = -360000, $path = MPF_C_COOKIE_PATH, $domain = MPF_C_COOKIE_DOMAIN, $secure = MPF_C_COOKIE_SECURE, $httponly = MPF_C_COOKIE_HTTPONLY) {
        if ($this->hasVar($varName))
            setcookie($varName, $varValue, time() + $expire, $path, $domain, $secure, $httponly);
    }
    
    /**
     * 获取cookie变量
     *
     * @param  String  $varName  变量名
     * @param  Boolean  $needUnserialize  标记是否需要反序列化值
     */
    public function getVar($varName, $needUnserialize = false) {
        return ($needUnserialize ? unserialize($_COOKIE[$varName]) : $_COOKIE[$varName]);
    }
    
}

?>