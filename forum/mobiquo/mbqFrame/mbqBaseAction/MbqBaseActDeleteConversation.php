<?php

defined('MBQ_IN_IT') or exit;

/**
 * delete_conversation action
 * 
 * @since  2012-11-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActDeleteConversation extends MbqBaseAct {
    
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
        $convId = MbqMain::$input[0];
        $mode = (int) MbqMain::$input[1];
        $oMbqRdEtPc = MbqMain::$oClk->newObj('MbqRdEtPc');
        if ($objsMbqEtPc = $oMbqRdEtPc->getObjsMbqEtPc(array($convId), array('case' => 'byConvIds'))) {
            $oMbqEtPc = $objsMbqEtPc[0];
            $oMbqAclEtPc = MbqMain::$oClk->newObj('MbqAclEtPc');
            if ($oMbqAclEtPc->canAclDeleteConversation($oMbqEtPc, $mode)) {    //acl judge
                $oMbqWrEtPc = MbqMain::$oClk->newObj('MbqWrEtPc');
                $oMbqWrEtPc->deleteConversation($oMbqEtPc, $mode);
                $this->data['result'] = true;
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            } 
        } else {
            MbqError::alert('', "Need valid conversation id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>