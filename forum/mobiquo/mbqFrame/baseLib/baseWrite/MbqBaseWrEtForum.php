<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum write class
 * 
 * @since  2012-9-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseWrEtForum extends MbqBaseWr {
    
    public function __construct() {
    }
    
    /**
     * subscribe forum
     */
    public function subscribeForum() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * unsubscribe forum
     */
    public function unsubscribeForum() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>