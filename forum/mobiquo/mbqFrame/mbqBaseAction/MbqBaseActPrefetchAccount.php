<?php

defined('MBQ_IN_IT') or exit;

/**
 * prefetch account
 * 
 * @since  2013-10-16
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActPrefetchAccount extends MbqBaseAct {
    
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
        if (MbqMain::$oMbqConfig->getCfg('user.sign_in')->oriValue != MbqBaseFdt::getFdt('MbqFdtConfig.user.sign_in.range.support')) {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_SUPPORT);
        }
        $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
        $result = $oMbqRdEtUser->initOMbqEtUser(MbqMain::$input[0], array('case' => 'byEmail'));
        if ($result) {
            $oMbqEtUser = $result;
            $this->data['result'] = true;
            if ($oMbqEtUser->userId->hasSetOriValue()) {
                $this->data['user_id'] = (string) $oMbqEtUser->userId->oriValue;
            }
            if ($oMbqEtUser->loginName->hasSetOriValue()) {
                $this->data['login_name'] = (string) $oMbqEtUser->loginName->oriValue;
            }
            $this->data['display_name'] = (string) $oMbqEtUser->getDisplayName();
            if ($oMbqEtUser->iconUrl->hasSetOriValue()) {
                $this->data['avatar'] = (string) $oMbqEtUser->iconUrl->oriValue;
            } else {
                $this->data['avatar'] = '';
            }
        } else {
            $this->data['result'] = false;
        }
    }
  
}

?>