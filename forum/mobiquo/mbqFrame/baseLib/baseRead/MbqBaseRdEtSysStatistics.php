<?php

defined('MBQ_IN_IT') or exit;

/**
 * system statistics read class
 * 
 * @since  2012-9-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseRdEtSysStatistics extends MbqBaseRd {
    
    public function __construct() {
    }
    
    /**
     * init system statistics by condition
     *
     * @return  Object
     */
    public function initOMbqEtSysStatistics() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>