<?php

defined('MBQ_IN_IT') or exit;

/**
 * private message acl class
 * 
 * @since  2012-12-28
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseAclEtPm extends MbqBaseAcl {
    
    public function __construct() {
    }
    
    /**
     * judge can report_pm
     *
     * @return  Boolean
     */
    public function canAclReportPm() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * judge can create_message
     *
     * @return  Boolean
     */
    public function canAclCreateMessage() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * judge can get_box_info
     *
     * @return  Boolean
     */
    public function canAclGetBoxInfo() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * judge can get_box
     *
     * @return  Boolean
     */
    public function canAclGetBox() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * judge can get_message
     *
     * @return  Boolean
     */
    public function canAclGetMessage() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * judge can get_quote_pm
     *
     * @return  Boolean
     */
    public function canAclGetQuotePm() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * judge can delete_message
     *
     * @return  Boolean
     */
    public function canAclDeleteMessage() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * judge can mark_pm_unread
     *
     * @return  Boolean
     */
    public function canAclMarkPmUnread() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>