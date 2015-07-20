<?php

defined('MBQ_IN_IT') or exit;

/**
 * private conversation message write class
 * 
 * @since  2012-11-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseWrEtPcMsg extends MbqBaseWr {
    
    public function __construct() {
    }
    
    /**
     * add private conversation message
     */
    public function addMbqEtPcMsg() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>