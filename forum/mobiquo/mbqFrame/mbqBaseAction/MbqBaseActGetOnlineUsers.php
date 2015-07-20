<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_online_users action
 * 
 * @since  2012-9-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetOnlineUsers extends MbqBaseAct {
    
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
        $oMbqAclEtUser = MbqMain::$oClk->newObj('MbqAclEtUser');
        if ($oMbqAclEtUser->canAclGetOnlineUsers()) {   //acl judge
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            $objsMbqEtUser = $oMbqRdEtUser->getObjsMbqEtUser(NULL, array('case' => 'online'));
            $oMbqRdEtSysStatistics = MbqMain::$oClk->newObj('MbqRdEtSysStatistics');
            $oMbqEtSysStatistics = $oMbqRdEtSysStatistics->initOMbqEtSysStatistics();
            $this->data['list'] = $oMbqRdEtUser->returnApiArrDataUser($objsMbqEtUser);
            $this->data['member_count'] = (int) ($oMbqEtSysStatistics->forumTotalOnline->oriValue - $oMbqEtSysStatistics->forumGuestOnline->oriValue);
            //$this->data['member_count'] = (int) count($objsMbqEtUser);
            $this->data['guest_count'] = (int) $oMbqEtSysStatistics->forumGuestOnline->oriValue;
        } else {
            MbqError::alert('', '', '', MBQ_ERR_APP);
        }
    }
  
}

?>