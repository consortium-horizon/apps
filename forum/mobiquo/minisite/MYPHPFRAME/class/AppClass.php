<?php
/**
 * 应用对象抽象类
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class AppClass {
    
    public $oDb;  /* 数据库对象 */
    public $tbPrefix;   /* 表名前缀 */

    /**
     * 构造函数
     */
    public function __construct() {
        $this->oDb = & MainApp::$oDb;
        $this->tbPrefix = MPF_C_APP_TBPREFIX;
    }
    
}

?>