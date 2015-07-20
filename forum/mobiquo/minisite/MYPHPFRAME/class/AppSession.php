<?php
/**
 * session类
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class AppSession {

    /**
     * 构造函数
     */
    public function __construct() {
    }
    
    /**
     * 开始session
     */
    public function sessionStart() {
        session_start();
    }
    
    /**
     * 销毁session
     */
    public function sessionDestory() {
        session_destroy();
    }
    
    /**
     * 判断是否已经设置了某个session变量
     *
     * @param  String  $varName  变量名
     */
    public function hasVar($varName) {
        if (isset($_SESSION[$varName])) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 设置session变量
     *
     * @param  String  $varName  变量名
     * @param  Mixed  $varValue  变量值
     * @param  Boolean  $needSerialize  标记是否需要保存序列化值
     */
    public function setVar($varName, $varValue, $needSerialize = true) {
        if ($needSerialize) {
            $_SESSION[$varName] = serialize($varValue);
        } else {
            $_SESSION[$varName] = $varValue;
        }
    }
    
    /**
     * 获取session变量
     *
     * @param  String  $varName  变量名
     * @param  Boolean  $needUnserialize  标记是否需要反序列化值
     */
    public function getVar($varName, $needUnserialize = true) {
        if ($needUnserialize) {
            return unserialize($_SESSION[$varName]);
        } else {
            return $_SESSION[$varName];
        }
    }
    
}

?>