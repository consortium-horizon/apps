<?php

defined('MBQ_IN_IT') or exit;

/**
 * m_ban_user action
 * 
 * @since  2012-9-28
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActMBanUser extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        if (!MbqMain::$oMbqConfig->moduleIsEnable('user')) {
            MbqError::alert('', "Not support module user!", '', MBQ_ERR_NOT_SUPPORT);
        }
        $userName = MbqMain::$input[0];
        $mode = MbqMain::$input[1];
        $reasonText = MbqMain::$input[2];
        $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
        if ($mode != 1 && $mode != 2) {
            MbqError::alert('', "Need valid mode!", '', MBQ_ERR_APP);
        }
        if ($oMbqEtUser = $oMbqRdEtUser->initOMbqEtUser($userName, array('case' => 'byLoginName'))) {
            $oMbqAclEtUser = MbqMain::$oClk->newObj('MbqAclEtUser');
            if ($oMbqAclEtUser->canAclMBanUser($oMbqEtUser, $mode)) {   //acl judge
                $oMbqWrEtUser = MbqMain::$oClk->newObj('MbqWrEtUser');
                $oMbqWrEtUser->mBanUser($oMbqEtUser, $mode, $reasonText);
                $this->data['result'] = true;
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "User not found!", '', MBQ_ERR_APP);
        }
    }
  
}

?>