<?php

defined('MBQ_IN_IT') or exit;

/**
 * attachment write class
 * 
 * @since  2012-9-11
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseWrEtAtt extends MbqBaseWr {
    
    public function __construct() {
    }
    
    /**
     * upload attachment
     */
    public function uploadAttachment() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * delete attachment
     */
    public function deleteAttachment() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>