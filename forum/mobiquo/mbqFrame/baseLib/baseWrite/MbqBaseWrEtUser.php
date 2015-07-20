<?php

defined('MBQ_IN_IT') or exit;

/**
 * user write class
 * 
 * @since  2012-9-28
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseWrEtUser extends MbqBaseWr {
    
    public function __construct() {
    }
    
    /**
     * m_ban_user
     */
    public function mBanUser() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * m_ban_user
     */
    public function mUnbanUser() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * m_mark_as_spam
     */
    public function mMarkAsSpam() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
    * register user
    */
    public function registerUser() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * update password
     */
    public function updatePassword() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * update email
     */
    public function updateEmail() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}
?>