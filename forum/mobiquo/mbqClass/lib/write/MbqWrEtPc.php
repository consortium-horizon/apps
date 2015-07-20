<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseWrEtPc');

/**
 * private conversation write class
 * 
 * @since  2012-11-4
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqWrEtPc extends MbqBaseWrEtPc {
    
    public function __construct() {
    }
    
    /**
     * mark private conversation read
     *
     * @param  Object  $oMbqEtPc
     * @return  Mixed
     */
    public function markPcRead($oMbqEtPc) {
        $oConversationModel = new ConversationModel();
        $oConversationModel->MarkRead($oMbqEtPc->convId->oriValue, MbqMain::$oCurMbqEtUser->userId->oriValue);
    }
    
    /**
     * add private conversation
     *
     * @param  Object  $oMbqEtPc
     */
    public function addMbqEtPc(&$oMbqEtPc) {
        require_once(MBQ_APPEXTENTION_PATH.'ExttMbqConversationMessagesController.php');
        $oExttMbqConversationMessagesController = new ExttMbqConversationMessagesController();
        $oExttMbqConversationMessagesController->Initialize();
        $oExttMbqConversationMessagesController->exttMbqStartConversation('', $oMbqEtPc);
    }
    
    /**
     * invite participant
     *
     * @param  Object  $oMbqEtPcInviteParticipant
     */
    public function inviteParticipant($oMbqEtPcInviteParticipant) {
        $oConversationModel = new ConversationModel();
        $userIds = array();
        foreach ($oMbqEtPcInviteParticipant->objsMbqEtUser as $oMbqEtUser) {
            $userIds[] = $oMbqEtUser->userId->oriValue;
        }
        $oConversationModel->AddUserToConversation($oMbqEtPcInviteParticipant->convId->oriValue, $userIds);
    }
    
    /**
     * delete conversation
     *
     * @param  Object  $oMbqEtPc
     * @param  Integer  $mode
     */
    public function deleteConversation($oMbqEtPc, $mode) {
        if ($mode == 2) {
            $oConversationModel = new ConversationModel();
            $oConversationModel->Clear($oMbqEtPc->convId->oriValue, MbqMain::$oCurMbqEtUser->userId->oriValue);
        } else {
            MbqError::alert('', "Need valid mode id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>