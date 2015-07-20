<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_user_info action
 * 
 * @since  2012-8-16
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetUserInfo extends MbqBaseAct {
    
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
        $userId = MbqMain::$input[1];
        $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
        if ($userId) {
            $oMbqEtUser = $oMbqRdEtUser->initOMbqEtUser($userId, array('case' => 'byUserId'));
        } else {
            $oMbqEtUser = $oMbqRdEtUser->initOMbqEtUser($userName, array('case' => 'byLoginName'));
        }
        if ($oMbqEtUser) {
            $this->data = $oMbqRdEtUser->returnApiDataUser($oMbqEtUser);
        } else {
            MbqError::alert('', "User not found!", '', MBQ_ERR_APP);
        }
    }
  
}

?>