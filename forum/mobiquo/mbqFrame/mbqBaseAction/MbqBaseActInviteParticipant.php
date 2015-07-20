<?php

defined('MBQ_IN_IT') or exit;

/**
 * invite_participant action
 * 
 * @since  2012-11-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActInviteParticipant extends MbqBaseAct {
    
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
        $oMbqEtPcInviteParticipant = MbqMain::$oClk->newObj('MbqEtPcInviteParticipant');
        $oMbqEtPcInviteParticipant->userNames->setOriValue(MbqMain::$input[0]);
        $oMbqEtPcInviteParticipant->convId->setOriValue(MbqMain::$input[1]);
        $oMbqEtPcInviteParticipant->inviteReasonText->setOriValue(MbqMain::$input[2]);
        $oMbqRdEtPc = MbqMain::$oClk->newObj('MbqRdEtPc');
        if ($objsMbqEtPc = $oMbqRdEtPc->getObjsMbqEtPc(array($oMbqEtPcInviteParticipant->convId->oriValue), array('case' => 'byConvIds'))) {
            $oMbqEtPcInviteParticipant->oMbqEtPc = $objsMbqEtPc[0];
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            foreach ($oMbqEtPcInviteParticipant->userNames->oriValue as $userName) {
                if ($oMbqEtUser = $oMbqRdEtUser->initOMbqEtUser($userName, array('case' => 'byLoginName'))) {
                    $oMbqEtPcInviteParticipant->objsMbqEtUser[] = $oMbqEtUser;
                }
            }
            $oMbqAclEtPc = MbqMain::$oClk->newObj('MbqAclEtPc');
            if ($oMbqAclEtPc->canAclInviteParticipant($oMbqEtPcInviteParticipant)) {    //acl judge
                $oMbqWrEtPc = MbqMain::$oClk->newObj('MbqWrEtPc');
                $oMbqWrEtPc->inviteParticipant($oMbqEtPcInviteParticipant);
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