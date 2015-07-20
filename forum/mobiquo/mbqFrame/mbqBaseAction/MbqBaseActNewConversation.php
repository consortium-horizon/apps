<?php

defined('MBQ_IN_IT') or exit;

/**
 * new_conversation action
 * 
 * @since  2012-11-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActNewConversation extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        if (MbqMain::$oMbqConfig->moduleIsEnable('pc') && (MbqMain::$oMbqConfig->getCfg('pc.conversation')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.pc.conversation.range.support'))) {
        } else {
            MbqError::alert('', "Not support module private conversation!", '', MBQ_ERR_NOT_SUPPORT);
        }
        $oMbqEtPc = MbqMain::$oClk->newObj('MbqEtPc');
        $oMbqEtPc->userNames->setOriValue((array) MbqMain::$input[0]);
        $oMbqEtPc->convTitle->setOriValue(MbqMain::$input[1]);
        $oMbqEtPc->convContent->setOriValue(MbqMain::$input[2]);
        $oMbqAclEtPc = MbqMain::$oClk->newObj('MbqAclEtPc');
        if ($oMbqAclEtPc->canAclNewConversation($oMbqEtPc)) {    //acl judge
            $oMbqWrEtPc = MbqMain::$oClk->newObj('MbqWrEtPc');
            $oMbqWrEtPc->addMbqEtPc($oMbqEtPc);
            $this->data['result'] = true;
            $this->data['conv_id'] = (string) $oMbqEtPc->convId->oriValue;
            $oTapatalkPush = new TapatalkPush();
            $oTapatalkPush->callMethod('doPushNewConversation', array(
                'oMbqEtPc' => $oMbqEtPc
            ));
        } else {
            MbqError::alert('', '', '', MBQ_ERR_APP);
        }
    }
  
}

?>