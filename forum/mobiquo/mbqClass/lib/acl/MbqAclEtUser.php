<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseAclEtUser');

/**
 * user acl class
 * 
 * @since  2012-9-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqAclEtUser extends MbqBaseAclEtUser {
    
    public function __construct() {
    }
    
    /**
     * judge can get online users
     *
     * @return  Boolean
     */
    public function canAclGetOnlineUsers() {
        if (MbqMain::hasLogin()) {
            return true;
        } else {
            if (MbqMain::$oMbqConfig->getCfg('user.guest_whosonline')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.user.guest_whosonline.range.support')) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * judge can update_password
     *
     * @return  Boolean
     */
    public function canAclUpdatePassword() {
        //ref ProfileController::Password()
        if (MbqMain::hasLogin() && Gdn::Session()->CheckPermission('Garden.SignIn.Allow')) {
            return true;
        }
        return false;
    }
    
    /**
     * judge can update_email
     *
     * @return  Boolean
     */
    public function canAclUpdateEmail() {
        //ref ProfileController::Edit()
        if (MbqMain::hasLogin() && Gdn::Session()->CheckPermission('Garden.SignIn.Allow')) {
            return true;
        }
        return false;
    }
  
}

?>