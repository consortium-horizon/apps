<?php

defined('MBQ_IN_IT') or exit;

/**
 * attachment acl class
 * 
 * @since  2012-9-11
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseAclEtAtt extends MbqBaseAcl {
    
    public function __construct() {
    }
    
    /**
     * judge can upload attachment
     *
     * @return  Boolean
     */
    public function canAclUploadAttach() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * judge can remove attachment
     *
     * @return  Boolean
     */
    public function canAclRemoveAttachment() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>